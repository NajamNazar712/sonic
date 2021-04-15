<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReportingLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reporting_locations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('city_id')->index();
            $table->string('name');
            $table->string('address');
            $table->string('lat');
            $table->string('long');
            $table->integer('radius');
            $table->integer('status')->index()->default(1);
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
        Schema::dropIfExists('reporting_locations');
    }
}
