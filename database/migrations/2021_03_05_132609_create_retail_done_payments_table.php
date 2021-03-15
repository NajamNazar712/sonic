<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRetailDonePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('retail_done_payments', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('user_id')->index();
            $table->integer('total_shipments');
            $table->integer('delivered_shipments');
            $table->integer('adjusted_shipments');
            $table->string('reference_number')->nullable();
            $table->integer('company_bank_id')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->integer('ibft_charges')->nullable();
            $table->timestamp('status_updated_at')->nullable();
            $table->integer('status_updated_by')->nullable();
            $table->integer('user_bank_info_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('retail_done_payments');
    }
}
