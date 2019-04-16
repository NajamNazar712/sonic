<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJunctionMappingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('junction_mappings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('origin_id');
            $table->integer('destination_id');
            $table->integer('junction_1');
            $table->integer('junction_2')->nullable();
            $table->integer('receiver')->nullable();
            $table->integer('updated_by');
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
        Schema::dropIfExists('junction_mappings');
    }
}
