<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateRiderDeliveriesForOtpEntered extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rider_deliveries', function (Blueprint $table) {
            $table->index('otp_entered');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rider_deliveries', function (Blueprint $table) {
            $table->dropIndex('otp_entered');
        });
    }
}
