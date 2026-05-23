<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // ID Promotor
            $table->string('code')->unique(); // Contoh: MERDEKA50
            $table->enum('type', ['nominal', 'percent']); // Diskon potongan fix atau persentase
            $table->integer('amount'); // Nominal diskon (ex: 50000 atau 50)
            $table->integer('quota')->default(0); // Kuota maksimal dipakai (0 = unlimited)
            $table->integer('used')->default(0); // Sudah dipakai berapa kali
            
            // INI KUNCI UTAMANYA: Status awal selalu 'pending', menunggu ACC Superowner
            $table->enum('status', ['pending', 'active', 'rejected'])->default('pending'); 
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('vouchers');
    }
};