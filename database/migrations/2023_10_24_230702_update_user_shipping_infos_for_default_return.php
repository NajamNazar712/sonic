<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUserShippingInfosForDefaultReturn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_shipping_infos', function (Blueprint $table) {
            $table->tinyInteger('default_return_address')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_shipping_infos', function (Blueprint $table) {
            $table->dropColumn('default_return_address');
        });
    }
}
