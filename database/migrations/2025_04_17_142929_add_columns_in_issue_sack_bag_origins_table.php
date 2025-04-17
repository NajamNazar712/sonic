<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('issue_sack_bag_origins', function (Blueprint $table) {
            $table->timestamp('active_date_time')->nullable();
            $table->timestamp('inactive_date_time')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('issue_sack_bag_origins', function (Blueprint $table) {
            $table->dropColumn('active_date_time');
            $table->dropColumn('inactive_date_time');
        });
    }
};
