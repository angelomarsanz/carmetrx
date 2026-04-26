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
        Schema::create('admins_melis', function (Blueprint $table) {
            $table->id();

            // Relación uno a uno con la tabla 'admins'
            // Usamos unsignedBigInteger porque el id de 'admins' es bigint(20) UNSIGNED
            $table->unsignedBigInteger('admin_id')->unique()->nullable();
            
            // Columnas para JSON grandes
            $table->longText('datos_meli')->nullable();
            $table->longText('envios_meli')->nullable();
            $table->longText('respuesta_meli')->nullable();

            $table->timestamps();

            // Definición de la llave foránea
            $table->foreign('admin_id')
                ->references('id')
                ->on('admins')
                ->onDelete('cascade'); // Si se borra el admin, se borran sus datos de Meli
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins_melis');
    }
};
