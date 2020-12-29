<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateColumnIndexes3 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('corporate_booking_type_charges', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('shipping_mode_id');
        });

        Schema::table('corporate_cash_handling_charges', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('shipping_mode_id');
            $table->index('range_up');
            $table->index('range_down');
        });

        Schema::table('corporate_delivery_type_statuses', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('delivery_type_id');
            $table->index('shipping_mode_id');
            $table->index('status');
        });

        Schema::table('corporate_discount_charges', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('shipping_mode_id');
            $table->index('to');
            $table->index('from');
            $table->index('added_by');
        });

        Schema::table('corporate_fuel_surcharges', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('shipping_mode_id');
        });

        Schema::table('corporate_insurance_charges', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('shipping_mode_id');
            $table->index('range_up');
            $table->index('range_down');
        });

        Schema::table('corporate_min_chargeable_weights', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('shipping_mode_id');
            $table->index('delivery_type_id');
        });

        Schema::table('corporate_rate_histories', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('updated_by');
            $table->index('approved_by');
            $table->index('from_date');
            $table->index('to_date');
        });

        Schema::table('corporate_rate_statuses', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('shipping_mode_id');
            $table->index('status');
            $table->index('cash_handling_charges');
            $table->index('insurance_charges');
            $table->index('return_charges');
            $table->index('fuel_charges');
        });

        Schema::table('corporate_return_charges', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('shipping_mode_id');
        });

        Schema::table('corporate_standard_booking_type_charges', function (Blueprint $table) {
            $table->index('shipping_mode_id');
        });

        Schema::table('corporate_standard_cash_handling_charges', function (Blueprint $table) {
            $table->index('shipping_mode_id');
            $table->index('range_up');
            $table->index('range_down');
        });

        Schema::table('corporate_standard_fuel_surcharges', function (Blueprint $table) {
            $table->index('shipping_mode_id');
        });

        Schema::table('corporate_standard_insurance_charges', function (Blueprint $table) {
            $table->index('shipping_mode_id');
            $table->index('range_up');
            $table->index('range_down');
        });

        Schema::table('corporate_standard_min_chargeable_weights', function (Blueprint $table) {
            $table->index('shipping_mode_id');
            $table->index('delivery_type_id');
        });

        Schema::table('corporate_standard_return_charges', function (Blueprint $table) {
            $table->index('shipping_mode_id');
        });

        Schema::table('corporate_standard_weight_charges', function (Blueprint $table) {
            $table->index('shipping_mode_id');
            $table->index('delivery_type_id');
            $table->index('range_up');
            $table->index('range_down');
        });

        Schema::table('corporate_weight_charges', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('shipping_mode_id');
            $table->index('delivery_type_id');
            $table->index('range_up');
            $table->index('range_down');
            $table->index('base');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('corporate_booking_type_charges', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['shipping_mode_id']);
        });

        Schema::table('corporate_cash_handling_charges', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['shipping_mode_id']);
            $table->dropIndex(['range_up']);
            $table->dropIndex(['range_down']);
        });

        Schema::table('corporate_delivery_type_statuses', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['delivery_type_id']);
            $table->dropIndex(['shipping_mode_id']);
            $table->dropIndex(['status']);
        });

        Schema::table('corporate_discount_charges', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['shipping_mode_id']);
            $table->dropIndex(['to']);
            $table->dropIndex(['from']);
            $table->dropIndex(['added_by']);
        });

        Schema::table('corporate_fuel_surcharges', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['shipping_mode_id']);
        });

        Schema::table('corporate_insurance_charges', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['shipping_mode_id']);
            $table->dropIndex(['range_up']);
            $table->dropIndex(['range_down']);
        });

        Schema::table('corporate_min_chargeable_weights', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['shipping_mode_id']);
            $table->dropIndex(['delivery_type_id']);
        });

        Schema::table('corporate_rate_histories', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['updated_by']);
            $table->dropIndex(['approved_by']);
            $table->dropIndex(['from_date']);
            $table->dropIndex(['to_date']);
        });

        Schema::table('corporate_rate_statuses', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['shipping_mode_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['cash_handling_charges']);
            $table->dropIndex(['insurance_charges']);
            $table->dropIndex(['return_charges']);
            $table->dropIndex(['fuel_charges']);
        });

        Schema::table('corporate_return_charges', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['shipping_mode_id']);
        });

        Schema::table('corporate_standard_booking_type_charges', function (Blueprint $table) {
            $table->dropIndex(['shipping_mode_id']);
        });

        Schema::table('corporate_standard_cash_handling_charges', function (Blueprint $table) {
            $table->dropIndex(['shipping_mode_id']);
            $table->dropIndex(['range_up']);
            $table->dropIndex(['range_down']);
        });

        Schema::table('corporate_standard_fuel_surcharges', function (Blueprint $table) {
            $table->dropIndex(['shipping_mode_id']);
        });

        Schema::table('corporate_standard_insurance_charges', function (Blueprint $table) {
            $table->dropIndex(['shipping_mode_id']);
            $table->dropIndex(['range_up']);
            $table->dropIndex(['range_down']);
        });

        Schema::table('corporate_standard_min_chargeable_weights', function (Blueprint $table) {
            $table->dropIndex(['shipping_mode_id']);
            $table->dropIndex(['delivery_type_id']);
        });

        Schema::table('corporate_standard_return_charges', function (Blueprint $table) {
            $table->dropIndex(['shipping_mode_id']);
        });

        Schema::table('corporate_standard_weight_charges', function (Blueprint $table) {
            $table->dropIndex(['shipping_mode_id']);
            $table->dropIndex(['delivery_type_id']);
            $table->dropIndex(['range_up']);
            $table->dropIndex(['range_down']);
        });

        Schema::table('corporate_weight_charges', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['shipping_mode_id']);
            $table->dropIndex(['delivery_type_id']);
            $table->dropIndex(['range_up']);
            $table->dropIndex(['range_down']);
            $table->dropIndex(['base']);
        });
    }
}
