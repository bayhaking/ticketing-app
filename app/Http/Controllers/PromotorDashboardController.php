<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Order;
use App\Models\TicketType;
use App\Models\User; 
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PromotorDashboardController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        // 1. STATISTIK UTAMA
        $upcomingEvents = Event::where('user_id', $userId)->where('status', 'upcoming')->count();
        
        $ordersQuery = Order::whereHas('event', function($q) use ($userId) {
            $q->where('user_id', $userId);
        })->where('status', 'success');

        $totalTicketsSold = $ordersQuery->sum('quantity');
        $totalTransactions = $ordersQuery->count();
        $totalRevenue = $ordersQuery->sum('total_price');

        // INI DIA VARIABEL YANG TADI HILANG
        $totalStaff = User::where('role', 'staff')->count();
        $totalGuestlist = (clone $ordersQuery)->where('total_price', 0)->count();

        // 2. DATA GRAFIK (7 Hari Terakhir)
        $chartData = Order::whereHas('event', function($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->where('status', 'success')
            ->where('created_at', '>=', now()->subDays(7))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_price) as total'))
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        // 3. DETAIL PENJUALAN & TIKET
        $events = Event::where('user_id', $userId)
            ->with(['ticketTypes', 'orders' => fn($q) => $q->where('status', 'success')])
            ->orderBy('date', 'desc')
            ->get();

        $eventDetails = $events->map(function($event) {
            $sold = $event->orders->sum('quantity');
            $quota = $event->ticketTypes->sum('stock') + $sold;
            return [
                'id' => $event->id,
                'name' => $event->name,
                'status' => $event->status == 'upcoming' ? 'Aktif' : 'Selesai',
                'views' => $event->views,
                'initial_quota' => $quota,
                'sold' => $sold,
                'percentage' => $quota > 0 ? round(($sold / $quota) * 100) : 0,
                'remaining' => $event->ticketTypes->sum('stock'),
                'revenue' => $event->orders->sum('total_price'),
                'transactions' => $event->orders->count(),
            ];
        });

        // PASTIKAN SEMUA MASUK COMPACT
        return view('promotor.dashboard', compact(
            'upcomingEvents', 'totalTicketsSold', 'totalTransactions', 
            'totalRevenue', 'chartData', 'eventDetails', 'totalStaff', 'totalGuestlist', 'events'
        ));
    }

    public function toggleStatus($id)
    {
        $event = Event::where('id', $id)->where('user_id', auth()->id())->firstOrFail();
        $event->status = ($event->status == 'upcoming') ? 'finished' : 'upcoming';
        $event->save();
        return back()->with('success', 'Status event berhasil diubah!');
    }

    public function destroy($id)
    {
        $event = Event::where('id', $id)->where('user_id', auth()->id())->firstOrFail();
        $event->delete();
        return back()->with('success', 'Event berhasil dihapus secara permanen!');
    }
}