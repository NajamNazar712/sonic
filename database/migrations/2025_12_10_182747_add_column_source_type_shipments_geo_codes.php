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
        Schema::table('shipments_geo_codes', function (Blueprint $table) {
           $table->integer('source_type_tpl')->default(0)->comment('0=Manual Api Hit,1=At the of Booking Auto,2= Already Exist Lat & Lng');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shipments_geo_codes', function (Blueprint $table) {
            $table->dropColumn('source_type_tpl');
        });
    }
};
