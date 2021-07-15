<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('idTributaryDocument');
            $table->unsignedBigInteger('idClient');
            $table->unsignedBigInteger('idModule');
            $table->unsignedBigInteger('idPaymentUnit');
            $table->unsignedBigInteger('idContractPaymentDetails');

            $table->float('invoices_monto');
            $table->decimal('invoices_porcentaje', 10, 2);
            $table->decimal('invoices_descuento', 10, 2)->nullable();
            $table->float('invoices_neto');
            $table->float('invoices_total');
            $table->integer('invoices_grupo')->unsigned();

            $table->string('invoices_numeroOC', 100)->nullable();
            $table->date('invoices_fechaOC')->nullable();
            $table->date('invoices_vigenciaOC')->nullable();

            $table->string('invoices_numeroHES', 100)->nullable();
            $table->date('invoices_fechaHES')->nullable();
            $table->date('invoices_vigenciaHES')->nullable();

            $table->timestamps();

            $table->foreign('idTributaryDocument')->references('id')->on('tributarydocuments')->onDelete('cascade');
            $table->foreign('idClient')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('idModule')->references('id')->on('modules')->onDelete('cascade');
            $table->foreign('idPaymentUnit')->references('id')->on('payment_units')->onDelete('cascade');
            $table->foreign('idContractPaymentDetails')->references('id')->on('contract_payment_details')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoices');
    }
}
