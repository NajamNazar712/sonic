<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCargoManifestDraftBagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cargo_manifest_draft_bags', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('bag_id')->index();
            $table->bigInteger('seal_number');
            $table->integer('shipments_count');
            $table->integer('origin_id')->index();
            $table->integer('destination_id')->index();
            $table->integer('added_by')->index();
            $table->integer('weight');
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
        Schema::dropIfExists('cargo_manifest_draft_bags');
    }
}
