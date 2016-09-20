<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->bigIncrements('id');
            $table->dateTime('valid_from');
            $table->dateTime('valid_to')->nullable();
            $table->text('routes')->nullable();
            $table->text('viewable_fields')->nullable();
            $table->text('viewable_roles')->nullable();
            $table->text('viewable_by_accounts')->nullable();
            $table->text('viewable_except_accounts')->nullable();
            $table->smallInteger('viewable_type');
            $table->text('viewable_conditions')->nullable();
            $table->integer('viewable_max_record')->nullable();
            $table->text('editable_fields')->nullable();
            $table->text('editable_roles')->nullable();
            $table->text('editable_by_accounts')->nullable();
            $table->text('editable_except_accounts')->nullable();
            $table->smallInteger('editable_type');
            $table->text('editable_conditions')->nullable();
            $table->text('deletable_fields')->nullable();
            $table->text('deletable_roles')->nullable();
            $table->text('deletable_by_accounts')->nullable();
            $table->text('deletable_except_accounts')->nullable();
            $table->smallInteger('deletable_type');
            $table->text('deletable_conditions')->nullable();
            $table->text('exportable_fields')->nullable();
            $table->text('exportable_roles')->nullable();
            $table->text('exportable_by_accounts')->nullable();
            $table->text('exportable_except_accounts')->nullable();
            $table->smallInteger('exportable_type');
            $table->text('exportable_conditions')->nullable();
            $table->integer('role_id');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->string('widget',255);
            $table->index('widget');
            $table->string('related_to',255)->nullable();
            $table->integer('shared_by')->nullable();
            $table->foreign('shared_by')->references('id')->on('users')->onDelete('cascade');
            $table->dateTime('shared_at')->nullable();

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
        Schema::drop('permissions');
    }
}
    