<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateVisionSoftDellRetRevenuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vision_soft_dell_ret_revenues', function (Blueprint $table) {
            $table->decimal('weight_charges',8, 2)->after('service_type_id');
            $table->decimal('insurance_charges',8, 2)->after('weight_charges');
            $table->decimal('fuel_surcharge',8, 2)->after('insurance_charges');
            $table->decimal('packing_charges',8, 2)->after('fuel_surcharge');
            $table->decimal('packaging_charges',8, 2)->after('packing_charges');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vision_soft_dell_ret_revenues', function (Blueprint $table) {
            $table->dropColumn('weight_charges');
            $table->dropColumn('insurance_charges');
            $table->dropColumn('fuel_surcharge');
            $table->dropColumn('packing_charges');
            $table->dropColumn('packaging_charges');
        });
    }
}
