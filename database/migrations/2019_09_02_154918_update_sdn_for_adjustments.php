<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateSdnForAdjustments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('station_deposit_notes', function (Blueprint $table) {
            $table->bigInteger('sdn_expense')->nullable()->change();
            $table->renameColumn('sdn_expense', 'adjustment_amount');
            $table->timestamp('adjustment_date')->nullable();
            $table->string('adjustment_ref')->nullable();
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
            $table->renameColumn('adjustment_amount','sdn_expense');
            $table->timestamp('adjustment_date')->nullable();
            $table->string('adjustment_ref')->nullable();
        });
    }
}
