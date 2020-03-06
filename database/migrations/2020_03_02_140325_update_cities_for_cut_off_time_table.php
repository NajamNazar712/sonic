<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCitiesForCutOffTimeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->string('cut_off_time')->default('11:00 AM')->nullable();
            $table->timestamp('cut_off_time_updated_at')->nullable();
            $table->integer('cut_off_time_updated_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->dropColumn('cut_off_time');
            $table->dropColumn('cut_off_time_updated_at');
            $table->dropColumn('cut_off_time_updated_by');
        });
    }
}
