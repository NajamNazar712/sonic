<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackagingMaterialRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packaging_material_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('city_id');
            $table->integer('small_flyers')->default(0);
            $table->integer('medium_flyers')->default(0);
            $table->integer('large_flyers')->default(0);
            $table->integer('boxes')->default(0);
            $table->string('address');
            $table->string('poc');
            $table->string('phone');
            $table->integer('packaging_payment_mode_id');
            $table->boolean('status')->default(0);
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
        Schema::dropIfExists('packaging_material_requests');
    }
}
