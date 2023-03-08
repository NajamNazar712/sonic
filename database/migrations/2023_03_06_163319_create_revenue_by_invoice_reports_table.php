<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRevenueByInvoiceReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('revenue_by_invoice_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('business_category_id')->index();
            $table->integer('user_id')->index();
            $table->integer('origin_id')->index();
            $table->bigInteger('invoice_number')->index();
            $table->timestamp('invoicing_date')->index();
            $table->decimal('weight_charges', 16,2);
            $table->decimal('cash_handling_charges', 16,2);
            $table->decimal('insurance_charges', 16,2);
            $table->decimal('packaging_charges', 16,2);
            $table->decimal('fuel_surcharge', 16,2);
            $table->decimal('return_charges', 16,2);
            $table->decimal('replacement_charges', 16,2);
            $table->decimal('packing_charges', 16,2);
            $table->decimal('try_buy_charges', 16,2);
            $table->decimal('nsa_osa_charges', 16,2);
            $table->decimal('intercept_charges', 16,2);
            $table->decimal('gst', 8,2);
            $table->decimal('total_charges', 16,2);
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
        Schema::dropIfExists('revenue_by_invoice_reports');
    }
}
