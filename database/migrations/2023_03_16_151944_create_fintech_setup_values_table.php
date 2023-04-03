<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFintechSetupValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fintech_setup_values', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('company_Id');
            $table->string('range_up');
            $table->string('range_down');
            $table->string('charges');
            $table->string('additional_charges');
            $table->string('fed_tax');
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
        Schema::dropIfExists('fintech_setup_values');
    }
}
