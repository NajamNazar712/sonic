<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumReverseToCorporateBookingTypeChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('corporate_booking_type_charges', function (Blueprint $table) {
            $table->decimal('reverse_pickup_charges')->default(0);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('corporate_booking_type_charges', function (Blueprint $table) {
            $table->dropColumn('reverse_pickup_charges');
        });
    }
}
