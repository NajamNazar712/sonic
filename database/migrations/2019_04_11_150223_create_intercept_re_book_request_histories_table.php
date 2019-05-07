<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInterceptReBookRequestHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('intercept_re_book_request_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shipment_id');
            $table->integer('old_consignee_city_id');
            $table->integer('new_consignee_city_id');
            $table->string('old_consignee_name');
            $table->string('new_consignee_name');
            $table->string('old_consignee_address');
            $table->string('new_consignee_address');
            $table->string('old_consignee_phone_number_1');
            $table->string('new_consignee_phone_number_1');
            $table->string('old_consignee_phone_number_2')->nullable();
            $table->string('new_consignee_phone_number_2')->nullable();
            $table->string('old_consignee_email')->nullable();
            $table->string('new_consignee_email')->nullable();
            $table->bigInteger('old_amount');
            $table->bigInteger('new_amount');
            $table->integer('shipper_id');
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
        Schema::dropIfExists('intercept_re_book_request_histories');
    }
}
