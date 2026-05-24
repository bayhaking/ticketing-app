<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_views', function (Blueprint $table) {
            $table->id();

            $table->foreignId('event_id')
                ->constrained('events')
                ->cascadeOnDelete();

            // Session ID dari Laravel — buat dedup view dalam session yang sama
            $table->string('session_id', 100)->index();

            // IP di-hash untuk privacy (PDP Indonesia compliant)
            $table->string('ip_hash', 64)->nullable()->index();

            // Kalau visitor login, link ke user
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Tracking funnel
            $table->timestamp('viewed_at');
            $table->timestamp('clicked_buy_at')->nullable();
            $table->foreignId('converted_to_order_id')
                ->nullable()
                ->constrained('orders')
                ->nullOnDelete();

            // Source tracking (UTM atau referrer)
            $table->string('referrer', 500)->nullable();
            $table->string('utm_source', 100)->nullable()->index();
            $table->string('utm_medium', 100)->nullable();
            $table->string('utm_campaign', 100)->nullable();

            // Device info (dari user agent)
            $table->string('device_type', 20)->nullable()->index(); // mobile/desktop/tablet
            $table->string('browser', 50)->nullable();

            $table->timestamps();

            // Composite index untuk query insight cepat
            $table->index(['event_id', 'viewed_at']);
            $table->index(['event_id', 'session_id']); // untuk firstOrCreate dedup
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_views');
    }
};
