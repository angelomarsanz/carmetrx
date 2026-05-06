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
        Schema::table('amenidades_melis', function (Blueprint $table) {
            // 1. Eliminamos la llave foránea actual
            $table->dropForeign(['amenity_id']);

            // 2. Renombramos la columna
            $table->renameColumn('amenity_id', 'user_amenity_id');
        });

        Schema::table('amenidades_melis', function (Blueprint $table) {
            // 3. Volvemos a crear la llave foránea con el nuevo nombre
            $table->foreign('user_amenity_id')
                  ->references('id')
                  ->on('user_amenities')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('amenidades_melis', function (Blueprint $table) {
            $table->dropForeign(['user_amenity_id']);
            $table->renameColumn('user_amenity_id', 'amenity_id');
        });

        Schema::table('amenidades_melis', function (Blueprint $table) {
            $table->foreign('amenity_id')
                  ->references('id')
                  ->on('user_amenities')
                  ->onDelete('cascade');
        });
    }
};
