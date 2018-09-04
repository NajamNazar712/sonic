<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateAddColumnsForRatesInUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('rates_added_by')->nullable();
            $table->integer('rates_authorized_by')->nullable();
            $table->integer('account_activated_by')->nullable();
            $table->timestamp('activated_at')->nullable();
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
            $table->dropColumn('rates_added_by');
            $table->dropColumn('rates_authorized_by');
            $table->dropColumn('account_activated_by');
            $table->dropColumn('activated_at');
        });
    }
}
