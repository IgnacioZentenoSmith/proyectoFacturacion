<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuantitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up()
    {
        Schema::create('quantities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('idContractCondition');

            $table->unsignedInteger('quantitiesCantidad');
            $table->string('quantitiesPeriodo', 7);
            $table->unsignedInteger('quantitiesMonto')->nullable();

            $table->timestamps();

            $table->foreign('idContractCondition')->references('id')->on('contract_conditions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quantities');
    }
}
