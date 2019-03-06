<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateAccountTypeForZoneClassCitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('zone_class_cities', function (Blueprint $table) {
            $table->integer('zone_classification_id')->default(1);
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
        Schema::table('zone_class_cities', function (Blueprint $table) {
            $table->dropColumn('zone_classification_id');
        });
    }
}
