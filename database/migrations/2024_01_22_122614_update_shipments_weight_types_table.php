<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateShipmentsWeightTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shipments_weight_types', function (Blueprint $table) {
            $table->decimal('shipper_weight_charges', 8, 2)->nullable()->default(NULL)->after('weight_type');
            $table->decimal('range_down_arrival_weight', 8, 2)->nullable()->default(NULL)->after('weight_type');
            $table->decimal('range_down_shipper_weight', 8, 2)->nullable()->default(NULL)->after('weight_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shipments_weight_types', function (Blueprint $table) {
            $table->dropColumn('shipper_weight_charges');
            $table->dropColumn('range_down_arrival_weight');
            $table->dropColumn('range_down_shipper_weight');
        });
    }
}
