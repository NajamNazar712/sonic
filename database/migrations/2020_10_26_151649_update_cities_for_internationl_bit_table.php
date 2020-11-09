<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCitiesForInternationlBitTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->tinyInteger('business_category_id')->default(1);
        });
        Schema::table('zones', function (Blueprint $table) {
            $table->tinyInteger('business_category_id')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->dropColumn('business_category_id');
        });
        Schema::table('zones', function (Blueprint $table) {
            $table->dropColumn('business_category_id');
        });
    }
}
