<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('region_id');
            $table->foreign('region_id')
                ->references('id')->on('regions');
            $table->string('name',255);
            $table->float('latitude', 8)->nullable(true);
            $table->float('longitude', 8)->nullable(true);
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
        Schema::drop('cities');
    }
}
