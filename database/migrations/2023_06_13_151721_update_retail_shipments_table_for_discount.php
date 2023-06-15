<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateRetailShipmentsTableForDiscount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('retail_shipments', function (Blueprint $table) {
            $table->integer('admin_discount')->nullable();
            $table->integer('admin_discount_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('retail_shipments', function (Blueprint $table) {
            $table->dropColumn('admin_discount');
            $table->dropColumn('admin_discount_type');
        });
    }
}
