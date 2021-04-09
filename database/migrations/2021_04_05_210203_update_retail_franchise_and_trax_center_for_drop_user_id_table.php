<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateRetailFranchiseAndTraxCenterForDropUserIdTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('retail_franchises', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
        Schema::table('retail_trax_centers', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
        Schema::table('retail_users', function (Blueprint $table) {
            $table->integer('otp')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('retail_franchises', function (Blueprint $table) {
            $table->integer('user_id');
        });
        Schema::table('retail_trax_centers', function (Blueprint $table) {
            $table->integer('user_id');
        });
        Schema::table('retail_users', function (Blueprint $table) {
            $table->dropColumn('otp');
        });
    }
}
