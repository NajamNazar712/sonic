<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTablesForIndexing extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bag_shipments', function (Blueprint $table) {
            $table->index('shipment_id');
           
        });

        Schema::table('shipment_otps', function (Blueprint $table) {
            $table->index('shipment_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bag_shipments', function (Blueprint $table) {
            $table->dropIndex(['shipment_id']);
        });

        Schema::table('shipment_otps', function (Blueprint $table) {
            $table->dropIndex(['shipment_id']);
        });
    }
}
