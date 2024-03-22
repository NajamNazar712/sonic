<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCorporateFixedSmsChargesFlagTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('corporate_fixed_sms_charges_flag', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->decimal('fixed_sms_charges', 16,2);
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
        Schema::dropIfExists('corporate_fixed_sms_charges_flag');
    }
}
