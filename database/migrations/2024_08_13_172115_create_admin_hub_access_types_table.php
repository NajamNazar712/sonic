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
            $table->integer('admin_id');
            $table->integer('hub_access_type');
            $table->longText('previous_assigned_hubs')->nullable();
            $table->longText('new_assigned_hubs')->nullable();
            $table->integer('updated_by');
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
