<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateManifestJourneyTableChangeSealNumberDatatypeToBigint extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cargo_manifest_bag_journeys', function (Blueprint $table) {
            $table->bigInteger('seal_number')->change();
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
            $table->Integer('seal_number')->change();
        });
    }
}
