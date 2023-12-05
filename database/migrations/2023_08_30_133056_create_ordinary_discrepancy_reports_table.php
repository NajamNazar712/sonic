<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdinaryDiscrepancyReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ordinary_discrepancy_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shipment_id')->index();
            $table->integer('user_id')->index();
            $table->integer('shipment_status_id')->index();
            $table->integer('city_id')->index();
            $table->string('product_content')->nullable();
            $table->string('picture_path')->nullable();
            $table->integer('quantity')->nullable();
            $table->string('remarks')->nullable();
            $table->integer('admin_id')->index()->nullable();
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
        Schema::dropIfExists('ordinary_discrepancy_reports');
    }
}
