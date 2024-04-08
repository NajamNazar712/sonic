<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTraxItemInsurancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trax_item_insurances', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('booking_id');
            $table->integer('special_handling_id')->nullable();
            $table->double('insurance')->nullable();
            $table->string('item_code')->nullable();
            $table->tinyInteger('user_type')->nullable(); // 1 - Admin, 2 - Rider, 0 -> shipper
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
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
        Schema::dropIfExists('trax_item_issurances');
    }
}
