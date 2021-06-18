<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFtlRequestAdditionalCostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ftl_request_additional_costs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ftl_request_id')->index();
            $table->float('amount');
            $table->string('cost_type');
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
        Schema::dropIfExists('ftl_request_additional_costs');
    }
}
