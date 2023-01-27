<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateRetailFranchiseAndTraxcenterForDiscountField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('retail_franchises', function (Blueprint $table) {
            $table->decimal('discount',8,2)->nullable();
        });
        Schema::table('retail_trax_centers', function (Blueprint $table) {
            $table->decimal('discount',8,2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('retail_franchises', function (Blueprint $table) {
            $table->dropColumn('discount');
        });
        Schema::table('retail_trax_centers', function (Blueprint $table) {
            $table->dropColumn('discount');
        });
    }
}
