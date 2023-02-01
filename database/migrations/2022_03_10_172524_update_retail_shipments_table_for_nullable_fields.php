<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateRetailShipmentsTableForNullableFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('retail_shipments', function (Blueprint $table) {
            $table->decimal('total_charges_without_gst')->nullable()->change();
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
            $table->decimal('total_charges_without_gst')->change();
        });
    }
}
