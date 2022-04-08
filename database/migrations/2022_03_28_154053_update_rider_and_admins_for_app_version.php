<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateRiderAndAdminsForAppVersion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('riders', function (Blueprint $table) {
            $table->Integer('current_app_version')->nullable();
        });

        Schema::table('admins', function (Blueprint $table) {
            $table->Integer('current_app_version')->nullable();
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
            $table->dropColumn('current_app_version');
        });

        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn('current_app_version');
        });
    }
}
