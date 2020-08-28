<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePaymentCalculationsLength extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pending_payment_calculations', function (Blueprint $table) {
            $table->decimal('amount', 20,2)->change();
            $table->decimal('charges', 20,2)->change();
            $table->decimal('gst', 20,2)->change();
            $table->decimal('payable', 20,2)->change();
        });
        Schema::table('done_payment_calculations', function (Blueprint $table) {
            $table->decimal('amount', 20,2)->change();
            $table->decimal('charges', 20,2)->change();
            $table->decimal('gst', 20,2)->change();
            $table->decimal('payable', 20,2)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pending_payment_calculations', function (Blueprint $table) {
            $table->decimal('amount', 8,2)->change();
            $table->decimal('charges', 8,2)->change();
            $table->decimal('gst', 8,2)->change();
            $table->decimal('payable', 8,2)->change();
        });
        Schema::table('done_payment_calculations', function (Blueprint $table) {
            $table->decimal('amount', 8,2)->change();
            $table->decimal('charges', 8,2)->change();
            $table->decimal('gst', 8,2)->change();
            $table->decimal('payable', 8,2)->change();
        });
    }
}
