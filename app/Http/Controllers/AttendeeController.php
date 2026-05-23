<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Order;
use App\Models\TicketType;
use App\Models\GuestlistCategory;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use App\Mail\EticketMail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AttendeeController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();

        // ============================================================
        // EVENTS — semua event milik promotor ini
        // ============================================================
        $events = Event::where('user_id', $userId)
            ->with('guestlistCategories')
            ->withCount(['orders as attendees_count' => function ($q) {
                $q->where('status', 'success')->where('total_price', '>', 0);
            }])
            ->orderBy('date', 'desc')
            ->get();

        $totalAllAttendees = $events->sum('attendees_count');

        // ============================================================
        // FILTER: event_id (cegah IDOR)
        // ============================================================
        $filterEventId = $request->query('event_id');
        $currentEvent = null;

        if ($filterEventId) {
            $currentEvent = $events->firstWhere('id', (int) $filterEventId);
            if (!$currentEvent) {
                return redirect()->route('promotor.attendees')
                    ->with('error', 'Event tidak ditemukan atau bukan milik Anda.');
            }
        }

        // ============================================================
        // QUERY ORDERS (PEMBELI REGULER, total_price > 0)
        // ============================================================
        $orderQuery = Order::whereHas('event', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->where('status', 'success')
            ->where('total_price', '>', 0)
            ->with(['event', 'ticketType', 'scanner']);

        // Filter: event
        if ($filterEventId) {
            $orderQuery->where('event_id', (int) $filterEventId);
        }

        // Filter: kategori jumlah tiket
        $qtyCategory = $request->query('qty_category');
        if ($qtyCategory) {
            switch ($qtyCategory) {
                case 'solo':
                    $orderQuery->where('quantity', 1);
                    break;
                case 'pair':
                    $orderQuery->where('quantity', 2);
                    break;
                case 'small_group':
                    $orderQuery->whereBetween('quantity', [3, 5]);
                    break;
                case 'large_group':
                    $orderQuery->whereBetween('quantity', [6, 9]);
                    break;
                case 'bulk':
                    $orderQuery->where('quantity', '>=', 10);
                    break;
            }
        }

        // Filter: search
        $search = $request->query('search');
        if ($search) {
            $search = mb_substr(trim($search), 0, 100);
            $orderQuery->where(function ($q) use ($search) {
                $q->where('customer_name', 'LIKE', '%' . $search . '%')
                  ->orWhere('customer_email', 'LIKE', '%' . $search . '%')
                  ->orWhere('order_number', 'LIKE', '%' . $search . '%');
            });
        }

        $orders = $orderQuery->latest()->get();

        // ============================================================
        // QUERY GUESTLIST (total_price = 0)
        // ============================================================
        $guestQuery = Order::whereHas('event', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->where('status', 'success')
            ->where('total_price', 0)
            ->with(['event', 'guestlistCategory', 'scanner']);

        if ($filterEventId) {
            $guestQuery->where('event_id', (int) $filterEventId);
        }

        if ($search) {
            $guestQuery->where(function ($q) use ($search) {
                $q->where('customer_name', 'LIKE', '%' . $search . '%')
                  ->orWhere('customer_email', 'LIKE', '%' . $search . '%')
                  ->orWhere('order_number', 'LIKE', '%' . $search . '%');
            });
        }

        $guests = $guestQuery->latest()->get();

        // ============================================================
        // QUERY STAFF — hanya milik promotor ini (kalau kolom ada)
        // ============================================================
        $staffsQuery = User::where('role', 'staff');

        if (Schema::hasColumn('users', 'parent_promotor_id')) {
            $staffsQuery->where('parent_promotor_id', $userId);
        }
        // Kalau kolom belum ada, untuk sementara tampilkan semua (TODO: migration)

        $staffs = $staffsQuery->latest()->get();

        return view('promotor.attendees', compact(
            'events', 'orders', 'guests', 'staffs',
            'currentEvent', 'totalAllAttendees'
        ));
    }

    /**
     * Buat akun staff scanner.
     * SECURITY: Tidak lagi simpan plain_password.
     * Generate password random kalau tidak diisi.
     */
    public function storeStaff(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/[a-z]/',      // wajib ada huruf kecil
                'regex:/[A-Z]/',      // wajib ada huruf besar
                'regex:/[0-9]/',      // wajib ada angka
            ],
        ], [
            'password.regex' => 'Password harus mengandung huruf besar, huruf kecil, dan angka.',
            'password.min' => 'Password minimal 8 karakter.',
            'email.unique' => 'Email sudah digunakan oleh user lain.',
        ]);

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'staff',
        ];

        // Kalau kolom parent_promotor_id ada, set
        if (Schema::hasColumn('users', 'parent_promotor_id')) {
            $userData['parent_promotor_id'] = Auth::id();
        }

        $user = User::create($userData);

        // Tampilkan password SEKALI di flash session, jangan simpan di database
        return back()->with([
            'success' => "Akun Staff {$user->name} berhasil dibuat!",
            'staff_password_notice' => [
                'name' => $user->name,
                'email' => $user->email,
                'password' => $validated['password'], // hanya dikirim sekali ke view
            ],
        ]);
    }

    /**
     * Buat kategori guestlist — dengan validasi authorization.
     */
    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'event_id' => [
                'required',
                'integer',
                Rule::exists('events', 'id')->where('user_id', Auth::id()),
            ],
            'category_name' => 'required|string|max:100',
        ], [
            'event_id.exists' => 'Event tidak ditemukan atau bukan milik Anda.',
        ]);

        GuestlistCategory::create([
            'event_id' => $validated['event_id'],
            'name' => $validated['category_name'],
        ]);

        return back()->with('success', 'Kategori Guestlist berhasil dibuat!');
    }

    /**
     * Tambah tamu ke guestlist — dengan validasi authorization.
     */
    public function storeGuest(Request $request)
    {
        $validated = $request->validate([
            'event_id' => [
                'required',
                'integer',
                Rule::exists('events', 'id')->where('user_id', Auth::id()),
            ],
            'guest_category_id' => [
                'required',
                'integer',
                Rule::exists('guestlist_categories', 'id')->where('event_id', $request->event_id),
            ],
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
        ], [
            'event_id.exists' => 'Event tidak ditemukan atau bukan milik Anda.',
            'guest_category_id.exists' => 'Kategori tidak valid untuk event ini.',
        ]);

        $ticketType = TicketType::where('event_id', $validated['event_id'])->first();

        if (!$ticketType) {
            return back()->with('error', 'Event ini belum punya tipe tiket. Buat tipe tiket dulu.');
        }

        $order = Order::create([
            'event_id' => $validated['event_id'],
            'ticket_type_id' => $ticketType->id,
            'guestlist_category_id' => $validated['guest_category_id'],
            'customer_name' => $validated['name'],
            'customer_email' => $validated['email'],
            'customer_phone' => $validated['phone'],
            'quantity' => 1,
            'total_price' => 0,
            'order_number' => 'GST-' . strtoupper(Str::random(8)),
            'status' => 'success',
        ]);

        $ticketType->decrement('stock', 1);

        try {
            Mail::to($validated['email'])->send(new EticketMail($order));
        } catch (\Exception $e) {
            \Log::error('Gagal kirim e-ticket guestlist: ' . $e->getMessage());
        }

        return back()->with('success', 'E-Tiket Guestlist berhasil dikirim!');
    }

    /**
     * Resend tiket — dengan authorization check.
     */
    public function resendTicket(Request $request, $id)
    {
        // Validasi: order harus milik event yang owned by current promotor
        $order = Order::where('id', $id)
            ->whereHas('event', function ($q) {
                $q->where('user_id', Auth::id());
            })
            ->firstOrFail();

        $validated = $request->validate([
            'new_email' => 'nullable|email|max:255',
        ]);

        if (!empty($validated['new_email'])) {
            $order->update(['customer_email' => $validated['new_email']]);
        }

        try {
            Mail::to($order->customer_email)->send(new EticketMail($order));
        } catch (\Exception $e) {
            \Log::error('Gagal resend e-ticket: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengirim ulang tiket. Coba lagi.');
        }

        return back()->with('success', 'E-Tiket berhasil dikirim ulang!');
    }

    /**
     * MATT FIX: Ekspor data pembeli (Excel / CSV)
     */
    public function export(Request $request)
    {
        $userId = Auth::id();
        $eventIds = Event::where('user_id', $userId)->pluck('id');

        $orderQuery = Order::with(['event', 'ticketType'])
            ->whereIn('event_id', $eventIds)
            ->where('status', 'success')
            ->orderBy('created_at', 'desc');

        if ($request->query('event_id')) {
            $orderQuery->where('event_id', (int) $request->query('event_id'));
        }

        $orders = $orderQuery->get();

        $filename = "Data_Peserta_SPECTIX_" . date('Ymd_His') . ".csv";
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Order ID', 'Nama Pembeli', 'Email', 'WhatsApp', 'Event', 'Tipe Tiket', 'Harga (Rp)', 'Status Kehadiran', 'Waktu Pembelian'];

        $callback = function() use($orders, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($orders as $order) {
                $row = [
                    $order->order_number,
                    $order->customer_name,
                    $order->customer_email,
                    $order->customer_phone,
                    $order->event->name ?? '-',
                    $order->ticketType->name ?? '-',
                    $order->total_price,
                    $order->scanned_at ? 'Hadir (Sudah Scan)' : 'Belum Datang',
                    $order->created_at->format('Y-m-d H:i:s')
                ];
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}