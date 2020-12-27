<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateColumnIndexes5 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('daily_fake_statuses', function (Blueprint $table) {
            $table->index('zone_id');
            $table->index('delivery_note_id');
            $table->index('hub_id');
            $table->index('rider_id');
        });

        Schema::table('daily_visits', function (Blueprint $table) {
            $table->index('admin_id');
        });

        Schema::table('delay_in_delivery_shipments', function (Blueprint $table) {
            $table->index('crm_request_id');
            $table->index('shipment_id');
        });

        Schema::table('delivery_call_verification_ratios', function (Blueprint $table) {
            $table->index('min');
            $table->index('max');
        });

        Schema::table('delivery_notes', function (Blueprint $table) {
            $table->index('last_updated_at');
            $table->index('verified_by');
            $table->index('status_updated_at');
            $table->index('status_verified_at');
            $table->index('cash_collected_by');
            $table->index('cash_collected_at');
        });

        Schema::table('delivery_note_shipments', function (Blueprint $table) {
            $table->index('fake_status');
        });

        Schema::table('discount_charges', function (Blueprint $table) {
            $table->index('to');
            $table->index('from');
        });

        Schema::table('done_payments_reports', function (Blueprint $table) {
            $table->index('payment_id');
            $table->index('shipper_id');
        });

        Schema::table('draft_cargos', function (Blueprint $table) {
            $table->index('origin_id');
            $table->index('destination_id');
            $table->index('cargo_type');
            $table->index('shipping_mode_id');
            $table->index('added_by');
        });

        Schema::table('draft_cargo_shipments', function (Blueprint $table) {
            $table->index('draft_cargo_id');
            $table->index('shipment_id');
        });

        Schema::table('fuel_factor_histories', function (Blueprint $table) {
            $table->index('admin_id');
        });

        Schema::table('global_settings', function (Blueprint $table) {
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('daily_fake_statuses', function (Blueprint $table) {
            $table->dropIndex(['zone_id']);
            $table->dropIndex(['delivery_note_id']);
            $table->dropIndex(['hub_id']);
            $table->dropIndex(['rider_id']);
        });

        Schema::table('daily_visits', function (Blueprint $table) {
            $table->dropIndex(['admin_id']);
        });

        Schema::table('delay_in_delivery_shipments', function (Blueprint $table) {
            $table->dropIndex(['crm_request_id']);
            $table->dropIndex(['shipment_id']);
        });

        Schema::table('delivery_call_verification_ratios', function (Blueprint $table) {
            $table->dropIndex(['min']);
            $table->dropIndex(['max']);
        });

        Schema::table('delivery_notes', function (Blueprint $table) {
            $table->dropIndex(['last_updated_at']);
            $table->dropIndex(['verified_by']);
            $table->dropIndex(['status_updated_at']);
            $table->dropIndex(['status_verified_at']);
            $table->dropIndex(['cash_collected_by']);
            $table->dropIndex(['cash_collected_at']);
        });

        Schema::table('delivery_note_shipments', function (Blueprint $table) {
            $table->dropIndex(['fake_status']);
        });

        Schema::table('discount_charges', function (Blueprint $table) {
            $table->dropIndex(['to']);
            $table->dropIndex(['from']);
        });

        Schema::table('done_payments_reports', function (Blueprint $table) {
            $table->dropIndex(['payment_id']);
            $table->dropIndex(['shipper_id']);
        });

        Schema::table('draft_cargos', function (Blueprint $table) {
            $table->dropIndex(['origin_id']);
            $table->dropIndex(['destination_id']);
            $table->dropIndex(['cargo_type']);
            $table->dropIndex(['shipping_mode_id']);
            $table->dropIndex(['added_by']);
        });

        Schema::table('draft_cargo_shipments', function (Blueprint $table) {
            $table->dropIndex(['draft_cargo_id']);
            $table->dropIndex(['shipment_id']);
        });

        Schema::table('fuel_factor_histories', function (Blueprint $table) {
            $table->dropIndex(['admin_id']);
        });

        Schema::table('global_settings', function (Blueprint $table) {
            $table->dropIndex(['type']);
        });
    }
}
