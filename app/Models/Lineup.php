<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lineup extends Model
{
    use HasFactory;

    // Izinkan kolom ini diisi massal
    protected $fillable = ['event_id', 'name', 'ig', 'spotify'];

    // Relasi balik ke Event
    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}