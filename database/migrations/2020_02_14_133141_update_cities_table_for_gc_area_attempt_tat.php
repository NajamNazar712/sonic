<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCitiesTableForGcAreaAttemptTat extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->integer('gc_area');
            $table->integer('attempt_tat')->default(1);
        });
        Schema::table('city_histories', function (Blueprint $table) {
            $table->integer('gc_area');
            $table->integer('attempt_tat');
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
            $table->dropColumn('gc_area');
            $table->dropColumn('attempt_tat');
        });
        Schema::table('city_histories', function (Blueprint $table) {
            $table->dropColumn('gc_area');
            $table->dropColumn('attempt_tat');
        });
    }
}
