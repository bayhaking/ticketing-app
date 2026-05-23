<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke tabel users (Promotor)
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); 
            
            $table->string('name');
            $table->date('date');
            $table->string('category');
            $table->string('type');
            $table->text('description');
            $table->string('banner')->nullable();
            $table->string('promo')->nullable();
            $table->integer('total_stock')->default(0);
            
            // 👇 FITUR-FITUR BARU SPECTIX 👇
            $table->integer('views')->default(0); // Untuk Insight di Hero Banner
            $table->string('status')->default('upcoming'); // Untuk Kontrol Buka/Tutup
            $table->dateTime('sales_start_date')->nullable(); // Untuk Countdown Coming Soon
            $table->softDeletes(); // Untuk Amanin Data (Soft Delete)
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};