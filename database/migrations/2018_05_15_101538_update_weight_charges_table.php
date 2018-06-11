<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateWeightChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('weight_charges', function (Blueprint $table) {
            $table->float('range_up')->change();
            $table->float('range_down')->change();
            $table->integer('spkg')->nullable(true)->change();
            $table->integer('local_or_6hr')->change();
            $table->integer('national_or_sameday')->change();
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
            $table->decimal('range_up', 8, 2);
            $table->decimal('range_down', 8, 2);
            $table->decimal('spkg')->nullable(true);
            $table->decimal('local_or_6hr', 8, 2);
            $table->decimal('national_or_sameday', 8, 2);
        });
    }
}
