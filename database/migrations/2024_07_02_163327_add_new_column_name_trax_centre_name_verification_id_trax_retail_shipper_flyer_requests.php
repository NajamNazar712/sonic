<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewColumnNameTraxCentreNameVerificationIdTraxRetailShipperFlyerRequests extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trax_retail_shipper_flyer_requests', function (Blueprint $table) {
            $table->string('trax_centre_name_verification_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trax_retail_shipper_flyer_requests', function (Blueprint $table) {
            $table->dropColumn('trax_centre_name_verification_id');
        });
    }
}
