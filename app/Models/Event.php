<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    // Karena pakai guarded kosong, semua kolom otomatis diizinkan
    protected $guarded = []; 

    protected $casts = [
        'gallery' => 'array',
        'date' => 'datetime',
        'sales_start_date' => 'datetime', // Baca sebagai waktu buat fitur Countdown
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ticketTypes()
    {
        return $this->hasMany(TicketType::class);
    }

    public function lineups()
    {
        return $this->hasMany(Lineup::class);
    }

    public function guestlistCategories()
    {
        return $this->hasMany(GuestlistCategory::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}