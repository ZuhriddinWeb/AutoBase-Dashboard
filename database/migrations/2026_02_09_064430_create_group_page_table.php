<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('group_page', function (Blueprint $table) {
            $table->id();
            // Sahifa IDsi
            $table->foreignId('page_id')->constrained()->cascadeOnDelete();
            // Guruh IDsi
            $table->foreignId('group_id')->constrained()->cascadeOnDelete();

            // Bir xil juftlik qayta yozilmasligi uchun (unique)
            $table->unique(['page_id', 'group_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_page');
    }
};
