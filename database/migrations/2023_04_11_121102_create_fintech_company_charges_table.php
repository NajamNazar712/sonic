<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFintechCompanyChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fintech_company_charges', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('company_Id');
            $table->string('range_up');
            $table->string('range_down');
            $table->string('charges');
            $table->Integer('charges_is_percentage');
            $table->string('additional_charges')->nullable();
            $table->Integer('additional_charges_is_percentage');
            $table->string('fed_tax');
            $table->Integer('fed_tax_is_percentage');
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
        Schema::dropIfExists('fintech_company_charges');
    }
}
