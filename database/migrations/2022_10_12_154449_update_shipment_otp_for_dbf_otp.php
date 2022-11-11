<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateShipmentOtpForDbfOtp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shipment_otps', function (Blueprint $table) {
            $table->integer('otp')->nullable()->change();
            $table->integer('dbf_otp')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shipment_otps', function (Blueprint $table) {
            $table->integer('otp')->nullable(false)->change();
            $table->dropColumn('dbf_otp');
        });
    }
}
