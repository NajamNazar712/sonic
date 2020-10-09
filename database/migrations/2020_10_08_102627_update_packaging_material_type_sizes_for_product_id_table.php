<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePackagingMaterialTypeSizesForProductIdTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('packaging_material_type_sizes', function (Blueprint $table) {
            $table->integer('wms_product_id');
            $table->bigInteger('wms_product_quantity')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('packaging_material_type_sizes', function (Blueprint $table) {
            $table->dropColumn('wms_product_id');
            $table->dropColumn('wms_product_quantity');
        });
    }
}
