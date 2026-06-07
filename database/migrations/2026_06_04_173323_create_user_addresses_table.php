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
        Schema::create('user_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('recipient_name');
            $table->string('phone');
            $table->string('address_label')->nullable();
            $table->string('province')->nullable();
            $table->string('city')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('village')->nullable();
            $table->string('postal_code')->nullable();
            $table->text('full_address')->nullable();
            $table->string('address_landmark')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->boolean('is_main_address')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_addresses');
    }
};
