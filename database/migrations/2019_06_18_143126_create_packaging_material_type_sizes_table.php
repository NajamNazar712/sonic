<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackagingMaterialTypeSizesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packaging_material_type_sizes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('size');
            $table->string('type_id');
            $table->integer('quantity');
            $table->float('standard_charges');
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
        Schema::dropIfExists('packaging_material_type_sizes');
    }
}
