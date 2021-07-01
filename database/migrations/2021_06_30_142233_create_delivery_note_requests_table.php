<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeliveryNoteRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_note_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('rider_id')->index();
            $table->integer('delivery_note_id')->index();
            $table->integer('amount')->index();
            $table->text('reason');
            $table->timestamp('requested_at')->nullable();
            $table->integer('requested_by')->index()->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->integer('approved_by')->index()->nullable();
            $table->integer('status')->index();
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
        Schema::dropIfExists('delivery_note_requests');
    }
}
