<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateRetailShipmentPackagingCharges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('retail_shipments', function (Blueprint $table) {
           $table->decimal('packaging_charges',8,2)->nullable();
           $table->decimal('insurance_charges',8,2)->nullable();
        });

        Schema::table('retail_franchises', function (Blueprint $table) {
            $table->decimal('insurance',8,2)->default(1);
        });

        Schema::table('retail_trax_centers', function (Blueprint $table) {
            $table->decimal('insurance',8,2)->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('retail_shipments', function (Blueprint $table) {
            $table->dropColumn('packaging_charges');
            $table->dropColumn('insurance_charges');
        });

        Schema::table('retail_franchises', function (Blueprint $table) {
            $table->dropColumn('insurance');
        });

        Schema::table('retail_trax_centers', function (Blueprint $table) {
            $table->dropColumn('insurance');
        });
    }
}
