<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateInterceptRebookRequestandHistoryTableAddArea extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('intercept_re_book_requests', function (Blueprint $table) {
            $table->integer('city_area_id')->nullable()->index();
        });
        Schema::table('intercept_re_book_request_histories', function (Blueprint $table) {
            $table->integer('new_con_city_area_id')->nullable()->index();
            $table->integer('old_con_city_area_id')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('intercept_re_book_requests', function (Blueprint $table) {
            $table->removeColumn('city_area_id');
        });
        Schema::table('intercept_re_book_request_histories', function (Blueprint $table) {
            $table->removeColumn('new_consignee_city_area_id');
            $table->removeColumn('old_consignee_city_area_id');
        });
    }
}
