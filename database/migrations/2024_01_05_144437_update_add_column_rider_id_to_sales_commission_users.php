<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateAddColumnRiderIdToSalesCommissionUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */public function up()
    {
        Schema::table('sales_commission_users', function (Blueprint $table) {
            $table->integer('rider_id')->after('user_id')->nullable()->index();
            $table->integer('user_id')->nullable()->change();

        });    
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales_commission_users', function (Blueprint $table) {
            $table->dropColumn('rider_id');

        });    
    }
}
