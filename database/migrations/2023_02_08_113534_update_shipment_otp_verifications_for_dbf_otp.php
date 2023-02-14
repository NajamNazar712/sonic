<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateShipmentOtpVerificationsForDbfOtp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shipment_otp_verifications', function (Blueprint $table) {
            $table->index('via_dbf_otp');
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
            $table->dropIndex('via_dbf_otp');
        });
    }
}
