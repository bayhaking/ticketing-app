<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class CheckInController extends Controller
{
    public function index() {
        return view('checkin.index');
    }

    public function validateTicket(Request $request) {
        $order = Order::where('order_number', $request->order_number)
                      ->where('status', 'success')
                      ->with('event')
                      ->first();

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Tiket tidak ditemukan atau belum lunas!']);
        }

        if ($order->scanned_at) {
            return response()->json([
                'success' => false, 
                'message' => 'Tiket SUDAH PERNAH digunakan pada ' . $order->scanned_at->format('H:i')
            ]);
        }

        // Simpan waktu scan dan SIAPA yang scan
        $order->update([
            'scanned_at' => now(),
            'scanned_by' => Auth::id(), // ID Staff/Promotor yang login
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Check-in Berhasil!',
            'attendee' => $order->customer_name,
            'event' => $order->event->name,
            'scanner_name' => Auth::user()->name // Munculkan nama yang scan di layar HP
        ]);
    }
}