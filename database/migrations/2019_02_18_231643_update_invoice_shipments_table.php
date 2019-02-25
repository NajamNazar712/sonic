<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateInvoiceShipmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoice_shipments', function (Blueprint $table) {
            $table->dropColumn('weight_charges');
            $table->dropColumn('cash_handling_charges');
            $table->dropColumn('insurance_charges');
            $table->dropColumn('return_charges');
            $table->dropColumn('fuel_surcharge');
            $table->dropColumn('replacement_charges');
            $table->dropColumn('try_and_buy_charges');
            $table->dropColumn('packaging_material_charges');
            $table->dropColumn('adjustment_charges');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoice_shipments', function (Blueprint $table) {
            $table->decimal('weight_charges', 16, 2)->nullable()->default(NULL)->after('type');
            $table->decimal('cash_handling_charges', 16, 2)->nullable()->default(NULL)->after('weight_charges');
            $table->decimal('insurance_charges', 16, 2)->nullable()->default(NULL)->after('cash_handling_charges');
            $table->decimal('return_charges', 16, 2)->nullable()->default(NULL)->after('insurance_charges');
            $table->decimal('fuel_surcharge', 16, 2)->nullable()->default(NULL)->after('return_charges');
            $table->decimal('replacement_charges', 16, 2)->nullable()->default(NULL)->after('fuel_surcharge');
            $table->decimal('try_and_buy_charges', 16, 2)->nullable()->default(NULL)->after('replacement_charges');
            $table->decimal('packaging_material_charges', 16, 2)->nullable()->default(NULL)->after('try_and_buy_charges');
            $table->decimal('adjustment_charges', 16, 2)->nullable()->default(NULL)->after('packaging_material_charges');
        });
    }
}
