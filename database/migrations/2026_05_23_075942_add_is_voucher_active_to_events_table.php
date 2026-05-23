<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {
            // Tambahkan kolom boolean untuk voucher (default: 0 / false)
            $table->boolean('is_voucher_active')->default(false)->after('total_stock');
        });
    }

    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('is_voucher_active');
        });
    }
};