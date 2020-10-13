<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OpenParcelHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('open_parcel_history', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shipment_id');
            $table->integer('user_id');
            $table->text('remarks')->nullable()->default(NULL);
            $table->integer('amount');
            $table->date('date');
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
        Schema::dropIfExists('open_parcel_history');
    }
}
