<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateColumnIndexes1 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('account_reconciliations', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('created_at');
            $table->index('updated_at');
        });

        Schema::table('adjustment_logs', function (Blueprint $table) {
            $table->index('shipment_id');
            $table->index('adjustment_type_id');
            $table->index('admin_id');
            $table->index('pending_id');
            $table->index('done_id');
            $table->index('type');
            $table->index('created_at');
            $table->index('updated_at');
        });

        Schema::table('admins_screen_list', function (Blueprint $table) {
            $table->index('permission_id');
        });

        Schema::table('bags', function (Blueprint $table) {
            $table->index('seal_number');
            $table->index('origin_hub_id');
            $table->index('destination_hub_id');
            $table->index('junction_hub_1_id');
            $table->index('junction_hub_2_id');
            $table->index('shipping_mode_id');
            $table->index('transport_mode_id');
            $table->index('transport_mode_vendor_id');
            $table->index('type');
            $table->index('status_id');
            $table->index('created_by');
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('receiver_id');
            $table->index('received_at');
        });

        Schema::table('bag_shipments', function (Blueprint $table) {
            $table->index('bag_id');
            $table->index('status');
            $table->index('created_at');
            $table->index('updated_at');
        });

        Schema::table('blacklisted_consignees', function (Blueprint $table) {
            $table->index('consignee_information_id');
            $table->index('shipments');
            $table->index('delivered');
            $table->index('undelivered');
            $table->index('return');
            $table->index('blacklist_setting_id');
        });

        Schema::table('blacklisted_consignee_manually_blacklisteds', function (Blueprint $table) {
            $table->index(['consignee_information_id'], 'consignee_information_id_index');
            $table->index('added_by');
            $table->index(['blacklist_setting_id'], 'blacklist_setting_id_index');
        });

        Schema::table('blacklisted_consignee_manually_excludeds', function (Blueprint $table) {
            $table->index(['consignee_information_id'], 'consignee_information_id_index');
            $table->index('excluded_by');
        });

        Schema::table('blacklist_settings', function (Blueprint $table) {
            $table->index('labeling_id');
            $table->index('status');
            $table->index('added_by');
            $table->index('updated_by');
        });

        Schema::table('blacklist_setting_conditions', function (Blueprint $table) {
            $table->index('blacklist_setting_id');
            $table->index('blacklist_condition_id');
            $table->index('blacklist_logic_id');
            $table->index('blacklist_logic_value');
            $table->index('blacklist_shipment_range_id');
            $table->index(['blacklist_shipment_range_value'], 'blacklist_shipment_range_value_index');
            $table->index('blacklist_operation_id');
        });

        Schema::table('booking_sms_for_shippers', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('status');
        });

        Schema::table('booking_type_charges', function (Blueprint $table) {
            $table->index('user_id');
        });

        Schema::table('business_projection_accounts', function (Blueprint $table) {
            $table->index('city_id');
            $table->index('business_projection_reason_id');
        });

        Schema::table('business_projection_hubs', function (Blueprint $table) {
            $table->index('hub_id');
            $table->index('date');
            $table->index('created_at');
            $table->index('updated_at');
        });

        Schema::table('business_projection_reasons_logs', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('admin_id');
            $table->index(['business_projection_reason_id'], 'business_projection_reason_id_index');
        });

        Schema::table('business_projection_shipments', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('shipment');
            $table->index('created_at');
            $table->index('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('account_reconciliations', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
        });

        Schema::table('adjustment_logs', function (Blueprint $table) {
            $table->dropIndex(['shipment_id']);
            $table->dropIndex(['adjustment_type_id']);
            $table->dropIndex(['admin_id']);
            $table->dropIndex(['pending_id']);
            $table->dropIndex(['done_id']);
            $table->dropIndex(['type']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
        });

        Schema::table('admins_screen_list', function (Blueprint $table) {
            $table->dropIndex(['permission_id']);
        });

        Schema::table('bags', function (Blueprint $table) {
            $table->dropIndex(['seal_number']);
            $table->dropIndex(['origin_hub_id']);
            $table->dropIndex(['destination_hub_id']);
            $table->dropIndex(['junction_hub_1_id']);
            $table->dropIndex(['junction_hub_2_id']);
            $table->dropIndex(['shipping_mode_id']);
            $table->dropIndex(['transport_mode_id']);
            $table->dropIndex(['transport_mode_vendor_id']);
            $table->dropIndex(['type']);
            $table->dropIndex(['status_id']);
            $table->dropIndex(['created_by']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['receiver_id']);
            $table->dropIndex(['received_at']);
        });

        Schema::table('bag_shipments', function (Blueprint $table) {
            $table->dropIndex(['bag_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
        });

        Schema::table('blacklisted_consignees', function (Blueprint $table) {
            $table->dropIndex(['consignee_information_id']);
            $table->dropIndex(['shipments']);
            $table->dropIndex(['delivered']);
            $table->dropIndex(['undelivered']);
            $table->dropIndex(['return']);
            $table->dropIndex(['blacklist_setting_id']);
        });

        Schema::table('blacklisted_consignee_manually_blacklisteds', function (Blueprint $table) {
            $table->dropIndex('consignee_information_id_index');
            $table->dropIndex(['added_by']);
            $table->dropIndex('blacklist_setting_id_index');
        });

        Schema::table('blacklisted_consignee_manually_excludeds', function (Blueprint $table) {
            $table->dropIndex('consignee_information_id_index');
            $table->dropIndex(['excluded_by']);
        });

        Schema::table('blacklist_settings', function (Blueprint $table) {
            $table->dropIndex(['labeling_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['added_by']);
            $table->dropIndex(['updated_by']);
        });

        Schema::table('blacklist_setting_conditions', function (Blueprint $table) {
            $table->dropIndex(['blacklist_setting_id']);
            $table->dropIndex(['blacklist_condition_id']);
            $table->dropIndex(['blacklist_logic_id']);
            $table->dropIndex(['blacklist_logic_value']);
            $table->dropIndex(['blacklist_shipment_range_id']);
            $table->dropIndex('blacklist_shipment_range_value_index');
            $table->dropIndex(['blacklist_operation_id']);
        });

        Schema::table('booking_sms_for_shippers', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['status']);
        });

        Schema::table('booking_type_charges', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
        });

        Schema::table('business_projection_accounts', function (Blueprint $table) {
            $table->dropIndex(['city_id']);
            $table->dropIndex(['business_projection_reason_id']);
        });

        Schema::table('business_projection_hubs', function (Blueprint $table) {
            $table->dropIndex(['hub_id']);
            $table->dropIndex(['date']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
        });

        Schema::table('business_projection_reasons_logs', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['admin_id']);
            $table->dropIndex('business_projection_reason_id_index');
        });

        Schema::table('business_projection_shipments', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['shipment']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
        });
    }
}
