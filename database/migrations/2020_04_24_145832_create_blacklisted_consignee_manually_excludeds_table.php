<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBlacklistedConsigneeManuallyExcludedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blacklisted_consignee_manually_excludeds', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('consignee_information_id');
            $table->integer('excluded_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('blacklisted_consignee_manually_excludeds');
    }
}
