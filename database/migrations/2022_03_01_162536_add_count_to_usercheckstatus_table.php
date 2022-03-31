<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCountToUsercheckstatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_check_statuses', function (Blueprint $table) {
            $table->bigInteger('status_count');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_check_statuses', function (Blueprint $table) {
            $table->dropColumn('status_count');
        });
    }
}
