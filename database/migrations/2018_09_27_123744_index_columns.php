<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class IndexColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('role_id');
            $table->index('updated_by');
            $table->index('status');
        });

        Schema::table('admin_hubs', function (Blueprint $table) {
            $table->index('admin_id');
            $table->index('hub_id');
        });

        Schema::table('admin_roles', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('department_id');
            $table->index('updated_by');
        });

        Schema::table('admin_role_module_permissions', function (Blueprint $table) {
            $table->index('role_id');
            $table->index('permission_id');
        });

        Schema::table('banks_lists', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('affiliate');
            $table->index('status');
        });

        Schema::table('booking_type_charges', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('shipping_mode_id');
        });

        Schema::table('cargo_consignments', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('origin_hub_id');
            $table->index('destination_hub_id');
            $table->index('junction_hub_1_id');
            $table->index('junction_hub_2_id');
            $table->index('shipping_mode_id');
            $table->index('transport_mode_id');
            $table->index('transport_mode_vendor_id');
            $table->index('sender_id');
            $table->index('receiver_id');
            $table->index('status_id');
        });

        Schema::table('cargo_consignment_junction_receivals', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('cargo_consignment_id');
            $table->index('junction_id');
            $table->index('receiver_id');
        });

        Schema::table('cargo_consignment_shipments', function (Blueprint $table) {
            $table->index('cargo_consignment_id');
            $table->index('shipment_id');
            $table->index('status');
        });

        Schema::table('cash_handling_charges', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('user_id');
            $table->index('shipping_mode_id');
        });

        Schema::table('cities', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('hub');
            $table->index('hub_id');
            $table->index('pickup');
            $table->index('status');
        });

        Schema::table('city_deliveries', function (Blueprint $table) {
            $table->index('city_id');
            $table->index('booking_type_id');
            $table->index('shipping_mode_id');
        });

        Schema::table('delivery_notes', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('hub_id');
            $table->index('rider_id');
            $table->index('route_id');
            $table->index('admin_id');
            $table->index('updated_by');
            $table->index('status');
            $table->index('dncc_status');
            $table->index('cash_collection_status');
            $table->index('pending_status');
            $table->index('undelivered_print');
        });

        Schema::table('delivery_note_shipments', function (Blueprint $table) {
            $table->index('delivery_note_id');
            $table->index('shipment_id');
            $table->index('status');
            $table->index('call_verification');
        });

        Schema::table('delivery_note_station_deposit_notes', function (Blueprint $table) {
            $table->index('station_deposit_note_id', 'station_deposit_note_id_index');
            $table->index('delivery_note_id', 'delivery_note_id_index');
        });

        Schema::table('discount_charges', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('user_id');
            $table->index('shipping_mode_id');
            $table->index('added_by');
        });

        Schema::table('disputes', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('raised_by');
            $table->index('raised_by_status');
            $table->index('city_id');
            $table->index('dispute_type_id');
            $table->index('status');
            $table->index('updated_by');
        });

        Schema::table('dispute_comments', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('dispute_id');
            $table->index('admin_id');
        });

        Schema::table('dispute_shipments', function (Blueprint $table) {
            $table->index('dispute_id');
            $table->index('shipment_id');
            $table->index('status');
        });

        Schema::table('done_payments', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('user_id');
            $table->index('company_bank_id');
            $table->index('status');
        });

        Schema::table('done_payment_shipments', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('done_payment_id');
            $table->index('shipment_id');
            $table->index('type');
        });

        Schema::table('fuel_surcharges', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('user_id');
            $table->index('shipping_mode_id');
        });

        Schema::table('global_settings', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
        });

        Schema::table('insurance_charges', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('user_id');
            $table->index('shipping_mode_id');
        });

        Schema::table('module_permissions', function (Blueprint $table) {
            $table->index('module_id');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('type_id');
            $table->index('updated_by');
            $table->index('status');
        });

        Schema::table('packaging_charges', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('user_id');
            $table->index('shipping_mode_id');
        });

        Schema::table('packaging_material_requests', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('user_id');
            $table->index('city_id');
            $table->index('packaging_payment_mode_id');
            $table->index('status');
            $table->index('tracking_number');
        });

        Schema::table('packaging_material_stock_heads', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
        });

        Schema::table('packaging_stock_histories', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('admin_id');
            $table->index('entry_type');
            $table->index('hub_id');
        });

        Schema::table('pending_payments', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('user_id');
            $table->index('status');
        });

        Schema::table('pending_payment_shipments', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('pending_payment_id');
            $table->index('shipment_id');
            $table->index('type');
        });

        Schema::table('pickup_notes', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('rider_id');
            $table->index('pickup_type');
            $table->index('assigned_by_user_id');
            $table->index('status_id');
            $table->index('city_id');
            $table->index('updated_by');
        });

        Schema::table('pickup_note_requests', function (Blueprint $table) {
            $table->index('pickup_note_id');
            $table->index('pickup_request_id');
        });

        Schema::table('pickup_requests', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('shipper_id');
            $table->index('pickup_address_id');
            $table->index('pickup_type');
            $table->index('pickup_date');
            $table->index('status');
        });

        Schema::table('pickup_request_assigned_shipments', function (Blueprint $table) {
            $table->index('pickup_request_id');
            $table->index('shipment_id');
            $table->index('status');
        });

        Schema::table('pickup_request_received_shipments', function (Blueprint $table) {
            $table->index('pickup_request_id');
            $table->index('shipment_id');
        });

        Schema::table('pickup_request_short_received_shipments', function (Blueprint $table) {
            $table->index('pickup_request_id');
            $table->index('shipment_id');
        });

        Schema::table('rate_statuses', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('user_id');
            $table->index('shipping_mode_id');
            $table->index('status');
        });

        Schema::table('receiving_sheets', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('user_id');
            $table->index('status');
        });

        Schema::table('receiving_sheet_received', function (Blueprint $table) {
            $table->index('receiving_sheet_id');
            $table->index('user_id');
            $table->index('pickup_address_id');
        });

        Schema::table('receiving_sheet_shipments', function (Blueprint $table) {
            $table->index('receiving_sheet_id');
            $table->index('status');
        });

        Schema::table('return_charges', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('user_id');
            $table->index('shipping_mode_id');
        });

        Schema::table('return_notes', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('hub_id');
            $table->index('rider_id');
            $table->index('route_id');
            $table->index('admin_id');
            $table->index('status');
            $table->index('updated_by');
        });

        Schema::table('return_note_shipments', function (Blueprint $table) {
            $table->index('return_note_id');
            $table->index('shipment_id');
            $table->index('status');
        });

        Schema::table('riders', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('city_id');
            $table->index('route_id');
            $table->index('rider_category_id');
            $table->index('status');
        });

        Schema::table('routes', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('city_id');
            $table->index('status');
        });

        Schema::table('shipments', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('user_id');
            $table->index('booking_type_id');
            $table->index('pickup_address_id');
            $table->index('information_display');
            $table->index('consignee_city_id');
            $table->index('package_type');
            $table->index('pickup_date');
            $table->index('shipping_mode_id');
            $table->index('same_day_timing_id');
            $table->index('payment_mode_id');
            $table->index('tracking_number');
            $table->index('shipper_status_id');
            $table->index('consignee_status_id');
            $table->index('packaging_material_request');
            $table->index('payment_status_id');
        });

        Schema::table('shipments_journey', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('shipment_id');
            $table->index('shipper_status_id');
            $table->index('consignee_status_id');
            $table->index('status_reason_id');
            $table->index('user_id');
            $table->index('admin_id');
            $table->index('city_id');
            $table->index('reference_1_id');
            $table->index('reference_2_id');
        });

        Schema::table('shipments_payment_journey', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('shipment_id');
            $table->index('status_id');
            $table->index('admin_id');
        });

        Schema::table('shipment_items', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('shipment_id');
            $table->index('product_type_id');
            $table->index('insurance');
            $table->index('type');
            $table->index('bought');
        });

        Schema::table('shipment_payment_status', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
        });

        Schema::table('shipment_status', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
        });

        Schema::table('shipment_status_reason', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
        });

        Schema::table('shipment_status_shipment_status_reason', function (Blueprint $table) {
            $table->index('shipment_status_id', 'shipment_status_id_index');
            $table->index('shipment_status_reason_id', 'shipment_status_reason_id_index');
        });

        Schema::table('standard_booking_type_charges', function (Blueprint $table) {
            $table->index('shipping_mode_id');
        });

        Schema::table('standard_cash_handling_charges', function (Blueprint $table) {
            $table->index('shipping_mode_id');
        });

        Schema::table('standard_fuel_surcharges', function (Blueprint $table) {
            $table->index('shipping_mode_id');
        });

        Schema::table('standard_insurance_charges', function (Blueprint $table) {
            $table->index('shipping_mode_id');
        });

        Schema::table('standard_packaging_charges', function (Blueprint $table) {
            $table->index('shipping_mode_id');
        });

        Schema::table('standard_return_charges', function (Blueprint $table) {
            $table->index('shipping_mode_id');
        });

        Schema::table('standard_weight_charges', function (Blueprint $table) {
            $table->index('shipping_mode_id');
        });

        Schema::table('station_deposit_notes', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('hub_id');
            $table->index('deposited_by');
            $table->index('banks_list_id');
            $table->index('status');
        });

        Schema::table('substitute_users', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('user_id');
            $table->index('status');
        });

        Schema::table('substitute_user_permissions', function (Blueprint $table) {
            $table->index('substitute_user_id');
            $table->index('permission_id');
        });

        Schema::table('transport_mode_vendors', function (Blueprint $table) {
            $table->index('transport_mode_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('city_id');
            $table->index('status');
            $table->index('blacklist');
            $table->index('authorize');
            $table->index('rates_added_by');
            $table->index('rates_authorized_by');
            $table->index('account_activated_by');
            $table->index('product_id');
        });

        Schema::table('user_bank_infos', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('user_id');
            $table->index('bank_name');
            $table->index('city_id');
        });

        Schema::table('user_shipping_infos', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('user_id');
            $table->index('city_id');
            $table->index('hidden');
        });

        Schema::table('weight_charges', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('user_id');
            $table->index('shipping_mode_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['role_id']);
            $table->dropIndex(['updated_by']);
            $table->dropIndex(['status']);
        });

        Schema::table('admin_hubs', function (Blueprint $table) {
            $table->dropIndex(['admin_id']);
            $table->dropIndex(['hub_id']);
        });

        Schema::table('admin_roles', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['department_id']);
            $table->dropIndex(['updated_by']);
        });

        Schema::table('admin_role_module_permissions', function (Blueprint $table) {
            $table->dropIndex(['role_id']);
            $table->dropIndex(['permission_id']);
        });

        Schema::table('banks_lists', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['affiliate']);
            $table->dropIndex(['status']);
        });

        Schema::table('booking_type_charges', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['shipping_mode_id']);
        });

        Schema::table('cargo_consignments', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['origin_hub_id']);
            $table->dropIndex(['destination_hub_id']);
            $table->dropIndex(['junction_1_hub_id']);
            $table->dropIndex(['junction_2_hub_id']);
            $table->dropIndex(['shipping_mode_id']);
            $table->dropIndex(['transport_mode_id']);
            $table->dropIndex(['transport_mode_vendor_id']);
            $table->dropIndex(['sender_id']);
            $table->dropIndex(['receiver_id']);
            $table->dropIndex(['status_id']);
        });

        Schema::table('cargo_consignment_junction_receivals', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['cargo_consignment_id']);
            $table->dropIndex(['junction_id']);
            $table->dropIndex(['receiver_id']);
        });

        Schema::table('cargo_consignment_shipments', function (Blueprint $table) {
            $table->dropIndex(['cargo_consignment_id']);
            $table->dropIndex(['shipment_id']);
            $table->dropIndex(['status']);
        });

        Schema::table('cash_handling_charges', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['shipping_mode_id']);
        });

        Schema::table('cities', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['hub']);
            $table->dropIndex(['hub_id']);
            $table->dropIndex(['pickup']);
            $table->dropIndex(['status']);
        });

        Schema::table('city_deliveries', function (Blueprint $table) {
            $table->dropIndex(['city_id']);
            $table->dropIndex(['booking_type_id']);
            $table->dropIndex(['shipping_mode_id']);
        });

        Schema::table('delivery_notes', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['hub_id']);
            $table->dropIndex(['rider_id']);
            $table->dropIndex(['route_id']);
            $table->dropIndex(['admin_id']);
            $table->dropIndex(['updated_by']);
            $table->dropIndex(['status']);
            $table->dropIndex(['dncc_status']);
            $table->dropIndex(['cash_collection_status']);
            $table->dropIndex(['pending_status']);
            $table->dropIndex(['undelivered_print']);
        });

        Schema::table('delivery_note_shipments', function (Blueprint $table) {
            $table->dropIndex(['delivery_note_id']);
            $table->dropIndex(['shipment_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['call_verification']);
        });

        Schema::table('delivery_note_station_deposit_notes', function (Blueprint $table) {
            $table->dropIndex(['station_deposit_note_id']);
            $table->dropIndex(['delivery_note_id']);
        });

        Schema::table('discount_charges', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['shipping_mode_id']);
            $table->dropIndex(['added_by']);
        });

        Schema::table('disputes', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['raised_by']);
            $table->dropIndex(['raised_by_status']);
            $table->dropIndex(['city_id']);
            $table->dropIndex(['dispute_type_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['updated_by']);
        });

        Schema::table('dispute_comments', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['dispute_id']);
            $table->dropIndex(['admin_id']);
        });

        Schema::table('dispute_shipments', function (Blueprint $table) {
            $table->dropIndex(['dispute_id']);
            $table->dropIndex(['shipment_id']);
            $table->dropIndex(['status']);
        });

        Schema::table('done_payments', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['company_bank_id']);
            $table->dropIndex(['status']);
        });

        Schema::table('done_payment_shipments', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['done_payment_id']);
            $table->dropIndex(['shipment_id']);
            $table->dropIndex(['type']);
        });

        Schema::table('fuel_surcharges', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['shipping_mode_id']);
        });

        Schema::table('global_settings', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
        });

        Schema::table('insurance_charges', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['shipping_mode_id']);
        });

        Schema::table('module_permissions', function (Blueprint $table) {
            $table->dropIndex(['module_id']);
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['type_id']);
            $table->dropIndex(['updated_by']);
            $table->dropIndex(['status']);
        });

        Schema::table('packaging_charges', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['shipping_mode_id']);
        });

        Schema::table('packaging_material_requests', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['city_id']);
            $table->dropIndex(['packaging_payment_mode_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['tracking_number']);
        });

        Schema::table('packaging_material_stock_heads', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
        });

        Schema::table('packaging_stock_histories', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['admind_id']);
            $table->dropIndex(['entry_type']);
            $table->dropIndex(['hub_id']);
        });

        Schema::table('pending_payments', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['status']);
        });

        Schema::table('pending_payment_shipments', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['pending_payment_id']);
            $table->dropIndex(['shipment_id']);
            $table->dropIndex(['type']);
        });

        Schema::table('pickup_notes', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['rider_id']);
            $table->dropIndex(['pickup_type']);
            $table->dropIndex(['assigned_by_user_id']);
            $table->dropIndex(['status_id']);
            $table->dropIndex(['city_id']);
            $table->dropIndex(['updated_by']);
        });

        Schema::table('pickup_note_requests', function (Blueprint $table) {
            $table->dropIndex(['pickup_note_id']);
            $table->dropIndex(['pickup_request_id']);
        });

        Schema::table('pickup_requests', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['shipper_id']);
            $table->dropIndex(['pickup_address_id']);
            $table->dropIndex(['pickup_type']);
            $table->dropIndex(['pickup_date']);
            $table->dropIndex(['status']);
        });

        Schema::table('pickup_request_assigned_shipments', function (Blueprint $table) {
            $table->dropIndex(['pickup_request_id']);
            $table->dropIndex(['shipment_id']);
            $table->dropIndex(['status']);
        });

        Schema::table('pickup_request_received_shipments', function (Blueprint $table) {
            $table->dropIndex(['pickup_request_id']);
            $table->dropIndex(['shipment_id']);
        });

        Schema::table('pickup_request_short_received_shipments', function (Blueprint $table) {
            $table->dropIndex(['pickup_request_id']);
            $table->dropIndex(['shipment_id']);
        });

        Schema::table('rate_statuses', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['shipping_mode_id']);
            $table->dropIndex(['status']);
        });

        Schema::table('receiving_sheets', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['status']);
        });

        Schema::table('receiving_sheet_received', function (Blueprint $table) {
            $table->dropIndex(['receiving_sheet_id']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['pickup_address_id']);
        });

        Schema::table('receiving_sheet_shipments', function (Blueprint $table) {
            $table->dropIndex(['receiving_sheet_id']);
            $table->dropIndex(['status']);
        });

        Schema::table('return_charges', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['shipping_mode_id']);
        });

        Schema::table('return_notes', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['hub_id']);
            $table->dropIndex(['rider_id']);
            $table->dropIndex(['route_id']);
            $table->dropIndex(['admin_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['updated_by']);
        });

        Schema::table('return_note_shipments', function (Blueprint $table) {
            $table->dropIndex(['return_note_id']);
            $table->dropIndex(['shipment_id']);
            $table->dropIndex(['status']);
        });

        Schema::table('riders', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['city_id']);
            $table->dropIndex(['route_id']);
            $table->dropIndex(['rider_category_id']);
            $table->dropIndex(['status']);
        });

        Schema::table('routes', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['city_id']);
            $table->dropIndex(['status']);
        });

        Schema::table('shipments', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['booking_type_id']);
            $table->dropIndex(['pickup_address_id']);
            $table->dropIndex(['information_display']);
            $table->dropIndex(['consignee_city_id']);
            $table->dropIndex(['package_type']);
            $table->dropIndex(['pickup_date']);
            $table->dropIndex(['shipping_mode_id']);
            $table->dropIndex(['same_day_timing_id']);
            $table->dropIndex(['payment_mode_id']);
            $table->dropIndex(['tracking_number']);
            $table->dropIndex(['shipper_status_id']);
            $table->dropIndex(['consignee_status_id']);
            $table->dropIndex(['packaging_material_request']);
            $table->dropIndex(['payment_status_id']);
        });

        Schema::table('shipments_journey', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['shipment_id']);
            $table->dropIndex(['shipper_status_id']);
            $table->dropIndex(['consignee_status_id']);
            $table->dropIndex(['status_reason_id']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['admin_id']);
            $table->dropIndex(['city_id']);
            $table->dropIndex(['reference_1_id']);
            $table->dropIndex(['reference_2_id']);
        });

        Schema::table('shipments_payment_journey', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['shipment_id']);
            $table->dropIndex(['status_id']);
            $table->dropIndex(['admin_id']);
        });

        Schema::table('shipment_items', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['shipment_id']);
            $table->dropIndex(['product_type_id']);
            $table->dropIndex(['insurance']);
            $table->dropIndex(['type']);
            $table->dropIndex(['bought']);
        });

        Schema::table('shipment_payment_status', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
        });

        Schema::table('shipment_status', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
        });

        Schema::table('shipment_status_reason', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
        });

        Schema::table('shipment_status_shipment_status_reason', function (Blueprint $table) {
            $table->dropIndex(['shipment_status_id']);
            $table->dropIndex(['shipment_status_reason_id']);
        });

        Schema::table('standard_booking_type_charges', function (Blueprint $table) {
            $table->dropIndex(['shipping_mode_id']);
        });

        Schema::table('standard_cash_handling_charges', function (Blueprint $table) {
            $table->dropIndex(['shipping_mode_id']);
        });

        Schema::table('standard_fuel_surcharges', function (Blueprint $table) {
            $table->dropIndex(['shipping_mode_id']);
        });

        Schema::table('standard_insurance_charges', function (Blueprint $table) {
            $table->dropIndex(['shipping_mode_id']);
        });

        Schema::table('standard_packaging_charges', function (Blueprint $table) {
            $table->dropIndex(['shipping_mode_id']);
        });

        Schema::table('standard_return_charges', function (Blueprint $table) {
            $table->dropIndex(['shipping_mode_id']);
        });

        Schema::table('standard_weight_charges', function (Blueprint $table) {
            $table->dropIndex(['shipping_mode_id']);
        });

        Schema::table('station_deposit_notes', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['hub_id']);
            $table->dropIndex(['deposited_by']);
            $table->dropIndex(['banks_list_id']);
            $table->dropIndex(['status']);
        });

        Schema::table('substitute_users', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['status']);
        });

        Schema::table('substitute_user_permissions', function (Blueprint $table) {
            $table->dropIndex(['substitute_user_id']);
            $table->dropIndex(['permission_id']);
        });

        Schema::table('transport_mode_vendors', function (Blueprint $table) {
            $table->dropIndex(['transport_mode_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['city_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['blacklist']);
            $table->dropIndex(['authorize']);
            $table->dropIndex(['rates_added_by']);
            $table->dropIndex(['rates_authorized_by']);
            $table->dropIndex(['account_activated_by']);
            $table->dropIndex(['product_id']);
        });

        Schema::table('user_bank_infos', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['bank_name']);
            $table->dropIndex(['city_id']);
        });

        Schema::table('user_shipping_infos', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['city_id']);
            $table->dropIndex(['hidden']);
        });

        Schema::table('weight_charges', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['shipping_mode_id']);
        });
    }
}
