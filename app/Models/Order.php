<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    protected $fillable = [
        'event_id',
        'ticket_type_id',
        'guestlist_category_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'quantity',
        'total_price',
        'service_fee',
        'promotor_net',
        'order_number',
        'qr_token',
        'status',
        'payment_invoice_id',
        'payment_method',
        'payment_url',
        'payment_expired_at',
        'paid_at',
        'scanned_at',
        'scanned_by',
    ];

    protected function casts(): array
    {
        return [
            'payment_expired_at' => 'datetime',
            'paid_at' => 'datetime',
            'scanned_at' => 'datetime',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function ticketType(): BelongsTo
    {
        return $this->belongsTo(TicketType::class);
    }

    public function guestlistCategory(): BelongsTo
    {
        return $this->belongsTo(GuestlistCategory::class);
    }

    public function scanner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'scanned_by');
    }
}
