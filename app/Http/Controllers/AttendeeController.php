<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Order;
use App\Models\TicketType;
use App\Models\GuestlistCategory;
use App\Models\User; // <--- PASTIKAN INI ADA
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\EticketMail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class AttendeeController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        
        // Data Events
        $events = Event::where('user_id', $userId)->with('guestlistCategories')->latest()->get();

        // 1. DATA PEMBELI REGULER
        $orderQuery = Order::whereHas('event', function($q) use ($userId) {
            $q->where('user_id', $userId);
        })->where('status', 'success')->where('total_price', '>', 0)->with(['event', 'ticketType', 'scanner']);

        // 2. DATA GUESTLIST
        $guestQuery = Order::whereHas('event', function($q) use ($userId) {
            $q->where('user_id', $userId);
        })->where('status', 'success')->where('total_price', 0)->with(['event', 'guestlistCategory', 'scanner']);

        if ($request->filled('event_id')) {
            $orderQuery->where('event_id', $request->event_id);
            $guestQuery->where('event_id', $request->event_id);
        }

        $orders = $orderQuery->latest()->get();
        $guests = $guestQuery->latest()->get();
        
        // 3. DATA STAFF (AMBIL SEMUA YANG ROLE STAFF)
        // Kita pakai get() biasa tanpa filter event agar tidak tertelan
        $staffs = User::where('role', 'staff')->latest()->get();

        // DEBUG: Hapus tanda // di bawah ini kalau masih (0) buat cek manual
        // dd($staffs); 

        return view('promotor.attendees', compact('events', 'orders', 'guests', 'staffs'));
    }

    public function storeStaff(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        // SIMPAN KE DATABASE
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'plain_password' => $request->password, // Agar Boy bisa liat password staff
            'role' => 'staff',
        ]);

        return back()->with('success', 'Akun Staff ' . $user->name . ' berhasil dibuat!');
    }

    // Fungsi Category & Guestlist biarkan tetap sama seperti sebelumnya...
    public function storeCategory(Request $request) {
        GuestlistCategory::create(['event_id' => $request->event_id, 'name' => $request->category_name]);
        return back()->with('success', 'Kategori Guestlist berhasil dibuat!');
    }

    public function storeGuest(Request $request) {
        $ticketType = TicketType::where('event_id', $request->event_id)->first();
        $order = Order::create([
            'event_id' => $request->event_id,
            'ticket_type_id' => $ticketType->id,
            'guestlist_category_id' => $request->guest_category_id,
            'customer_name' => $request->name,
            'customer_email' => $request->email,
            'customer_phone' => $request->phone,
            'quantity' => 1,
            'total_price' => 0, 
            'order_number' => 'GST-' . strtoupper(Str::random(8)),
            'status' => 'success' 
        ]);
        $ticketType->decrement('stock', 1);
        try { Mail::to($request->email)->send(new EticketMail($order)); } catch (\Exception $e) {}
        return back()->with('success', 'E-Tiket Guestlist berhasil dikirim!');
    }

    public function resendTicket(Request $request, $id) {
        $order = Order::findOrFail($id);
        if ($request->filled('new_email')) { $order->update(['customer_email' => $request->new_email]); }
        Mail::to($order->customer_email)->send(new EticketMail($order));
        return back()->with('success', 'E-Tiket berhasil dikirim ulang!');
    }
}