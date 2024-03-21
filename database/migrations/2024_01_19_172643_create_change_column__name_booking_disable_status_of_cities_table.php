<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChangeColumnNameBookingDisableStatusOfCitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->renameColumn('booking_disable_status', 'booking_enable_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('your_table_name', function (Blueprint $table) {
            $table->renameColumn('booking_disable_status', 'booking_enable_status');
        });    }
}
