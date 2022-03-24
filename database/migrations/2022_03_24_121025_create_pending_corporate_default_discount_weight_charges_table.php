<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePendingCorporateDefaultDiscountWeightChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pending_corporate_default_discount_weight_charges', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->index();
            $table->integer('shipping_mode_id')->index('smi');
            $table->integer('destination_id')->index('di');
            $table->decimal('range_up', 8, 2);
            $table->decimal('range_down', 8, 2);
            $table->boolean('weight_addition')->default(0);
            $table->decimal('spkg')->nullable(true);
            $table->decimal('local_or_6hr', 8, 2);
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
        Schema::dropIfExists('pending_corporate_default_discount_weight_charges');
    }
}
