<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateDeliveryNotesForSpecialRiders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('delivery_notes', function (Blueprint $table) {
            $table->string('special_rider_name')->nullable();
            $table->string('special_rider_phone')->nullable();
            $table->boolean('special_rider')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('delivery_notes', function (Blueprint $table) {
            $table->dropColumn('special_rider_name');
            $table->dropColumn('special_rider_phone');
            $table->dropColumn('special_rider');
        });
    }
}
