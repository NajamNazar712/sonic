<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShipmentMultiplePiecesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipment_multiple_pieces', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shipment_id');
            $table->integer('added_by');
            $table->integer('status')->default(1);
            $table->integer('last_updated_by');
            $table->timestamp('last_updated_at');
            $table->integer('department_id');
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
        Schema::dropIfExists('shipment_multiple_pieces');
    }
}
