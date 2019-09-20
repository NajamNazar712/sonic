<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDailyVisitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daily_visits', function (Blueprint $table) {
            $table->increments('id');
            $table->string('company_name');
            $table->string('customer_name');
            $table->string('customer_address');
            $table->string('phone_no');
            $table->string('email');
            $table->string('lead_status_id');
            $table->string('feedback');
            $table->string('business_card_image')->nullable();
            $table->string('location_image')->nullable();
            $table->string('latitude');
            $table->string('longitude');
            $table->string('user_id');
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
        Schema::dropIfExists('daily_visits');
    }
}
