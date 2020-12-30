<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateColumnIndexes2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cargo_consignments', function (Blueprint $table) {
            $table->index('type');
        });

        Schema::table('cargo_consignment_junction_sends', function (Blueprint $table) {
            $table->index('cargo_consignment_id');
            $table->index('sender_id');
        });

        Schema::table('cash_handling_charges', function (Blueprint $table) {
            $table->index('range_up');
            $table->index('range_down');
        });

        Schema::table('change_shipment_amount_logs', function (Blueprint $table) {
            $table->index('shipment_id');
            $table->index('admin_id');
        });

        Schema::table('change_shipment_weight_logs', function (Blueprint $table) {
            $table->index('shipment_id');
            $table->index('admin_id');
        });

        Schema::table('cities', function (Blueprint $table) {
            $table->index('business_category_id');
        });

        Schema::table('completed_aging_reports', function (Blueprint $table) {
            $table->index('hub_id');
            $table->index('zone_id');
            $table->index('date');
            $table->index('created_at');
            $table->index('updated_at');
        });

        Schema::table('consignee_informations', function (Blueprint $table) {
            $table->index('phone');
            $table->index('city_id');
        });

        Schema::table('consignee_information_logs', function (Blueprint $table) {
            $table->index('consignee_information_id');
            $table->index('phone');
            $table->index('city_id');
            $table->index('user_id');
        });

        Schema::table('consignee_infos', function (Blueprint $table) {
            $table->index('shipper_id');
            $table->index('city_id');
            $table->index('phone_number_1');
        });

        Schema::table('consignee_locations', function (Blueprint $table) {
            $table->index('phone_number');
        });

        Schema::table('consignee_shipment_locations', function (Blueprint $table) {
            $table->index('shipment_id');
            $table->index('previous_location_id');
            $table->index('current_location_id');
        });

        Schema::table('consolidations', function (Blueprint $table) {
            $table->index('default_shipment_id');
        });

        Schema::table('consolidation_shipments', function (Blueprint $table) {
            $table->index('consolidation_id');
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
        Schema::table('cargo_consignments', function (Blueprint $table) {
            $table->dropIndex(['type']);
        });

        Schema::table('cargo_consignment_junction_sends', function (Blueprint $table) {
            $table->dropIndex(['cargo_consignment_id']);
            $table->dropIndex(['sender_id']);
        });

        Schema::table('cash_handling_charges', function (Blueprint $table) {
            $table->dropIndex(['range_up']);
            $table->dropIndex(['range_down']);
        });

        Schema::table('change_shipment_amount_logs', function (Blueprint $table) {
            $table->dropIndex(['shipment_id']);
            $table->dropIndex(['admin_id']);
        });

        Schema::table('change_shipment_weight_logs', function (Blueprint $table) {
            $table->dropIndex(['shipment_id']);
            $table->dropIndex(['admin_id']);
        });

        Schema::table('cities', function (Blueprint $table) {
            $table->dropIndex(['business_category_id']);
        });

        Schema::table('completed_aging_reports', function (Blueprint $table) {
            $table->dropIndex(['hub_id']);
            $table->dropIndex(['zone_id']);
            $table->dropIndex(['date']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
        });

        Schema::table('consignee_informations', function (Blueprint $table) {
            $table->dropIndex(['phone']);
            $table->dropIndex(['city_id']);
        });

        Schema::table('consignee_information_logs', function (Blueprint $table) {
            $table->dropIndex(['consignee_information_id']);
            $table->dropIndex(['phone']);
            $table->dropIndex(['city_id']);
            $table->dropIndex(['user_id']);
        });

        Schema::table('consignee_infos', function (Blueprint $table) {
            $table->dropIndex(['shipper_id']);
            $table->dropIndex(['city_id']);
            $table->dropIndex(['phone_number_1']);
        });

        Schema::table('consignee_locations', function (Blueprint $table) {
            $table->dropIndex(['phone_number']);
        });

        Schema::table('consignee_shipment_locations', function (Blueprint $table) {
            $table->dropIndex(['shipment_id']);
            $table->dropIndex(['previous_location_id']);
            $table->dropIndex(['current_location_id']);
        });

        Schema::table('consolidations', function (Blueprint $table) {
            $table->dropIndex(['default_shipment_id']);
        });

        Schema::table('consolidation_shipments', function (Blueprint $table) {
            $table->dropIndex(['consolidation_id']);
            $table->dropIndex(['shipment_id']);
        });
    }
}
