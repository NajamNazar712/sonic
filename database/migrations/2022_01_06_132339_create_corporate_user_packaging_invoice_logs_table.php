<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCorporateUserPackagingInvoiceLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('corporate_user_packaging_invoice_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('packaging_invoice_id')->index();
            $table->integer('user_id')->index();
            $table->integer('admin_id')->index();
            $table->integer('status')->index();
            $table->integer('rate_type_id')->index();
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
        Schema::dropIfExists('corporate_user_packaging_invoice_logs');
    }
}
