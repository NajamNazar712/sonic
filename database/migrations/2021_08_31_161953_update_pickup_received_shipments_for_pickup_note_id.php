<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePickupReceivedShipmentsForPickupNoteId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('v2_pickup_received_shipments', function (Blueprint $table) {
            $table->integer('pickup_note_id')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('v2_pickup_received_shipments', function (Blueprint $table) {
            $table->dropColumn('pickup_note_id');
        });
    }
}
