<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImportadoresTable extends Migration
{
    public function up()
    {
        Schema::create('importadores', function (Blueprint $table) {
            $table->id();
            
            // Relación con el usuario (Inmobiliaria)
            $table->unsignedBigInteger('user_id');
            
            // Datos básicos del importador
            $table->string('nombre_concesionario')->nullable()->comment('Nombre descriptivo');
            $table->string('identificador_meli')->nullable()->comment('ID de cuenta en Mercado Libre');
            $table->string('paso_ejecutado')->nullable()->default('inicio');
            
            // Relación formal
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::table('importadores', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
        Schema::dropIfExists('importadores');
    }
}