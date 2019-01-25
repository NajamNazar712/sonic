<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateChargesModeTableColumnType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('charges_modes', function (Blueprint $table) {
            $table->dropColumn('id');
        });

        Schema::table('charges_modes', function (Blueprint $table) {
            $table->increments('id')->first();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('charges_modes', function (Blueprint $table) {
            $table->dropColumn('id');
        });

        Schema::table('charges_modes', function (Blueprint $table) {
            $table->integer('id')->first();
        });
    }
}
