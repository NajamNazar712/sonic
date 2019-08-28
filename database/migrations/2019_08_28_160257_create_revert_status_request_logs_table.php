<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRevertStatusRequestLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('revert_status_request_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('delivery_note_id');
            $table->integer('shipment_id');
            $table->integer('previous_status');
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
        Schema::dropIfExists('revert_status_request_logs');
    }
}
