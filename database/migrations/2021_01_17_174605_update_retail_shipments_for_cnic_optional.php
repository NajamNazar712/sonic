<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateRetailShipmentsForCnicOptional extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('retail_shipper_infos', function (Blueprint $table) {
            $table->string('shipper_cnic')->nullable()->change();
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
            $table->string('shipper_cnic')->change();
        });
    }
}
