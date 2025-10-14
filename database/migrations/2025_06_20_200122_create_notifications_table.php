<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();             // معرف الإشعار
            $table->string('type');                    // نوع الإشعار
            $table->morphs('notifiable');             // العلاقة polymorphic مع المستخدم
            $table->text('data');                      // بيانات الإشعار (json)
            $table->timestamp('read_at')->nullable();  // وقت القراءة
            $table->timestamps();                      // created_at & updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
