<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateReceivingSheetShipments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('receiving_sheet_shipments', function (Blueprint $table) {
            $table->tinyinteger('status')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('receiving_sheet_shipments', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
