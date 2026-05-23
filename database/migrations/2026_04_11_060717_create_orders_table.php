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
    Schema::create('orders', function (Blueprint $table) {
        $table->id();
        $table->foreignId('event_id')->constrained()->onDelete('cascade');
        $table->foreignId('ticket_type_id')->constrained('ticket_types')->onDelete('cascade');
        $table->string('customer_name');
        $table->string('customer_email');
        $table->string('customer_phone');
        $table->integer('quantity');
        $table->decimal('total_price', 15, 2);
        $table->string('status')->default('pending'); // pending, success, failed
        $table->string('order_number')->unique();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }        
};
