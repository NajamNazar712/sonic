<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSdnDetailLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sdn_detail_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('sdn_no')->nullable();
            $table->unsignedInteger('dncc_no')->nullable();
            $table->bigInteger('dncc_amount')->nullable();
            $table->bigInteger('previous_amount');
            $table->bigInteger('new_amount');
            $table->string('previous_bank');
            $table->string('new_status');
            $table->date('date');
            $table->unsignedInteger('updated_by');
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
        Schema::dropIfExists('sdn_detail_logs');
    }
}
