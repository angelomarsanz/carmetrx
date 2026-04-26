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
        Schema::create('users_melis', function (Blueprint $table) {
            $table->id();

            // Relación con 'users' (bigint(20) UNSIGNED en users.sql)
            $table->unsignedBigInteger('user_id')->unique()->nullable();
            
            // Columnas para almacenar JSONs grandes de Mercado Libre
            $table->longText('datos_meli')->nullable();
            $table->longText('envios_meli')->nullable();
            $table->longText('respuesta_meli')->nullable();

            $table->timestamps();

            // Llave foránea vinculada a la tabla 'users'
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_melis');
    }
};
