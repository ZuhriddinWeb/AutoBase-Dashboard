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
       Schema::create('wialon_settings', function (Blueprint $table) {
        $table->id();
        $table->string('base_url')->default('https://hst-api.wialon.com'); // Asosiy API URL
        $table->text('token')->nullable(); // UZUN TOKEN
        $table->string('username')->nullable(); // Login (agar token ishlatilmasa)
        $table->string('password')->nullable(); // Parol (agar token ishlatilmasa)
        $table->string('resource_id')->nullable(); // Qaysi resurs (Resource ID) dan ma'lumot olinishi
        $table->string('status')->default('active');
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wialon_settings');
    }
};
