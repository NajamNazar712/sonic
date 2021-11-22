<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesIncentivesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_incentives', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('territory_id')->index();
            $table->integer('designation_id')->index();
            $table->integer('admin_id')->index();
            $table->integer('shipper_count');
            $table->integer('shipment_count');
            $table->decimal('revenue',16,2);
            $table->decimal('commission',8,2);
            $table->integer('filter_date_id')->index();
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
        Schema::dropIfExists('sales_incentives');
    }
}
