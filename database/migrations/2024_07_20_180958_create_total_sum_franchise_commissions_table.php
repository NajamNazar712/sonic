<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTotalSumFranchiseCommissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('total_sum_franchise_commissions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('franchise_id')->nullable();
            $table->string('franchise_code')->nullable();
            $table->string('franchise_name')->nullable();
            $table->integer('sum_of_all_shipments')->nullable();
            $table->decimal('sum_of_total_charges', 8, 2)->nullable();
            $table->decimal('sum_of_gst', 8, 2)->nullable();
            $table->decimal('sum_of_weight_charges', 8, 2)->nullable();
            $table->decimal('sum_of_commission', 8, 2)->nullable();
            $table->decimal('withholding_tax_percent', 8, 2)->nullable();
            $table->decimal('withholding_amount', 8, 2)->nullable();
            $table->decimal('commission_gst_deduction_percent', 8, 2)->nullable();
            $table->decimal('deduction_amount', 8, 2)->nullable();
            $table->decimal('net_commission', 8, 2)->nullable();
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
        Schema::dropIfExists('total_sum_franchise_commissions');
    }
}
