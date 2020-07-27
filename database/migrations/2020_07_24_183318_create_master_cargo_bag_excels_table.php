<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMasterCargoBagExcelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_cargo_bag_excels', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('master_cargo_excel_id');
            $table->integer('master_cargo_id');
            $table->integer('bag_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('master_cargo_bag_excels');
    }
}
