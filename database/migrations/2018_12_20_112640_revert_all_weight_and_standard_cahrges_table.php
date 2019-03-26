<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RevertAllWeightAndStandardCahrgesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pending_weight_charges', function (Blueprint $table) {
            $table->float('range_up')->change();
            $table->float('range_down')->change();
            $table->renameColumn('national_charges_class_0','national_or_sameday');
            $table->dropColumn('national_charges_class_1');
            $table->dropColumn('national_charges_class_2');
            $table->dropColumn('national_charges_class_3');
        });
        Schema::table('history_weight_charges', function (Blueprint $table) {
            $table->float('range_up')->change();
            $table->float('range_down')->change();
            $table->renameColumn('national_charges_class_0','national_or_sameday');
            $table->dropColumn('national_charges_class_1');
            $table->dropColumn('national_charges_class_2');
            $table->dropColumn('national_charges_class_3');
        });
        Schema::table('pending_rate_statuses', function (Blueprint $table) {
            $table->boolean('fuel_charges')->default(0)->change();
        });
        Schema::table('pending_discount_charges', function (Blueprint $table) {
            $table->integer('weight')->default(0)->change();
            $table->integer('cash')->default(0)->change();
            $table->integer('insurance')->default(0)->change();
            $table->integer('return')->default(0)->change();
            $table->integer('packaging')->default(0)->change();
            $table->integer('added_by')->change();
        });
        Schema::table('pending_booking_type_charges', function (Blueprint $table) {
            $table->float('replacement_charges')->change();
            $table->float('try_and_buy_charges')->change();
        });
        Schema::table('history_rate_statuses', function (Blueprint $table) {
            $table->boolean('fuel_charges')->default(0)->change();
        });
        Schema::table('history_discount_charges', function (Blueprint $table) {
            $table->integer('weight')->default(0)->change();
            $table->integer('cash')->default(0)->change();
            $table->integer('insurance')->default(0)->change();
            $table->integer('return')->default(0)->change();
            $table->integer('packaging')->default(0)->change();
            $table->integer('added_by')->change();
        });
        Schema::table('history_booking_type_charges', function (Blueprint $table) {
            $table->float('replacement_charges')->change();
            $table->float('try_and_buy_charges')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
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
}
