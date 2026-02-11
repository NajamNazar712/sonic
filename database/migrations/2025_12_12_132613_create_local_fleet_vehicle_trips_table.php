<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('local_fleet_vehicle_trips', function (Blueprint $table) {
            $table->id();
            $table->integer('vehicle_id')->index(); //vehicles table
            $table->decimal('vehicle_mileage_per_liter', 12, 2); // save vehicle_mileage_per_liter because in future update vehicle detail not issue in reports
            $table->dateTime('out_time')->nullable();
            $table->dateTime('in_time')->nullable();
            $table->integer('out_meter')->nullable();
            $table->integer('in_meter')->nullable();
            $table->integer('rider_id')->index()->nullable(); //riders table
            $table->integer('route_id')->index()->nullable(); // routes table
            $table->text('out_remarks')->nullable();
            $table->text('in_remarks')->nullable();
            $table->integer('mileage')->nullable();        // in_meter - out_meter
            $table->decimal('fuel_liters', 10, 2)->nullable(); // mileage / mileage_per_liter
            $table->text('incident_report')->nullable();
            $table->string('incident_image')->nullable();
            $table->tinyInteger('status')->default(0)->comment('0-pending,1-completed');
            $table->integer('created_by')->index()->nullable();
            $table->integer('updated_by')->index()->nullable();
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
        Schema::dropIfExists('local_fleet_vehicle_trips');
    }
};
