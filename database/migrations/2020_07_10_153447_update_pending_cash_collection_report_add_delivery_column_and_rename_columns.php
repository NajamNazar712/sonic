<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePendingCashCollectionReportAddDeliveryColumnAndRenameColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pending_cash_collection_aging_reports', function (Blueprint $table) {
            $table->renameColumn('main_hub_id','zone');
            $table->renameColumn('inserted_at','date');
            $table->integer('delivery_note_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pending_cash_collection_aging_reports', function (Blueprint $table) {
            $table->renameColumn('zone','main_hub_id');
            $table->renameColumn('date','inserted_at');
            $table->dropColumn('delivery_note_id');
        });
    }
}
