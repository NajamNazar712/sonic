<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBlacklistedConsigneeManuallyBlacklistedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blacklisted_consignee_manually_blacklisteds', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('consignee_information_id');
            $table->integer('added_by');
            $table->integer('blacklist_setting_id');
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
        Schema::dropIfExists('blacklisted_consignee_manually_blacklisteds');
    }
}
