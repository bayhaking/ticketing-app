<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Migration ini menambahkan kolom parent_promotor_id ke tabel users.
 * Ini WAJIB untuk memisahkan staff per-promotor — supaya:
 * - Promotor A tidak bisa lihat staff Promotor B
 * - Staff terkait ke promotor tertentu
 * - Saat hapus promotor, staff bisa auto-deleted (kalau pakai onDelete cascade)
 *
 * Juga drop kolom plain_password yang berbahaya (security risk).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Tambah kolom parent_promotor_id (nullable karena promotor sendiri tidak punya parent)
            if (!Schema::hasColumn('users', 'parent_promotor_id')) {
                $table->foreignId('parent_promotor_id')
                    ->nullable()
                    ->after('role')
                    ->constrained('users')
                    ->nullOnDelete(); // kalau promotor dihapus, parent_promotor_id staff jadi null
                $table->index('parent_promotor_id');
            }
        });

        // Drop kolom plain_password (security risk) — kalau ada
        if (Schema::hasColumn('users', 'plain_password')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('plain_password');
            });
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'parent_promotor_id')) {
                $table->dropForeign(['parent_promotor_id']);
                $table->dropColumn('parent_promotor_id');
            }
        });

        // Re-add plain_password kalau rollback (TIDAK RECOMMENDED tapi untuk reversibility)
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'plain_password')) {
                $table->string('plain_password')->nullable()->after('password');
            }
        });
    }
};