<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateRetailStandardRatesForDecimal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('retail_standard_rates', function (Blueprint $table) {
            $table->decimal('range_up', 8,2)->change();
            $table->decimal('range_down', 8,2)->change();
        });
    }

    /**                               c
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('retail_standard_rates', function (Blueprint $table) {
            $table->integer('range_up')->change();
            $table->integer('range_down')->change();
        });
    }
}
