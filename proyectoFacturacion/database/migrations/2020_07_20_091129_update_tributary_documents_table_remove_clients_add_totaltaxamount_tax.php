<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTributaryDocumentsTableRemoveClientsAddTotaltaxamountTax extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tributarydocuments', function (Blueprint $table) {
            $table->dropColumn('idClient');
            $table->integer('tributarydocuments_tax')->unsigned();
            $table->float('tributarydocuments_totalAmountTax');
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
