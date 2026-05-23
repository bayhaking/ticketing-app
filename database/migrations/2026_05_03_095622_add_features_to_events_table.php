<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            // 1. Fitur Coming Soon (Kapan tiket mulai bisa dibeli)
            $table->dateTime('sales_start_date')->nullable()->after('status');
            
            // 2. Fitur Amanin Data (Soft Deletes)
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('sales_start_date');
            $table->dropSoftDeletes();
        });
    }
};