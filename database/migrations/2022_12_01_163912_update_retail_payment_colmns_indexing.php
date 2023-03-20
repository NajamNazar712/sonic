<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateRetailPaymentColmnsIndexing extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('retail_done_payment_calculations', function (Blueprint $table) {
            $table->index('retail_done_payment_id', 'retail_done_payment_id_index');
        });
        Schema::table('retail_pending_payment_calculations', function (Blueprint $table) {
            $table->index('retail_pending_payment_id', 'retail_pending_payment_id_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
