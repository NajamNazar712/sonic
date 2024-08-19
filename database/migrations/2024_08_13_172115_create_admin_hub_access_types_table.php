<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminHubAccessTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_hub_access_types', function (Blueprint $table) {
            $table->increments('id');
            $table->index('admin_id');
            $table->integer('hub_access_type'); 
            $table->unsignedInteger('admin_id');
            $table->unsignedInteger('updated_by');
            $table->longText('new_assigned_hubs');
            $table->longText('previous_assigned_hubs');
            $table->foreign('admin_id')->references('id')->on('admins');
            $table->foreign('updated_by')->references('id')->on('admins');
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin_hub_access_types');
    }
}
