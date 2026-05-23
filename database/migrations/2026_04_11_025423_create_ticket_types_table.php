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
    Schema::create('ticket_types', function (Blueprint $table) {
        $table->id();
        $table->foreignId('event_id')->constrained()->cascadeOnDelete(); // Relasi ke Event Utama
        $table->string('name');
        $table->integer('price');
        $table->integer('stock');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_types');
    }
};
