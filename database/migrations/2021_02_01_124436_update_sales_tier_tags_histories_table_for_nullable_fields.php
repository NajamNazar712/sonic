<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateSalesTierTagsHistoriesTableForNullableFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sale_tier_tag_histories', function (Blueprint $table) {
            $table->integer('poc')->nullable()->change();
            $table->integer('kam')->nullable()->change();
            $table->integer('ref')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sale_tier_tag_histories', function (Blueprint $table) {
            $table->integer('poc')->change();
            $table->integer('kam')->change();
            $table->integer('ref')->change();
        });
    }
}
