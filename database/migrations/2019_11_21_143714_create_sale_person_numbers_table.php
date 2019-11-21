<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalePersonNumbersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_person_numbers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('admin_id');
            $table->integer('shipments');
            $table->decimal('revenue');
            $table->decimal('avg_revenue');
            $table->decimal('contribution');
            $table->decimal('total_revenue');
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
        Schema::dropIfExists('sale_person_numbers');
    }
}
