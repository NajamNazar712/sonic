<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateLeadsTableAddingTerritoryAndAreaIdColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->integer('territory_id')->index()->nullable();
            $table->integer('territory_area_id')->index()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('riders', function (Blueprint $table) {
            $table->dropColumn('territory_id');
            $table->dropColumn('territory_area_id');
        });
    }
}
