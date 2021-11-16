<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePettyCashSdnLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('petty_cash_sdn_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('petty_cash_statement_id')->index();
            $table->unsignedInteger('previous_sdn_id')->index();
            $table->unsignedInteger('admin_id')->index();
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
        Schema::dropIfExists('petty_cash_sdn_logs');
    }
}
