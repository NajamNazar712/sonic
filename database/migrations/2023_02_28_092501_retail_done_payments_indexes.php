<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RetailDonePaymentsIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('retail_done_payments', function (Blueprint $table) {
            $table->index('company_bank_id');
            $table->index('status');
            $table->index('status_updated_by');
            $table->index('user_bank_info_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('retail_done_payments', function (Blueprint $table) {
            $table->dropIndex(['company_bank_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['status_updated_by']);
            $table->dropIndex(['user_bank_info_id']);
        });
    }
}
