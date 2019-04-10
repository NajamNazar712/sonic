<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInterceptReBookRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('intercept_re_book_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shipment_id');
            $table->integer('consignee_city_id');
            $table->string('consignee_name');
            $table->string('consignee_address');
            $table->integer('consignee_phone_number_1');
            $table->integer('consignee_phone_number_2')->nullable();
            $table->string('consignee_email')->nullable();
            $table->integer('amount');
            $table->integer('shipper_id');
            $table->integer('status');
            $table->integer('updated_by')->nullable();
            $table->integer('updated_by_date')->nullable();
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
        Schema::dropIfExists('intercept_re_book_requests');
    }
}
