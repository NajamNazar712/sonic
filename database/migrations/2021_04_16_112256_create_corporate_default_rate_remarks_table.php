<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCorporateDefaultRateRemarksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('corporate_default_rate_remarks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('remarks');
            $table->integer('admin_id');
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
        Schema::dropIfExists('corporate_default_rate_remarks');
    }
}
