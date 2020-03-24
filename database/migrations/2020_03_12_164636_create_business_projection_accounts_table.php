<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBusinessProjectionAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('business_projection_accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('city_id');
            $table->integer('sale_person_id');
            $table->integer('average_shipment');
            $table->integer('projected_shipment');
            $table->integer('last_day_number');
            $table->integer('achieved');
            $table->integer('business_projection_reason_id')->nullable();
            $table->string('remarks')->nullable();
            $table->date('date');
            $table->timestamps();
            $table->index(['user_id', 'sale_person_id', 'average_shipment', 'projected_shipment', 'last_day_number', 'achieved', 'date','created_at','updated_at'],'index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('business_projection_accounts');
    }
}
