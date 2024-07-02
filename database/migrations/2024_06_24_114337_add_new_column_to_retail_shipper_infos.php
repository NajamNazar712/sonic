<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewColumnToRetailShipperInfos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('retail_shipper_infos', function (Blueprint $table) {
            $table->string('api_token')->nullable()->before('pin');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('retail_shipper_infos', function (Blueprint $table) {
            $table->dropColumn('api_token');
        });
    }
}
