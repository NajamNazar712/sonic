<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateWalkInStandardWightChargesTableChargesPerKgLocalNational extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('walk_in_standard_weight_charges', function (Blueprint $table) {

            $table->renameColumn('chargeable_weight','chargeable_weight_local')->change();
            $table->integer('chargeable_weight_charges_class_0')->after('chargeable_weight');
            $table->integer('chargeable_weight_charges_class_1')->after('chargeable_weight_charges_class_0');
            $table->integer('chargeable_weight_charges_class_2')->after('chargeable_weight_charges_class_1');
            $table->integer('chargeable_weight_charges_class_3')->after('chargeable_weight_charges_class_2');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('walk_in_standard_weight_charges', function (Blueprint $table) {
            $table->renameColumn('chargeable_weight_local','chargeable_weight')->change();
            $table->dropColumn('chargeable_weight_charges_class_0');
            $table->dropColumn('chargeable_weight_charges_class_1');
            $table->dropColumn('chargeable_weight_charges_class_2');
            $table->dropColumn('chargeable_weight_charges_class_3');
        });
    }
}
