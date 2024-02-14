<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateAddColumnBlockDisableRemarksReasonTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('blacklist_reason_1')->index()->nullable();
            $table->integer('disable_reason_1')->index()->nullable();
            $table->string('disable_reason')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('blacklist_reason_1');
            $table->dropColumn('disable_reason_1');
            $table->dropColumn('disable_reason');
        });
    }
}
