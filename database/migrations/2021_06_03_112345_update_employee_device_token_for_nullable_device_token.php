<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateEmployeeDeviceTokenForNullableDeviceToken extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employee_device_tokens', function (Blueprint $table) {
            $table->string('device_token', 500)->nullabe()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employee_device_tokens', function (Blueprint $table) {
            $table->string('device_token', 500)->change();
        });
    }
}
