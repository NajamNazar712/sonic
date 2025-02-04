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
        Schema::table('v2_pickup_requests', function (Blueprint $table) {
            $table->tinyInteger('email_sent')->default(0)->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::table('v2_pickup_requests', function (Blueprint $table) {
            $table->dropColumn('email_sent');
        });
    }
};
