<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableAddColumnsChargeSmsInNotificationSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notification_settings', function (Blueprint $table) {
            $table->integer('charged_sms_toggle');
            $table->integer('charging_frequency')->nullable();
            $table->integer('sending_frequency')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notification_settings', function (Blueprint $table) {
            $table->dropColumn('charged_sms_toggle');
            $table->dropColumn('charging_frequency');
            $table->dropColumn('sending_frequency');

        });
    }
}
