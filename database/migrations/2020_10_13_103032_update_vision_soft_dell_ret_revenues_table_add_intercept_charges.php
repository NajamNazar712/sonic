<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateVisionSoftDellRetRevenuesTableAddInterceptCharges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vision_soft_dell_ret_revenues', function (Blueprint $table) {
            $table->decimal('intercept_charges', 8, 2)->after('return_charges');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vision_soft_dell_ret_revenues', function (Blueprint $table) {
            $table->dropColumn('intercept_charges');
        });
    }
}
