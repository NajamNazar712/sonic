<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateRiderDeliveriesForCnicAndHouseImages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rider_deliveries', function (Blueprint $table) {
            $table->string('cnic_image')->nullable();
            $table->string('house_image')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rider_deliveries', function (Blueprint $table) {
            $table->dropColumn('cnic_image');
            $table->dropColumn('house_image');
        });
    }
}
