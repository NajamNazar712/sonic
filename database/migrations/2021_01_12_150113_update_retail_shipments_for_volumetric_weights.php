<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateRetailShipmentsForVolumetricWeights extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('retail_shipments', function (Blueprint $table) {
            $table->decimal('weight', 8, 2)->nullable()->default(NULL);
            $table->decimal('length', 8, 2)->nullable()->default(NULL);
            $table->decimal('breadth', 8, 2)->nullable()->default(NULL);
            $table->decimal('height', 8, 2)->nullable()->default(NULL);
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
            $table->dropColumn('weight');
            $table->dropColumn('length');
            $table->dropColumn('breadth');
            $table->dropColumn('height');
        });
    }
}
