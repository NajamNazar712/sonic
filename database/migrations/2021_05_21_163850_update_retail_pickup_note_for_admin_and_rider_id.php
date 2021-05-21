<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateRetailPickupNoteForAdminAndRiderId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('retail_pickup_notes', function (Blueprint $table) {
            $table->integer('retail_user_id')->nullable()->change();
            $table->integer('admin_id')->index()->nullable();
            $table->integer('rider_booking_id')->index()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('retail_pickup_notes', function (Blueprint $table) {
            $table->integer('retail_user_id')->change();
            $table->dropColumn('admin_id');
            $table->dropColumn('rider_booking_id');
        });
    }
}
