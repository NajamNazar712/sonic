<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWholesaleInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wholesale_invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('wholesale_user_id')->index();
            $table->string('invoice_number')->index();
            $table->bigInteger('total_courier_charges');
            $table->bigInteger('service_charges');
            $table->bigInteger('gst');
            $table->tinyInteger('status')->index();
            $table->integer('updated_by')->nullable()->index();
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
        Schema::dropIfExists('wholesale_invoices');
    }
}
