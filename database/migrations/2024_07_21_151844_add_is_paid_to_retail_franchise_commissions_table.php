<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsPaidToRetailFranchiseCommissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('retail_franchise_commissions', function (Blueprint $table) {
            $table->boolean('is_paid')->default(0)->after('deduction_amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('retail_franchise_commissions', function (Blueprint $table) {
            $table->dropColumn('is_paid');
        });
    }
}
