<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStandardFintechChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('standard_fintech_charges', function (Blueprint $table) {
            $table->increments('id');
            $table->string('standard_fintech_charges');
            $table->string('standard_fed_charges');
            $table->tinyInteger('status')->default('1');
            $table->Integer('created_by');
            $table->Integer('updated_by');
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
        Schema::dropIfExists('standard_fintech_charges');
    }
}
