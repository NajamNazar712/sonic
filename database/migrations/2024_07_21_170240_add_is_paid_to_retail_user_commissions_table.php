<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsPaidToRetailUserCommissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('retail_user_commissions', function (Blueprint $table) {
            $table->boolean('is_paid')->default(0)->after('net_commission');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('retail_user_commissions', function (Blueprint $table) {
            $table->dropColumn('is_paid');
        });
    }
}
