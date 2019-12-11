<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateMonthAverageReportForRevenueTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('month_averages', function (Blueprint $table) {
            $table->decimal('revenue',16,2)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('month_averages', function (Blueprint $table) {
            $table->decimal('revenue',8,2)->change();
        });
    }
}
