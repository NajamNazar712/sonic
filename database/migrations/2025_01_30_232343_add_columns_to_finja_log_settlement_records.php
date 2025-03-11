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
        Schema::table('finja_log_settlement_records', function (Blueprint $table) {
            $table->boolean('wallet_log_charges_updated')->default(0);
            $table->timestamp('wallet_log_charges_updated_at')->nullabe();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('finja_log_settlement_records', function (Blueprint $table) {
            $table->dropColumn(['wallet_log_charges_updated', 'wallet_log_charges_updated_at']);
        });
    }
};
