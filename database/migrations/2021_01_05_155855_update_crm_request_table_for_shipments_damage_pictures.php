<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCrmRequestTableForShipmentsDamagePictures extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('crm_requests', function (Blueprint $table) {
            $table->string('damage_product_picture')->nullable()->default(null);
            $table->string('product_packaging_picture')->nullable()->default(null);
            $table->string('actual_product_picture')->nullable()->default(null);
            $table->integer('damage_product_price')->nullable()->default(null);
            $table->string('missing_product_picture')->nullable()->default(null);
            $table->string('product_packaging_picture_for_content_short')->nullable()->default(null);
            $table->string('actual_product_picture_for_content_short')->nullable()->default(null);
            $table->integer('missing_product_price')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('crm_requests', function (Blueprint $table) {
            //
        });
    }
}
