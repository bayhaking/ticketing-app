<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Mail\EticketMail;
use App\Models\Event;
use App\Models\TicketType;
use App\Models\Order;
use App\Models\Lineup;
use App\Models\Voucher;
use App\Models\EventView;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TicketController extends Controller
{
    /**
     * Service fee rate: 5% dari harga tiket.
     * Untuk mudah diubah di satu tempat.
     */
    const SERVICE_FEE_RATE = 0.05;

    public function index()
    {
        $events = Event::with('ticketTypes')->latest()->get();
        return view('welcome', compact('events'));
    }

    public function create()
    {
        $activeVouchers = Voucher::where('user_id', Auth::id())->where('status', 'active')->get();
        return view('tickets.create', compact('activeVouchers'));
    }

    public function store(Request $request)
    {
        // VALIDASI field baru
        $validator = Validator::make($request->all(), [
            'f_name' => 'required|string|max:255',
            'f_date' => 'required|date',
            'f_start_time' => 'required|date_format:H:i',
            'f_end_time' => 'nullable|date_format:H:i|after:f_start_time',
            'f_venue' => 'required|string|min:5|max:255',
            'f_venue_url' => 'nullable|url|max:500',
            'f_desc' => 'required|string',
            'f_cat' => 'required|in:Musik,Olahraga,Seminar,Hiburan',
            'f_type' => 'required|in:Publik,Private',
            'f_banner' => 'required|image|max:5120', // max 5MB
            'f_creator_ig' => 'nullable|string|max:100',
            'sponsor_name.*' => 'nullable|string|max:100',
            'sponsor_ig.*' => 'nullable|string|max:100',
        ], [
            'f_venue.required' => 'Nama venue/lokasi wajib diisi.',
            'f_venue.min' => 'Nama venue minimal 5 karakter.',
            'f_start_time.required' => 'Jam mulai acara wajib diisi.',
            'f_end_time.after' => 'Jam selesai harus lebih besar dari jam mulai.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Upload banner
        $imagePath = $request->hasFile('f_banner') ? $request->file('f_banner')->store('banners', 'public') : null;

        // Upload gallery
        $galleryPaths = [];
        if ($request->hasFile('f_gallery')) {
            foreach ($request->file('f_gallery') as $file) {
                $galleryPaths[] = $file->store('galleries', 'public');
            }
        }

        // Sanitize creator_ig (strip @, https://, instagram.com/)
        $creatorIg = $this->sanitizeIgUsername($request->f_creator_ig);

        // Build sponsors array
        $sponsors = [];
        if ($request->has('sponsor_name') && is_array($request->sponsor_name)) {
            foreach ($request->sponsor_name as $idx => $name) {
                $name = trim($name ?? '');
                if (empty($name)) continue;

                $ig = $this->sanitizeIgUsername($request->sponsor_ig[$idx] ?? null);

                $sponsors[] = [
                    'name' => mb_substr($name, 0, 100),
                    'ig' => $ig,
                ];
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

            // FIELD BARU
            'venue' => $request->f_venue,
            'venue_url' => $request->f_venue_url ?: null,
            'start_time' => $request->f_start_time,
            'end_time' => $request->f_end_time ?: null,
            'creator_ig' => $creatorIg,
            'sponsors' => $sponsors,

            'banner' => $imagePath,
            'gallery' => $galleryPaths,
            'promo' => 0,
            'total_stock' => 0,
            'is_voucher_active' => $request->has('is_voucher_active') ? 1 : 0,
            'status' => 'upcoming',
        ]);

        // Create ticket types
        $totalStock = 0;
        if ($request->has('t_names')) {
            foreach ($request->t_names as $key => $name) {
                $price = (int) str_replace('.', '', $request->t_prices[$key] ?? '0');
                $stock = (int) str_replace('.', '', $request->t_stocks[$key] ?? '0');

                TicketType::create([
                    'event_id' => $event->id,
                    'name' => $name,
                    'price' => $price,
                    'stock' => $stock,
                ]);

                $totalStock += $stock;
            }
        }

        $event->update(['total_stock' => $totalStock]);

        // Create lineups
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

        return redirect('/promotor/dashboard')->with('success', '✅ Event "' . $event->name . '" berhasil di-publish!');
    }

    public function show($id)
    {
        $event = Event::with(['ticketTypes', 'lineups', 'user'])->findOrFail($id);

        // Track view dengan firstOrCreate (dedup per session)
        EventView::trackView($event->id, Auth::id());

        return view('tickets.show', compact('event'));
    }

    public function prepareCheckout(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'ticket_id' => 'required|exists:ticket_types,id',
            'quantity' => 'required|integer|min:1|max:5',
        ]);

        $event = Event::findOrFail($request->event_id);

        // Cek event masih jualan tiket
        if (!$event->isSellingTickets()) {
            return back()->with('error', 'Mohon maaf, penjualan tiket untuk event ini sudah ditutup atau belum dibuka.');
        }

        // Cek stock
        $ticket = TicketType::findOrFail($request->ticket_id);
        if ($ticket->stock < $request->quantity) {
            return back()->with('error', "Stok tiket {$ticket->name} tidak cukup. Sisa stok: {$ticket->stock}");
        }

        // Track click "Beli Tiket"
        EventView::trackBuyClick($event->id);

        session(['checkout_data' => $request->all()]);
        return redirect()->route('checkout.form');
    }

    public function checkoutForm()
    {
        $data = session('checkout_data');
        if (!$data) return redirect('/');

        $ticket = TicketType::with('event')->findOrFail($data['ticket_id']);
        $quantity = $data['quantity'];
        $subtotal = $ticket->price * $quantity;

        return view('tickets.checkout', compact('ticket', 'quantity', 'subtotal'));
    }

    public function checkVoucher(Request $request)
    {
        $data = session('checkout_data');
        if (!$data) return response()->json(['success' => false, 'message' => 'Sesi berakhir, silahkan ulang pesanan.']);

        $event = Event::find($data['event_id']);
        if (!$event || !$event->is_voucher_active) {
            return response()->json(['success' => false, 'message' => 'Event ini tidak mengizinkan penggunaan voucher.']);
        }

        $voucher = Voucher::where('code', strtoupper($request->code))
            ->where('user_id', $event->user_id)
            ->where('status', 'active')
            ->first();

        if (!$voucher) {
            return response()->json(['success' => false, 'message' => 'Kode Voucher tidak valid.']);
        }

        if ($voucher->quota > 0 && $voucher->used >= $voucher->quota) {
            return response()->json(['success' => false, 'message' => 'Kuota pemakaian voucher ini sudah habis.']);
        }

        $ticket = TicketType::find($data['ticket_id']);
        $subtotal = $ticket->price * $data['quantity'];

        $discount = 0;
        if ($voucher->type == 'nominal') {
            $discount = $voucher->amount;
        } else {
            $discount = $subtotal * ($voucher->amount / 100);
        }

        if ($discount > $subtotal) $discount = $subtotal;
        $final = $subtotal - $discount;

        return response()->json([
            'success' => true,
            'discount_formatted' => number_format($discount, 0, ',', '.'),
            'final_formatted' => number_format($final, 0, ',', '.'),
        ]);
    }

    public function processPayment(Request $request)
    {
        $data = session('checkout_data');
        if (!$data) return response()->json(['success' => false, 'message' => 'Sesi Berakhir, silahkan ulangi pesanan.']);

        $ticketType = TicketType::findOrFail($data['ticket_id']);
        $event = Event::findOrFail($data['event_id']);

        $totalPrice = $ticketType->price * $data['quantity'];
        $discount = 0;

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
                $voucher->increment('used');
            }
        }

        $finalPrice = max(0, $totalPrice - $discount);
        $pricePerTicket = $finalPrice / $data['quantity'];

        // ==========================================================
        // CALCULATE FEE 5% PER TIKET
        // ==========================================================
        $feePerTicket = (int) round($pricePerTicket * self::SERVICE_FEE_RATE);
        $netPerTicket = (int) round($pricePerTicket - $feePerTicket);

        $baseId = 'SPX-' . strtoupper(Str::random(8));

        $names = $request->names;
        $emails = $request->emails;

        $order = null;
        for ($i = 0; $i < $data['quantity']; $i++) {
            $orderNumber = $baseId . '-' . ($i + 1);

            // Unique QR token (beda dengan order_number, lebih sulit ditebak)
            $qrToken = 'QR-' . strtoupper(Str::random(16)) . '-' . time();

            $order = Order::create([
                'event_id' => $data['event_id'],
                'ticket_type_id' => $data['ticket_id'],
                'customer_name' => $names[$i],
                'customer_email' => $emails[$i],
                'customer_phone' => $request->whatsapp,
                'quantity' => 1,
                'total_price' => $pricePerTicket,
                'service_fee' => $feePerTicket,
                'promotor_net' => $netPerTicket,
                'order_number' => $orderNumber,
                'qr_token' => $qrToken,
                'status' => 'success',
            ]);

            $ticketType->decrement('stock', 1);

            try {
                Mail::to($emails[$i])->send(new EticketMail($order));
            } catch (\Exception $e) {
                logger("Gagal kirim email ke: " . $emails[$i] . ' - ' . $e->getMessage());
            }
        }

        // Track conversion
        EventView::trackConversion($data['event_id'], $order->id);

        session()->forget('checkout_data');

        return response()->json([
            'success' => true,
            'redirect' => route('checkout.invoice', $baseId),
        ]);
    }

    public function invoice($base_id)
    {
        $orders = Order::with(['event', 'ticketType'])
            ->where('order_number', 'like', $base_id . '-%')
            ->get();

        if ($orders->isEmpty()) abort(404);

        return view('tickets.invoice', compact('orders'));
    }

    public function edit($id)
    {
        $event = Event::with(['ticketTypes', 'lineups'])->findOrFail($id);
        if ($event->user_id !== Auth::id()) abort(403, 'Aksi tidak diizinkan.');
        $activeVouchers = Voucher::where('user_id', Auth::id())->where('status', 'active')->get();
        return view('promotor.event.edit', compact('event', 'activeVouchers'));
    }

    public function update(Request $request, $id)
    {
        $event = Event::findOrFail($id);
        if ($event->user_id !== Auth::id()) abort(403);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'category' => 'required|string|in:Musik,Olahraga,Seminar,Hiburan',
            'type' => 'required|string|in:Publik,Privat,Private',
            'date' => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'venue' => 'nullable|string|max:255',
            'venue_url' => 'nullable|url|max:500',
            'creator_ig' => 'nullable|string|max:100',
            'sales_start_date' => 'nullable|date',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $request->only(['name', 'category', 'type', 'date', 'description']);
        $data['sales_start_date'] = $request->sales_start_date ?: null;
        $data['is_voucher_active'] = $request->has('is_voucher_active') ? 1 : 0;

        // Field baru
        if ($request->has('venue')) $data['venue'] = $request->venue;
        if ($request->has('venue_url')) $data['venue_url'] = $request->venue_url ?: null;
        if ($request->has('start_time')) $data['start_time'] = $request->start_time ?: null;
        if ($request->has('end_time')) $data['end_time'] = $request->end_time ?: null;
        if ($request->has('creator_ig')) $data['creator_ig'] = $this->sanitizeIgUsername($request->creator_ig);

        // Sponsors
        if ($request->has('sponsor_name') && is_array($request->sponsor_name)) {
            $sponsors = [];
            foreach ($request->sponsor_name as $idx => $name) {
                $name = trim($name ?? '');
                if (empty($name)) continue;
                $sponsors[] = [
                    'name' => mb_substr($name, 0, 100),
                    'ig' => $this->sanitizeIgUsername($request->sponsor_ig[$idx] ?? null),
                ];
            }
            $data['sponsors'] = $sponsors;
        }

        if ($request->hasFile('banner')) {
            if ($event->banner && Storage::disk('public')->exists($event->banner)) {
                Storage::disk('public')->delete($event->banner);
            }
            $data['banner'] = $request->file('banner')->store('banners', 'public');
        }

        $event->update($data);

        if ($request->has('t_names')) {
            foreach ($request->t_names as $key => $name) {
                $price = (int) str_replace('.', '', $request->t_prices[$key] ?? '0');
                $stock = (int) str_replace('.', '', $request->t_stocks[$key] ?? '0');
                $ticketId = $request->t_ids[$key] ?? null;

                if ($ticketId) {
                    $ticket = TicketType::find($ticketId);
                    if ($ticket) $ticket->update(['name' => $name, 'price' => $price, 'stock' => $stock]);
                } else {
                    TicketType::create(['event_id' => $event->id, 'name' => $name, 'price' => $price, 'stock' => $stock]);
                }
            }
        }

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

        return redirect('/promotor/dashboard')->with('success', 'Event diperbarui!');
    }

    // ============================================================
    // HELPERS
    // ============================================================

    /**
     * Sanitize Instagram username — strip @, URL prefix, trailing slash.
     * Input "https://instagram.com/cocacola/" → "cocacola"
     * Input "@cocacola" → "cocacola"
     * Input "cocacola" → "cocacola"
     */
    private function sanitizeIgUsername(?string $input): ?string
    {
        if (!$input) return null;
        $clean = trim($input);
        $clean = preg_replace('/^https?:\/\/(www\.)?instagram\.com\//i', '', $clean);
        $clean = ltrim($clean, '@/');
        $clean = rtrim($clean, '/');
        $clean = mb_substr($clean, 0, 100);
        return $clean ?: null;
    }
}