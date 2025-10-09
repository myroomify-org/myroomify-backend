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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('room_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->enum('status', ['pending','cancelled','confirmed','completed']);
            $table->date('check_in');
            $table->date('check_out');
            $table->integer('guest_count');
            $table->decimal('total_price', 10, 2);
            $table->enum('billing_type', ['private','company'])->default('private')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
