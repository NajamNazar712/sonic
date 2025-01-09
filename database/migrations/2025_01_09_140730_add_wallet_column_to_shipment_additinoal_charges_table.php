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
        Schema::table('shipment_additional_charges', function (Blueprint $table) {
            $table->boolean('wallet_log_updated')->default(0);
            $table->boolean('wallet_settlement_updated')->default(0);
            $table->boolean('wallet_adjustment_updated')->default(0);
            $table->timestamp('wallet_log_updated_at')->nullabe();
            $table->timestamp('wallet_settlement_updated_at')->nullabe();
            $table->timestamp('wallet_adjustment_updated_at')->nullabe();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shipment_additional_charges', function (Blueprint $table) {
            $table->dropColumn(['wallet_log_updated', 'wallet_settlement_updated', 'wallet_adjustment_updated', 'wallet_log_updated_at', 'wallet_settlement_updated_at','wallet_adjustment_updated_at']);
        });
    }
};
