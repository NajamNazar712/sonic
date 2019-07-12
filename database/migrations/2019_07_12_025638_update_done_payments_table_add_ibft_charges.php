<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateDonePaymentsTableAddIbftCharges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('done_payments', function (Blueprint $table) {
            $table->integer('ibft_charges')->nullable()->default(NULL);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('done_payments', function (Blueprint $table) {
            $table->dropColumn('ibft_charges');
        });
    }
}
