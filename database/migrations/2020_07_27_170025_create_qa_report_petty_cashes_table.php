<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQaReportPettyCashesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('qa_report_petty_cashes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('hub_id');
            $table->string('hub_name');
            $table->integer('station_approval');
            $table->integer('operation_approval');
            $table->integer('finance_approval');
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
        Schema::dropIfExists('qa_report_petty_cashes');
    }
}
