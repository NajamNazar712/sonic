<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDonePaymentsReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('done_payments_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('payment_id');
            $table->string('shipper_id');
            $table->string('shipper_name');
            $table->decimal('amount', 20,2);
            $table->string('iban_number');
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
        Schema::dropIfExists('done_payments_reports');
    }
}
