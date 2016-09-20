<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',255);
            $table->string('short_name',255);
            $table->string('reg_number',255)->nullable();
            $table->date('reg_date')->nullable();
            $table->string('tax_number',255)->nullable();
            $table->text('reg_address')->nullable();
            $table->string('email',255);
            $table->string('phone',255);
            $table->string('fax',255)->nullable();
            $table->string('website',255)->nullable();
            $table->string('logo',255)->nullable();
            $table->string('billing_to',255)->nullable();
            $table->text('billing_address')->nullable();
            $table->string('billing_email',255)->nullable();
            $table->integer('country_id');
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');
            $table->integer('customer_source_id');
            $table->foreign('customer_source_id')->references('id')->on('customer_sources')->onDelete('cascade');
            $table->integer('business_domain_id');
            $table->foreign('business_domain_id')->references('id')->on('business_domains')->onDelete('cascade');
            $table->boolean('verified')->default(false);
            
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
        Schema::drop('customers');
    }
}
    