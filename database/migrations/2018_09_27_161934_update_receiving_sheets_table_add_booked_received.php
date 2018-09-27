<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateReceivingSheetsTableAddBookedReceived extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('receiving_sheets', function (Blueprint $table) {
            $table->integer('booked')->after('pickup_address_id');
            $table->integer('received')->after('booked');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('receiving_sheets', function (Blueprint $table) {
            $table->dropColumn('booked');
            $table->dropColumn('received');
        });
    }
}
