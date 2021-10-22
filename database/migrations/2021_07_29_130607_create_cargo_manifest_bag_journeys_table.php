<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCargoManifestBagJourneysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cargo_manifest_bag_journeys', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cargo_manifest_bag_id')->index();
            $table->integer('seal_number')->index();
            $table->integer('bag_status_id')->index();
            $table->integer('admin_id')->index();
            $table->integer('cargo_manifest_id')->nullable()->index();
            $table->integer('cargo_manifest_status_id')->nullable()->index();
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
        Schema::dropIfExists('cargo_manifest_bag_journeys');
    }
}
