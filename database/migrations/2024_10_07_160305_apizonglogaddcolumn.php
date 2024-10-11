<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Apizonglogaddcolumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('api_zong_logs', function (Blueprint $table) {
            $table->integer('shipment_id')->index()->nullable();
        });

        Schema::table('api_zong_logs', function (Blueprint $table) {
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('api_zong_logs', function (Blueprint $table) {
            $table->dropColumn('shipment_id');
        });
        Schema::table('api_zong_logs', function (Blueprint $table) {
            $table->dropColumn('updated_at');
        });
    }
}
