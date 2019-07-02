<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class IndexCrmColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('crm_comments', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('crm_request_id');
            $table->index('comment_by_id');
            $table->index('comment_by');
            $table->index('comment_type');
        });

        Schema::table('crm_request_agent_histories', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('crm_request_id');
            $table->index('agent_id');
        });

        Schema::table('crm_request_case_nature_and_type_histories', function (Blueprint $table) {
            $table->index('created_at', 'created_at_index');
            $table->index('updated_at', 'updated_at_index');
            $table->index('crm_request_id', 'crm_request_id_index');
            $table->index('case_nature_id', 'case_nature_id_index');
            $table->index('case_nature_type_id', 'case_nature_type_id_index');
            $table->index('edited_by', 'edited_by_index');
        });

        Schema::table('crm_request_case_nature_types', function (Blueprint $table) {
            $table->index('nature_id');
        });

        Schema::table('crm_request_status_histories', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('crm_request_id');
            $table->index('status_id');
            $table->index('agent_id');
        });

        Schema::table('crm_request_tagging_histories', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('crm_request_id');
            $table->index('crm_request_tagging_type_id');
            $table->index('tagged_id');
            $table->index('agent_id');
        });

        Schema::table('crm_request_taggings', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('crm_request_id');
            $table->index('crm_request_tagging_type_id');
            $table->index('tagged_id');
        });

        Schema::table('crm_requests', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('case_nature_id');
            $table->index('case_nature_type_id');
            $table->index('channel_id');
            $table->index('status_id');
            $table->index('launched_by_id');
            $table->index('launched_by');
            $table->index('shipment_id');
            $table->index('shipper_id');
            $table->index('agent_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('crm_comments', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['comment_request_id']);
            $table->dropIndex(['comment_by_id']);
            $table->dropIndex(['comment_by']);
            $table->dropIndex(['comment_type']);
        });

        Schema::table('crm_request_agent_histories', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['crm_request_id']);
            $table->dropIndex(['agent_id']);
        });

        Schema::table('crm_request_case_nature_and_type_histories', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['crm_request_id']);
            $table->dropIndex(['case_nature_id']);
            $table->dropIndex(['case_nature_type_id']);
            $table->dropIndex(['edited_by']);
        });

        Schema::table('crm_request_case_nature_types', function (Blueprint $table) {
            $table->dropIndex(['nature_id']);
        });

        Schema::table('crm_request_status_histories', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['crm_request_id']);
            $table->dropIndex(['status_id']);
            $table->dropIndex(['agent_id']);
        });

        Schema::table('crm_request_tagging_histories', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['crm_request_id']);
            $table->dropIndex(['crm_request_tagging_type_id']);
            $table->dropIndex(['tagged_id']);
            $table->dropIndex(['agent_id']);
        });

        Schema::table('crm_request_taggings', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['crm_request_id']);
            $table->dropIndex(['crm_request_tagging_type_id']);
            $table->dropIndex(['tagged_id']);
        });

        Schema::table('crm_requests', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['case_nature_id']);
            $table->dropIndex(['case_nature_type_id']);
            $table->dropIndex(['channel_id']);
            $table->dropIndex(['status_id']);
            $table->dropIndex(['launched_by_id']);
            $table->dropIndex(['launched_by']);
            $table->dropIndex(['shipment_id']);
            $table->dropIndex(['shipper_id']);
            $table->dropIndex(['agent_id']);
        });
    }
}
