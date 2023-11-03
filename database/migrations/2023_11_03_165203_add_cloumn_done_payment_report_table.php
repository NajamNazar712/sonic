<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCloumnDonePaymentReportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('retail_done_payments_reports', function (Blueprint $table) {
            $table->integer('ibft_charges')->after('amount')->nullable(); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
        Schema::table('retail_done_payments_reports', function (Blueprint $table) {
            $table->dropColumn('ibft_charges');
        });
    }
}
