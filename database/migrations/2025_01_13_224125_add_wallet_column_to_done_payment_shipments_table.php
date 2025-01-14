<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('done_payment_shipments', function (Blueprint $table) {
            $table->integer('wallet_action_bid')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('done_payment_shipments', function (Blueprint $table) {
            $table->dropColumn('wallet_action_bid');
        });
    }
};
