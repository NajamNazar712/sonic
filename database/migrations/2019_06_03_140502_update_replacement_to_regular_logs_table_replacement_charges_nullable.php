<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateReplacementToRegularLogsTableReplacementChargesNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('replacement_to_regular_logs', function (Blueprint $table) {
            $table->Integer('replacement_charges')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('replacement_to_regular_logs', function (Blueprint $table) {
            $table->integer('replacement_charges')->nullable(FALSE)->change();
        });
    }
}
