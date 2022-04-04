<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateEmployeeNotificationForStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employee_notification_histories', function (Blueprint $table) {
            $table->tinyInteger('status')->default('0')->index();
            $table->Integer('screen_id')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employee_notification_histories', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('screen_id');
        });
    }
}
