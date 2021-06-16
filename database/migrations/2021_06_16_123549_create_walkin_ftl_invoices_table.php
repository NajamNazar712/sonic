<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWalkinFtlInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('walkin_ftl_invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ftl_request_id')->index();
            $table->bigInteger('invoice_number')->nullable()->index();
            $table->integer('status_id')->default(1);
            $table->integer('company_bank_id')->nullable()->index();
            $table->decimal('recieved_amount')->nullable();
            $table->decimal('tax_amount')->nullable();
            $table->timestamp('recieving_date')->nullable();
            $table->timestamp('deposit_date')->nullable();
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
        Schema::dropIfExists('walkin_ftl_invoices');
    }
}
