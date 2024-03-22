<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableAddSmsChargesColumnToDonePaymentCalculationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('done_payment_calculations', function (Blueprint $table) {
            $table->decimal('sms_charges', 16, 2)->default(0)->after('gst');
            $table->boolean('sms_fixed_charge_flag')->after('sms_charges');
            $table->decimal('fixed_sms_charges', 16, 2)->default(0)->after('sms_fixed_charge_flag');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('done_payment_calculations', function (Blueprint $table) {
            $table->dropColumn('sms_charges');
            $table->dropColumn('sms_fixed_charge_flag');
            $table->dropColumn('fixed_sms_charges');
        });
    }
}
