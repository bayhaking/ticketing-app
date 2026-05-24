<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Order;
use App\Models\TicketType;
use App\Models\User;
use App\Models\Wallet;
use App\Services\EventInsightService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class PromotorDashboardController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        $filterEventId = $request->query('event_id');

        if ($filterEventId) {
            $exists = Event::where('id', (int) $filterEventId)
                ->where('user_id', $userId)
                ->exists();
            if (!$exists) {
                $filterEventId = null;
            }
        }

        // ============================================================
        // FITUR DOMPET (WALLET) - MATT FIX
        // ============================================================
        $wallet = Wallet::firstOrCreate(['user_id' => $userId]);

        // ============================================================
        // STATISTIK UTAMA
        // ============================================================
        $upcomingEvents = Event::where('user_id', $userId)
            ->whereIn('status', ['upcoming', 'live'])
            ->count();

        $ordersBaseQuery = Order::whereHas('event', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })->where('status', 'success');

        $totalTicketsSold = (clone $ordersBaseQuery)->where('total_price', '>', 0)->sum('quantity');
        $totalTransactions = (clone $ordersBaseQuery)->where('total_price', '>', 0)->count();
        $totalRevenue = (clone $ordersBaseQuery)->sum('total_price');
        $totalStaff = $this->getStaffCount($userId);
        $totalGuestlist = (clone $ordersBaseQuery)->where('total_price', 0)->count();

        // CHART DATA
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

        // DISTRIBUSI
        $distributionQuery = Order::whereHas('event', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->where('status', 'success')
            ->where('total_price', '>', 0);

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

        // EVENT DETAILS
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
                'status' => $event->status ?? 'upcoming', 
                'status_label' => $this->getStatusLabel($event->status ?? 'upcoming'),
                'sales_closed_manually' => $event->sales_closed_manually ?? false,
                'views' => $event->views ?? 0,
                'date' => $event->date,
                'initial_quota' => $quota,
                'sold' => $sold,
                'percentage' => $quota > 0 ? round(($sold / $quota) * 100) : 0,
                'remaining' => $event->ticketTypes->sum('stock'),
                'revenue' => $paidOrders->sum('total_price'),
                'transactions' => $paidOrders->count(),
                'attendees' => $uniqueAttendees,
            ];
        });

        $eventsForFilter = $events->map(fn($e) => ['id' => $e->id, 'name' => $e->name]);

        return view('promotor.dashboard', compact(
            'upcomingEvents', 'totalTicketsSold', 'totalTransactions',
            'totalRevenue', 'chartData', 'eventDetails', 'totalStaff',
            'totalGuestlist', 'events', 'distribution', 'eventsForFilter',
            'filterEventId', 'wallet'
        ));
    }

    public function closeSales($id)
    {
        $event = Event::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($event->status === 'finished') {
            return back()->with('error', 'Event sudah selesai, tidak bisa di-close lagi.');
        }

        $event->update([
            'status' => 'live',
            'sales_closed_manually' => true,
        ]);

        return back()->with('success', "Penjualan tiket untuk {$event->name} sudah ditutup. Scanner tetap aktif.");
    }

    public function reopenSales($id)
    {
        $event = Event::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($event->status === 'finished') {
            return back()->with('error', 'Event sudah selesai, tidak bisa dibuka lagi.');
        }

        $event->update([
            'status' => 'upcoming',
            'sales_closed_manually' => false,
        ]);

        return back()->with('success', "Penjualan tiket untuk {$event->name} sudah dibuka kembali.");
    }

    public function finishEvent($id)
    {
        $event = Event::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $event->update([
            'status' => 'finished',
            'sales_closed_manually' => true,
        ]);

        return back()->with('success', "Event {$event->name} ditandai sebagai SELESAI.");
    }

    // ============================================================
    // MATT FIX: FUNGSI INSIGHT YANG SUDAH TERHUBUNG KE SERVICE
    // ============================================================
    public function getInsight($id)
    {
        $event = Event::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $insightData = (new EventInsightService($event))->generate();

        return response()->json([
            'event' => [
                'id' => $event->id,
                'name' => $event->name,
                'date' => $event->date,
                'status' => $event->status ?? 'upcoming',
                'status_label' => $this->getStatusLabel($event->status ?? 'upcoming'),
            ],
            'insight' => $insightData,
        ]);
    }

    public function destroy($id)
    {
        $event = Event::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $event->delete();

        return back()->with('success', 'Event berhasil dihapus secara permanen!');
    }

    private function getStaffCount($promotorId): int
    {
        if (Schema::hasColumn('users', 'parent_promotor_id')) {
            return User::where('role', 'staff')
                ->where('parent_promotor_id', $promotorId)
                ->count();
        }
        return User::where('role', 'staff')->count();
    }

    private function getStatusLabel(string $status): array
    {
        return match($status) {
            'upcoming' => ['label' => '🟢 AKTIF', 'color' => '#1DB954', 'desc' => 'Tiket dijual'],
            'live' => ['label' => '🔴 LIVE', 'color' => '#ff6b6b', 'desc' => 'Hari H, penjualan ditutup'],
            'finished' => ['label' => '⚫ SELESAI', 'color' => '#a0a0a0', 'desc' => 'Event berakhir'],
            default => ['label' => '⚪ DRAFT', 'color' => '#94a3b8', 'desc' => 'Status tidak diketahui'],
        };
    }
}