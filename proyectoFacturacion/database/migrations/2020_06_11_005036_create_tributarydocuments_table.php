<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTributarydocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tributarydocuments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('idClient');
            $table->unsignedBigInteger('idContract');
            $table->string('tributarydocuments_documentType', 50);
            $table->string('tributarydocuments_period', 7);
            $table->float('tributarydocuments_totalAmount');

            $table->timestamps();

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
        Schema::dropIfExists('tributarydocuments');
    }
}
