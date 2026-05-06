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
        Schema::table('estados_melis', function (Blueprint $table) {
            // 1. Eliminamos la llave foránea vinculada a state_id
            $table->dropForeign(['state_id']);

            // 2. Renombramos la columna a user_state_id
            $table->renameColumn('state_id', 'user_state_id');
        });

        Schema::table('estados_melis', function (Blueprint $table) {
            // 3. Volvemos a crear la llave foránea con el nombre corregido
            $table->foreign('user_state_id')
                  ->references('id')
                  ->on('user_states')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('estados_melis', function (Blueprint $table) {
            $table->dropForeign(['user_state_id']);
            $table->renameColumn('user_state_id', 'state_id');
        });

        Schema::table('estados_melis', function (Blueprint $table) {
            $table->foreign('state_id')
                  ->references('id')
                  ->on('user_states')
                  ->onDelete('cascade');
        });
    }
};
