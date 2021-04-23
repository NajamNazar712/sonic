<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePendingCorporateDefaultReturnCharges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pending_corporate_default_return_charges', function (Blueprint $table) {
            $table->renameColumn('national','national_charges_class_0');
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
        Schema::table('pending_corporate_default_return_charges', function (Blueprint $table) {
            $table->renameColumn('national_charges_class_0','national');
            $table->dropColumn('national_charges_class_1');
            $table->dropColumn('national_charges_class_2');
            $table->dropColumn('national_charges_class_3');
        });
    }
}
