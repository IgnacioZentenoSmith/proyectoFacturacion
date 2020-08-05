<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateContractPaymentDetailsTableAddUnits extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contract_payment_details', function (Blueprint $table) {
            $table->unsignedInteger('contractPaymentDetails_units')->nullable();
            $table->text('contractPaymentDetails_glosaProyecto')->nullable();
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
