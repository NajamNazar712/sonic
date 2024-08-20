<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRetailUserCommissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('retail_user_commissions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('franchise_id')->nullable();
            $table->string('franchise_code')->nullable();
            $table->integer('month')->nullable();
            $table->integer('retail_shipping_mode_id')->nullable();
            $table->integer('number_of_shipments')->nullable();
            $table->decimal('total_charges_without_gst', 8,2)->nullable();
            $table->decimal('product_percentage', 8,2)->nullable();
            $table->decimal('commission', 8,2)->nullable();
            $table->decimal('gst_percentage', 8,2)->nullable();
            $table->decimal('total_charges_with_gst', 8,2)->nullable();
            $table->decimal('franchise_gst_amount', 8,2)->nullable();
            $table->decimal('franchise_withholding_percentage', 8,2)->nullable();
            $table->decimal('franchise_withholding_amount', 8,2)->nullable();
            $table->decimal('charges_without_withholding', 8,2)->nullable();
            $table->decimal('deduction_percentage', 8,2)->nullable();
            $table->decimal('deduction_amount', 8,2)->nullable();
            $table->decimal('net_commission', 8,2)->nullable();
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
        Schema::dropIfExists('retail_user_commissions');
    }
}
