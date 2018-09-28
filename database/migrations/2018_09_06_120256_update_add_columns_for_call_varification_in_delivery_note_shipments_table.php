<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateAddColumnsForCallVarificationInDeliveryNoteShipmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('delivery_note_shipments', function (Blueprint $table) {
            $table->boolean('call_verification')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('delivery_note_shipments', function (Blueprint $table) {
            $table->dropColumn('call_verification');
        });
    }
}
