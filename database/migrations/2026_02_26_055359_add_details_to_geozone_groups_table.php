<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('geozone_groups', function (Blueprint $table) {
            // Yangi ustunlarni qo'shamiz
            $table->bigInteger('resource_id')->nullable()->after('wialon_id');
            $table->string('description')->nullable()->after('name');
            $table->integer('type')->nullable()->after('description');
            
            $table->decimal('min_x', 11, 8)->nullable();
            $table->decimal('min_y', 11, 8)->nullable();
            $table->decimal('max_x', 11, 8)->nullable();
            $table->decimal('max_y', 11, 8)->nullable();
            $table->decimal('cen_x', 11, 8)->nullable();
            $table->decimal('cen_y', 11, 8)->nullable();

            // Eski wialon_id dagi "unique" ni olib tashlab, ikkalasiga (wialon_id + resource_id) birgalikda qo'yamiz
            $table->dropUnique(['wialon_id']); 
            $table->unique(['wialon_id', 'resource_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('geozone_groups', function (Blueprint $table) {
            // Orqaga qaytarish uchun (rollback qilinganda ishlaydi)
            $table->dropUnique(['wialon_id', 'resource_id']);
            $table->unique('wialon_id');

            $table->dropColumn([
                'resource_id', 'description', 'type', 
                'min_x', 'min_y', 'max_x', 'max_y', 'cen_x', 'cen_y'
            ]);
        });
    }
};