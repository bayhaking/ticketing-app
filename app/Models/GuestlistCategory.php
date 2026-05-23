<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuestlistCategory extends Model
{
    use HasFactory;

    // Tambahkan ini biar bisa simpan data!
    protected $guarded = [];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}