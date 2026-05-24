<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketLock extends Model
{
    protected $fillable = [
        'ticket_type_id',
        'event_id',
        'session_id',
        'quantity',
        'order_reference',
        'locked_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'locked_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function ticketType(): BelongsTo
    {
        return $this->belongsTo(TicketType::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Helper: cek apakah lock masih valid (belum expired).
     */
    public function isActive(): bool
    {
        return $this->expires_at && $this->expires_at->isFuture();
    }

    /**
     * Scope: hanya lock yang masih aktif.
     */
    public function scopeActive($query)
    {
        return $query->where('expires_at', '>', now());
    }

    /**
     * Scope: lock yang sudah expired (untuk cleanup).
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', now());
    }
}
