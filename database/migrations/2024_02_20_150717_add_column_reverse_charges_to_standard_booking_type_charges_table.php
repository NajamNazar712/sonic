<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnReverseChargesToStandardBookingTypeChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('standard_booking_type_charges', function (Blueprint $table) {
            $table->double('reverse_pickup_charges');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('standard_booking_type_charges', function (Blueprint $table) {
            $table->dropColumn('reverse_pickup_charges');
        });
    }
}
