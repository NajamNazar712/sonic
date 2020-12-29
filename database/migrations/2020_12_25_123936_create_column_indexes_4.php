<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateColumnIndexes4 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('crf_terms_conditions', function (Blueprint $table) {
            $table->index('user_id');
        });

        Schema::table('crm_comments', function (Blueprint $table) {
            $table->index('comment_updated_by');
            $table->index('comment_updated_at');
        });

        Schema::table('crm_consignee_info_prints', function (Blueprint $table) {
            $table->index('crm_request_id');
            $table->index('shipment_id');
        });

        Schema::table('crm_escalations', function (Blueprint $table) {
            $table->index('crm_request_status');
            $table->index('case_nature');
            $table->index('case_nature_type');
            $table->index('tat');
            $table->index('mark_as');
            $table->index('updated_by');
            $table->index('status');
        });

        Schema::table('crm_escalation_shipment_statuses', function (Blueprint $table) {
            $table->index('escalation_id');
            $table->index('shipment_status_id');
        });

        Schema::table('crm_escalation_taggings', function (Blueprint $table) {
            $table->index('case_nature');
            $table->index('case_nature_type');
            $table->index('hub_status');
            $table->index('updated_by');
            $table->index('status');
        });

        Schema::table('crm_escalation_tagging_hubs', function (Blueprint $table) {
            $table->index('escalation_tagging_id');
            $table->index('hub_id');
        });

        Schema::table('crm_escalation_tagging_levels', function (Blueprint $table) {
            $table->index('escalation_tagging_id');
            $table->index('level_id');
            $table->index('tat');
        });

        Schema::table('crm_escalation_tagging_level_emails', function (Blueprint $table) {
            $table->index('escalation_tagging_id');
            $table->index('tagging_level_id');
            $table->index('status');
        });

        Schema::table('crm_escalation_tagging_level_roles', function (Blueprint $table) {
            $table->index('escalation_tagging_id');
            $table->index('tagging_level_id');
            $table->index('role_id');
        });

        Schema::table('crm_escalation_tagging_shipment_statuses', function (Blueprint $table) {
            $table->index(['escalation_tagging_id'], 'escalation_tagging_id_index');
            $table->index(['shipment_status_id'], 'shipment_status_id_index');
        });

        Schema::table('crm_payment_shipments', function (Blueprint $table) {
            $table->index('crm_request_id');
            $table->index('shipment_id');
        });

        Schema::table('crm_request_agent_histories', function (Blueprint $table) {
            $table->index('assigned_by');
        });

        Schema::table('crm_request_case_nature_types', function (Blueprint $table) {
            $table->index('updated_by');
        });

        Schema::table('crm_request_escalation_logs', function (Blueprint $table) {
            $table->index('crm_request_id');
            $table->index('escalation_tagging_id');
            $table->index('tagging_level_id');
            $table->index('level_id');
        });

        Schema::table('crm_request_escalation_statuses', function (Blueprint $table) {
            $table->index('crm_request_id');
            $table->index('status');
        });

        Schema::table('crm_request_escalation_taggings', function (Blueprint $table) {
            $table->index('crm_request_id');
            $table->index('role_id');
            $table->index('hub_id');
        });

        Schema::table('crm_request_images', function (Blueprint $table) {
            $table->index('crm_request_id');
            $table->index('added_by');
        });

        Schema::table('crm_request_taggings', function (Blueprint $table) {
            $table->index('hub_id');
        });

        Schema::table('crm_request_tagging_histories', function (Blueprint $table) {
            $table->index('hub_id');
        });

        Schema::table('crm_settings', function (Blueprint $table) {
            $table->index('name');
        });

        Schema::table('crm_tat_holidays', function (Blueprint $table) {
            $table->index('holiday');
            $table->index('created_by');
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
        Schema::table('crf_terms_conditions', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
        });

        Schema::table('crm_comments', function (Blueprint $table) {
            $table->dropIndex(['comment_updated_by']);
            $table->dropIndex(['comment_updated_at']);
        });

        Schema::table('crm_consignee_info_prints', function (Blueprint $table) {
            $table->dropIndex(['crm_request_id']);
            $table->dropIndex(['shipment_id']);
        });

        Schema::table('crm_escalations', function (Blueprint $table) {
            $table->dropIndex(['crm_request_status']);
            $table->dropIndex(['case_nature']);
            $table->dropIndex(['case_nature_type']);
            $table->dropIndex(['tat']);
            $table->dropIndex(['mark_as']);
            $table->dropIndex(['updated_by']);
            $table->dropIndex(['status']);
        });

        Schema::table('crm_escalation_shipment_statuses', function (Blueprint $table) {
            $table->dropIndex(['escalation_id']);
            $table->dropIndex(['shipment_status_id']);
        });

        Schema::table('crm_escalation_taggings', function (Blueprint $table) {
            $table->dropIndex(['case_nature']);
            $table->dropIndex(['case_nature_type']);
            $table->dropIndex(['hub_status']);
            $table->dropIndex(['updated_by']);
            $table->dropIndex(['status']);
        });

        Schema::table('crm_escalation_tagging_hubs', function (Blueprint $table) {
            $table->dropIndex(['escalation_tagging_id']);
            $table->dropIndex(['hub_id']);
        });

        Schema::table('crm_escalation_tagging_levels', function (Blueprint $table) {
            $table->dropIndex(['escalation_tagging_id']);
            $table->dropIndex(['level_id']);
            $table->dropIndex(['tat']);
        });

        Schema::table('crm_escalation_tagging_level_emails', function (Blueprint $table) {
            $table->dropIndex(['escalation_tagging_id']);
            $table->dropIndex(['tagging_level_id']);
            $table->dropIndex(['status']);
        });

        Schema::table('crm_escalation_tagging_level_roles', function (Blueprint $table) {
            $table->dropIndex(['escalation_tagging_id']);
            $table->dropIndex(['tagging_level_id']);
            $table->dropIndex(['role_id']);
        });

        Schema::table('crm_escalation_tagging_shipment_statuses', function (Blueprint $table) {
            $table->dropIndex('escalation_tagging_id_index');
            $table->dropIndex('shipment_status_id_index');
        });

        Schema::table('crm_payment_shipments', function (Blueprint $table) {
            $table->dropIndex(['crm_request_id']);
            $table->dropIndex(['shipment_id']);
        });

        Schema::table('crm_request_agent_histories', function (Blueprint $table) {
            $table->dropIndex(['assigned_by']);
        });

        Schema::table('crm_request_case_nature_types', function (Blueprint $table) {
            $table->dropIndex(['updated_by']);
        });

        Schema::table('crm_request_escalation_logs', function (Blueprint $table) {
            $table->dropIndex(['crm_request_id']);
            $table->dropIndex(['escalation_tagging_id']);
            $table->dropIndex(['tagging_level_id']);
            $table->dropIndex(['level_id']);
        });

        Schema::table('crm_request_escalation_statuses', function (Blueprint $table) {
            $table->dropIndex(['crm_request_id']);
            $table->dropIndex(['status']);
        });

        Schema::table('crm_request_escalation_taggings', function (Blueprint $table) {
            $table->dropIndex(['crm_request_id']);
            $table->dropIndex(['role_id']);
            $table->dropIndex(['hub_id']);
        });

        Schema::table('crm_request_images', function (Blueprint $table) {
            $table->dropIndex(['crm_request_id']);
            $table->dropIndex(['added_by']);
        });

        Schema::table('crm_request_taggings', function (Blueprint $table) {
            $table->dropIndex(['hub_id']);
        });

        Schema::table('crm_request_tagging_histories', function (Blueprint $table) {
            $table->dropIndex(['hub_id']);
        });

        Schema::table('crm_settings', function (Blueprint $table) {
            $table->dropIndex(['name']);
        });

        Schema::table('crm_tat_holidays', function (Blueprint $table) {
            $table->dropIndex(['holiday']);
            $table->dropIndex(['created_by']);
            $table->dropIndex(['updated_by']);
        });
    }
}
