<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableAddSmsChargesColumnToPendingInvoiceShipmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pending_invoice_shipments', function (Blueprint $table) {
            $table->decimal('sms_charges', 16, 2)->default(0)->after('gst');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pending_invoice_shipments', function (Blueprint $table) {
            $table->dropColumn('sms_charges');
        });
    }
}
