<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateAdminUserRequestsForOutlookEmailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('admin_user_requests', function (Blueprint $table) {
            $table->integer('outlook_email')->default(0);
            $table->string('visible_password')->nullable();
            $table->string('visible_outlook_password')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('admin_user_requests', function (Blueprint $table) {
            $table->dropColumn('outlook_email');
            $table->dropColumn('visible_password');
            $table->dropColumn('visible_outlook_password');
        });
    }
}
