<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateClientsTableCorreoTelefonoDireccionGiro extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            //Eliminar columna de nombres
            $table->dropColumn('clientName');
            //Agregar columnas de email, telefono, direccion y giro de la empresa
            $table->string('clientContactEmail', 100);
            $table->string('clientPhone', 100);
            $table->string('clientDirection', 100);
            $table->string('clientBusinessActivity', 100);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
