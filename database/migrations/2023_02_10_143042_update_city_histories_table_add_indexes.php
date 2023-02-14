<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCityHistoriesTableAddIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('city_histories', function (Blueprint $table) {
            $table->index('city_id');
            $table->index('hub');
            $table->index('hub_id');
            $table->index('zone_id');
            $table->index('pickup');
            $table->index('status');
            $table->index('updated_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('city_histories', function (Blueprint $table) {
            $table->dropIndex('city_id');
            $table->dropIndex('hub');
            $table->dropIndex('hub_id');
            $table->dropIndex('zone_id');
            $table->dropIndex('pickup');
            $table->dropIndex('status');
            $table->dropIndex('updated_by');
        });
    }
}
