<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Update kolom status di tabel events.
 *
 * Sebelumnya: 'upcoming', 'finished'
 * Sekarang: 'upcoming', 'live', 'finished'
 *
 * Plus tambah kolom:
 * - sales_closed_manually (boolean) — kalau true, jangan auto-reopen status
 * - last_status_changed_at (timestamp) — buat audit & undo
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            // Kalau kolom belum ada
            if (!Schema::hasColumn('events', 'sales_closed_manually')) {
                $table->boolean('sales_closed_manually')
                    ->default(false)
                    ->after('status')
                    ->comment('Kalau true, scheduler tidak akan auto-reopen status');
            }

            if (!Schema::hasColumn('events', 'last_status_changed_at')) {
                $table->timestamp('last_status_changed_at')
                    ->nullable()
                    ->after('sales_closed_manually');
            }
        });

        // Note: status kolom-nya kemungkinan VARCHAR atau ENUM
        // Untuk SQLite, ENUM jadi TEXT, jadi value baru 'live' otomatis bisa
        // Untuk MySQL, kalau pakai ENUM mungkin perlu ALTER. Cek dulu.
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (Schema::hasColumn('events', 'sales_closed_manually')) {
                $table->dropColumn('sales_closed_manually');
            }
            if (Schema::hasColumn('events', 'last_status_changed_at')) {
                $table->dropColumn('last_status_changed_at');
            }
        });
    }
};
