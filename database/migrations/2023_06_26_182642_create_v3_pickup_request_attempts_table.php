<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateV3PickupRequestAttemptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('v3_pickup_request_attempts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pickup_request_id')->index();
            $table->integer('rider_id')->index();
            $table->integer('shipments_picked')->nullable()->index();
            $table->integer('shipments_received')->nullable()->index();
            $table->integer('reason_id')->nullable()->index();
            $table->string('trax_remarks')->nullable();
            $table->timestamp('attempt_date')->index();
            $table->integer('assigned_by')->index();
            $table->timestamps();
            $table->index(['created_at', 'updated_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('v3_pickup_request_attempts');
    }
}
