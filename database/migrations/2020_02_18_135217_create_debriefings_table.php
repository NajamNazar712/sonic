<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDebriefingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('debriefings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('hub');
            $table->integer('zone_id');
            $table->integer('delivered');
            $table->integer('delivery_unsuccessful');
            $table->integer('on_hold');
            $table->integer('status_not_attempted');
            $table->integer('fake_status');
            $table->integer('confirmation_pending');
            $table->integer('delivery_note_pending');
            $table->integer('delivery_tomorrow');
            $table->integer('total_1');
            $table->float('total_1_ratio');
            $table->integer('total_2');
            $table->float('total_2_ratio');
            $table->integer('grand_total');
            $table->float('grand_total_ratio');
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
        Schema::dropIfExists('debriefings');
    }
}
