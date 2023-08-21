<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRiderAssignedHubForDeliveryNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rider_assigned_hub_for_delivery_notes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('rider_id')->index();
            $table->string('hubs');
            $table->integer('is_enable')->default(1);
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
        Schema::dropIfExists('rider_assigned_hub_for_delivery_notes');
    }
}
