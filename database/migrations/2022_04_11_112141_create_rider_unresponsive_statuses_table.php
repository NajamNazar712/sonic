<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRiderUnresponsiveStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rider_unresponsive_statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('rider_id')->index();
            $table->integer('admin_id')->index();
            $table->tinyInteger('status');
            $table->integer('note_id')->index()->nullable();
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
        Schema::dropIfExists('rider_unresponsive_statuses');
    }
}
