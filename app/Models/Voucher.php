<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'code', 
        'type', 
        'amount', 
        'quota', 
        'used', 
        'status'
    ];

    // Relasi: 1 Voucher dimiliki oleh 1 Promotor
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}