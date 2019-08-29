<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateAdjustmentTypeLogsForPendingPayment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('adjustment_logs', function (Blueprint $table) {
            $table->renameColumn('amount', 'adjustment_amount');
            $table->tinyInteger('pending_id')->nullable();
            $table->tinyInteger('done_id')->nullable();
            $table->tinyInteger('type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('adjustment_logs', function (Blueprint $table) {
            $table->renameColumn('adjustment_amount', 'amount');
            $table->dropColumn('pending_id');
            $table->dropColumn('done_id');
            $table->dropColumn('type');
        });
    }
}
