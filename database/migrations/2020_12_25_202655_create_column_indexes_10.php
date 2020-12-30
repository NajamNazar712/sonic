<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateColumnIndexes10 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//        Schema::table('shipment_information_logs', function (Blueprint $table) {
////            $table->index('shipment_id');
//            $table->index('updated_by');
//        });

//        Schema::table('shipment_invoices', function (Blueprint $table) {
//            $table->index('shipment_id');
//        });

//        Schema::table('shipment_invoice_items', function (Blueprint $table) {
//            $table->index('shipment_invoice_id');
//        });
//
//        Schema::table('shipment_on_hold', function (Blueprint $table) {
//            $table->index('shipment_id');
//            $table->index('delivery_date');
//            $table->index('dispatch_date');
//            $table->index('status');
//            $table->index('email_status');
//            $table->index('added_by');
//        });
//
//        Schema::table('shipment_open_box_journeys', function (Blueprint $table) {
//            $table->index('shipment_id');
//            $table->index('open_box_status_id');
//            $table->index('created_by');
//        });
//
//        Schema::table('shipment_pieces_requests', function (Blueprint $table) {
//            $table->index('shipment_id');
//            $table->index('added_by');
//            $table->index('status');
//            $table->index('request_status_id');
//            $table->index('last_updated_by_admin');
//            $table->index('last_updated_by_user');
//            $table->index('last_updated_at');
//            $table->index('department_id');
//        });
//
//        Schema::table('shipment_prebooks', function (Blueprint $table) {
//            $table->index('user_id');
//        });
//
//        Schema::table('shipper_air_waybill_settings', function (Blueprint $table) {
//            $table->index('user_id');
//            $table->index('type');
//        });
//
//        Schema::table('shipper_contacts', function (Blueprint $table) {
//            $table->index('shipper_id');
//            $table->index('status');
//        });
//
//        Schema::table('shipper_notification_emails', function (Blueprint $table) {
//            $table->index('user_id');
//        });
//
//        Schema::table('shopify_invoice_settings', function (Blueprint $table) {
//            $table->index('user_id');
//        });
//
//        Schema::table('short_receive_report_time_hub_wises', function (Blueprint $table) {
//            $table->index('hub_id');
//            $table->index('time');
//        });
//
//        Schema::table('sms', function (Blueprint $table) {
//            $table->index('status');
//        });
//
//        Schema::table('sms_histories', function (Blueprint $table) {
//            $table->index('sender_id');
//        });
//
//        Schema::table('sms_history_riders', function (Blueprint $table) {
//            $table->index('sms_history_id');
//            $table->index('rider_id');
//        });
//
//        Schema::table('standard_cash_handling_charges', function (Blueprint $table) {
//            $table->index('range_up');
//            $table->index('range_down');
//        });
//
//        Schema::table('standard_insurance_charges', function (Blueprint $table) {
//            $table->index('range_up');
//            $table->index('range_down');
//        });
//
//        Schema::table('standard_weight_charges', function (Blueprint $table) {
//            $table->index('range_up');
//            $table->index('range_down');
//        });
//
//        Schema::table('station_deposit_note_slips', function (Blueprint $table) {
//            $table->index('station_deposit_note_id');
//            $table->index('bank_id');
//        });
//
//        Schema::table('station_recovery_reports', function (Blueprint $table) {
//            $table->index('date');
//            $table->index('city_id');
//            $table->index('zone_id');
//        });
//
//        Schema::table('telenor_call_responses', function (Blueprint $table) {
//            $table->index('shipment_id');
//            $table->index('call_id');
//            $table->index('status');
//        });
//
//        Schema::table('users', function (Blueprint $table) {
//            $table->index('activated_at');
//            $table->index('updated_by_type');
//            $table->index('updated_by_id');
//            $table->index('rate_status');
//            $table->index('logo_status');
//            $table->index('payment_day');
//            $table->index('multipiece_status');
//            $table->index('invoice_group_by');
//            $table->index('segment_id');
//        });
//
//        Schema::table('user_bank_infos', function (Blueprint $table) {
//            $table->index('invoicing_cycle_id');
//            $table->index('generation_date');
//        });
//
//        Schema::table('user_shipping_infos', function (Blueprint $table) {
//            $table->index('status');
//            $table->index('warehouse');
//        });
//
//        Schema::table('v2_pickup_requests', function (Blueprint $table) {
//            $table->index('regenerate');
//            $table->index('reverse_pickup');
//        });
//
//        Schema::table('vision_soft_cod_payment_clears', function (Blueprint $table) {
//            $table->index('created_at');
//        });
//
//        Schema::table('vision_soft_daily_exps', function (Blueprint $table) {
//            $table->index('petty_cash_statement_id');
//        });
//
//        Schema::table('walkin_shipment_weight_charges', function (Blueprint $table) {
//            $table->index('shipment_id');
//        });
//
//        Schema::table('walk_in_cities', function (Blueprint $table) {
//            $table->index('city_id');
//            $table->index('pickup');
//            $table->index('delivery');
//        });
//
//        Schema::table('walk_in_international_standard_weight_charges', function (Blueprint $table) {
//            $table->index(['shipping_mode_id'], 'shipping_mode_id_index');
//        });
//
//        Schema::table('walk_in_international_standard_weight_charge_hubs', function (Blueprint $table) {
//            $table->index(['international_charges_id'], 'international_charges_id_index');
//            $table->index('hub_id');
//        });
//
//        Schema::table('walk_in_shipment_packaging_material_histories', function (Blueprint $table) {
//            $table->index('shipment_id');
//            $table->index('type_id');
//            $table->index('size_id');
//        });
//
//        Schema::table('walk_in_standard_weight_charges', function (Blueprint $table) {
//            $table->index('shipping_mode_id');
//            $table->index('delivery_type_id');
//        });
//
//        Schema::table('warehouses', function (Blueprint $table) {
//            $table->index('hub_id');
//            $table->index('status');
//            $table->index('master_type');
//            $table->index('pickup_address_id');
//        });
//
//        Schema::table('warehouse_fulfilment_hubs', function (Blueprint $table) {
//            $table->index('warehouse_id');
//            $table->index('hub_id');
//        });
//
//        Schema::table('warehouse_fulfilment_hubs_histories', function (Blueprint $table) {
//            $table->index('warehouse_id');
//            $table->index('hub_id');
//        });
//
//        Schema::table('warehouse_histories', function (Blueprint $table) {
//            $table->index('warehouse_id');
//            $table->index('hub_id');
//            $table->index('status');
//            $table->index('master_type');
//        });
//
//        Schema::table('weight_charges', function (Blueprint $table) {
//            $table->index('range_up');
//            $table->index('range_down');
//            $table->index('weight_addition');
//            $table->index('spkg');
//        });
//
//        Schema::table('zones', function (Blueprint $table) {
//            $table->index('status');
//            $table->index('business_category_id');
//        });
//
//        Schema::table('zone_class_cities', function (Blueprint $table) {
//            $table->index('zone_classification_id');
//        });
//
//        Schema::table('packaging_material_requests', function (Blueprint $table) {
////            $table->index('shipment_id');
//        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
//        Schema::table('shipment_information_logs', function (Blueprint $table) {
////            $table->dropIndex(['shipment_id']);
//            $table->dropIndex(['updated_by']);
//        });

//        Schema::table('shipment_invoices', function (Blueprint $table) {
//            $table->dropIndex(['shipment_id']);
//        });

//        Schema::table('shipment_invoice_items', function (Blueprint $table) {
//            $table->dropIndex(['shipment_invoice_id']);
//        });
//
//        Schema::table('shipment_on_hold', function (Blueprint $table) {
//            $table->dropIndex(['shipment_id']);
//            $table->dropIndex(['delivery_date']);
//            $table->dropIndex(['dispatch_date']);
//            $table->dropIndex(['status']);
//            $table->dropIndex(['email_status']);
//            $table->dropIndex(['added_by']);
//        });
//
//        Schema::table('shipment_open_box_journeys', function (Blueprint $table) {
//            $table->dropIndex(['shipment_id']);
//            $table->dropIndex(['open_box_status_id']);
//            $table->dropIndex(['created_by']);
//        });
//
//        Schema::table('shipment_pieces_requests', function (Blueprint $table) {
//            $table->dropIndex(['shipment_id']);
//            $table->dropIndex(['added_by']);
//            $table->dropIndex(['status']);
//            $table->dropIndex(['request_status_id']);
//            $table->dropIndex(['last_updated_by_admin']);
//            $table->dropIndex(['last_updated_by_user']);
//            $table->dropIndex(['last_updated_at']);
//            $table->dropIndex(['department_id']);
//        });
//
//        Schema::table('shipment_prebooks', function (Blueprint $table) {
//            $table->dropIndex(['user_id']);
//        });
//
//        Schema::table('shipper_air_waybill_settings', function (Blueprint $table) {
//            $table->dropIndex(['user_id']);
//            $table->dropIndex(['type']);
//        });
//
//        Schema::table('shipper_contacts', function (Blueprint $table) {
//            $table->dropIndex(['shipper_id']);
//            $table->dropIndex(['status']);
//        });
//
//        Schema::table('shipper_notification_emails', function (Blueprint $table) {
//            $table->dropIndex(['user_id']);
//        });
//
//        Schema::table('shopify_invoice_settings', function (Blueprint $table) {
//            $table->dropIndex(['user_id']);
//        });
//
//        Schema::table('short_receive_report_time_hub_wises', function (Blueprint $table) {
//            $table->dropIndex(['hub_id']);
//            $table->dropIndex(['time']);
//        });
//
//        Schema::table('sms', function (Blueprint $table) {
//            $table->dropIndex(['status']);
//        });
//
//        Schema::table('sms_histories', function (Blueprint $table) {
//            $table->dropIndex(['sender_id']);
//        });
//
//        Schema::table('sms_history_riders', function (Blueprint $table) {
//            $table->dropIndex(['sms_history_id']);
//            $table->dropIndex(['rider_id']);
//        });
//
//        Schema::table('standard_cash_handling_charges', function (Blueprint $table) {
//            $table->dropIndex(['range_up']);
//            $table->dropIndex(['range_down']);
//        });
//
//        Schema::table('standard_insurance_charges', function (Blueprint $table) {
//            $table->dropIndex(['range_up']);
//            $table->dropIndex(['range_down']);
//        });
//
//        Schema::table('standard_weight_charges', function (Blueprint $table) {
//            $table->dropIndex(['range_up']);
//            $table->dropIndex(['range_down']);
//        });
//
//        Schema::table('station_deposit_note_slips', function (Blueprint $table) {
//            $table->dropIndex(['station_deposit_note_id']);
//            $table->dropIndex(['bank_id']);
//        });
//
//        Schema::table('station_recovery_reports', function (Blueprint $table) {
//            $table->dropIndex(['date']);
//            $table->dropIndex(['city_id']);
//            $table->dropIndex(['zone_id']);
//        });
//
//        Schema::table('telenor_call_responses', function (Blueprint $table) {
//            $table->dropIndex(['shipment_id']);
//            $table->dropIndex(['call_id']);
//            $table->dropIndex(['status']);
//        });
//
//        Schema::table('users', function (Blueprint $table) {
//            $table->dropIndex(['activated_at']);
//            $table->dropIndex(['updated_by_type']);
//            $table->dropIndex(['updated_by_id']);
//            $table->dropIndex(['rate_status']);
//            $table->dropIndex(['logo_status']);
//            $table->dropIndex(['payment_day']);
//            $table->dropIndex(['multipiece_status']);
//            $table->dropIndex(['invoice_group_by']);
//            $table->dropIndex(['segment_id']);
//        });
//
//        Schema::table('user_bank_infos', function (Blueprint $table) {
//            $table->dropIndex(['invoicing_cycle_id']);
//            $table->dropIndex(['generation_date']);
//        });
//
//        Schema::table('user_shipping_infos', function (Blueprint $table) {
//            $table->dropIndex(['status']);
//            $table->dropIndex(['warehouse']);
//        });
//
//        Schema::table('v2_pickup_requests', function (Blueprint $table) {
//            $table->dropIndex(['regenerate']);
//            $table->dropIndex(['reverse_pickup']);
//        });
//
//        Schema::table('vision_soft_cod_payment_clears', function (Blueprint $table) {
//            $table->dropIndex(['created_at']);
//        });
//
//        Schema::table('vision_soft_daily_exps', function (Blueprint $table) {
//            $table->dropIndex(['petty_cash_statement_id']);
//        });
//
//        Schema::table('walkin_shipment_weight_charges', function (Blueprint $table) {
//            $table->dropIndex(['shipment_id']);
//        });
//
//        Schema::table('walk_in_cities', function (Blueprint $table) {
//            $table->dropIndex(['city_id']);
//            $table->dropIndex(['pickup']);
//            $table->dropIndex(['delivery']);
//        });
//
//        Schema::table('walk_in_international_standard_weight_charges', function (Blueprint $table) {
//            $table->dropIndex('shipping_mode_id_index');
//        });
//
//        Schema::table('walk_in_international_standard_weight_charge_hubs', function (Blueprint $table) {
//            $table->dropIndex('international_charges_id_index');
//            $table->dropIndex(['hub_id']);
//        });
//
//        Schema::table('walk_in_shipment_packaging_material_histories', function (Blueprint $table) {
//            $table->dropIndex(['shipment_id']);
//            $table->dropIndex(['type_id']);
//            $table->dropIndex(['size_id']);
//        });
//
//        Schema::table('walk_in_standard_weight_charges', function (Blueprint $table) {
//            $table->dropIndex(['shipping_mode_id']);
//            $table->dropIndex(['delivery_type_id']);
//        });
//
//        Schema::table('warehouses', function (Blueprint $table) {
//            $table->dropIndex(['hub_id']);
//            $table->dropIndex(['status']);
//            $table->dropIndex(['master_type']);
//            $table->dropIndex(['pickup_address_id']);
//        });
//
//        Schema::table('warehouse_fulfilment_hubs', function (Blueprint $table) {
//            $table->dropIndex(['warehouse_id']);
//            $table->dropIndex(['hub_id']);
//        });
//
//        Schema::table('warehouse_fulfilment_hubs_histories', function (Blueprint $table) {
//            $table->dropIndex(['warehouse_id']);
//            $table->dropIndex(['hub_id']);
//        });
//
//        Schema::table('warehouse_histories', function (Blueprint $table) {
//            $table->dropIndex(['warehouse_id']);
//            $table->dropIndex(['hub_id']);
//            $table->dropIndex(['status']);
//            $table->dropIndex(['master_type']);
//        });
//
//        Schema::table('weight_charges', function (Blueprint $table) {
//            $table->dropIndex(['range_up']);
//            $table->dropIndex(['range_down']);
//            $table->dropIndex(['weight_addition']);
//            $table->dropIndex(['spkg']);
//        });
//
//        Schema::table('zones', function (Blueprint $table) {
//            $table->dropIndex(['status']);
//            $table->dropIndex(['business_category_id']);
//        });
//
//        Schema::table('zone_class_cities', function (Blueprint $table) {
//            $table->dropIndex(['zone_classification_id']);
//        });
//
//        Schema::table('packaging_material_requests', function (Blueprint $table) {
////            $table->dropIndex(['shipment_id']);
//        });
    }
}
