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
        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id('contact_id');
            
            // ممكن يجي من user مسجل أو guest عادي
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('guest_id')->nullable();

            $table->string('name');        // اسم الشخص المرسل
            $table->string('email');       // البريد الإلكتروني
            $table->string('phone')->nullable(); // الهاتف (اختياري)

            $table->string('subject');     // موضوع الرسالة
            $table->text('message');       // محتوى الرسالة

            $table->enum('status', ['pending', 'in_progress', 'resolved'])->default('pending'); 
            $table->timestamps();

            // ✅ هنا التعديلات
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('guest_id')->references('id')->on('guests')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_messages');
    }
};
