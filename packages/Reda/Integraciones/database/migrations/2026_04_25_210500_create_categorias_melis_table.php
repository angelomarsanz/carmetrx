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
        Schema::create('categorias_melis', function (Blueprint $table) {
            $table->id(); 
            
            // El ID de Mercado Libre (ej: MLA1234). 
            // Lo mantenemos unique pero nullable por tu requerimiento.
            $table->string('id_categoria_meli')->unique()->nullable();
            
            // Nombre de la categoría
            $table->string('nombre_categoria_meli')->nullable();
            
            // Atributos: Usamos longText para asegurar que quepan las 30,000 líneas.
            // Laravel permite manejar longText como JSON si lo defines en el Modelo.
            $table->longText('atributos_generales')->nullable();
            $table->longText('atributos_especificos')->nullable();
            
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categorias_melis');
    }
};