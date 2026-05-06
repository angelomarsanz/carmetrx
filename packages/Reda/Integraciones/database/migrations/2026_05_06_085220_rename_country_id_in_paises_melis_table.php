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
        Schema::table('paises_melis', function (Blueprint $table) {
            // 1. Eliminamos la llave foránea vinculada a country_id
            $table->dropForeign(['country_id']);

            // 2. Renombramos la columna para incluir el prefijo user_
            $table->renameColumn('country_id', 'user_country_id');
        });

        Schema::table('paises_melis', function (Blueprint $table) {
            // 3. Volvemos a crear la llave foránea con el nombre corregido
            $table->foreign('user_country_id')
                  ->references('id')
                  ->on('user_countries')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('paises_melis', function (Blueprint $table) {
            $table->dropForeign(['user_country_id']);
            $table->renameColumn('user_country_id', 'country_id');
        });

        Schema::table('paises_melis', function (Blueprint $table) {
            $table->foreign('country_id')
                  ->references('id')
                  ->on('user_countries')
                  ->onDelete('cascade');
        });
    }
};
