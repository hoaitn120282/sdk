<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',255);
            $table->string('email',100);
            $table->unique('email');
            $table->string('password',100);
            $table->string('avatar',255)->nullable();
            $table->text('note')->nullable();
            $table->string('reset_token',255)->nullable();
            $table->dateTime('reset_date')->nullable();
            $table->string('secret_key',255)->nullable();
            $table->text('permissions')->nullable();
            $table->text('permissions_custom')->nullable();
            $table->text('routes')->nullable();
            $table->text('routes_shared')->nullable();
            $table->text('routes_related')->nullable();
            $table->text('routes_custom')->nullable();
            $table->text('menu')->nullable();
            $table->boolean('active')->default(false);

            $table->timestamps();
            $table->softDeletes();
            $table->integer('created_by');
            $table->foreign('created_by')->references('id')->on('users');
            $table->integer('updated_by');
            $table->foreign('updated_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('users');
    }
}
    