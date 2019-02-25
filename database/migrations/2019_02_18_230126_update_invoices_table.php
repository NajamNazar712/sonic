<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->unsignedBigInteger('invoice_number', FALSE)->unsigned()->nullable()->default(NULL)->change();

            $table->timestamp('received_date')->nullable()->default(NULL);
            $table->integer('company_bank_id')->nullable()->default(NULL);
            $table->decimal('received_amount', 16, 2)->nullable()->default(NULL);
            $table->decimal('tax_amount', 16, 2)->nullable()->default(NULL);
            $table->timestamp('deposit_date')->nullable()->default(NULL);
            $table->integer('status_id');

            $table->index('due_date');
            $table->index('received_date');
            $table->index('deposit_date');
            $table->index('status_id');
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
            $table->string('invoice_number', 250)->nullable()->default(NULL)->change();

            $table->dropColumn('received_date');
            $table->dropColumn('company_bank_id');
            $table->dropColumn('received_amount');
            $table->dropColumn('tax_amount');
            $table->dropColumn('deposit_date');
            $table->dropColumn('status_id');
        });
    }
}
