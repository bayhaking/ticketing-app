<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Fee breakdown
            if (!Schema::hasColumn('orders', 'service_fee')) {
                $table->integer('service_fee')->default(0)->after('total_price')
                    ->comment('Fee 5% yang masuk ke owner SPECTIX');
            }
            if (!Schema::hasColumn('orders', 'promotor_net')) {
                $table->integer('promotor_net')->default(0)->after('service_fee')
                    ->comment('Net yang diterima promotor (total_price - service_fee)');
            }

            // Xendit payment fields
            if (!Schema::hasColumn('orders', 'payment_invoice_id')) {
                $table->string('payment_invoice_id', 100)->nullable()->after('promotor_net')
                    ->comment('Xendit invoice ID');
                $table->index('payment_invoice_id');
            }
            if (!Schema::hasColumn('orders', 'payment_method')) {
                $table->string('payment_method', 50)->nullable()->after('payment_invoice_id');
            }
            if (!Schema::hasColumn('orders', 'payment_url')) {
                $table->string('payment_url', 500)->nullable()->after('payment_method');
            }
            if (!Schema::hasColumn('orders', 'payment_expired_at')) {
                $table->timestamp('payment_expired_at')->nullable()->after('payment_url');
            }
            if (!Schema::hasColumn('orders', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('payment_expired_at');
            }

            // QR code untuk e-ticket (unique per ticket)
            if (!Schema::hasColumn('orders', 'qr_token')) {
                $table->string('qr_token', 100)->nullable()->unique()->after('paid_at')
                    ->comment('Unique token untuk QR code, beda dengan order_number');
            }
        });

        // UPDATE status enum: tambah 'pending' & 'expired' selain 'success'
        // (di SQLite, status VARCHAR jadi auto-accept value baru)
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $cols = ['service_fee', 'promotor_net', 'payment_invoice_id', 'payment_method',
                     'payment_url', 'payment_expired_at', 'paid_at', 'qr_token'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('orders', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
