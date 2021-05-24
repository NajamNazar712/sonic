<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateMasterCargoAndBagsForHunctionNullableTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('master_cargoes', function (Blueprint $table) {
            $table->integer('junction_hub_1_id')->nullable()->change();
        });
        Schema::table('bags', function (Blueprint $table) {
            $table->integer('junction_hub_1_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('master_cargoes', function (Blueprint $table) {
            $table->integer('junction_hub_1_id')->change();
        });
        Schema::table('bags', function (Blueprint $table) {
            $table->integer('junction_hub_1_id')->change();
        });
    }
}
