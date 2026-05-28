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
        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('is_delivery')->default(false)->after('status');
            $table->text('delivery_address')->nullable()->after('is_delivery');
            $table->integer('shoe_quantity')->default(1)->after('shoe_size');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['is_delivery', 'delivery_address', 'shoe_quantity']);
        });
    }
};
