<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractPaymentDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contract_payment_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('idClient');
            $table->unsignedBigInteger('idContract');
            $table->unsignedBigInteger('idPaymentUnit');

            $table->string('contractPaymentDetails_period', 7);
            $table->unsignedInteger('ccontractPaymentDetails_quantity');
            $table->text('contractPaymentDetails_description')->nullable();
            $table->date('contractPaymentDetails_recepcionMunicipal')->nullable();
            $table->timestamps();

            $table->foreign('idClient')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('idContract')->references('id')->on('contracts')->onDelete('cascade');
            $table->foreign('idPaymentUnit')->references('id')->on('payment_units')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contract_payment_details');
    }
}
