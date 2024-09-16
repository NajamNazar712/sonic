<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateInternationalStandardDhlRateAddzone extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('international_standard_dhl_rates', function (Blueprint $table) {
            $table->decimal('zone_13', 8,2)->default(0)->after('zone_11');
            $table->decimal('zone_12', 8,2)->default(0)->after('zone_11');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('international_standard_dhl_rates', function (Blueprint $table) {
            $table->dropColumn('zone_12');
            $table->dropColumn('zone_13');
        });
    }
}
