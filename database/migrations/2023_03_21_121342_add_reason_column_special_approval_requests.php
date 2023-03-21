<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddReasonColumnSpecialApprovalRequests extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('special_approval_requests', function (Blueprint $table) {
            $table->integer('special_request_reason_id')->nullable()->index()->after('updated_at');
            $table->integer('approved_status')->index()->default(1)->after('special_request_reason_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('special_approval_requests', function (Blueprint $table) {
            $table->dropColumn('special_request_reason_id');
        });
    }
}
