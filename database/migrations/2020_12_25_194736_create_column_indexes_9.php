<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateColumnIndexes9 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rate_histories', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('updated_by');
            $table->index('approved_by');
            $table->index('from_date');
            $table->index('to_date');
            $table->index('created_at');
            $table->index('updated_at');
        });

        Schema::table('replacement_to_regular_logs', function (Blueprint $table) {
            $table->index('shipment_id');
            $table->index('updated_by');
            $table->index('type');
            $table->index('created_at');
            $table->index('updated_at');
        });

        Schema::table('restricted_city_intercepts', function (Blueprint $table) {
            $table->index('city_id');
        });

        Schema::table('restrict_parcels_attempts', function (Blueprint $table) {
            $table->index('shipper_id');
            $table->index('status');
            $table->index('updated_by');
        });

        Schema::table('return_notes', function (Blueprint $table) {
            $table->index('completion_status');
            $table->index('actual_date');
        });

        Schema::table('return_note_images', function (Blueprint $table) {
            $table->index('return_note_id');
        });

        Schema::table('return_reattempt_ratios', function (Blueprint $table) {
            $table->index('shipment_id');
            $table->index('return_confirm_date');
        });

        Schema::table('revert_status_requests', function (Blueprint $table) {
            $table->index('shipment_id');
            $table->index('delivery_note_id');
            $table->index('admin_id');
        });

        Schema::table('revert_status_request_logs', function (Blueprint $table) {
            $table->index('shipment_id');
            $table->index('delivery_note_id');
            $table->index('previous_status');
            $table->index('updated_by');
        });

        Schema::table('riders', function (Blueprint $table) {
            $table->index('special_rider');
            $table->index('created_by');
            $table->index('updated_by');
            $table->index('rider_type_id');
            $table->index('blacklist');
        });

        Schema::table('rider_deliveries', function (Blueprint $table) {
            $table->index('delivery_note_id');
            $table->index('shipment_id');
            $table->index('rider_id');
            $table->index('rider_status_id');
            $table->index('rider_status_reason_id');
            $table->index('delivered_status');
        });

        Schema::table('rider_delivery_action_logs', function (Blueprint $table) {
            $table->index('type_id');
            $table->index('delivery_note_id');
            $table->index('shipment_id');
        });

        Schema::table('rider_delivery_note_statuses', function (Blueprint $table) {
            $table->index('delivery_note_id');
            $table->index('status');
        });

        Schema::table('rider_pickups', function (Blueprint $table) {
            $table->index('pickup_note_id');
            $table->index('pickup_request_id');
            $table->index('pickup_type');
            $table->index('pickup_not_pick_reason_id');
        });

        Schema::table('rider_pickup_action_logs', function (Blueprint $table) {
            $table->index('type_id');
            $table->index('pickup_note_id');
            $table->index('pickup_request_id');
        });

        Schema::table('rider_pickup_shipments', function (Blueprint $table) {
            $table->index('rider_pickup_id');
            $table->index('shipment_id');
        });

        Schema::table('route_locations', function (Blueprint $table) {
            $table->index('pickup_address_id');
            $table->index('route_id');
        });

        Schema::table('runner_details', function (Blueprint $table) {
            $table->index('runner_id');
            $table->index('status');
            $table->index('created_by');
            $table->index('completed_by');
        });

        Schema::table('runner_detail_times', function (Blueprint $table) {
            $table->index('runner_detail_id');
            $table->index('origin');
            $table->index('destination');
        });

        Schema::table('runner_junctions', function (Blueprint $table) {
            $table->index('runner_id');
            $table->index('junction_id');
            $table->index('order');
        });

        Schema::table('sales_commissions', function (Blueprint $table) {
            $table->index('shipper_id');
            $table->index('updated_by');
            $table->index('status');
        });

        Schema::table('sales_commission_external_users', function (Blueprint $table) {
            $table->index('shipper_id');
        });

        Schema::table('sales_commission_users', function (Blueprint $table) {
            $table->index('sales_commission_id');
            $table->index('tier_type_id');
            $table->index('tier_id');
            $table->index('user_id');
        });

        Schema::table('sales_tiers', function (Blueprint $table) {
            $table->index('tier_type');
            $table->index('added_by');
            $table->index('updated_by');
            $table->index('sales_status');
            $table->index('status');
        });

        Schema::table('sale_person_numbers', function (Blueprint $table) {
            $table->index('admin_id');
        });

        Schema::table('sale_person_target_logs', function (Blueprint $table) {
            $table->index('start_date');
            $table->index('end_date');
            $table->index('sales_person_id');
        });

        Schema::table('self_collection_shipments', function (Blueprint $table) {
            $table->index('shipment_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rate_histories', function (Blueprint $table) {
            $table->dropIndex('user_id');
            $table->dropIndex('updated_by');
            $table->dropIndex('approved_by');
            $table->dropIndex('from_date');
            $table->dropIndex('to_date');
            $table->dropIndex('created_at');
            $table->dropIndex('updated_at');
        });

        Schema::table('replacement_to_regular_logs', function (Blueprint $table) {
            $table->dropIndex('shipment_id');
            $table->dropIndex('updated_by');
            $table->dropIndex('type');
            $table->dropIndex('created_at');
            $table->dropIndex('updated_at');
        });

        Schema::table('restricted_city_intercepts', function (Blueprint $table) {
            $table->dropIndex('city_id');
        });

        Schema::table('restrict_parcels_attempts', function (Blueprint $table) {
            $table->dropIndex('shipper_id');
            $table->dropIndex('status');
            $table->dropIndex('updated_by');
        });

        Schema::table('return_notes', function (Blueprint $table) {
            $table->dropIndex('completion_status');
            $table->dropIndex('actual_date');
        });

        Schema::table('return_note_images', function (Blueprint $table) {
            $table->dropIndex('return_note_id');
        });

        Schema::table('return_reattempt_ratios', function (Blueprint $table) {
            $table->dropIndex('shipment_id');
            $table->dropIndex('return_confirm_date');
        });

        Schema::table('revert_status_requests', function (Blueprint $table) {
            $table->dropIndex('shipment_id');
            $table->dropIndex('delivery_note_id');
            $table->dropIndex('admin_id');
        });

        Schema::table('revert_status_request_logs', function (Blueprint $table) {
            $table->dropIndex('shipment_id');
            $table->dropIndex('delivery_note_id');
            $table->dropIndex('previous_status');
            $table->dropIndex('updated_by');
        });

        Schema::table('riders', function (Blueprint $table) {
            $table->dropIndex('special_rider');
            $table->dropIndex('created_by');
            $table->dropIndex('updated_by');
            $table->dropIndex('rider_type_id');
            $table->dropIndex('blacklist');
        });

        Schema::table('rider_deliveries', function (Blueprint $table) {
            $table->dropIndex('delivery_note_id');
            $table->dropIndex('shipment_id');
            $table->dropIndex('rider_id');
            $table->dropIndex('rider_status_id');
            $table->dropIndex('rider_status_reason_id');
            $table->dropIndex('delivered_status');
        });

        Schema::table('rider_delivery_action_logs', function (Blueprint $table) {
            $table->dropIndex('type_id');
            $table->dropIndex('delivery_note_id');
            $table->dropIndex('shipment_id');
        });

        Schema::table('rider_delivery_note_statuses', function (Blueprint $table) {
            $table->dropIndex('delivery_note_id');
            $table->dropIndex('status');
        });

        Schema::table('rider_pickups', function (Blueprint $table) {
            $table->dropIndex('pickup_note_id');
            $table->dropIndex('pickup_request_id');
            $table->dropIndex('pickup_type');
            $table->dropIndex('pickup_not_pick_reason_id');
        });

        Schema::table('rider_pickup_action_logs', function (Blueprint $table) {
            $table->dropIndex('type_id');
            $table->dropIndex('pickup_note_id');
            $table->dropIndex('pickup_request_id');
        });

        Schema::table('rider_pickup_shipments', function (Blueprint $table) {
            $table->dropIndex('rider_pickup_id');
            $table->dropIndex('shipment_id');
        });

        Schema::table('route_locations', function (Blueprint $table) {
            $table->dropIndex('pickup_address_id');
            $table->dropIndex('route_id');
        });

        Schema::table('runner_details', function (Blueprint $table) {
            $table->dropIndex('runner_id');
            $table->dropIndex('status');
            $table->dropIndex('created_by');
            $table->dropIndex('completed_by');
        });

        Schema::table('runner_detail_times', function (Blueprint $table) {
            $table->dropIndex('runner_detail_id');
            $table->dropIndex('origin');
            $table->dropIndex('destination');
        });

        Schema::table('runner_junctions', function (Blueprint $table) {
            $table->dropIndex('runner_id');
            $table->dropIndex('junction_id');
            $table->dropIndex('order');
        });

        Schema::table('sales_commissions', function (Blueprint $table) {
            $table->dropIndex('shipper_id');
            $table->dropIndex('updated_by');
            $table->dropIndex('status');
        });

        Schema::table('sales_commission_external_users', function (Blueprint $table) {
            $table->dropIndex('shipper_id');
        });

        Schema::table('sales_commission_users', function (Blueprint $table) {
            $table->dropIndex('sales_commission_id');
            $table->dropIndex('tier_type_id');
            $table->dropIndex('tier_id');
            $table->dropIndex('user_id');
        });

        Schema::table('sales_tiers', function (Blueprint $table) {
            $table->dropIndex('tier_type');
            $table->dropIndex('added_by');
            $table->dropIndex('updated_by');
            $table->dropIndex('sales_status');
            $table->dropIndex('status');
        });

        Schema::table('sale_person_numbers', function (Blueprint $table) {
            $table->dropIndex('admin_id');
        });

        Schema::table('sale_person_target_logs', function (Blueprint $table) {
            $table->dropIndex('start_date');
            $table->dropIndex('end_date');
            $table->dropIndex('sales_person_id');
        });

        Schema::table('self_collection_shipments', function (Blueprint $table) {
            $table->dropIndex('shipment_id');
            $table->dropIndex('status');
        });
    }
}
