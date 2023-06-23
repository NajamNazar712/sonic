<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateRevenueByInvoiceReportsForPaymentColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('revenue_by_invoice_reports', function (Blueprint $table) {
            $table->integer('payment_type')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('revenue_by_invoice_reports', function (Blueprint $table) {
              $table->dropColumn('payment_type');
        });
    }
}
