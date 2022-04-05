<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateSpecialApprovalRequestforAdjustedPercentage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('special_approval_requests', function (Blueprint $table) {
            $table->float('adjusted_percentage')->nullable();
            $table->date('approved_date')->nullable();
            $table->integer('requested_by')->nullable()->index();
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
            $table->dropColumn('adjusted_percentage');
            $table->dropColumn('approved_date');
            $table->dropColumn('requested_by');
        });
    }
}
