<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateRetailFranchiseAndTraxCenterForPickupAddressIdTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('retail_franchises', function (Blueprint $table) {
            $table->integer('pickup_address_id')->nullable()->index();
        });
        Schema::table('retail_trax_centers', function (Blueprint $table) {
            $table->integer('pickup_address_id')->nullable()->index();
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
            $table->dropColumn('pickup_address_id');
        });
        Schema::table('retail_trax_centers', function (Blueprint $table) {
            $table->dropColumn('pickup_address_id');
        });
    }
}
