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
        Schema::table('booking_api_logs', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('shipment_id')->nullable()->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('booking_api_logs', function (Blueprint $table) {
            //
            $table->dropColumn('shipment_id');
        });
    }
};
