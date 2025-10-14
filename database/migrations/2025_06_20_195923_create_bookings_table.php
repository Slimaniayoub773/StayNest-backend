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
    $table->foreignId('user_id')->constrained('users');
    $table->foreignId('guest_id')->constrained('guests');
    $table->foreignId('room_id')->constrained('rooms');
    $table->foreignId('offer_id')->nullable()->constrained('offers');
    $table->date('check_in_date');
    $table->date('check_out_date');
    $table->string('booking_status')->default('pending');
    $table->decimal('total_price', 10, 2);
    $table->string('payment_status')->default('unpaid');
    $table->integer('number_of_guests');
    $table->text('cancellation_policy')->nullable();
    $table->text('special_requests')->nullable();
    $table->timestamps();
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
