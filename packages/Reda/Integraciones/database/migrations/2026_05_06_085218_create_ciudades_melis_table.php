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
        Schema::create('ciudades_melis', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_city_id')->unique()->nullable();
            $table->longText('datos_meli')->nullable();
            $table->longText('envios_meli')->nullable();
            $table->longText('respuesta_meli')->nullable();
            $table->timestamps();

            $table->foreign('user_city_id')
                  ->references('id')
                  ->on('user_cities')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ciudades_melis');
    }
};
