<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsEmailToTraxLogisticBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trax_logistic_bookings', function (Blueprint $table) {
            $table->tinyInteger('is_email')->default(0)->after('consignee_email');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trax_logistic_bookings', function (Blueprint $table) {
           $table->dropColumn('is_email');
        });
    }
}