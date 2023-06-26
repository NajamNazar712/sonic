<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateV3RegularPickupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('v3_regular_pickups', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shipper_id')->index();
            $table->integer('pickup_address_id')->index();
            $table->tinyInteger('pickup')->default(0)->index();
            $table->tinyInteger('approval')->default(0)->index(); //1 - approved, 2 - rejected
            $table->timestamps();
            $table->index(['created_at', 'updated_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('v3_regular_pickups');
    }
}
