<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalePersonTargetsTableDeleteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_person_target_deletes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('deleted_id')->index();
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('sales_person_id')->index();
            $table->integer('target_days');
            $table->integer('target_month');
            $table->integer('average_revenue');
            $table->integer('deleted_by')->index();
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
        Schema::dropIfExists('sale_person_target_deletes');
    }
}
