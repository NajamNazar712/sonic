<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateRetailFranchiseChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('retail_franchise_charges', function (Blueprint $table) {
            $table->integer('security_deposit')->nullable()->after('franchise_withholding');
            $table->integer('license_fees')->nullable()->after('security_deposit');
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
            $table->dropColumn('security_deposit');
            $table->dropColumn('license_fees');
        });
    }
}
