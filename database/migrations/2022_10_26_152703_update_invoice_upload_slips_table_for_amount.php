<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateInvoiceUploadSlipsTableForAmount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoice_upload_slips', function (Blueprint $table) {
            $table->integer('amount')->nullable();
            $table->integer('added_by')->index()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoice_upload_slips', function (Blueprint $table) {
            $table->dropColumn('amount')->nullable();
            $table->dropColumn('added_by')->index()->nullable();
        });
    }
}
