<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableAddColumnSmsChargesToInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->decimal('total_sms_charges', 16, 2)->default(0)->after('total_gst');
            $table->decimal('total_fixed_sms_charges', 16, 2)->default(0)->after('total_gst');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('total_sms_charges');
            $table->dropColumn('total_fixed_sms_charges');
        });
    }
}
