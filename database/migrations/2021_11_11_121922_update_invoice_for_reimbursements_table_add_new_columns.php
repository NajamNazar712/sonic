<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateInvoiceForReimbursementsTableAddNewColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoice_for_reimbursements', function (Blueprint $table) {
            $table->float('total_charges')->default(0);
            $table->float('total_gst')->default(0);
            $table->bigInteger('total_invoice_amount')->default(0);
            $table->timestamp('invoicing_date')->nullable();
            $table->boolean('to_show')->default(0)->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoice_for_reimbursements', function (Blueprint $table) {
            $table->dropColumn('total_charges');
            $table->dropColumn('total_gst');
            $table->dropColumn('total_invoice_amount');
            $table->dropColumn('invoicing_date');
            $table->dropColumn('to_show');
        });
    }
}
