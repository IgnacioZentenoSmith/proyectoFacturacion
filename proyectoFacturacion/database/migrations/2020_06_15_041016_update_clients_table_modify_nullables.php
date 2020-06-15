<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateClientsTableModifyNullables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            //nullables
            $table->string('clientRUT', 100)->nullable()->change();
            $table->string('clientContactEmail', 100)->nullable()->change();
            $table->string('clientPhone', 100)->nullable()->change();
            $table->string('clientDirection', 100)->nullable()->change();
            $table->string('clientBusinessActivity', 100)->nullable()->change();
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
