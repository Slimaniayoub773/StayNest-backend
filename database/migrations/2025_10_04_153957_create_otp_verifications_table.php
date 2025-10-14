<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('otp_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guest_id')->constrained()->onDelete('cascade');
            $table->string('otp', 6);
            $table->timestamp('expires_at');
            $table->timestamps();
            
            $table->index(['guest_id', 'otp']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('otp_verifications');
    }
};