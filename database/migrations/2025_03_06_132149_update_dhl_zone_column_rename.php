<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('international_standard_dhl_rates', function (Blueprint $table) {
            $table->renameColumn('zone_1b', 'zone_12');
            $table->renameColumn('zone_8b', 'zone_13');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('international_standard_dhl_rates', function (Blueprint $table) {
            $table->renameColumn('zone_12', 'zone_1b');
            $table->renameColumn('zone_13', 'zone_8b');
        });
    }
};
