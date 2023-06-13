<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFintechPaymentDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fintech_payment_details', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('trax_pay_id');
            $table->String('transaction_id');
            $table->Integer('cod_amount');
            $table->decimal('rider_tip', 8, 2);
            $table->Integer('rider_id');
            $table->Integer('fintech_company_id');
            $table->decimal('total_fintech_amount', 8, 2);
            $table->decimal('fintech_company_amount', 8, 2);
            $table->decimal('revenue', 8, 2);
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
        Schema::dropIfExists('fintech_payment_details');
    }
}
