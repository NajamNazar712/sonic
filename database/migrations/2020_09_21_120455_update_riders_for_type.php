<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateRidersForType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('riders', function (Blueprint $table) {
            $table->integer('rider_type_id')->default(1);
            $table->integer('blacklist')->default(0);
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
            $table->dropColumn('rider_type_id');
            $table->dropColumn('blacklist');
        });
    }
}
