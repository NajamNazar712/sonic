<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCrmTaggingForHubTaggingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('crm_request_taggings', function (Blueprint $table) {
            $table->integer('hub_id')->nullable();
        });
        Schema::table('crm_request_tagging_histories', function (Blueprint $table) {
            $table->integer('hub_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('crm_request_taggings', function (Blueprint $table) {
            $table->dropColumn('hub_id');
        });
        Schema::table('crm_request_tagging_histories', function (Blueprint $table) {
            $table->dropColumn('hub_id');
        });
    }
}
