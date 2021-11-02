<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateBookingTypeChargesForDwsSystem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('booking_type_charges', function (Blueprint $table) {
            $table->tinyInteger('dws_weight_type')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('booking_type_charges', function (Blueprint $table) {
            $table->dropColumn('dws_weight_type');

        });
    }
}
