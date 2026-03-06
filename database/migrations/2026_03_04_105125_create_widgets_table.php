<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('widgets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Qaysi foydalanuvchiga tegishli ekanligi
            $table->string('i')->unique(); // Vue Grid Layout dagi noyob 'i' identifikatori
            $table->string('type'); // Vidjet turi (masalan: GreenGrid, SpeedGauge)
            $table->string('title')->nullable(); // Vidjet sarlavhasi
            
            // Joylashuv o'lchamlari
            $table->integer('x')->default(0);
            $table->integer('y')->default(0);
            $table->integer('w')->default(3);
            $table->integer('h')->default(3);
            
            // Boshqa qo'shimcha sozlamalar (color, apiUrl, va hokazolar uchun JSON)
            $table->json('data')->nullable(); 
            
            $table->timestamps();

            // Agar user o'chirilsa, uning vidjetlari ham o'chib ketadi
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('widgets');
    }
};