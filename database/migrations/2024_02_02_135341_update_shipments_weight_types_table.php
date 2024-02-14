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
            $table->decimal('shipper_range_weight_charges', 8, 2)->nullable()->default(NULL)->after('shipper_weight_charges');
            $table->decimal('arrival_range_weight_charges', 8, 2)->nullable()->default(NULL)->after('shipper_weight_charges');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
     
    }
}
