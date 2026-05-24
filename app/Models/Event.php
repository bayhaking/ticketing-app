<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'date',
        'sales_start_date',
        'category',
        'type',
        'description',
        'venue',
        'venue_url',
        'start_time',
        'end_time',
        'creator_ig',
        'sponsors',
        'banner',
        'gallery',
        'promo',
        'total_stock',
        'is_voucher_active',
        'status',
        'views',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'sales_start_date' => 'datetime',
            'gallery' => 'array',
            'sponsors' => 'array',
            'is_voucher_active' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ticketTypes(): HasMany
    {
        return $this->hasMany(TicketType::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function lineups(): HasMany
    {
        return $this->hasMany(Lineup::class);
    }

    public function guestlistCategories(): HasMany
    {
        return $this->hasMany(GuestlistCategory::class);
    }

    public function eventViews(): HasMany
    {
        return $this->hasMany(EventView::class);
    }

    public function ticketLocks(): HasMany
    {
        return $this->hasMany(TicketLock::class);
    }

    /**
     * Accessor: format Instagram username (hapus @, hapus URL prefix).
     */
    public function getCreatorIgUsernameAttribute(): ?string
    {
        if (!$this->creator_ig) return null;
        $username = trim($this->creator_ig);
        $username = preg_replace('/^https?:\/\/(www\.)?instagram\.com\//i', '', $username);
        $username = ltrim($username, '@/');
        $username = rtrim($username, '/');
        return $username ?: null;
    }

    /**
     * Accessor: full IG URL.
     */
    public function getCreatorIgUrlAttribute(): ?string
    {
        $username = $this->creator_ig_username;
        return $username ? "https://instagram.com/{$username}" : null;
    }

    /**
     * Accessor: sponsors array yang sudah di-sanitize.
     * Format expected: [["name" => "Sponsor A", "ig" => "sponsorA"], ...]
     */
    public function getSponsorsListAttribute(): array
    {
        if (!is_array($this->sponsors)) return [];

        return array_map(function ($s) {
            $name = isset($s['name']) ? trim($s['name']) : '';
            $ig = isset($s['ig']) ? trim($s['ig']) : '';
            $ig = preg_replace('/^https?:\/\/(www\.)?instagram\.com\//i', '', $ig);
            $ig = ltrim($ig, '@/');
            $ig = rtrim($ig, '/');

            return [
                'name' => $name,
                'ig' => $ig,
                'ig_url' => $ig ? "https://instagram.com/{$ig}" : null,
            ];
        }, array_filter($this->sponsors, fn($s) => !empty($s['name'] ?? '')));
    }

    /**
     * Helper: format jadwal lengkap (tanggal + jam).
     */
    public function getScheduleAttribute(): string
    {
        $date = $this->date ? $this->date->format('d M Y') : 'TBA';
        if (!$this->start_time) return $date;

        $time = $this->start_time->format('H:i');
        if ($this->end_time) {
            $time .= ' - ' . $this->end_time->format('H:i');
        }
        return "{$date}, {$time} WIB";
    }

    /**
     * Helper: cek apakah masih jualan tiket.
     */
    public function isSellingTickets(): bool
    {
        if ($this->status !== 'upcoming') return false;
        if ($this->sales_start_date && now()->lessThan($this->sales_start_date)) return false;
        return true;
    }
}
