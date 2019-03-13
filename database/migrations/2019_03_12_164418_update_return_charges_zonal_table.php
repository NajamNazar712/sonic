<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateReturnChargesZonalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('return_charges', function (Blueprint $table) {
            $table->renameColumn('national','national_charges_class_0')->change();
            $table->string('national_charges_class_1')->after('national');
            $table->string('national_charges_class_2')->after('national_charges_class_1');
            $table->string('national_charges_class_3')->after('national_charges_class_2');
        });
        Schema::table('standard_return_charges', function (Blueprint $table) {
            $table->renameColumn('national','national_charges_class_0')->change();
            $table->string('national_charges_class_1')->after('national');
            $table->string('national_charges_class_2')->after('national_charges_class_1');
            $table->string('national_charges_class_3')->after('national_charges_class_2');
        });

        Schema::table('history_return_charges', function (Blueprint $table) {
            $table->renameColumn('national','national_charges_class_0')->change();
            $table->string('national_charges_class_1')->after('national');
            $table->string('national_charges_class_2')->after('national_charges_class_1');
            $table->string('national_charges_class_3')->after('national_charges_class_2');
        });
        Schema::table('history_corporate_return_charges', function (Blueprint $table) {
            $table->renameColumn('national','national_charges_class_0')->change();
            $table->string('national_charges_class_1')->after('national');
            $table->string('national_charges_class_2')->after('national_charges_class_1');
            $table->string('national_charges_class_3')->after('national_charges_class_2');
        });
        Schema::table('pending_return_charges', function (Blueprint $table) {
            $table->renameColumn('national','national_charges_class_0')->change();
            $table->string('national_charges_class_1')->after('national');
            $table->string('national_charges_class_2')->after('national_charges_class_1');
            $table->string('national_charges_class_3')->after('national_charges_class_2');
        });
        Schema::table('pending_corporate_return_charges', function (Blueprint $table) {
            $table->renameColumn('national','national_charges_class_0')->change();
            $table->string('national_charges_class_1')->after('national');
            $table->string('national_charges_class_2')->after('national_charges_class_1');
            $table->string('national_charges_class_3')->after('national_charges_class_2');
        });

        Schema::table('corporate_return_charges', function (Blueprint $table) {
            $table->renameColumn('national','national_charges_class_0')->change();
            $table->string('national_charges_class_1')->after('national');
            $table->string('national_charges_class_2')->after('national_charges_class_1');
            $table->string('national_charges_class_3')->after('national_charges_class_2');
        });
        Schema::table('corporate_standard_return_charges', function (Blueprint $table) {
            $table->renameColumn('national','national_charges_class_0')->change();
            $table->string('national_charges_class_1')->after('national');
            $table->string('national_charges_class_2')->after('national_charges_class_1');
            $table->string('national_charges_class_3')->after('national_charges_class_2');
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('return_charges', function (Blueprint $table) {
            $table->renameColumn('national_charges_class_0','national')->change();
            $table->dropColumn('national_charges_class_1');
            $table->dropColumn('national_charges_class_2');
            $table->dropColumn('national_charges_class_3');
        });
        Schema::table('standard_return_charges', function (Blueprint $table) {
            $table->renameColumn('national_charges_class_0','national')->change();
            $table->dropColumn('national_charges_class_1');
            $table->dropColumn('national_charges_class_2');
            $table->dropColumn('national_charges_class_3');
        });

        Schema::table('history_return_charges', function (Blueprint $table) {
            $table->renameColumn('national_charges_class_0','national')->change();
            $table->dropColumn('national_charges_class_1');
            $table->dropColumn('national_charges_class_2');
            $table->dropColumn('national_charges_class_3');
        });
        Schema::table('pending_return_charges', function (Blueprint $table) {
            $table->renameColumn('national_charges_class_0','national')->change();
            $table->dropColumn('national_charges_class_1');
            $table->dropColumn('national_charges_class_2');
            $table->dropColumn('national_charges_class_3');
        });
        Schema::table('pending_corporate_return_charges', function (Blueprint $table) {
            $table->renameColumn('national_charges_class_0','national')->change();
            $table->dropColumn('national_charges_class_1');
            $table->dropColumn('national_charges_class_2');
            $table->dropColumn('national_charges_class_3');
        });
        Schema::table('history_corporate_return_charges', function (Blueprint $table) {
            $table->renameColumn('national_charges_class_0','national')->change();
            $table->dropColumn('national_charges_class_1');
            $table->dropColumn('national_charges_class_2');
            $table->dropColumn('national_charges_class_3');
        });

        Schema::table('corporate_return_charges', function (Blueprint $table) {
            $table->renameColumn('national_charges_class_0','national')->change();
            $table->dropColumn('national_charges_class_1');
            $table->dropColumn('national_charges_class_2');
            $table->dropColumn('national_charges_class_3');
        });
        Schema::table('corporate_standard_return_charges', function (Blueprint $table) {
            $table->renameColumn('national_charges_class_0','national')->change();
            $table->dropColumn('national_charges_class_1');
            $table->dropColumn('national_charges_class_2');
            $table->dropColumn('national_charges_class_3');
        });
    }
}
