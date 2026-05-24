<?php

namespace App\Services;

use App\Models\Event;
use App\Models\EventView;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Service untuk menghitung insight & metrics per event.
 *
 * Usage:
 *   $insight = (new EventInsightService($event))->generate();
 */
class EventInsightService
{
    protected Event $event;

    public function __construct(Event $event)
    {
        $this->event = $event;
    }

    /**
     * Generate semua insight dalam satu array.
     */
    public function generate(): array
    {
        return [
            'big_numbers' => $this->getBigNumbers(),
            'funnel' => $this->getFunnel(),
            'time_to_convert' => $this->getTimeToConvert(),
            'hourly_traffic' => $this->getHourlyTraffic(),
            'top_sources' => $this->getTopSources(),
            'device_breakdown' => $this->getDeviceBreakdown(),
            'daily_trend' => $this->getDailyTrend(),
        ];
    }

    /**
     * BIG NUMBERS: Total views, unique sessions, clicks, sales, conversion rate.
     */
    public function getBigNumbers(): array
    {
        $totalViews = EventView::where('event_id', $this->event->id)->count();
        $uniqueSessions = EventView::where('event_id', $this->event->id)
            ->distinct('session_id')
            ->count('session_id');
        $clickBuy = EventView::where('event_id', $this->event->id)
            ->whereNotNull('clicked_buy_at')
            ->count();
        $conversions = EventView::where('event_id', $this->event->id)
            ->whereNotNull('converted_to_order_id')
            ->count();

        // Total tiket terjual (langsung dari orders, biar akurat)
        $ticketsSold = Order::where('event_id', $this->event->id)
            ->where('status', 'success')
            ->where('total_price', '>', 0)
            ->sum('quantity');

        $revenue = Order::where('event_id', $this->event->id)
            ->where('status', 'success')
            ->sum('total_price');

        return [
            'total_views' => $totalViews,
            'unique_sessions' => $uniqueSessions,
            'click_buy' => $clickBuy,
            'conversions' => $conversions,
            'tickets_sold' => $ticketsSold,
            'revenue' => $revenue,
            'ctr' => $totalViews > 0 ? round(($clickBuy / $totalViews) * 100, 2) : 0,
            'conversion_rate' => $totalViews > 0 ? round(($conversions / $totalViews) * 100, 2) : 0,
            'click_to_convert_rate' => $clickBuy > 0 ? round(($conversions / $clickBuy) * 100, 2) : 0,
        ];
    }

    /**
     * FUNNEL: View → Click "Beli" → Mulai Checkout → Bayar Lunas
     */
    public function getFunnel(): array
    {
        $views = EventView::where('event_id', $this->event->id)->count();
        $clicks = EventView::where('event_id', $this->event->id)
            ->whereNotNull('clicked_buy_at')
            ->count();
        $startedCheckout = EventView::where('event_id', $this->event->id)
            ->whereNotNull('converted_to_order_id')
            ->count();
        $paid = EventView::where('event_id', $this->event->id)
            ->whereNotNull('converted_to_order_id')
            ->whereHas('order', function ($q) {
                $q->where('status', 'success');
            })
            ->count();

        return [
            ['stage' => 'Lihat Event', 'count' => $views, 'percentage' => 100],
            ['stage' => 'Klik Beli Tiket', 'count' => $clicks, 'percentage' => $views > 0 ? round(($clicks / $views) * 100, 1) : 0],
            ['stage' => 'Mulai Checkout', 'count' => $startedCheckout, 'percentage' => $views > 0 ? round(($startedCheckout / $views) * 100, 1) : 0],
            ['stage' => 'Bayar Lunas', 'count' => $paid, 'percentage' => $views > 0 ? round(($paid / $views) * 100, 1) : 0],
        ];
    }

    /**
     * TIME TO CONVERT: Berapa lama dari view → bayar lunas.
     * Distribusi: <5 min, 5-15 min, 15-30 min, 30-60 min, >1 jam, >24 jam
     */
    public function getTimeToConvert(): array
    {
        $conversions = EventView::where('event_id', $this->event->id)
            ->whereNotNull('converted_to_order_id')
            ->whereHas('order', function ($q) {
                $q->where('status', 'success');
            })
            ->with('order')
            ->get();

        $buckets = [
            '< 5 menit' => 0,
            '5-15 menit' => 0,
            '15-30 menit' => 0,
            '30-60 menit' => 0,
            '1-24 jam' => 0,
            '> 24 jam' => 0,
        ];

        $totalMinutes = 0;
        $count = 0;

        foreach ($conversions as $view) {
            if (!$view->order || !$view->viewed_at) continue;

            $minutes = $view->viewed_at->diffInMinutes($view->order->created_at);
            $totalMinutes += $minutes;
            $count++;

            if ($minutes < 5) $buckets['< 5 menit']++;
            elseif ($minutes < 15) $buckets['5-15 menit']++;
            elseif ($minutes < 30) $buckets['15-30 menit']++;
            elseif ($minutes < 60) $buckets['30-60 menit']++;
            elseif ($minutes < 1440) $buckets['1-24 jam']++;
            else $buckets['> 24 jam']++;
        }

        $avgMinutes = $count > 0 ? round($totalMinutes / $count, 1) : 0;

        return [
            'distribution' => $buckets,
            'average_minutes' => $avgMinutes,
            'average_label' => $this->formatDuration($avgMinutes),
            'total_converted' => $count,
        ];
    }

    /**
     * HOURLY TRAFFIC: Heatmap jam berapa paling rame.
     */
    public function getHourlyTraffic(): array
    {
        $raw = EventView::where('event_id', $this->event->id)
            ->select(
                DB::raw('CAST(strftime("%H", viewed_at) AS INTEGER) as hour'),
                DB::raw('COUNT(*) as views'),
                DB::raw('SUM(CASE WHEN converted_to_order_id IS NOT NULL THEN 1 ELSE 0 END) as conversions')
            )
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        // Build 24-hour array (0-23)
        $hourly = [];
        for ($i = 0; $i < 24; $i++) {
            $hourly[$i] = [
                'hour' => $i,
                'label' => sprintf('%02d:00', $i),
                'views' => 0,
                'conversions' => 0,
            ];
        }

        foreach ($raw as $row) {
            $h = (int) $row->hour;
            if (isset($hourly[$h])) {
                $hourly[$h]['views'] = (int) $row->views;
                $hourly[$h]['conversions'] = (int) $row->conversions;
            }
        }

        return array_values($hourly);
    }

    /**
     * TOP SOURCES: Dari mana traffic datang.
     */
    public function getTopSources(): array
    {
        $sources = EventView::where('event_id', $this->event->id)
            ->select(
                DB::raw('COALESCE(utm_source, referrer, "direct") as source'),
                DB::raw('COUNT(*) as views'),
                DB::raw('SUM(CASE WHEN clicked_buy_at IS NOT NULL THEN 1 ELSE 0 END) as clicks'),
                DB::raw('SUM(CASE WHEN converted_to_order_id IS NOT NULL THEN 1 ELSE 0 END) as conversions')
            )
            ->groupBy('source')
            ->orderBy('views', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($row) {
                return [
                    'source' => $this->prettifySource($row->source),
                    'views' => (int) $row->views,
                    'clicks' => (int) $row->clicks,
                    'conversions' => (int) $row->conversions,
                    'conversion_rate' => $row->views > 0 ? round(($row->conversions / $row->views) * 100, 2) : 0,
                ];
            });

        return $sources->toArray();
    }

    /**
     * DEVICE BREAKDOWN: Mobile vs Desktop vs Tablet.
     */
    public function getDeviceBreakdown(): array
    {
        $devices = EventView::where('event_id', $this->event->id)
            ->select(
                DB::raw('COALESCE(device_type, "unknown") as device'),
                DB::raw('COUNT(*) as views'),
                DB::raw('SUM(CASE WHEN converted_to_order_id IS NOT NULL THEN 1 ELSE 0 END) as conversions')
            )
            ->groupBy('device')
            ->orderBy('views', 'desc')
            ->get()
            ->map(function ($row) {
                return [
                    'device' => ucfirst($row->device),
                    'views' => (int) $row->views,
                    'conversions' => (int) $row->conversions,
                ];
            });

        return $devices->toArray();
    }

    /**
     * DAILY TREND: Views per hari 30 hari terakhir.
     */
    public function getDailyTrend(): array
    {
        $trend = EventView::where('event_id', $this->event->id)
            ->where('viewed_at', '>=', now()->subDays(30))
            ->select(
                DB::raw('DATE(viewed_at) as date'),
                DB::raw('COUNT(*) as views'),
                DB::raw('SUM(CASE WHEN clicked_buy_at IS NOT NULL THEN 1 ELSE 0 END) as clicks'),
                DB::raw('SUM(CASE WHEN converted_to_order_id IS NOT NULL THEN 1 ELSE 0 END) as conversions')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return $trend->toArray();
    }

    /**
     * Helper: Format durasi menit jadi human-readable.
     */
    private function formatDuration(float $minutes): string
    {
        if ($minutes < 1) return '< 1 menit';
        if ($minutes < 60) return round($minutes) . ' menit';
        if ($minutes < 1440) return round($minutes / 60, 1) . ' jam';
        return round($minutes / 1440, 1) . ' hari';
    }

    /**
     * Helper: Bersihkan nama source untuk display.
     */
    private function prettifySource(string $source): string
    {
        $map = [
            'direct' => '🌐 Direct',
            'instagram.com' => '📷 Instagram',
            'm.instagram.com' => '📷 Instagram',
            'l.instagram.com' => '📷 Instagram',
            'facebook.com' => '📘 Facebook',
            'm.facebook.com' => '📘 Facebook',
            'l.facebook.com' => '📘 Facebook',
            'twitter.com' => '🐦 Twitter',
            'x.com' => '🐦 X (Twitter)',
            'tiktok.com' => '🎵 TikTok',
            't.co' => '🐦 Twitter',
            'youtube.com' => '▶️ YouTube',
            'youtu.be' => '▶️ YouTube',
            'wa.me' => '💬 WhatsApp',
            'whatsapp.com' => '💬 WhatsApp',
            'api.whatsapp.com' => '💬 WhatsApp',
            'google.com' => '🔍 Google',
            'bing.com' => '🔍 Bing',
            'spectix.com' => '🎫 Spectix',
        ];

        return $map[strtolower($source)] ?? mb_substr($source, 0, 30);
    }
}