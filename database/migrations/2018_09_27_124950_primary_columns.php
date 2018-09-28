<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PrimaryColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cargo_consignment_junction_receivals', function (Blueprint $table) {
            $table->increments('id')->first();
        });

        Schema::table('cargo_consignment_shipments', function (Blueprint $table) {
            $table->increments('id')->first();
        });

        Schema::table('city_deliveries', function (Blueprint $table) {
            $table->increments('id')->first();
        });

        Schema::table('delivery_note_shipments', function (Blueprint $table) {
            $table->primary(['delivery_note_id', 'shipment_id']);
        });

        Schema::table('delivery_note_station_deposit_notes', function (Blueprint $table) {
            $table->primary(['station_deposit_note_id', 'delivery_note_id'], 'primary_index');
        });

        Schema::table('dispute_shipments', function (Blueprint $table) {
            $table->primary(['dispute_id', 'shipment_id']);
        });

        Schema::table('packaging_material_stock_hubs', function (Blueprint $table) {
            $table->primary('hub_id');
        });

        Schema::table('pickup_note_requests', function (Blueprint $table) {
            $table->primary(['pickup_note_id', 'pickup_request_id']);
        });

        Schema::table('return_note_shipments', function (Blueprint $table) {
            $table->primary(['return_note_id', 'shipment_id']);
        });

        Schema::table('shipment_status_shipment_status_reason', function (Blueprint $table) {
            $table->primary(['shipment_status_id', 'shipment_status_reason_id'], 'primary_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cargo_consignment_junction_receivals', function (Blueprint $table) {
            $table->dropColumn('id');
        });

        Schema::table('cargo_consignment_shipments', function (Blueprint $table) {
            $table->dropColumn('id');
        });

        Schema::table('city_deliveries', function (Blueprint $table) {
            $table->dropColumn('id');
        });

        Schema::table('delivery_note_shipments', function (Blueprint $table) {
            $table->dropPrimary(['delivery_note_id', 'shipment_id']);
        });

        Schema::table('delivery_note_station_deposit_notes', function (Blueprint $table) {
            $table->dropPrimary(['station_deposit_note_id', 'delivery_note_id']);
        });

        Schema::table('dispute_shipments', function (Blueprint $table) {
            $table->dropPrimary(['dispute_id', 'shipment_id']);
        });

        Schema::table('packaging_material_stock_hubs', function (Blueprint $table) {
            $table->dropPrimary('hub_id');
        });

        Schema::table('pickup_note_requests', function (Blueprint $table) {
            $table->dropPrimary(['pickup_note_id', 'pickup_request_id']);
        });

        Schema::table('return_note_shipments', function (Blueprint $table) {
            $table->dropPrimary(['return_note_id', 'shipment_id']);
        });

        Schema::table('shipment_status_shipment_status_reason', function (Blueprint $table) {
            $table->dropPrimary(['shipment_status_id', 'shipment_status_reason_id']);
        });
    }
}
