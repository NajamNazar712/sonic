<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnSmsChargesToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->tinyInteger('sms_charges_type_id')->nullable()->default(NULL);
            $table->double('sms_charges')->nullable()->default(NULL);
            $table->boolean('sms_charges_status')->default(0);
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
            $table->dropColumn('sms_charges_type_id');
            $table->dropColumn('sms_charges');
            $table->dropColumn('sms_charges_status');
        });
    }
}
