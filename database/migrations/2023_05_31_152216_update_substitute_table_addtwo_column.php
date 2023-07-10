<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateSubstituteTableAddtwoColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('substitute_users', function (Blueprint $table) {
            $table->tinyInteger('is_created_by_admin')->nullable()->default(0);
            $table->integer('created_by_admin_id')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('substitute_users', function (Blueprint $table) {
            $table->dropColumn('is_created_by_admin');
            $table->dropColumn('created_by_admin_id');
        });
    }
}
