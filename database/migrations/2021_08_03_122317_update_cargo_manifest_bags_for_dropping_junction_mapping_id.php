<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCargoManifestBagsForDroppingJunctionMappingId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cargo_manifest_bags', function (Blueprint $table) {
            $table->dropColumn('junction_mapping_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cargo_manifest_bags', function (Blueprint $table) {
            $table->integer('junction_mapping_id')->nullable();
        });
    }
}
