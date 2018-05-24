<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateFuelSurchargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fuel_surcharges', function (Blueprint $table) {
            $table->float('fuel_surcharge')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fuel_surcharges', function (Blueprint $table) {
            $table->decimal('fuel_surcharge',10,2);
        });
    }
}
