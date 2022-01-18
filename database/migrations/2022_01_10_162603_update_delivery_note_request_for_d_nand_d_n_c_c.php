<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateDeliveryNoteRequestForDNandDNCC extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('delivery_note_requests', function (Blueprint $table) {
            $table->integer('delivery_note')->default(0)->index();
            $table->renameColumn('delivery_note_id', 'dn_received_amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('delivery_note_requests', function (Blueprint $table) {
            $table->dropColumn('delivery_note');
            $table->renameColumn('dn_received_amount','delivery_note_id');
        });
    }
}
