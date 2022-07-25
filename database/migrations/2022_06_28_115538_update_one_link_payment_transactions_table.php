<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateOneLinkPaymentTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('one_link_payment_transactions', function (Blueprint $table) {
            $table->Integer('delivery_note_id')->index()->after('tracking_no');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('one_link_payment_transactions', function (Blueprint $table) {
            $table->dropColumn('delivery_note_id');
        });
    }
}
