<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateShipmentsTableAddCharges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->decimal('weight_charges', 8, 2)->nullable()->default(NULL);
            $table->decimal('cash_handling_charges', 8, 2)->nullable()->default(NULL);
            $table->decimal('insurance_charges', 8, 2)->nullable()->default(NULL);
            $table->decimal('return_charges', 8, 2)->nullable()->default(NULL);
            $table->decimal('fuel_surcharge', 8, 2)->nullable()->default(NULL);
            $table->decimal('replacement_charges', 8, 2)->nullable()->default(NULL);
            $table->decimal('try_and_buy_charges', 8, 2)->nullable()->default(NULL);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn('weight_charges');
            $table->dropColumn('cash_handling_charges');
            $table->dropColumn('insurance_charges');
            $table->dropColumn('return_charges');
            $table->dropColumn('fuel_surcharge');
            $table->dropColumn('replacement_charges');
            $table->dropColumn('try_and_buy_charges');
        });
    }
}
