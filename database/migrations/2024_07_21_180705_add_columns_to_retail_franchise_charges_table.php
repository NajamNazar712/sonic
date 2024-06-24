<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToRetailFranchiseChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('retail_franchise_charges', function (Blueprint $table) {
            $table->integer('bank_id')->nullable()->after('license_fees');
            $table->string('bank_name')->nullable()->after('bank_id');
            $table->string('cheque_number')->nullable()->after('bank_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('retail_franchise_charges', function (Blueprint $table) {
            $table->dropColumn('bank_id');
            $table->dropColumn('bank_name');
            $table->dropColumn('cheque_number');
        });
    }
}
