<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateRiderDeliveriesForRefusedOtp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rider_deliveries', function (Blueprint $table) {
            $table->integer('otp_entered')->nullable();
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
            $table->dropColumn('otp_entered');
        });
    }
}
