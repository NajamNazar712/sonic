<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoiceAdjustmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_adjustments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('invoice_id')->index();
            $table->integer('amount');
            $table->text('reason');
            $table->text('remarks')->nullable();
            $table->integer('added_by')->index();
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
        Schema::dropIfExists('invoice_adjustments');
    }
}
