<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateConsigneeInfoForPinAndApiToken extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('consignee_infos', function (Blueprint $table) {
            $table->string('pin')->nullable();
            $table->string('api_token')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('consignee_infos', function (Blueprint $table) {
            $table->dropColumn('pin');
            $table->dropColumn('api_token');
        });
    }
}
