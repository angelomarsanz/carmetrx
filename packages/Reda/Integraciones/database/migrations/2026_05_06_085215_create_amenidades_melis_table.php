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
        Schema::create('amenidades_melis', function (Blueprint $table) {
            $table->id();

            // ID de la amenidad en la tabla user_amenities
            // Lo marcamos como unique para asegurar relación 1:1
            $table->unsignedBigInteger('amenity_id')->unique()->nullable();

            // Campos longText para soportar los JSON de atributos y respuestas de la API
            $table->longText('datos_meli')->nullable();
            $table->longText('envios_meli')->nullable();
            $table->longText('respuesta_meli')->nullable();

            $table->timestamps();

            // Llave foránea hacia la tabla de amenidades original
            $table->foreign('amenity_id')
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
        Schema::dropIfExists('amenidades_melis');
    }
};
