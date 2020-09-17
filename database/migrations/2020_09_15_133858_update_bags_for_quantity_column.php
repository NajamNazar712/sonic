<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateBagsForQuantityColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bags', function (Blueprint $table) {
            $table->integer('quantity')->after('shipments')->nullable();
        });
        Schema::table('master_cargoes', function (Blueprint $table) {
            $table->integer('quantity')->after('shipments')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bags', function (Blueprint $table) {
            $table->dropColumn('quantity');
        });
        Schema::table('master_cargoes', function (Blueprint $table) {
            $table->dropColumn('quantity');
        });
    }
}
