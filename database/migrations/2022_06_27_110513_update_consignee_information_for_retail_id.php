<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateConsigneeInformationForRetailId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('consignee_informations', function (Blueprint $table) {
            $table->integer('retail_consignee')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('consignee_informations', function (Blueprint $table) {
            $table->dropColumn('retail_consignee');
        });
    }
}
