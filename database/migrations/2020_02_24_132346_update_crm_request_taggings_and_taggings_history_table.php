<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCrmRequestTaggingsAndTaggingsHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('crm_request_taggings', function (Blueprint $table) {
            $table->integer('tagged_id')->nullable()->change();
        });
        Schema::table('crm_request_tagging_histories', function (Blueprint $table) {
            $table->integer('tagged_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('crm_request_taggings', function (Blueprint $table) {
            $table->integer('tagged_id')->change();
        });
        Schema::table('crm_request_tagging_histories', function (Blueprint $table) {
            $table->integer('tagged_id')->change();
        });
    }
}
