<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        //check
        Schema::table('one_link_transactions', function (Blueprint $table) {
            $table->integer('shipment_id')->nullable()->index();
            $table->dateTime('expiry_time')->nullable();
            $table->integer('log_id')->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('one_link_transactions', function (Blueprint $table) {
            $table->dropColumn('shipment_id');
            $table->dropColumn('expiry_time');
            $table->dropColumn('log_id');
        });
    }
};
