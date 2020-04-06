<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->increments('idPermissions');
            $table->integer('idUser')->unsigned();
            $table->integer('idActions')->unsigned();
            $table->timestamps();

            $table->unique("idPermissions");
            $table->index("idUser");
            $table->index("idActions");

            $table->foreign('idUser')
                ->references('id')->on('users');

            $table->foreign('idActions')
                ->references('idActions')->on('actions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('permissions');
    }
}
