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
        Schema::create('international_zonal_margin_columns', function (Blueprint $table) {
            $table->id();
            $table->integer('type')->default(0);
            $table->text('zone_column')->nullable();
            $table->text('margin_column')->nullable();
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
        Schema::dropIfExists('international_zonal_margin_columns');
    }
};
