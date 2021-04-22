<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUsersTableAddNewColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('rates_added_at')->nullable();
            $table->timestamp('rates_approved_at')->nullable();
            $table->timestamp('rates_rejected_at')->nullable();
            $table->integer('rates_rejected_by')->index()->nullable();
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
            $table->dropColumn('rates_added_at');
            $table->dropColumn('rates_approved_at');
            $table->dropColumn('rates_rejected_at');
            $table->dropColumn('rates_rejected_by');
        });
    }
}
