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
        Schema::create('agents_melis', function (Blueprint $table) {
            $table->id();

            // Relación uno a uno: Un agente solo tiene una configuración de Meli
            // Debe ser unsignedBigInteger para ser compatible con user_agents.id
            $table->unsignedBigInteger('user_agent_id')->unique()->nullable();
            
            // Campos de gran capacidad para las respuestas de la API
            $table->longText('datos_meli')->nullable();
            $table->longText('envios_meli')->nullable();
            $table->longText('respuesta_meli')->nullable();

            $table->timestamps();

            // Definición de la llave foránea apuntando a user_agents
            $table->foreign('user_agent_id')
                  ->references('id')
                  ->on('user_agents')
                  ->onDelete('cascade');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agents_melis');
    }
};
