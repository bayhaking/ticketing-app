<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
    Schema::table('users', function (Blueprint $table) {
        $table->string('ktp_number')->nullable();
        $table->string('npwp_number')->nullable();
        $table->string('bank_name')->nullable();
        $table->string('bank_account_number')->nullable();
        $table->string('bank_account_name')->nullable();
        $table->boolean('is_verified')->default(false);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
