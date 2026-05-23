<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    // Ini adalah "Izin Resmi" supaya Laravel mau menyimpan data ke kolom-kolom ini
    protected $fillable = [
        'event_id',
        'ticket_type_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'quantity',
        'total_price',
        'order_number',
        'status',
    ];

    // Relasi ke Event
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

        public function guestlistCategory()
{
    return $this->belongsTo(GuestlistCategory::class);
}

public function scanner()
{
    return $this->belongsTo(User::class, 'scanned_by');
}

    // Relasi ke Tipe Tiket
    public function ticketType()
    {
        return $this->belongsTo(TicketType::class);
    }
}