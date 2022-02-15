<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateLeadTaggingForTerritoryId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lead_taggings', function (Blueprint $table) {
            $table->integer('territory_id')->index()->nullable();
            $table->integer('city_id')->nullable()->change();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lead_taggings', function (Blueprint $table) {
            $table->dropColumn('territory_id');
            $table->integer('city_id')->nullable(FALSE)->change();

        });
    }
}
