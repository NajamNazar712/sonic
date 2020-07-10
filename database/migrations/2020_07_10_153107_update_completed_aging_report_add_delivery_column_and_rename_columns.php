<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCompletedAgingReportAddDeliveryColumnAndRenameColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('completed_aging_reports', function (Blueprint $table) {
            $table->renameColumn('main_hub_id','zone_id');
            $table->renameColumn('inserted_at','date');
            $table->renameColumn('days','count');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('completed_aging_reports', function (Blueprint $table) {
            $table->renameColumn('zone','main_hub_id');
            $table->renameColumn('date','inserted_at');
            $table->renameColumn('count','days');
        });
    }
}
