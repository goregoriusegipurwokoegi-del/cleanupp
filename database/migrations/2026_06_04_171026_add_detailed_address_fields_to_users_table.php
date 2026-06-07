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
        Schema::table('users', function (Blueprint $table) {
            $table->string('address_label')->nullable()->after('phone');
            $table->string('province')->nullable()->after('kecamatan');
            $table->string('city')->nullable()->after('province');
            $table->string('village')->nullable()->after('kecamatan');
            $table->string('address_landmark')->nullable()->after('address');
            $table->boolean('is_main_address')->default(true)->after('address_landmark');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'address_label',
                'province',
                'city',
                'village',
                'address_landmark',
                'is_main_address'
            ]);
        });
    }
};
