<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdvacncePettyCashSdnLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('advacnce_petty_cash_sdn_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('petty_cash_statement_id')->index();
            $table->integer('previous_sdn_id')->index();
            $table->integer('admin_id')->index();
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
        Schema::dropIfExists('advacnce_petty_cash_sdn_logs');
    }
}
