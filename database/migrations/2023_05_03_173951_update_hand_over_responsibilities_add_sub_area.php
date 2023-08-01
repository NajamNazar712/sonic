<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateHandOverResponsibilitiesAddSubArea extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('handover_responsibilities', function (Blueprint $table) {
            $table->integer('city_area_id')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('handover_responsibilities', function (Blueprint $table) {
            $table->removeColumn('city_area_id');
        });
    }
}
