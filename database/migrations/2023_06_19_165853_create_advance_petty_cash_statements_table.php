<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdvancePettyCashStatementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('advance_petty_cash_statements', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sdn_id')->index();
            $table->integer('hub_id')->index();
            $table->bigInteger('reference_no')->unique();
            $table->timestamp('from');
            $table->timestamp('to');
            $table->integer('total_amount');
            $table->integer('created_by')->index();
            $table->integer('origin_hub_id')->index();
            $table->integer('destination_hub_id')->index();
            $table->integer('zone_id')->index();
            $table->integer('station_manager_id')->index();
            $table->tinyInteger('status_id')->index();
            $table->integer('amount_availed')->default(0);
            $table->integer('balance')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('advance_petty_cash_statements');
    }
}
