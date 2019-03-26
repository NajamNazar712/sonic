<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateReceivingSheetsTableAddPickupAddressId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('receiving_sheets', function (Blueprint $table) {
            $table->integer('pickup_address_id')->after('user_id');

            $table->index('pickup_address_id');
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
            $table->dropColumn('pickup_address_id');
        });
    }
}
