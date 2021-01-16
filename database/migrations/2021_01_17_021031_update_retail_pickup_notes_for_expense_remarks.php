<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateRetailPickupNotesForExpenseRemarks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('retail_pickup_notes', function (Blueprint $table) {
            $table->bigInteger('expense')->nullable();
            $table->bigInteger('net_amount')->nullable();
            $table->string('remarks')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('retail_pickup_notes', function (Blueprint $table) {
            $table->dropColumn('expense');
            $table->dropColumn('net_amount');
            $table->dropColumn('remarks');
        });
    }
}
