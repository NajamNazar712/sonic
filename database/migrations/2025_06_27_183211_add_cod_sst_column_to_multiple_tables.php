<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pending_payment_calculations', function (Blueprint $table) {
            $table->decimal('cod_sst')->default(0);
        });

        Schema::table('pending_payment_shipments', function (Blueprint $table) {
            $table->decimal('cod_sst')->default(0);
        });

        Schema::table('done_payment_calculations', function (Blueprint $table) {
            $table->decimal('cod_sst')->default(0);
        });

        Schema::table('done_payment_shipments', function (Blueprint $table) {
            $table->decimal('cod_sst')->default(0);
        });

        Schema::table('pending_invoice_shipments', function (Blueprint $table) {
            $table->decimal('cod_sst')->default(0);
        });

        Schema::table('invoice_shipments', function (Blueprint $table) {
            $table->decimal('cod_sst')->default(0);
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->decimal('cod_sst')->default(0);
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
            $table->dropColumn(['cod_sst']);
        });

        Schema::table('pending_payment_shipments', function (Blueprint $table) {
             $table->dropColumn(['cod_sst']);
        });

        Schema::table('done_payment_calculations', function (Blueprint $table) {
             $table->dropColumn(['cod_sst']);
        });

        Schema::table('done_payment_shipments', function (Blueprint $table) {
             $table->dropColumn(['cod_sst']);
        });

        Schema::table('pending_invoice_shipments', function (Blueprint $table) {
             $table->dropColumn(['cod_sst']);
        });

        Schema::table('invoice_shipments', function (Blueprint $table) {
             $table->dropColumn(['cod_sst']);
        });

        Schema::table('invoices', function (Blueprint $table) {
             $table->dropColumn(['cod_sst']);
        });
    }
};
