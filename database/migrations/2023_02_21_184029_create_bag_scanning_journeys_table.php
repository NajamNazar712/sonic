<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBagScanningJourneysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bag_scanning_journeys', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('bag_id')->index();
            $table->integer('screen_location_id')->index();
            $table->integer('admin_id')->nullable()->index();
            $table->string('ip_address')->nullable();
            $table->decimal('latitude', 11, 8)->nullable()->default(NULL);
            $table->decimal('longitude', 11, 8)->nullable()->default(NULL);
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
        Schema::dropIfExists('bag_scanning_journeys');
    }
}
