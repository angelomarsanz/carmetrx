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
        Schema::create('propiedades_melis', function (Blueprint $table) {
            $table->id();

            // ID del vehículo en la tabla user_properties
            // Lo marcamos como unique para asegurar que un auto solo tenga una vinculación activa
            $table->unsignedBigInteger('user_property_id')->unique()->nullable();
            
            // Campos longText para soportar los JSON de atributos y respuestas de la API
            $table->longText('datos_meli')->nullable();
            $table->longText('envios_meli')->nullable();
            $table->longText('respuesta_meli')->nullable();

            $table->timestamps();

            // Llave foránea hacia la tabla de vehículos
            $table->foreign('user_property_id')
                  ->references('id')
                  ->on('user_properties')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('propiedades_melis');
    }
};