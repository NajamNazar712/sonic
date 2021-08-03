<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCargoManifestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cargo_manifests', function (Blueprint $table) {
            $table->string('driver_phone');
            $table->string('vendor_name');
            $table->integer('vehicle_id')->index();
            $table->dropColumn('quantity');
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
            $table->dropColumn('driver_phone');
            $table->dropColumn('vendor_name');
            $table->dropColumn('vehicle_id');
            $table->integer('quantity');
        });
    }
}
