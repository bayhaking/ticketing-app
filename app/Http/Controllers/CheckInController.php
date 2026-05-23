<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CheckInController extends Controller
{
    /**
     * Tampilkan halaman scanner.
     */
    public function index()
    {
        return view('promotor.scanner');
    }

    /**
     * Validasi tiket — return JSON dengan detail.
     */
    public function validateTicket(Request $request)
    {
        $validated = $request->validate([
            'order_number' => 'required|string|max:50',
        ]);

        $orderNumber = trim($validated['order_number']);

        // Cari order
        $order = Order::with(['event', 'ticketType'])
            ->where('order_number', $orderNumber)
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Tiket Tidak Valid',
                'detail' => 'Kode tidak ditemukan di sistem.',
            ]);
        }

        // Auth check
        $user = Auth::user();
        if ($user->role === 'staff') {
            if ($order->event->user_id !== $user->parent_promotor_id) {
                return response()->json(['success' => false, 'message' => 'Bukan Event Anda']);
            }
        } elseif ($user->role === 'promotor') {
            if ($order->event->user_id !== $user->id) {
                return response()->json(['success' => false, 'message' => 'Bukan Event Anda']);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'Akses Ditolak'], 403);
        }

        // Status Check
        if ($order->status !== 'success') {
            return response()->json(['success' => false, 'message' => 'Tiket Belum Lunas']);
        }

        // Sudah pernah discan?
        if ($order->scanned_at) {
            return response()->json([
                'success' => false,
                'message' => 'Sudah Discan!',
                'customer_name' => $order->customer_name,
                'event_name' => $order->event->name,
                'ticket_type' => $order->ticketType->name ?? '-',
                'scanned_at' => Carbon::parse($order->scanned_at)->format('d M, H:i'),
            ]);
        }

        // VALID — update scanned_at
        $order->update([
            'scanned_at' => now(),
            'scanned_by' => $user->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tiket Valid!',
            'customer_name' => $order->customer_name,
            'event_name' => $order->event->name,
            'ticket_type' => $order->ticketType->name ?? '-',
            'scanned_at' => now()->format('d M, H:i'),
        ]);
    }
}