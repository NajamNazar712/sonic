<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateColumnIndexes6 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('history_booking_type_charges', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('shipping_mode_id');
        });

        Schema::table('history_cash_handling_charges', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('shipping_mode_id');
            $table->index('range_up');
            $table->index('range_down');
        });

        Schema::table('history_corporate_booking_type_charges', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('shipping_mode_id');
        });

        Schema::table('history_corporate_cash_handling_charges', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('shipping_mode_id');
            $table->index('range_up');
            $table->index('range_down');
        });

        Schema::table('history_corporate_discount_charges', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('shipping_mode_id');
            $table->index('to');
            $table->index('from');
            $table->index('added_by');
        });

        Schema::table('history_corporate_fuel_surcharges', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('shipping_mode_id');
        });

        Schema::table('history_corporate_insurance_charges', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('shipping_mode_id');
            $table->index('range_up');
            $table->index('range_down');
        });

        Schema::table('history_corporate_min_chargeable_weights', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('shipping_mode_id');
            $table->index('delivery_type_id');
        });

        Schema::table('history_corporate_rate_statuses', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('shipping_mode_id');
            $table->index('status');
            $table->index('cash_handling_charges');
            $table->index('insurance_charges');
            $table->index('return_charges');
            $table->index('fuel_charges');
        });

        Schema::table('history_corporate_return_charges', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('shipping_mode_id');
        });

        Schema::table('history_corporate_weight_charges', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('shipping_mode_id');
            $table->index('delivery_type_id');
            $table->index('range_up');
            $table->index('range_down');
            $table->index('base');
        });

        Schema::table('history_discount_charges', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('shipping_mode_id');
            $table->index('to');
            $table->index('from');
            $table->index('added_by');
        });

        Schema::table('history_fuel_surcharges', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('shipping_mode_id');
        });

        Schema::table('history_insurance_charges', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('shipping_mode_id');
            $table->index('range_up');
            $table->index('range_down');
        });

        Schema::table('history_international_rates_cash_handling_charges', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('box_id');
            $table->index(['range_up'], 'range_up_index');
            $table->index(['range_down'], 'range_down_index');
        });

        Schema::table('history_international_rates_discount_charges', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('box_id');
            $table->index('to');
            $table->index('from');
        });

        Schema::table('history_international_rates_hubs', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('box_id');
            $table->index('hub_id');
        });

        Schema::table('history_international_rates_insurance_charges', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('box_id');
            $table->index(['range_up'], 'range_up_index');
            $table->index(['range_down'], 'range_down_index');
        });

        Schema::table('history_international_rates_remarks', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('admin_id');
        });

        Schema::table('history_international_rates_return_charges', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('box_id');
        });

        Schema::table('history_international_rates_statuses', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('box_id');
            $table->index('status');
            $table->index('cash_handling_charges');
            $table->index('insurance_charges');
            $table->index('return_charges');
            $table->index('admin_id');
        });

        Schema::table('history_international_rates_weight_charges', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('box_id');
            $table->index('range_up');
            $table->index('range_down');
            $table->index('weight_addition');
            $table->index('spkg');
        });

        Schema::table('history_packaging_charges', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('type_id');
            $table->index('size_id');
        });

        Schema::table('history_rate_statuses', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('shipping_mode_id');
            $table->index('status');
            $table->index('cash_handling_charges');
            $table->index('insurance_charges');
            $table->index('return_charges');
            $table->index('fuel_charges');
        });

        Schema::table('history_return_charges', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('shipping_mode_id');
        });

        Schema::table('history_shipper_bank_accounts', function (Blueprint $table) {
            $table->index('bank_id');
            $table->index('user_id');
        });

        Schema::table('history_weight_charges', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('shipping_mode_id');
            $table->index('range_up');
            $table->index('range_down');
            $table->index('weight_addition');
            $table->index('spkg');
        });

        Schema::table('holidays', function (Blueprint $table) {
            $table->index('holiday');
            $table->index('created_by');
            $table->index('updated_by');
        });

        Schema::table('hub_wise_splits', function (Blueprint $table) {
            $table->index('hub_id');
            $table->index('origin');
            $table->index('created_at');
            $table->index('updated_at');
        });

        Schema::table('insurance_charges', function (Blueprint $table) {
            $table->index('range_up');
            $table->index('range_down');
        });

        Schema::table('intercept_re_book_requests', function (Blueprint $table) {
            $table->index('shipment_id');
            $table->index('consignee_city_id');
            $table->index('shipper_id');
            $table->index('status');
            $table->index('updated_by');
            $table->index('updated_by_date');
            $table->index('admin_id');
        });

        Schema::table('intercept_re_book_request_histories', function (Blueprint $table) {
            $table->index('shipment_id');
            $table->index('old_consignee_city_id');
            $table->index('new_consignee_city_id');
            $table->index('shipper_id');
        });

        Schema::table('international_rates_cash_handling_charges', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('box_id');
            $table->index('range_up');
            $table->index('range_down');
        });

        Schema::table('international_rates_discount_charges', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('box_id');
            $table->index('to');
            $table->index('from');
        });

        Schema::table('international_rates_hubs', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('box_id');
            $table->index('hub_id');
        });

        Schema::table('international_rates_insurance_charges', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('box_id');
            $table->index('range_up');
            $table->index('range_down');
        });

        Schema::table('international_rates_remarks', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('admin_id');
        });

        Schema::table('international_rates_return_charges', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('box_id');
        });

        Schema::table('international_rates_statuses', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('box_id');
            $table->index('status');
            $table->index('cash_handling_charges');
            $table->index('insurance_charges');
            $table->index('return_charges');
            $table->index('admin_id');
        });

        Schema::table('international_rates_weight_charges', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('box_id');
            $table->index('range_up');
            $table->index('range_down');
            $table->index('weight_addition');
            $table->index('spkg');
        });

        Schema::table('international_shipments', function (Blueprint $table) {
            $table->index('postal_code');
        });

        Schema::table('junction_mappings', function (Blueprint $table) {
            $table->index('origin_id');
            $table->index('destination_id');
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
        Schema::table('history_booking_type_charges', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['shipping_mode_id']);
        });

        Schema::table('history_cash_handling_charges', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['shipping_mode_id']);
            $table->dropIndex(['range_up']);
            $table->dropIndex(['range_down']);
        });

        Schema::table('history_corporate_booking_type_charges', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['shipping_mode_id']);
        });

        Schema::table('history_corporate_cash_handling_charges', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['shipping_mode_id']);
            $table->dropIndex(['range_up']);
            $table->dropIndex(['range_down']);
        });

        Schema::table('history_corporate_discount_charges', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['shipping_mode_id']);
            $table->dropIndex(['to']);
            $table->dropIndex(['from']);
            $table->dropIndex(['added_by']);
        });

        Schema::table('history_corporate_fuel_surcharges', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['shipping_mode_id']);
        });

        Schema::table('history_corporate_insurance_charges', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['shipping_mode_id']);
            $table->dropIndex(['range_up']);
            $table->dropIndex(['range_down']);
        });

        Schema::table('history_corporate_min_chargeable_weights', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['shipping_mode_id']);
            $table->dropIndex(['delivery_type_id']);
        });

        Schema::table('history_corporate_rate_statuses', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['shipping_mode_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['cash_handling_charges']);
            $table->dropIndex(['insurance_charges']);
            $table->dropIndex(['return_charges']);
            $table->dropIndex(['fuel_charges']);
        });

        Schema::table('history_corporate_return_charges', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['shipping_mode_id']);
        });

        Schema::table('history_corporate_weight_charges', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['shipping_mode_id']);
            $table->dropIndex(['delivery_type_id']);
            $table->dropIndex(['range_up']);
            $table->dropIndex(['range_down']);
            $table->dropIndex(['base']);
        });

        Schema::table('history_discount_charges', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['shipping_mode_id']);
            $table->dropIndex(['to']);
            $table->dropIndex(['from']);
            $table->dropIndex(['added_by']);
        });

        Schema::table('history_fuel_surcharges', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['shipping_mode_id']);
        });

        Schema::table('history_insurance_charges', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['shipping_mode_id']);
            $table->dropIndex(['range_up']);
            $table->dropIndex(['range_down']);
        });

        Schema::table('history_international_rates_cash_handling_charges', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['box_id']);
            $table->dropIndex('range_up_index');
            $table->dropIndex('range_down_index');
        });

        Schema::table('history_international_rates_discount_charges', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['box_id']);
            $table->dropIndex(['to']);
            $table->dropIndex(['from']);
        });

        Schema::table('history_international_rates_hubs', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['box_id']);
            $table->dropIndex(['hub_id']);
        });

        Schema::table('history_international_rates_insurance_charges', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['box_id']);
            $table->dropIndex('range_up_index');
            $table->dropIndex('range_down_index');
        });

        Schema::table('history_international_rates_remarks', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['admin_id']);
        });

        Schema::table('history_international_rates_return_charges', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['box_id']);
        });

        Schema::table('history_international_rates_statuses', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['box_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['cash_handling_charges']);
            $table->dropIndex(['insurance_charges']);
            $table->dropIndex(['return_charges']);
            $table->dropIndex(['admin_id']);
        });

        Schema::table('history_international_rates_weight_charges', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['box_id']);
            $table->dropIndex(['range_up']);
            $table->dropIndex(['range_down']);
            $table->dropIndex(['weight_addition']);
            $table->dropIndex(['spkg']);
        });

        Schema::table('history_packaging_charges', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['type_id']);
            $table->dropIndex(['size_id']);
        });

        Schema::table('history_rate_statuses', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['shipping_mode_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['cash_handling_charges']);
            $table->dropIndex(['insurance_charges']);
            $table->dropIndex(['return_charges']);
            $table->dropIndex(['fuel_charges']);
        });

        Schema::table('history_return_charges', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['shipping_mode_id']);
        });

        Schema::table('history_shipper_bank_accounts', function (Blueprint $table) {
            $table->dropIndex(['bank_id']);
            $table->dropIndex(['user_id']);
        });

        Schema::table('history_weight_charges', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['shipping_mode_id']);
            $table->dropIndex(['range_up']);
            $table->dropIndex(['range_down']);
            $table->dropIndex(['weight_addition']);
            $table->dropIndex(['spkg']);
        });

        Schema::table('holidays', function (Blueprint $table) {
            $table->dropIndex(['holiday']);
            $table->dropIndex(['created_by']);
            $table->dropIndex(['updated_by']);
        });

        Schema::table('hub_wise_splits', function (Blueprint $table) {
            $table->dropIndex(['hub_id']);
            $table->dropIndex(['origin']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
        });

        Schema::table('insurance_charges', function (Blueprint $table) {
            $table->dropIndex(['range_up']);
            $table->dropIndex(['range_down']);
        });

        Schema::table('intercept_re_book_requests', function (Blueprint $table) {
            $table->dropIndex(['shipment_id']);
            $table->dropIndex(['consignee_city_id']);
            $table->dropIndex(['shipper_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['updated_by']);
            $table->dropIndex(['updated_by_date']);
            $table->dropIndex(['admin_id']);
        });

        Schema::table('intercept_re_book_request_histories', function (Blueprint $table) {
            $table->dropIndex(['shipment_id']);
            $table->dropIndex(['old_consignee_city_id']);
            $table->dropIndex(['new_consignee_city_id']);
            $table->dropIndex(['shipper_id']);
        });

        Schema::table('international_rates_cash_handling_charges', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['box_id']);
            $table->dropIndex(['range_up']);
            $table->dropIndex(['range_down']);
        });

        Schema::table('international_rates_discount_charges', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['box_id']);
            $table->dropIndex(['to']);
            $table->dropIndex(['from']);
        });

        Schema::table('international_rates_hubs', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['box_id']);
            $table->dropIndex(['hub_id']);
        });

        Schema::table('international_rates_insurance_charges', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['box_id']);
            $table->dropIndex(['range_up']);
            $table->dropIndex(['range_down']);
        });

        Schema::table('international_rates_remarks', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['admin_id']);
        });

        Schema::table('international_rates_return_charges', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['box_id']);
        });

        Schema::table('international_rates_statuses', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['box_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['cash_handling_charges']);
            $table->dropIndex(['insurance_charges']);
            $table->dropIndex(['return_charges']);
            $table->dropIndex(['admin_id']);
        });

        Schema::table('international_rates_weight_charges', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['box_id']);
            $table->dropIndex(['range_up']);
            $table->dropIndex(['range_down']);
            $table->dropIndex(['weight_addition']);
            $table->dropIndex(['spkg']);
        });

        Schema::table('international_shipments', function (Blueprint $table) {
            $table->dropIndex(['postal_code']);
        });

        Schema::table('junction_mappings', function (Blueprint $table) {
            $table->dropIndex(['origin_id']);
            $table->dropIndex(['destination_id']);
            $table->dropIndex(['updated_by']);
        });
    }
}
