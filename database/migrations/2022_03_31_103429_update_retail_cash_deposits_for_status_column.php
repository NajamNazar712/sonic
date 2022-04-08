<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateRetailCashDepositsForStatusColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('retail_cash_deposits', function (Blueprint $table) {
            $table->tinyInteger('status')->default(0);
        });
    }
    //0 pending, 1 cash collected, 2 deposited

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('retail_cash_deposits', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
