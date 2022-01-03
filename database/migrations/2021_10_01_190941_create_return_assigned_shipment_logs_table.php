<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReturnAssignedShipmentLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('return_assigned_shipment_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('return_assign_shipment_id')->index();
            $table->tinyInteger('status');
            $table->integer('assigned_by')->index();
            $table->timestamps();
        });
    }
    //0 asssigned,1 re-attempt,2 return, 3 intercept, 4 unassign, 5 reattemp request, 6 return confirm pending, 7 on hold for selfcollection
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('return_assigned_shipment_logs');
    }
}
