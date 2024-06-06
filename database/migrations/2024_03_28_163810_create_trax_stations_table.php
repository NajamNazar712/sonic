<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTraxStationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trax_stations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 255);
            $table->string('station_code', 255)->nullable();
            $table->tinyInteger('hub')->default(0);
            $table->integer('hub_id')->nullable();
            $table->integer('zone_id');
            $table->tinyInteger('pickup')->default(1);
            $table->tinyInteger('status')->default(1);
            $table->decimal('location_latitude', 10, 6)->nullable();
            $table->decimal('location_longitude', 10, 6)->nullable();
            $table->integer('gc_area');
            $table->integer('attempt_tat')->default(1);
            $table->string('cut_off_time', 191)->default('11:00 AM');
            $table->timestamp('cut_off_time_updated_at')->nullable();
            $table->integer('cut_off_time_updated_by')->nullable();
            $table->string('address', 191)->nullable();
            $table->tinyInteger('business_category_id')->default(1);
            $table->string('city_code', 191)->nullable();
            $table->decimal('hub_location_latitude', 10, 6)->nullable();
            $table->decimal('hub_location_longitude', 10, 6)->nullable();
            $table->integer('pickup_cut_off_time')->nullable();
            $table->integer('tax_id')->nullable();
            $table->smallInteger('permanent_disabled')->default(0);
            $table->integer('booking_enable_status')->default(1);
            $table->string('iata_code', 191)->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index('created_at', 'cities_created_at_index');
            $table->index('updated_at', 'cities_updated_at_index');
            $table->index('hub', 'cities_hub_index');
            $table->index('hub_id', 'cities_hub_id_index');
            $table->index('pickup', 'cities_pickup_index');
            $table->index('status', 'cities_status_index');
            $table->index('zone_id', 'cities_zone_id_index');
            $table->index('business_category_id', 'cities_business_category_id_index');
            $table->index('name', 'cities_name_index');
            $table->index('permanent_disabled', 'cities_permanent_disabled_index');
            $table->index('iata_code', 'cities_iata_code_index');
            $table->index('booking_enable_status', 'cities_booking_disable_status_index');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trax_stations');
    }
}
