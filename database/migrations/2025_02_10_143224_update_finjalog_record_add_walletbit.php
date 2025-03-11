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
            $table->tinyInteger('wallet_charges_finova_settled')->nullable();
            $table->dateTime('wallet_charges_finova_settled_updated_at')->nullable();
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
            $table->dropColumn('wallet_charges_finova_settled');
            $table->dropColumn('wallet_charges_finova_settled_updated_at');
        });
    }
};
