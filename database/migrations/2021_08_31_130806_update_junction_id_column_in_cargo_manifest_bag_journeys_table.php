<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateJunctionIdColumnInCargoManifestBagJourneysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cargo_manifest_bag_journeys', function (Blueprint $table) {
            $table->integer('junction_id')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cargo_manifest_bag_journeys', function (Blueprint $table) {
            $table->dropColumn('junction_id');
        });
    }
}
