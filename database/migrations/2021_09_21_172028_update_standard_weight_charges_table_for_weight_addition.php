<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateStandardWeightChargesTableForWeightAddition extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('standard_weight_charges', function (Blueprint $table) {
            $table->tinyInteger('weight_addition')->after('range_down')->default(0);
            $table->decimal('kg_range',8,2)->after('weight_addition');
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
            $table->dropColumn('weight_addition');
            $table->dropColumn('kg_range');
        });
    }
}
