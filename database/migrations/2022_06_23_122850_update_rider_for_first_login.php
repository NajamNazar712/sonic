<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateRiderForFirstLogin extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('riders', function (Blueprint $table) {
            $table->integer('first_login')->index()->default(0);
        });

        Schema::table('retail_users', function (Blueprint $table) {
            $table->integer('first_login')->index()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('riders', function (Blueprint $table) {
            $table->dropColumn('first_login');
        });

        Schema::table('retail_users', function (Blueprint $table) {
            $table->dropColumn('first_login');
        });
    }
}
