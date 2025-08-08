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
        Schema::create('shipments_geo_codes', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->index();
            $table->integer('shipment_id')->index();
            $table->tinyInteger('geo_code_type')->default(1)->index();
            $table->string('latitude');
            $table->string('longitude');
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
        Schema::dropIfExists('shipments_geo_codes');
    }
};
