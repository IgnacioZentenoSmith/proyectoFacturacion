<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateContractConditionsTableADDFechaInicioFechaTermino extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contract_conditions', function (Blueprint $table) {
            //Agregar fecha de inicio y fecha de termino de contratos contractuales
            $table->date('contractsConditions_fechaInicio');
            $table->date('contractsConditions_fechaTermino')->nullable();
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
