<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateStandardWeightChargesTableForDatatype extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('standard_weight_charges', function (Blueprint $table) {
            $table->decimal('local_or_6hr')->change();
            $table->decimal('national_charges_class_0')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('standard_weight_charges', function (Blueprint $table) {
            $table->integer('local_or_6hr')->change();
            $table->integer('national_charges_class_0')->change();
        });
    }
}
