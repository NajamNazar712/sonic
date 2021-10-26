<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCargoManifestTableAddingShortRecievedBagAndRecievedBagsColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cargo_manifests', function (Blueprint $table) {
            $table->integer('short_received_bags')->nullable();
            $table->integer('received_bags')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cargo_manifests', function (Blueprint $table) {
            $table->dropColumn('short_received_bags');
            $table->dropColumn('received_bags');
        });
    }
}
