<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateWalkinReturnCharges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('walk_in_standard_weight_charges', function (Blueprint $table) {

                $table->renameColumn('national','national_charges_class_0')->change();
                $table->integer('national_charges_class_1')->after('national');
                $table->integer('national_charges_class_2')->after('national_charges_class_1');
                $table->integer('national_charges_class_3')->after('national_charges_class_2');

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
            $table->renameColumn('national_charges_class_0','national')->change();
            $table->dropColumn('national_charges_class_1');
            $table->dropColumn('national_charges_class_2');
            $table->dropColumn('national_charges_class_3');
        });
    }
}
