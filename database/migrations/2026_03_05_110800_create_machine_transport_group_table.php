<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        // Pivot jadval: Guruh <-> Mashina
        Schema::create('machine_transport_group', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transport_group_id');
            // 'machines' bu sizning texnikalar jadvalingiz nomi (agar boshqacha bo'lsa o'zgartiring, masalan 'avl_units')
            $table->unsignedBigInteger('machine_id'); 

            $table->foreign('transport_group_id')->references('id')->on('transport_groups')->onDelete('cascade');
            // 'machines' jadvaliga bog'lanish
            $table->foreign('machine_id')->references('id')->on('machines')->onDelete('cascade');

            // Bir mashina bir guruhga ikki marta qo'shilmasligi uchun
            $table->unique(['transport_group_id', 'machine_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('machine_transport_group');
    }
};