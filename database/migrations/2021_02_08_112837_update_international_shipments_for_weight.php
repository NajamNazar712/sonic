<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateInternationalShipmentsForWeight extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('international_shipments', function (Blueprint $table) {
            $table->decimal('actual_weight', 8,2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('international_shipments', function (Blueprint $table) {
            $table->dropColumn('actual_weight');
        });
    }
}
