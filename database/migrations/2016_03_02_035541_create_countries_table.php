<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCountriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->increments('id');
            $table->string('alpha2', 2);
            $table->string('alpha3', 3)->nullable(true);
            $table->string('name', 255);
            $table->float('latitude', 8);
            $table->float('longitude', 8);
            $table->timestamps();
            $table->integer('created_by');
            $table->foreign('created_by')
                ->references('id')->on('users');
            $table->integer('updated_by');
            $table->foreign('updated_by')
                ->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('countries');
    }
}
