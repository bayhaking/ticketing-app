<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketType extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'name',
        'price',
        'stock',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    // RELASI INI YANG WAJIB ADA BUAT DASHBOARD
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}