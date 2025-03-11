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
            $table->integer('wallet_charges')->default(0);
            $table->dateTime('wallet_charges_updated_at')->nullable();
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
            $table->dropColumn(['wallet_charges','wallet_charges_updated_at']);
        });
    }
};
