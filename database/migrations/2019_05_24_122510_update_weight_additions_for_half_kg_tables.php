<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateWeightAdditionsForHalfKgTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('weight_charges', function (Blueprint $table) {
            $table->decimal('spkg')->change();
        });
        Schema::table('pending_weight_charges', function (Blueprint $table) {
            $table->decimal('spkg')->change();
        });
        Schema::table('history_weight_charges', function (Blueprint $table) {
            $table->decimal('spkg')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('weight_charges', function (Blueprint $table) {
            $table->integer('spkg')->change();
        });
        Schema::table('pending_weight_charges', function (Blueprint $table) {
            $table->integer('spkg')->change();
        });
        Schema::table('history_weight_charges', function (Blueprint $table) {
            $table->integer('spkg')->change();
        });
    }
}
