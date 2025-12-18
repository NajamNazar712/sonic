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
        Schema::create('local_fleet_vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('vehicle_number')->unique();
            $table->string('make')->nullable();
            $table->integer('city_id')->index();
            $table->string('vendor_name')->nullable();
            $table->tinyInteger('vendor_type')->default(1)->comment('1-Vendor,2-Self');
            $table->string('driver_name')->nullable(); // NO drivers table needed
            $table->integer('capacity')->nullable();
            $table->decimal('mileage_per_liter', 12, 2);
            $table->tinyInteger('vehicle_type')->comment('1-Permanent,2-Temporary');
            $table->tinyInteger('rent_type')->comment('1-Daily,2-Monthly');
            $table->decimal('rent_amount', 12, 2);
            $table->tinyInteger('fueling_responsibility')->comment('1-Trax,2-Vendor');
            $table->string('qr_path')->nullable();
            $table->tinyInteger('status')->default(1)->comment('1-Active,0-Inactive');
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
        Schema::dropIfExists('local_fleet_vehicles');
    }
};
