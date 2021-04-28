<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCorporateDefaultTableForIndexing extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pending_corporate_default_insurance_charges', function (Blueprint $table) {
            $table->index('user_id','ui');
            $table->index('shipping_mode_id','smi');
            $table->index('range_up','ru');
            $table->index('range_down','rd');
        });


        Schema::table('corporate_default_rate_statuses', function (Blueprint $table) {
           
            $table->index('user_id','ui');
            $table->index('shipping_mode_id','smi');
            $table->index('status');
            $table->index('created_at','ca');
            $table->index('updated_at','ua');
        });

        Schema::table('corporate_default_weight_charges', function (Blueprint $table) {
            $table->index('user_id','ui');
            $table->index('shipping_mode_id','smi');
            $table->index('created_at','ca');
            $table->index('updated_at','ua');
            $table->index('range_up','ru');
            $table->index('range_down','rd');
            $table->index('weight_addition','wa');
            $table->index('spkg');

        });
        Schema::table('corporate_default_booking_type_charges', function (Blueprint $table) {
            $table->index('user_id','ui');
            $table->index('shipping_mode_id','smi');
            $table->index('created_at','ca');
            $table->index('updated_at','ua');
        });

        Schema::table('corporate_default_cash_handling_charges', function (Blueprint $table) {
            $table->index('user_id','ui');
            $table->index('shipping_mode_id','smi');
            $table->index('created_at','ca');
            $table->index('updated_at','ua');
            $table->index('range_up','ru');
            $table->index('range_down','rd');
        });

        Schema::table('corporate_default_insurance_charges', function (Blueprint $table) {
            $table->index('user_id','ui');
            $table->index('shipping_mode_id','smi');
            $table->index('created_at','ca');
            $table->index('updated_at','ua');
            $table->index('range_up','ru');
            $table->index('range_down','rd');
        });
        Schema::table('corporate_default_return_charges', function (Blueprint $table) {
            $table->index('user_id','ui');
            $table->index('shipping_mode_id','smi');
            $table->index('created_at','ca');
            $table->index('updated_at','ua');
        });

        Schema::table('corporate_default_fuel_surcharges', function (Blueprint $table) {
            $table->index('user_id','ui');
            $table->index('shipping_mode_id','smi');
            $table->index('created_at','ca');
            $table->index('updated_at','ua');
        });

        Schema::table('corporate_default_discount_charges', function (Blueprint $table) {
            $table->index('user_id','ui');
            $table->index('shipping_mode_id','smi');
            $table->index('created_at','ca');
            $table->index('updated_at','ua');
            $table->index('to');
            $table->index('from');
            $table->index('added_by','ab');
        });

        Schema::table('pending_corporate_default_rate_statuses', function (Blueprint $table) {
            $table->index('user_id','ui');
            $table->index('shipping_mode_id','smi');
            $table->index('status');
            $table->index('cash_handling_charges','chc');
            $table->index('insurance_charges','ic');
            $table->index('return_charges','rc');
            $table->index('packaging_charges','pc');
            $table->index('fuel_charges','fc');
        });

        Schema::table('pending_corporate_default_weight_charges', function (Blueprint $table) {
            $table->index('user_id','ui');
            $table->index('shipping_mode_id','smi');
            $table->index('range_up','ru');
            $table->index('range_down','rd');
            $table->index('weight_addition','wa');
            $table->index('spkg');
        });
        
        Schema::table('pending_corporate_default_booking_type_charges', function (Blueprint $table) {
            $table->index('user_id','ui');
            $table->index('shipping_mode_id','smi');
        });

        Schema::table('pending_corporate_default_cash_handling_charges', function (Blueprint $table) {
            $table->index('user_id','ui');
            $table->index('shipping_mode_id','smi');
            $table->index('range_up','ru');
            $table->index('range_down','rd');
        });


        Schema::table('pending_corporate_default_return_charges', function (Blueprint $table) {
            $table->index('user_id','ui');
            $table->index('shipping_mode_id','smi');
        });

        Schema::table('pending_corporate_default_fuel_surcharges', function (Blueprint $table) {
            $table->index('user_id','ui');
            $table->index('shipping_mode_id','smi');
        });

        Schema::table('pending_corporate_default_discount_charges', function (Blueprint $table) {
            $table->index('user_id','ui');
            $table->index('shipping_mode_id','smi');
            $table->index('to');
            $table->index('from');
            $table->index('added_by','ab');
        });

        Schema::table('corporate_default_history_rate_statuses', function (Blueprint $table) {
            $table->index('user_id','ui');
            $table->index('shipping_mode_id','smi');
            $table->index('status');
            $table->index('cash_handling_charges','chc');
            $table->index('insurance_charges','ic');
            $table->index('return_charges','rc');

        });

        Schema::table('corporate_default_history_weight_charges', function (Blueprint $table) {
            $table->index('user_id','ui');
            $table->index('shipping_mode_id','smi');
            $table->index('range_up','ru');
            $table->index('range_down','rd');
            $table->index('weight_addition','wa');
            $table->index('spkg');

        });

        Schema::table('corporate_default_history_booking_type_charges', function (Blueprint $table) {
            $table->index('user_id','ui');
            $table->index('shipping_mode_id','smi');
        });

        Schema::table('corporate_default_history_cash_handling_charges', function (Blueprint $table) {
            $table->index('user_id','ui');
            $table->index('shipping_mode_id','smi');
            $table->index('range_up','ru');
            $table->index('range_down','rd');
        });

        Schema::table('corporate_default_history_insurance_charges', function (Blueprint $table) {
            $table->index('user_id','ui');
            $table->index('shipping_mode_id','smi');
            $table->index('range_up','ru');
            $table->index('range_down','rd');
        });

        Schema::table('corporate_default_history_return_charges', function (Blueprint $table) {
            $table->index('user_id','ui');
            $table->index('shipping_mode_id','smi');
        });

        Schema::table('corporate_default_history_fuel_surcharges', function (Blueprint $table) {
            $table->index('user_id','ui');
            $table->index('shipping_mode_id','smi');
        });

        Schema::table('corporate_default_history_discount_charges', function (Blueprint $table) {
            $table->index('user_id','ui');
            $table->index('shipping_mode_id','smi');
            $table->index('to');
            $table->index('from');
            $table->index('added_by','ab');
        });

        Schema::table('corporate_default_rate_histories', function (Blueprint $table) {

            $table->index('user_id','ui');
            $table->index('updated_by','ub');
            $table->index('approved_by','ab');
            $table->index('from_date','fd');
            $table->index('to_date','td');
        });

        Schema::table('corporate_rate_type_histories', function (Blueprint $table) {
            $table->index('user_id','ui');
            $table->index('corporate_rate_type_id','crti');
            $table->index('admin_id','ai');
        });

        Schema::table('history_corporate_delivery_type_statuses', function (Blueprint $table) {
            $table->index('user_id','ui');
            $table->index('status');
        });
        
        Schema::table('history_rate_remarks', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('admin_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pending_corporate_default_insurance_charges', function (Blueprint $table) {
            $table->dropIndex('ui');
            $table->dropIndex('smi');
            $table->dropIndex('ru');
            $table->dropIndex('rd');

        });


        Schema::table('corporate_default_rate_statuses', function (Blueprint $table) {
            $table->dropIndex('ui');
            $table->dropIndex('smi');
            $table->dropIndex(['status']);
            $table->dropIndex('ca');
            $table->dropIndex('ua');
        });

        Schema::table('corporate_default_weight_charges', function (Blueprint $table) {
            $table->dropIndex('ui');
            $table->dropIndex('smi');
            $table->dropIndex('ca');
            $table->dropIndex('ua');
            $table->dropIndex('ru');
            $table->dropIndex('rd');
            $table->dropIndex('wa');
            $table->dropIndex(['spkg']);

        });
        Schema::table('corporate_default_booking_type_charges', function (Blueprint $table) {
            $table->dropIndex('ui');
            $table->dropIndex('smi');
            $table->dropIndex('ca');
            $table->dropIndex('ua');
        });

        Schema::table('corporate_default_cash_handling_charges', function (Blueprint $table) {
            $table->dropIndex('ui');
            $table->dropIndex('smi');
            $table->dropIndex('ca');
            $table->dropIndex('ua');
            $table->dropIndex('ru');
            $table->dropIndex('rd');
        });

        Schema::table('corporate_default_insurance_charges', function (Blueprint $table) {
            $table->dropIndex('ui');
            $table->dropIndex('smi');
            $table->dropIndex('ca');
            $table->dropIndex('ua');
            $table->dropIndex('ru');
            $table->dropIndex('rd');
        });

        Schema::table('corporate_default_return_charges', function (Blueprint $table) {
            $table->dropIndex('ui');
            $table->dropIndex('smi');
            $table->dropIndex('ca');
            $table->dropIndex('ua');
        });

        Schema::table('corporate_default_fuel_surcharges', function (Blueprint $table) {
            $table->dropIndex('ui');
            $table->dropIndex('smi');
            $table->dropIndex('ca');
            $table->dropIndex('ua');
        });

        Schema::table('corporate_default_discount_charges', function (Blueprint $table) {
            $table->dropIndex('ui');
            $table->dropIndex('smi');
            $table->dropIndex('ca');
            $table->dropIndex('ua');
            $table->dropIndex(['to']);
            $table->dropIndex(['from']);
            $table->dropIndex('ab');
        });

        Schema::table('pending_corporate_default_rate_statuses', function (Blueprint $table) {
            $table->dropIndex('ui');
            $table->dropIndex('smi');
            $table->dropIndex(['status']);
            $table->dropIndex('chc');
            $table->dropIndex('ic');
            $table->dropIndex('rc');
            $table->dropIndex('pc');
            $table->dropIndex('fc');
        });

        Schema::table('pending_corporate_default_weight_charges', function (Blueprint $table) {
            $table->dropIndex('ui');
            $table->dropIndex('smi');
            $table->dropIndex('ru');
            $table->dropIndex('rd');
            $table->dropIndex('wa');
            $table->dropIndex(['spkg']);

        });

        Schema::table('pending_corporate_default_booking_type_charges', function (Blueprint $table) {
            $table->dropIndex('ui');
            $table->dropIndex('smi');

        });

        Schema::table('pending_corporate_default_cash_handling_charges', function (Blueprint $table) {
            $table->dropIndex('ui');
            $table->dropIndex('smi');
            $table->dropIndex('ru');
            $table->dropIndex('rd');
        });


        Schema::table('pending_corporate_default_return_charges', function (Blueprint $table) {
            $table->dropIndex('ui');
            $table->dropIndex('smi');
        });

        Schema::table('pending_corporate_default_fuel_surcharges', function (Blueprint $table) {
            $table->dropIndex('ui');
            $table->dropIndex('smi');
        });

        Schema::table('pending_corporate_default_discount_charges', function (Blueprint $table) {
            $table->dropIndex('ui');
            $table->dropIndex('smi');
            $table->dropIndex(['to']);
            $table->dropIndex(['from']);
            $table->dropIndex('ab');
        });

        Schema::table('corporate_default_history_rate_statuses', function (Blueprint $table) {
            $table->dropIndex('ui');
            $table->dropIndex('smi');
            $table->dropIndex('chc');
            $table->dropIndex(['status']);
            $table->dropIndex('ic');
            $table->dropIndex('rc');
        });

        Schema::table('corporate_default_history_weight_charges', function (Blueprint $table) {
            $table->dropIndex('ui');
            $table->dropIndex('smi');
            $table->dropIndex('wa');
            $table->dropIndex('ru');
            $table->dropIndex('rd');
            $table->dropIndex(['spkg']);
        });

        Schema::table('corporate_default_history_booking_type_charges', function (Blueprint $table) {
            $table->dropIndex('ui');
            $table->dropIndex('smi');
        });

        Schema::table('corporate_default_history_cash_handling_charges', function (Blueprint $table) {
            $table->dropIndex('ui');
            $table->dropIndex('smi');
            $table->dropIndex('ru');
            $table->dropIndex('rd');
        });

        Schema::table('corporate_default_history_insurance_charges', function (Blueprint $table) {
            $table->dropIndex('ui');
            $table->dropIndex('smi');
            $table->dropIndex('ru');
            $table->dropIndex('rd');
        });

        Schema::table('corporate_default_history_return_charges', function (Blueprint $table) {
            $table->dropIndex('ui');
            $table->dropIndex('smi');
        });

        Schema::table('corporate_default_history_fuel_surcharges', function (Blueprint $table) {
            $table->dropIndex('ui');
            $table->dropIndex('smi');
        });

        Schema::table('corporate_default_history_discount_charges', function (Blueprint $table) {
            $table->dropIndex('ui');
            $table->dropIndex('smi');
            $table->dropIndex(['to']);
            $table->dropIndex(['from']);
            $table->dropIndex('ab');
        });

        Schema::table('corporate_default_rate_histories', function (Blueprint $table) {
            $table->dropIndex('ui');
            $table->dropIndex('ub');
            $table->dropIndex('ab');
            $table->dropIndex('fd');
            $table->dropIndex('td');
        });

        Schema::table('corporate_rate_type_histories', function (Blueprint $table) {
            $table->dropIndex('ui');
            $table->dropIndex('ai');
            $table->dropIndex('crti');
        });

        Schema::table('history_corporate_delivery_type_statuses', function (Blueprint $table) {
            $table->dropIndex('ui');
            $table->dropIndex(['status']);
        });
        Schema::table('history_rate_remarks', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['admin_id']);
        });
    }
}
