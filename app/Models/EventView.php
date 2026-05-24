<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Jenssegers\Agent\Agent;

class EventView extends Model
{
    protected $fillable = [
        'event_id',
        'session_id',
        'ip_hash',
        'user_id',
        'viewed_at',
        'clicked_buy_at',
        'converted_to_order_id',
        'referrer',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'device_type',
        'browser',
    ];

    protected function casts(): array
    {
        return [
            'viewed_at' => 'datetime',
            'clicked_buy_at' => 'datetime',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'converted_to_order_id');
    }

    /**
     * Helper: track view secara aman (dedup per session).
     * Dipanggil dari TicketController saat user buka halaman event.
     */
    public static function trackView(int $eventId, ?int $userId = null): self
    {
        $sessionId = session()->getId();

        // firstOrCreate cegah duplicate view dalam session yang sama
        return self::firstOrCreate(
            [
                'event_id' => $eventId,
                'session_id' => $sessionId,
            ],
            [
                'ip_hash' => hash('sha256', request()->ip() . config('app.key')),
                'user_id' => $userId,
                'referrer' => self::sanitizeReferrer(request()->headers->get('referer')),
                'utm_source' => self::sanitizeUtm(request()->query('utm_source')),
                'utm_medium' => self::sanitizeUtm(request()->query('utm_medium')),
                'utm_campaign' => self::sanitizeUtm(request()->query('utm_campaign')),
                'device_type' => self::detectDevice(),
                'browser' => self::detectBrowser(),
                'viewed_at' => now(),
            ]
        );
    }

    /**
     * Helper: track click "Beli Tiket".
     */
    public static function trackBuyClick(int $eventId): void
    {
        self::where('event_id', $eventId)
            ->where('session_id', session()->getId())
            ->whereNull('clicked_buy_at')
            ->update(['clicked_buy_at' => now()]);
    }

    /**
     * Helper: track conversion saat order berhasil dibuat.
     */
    public static function trackConversion(int $eventId, int $orderId): void
    {
        self::where('event_id', $eventId)
            ->where('session_id', session()->getId())
            ->whereNull('converted_to_order_id')
            ->update(['converted_to_order_id' => $orderId]);
    }

    /**
     * Sanitize referrer URL (cuma simpan domain, bukan full URL).
     */
    private static function sanitizeReferrer(?string $url): ?string
    {
        if (!$url) return null;
        $parsed = parse_url($url);
        if (!isset($parsed['host'])) return null;
        return mb_substr($parsed['host'], 0, 250);
    }

    /**
     * Sanitize UTM parameter — max 100 char, strip tag berbahaya.
     */
    private static function sanitizeUtm(?string $value): ?string
    {
        if (!$value) return null;
        return mb_substr(strip_tags(trim($value)), 0, 100);
    }

    /**
     * Detect device type dari user agent.
     */
    private static function detectDevice(): string
    {
        try {
            $agent = new Agent();
            if ($agent->isMobile()) return 'mobile';
            if ($agent->isTablet()) return 'tablet';
            if ($agent->isDesktop()) return 'desktop';
        } catch (\Throwable $e) {
            // Fallback kalau library jenssegers/agent tidak ke-install
            $ua = strtolower(request()->userAgent() ?? '');
            if (str_contains($ua, 'mobi')) return 'mobile';
            if (str_contains($ua, 'tablet') || str_contains($ua, 'ipad')) return 'tablet';
            return 'desktop';
        }
        return 'unknown';
    }

    /**
     * Detect browser dari user agent.
     */
    private static function detectBrowser(): ?string
    {
        try {
            $agent = new Agent();
            $browser = $agent->browser();
            return $browser ? mb_substr($browser, 0, 50) : null;
        } catch (\Throwable $e) {
            // Fallback: simple browser detection
            $ua = strtolower(request()->userAgent() ?? '');
            if (str_contains($ua, 'chrome') && !str_contains($ua, 'edge')) return 'Chrome';
            if (str_contains($ua, 'firefox')) return 'Firefox';
            if (str_contains($ua, 'safari') && !str_contains($ua, 'chrome')) return 'Safari';
            if (str_contains($ua, 'edge')) return 'Edge';
            return 'Other';
        }
    }
}