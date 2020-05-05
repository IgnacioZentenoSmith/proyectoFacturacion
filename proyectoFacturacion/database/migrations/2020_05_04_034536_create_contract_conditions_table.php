<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractConditionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contract_conditions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('idModule');
            $table->unsignedBigInteger('idPaymentUnit');
            $table->unsignedBigInteger('idClient');
            $table->unsignedBigInteger('idContract');
            $table->string('contractsConditions_Moneda', 20);
            $table->unsignedInteger('contractsConditions_Precio');
            $table->string('contractsConditions_Modalidad', 30);
            $table->unsignedInteger('contractsConditions_Cantidad');

            $table->timestamps();

            $table->foreign('idModule')->references('id')->on('modules')->onDelete('cascade');
            $table->foreign('idPaymentUnit')->references('id')->on('payment_units')->onDelete('cascade');
            $table->foreign('idClient')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('idContract')->references('id')->on('contracts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contract_conditions');
    }
}
