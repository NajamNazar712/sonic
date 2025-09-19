<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shipment_otp_verifications', function (Blueprint $table) {
            //
            $table->unsignedSmallInteger('via_rvrsub_reason')->nullable()->after('via_dbf_otp');
            $table->integer('rider_id')->nullable()->after('via_rvrsub_reason');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shipment_otp_verifications', function (Blueprint $table) {
            $table->dropColumn('via_rvrsub_reason');
            $table->dropColumn('rider_id');
        });
    }
};
