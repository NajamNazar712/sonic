<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('city_etds', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('from_etd_city_id');
            $table->unsignedBigInteger('to_etd_city_id');
            $table->string('range');  // e.g., "1-2"
            $table->string('label');  // e.g., "1 - 2 Working Days"
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('city_etds');
    }
};
