<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRetailAdjustmentLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('retail_adjustment_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shipment_id')->index();
            $table->integer('adjustment_type_id')->index();
            $table->integer('admin_id')->index();
            $table->string('remarks')->nullable();
            $table->decimal('adjustment_amount', 8, 2);
            $table->integer('pending_id')->index()->nullable();
            $table->integer('done_id')->index()->nullable();
            $table->integer('type');
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
        Schema::dropIfExists('retail_adjustment_logs');
    }
}
