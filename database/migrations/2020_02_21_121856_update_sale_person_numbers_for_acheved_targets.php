<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateSalePersonNumbersForAchevedTargets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sale_person_numbers', function (Blueprint $table) {
            $table->bigInteger('target_shipments')->nullable();
            $table->bigInteger('target_revenue')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sale_person_numbers', function (Blueprint $table) {
            $table->dropColumn('target_shipments');
            $table->dropColumn('target_revenue');
        });
    }
}
