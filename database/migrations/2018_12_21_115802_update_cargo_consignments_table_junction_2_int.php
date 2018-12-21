<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCargoConsignmentsTableJunction2Int extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cargo_consignments', function (Blueprint $table) {
            $table->integer('junction_hub_2_id')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cargo_consignments', function (Blueprint $table) {
            $table->tinyInteger('junction_hub_2_id')->change();
        });
    }
}
