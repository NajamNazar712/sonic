<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RecreateZonalWork extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('weight_charges', function (Blueprint $table) {
            $table->renameColumn('national_or_sameday','national_charges_class_0');
            $table->string('national_charges_class_1');
            $table->string('national_charges_class_2');
            $table->string('national_charges_class_3');
        });

        Schema::table('standard_weight_charges', function (Blueprint $table) {
            $table->renameColumn('national_or_sameday','national_charges_class_0');
            $table->string('national_charges_class_1');
            $table->string('national_charges_class_2');
            $table->string('national_charges_class_3');
        });

        Schema::table('pending_weight_charges', function (Blueprint $table) {
            $table->renameColumn('national_or_sameday','national_charges_class_0');
            $table->string('national_charges_class_1');
            $table->string('national_charges_class_2');
            $table->string('national_charges_class_3');
        });
        Schema::table('history_weight_charges', function (Blueprint $table) {
            $table->renameColumn('national_or_sameday','national_charges_class_0');
            $table->string('national_charges_class_1');
            $table->string('national_charges_class_2');
            $table->string('national_charges_class_3');
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
            $table->renameColumn('national_charges_class_0','national_or_sameday');
            $table->dropColumn('national_charges_class_1');
            $table->dropColumn('national_charges_class_2');
            $table->dropColumn('national_charges_class_3');
        });

        Schema::table('standard_weight_charges', function (Blueprint $table) {
            $table->renameColumn('national_charges_class_0','national_or_sameday');
            $table->dropColumn('national_charges_class_1');
            $table->dropColumn('national_charges_class_2');
            $table->dropColumn('national_charges_class_3');
        });
        Schema::table('pending_weight_charges', function (Blueprint $table) {
            $table->renameColumn('national_charges_class_0','national_or_sameday');
            $table->dropColumn('national_charges_class_1');
            $table->dropColumn('national_charges_class_2');
            $table->dropColumn('national_charges_class_3');
        });
        Schema::table('history_weight_charges', function (Blueprint $table) {
            $table->renameColumn('national_charges_class_0','national_or_sameday');
            $table->dropColumn('national_charges_class_1');
            $table->dropColumn('national_charges_class_2');
            $table->dropColumn('national_charges_class_3');
        });
    }
}
