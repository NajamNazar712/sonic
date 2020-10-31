<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateInternationRateStatusesForAdminIdTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('international_rates_statuses', function (Blueprint $table) {
            $table->integer('admin_id');
        });
        Schema::table('international_users_informations', function (Blueprint $table) {
            $table->string('rejected_reason')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('international_rates_statuses', function (Blueprint $table) {
            $table->dropColumn('admin_id');
        });
        Schema::table('international_users_informations', function (Blueprint $table) {
            $table->dropColumn('rejected_reason');
        });
    }
}
