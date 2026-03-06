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
        Schema::create('geozones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('wialon_id'); // Wialon ichidagi geozona ID-si
            $table->unsignedBigInteger('resource_id'); // Geozona qaysi resursga tegishli ekanligi (1C_UAT)
            $table->string('name'); // Geozona nomi (Masalan: Amantaytau_koni)
            $table->string('description')->nullable(); // Ta'rifi
            $table->integer('type')->nullable(); // Turi (1-chiziq, 2-poligon, 3-aylana)
            
            // Geozonaning koordinata chegaralari (quti / kvadrat)
            $table->decimal('min_x', 11, 8)->nullable();
            $table->decimal('min_y', 11, 8)->nullable();
            $table->decimal('max_x', 11, 8)->nullable();
            $table->decimal('max_y', 11, 8)->nullable();
            
            // Geozonaning markazi (markaziy nuqtasi)
            $table->decimal('cen_x', 11, 8)->nullable();
            $table->decimal('cen_y', 11, 8)->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('geozones');
    }
};
