<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateClientsTableAddTipoempresa extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     * TIPOS -> Holding, Empresa
     */
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('clientTipoEmpresa', 10);
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
