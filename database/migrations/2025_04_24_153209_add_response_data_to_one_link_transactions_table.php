<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddResponseDataToOneLinkTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('one_link_transactions', function (Blueprint $table) {
            $table->json('response_data')->nullable()->after('log_id'); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('one_link_transactions', function (Blueprint $table) {
            $table->dropColumn('response_data');
        });
    }
}

