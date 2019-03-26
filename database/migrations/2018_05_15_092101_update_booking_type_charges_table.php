<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateBookingTypeChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('booking_type_charges', function (Blueprint $table) {
            $table->float('replacement_charges')->change();
            $table->float('try_and_buy_charges')->change();
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
            $table->decimal('replacement_charges');
            $table->decimal('try_and_buy_charges');
        });
    }
}
