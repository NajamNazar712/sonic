<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateAddColumnShipmentsJourney extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shipments_journey', function (Blueprint $table) {
           $table->boolean('verification')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shipments_journey', function (Blueprint $table) {
            $table->dropColumn('verification');
        });
    }
}
