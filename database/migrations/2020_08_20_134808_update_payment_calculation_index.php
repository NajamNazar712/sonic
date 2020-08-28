<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePaymentCalculationIndex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pending_payment_calculations', function (Blueprint $table) {
            $table->index('pending_payment_id');
        });
        Schema::table('done_payment_calculations', function (Blueprint $table) {
            $table->index('done_payment_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pending_payment_calculations', function (Blueprint $table) {
            $table->dropIndex(['pending_payment_id']);
        });
        Schema::table('done_payment_calculations', function (Blueprint $table) {
            $table->dropIndex(['pending_payment_id']);

        });
    }
}
