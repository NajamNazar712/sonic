<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PickupRequestsTableChangeColumnsTinyintToInt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pickup_requests', function (Blueprint $table) {
            $table->integer('bookings')->change();
            $table->integer('pending_bookings')->change();
            $table->integer('received')->change();
            $table->integer('short_received')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pickup_requests', function (Blueprint $table) {
            $table->tinyInteger('bookings')->change();
            $table->tinyInteger('pending_bookings')->change();
            $table->tinyInteger('received')->change();
            $table->tinyInteger('short_received')->change();
        });
    }
}
