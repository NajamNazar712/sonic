<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTokensIndexing extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->index('phone_number');
            $table->index('api_token');
            $table->index('default_hub_id');
            $table->index('trax_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index('api_token');
            $table->index('phone');
            $table->index('phone2');
            $table->index('default_shipping_mode');
        });

        Schema::table('riders', function (Blueprint $table) {
            $table->index('api_token');
            $table->index('pin');
            $table->index('dummy_pin');
            $table->index('phone');
            $table->index('trax_id');
            $table->index('ccd');
        });

        Schema::table('retail_users', function (Blueprint $table) {
            $table->index('api_token');
            $table->index('phone_no');
            $table->index('trax_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
}
