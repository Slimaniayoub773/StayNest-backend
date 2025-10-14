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
        Schema::create('room_service_items', function (Blueprint $table) {
    $table->id();
    $table->foreignId('category_id')->constrained('room_service_categories')->onDelete('cascade');
    $table->string('name_ar');
    $table->string('name_en');
    $table->text('description')->nullable();
    $table->decimal('price', 8, 2);
    $table->integer('preparation_time');
    $table->boolean('is_available')->default(true);
    $table->string('image_url')->nullable();
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_service_items');
    }
};
