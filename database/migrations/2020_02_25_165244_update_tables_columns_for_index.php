<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTablesColumnsForIndex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rate_remarks', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('user_id');
            $table->index('admin_id');
        });
        
        Schema::table('delivery_notes', function (Blueprint $table) {
            $table->index('ordering');
        });
        Schema::table('delivery_notes', function (Blueprint $table) {
            $table->index('special_rider');
        });
        Schema::table('delivery_note_shipments', function (Blueprint $table) {
            $table->index('ordering');
        });
        Schema::table('user_bank_infos', function (Blueprint $table) {
            $table->index('default_bank');
        });
        Schema::table('minimum_chargeable_weight_settings', function (Blueprint $table) {
            $table->index('shipping_mode_id');
            $table->index('created_at');
            $table->index('updated_at');
        });
        Schema::table('user_default_bank_durations', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('user_id');
            $table->index('last_default_bank_id');
            $table->index('day');
            
        });
        Schema::table('done_payments', function (Blueprint $table) {
            $table->index('user_bank_info_id');
        });
        Schema::table('station_deposit_notes', function (Blueprint $table) {
            $table->index('adjusted');
        });
        Schema::table('shipment_scanning_journeys', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('shipment_id');
            $table->index('screen_location_id');
            $table->index('user_type');
            $table->index('admin_id');
            $table->index('user_id');
            $table->index('substitute_user_id');
            
        });
        Schema::table('telenor', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('status');
        });

        Schema::table('shipment_scanning_screen_locations', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
        });

        Schema::table('cities', function (Blueprint $table) {
            $table->index('gc_area');
            $table->index('attempt_tat');
        });
        Schema::table('city_histories', function (Blueprint $table) {
            $table->index('gc_area');
            $table->index('attempt_tat');
        });
        Schema::table('debriefings', function (Blueprint $table) {
            
            $table->index('hub');
            $table->index('zone_id');
            $table->index('delivered');
            $table->index('delivery_unsuccessful');
            $table->index('on_hold');
            $table->index('status_not_attempted');
            $table->index('fake_status');
            $table->index('confirmation_pending');
            $table->index('delivery_note_pending');
            $table->index('delivery_tomorrow');
            $table->index('total_1');
            $table->index('total_1_ratio');
            $table->index('total_2');
            $table->index('total_2_ratio');
            $table->index('grand_total');
            $table->index('grand_total_ratio');
            $table->index('created_at');
            $table->index('updated_at');
        });
        Schema::table('sale_person_targets', function (Blueprint $table) {
            $table->index('start_date');
            $table->index('end_date');
            $table->index('sales_person_id');
            $table->index('target_days');
            $table->index('target_week');
            $table->index('average_revenue');
            $table->index('created_at');
            $table->index('updated_at');
        });
        Schema::table('sale_person_numbers', function (Blueprint $table) {
            $table->index('target_shipments');
            $table->index('target_revenue');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rate_remarks', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['admin_id']);
        });
        
        Schema::table('delivery_notes', function (Blueprint $table) {
            $table->dropIndex(['ordering']);
        });
        Schema::table('delivery_notes', function (Blueprint $table) {
            $table->dropIndex(['special_rider']);
        });
        Schema::table('delivery_note_shipments', function (Blueprint $table) {
            $table->dropIndex(['ordering']);
        });
        Schema::table('user_bank_infos', function (Blueprint $table) {
            $table->dropIndex(['default_bank']);
        });
        Schema::table('minimum_chargeable_weight_settings', function (Blueprint $table) {
            $table->dropIndex(['shipping_mode_id']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
        });
        Schema::table('user_default_bank_durations', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['last_default_bank_id']);
            $table->dropIndex(['day']);
            
        });
        Schema::table('done_payments', function (Blueprint $table) {
            $table->dropIndex(['user_bank_info_id']);
        });
        Schema::table('station_deposit_notes', function (Blueprint $table) {
            $table->dropIndex(['adjusted']);
        });
        Schema::table('shipment_scanning_journeys', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['shipment_id']);
            $table->dropIndex(['screen_location_id']);
            $table->dropIndex(['user_type']);
            $table->dropIndex(['admin_id']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['substitute_user_id']);
            
        });
        Schema::table('telenor', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['status']);
        });

        Schema::table('shipment_scanning_screen_locations', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
        });

        Schema::table('cities', function (Blueprint $table) {
            $table->dropIndex(['gc_area']);
            $table->dropIndex(['attempt_tat']);
        });
        Schema::table('city_histories', function (Blueprint $table) {
            $table->dropIndex(['gc_area']);
            $table->dropIndex(['attempt_tat']);
        });
        Schema::table('debriefings', function (Blueprint $table) {
            
            $table->dropIndex(['hub']);
            $table->dropIndex(['zone_id']);
            $table->dropIndex(['delivered']);
            $table->dropIndex(['delivery_unsuccessful']);
            $table->dropIndex(['on_hold']);
            $table->dropIndex(['status_not_attempted']);
            $table->dropIndex(['fake_status']);
            $table->dropIndex(['confirmation_pending']);
            $table->dropIndex(['delivery_note_pending']);
            $table->dropIndex(['delivery_tomorrow']);
            $table->dropIndex(['total_1']);
            $table->dropIndex(['total_1_ratio']);
            $table->dropIndex(['total_2']);
            $table->dropIndex(['total_2_ratio']);
            $table->dropIndex(['grand_total']);
            $table->dropIndex(['grand_total_ratio']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
        });
        Schema::table('sale_person_targets', function (Blueprint $table) {
            $table->dropIndex(['start_date']);
            $table->dropIndex(['end_date']);
            $table->dropIndex(['sales_person_id']);
            $table->dropIndex(['target_days']);
            $table->dropIndex(['target_week']);
            $table->dropIndex(['average_revenue']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
        });
        Schema::table('sale_person_numbers', function (Blueprint $table) {
            $table->dropIndex(['target_shipments']);
            $table->dropIndex(['target_revenue']);
        });
    }
}
