<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateColumnIndexes7 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('key_account_daily_shipments', function (Blueprint $table) {
            $table->index('shipment_id');
            $table->index('admin_id');
            $table->index('created_at');
            $table->index('updated_at');
        });

        Schema::table('key_account_daily_shipment_crms', function (Blueprint $table) {
            $table->index('case_nature_type_id');
            $table->index('shipment_id');
            $table->index('admin_id');
            $table->index('created_at');
            $table->index('updated_at');
        });

        Schema::table('key_account_daily_summaries', function (Blueprint $table) {
            $table->index('admin_id');
            $table->index('created_at');
            $table->index('updated_at');
        });

        Schema::table('key_account_daily_summary_crms', function (Blueprint $table) {
            $table->index('admin_id');
            $table->index('case_nature_type_id');
            $table->index('channel_id');
            $table->index('created_at');
            $table->index('updated_at');
        });

        Schema::table('key_account_pending_crms', function (Blueprint $table) {
            $table->index('admin_id');
            $table->index('crm_request_id');
            $table->index('summary_crm_request_id');
            $table->index('created_at');
            $table->index('updated_at');
        });

        Schema::table('key_account_pending_summary_crms', function (Blueprint $table) {
            $table->index('admin_id');
            $table->index('case_nature_type_id');
            $table->index('created_at');
            $table->index('updated_at');
        });

        Schema::table('master_cargoes', function (Blueprint $table) {
            $table->index('origin_hub_id');
            $table->index('destination_hub_id');
            $table->index('junction_hub_1_id');
            $table->index('junction_hub_2_id');
            $table->index('shipping_mode_id');
            $table->index('transport_mode_id');
            $table->index('transport_mode_vendor_id');
            $table->index('status_id');
            $table->index('created_by');
            $table->index('received_by');
            $table->index('onward_forwarding');
        });

        Schema::table('master_cargo_bags', function (Blueprint $table) {
            $table->index('master_cargo_id');
            $table->index('bag_id');
            $table->index('status');
        });

        Schema::table('master_cargo_bag_excels', function (Blueprint $table) {
            $table->index('master_cargo_excel_id');
            $table->index('master_cargo_id');
            $table->index('bag_id');
        });

        Schema::table('master_cargo_bag_journey', function (Blueprint $table) {
            $table->index('bag_id');
            $table->index('seal_number');
            $table->index('bag_status_id');
            $table->index('admin_id');
            $table->index('master_cargo_id');
            $table->index('master_cargo_status_id');
        });

        Schema::table('master_cargo_excels', function (Blueprint $table) {
            $table->index('created_by');
        });

        Schema::table('master_cargo_junction_receivals', function (Blueprint $table) {
            $table->index('master_cargo_id');
            $table->index('junction_id');
            $table->index('receiver_id');
        });

        Schema::table('master_cargo_junction_sends', function (Blueprint $table) {
            $table->index('master_cargo_id');
            $table->index('sender_id');
        });

        Schema::table('merged_account_heads', function (Blueprint $table) {
            $table->index('created_by');
            $table->index('updated_by');
        });

        Schema::table('merged_sister_accounts', function (Blueprint $table) {
            $table->index('merged_head_id');
            $table->index('user_id');
        });

        Schema::table('merged_sister_account_mappings', function (Blueprint $table) {
            $table->index('merged_head_id');
            $table->index('head_user_id');
            $table->index('sister_user_id');
        });

        Schema::table('misrouted_history', function (Blueprint $table) {
            $table->index('shipment_id');
            $table->index('old_consignee_city_id');
            $table->index('new_consignee_city_id');
            $table->index('admin_id');
        });

        Schema::table('month_averages', function (Blueprint $table) {
            $table->index('origin_id');
        });

        Schema::table('multiple_sale_leads', function (Blueprint $table) {
            $table->index('admin_id');
            $table->index('updated_by');
        });

        Schema::table('multiple_sale_taggings', function (Blueprint $table) {
            $table->index('lead_id');
            $table->index('admin_id');
        });

        Schema::table('not_attempted_shipment_agings', function (Blueprint $table) {
            $table->index('hub_id');
            $table->index('zone_id');
            $table->index('created_at');
            $table->index('updated_at');
        });

        Schema::table('nsa_account_shipments', function (Blueprint $table) {
            $table->index('shipment_id');
            $table->index('hub_id');
            $table->index('status');
        });

        Schema::table('open_parcel_histories', function (Blueprint $table) {
            $table->index('shipment_id');
            $table->index('user_mode');
            $table->index('user_id');
            $table->index('date');
        });

        Schema::table('orderhive', function (Blueprint $table) {
            $table->index('user_id');
        });

        Schema::table('overnight_overland_report_datas', function (Blueprint $table) {
            $table->index('cargo_id');
            $table->index('origin_id');
            $table->index('destination_id');
            $table->index('shipping_mode_id');
            $table->index('vendor_id');
            $table->index('cargo_created_at');
            $table->index('status');
        });

        Schema::table('overnight_overland_report_origin_hubs', function (Blueprint $table) {
            $table->index('origin_id');
            $table->index('hub_id');
            $table->index('shipping_mode_id');
        });

        Schema::table('packaging_charges', function (Blueprint $table) {
            $table->index('type_id');
            $table->index('size_id');
        });

        Schema::table('packaging_material_request_details', function (Blueprint $table) {
            $table->index('wms_product_id');
        });

        Schema::table('packaging_material_request_histories', function (Blueprint $table) {
            $table->index(['packaging_material_request_id'], 'packaging_material_request_id_index');
            $table->index('status');
            $table->index('updated_by');
            $table->index('updated_by_user_id');
        });

        Schema::table('packaging_material_types', function (Blueprint $table) {
            $table->index('status');
            $table->index('created_by');
            $table->index('updated_by');
        });

        Schema::table('packaging_material_types_histories', function (Blueprint $table) {
            $table->index('type_id');
            $table->index('status');
            $table->index('created_by');
            $table->index('updated_by');
        });

        Schema::table('packaging_material_type_sizes', function (Blueprint $table) {
            $table->index('type_id');
            $table->index('wms_product_id');
        });

        Schema::table('packaging_material_type_sizes_histories', function (Blueprint $table) {
            $table->index('type_id');
            $table->index('updated_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('key_account_daily_shipments', function (Blueprint $table) {
            $table->dropIndex(['shipment_id']);
            $table->dropIndex(['admin_id']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
        });

        Schema::table('key_account_daily_shipment_crms', function (Blueprint $table) {
            $table->dropIndex(['case_nature_type_id']);
            $table->dropIndex(['shipment_id']);
            $table->dropIndex(['admin_id']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
        });

        Schema::table('key_account_daily_summaries', function (Blueprint $table) {
            $table->dropIndex(['admin_id']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
        });

        Schema::table('key_account_daily_summary_crms', function (Blueprint $table) {
            $table->dropIndex(['admin_id']);
            $table->dropIndex(['case_nature_type_id']);
            $table->dropIndex(['channel_id']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
        });

        Schema::table('key_account_pending_crms', function (Blueprint $table) {
            $table->dropIndex(['admin_id']);
            $table->dropIndex(['crm_request_id']);
            $table->dropIndex(['summary_crm_request_id']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
        });

        Schema::table('key_account_pending_summary_crms', function (Blueprint $table) {
            $table->dropIndex(['admin_id']);
            $table->dropIndex(['case_nature_type_id']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
        });

        Schema::table('master_cargoes', function (Blueprint $table) {
            $table->dropIndex(['origin_hub_id']);
            $table->dropIndex(['destination_hub_id']);
            $table->dropIndex(['junction_hub_1_id']);
            $table->dropIndex(['junction_hub_2_id']);
            $table->dropIndex(['shipping_mode_id']);
            $table->dropIndex(['transport_mode_id']);
            $table->dropIndex(['transport_mode_vendor_id']);
            $table->dropIndex(['status_id']);
            $table->dropIndex(['created_by']);
            $table->dropIndex(['received_by']);
            $table->dropIndex(['onward_forwarding']);
        });

        Schema::table('master_cargo_bags', function (Blueprint $table) {
            $table->dropIndex(['master_cargo_id']);
            $table->dropIndex(['bag_id']);
            $table->dropIndex(['status']);
        });

        Schema::table('master_cargo_bag_excels', function (Blueprint $table) {
            $table->dropIndex(['master_cargo_excel_id']);
            $table->dropIndex(['master_cargo_id']);
            $table->dropIndex(['bag_id']);
        });

        Schema::table('master_cargo_bag_journey', function (Blueprint $table) {
            $table->dropIndex(['bag_id']);
            $table->dropIndex(['seal_number']);
            $table->dropIndex(['bag_status_id']);
            $table->dropIndex(['admin_id']);
            $table->dropIndex(['master_cargo_id']);
            $table->dropIndex(['master_cargo_status_id']);
        });

        Schema::table('master_cargo_excels', function (Blueprint $table) {
            $table->dropIndex(['created_by']);
        });

        Schema::table('master_cargo_junction_receivals', function (Blueprint $table) {
            $table->dropIndex(['master_cargo_id']);
            $table->dropIndex(['junction_id']);
            $table->dropIndex(['receiver_id']);
        });

        Schema::table('master_cargo_junction_sends', function (Blueprint $table) {
            $table->dropIndex(['master_cargo_id']);
            $table->dropIndex(['sender_id']);
        });

        Schema::table('merged_account_heads', function (Blueprint $table) {
            $table->dropIndex(['created_by']);
            $table->dropIndex(['updated_by']);
        });

        Schema::table('merged_sister_accounts', function (Blueprint $table) {
            $table->dropIndex(['merged_head_id']);
            $table->dropIndex(['user_id']);
        });

        Schema::table('merged_sister_account_mappings', function (Blueprint $table) {
            $table->dropIndex(['merged_head_id']);
            $table->dropIndex(['head_user_id']);
            $table->dropIndex(['sister_user_id']);
        });

        Schema::table('misrouted_history', function (Blueprint $table) {
            $table->dropIndex(['shipment_id']);
            $table->dropIndex(['old_consignee_city_id']);
            $table->dropIndex(['new_consignee_city_id']);
            $table->dropIndex(['admin_id']);
        });

        Schema::table('month_averages', function (Blueprint $table) {
            $table->dropIndex(['origin_id']);
        });

        Schema::table('multiple_sale_leads', function (Blueprint $table) {
            $table->dropIndex(['admin_id']);
            $table->dropIndex(['updated_by']);
        });

        Schema::table('multiple_sale_taggings', function (Blueprint $table) {
            $table->dropIndex(['lead_id']);
            $table->dropIndex(['admin_id']);
        });

        Schema::table('not_attempted_shipment_agings', function (Blueprint $table) {
            $table->dropIndex(['hub_id']);
            $table->dropIndex(['zone_id']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
        });

        Schema::table('nsa_account_shipments', function (Blueprint $table) {
            $table->dropIndex(['shipment_id']);
            $table->dropIndex(['hub_id']);
            $table->dropIndex(['status']);
        });

        Schema::table('open_parcel_histories', function (Blueprint $table) {
            $table->dropIndex(['shipment_id']);
            $table->dropIndex(['user_mode']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['date']);
        });

        Schema::table('orderhive', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
        });

        Schema::table('overnight_overland_report_datas', function (Blueprint $table) {
            $table->dropIndex(['cargo_id']);
            $table->dropIndex(['origin_id']);
            $table->dropIndex(['destination_id']);
            $table->dropIndex(['shipping_mode_id']);
            $table->dropIndex(['vendor_id']);
            $table->dropIndex(['cargo_created_at']);
            $table->dropIndex(['status']);
        });

        Schema::table('overnight_overland_report_origin_hubs', function (Blueprint $table) {
            $table->dropIndex(['origin_id']);
            $table->dropIndex(['hub_id']);
            $table->dropIndex(['shipping_mode_id']);
        });

        Schema::table('packaging_charges', function (Blueprint $table) {
            $table->dropIndex(['type_id']);
            $table->dropIndex(['size_id']);
        });

        Schema::table('packaging_material_request_details', function (Blueprint $table) {
            $table->dropIndex(['wms_product_id']);
        });

        Schema::table('packaging_material_request_histories', function (Blueprint $table) {
            $table->dropIndex('packaging_material_request_id_index');
            $table->dropIndex(['status']);
            $table->dropIndex(['updated_by']);
            $table->dropIndex(['updated_by_user_id']);
        });

        Schema::table('packaging_material_types', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['created_by']);
            $table->dropIndex(['updated_by']);
        });

        Schema::table('packaging_material_types_histories', function (Blueprint $table) {
            $table->dropIndex(['type_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['created_by']);
            $table->dropIndex(['updated_by']);
        });

        Schema::table('packaging_material_type_sizes', function (Blueprint $table) {
            $table->dropIndex(['type_id']);
            $table->dropIndex(['wms_product_id']);
        });

        Schema::table('packaging_material_type_sizes_histories', function (Blueprint $table) {
            $table->dropIndex(['type_id']);
            $table->dropIndex(['updated_by']);
        });
    }
}
