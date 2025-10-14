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
        Schema::create('legal_mentions', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // عنوان المحتوى القانوني
            $table->text('content'); // نص المعلومات القانونية
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('legal_mentions');
    }
};
