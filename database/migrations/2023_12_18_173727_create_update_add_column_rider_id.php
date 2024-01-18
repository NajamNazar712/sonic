<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUpdateAddColumnRiderId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shipment_scanning_journey_area_logs', function (Blueprint $table) {
            $table->integer('rider_id')->after('admin_id')->nullable()->index();
        });    
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shipment_scanning_journey_area_logs', function (Blueprint $table) {
            $table->dropColumn('rider_id');

        });    
    }
}
