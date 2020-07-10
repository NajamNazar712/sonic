<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateV2PickupTablesAddIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('v2_pickup_note_requests', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('pickup_note_id');
            $table->index('pickup_request_id');
            $table->index('status');
            $table->index('ordering');
        });

        Schema::table('v2_pickup_notes', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('rider_id');
            $table->index('pickups');
            $table->index('status');
        });

        Schema::table('v2_pickup_received_shipments', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('pickup_request_id');
            $table->index('shipment_id');
        });

        Schema::table('v2_pickup_report_categories', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
        });

        Schema::table('v2_pickup_report_legends', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
        });

        Schema::table('v2_pickup_report_summaries', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('date');
            $table->index('total');
            $table->index('pending_operations');
            $table->index('pending_sales');
            $table->index('before_cut_off_time');
            $table->index('after_cut_off_time');
            $table->index('attempted_and_picked');
            $table->index('attempted_and_not_picked');
            $table->index('attempted_failed');
        });

        Schema::table('v2_pickup_reports', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('date');
            $table->index('pickup_request_id');
            $table->index('status_id');
            $table->index('sale_person_id');
            $table->index('expected_shipments');
            $table->index('received_shipments');
            $table->index('difference_shipments');
            $table->index('department_id');
            $table->index('legend_id');
            $table->index('category_id');
        });

        Schema::table('v2_pickup_request_attempts', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('pickup_request_id');
            $table->index('rider_id');
            $table->index('shipments_picked');
            $table->index('shipments_received');
            $table->index('reason_id');
            $table->index('attempt_date');
            $table->index('assigned_by');
        });

        Schema::table('v2_pickup_request_legends', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
        });

        Schema::table('v2_pickup_request_not_pick_reasons', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
        });

        Schema::table('v2_pickup_request_rider_statuses', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
        });

        Schema::table('v2_pickup_request_shipments', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('pickup_request_id');
            $table->index('shipment_id');
            $table->index('status');
        });

        Schema::table('v2_pickup_request_statuses', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
        });

        Schema::table('v2_pickup_requests', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('shipper_id');
            $table->index('pickup_address_id');
            $table->index('booked');
            $table->index('received');
            $table->index('city_id');
            $table->index('status_id');
            $table->index('rider_status');
            $table->index('attempts');
            $table->index('current_rider_id');
            $table->index('last_rider_id');
            $table->index('last_updated_by');
            $table->index('after_cut_off_time');
            $table->index('renew');
            $table->index('vendor');
            $table->index('try_and_buy');
        });

        Schema::table('v2_rider_pickup_action_logs', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('logged_at');
            $table->index('type_id');
            $table->index('pickup_request_id');
            $table->index('pickup_note_id');
        });

        Schema::table('v2_rider_pickups', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('added_at');
            $table->index('pickup_note_id');
            $table->index('pickup_request_id');
            $table->index('pickup_type');
        });

        Schema::table('shipments_v2_pickup_journeys', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('shipment_id');
            $table->index('status_id');
            $table->index('admin_id');
            $table->index('reference_1_id');
            $table->index('reference_2_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('v2_pickup_note_requests', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['pickup_note_id']);
            $table->dropIndex(['pickup_request_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['ordering']);
        });

        Schema::table('v2_pickup_notes', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['rider_id']);
            $table->dropIndex(['pickups']);
            $table->dropIndex(['status']);
        });

        Schema::table('v2_pickup_received_shipments', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['pickup_request_id']);
            $table->dropIndex(['shipment_id']);
        });

        Schema::table('v2_pickup_report_categories', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
        });

        Schema::table('v2_pickup_report_legends', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
        });

        Schema::table('v2_pickup_report_summaries', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['date']);
            $table->dropIndex(['total']);
            $table->dropIndex(['pending_operations']);
            $table->dropIndex(['pending_sales']);
            $table->dropIndex(['before_cut_off_time']);
            $table->dropIndex(['after_cut_off_time']);
            $table->dropIndex(['attempted_and_picked']);
            $table->dropIndex(['attempted_and_not_picked']);
            $table->dropIndex(['attempted_failed']);
        });

        Schema::table('v2_pickup_reports', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['date']);
            $table->dropIndex(['pickup_request_id']);
            $table->dropIndex(['status_id']);
            $table->dropIndex(['sale_person_id']);
            $table->dropIndex(['expected_shipments']);
            $table->dropIndex(['received_shipments']);
            $table->dropIndex(['difference_shipments']);
            $table->dropIndex(['department_id']);
            $table->dropIndex(['legend_id']);
            $table->dropIndex(['category_id']);
        });

        Schema::table('v2_pickup_request_attempts', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['pickup_request_id']);
            $table->dropIndex(['rider_id']);
            $table->dropIndex(['shipments_picked']);
            $table->dropIndex(['shipments_received']);
            $table->dropIndex(['reason_id']);
            $table->dropIndex(['attempt_date']);
            $table->dropIndex(['assigned_by']);
        });

        Schema::table('v2_pickup_request_legends', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
        });

        Schema::table('v2_pickup_request_not_pick_reasons', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
        });

        Schema::table('v2_pickup_request_rider_statuses', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
        });

        Schema::table('v2_pickup_request_shipments', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['pickup_request_id']);
            $table->dropIndex(['shipment_id']);
            $table->dropIndex(['status']);
        });

        Schema::table('v2_pickup_request_statuses', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
        });

        Schema::table('v2_pickup_requests', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['shipper_id']);
            $table->dropIndex(['pickup_address_id']);
            $table->dropIndex(['booked']);
            $table->dropIndex(['received']);
            $table->dropIndex(['city_id']);
            $table->dropIndex(['status_id']);
            $table->dropIndex(['rider_status']);
            $table->dropIndex(['attempts']);
            $table->dropIndex(['current_rider_id']);
            $table->dropIndex(['last_rider_id']);
            $table->dropIndex(['last_updated_by']);
            $table->dropIndex(['after_cut_off_time']);
            $table->dropIndex(['renew']);
            $table->dropIndex(['vendor']);
            $table->dropIndex(['try_and_buy']);
        });

        Schema::table('v2_rider_pickup_action_logs', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['logged_at']);
            $table->dropIndex(['type_id']);
            $table->dropIndex(['pickup_request_id']);
            $table->dropIndex(['pickup_note_id']);
        });

        Schema::table('v2_rider_pickups', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['added_at']);
            $table->dropIndex(['pickup_note_id']);
            $table->dropIndex(['pickup_request_id']);
            $table->dropIndex(['pickup_type']);
        });

        Schema::table('shipments_v2_pickup_journeys', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['shipment_id']);
            $table->dropIndex(['status_id']);
            $table->dropIndex(['admin_id']);
            $table->dropIndex(['reference_1_id']);
            $table->dropIndex(['reference_2_id']);
        });
    }
}
