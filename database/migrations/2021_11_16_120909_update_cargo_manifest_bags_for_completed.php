<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCargoManifestBagsForCompleted extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cargo_manifest_bags', function (Blueprint $table) {
            $table->smallInteger('completed')->default(1);
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
            $table->dropColumn('completed');
        });
    }
}
