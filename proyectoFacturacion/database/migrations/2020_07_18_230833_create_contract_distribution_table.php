<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractDistributionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contract_distribution', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('idClient');
            $table->unsignedBigInteger('idContract');
            $table->string('contractDistribution_type', 50);
            $table->decimal('contractDistribution_percentage', 10, 2);

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
        Schema::dropIfExists('contract_distribution');
    }
}
