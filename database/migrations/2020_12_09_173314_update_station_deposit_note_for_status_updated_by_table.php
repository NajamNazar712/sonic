<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateStationDepositNoteForStatusUpdatedByTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('station_deposit_notes', function (Blueprint $table) {
            $table->timestamp('status_updated_at')->nullable();
            $table->integer('status_updated_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('station_deposit_notes', function (Blueprint $table) {
            $table->dropColumn('status_updated_at');
            $table->dropColumn('status_updated_by');
        });
    }
}
