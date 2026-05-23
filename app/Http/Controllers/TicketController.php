<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Mail\EticketMail;
use App\Models\Event;
use App\Models\TicketType;
use App\Models\Order;
use App\Models\OtpVerification;
use App\Models\Lineup; 
use App\Models\Voucher; // MATT FIX: Panggil model Voucher
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function index()
    {
        $events = Event::with('ticketTypes')->latest()->get();
        return view('welcome', compact('events'));
    }

    public function create()
    {
        // MATT FIX: Ambil voucher aktif milik promotor
        $activeVouchers = Voucher::where('user_id', Auth::id())->where('status', 'active')->get();
        return view('tickets.create', compact('activeVouchers')); 
    }

    public function store(Request $request)
    {
        $imagePath = null;
        if ($request->hasFile('f_banner')) {
            $imagePath = $request->file('f_banner')->store('banners', 'public');
        }

        $galleryPaths = [];
        if ($request->hasFile('f_gallery')) {
            foreach ($request->file('f_gallery') as $file) {
                $galleryPaths[] = $file->store('galleries', 'public');
            }
        }

        $event = Event::create([
            'user_id' => Auth::id(), 
            'name' => $request->f_name,
            'date' => $request->f_date,
            'sales_start_date' => $request->sales_start_date ?: null,
            'category' => $request->f_cat,
            'type' => $request->f_type,
            'description' => $request->f_desc,
            'banner' => $imagePath,
            'gallery' => $galleryPaths,
            'promo' => 0, // Reset default karena promo sekarang pakai is_voucher_active
            'total_stock' => 0, 
            'is_voucher_active' => $request->has('is_voucher_active') ? 1 : 0,
        ]);

        $totalStock = 0;
        if($request->has('t_names')) {
            foreach($request->t_names as $key => $name) {
                $price = str_replace('.', '', $request->t_prices[$key]);
                $stock = str_replace('.', '', $request->t_stocks[$key]);
                
                TicketType::create([
                    'event_id' => $event->id,
                    'name' => $name,
                    'price' => $price,
                    'stock' => $stock
                ]);
                $totalStock += (int)$stock;
            }
        }

        $event->update(['total_stock' => $totalStock]);

        if ($request->has('lineup_name')) {
            foreach ($request->lineup_name as $index => $name) {
                if (!empty($name)) {
                    Lineup::create([
                        'event_id' => $event->id,
                        'name' => $name,
                        'ig' => $request->lineup_ig[$index] ?? null,
                        'spotify' => $request->lineup_spotify[$index] ?? null,
                    ]);
                }
            }
        }

        return redirect('/')->with('success', 'Event Berhasil Di-publish!');
    }

    public function show($id)
    {
        $event = Event::with(['ticketTypes', 'lineups'])->findOrFail($id);
        $event->increment('views'); 
        
        return view('tickets.show', compact('event'));
    }

    public function prepareCheckout(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'ticket_id' => 'required|exists:ticket_types,id',
            'quantity' => 'required|integer|min:1|max:5',
        ]);

        session(['checkout_data' => $request->all()]);
        return redirect()->route('checkout.form');
    }

    public function checkoutForm()
    {
        $data = session('checkout_data');
        if(!$data) return redirect('/');

        $ticket = TicketType::with('event')->findOrFail($data['ticket_id']);
        $quantity = $data['quantity'];
        $subtotal = $ticket->price * $quantity;

        return view('tickets.checkout', compact('ticket', 'quantity', 'subtotal'));
    }

    // MATT FIX: Fungsi sendOTP() DIHAPUS karena diganti dengan pembayaran langsung.

    public function checkVoucher(Request $request)
    {
        $data = session('checkout_data');
        if(!$data) return response()->json(['success' => false, 'message' => 'Sesi berakhir, silahkan ulang pesanan.']);

        $event = Event::find($data['event_id']);
        // Keamanan: Cek apakah promotor event ini mengaktifkan toggle voucher
        if(!$event || !$event->is_voucher_active) {
            return response()->json(['success' => false, 'message' => 'Event ini tidak mengizinkan penggunaan voucher.']);
        }

        // Cek voucher (Hanya voucher milik promotor event ini yang berstatus 'active')
        $voucher = Voucher::where('code', strtoupper($request->code))
                          ->where('user_id', $event->user_id)
                          ->where('status', 'active')
                          ->first();

        if(!$voucher) {
            return response()->json(['success' => false, 'message' => 'Kode Voucher tidak valid atau bukan untuk event ini.']);
        }

        if($voucher->quota > 0 && $voucher->used >= $voucher->quota) {
            return response()->json(['success' => false, 'message' => 'Kuota pemakaian voucher ini sudah habis.']);
        }

        $ticket = TicketType::find($data['ticket_id']);
        $subtotal = $ticket->price * $data['quantity'];

        $discount = 0;
        if($voucher->type == 'nominal') {
            $discount = $voucher->amount;
        } else {
            $discount = $subtotal * ($voucher->amount / 100);
        }

        // Pastikan diskon tidak melebihi harga tiket
        if($discount > $subtotal) $discount = $subtotal;
        $final = $subtotal - $discount;

        return response()->json([
            'success' => true,
            'discount_formatted' => number_format($discount, 0, ',', '.'),
            'final_formatted' => number_format($final, 0, ',', '.')
        ]);
    }

    public function processPayment(Request $request)
    {
        $data = session('checkout_data');
        if(!$data) return response()->json(['success' => false, 'message' => 'Sesi Berakhir, silahkan ulangi pesanan.']);

        $ticketType = TicketType::findOrFail($data['ticket_id']);
        $event = Event::findOrFail($data['event_id']);
        
        $totalPrice = $ticketType->price * $data['quantity'];
        $discount = 0;

        // Validasi Voucher Saat Eksekusi Pembayaran
        if ($request->voucher_code && $event->is_voucher_active) {
            $voucher = Voucher::where('code', strtoupper($request->voucher_code))
                              ->where('user_id', $event->user_id)
                              ->where('status', 'active')
                              ->first();

            if ($voucher && ($voucher->quota == 0 || $voucher->used < $voucher->quota)) {
                if ($voucher->type == 'nominal') {
                    $discount = $voucher->amount;
                } else {
                    $discount = $totalPrice * ($voucher->amount / 100);
                }
                if ($discount > $totalPrice) $discount = $totalPrice;

                // Tambah angka pemakaian voucher
                $voucher->increment('used');
            }
        }

        $finalPrice = $totalPrice - $discount;
        $pricePerTicket = $finalPrice / $data['quantity'];
        $baseId = 'SPX-' . strtoupper(Str::random(8));

        $names = $request->names; 
        $emails = $request->emails; 

        for ($i = 0; $i < $data['quantity']; $i++) {
            $orderNumber = $baseId . '-' . ($i + 1);

            $order = Order::create([
                'event_id' => $data['event_id'],
                'ticket_type_id' => $data['ticket_id'],
                'customer_name' => $names[$i],
                'customer_email' => $emails[$i],
                'customer_phone' => $request->whatsapp, // Menggunakan WA yang diinput
                'quantity' => 1,
                'total_price' => $pricePerTicket,
                'order_number' => $orderNumber,
                'status' => 'success' 
            ]);

            $ticketType->decrement('stock', 1);

            try {
                Mail::to($emails[$i])->send(new EticketMail($order));
            } catch (\Exception $e) {
                logger("Gagal kirim email ke: " . $emails[$i]);
            }
        }

        session()->forget('checkout_data');

        return response()->json([
            'success' => true, 
            'redirect' => route('checkout.invoice', $baseId)
        ]);
    }

    public function invoice($base_id)
    {
        $orders = Order::with(['event', 'ticketType'])
                       ->where('order_number', 'like', $base_id . '-%')
                       ->get();

        if($orders->isEmpty()) abort(404);

        return view('tickets.invoice', compact('orders'));
    }

    public function edit($id)
    {
        $event = Event::with(['ticketTypes', 'lineups'])->findOrFail($id);

        if ($event->user_id !== Auth::id()) {
            abort(403, 'Aksi tidak diizinkan. Ini bukan event milikmu!');
        }

        // MATT FIX: Ambil voucher aktif milik promotor
        $activeVouchers = Voucher::where('user_id', Auth::id())->where('status', 'active')->get();

        return view('promotor.event.edit', compact('event', 'activeVouchers'));
    }

    public function update(Request $request, $id)
    {
        $event = Event::findOrFail($id);

        if ($event->user_id !== Auth::id()) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|in:Musik,Olahraga,Seminar,Hiburan',
            'type' => 'required|string|in:Publik,Privat', 
            'date' => 'required|date',
            'sales_start_date' => 'nullable|date',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'description' => 'nullable|string',
        ]);

        $data = $request->only(['name', 'category', 'type', 'date', 'description']);

        $data['sales_start_date'] = $request->sales_start_date ?: null;
        $data['is_voucher_active'] = $request->has('is_voucher_active') ? 1 : 0;

        if ($request->hasFile('banner')) {
            if ($event->banner && Storage::disk('public')->exists($event->banner)) {
                Storage::disk('public')->delete($event->banner);
            }
            $data['banner'] = $request->file('banner')->store('banners', 'public');
        }

        $event->update($data);

        $totalStock = 0;
        if($request->has('t_names')) {
            foreach($request->t_names as $key => $name) {
                $price = str_replace('.', '', $request->t_prices[$key]);
                $stock = str_replace('.', '', $request->t_stocks[$key]);
                $ticketId = $request->t_ids[$key] ?? null;

                if ($ticketId) {
                    $ticket = TicketType::find($ticketId);
                    if ($ticket) {
                        $ticket->update(['name' => $name, 'price' => $price, 'stock' => $stock]);
                        $totalStock += (int)$stock;
                    }
                } else {
                    TicketType::create([
                        'event_id' => $event->id,
                        'name' => $name,
                        'price' => $price,
                        'stock' => $stock
                    ]);
                    $totalStock += (int)$stock;
                }
            }
        }
        $event->update(['total_stock' => $totalStock]);

        if ($request->has('lineup_name')) {
            $event->lineups()->delete(); 
            foreach ($request->lineup_name as $index => $name) {
                if (!empty($name)) {
                    Lineup::create([
                        'event_id' => $event->id,
                        'name' => $name,
                        'ig' => $request->lineup_ig[$index] ?? null,
                        'spotify' => $request->lineup_spotify[$index] ?? null,
                    ]);
                }
            }
        }

        return redirect('/promotor/dashboard')->with('success', '🔥 Event dan Tiket berhasil diperbarui!');
    }
}