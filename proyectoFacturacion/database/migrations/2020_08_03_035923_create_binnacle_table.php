<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBinnacleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('binnacle', function (Blueprint $table) {
            $table->id();


            $table->unsignedBigInteger('idUser');

            $table->string('binnacle_action', 20);
            $table->string('binnacle_tableName', 50);
            $table->unsignedBigInteger('binnacle_tableId');
            $table->text('binnacle_tablePreValues')->nullable();
            $table->text('binnacle_tablePostValues')->nullable();

            $table->timestamps();

            $table->foreign('idUser')->references('id')->on('users')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('binnacle');
    }
}
