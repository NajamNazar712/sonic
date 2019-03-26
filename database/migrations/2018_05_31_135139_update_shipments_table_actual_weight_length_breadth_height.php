<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateShipmentsTableActualWeightLengthBreadthHeight extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->decimal('actual_weight', 8, 2)->nullable()->default(NULL)->after('estimated_weight');
            $table->decimal('length', 8, 2)->nullable()->default(NULL)->after('actual_weight');
            $table->decimal('breadth', 8, 2)->nullable()->default(NULL)->after('length');
            $table->decimal('height', 8, 2)->nullable()->default(NULL)->after('breadth');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn('actual_weight');
            $table->dropColumn('length');
            $table->dropColumn('breadth');
            $table->dropColumn('height');
        });
    }
}
