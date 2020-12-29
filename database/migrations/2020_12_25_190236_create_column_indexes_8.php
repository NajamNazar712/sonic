<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateColumnIndexes8 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pending_booking_type_charges', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('shipping_mode_id');
        });

        Schema::table('pending_cash_collection_aging_reports', function (Blueprint $table) {
            $table->index('hub_id');
            $table->index('zone_id');
            $table->index('count');
        });

        Schema::table('pending_cash_handling_charges', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('shipping_mode_id');
            $table->index('range_up');
            $table->index('range_down');
        });

        Schema::table('pending_corporate_booking_type_charges', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('shipping_mode_id');
        });

        Schema::table('pending_corporate_cash_handling_charges', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('shipping_mode_id');
            $table->index('range_up');
            $table->index('range_down');
        });

        Schema::table('pending_corporate_delivery_type_statuses', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('delivery_type_id');
            $table->index('shipping_mode_id');
            $table->index('status');
        });

        Schema::table('pending_corporate_discount_charges', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('shipping_mode_id');
            $table->index('to');
            $table->index('from');
            $table->index('added_by');
        });

        Schema::table('pending_corporate_fuel_surcharges', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('shipping_mode_id');
        });

        Schema::table('pending_corporate_insurance_charges', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('shipping_mode_id');
            $table->index('range_up');
            $table->index('range_down');
        });

        Schema::table('pending_corporate_min_chargeable_weights', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('shipping_mode_id');
            $table->index('delivery_type_id');
        });

        Schema::table('pending_corporate_rate_statuses', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('shipping_mode_id');
            $table->index('status');
            $table->index('cash_handling_charges');
            $table->index('insurance_charges');
            $table->index('return_charges');
            $table->index('fuel_charges');
        });

        Schema::table('pending_corporate_return_charges', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('shipping_mode_id');
        });

        Schema::table('pending_corporate_weight_charges', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('shipping_mode_id');
            $table->index('delivery_type_id');
            $table->index('range_up');
            $table->index('range_down');
            $table->index('base');
        });

        Schema::table('pending_discount_charges', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('shipping_mode_id');
            $table->index('to');
            $table->index('from');
            $table->index('added_by');
        });

        Schema::table('pending_fuel_surcharges', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('shipping_mode_id');
        });

        Schema::table('pending_insurance_charges', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('shipping_mode_id');
            $table->index('range_up');
            $table->index('range_down');
        });

        Schema::table('pending_international_rates_cash_handling_charges', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('box_id');
            $table->index(['range_up'], 'range_up_index');
            $table->index(['range_down'], 'range_down_index');
        });

        Schema::table('pending_international_rates_discount_charges', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('box_id');
            $table->index('to');
            $table->index('from');
        });

        Schema::table('pending_international_rates_hubs', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('box_id');
            $table->index('hub_id');
        });

        Schema::table('pending_international_rates_insurance_charges', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('box_id');
            $table->index(['range_up'], 'range_up_index');
            $table->index(['range_down'], 'range_down_index');
        });

        Schema::table('pending_international_rates_remarks', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('admin_id');
        });

        Schema::table('pending_international_rates_return_charges', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('box_id');
        });

        Schema::table('pending_international_rates_statuses', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('box_id');
            $table->index('status');
            $table->index('cash_handling_charges');
            $table->index('insurance_charges');
            $table->index('return_charges');
            $table->index('admin_id');
        });

        Schema::table('pending_international_rates_weight_charges', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('box_id');
            $table->index('range_up');
            $table->index('range_down');
            $table->index('weight_addition');
            $table->index('spkg');
        });

        Schema::table('pending_packaging_charges', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('type_id');
            $table->index('size_id');
        });

        Schema::table('pending_rate_statuses', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('shipping_mode_id');
            $table->index('status');
            $table->index('cash_handling_charges');
            $table->index('insurance_charges');
            $table->index('return_charges');
            $table->index('fuel_charges');
        });

        Schema::table('pending_return_charges', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('shipping_mode_id');
        });

        Schema::table('pending_weight_charges', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('shipping_mode_id');
            $table->index('range_up');
            $table->index('range_down');
            $table->index('weight_addition');
            $table->index('spkg');
        });

        Schema::table('petty_cash_account_heads', function (Blueprint $table) {
            $table->index('status');
        });

        Schema::table('petty_cash_account_head_account_title', function (Blueprint $table) {
            $table->index(['petty_cash_account_head_id'], 'petty_cash_account_head_id_index');
            $table->index(['petty_cash_account_title_id'], 'petty_cash_account_title_id_index');
        });

        Schema::table('petty_cash_account_titles', function (Blueprint $table) {
            $table->index('status');
        });

        Schema::table('petty_cash_statements', function (Blueprint $table) {
            $table->index('hub_id');
            $table->index('created_by');
            $table->index('station_approved_at');
            $table->index('operation_approved_by');
            $table->index('operation_approved_at');
            $table->index('finance_approved_by');
            $table->index('finance_approved_at');
            $table->index('status');
            $table->index('rejected_by');
            $table->index('rejected_at');
            $table->index('shipment_id');
            $table->index('finance_received_statement_by');
            $table->index('finance_received_statement_at');
            $table->index('created_at');
            $table->index('updated_at');
        });

        Schema::table('petty_cash_statement_amount_logs', function (Blueprint $table) {
            $table->index(['petty_cash_statement_detail_id'], 'petty_cash_statement_detail_id_index');
            $table->index('admin_id');
        });

        Schema::table('petty_cash_statement_details', function (Blueprint $table) {
            $table->index('petty_cash_statement_id');
            $table->index('account_head_id');
            $table->index('account_title_id');
            $table->index('hub_id');
            $table->index('date');
            $table->index('updated_by');
            $table->index('status');
        });

        Schema::table('petty_cash_statement_detail_drafts', function (Blueprint $table) {
            $table->index(['petty_cash_statement_draft_id'], 'petty_cash_statement_draft_id_index');
            $table->index('account_head_id');
            $table->index('account_title_id');
            $table->index('hub_id');
            $table->index('date');
        });

        Schema::table('petty_cash_statement_drafts', function (Blueprint $table) {
            $table->index('hub_id');
            $table->index('created_by');
            $table->index('created_at');
            $table->index('updated_at');
        });

        Schema::table('pickup_address_iban_mappings', function (Blueprint $table) {
            $table->index('pickup_address_id');
            $table->index('bank_info_id');
        });

        Schema::table('qa_report_petty_cashes', function (Blueprint $table) {
            $table->index('hub_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pending_booking_type_charges', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['shipping_mode_id']);
        });

        Schema::table('pending_cash_collection_aging_reports', function (Blueprint $table) {
            $table->dropIndex(['hub_id']);
            $table->dropIndex(['zone_id']);
            $table->dropIndex(['count']);
        });

        Schema::table('pending_cash_handling_charges', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['shipping_mode_id']);
            $table->dropIndex(['range_up']);
            $table->dropIndex(['range_down']);
        });

        Schema::table('pending_corporate_booking_type_charges', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['shipping_mode_id']);
        });

        Schema::table('pending_corporate_cash_handling_charges', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['shipping_mode_id']);
            $table->dropIndex(['range_up']);
            $table->dropIndex(['range_down']);
        });

        Schema::table('pending_corporate_delivery_type_statuses', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['delivery_type_id']);
            $table->dropIndex(['shipping_mode_id']);
            $table->dropIndex(['status']);
        });

        Schema::table('pending_corporate_discount_charges', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['shipping_mode_id']);
            $table->dropIndex(['to']);
            $table->dropIndex(['from']);
            $table->dropIndex(['added_by']);
        });

        Schema::table('pending_corporate_fuel_surcharges', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['shipping_mode_id']);
        });

        Schema::table('pending_corporate_insurance_charges', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['shipping_mode_id']);
            $table->dropIndex(['range_up']);
            $table->dropIndex(['range_down']);
        });

        Schema::table('pending_corporate_min_chargeable_weights', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['shipping_mode_id']);
            $table->dropIndex(['delivery_type_id']);
        });

        Schema::table('pending_corporate_rate_statuses', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['shipping_mode_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['cash_handling_charges']);
            $table->dropIndex(['insurance_charges']);
            $table->dropIndex(['return_charges']);
            $table->dropIndex(['fuel_charges']);
        });

        Schema::table('pending_corporate_return_charges', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['shipping_mode_id']);
        });

        Schema::table('pending_corporate_weight_charges', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['shipping_mode_id']);
            $table->dropIndex(['delivery_type_id']);
            $table->dropIndex(['range_up']);
            $table->dropIndex(['range_down']);
            $table->dropIndex(['base']);
        });

        Schema::table('pending_discount_charges', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['shipping_mode_id']);
            $table->dropIndex(['to']);
            $table->dropIndex(['from']);
            $table->dropIndex(['added_by']);
        });

        Schema::table('pending_fuel_surcharges', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['shipping_mode_id']);
        });

        Schema::table('pending_insurance_charges', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['shipping_mode_id']);
            $table->dropIndex(['range_up']);
            $table->dropIndex(['range_down']);
        });

        Schema::table('pending_international_rates_cash_handling_charges', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['box_id']);
            $table->dropIndex('range_up_index');
            $table->dropIndex('range_down_index');
        });

        Schema::table('pending_international_rates_discount_charges', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['box_id']);
            $table->dropIndex(['to']);
            $table->dropIndex(['from']);
        });

        Schema::table('pending_international_rates_hubs', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['box_id']);
            $table->dropIndex(['hub_id']);
        });

        Schema::table('pending_international_rates_insurance_charges', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['box_id']);
            $table->dropIndex('range_up_index');
            $table->dropIndex('range_down_index');
        });

        Schema::table('pending_international_rates_remarks', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['admin_id']);
        });

        Schema::table('pending_international_rates_return_charges', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['box_id']);
        });

        Schema::table('pending_international_rates_statuses', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['box_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['cash_handling_charges']);
            $table->dropIndex(['insurance_charges']);
            $table->dropIndex(['return_charges']);
            $table->dropIndex(['admin_id']);
        });

        Schema::table('pending_international_rates_weight_charges', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['box_id']);
            $table->dropIndex(['range_up']);
            $table->dropIndex(['range_down']);
            $table->dropIndex(['weight_addition']);
            $table->dropIndex(['spkg']);
        });

        Schema::table('pending_packaging_charges', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['type_id']);
            $table->dropIndex(['size_id']);
        });

        Schema::table('pending_rate_statuses', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['shipping_mode_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['cash_handling_charges']);
            $table->dropIndex(['insurance_charges']);
            $table->dropIndex(['return_charges']);
            $table->dropIndex(['fuel_charges']);
        });

        Schema::table('pending_return_charges', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['shipping_mode_id']);
        });

        Schema::table('pending_weight_charges', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['shipping_mode_id']);
            $table->dropIndex(['range_up']);
            $table->dropIndex(['range_down']);
            $table->dropIndex(['weight_addition']);
            $table->dropIndex(['spkg']);
        });

        Schema::table('petty_cash_account_heads', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });

        Schema::table('petty_cash_account_head_account_title', function (Blueprint $table) {
            $table->dropIndex('petty_cash_account_head_id_index');
            $table->dropIndex('petty_cash_account_title_id_index');
        });

        Schema::table('petty_cash_account_titles', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });

        Schema::table('petty_cash_statements', function (Blueprint $table) {
            $table->dropIndex(['hub_id']);
            $table->dropIndex(['created_by']);
            $table->dropIndex(['station_approved_at']);
            $table->dropIndex(['operation_approved_by']);
            $table->dropIndex(['operation_approved_at']);
            $table->dropIndex(['finance_approved_by']);
            $table->dropIndex(['finance_approved_at']);
            $table->dropIndex(['status']);
            $table->dropIndex(['rejected_by']);
            $table->dropIndex(['rejected_at']);
            $table->dropIndex(['shipment_id']);
            $table->dropIndex(['finance_received_statement_by']);
            $table->dropIndex(['finance_received_statement_at']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
        });

        Schema::table('petty_cash_statement_amount_logs', function (Blueprint $table) {
            $table->dropIndex('petty_cash_statement_detail_id_index');
            $table->dropIndex(['admin_id']);
        });

        Schema::table('petty_cash_statement_details', function (Blueprint $table) {
            $table->dropIndex(['petty_cash_statement_id']);
            $table->dropIndex(['account_head_id']);
            $table->dropIndex(['account_title_id']);
            $table->dropIndex(['hub_id']);
            $table->dropIndex(['date']);
            $table->dropIndex(['updated_by']);
            $table->dropIndex(['status']);
        });

        Schema::table('petty_cash_statement_detail_drafts', function (Blueprint $table) {
            $table->dropIndex('petty_cash_statement_draft_id_index');
            $table->dropIndex(['account_head_id']);
            $table->dropIndex(['account_title_id']);
            $table->dropIndex(['hub_id']);
            $table->dropIndex(['date']);
        });

        Schema::table('petty_cash_statement_drafts', function (Blueprint $table) {
            $table->dropIndex(['hub_id']);
            $table->dropIndex(['created_by']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
        });

        Schema::table('pickup_address_iban_mappings', function (Blueprint $table) {
            $table->dropIndex(['pickup_address_id']);
            $table->dropIndex(['bank_info_id']);
        });

        Schema::table('qa_report_petty_cashes', function (Blueprint $table) {
            $table->dropIndex(['hub_id']);
        });
    }
}
