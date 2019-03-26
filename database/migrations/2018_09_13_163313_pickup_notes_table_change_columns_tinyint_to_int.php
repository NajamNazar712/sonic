<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PickupNotesTableChangeColumnsTinyintToInt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pickup_notes', function (Blueprint $table) {
            $table->integer('pickups')->change();
            $table->integer('bookings')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pickup_notes', function (Blueprint $table) {
            $table->tinyInteger('pickups')->change();
            $table->tinyInteger('bookings')->change();
        });
    }
}
