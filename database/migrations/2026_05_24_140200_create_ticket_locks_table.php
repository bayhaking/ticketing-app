<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ticket Locks Table — Inventory Locking
 *
 * Saat customer mulai checkout, tiket di-LOCK selama timer berjalan (10 menit).
 * Tiket yang locked tidak akan dijual ke customer lain.
 *
 * Setelah expired (10 menit), lock auto-release (di-cleanup scheduled command).
 * Saat payment success, lock dihapus + stock real ter-decrement.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_locks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ticket_type_id')
                ->constrained('ticket_types')
                ->cascadeOnDelete();

            $table->foreignId('event_id')
                ->constrained('events')
                ->cascadeOnDelete();

            $table->string('session_id', 100)->index();
            $table->integer('quantity')->default(1);

            // Identifier order jika sudah dibuat (sebelum payment)
            $table->string('order_reference', 100)->nullable()->index();

            $table->timestamp('locked_at');
            $table->timestamp('expires_at')->index();

            $table->timestamps();

            // Composite index untuk query cepat
            $table->index(['ticket_type_id', 'expires_at']);
            $table->index(['session_id', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_locks');
    }
};
