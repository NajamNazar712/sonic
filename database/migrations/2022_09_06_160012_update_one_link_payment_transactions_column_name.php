<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateOneLinkPaymentTransactionsColumnName extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('one_link_payment_transactions', function (Blueprint $table) {
            $table->renameColumn('consumer_prefx', 'consumer_prefix');
            $table->integer('status')->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('one_link_payment_transactions', function (Blueprint $table) {
            $table->renameColumn('consumer_prefix', 'consumer_prefx');
        });
    }
}
