<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateRetailShippingModesForBusinessCategoryId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('retail_international_shipping_modes');

        Schema::table('retail_shipping_modes', function (Blueprint $table) {
            $table->integer('business_category_id')->index()->default(1)->after('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('retail_shipping_modes', function (Blueprint $table) {
            $table->dropColumn('business_category_id');
        });
    }
}
