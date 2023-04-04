<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeliveryCashCollectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_cash_collections', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('delivery_note_id')->index();
            $table->integer('amount');
            $table->integer('collected_by')->index();
            $table->string('remarks');
            $table->string('deposit_slip');
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
        Schema::dropIfExists('delivery_cash_collections');
    }
}
