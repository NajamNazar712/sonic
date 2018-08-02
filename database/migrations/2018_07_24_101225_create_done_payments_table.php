<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDonePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('done_payments', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('user_id');
            $table->integer('total_shipments');
            $table->integer('delivered_shipments');
            $table->integer('returned_shipments');
            $table->integer('adjusted_shipments');
            $table->string('reference_number')->nullable()->default(NULL);
            $table->integer('company_bank_id')->nullable()->default(NULL);
            $table->tinyinteger('status')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('done_payments');
    }
}
