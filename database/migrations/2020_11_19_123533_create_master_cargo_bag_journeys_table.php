<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMasterCargoBagJourneysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_cargo_bag_journey', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('bag_id');
            $table->bigInteger('seal_number');
            $table->integer('bag_status_id');
            $table->integer('admin_id');
            $table->integer('master_cargo_id')->nullable();
            $table->integer('master_cargo_status_id')->nullable();
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
        Schema::dropIfExists('master_cargo_bag_journey');
    }
}
