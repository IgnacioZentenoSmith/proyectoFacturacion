<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTributaryDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tributarydetails', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('idTributarydocument');
            $table->unsignedBigInteger('idClient');
            $table->unsignedBigInteger('idPaymentUnit');

            $table->integer('tributarydetails_paymentUnitQuantity')->unsigned();
            $table->decimal('tributarydetails_paymentPercentage', 10, 2);
            $table->decimal('tributarydetails_paymentValue', 10, 2);
            $table->timestamps();

            $table->foreign('idTributarydocument')->references('id')->on('tributarydocuments')->onDelete('cascade');
            $table->foreign('idClient')->references('id')->on('clients')->onDelete('cascade');
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
        Schema::dropIfExists('tributary_details');
    }
}
