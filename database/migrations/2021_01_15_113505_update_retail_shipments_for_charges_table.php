<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateRetailShipmentsForChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('retail_shipments', function (Blueprint $table) {
            $table->renameColumn('total_charges', 'total_charges_without_gst');
            $table->renameColumn('gst_charges', 'gst');
            $table->renameColumn('total_amount', 'total_charges');
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
            $table->renameColumn('total_charges_without_gst', 'total_charges');
            $table->renameColumn('gst', 'gst_charges');
            $table->renameColumn('total_charges', 'total_amount');
        });
    }
}
