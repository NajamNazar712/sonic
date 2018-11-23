<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('invoice_number', 250)->nullable()->default(NULL);
            $table->integer('user_id');
            $table->timestamp('billing_period_from_date');
            $table->timestamp('billing_period_to_date');
            $table->timestamp('due_date');
            $table->integer('total_shipments')->default(0);
            $table->integer('total_delivered_shipments')->default(0);
            $table->integer('total_returned_shipments')->default(0);
            $table->integer('total_adjusted_shipments')->default(0);
            $table->integer('total_charges')->default(0);
            $table->integer('total_gst')->default(0);
            $table->integer('total_invoice_amount')->default(0);
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('invoice_number');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoices');
    }
}
