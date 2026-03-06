<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('widget_transport_group', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('widget_id');
            $table->unsignedBigInteger('transport_group_id');

            // Bog'lanishlar va kaskad o'chirish
            $table->foreign('widget_id')->references('id')->on('widgets')->onDelete('cascade');
            $table->foreign('transport_group_id')->references('id')->on('transport_groups')->onDelete('cascade');
            
            // Bir xil ma'lumot ikki marta yozilmasligi uchun
            $table->unique(['widget_id', 'transport_group_id'], 'widget_transport_unique');
        });
    }

    public function down()
    {
        Schema::dropIfExists('widget_transport_group');
    }
};