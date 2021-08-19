<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateInterceptReBookRequestsHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('intercept_re_book_request_histories', function (Blueprint $table) {
            $table->tinyInteger('intercept_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('intercept_re_book_request_histories', function (Blueprint $table) {
            $table->dropColumn('intercept_type');
        });
    }
}
