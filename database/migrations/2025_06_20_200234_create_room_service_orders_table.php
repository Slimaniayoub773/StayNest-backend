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
        Schema::create('room_service_orders', function (Blueprint $table) {
    $table->id();
    $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');
    $table->foreignId('room_id')->constrained('rooms')->onDelete('cascade');
    $table->foreignId('guest_id')->constrained('guests')->onDelete('cascade');
    $table->timestamp('order_date')->useCurrent();
    $table->string('status')->default('pending');
    $table->text('special_instructions')->nullable();
    $table->decimal('total_price', 8, 2);
    $table->decimal('delivery_charge', 8, 2)->default(0);
    $table->time('expected_delivery_time')->nullable();
    $table->time('actual_delivery_time')->nullable();
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_service_orders');
    }
};
