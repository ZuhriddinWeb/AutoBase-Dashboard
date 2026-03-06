<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('widget_geozone_group', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('widget_id');
            $table->unsignedBigInteger('geozone_group_id');

            // Bog'lanishlar va kaskad o'chirish
            $table->foreign('widget_id')->references('id')->on('widgets')->onDelete('cascade');
            $table->foreign('geozone_group_id')->references('id')->on('geozone_groups')->onDelete('cascade');
            
            // Bir xil ma'lumot ikki marta yozilmasligi uchun
            $table->unique(['widget_id', 'geozone_group_id'], 'widget_geozone_unique');
        });
    }

    public function down()
    {
        Schema::dropIfExists('widget_geozone_group');
    }
};