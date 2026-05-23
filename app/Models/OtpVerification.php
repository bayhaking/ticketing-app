<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtpVerification extends Model
{
    use HasFactory;

    // Beri izin Laravel untuk mengisi kolom-kolom ini
    protected $fillable = [
        'whatsapp_number', 
        'otp_code', 
        'expires_at'
    ];
}