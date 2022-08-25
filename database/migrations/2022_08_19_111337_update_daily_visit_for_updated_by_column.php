<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateDailyVisitForUpdatedByColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('daily_visits', function (Blueprint $table) {
            $table->Integer('updated_by')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('daily_visits', function (Blueprint $table) {
            $table->dropColumn('updated_by');
        });
    }
}
