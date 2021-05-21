<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateRetailCashDepositForAdminId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('retail_cash_deposits', function (Blueprint $table) {
            $table->integer('retail_user_id')->nullable()->change();
            $table->integer('admin_id')->index()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('retail_cash_deposits', function (Blueprint $table) {
            $table->integer('retail_user_id')->change();
            $table->dropColumn('admin_id');
        });
    }
}
