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
       Schema::create('rooms', function (Blueprint $table) {
    $table->id();
    $table->string('room_number')->unique();
    $table->foreignId('type_id')->constrained('room_types');
    $table->integer('floor_number');
    $table->decimal('price_per_night', 8, 2);
    $table->text('description')->nullable();
    $table->string('status')->default('available'); // available / booked / maintenance
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
