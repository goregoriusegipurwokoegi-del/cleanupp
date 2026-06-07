<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Catatan: Kolom payment_status (unpaid/paid) sudah ada sejak migrasi awal.
     * Kolom status_pembayaran ini digunakan khusus untuk tracking status
     * transaksi Midtrans (pending/success/failed).
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Tambahkan kolom status_pembayaran untuk tracking Midtrans
            // Berbeda dengan payment_status yang hanya 'unpaid'/'paid'
            if (!Schema::hasColumn('orders', 'status_pembayaran')) {
                $table->string('status_pembayaran')->default('pending')->after('snap_token')
                      ->comment('Status transaksi Midtrans: pending, success, failed');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'status_pembayaran')) {
                $table->dropColumn('status_pembayaran');
            }
        });
    }
};
