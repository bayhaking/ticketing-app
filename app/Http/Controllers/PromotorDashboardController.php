<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Order;
use App\Models\TicketType;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PromotorDashboardController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();

        // ============================================================
        // FILTER: event_id (optional) — untuk chart per-event
        // ============================================================
        $filterEventId = $request->query('event_id');

        // Validasi: pastikan event_id (jika ada) milik promotor ini (cegah IDOR)
        if ($filterEventId) {
            $exists = Event::where('id', (int) $filterEventId)
                ->where('user_id', $userId)
                ->exists();
            if (!$exists) {
                $filterEventId = null; // ignore filter kalau invalid
            }
        }

        // ============================================================
        // 1. STATISTIK UTAMA
        // ============================================================
        $upcomingEvents = Event::where('user_id', $userId)
            ->where('status', 'upcoming')
            ->count();

        $ordersBaseQuery = Order::whereHas('event', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })->where('status', 'success');

        $totalTicketsSold = (clone $ordersBaseQuery)
            ->where('total_price', '>', 0)
            ->sum('quantity');

        $totalTransactions = (clone $ordersBaseQuery)
            ->where('total_price', '>', 0)
            ->count();

        $totalRevenue = (clone $ordersBaseQuery)->sum('total_price');

        // Total Staff — HANYA staff milik promotor ini (butuh kolom parent_promotor_id)
        // Kalau belum ada kolom itu, sementara pakai count semua staff (TEMPORARY)
        $totalStaff = $this->getStaffCount($userId);

        $totalGuestlist = (clone $ordersBaseQuery)
            ->where('total_price', 0)
            ->count();

        // ============================================================
        // 2. CHART DATA — dengan filter event
        // ============================================================
        $chartQuery = Order::whereHas('event', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->where('status', 'success')
            ->where('created_at', '>=', now()->subDays(90));

        if ($filterEventId) {
            $chartQuery->where('event_id', (int) $filterEventId);
        }

        $chartData = $chartQuery
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_price) as total'),
                DB::raw('COUNT(*) as transactions'),
                DB::raw('SUM(quantity) as tickets')
            )
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        // ============================================================
        // 3. DISTRIBUSI JUMLAH TIKET PER TRANSAKSI
        // ============================================================
        // Kategorisasi: 1 tiket, 2 tiket, ..., 10+ tiket
        $distributionQuery = Order::whereHas('event', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->where('status', 'success')
            ->where('total_price', '>', 0); // exclude guestlist

        if ($filterEventId) {
            $distributionQuery->where('event_id', (int) $filterEventId);
        }

        $rawDistribution = $distributionQuery
            ->select(
                'quantity',
                DB::raw('COUNT(*) as transaction_count'),
                DB::raw('SUM(total_price) as revenue')
            )
            ->groupBy('quantity')
            ->orderBy('quantity', 'asc')
            ->get();

        // Normalisasi ke 10 bucket
        $distribution = [];
        for ($i = 1; $i <= 10; $i++) {
            $distribution[$i] = [
                'label' => $i == 10 ? '10+ tiket' : $i . ' tiket',
                'transactions' => 0,
                'tickets' => 0,
                'revenue' => 0,
            ];
        }

        foreach ($rawDistribution as $row) {
            $bucket = min((int) $row->quantity, 10);
            $distribution[$bucket]['transactions'] += $row->transaction_count;
            $distribution[$bucket]['tickets'] += $row->quantity * $row->transaction_count;
            $distribution[$bucket]['revenue'] += $row->revenue;
        }

        // ============================================================
        // 4. DETAIL PENJUALAN PER EVENT
        // ============================================================
        $events = Event::where('user_id', $userId)
            ->with([
                'ticketTypes',
                'orders' => fn($q) => $q->where('status', 'success'),
            ])
            ->orderBy('date', 'desc')
            ->get();

        $eventDetails = $events->map(function ($event) {
            $paidOrders = $event->orders->where('total_price', '>', 0);
            $sold = $paidOrders->sum('quantity');
            $quota = $event->ticketTypes->sum('stock') + $sold;
            $uniqueAttendees = $event->orders->unique('customer_email')->count();

            return [
                'id' => $event->id,
                'name' => $event->name,
                'status' => $event->status == 'upcoming' ? 'Aktif' : 'Selesai',
                'views' => $event->views,
                'initial_quota' => $quota,
                'sold' => $sold,
                'percentage' => $quota > 0 ? round(($sold / $quota) * 100) : 0,
                'remaining' => $event->ticketTypes->sum('stock'),
                'revenue' => $paidOrders->sum('total_price'),
                'transactions' => $paidOrders->count(),
                'attendees' => $uniqueAttendees,
            ];
        });

        // Untuk dropdown filter
        $eventsForFilter = $events->map(fn($e) => ['id' => $e->id, 'name' => $e->name]);

        return view('promotor.dashboard', compact(
            'upcomingEvents', 'totalTicketsSold', 'totalTransactions',
            'totalRevenue', 'chartData', 'eventDetails', 'totalStaff',
            'totalGuestlist', 'events', 'distribution', 'eventsForFilter',
            'filterEventId'
        ));
    }

    public function toggleStatus($id)
    {
        $event = Event::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $event->status = ($event->status == 'upcoming') ? 'finished' : 'upcoming';
        $event->save();

        return back()->with('success', 'Status event berhasil diubah!');
    }

    public function destroy($id)
    {
        $event = Event::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $event->delete();

        return back()->with('success', 'Event berhasil dihapus secara permanen!');
    }

    /**
     * Helper untuk hitung staff milik promotor.
     * Kalau kolom parent_promotor_id ada → pakai itu.
     * Kalau belum ada → fallback ke total semua staff (TEMPORARY).
     *
     * TODO: tambah migration kolom parent_promotor_id di tabel users
     * supaya staff bisa dipisahkan per promotor.
     */
    private function getStaffCount($promotorId): int
    {
        // Cek apakah kolom parent_promotor_id sudah ada di tabel users
        if (\Schema::hasColumn('users', 'parent_promotor_id')) {
            return User::where('role', 'staff')
                ->where('parent_promotor_id', $promotorId)
                ->count();
        }

        // Fallback: count semua staff (kurang aman, sementara saja)
        return User::where('role', 'staff')->count();
    }
}