<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
    {
        Schema::create('user_dashboards', function (Blueprint $table) {
            $table->id();
            // Qaysi foydalanuvchiga tegishliligi
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            // Dashboard sozlamalari (JSON formatda)
            $table->json('layout')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_dashboards');
    }
};
