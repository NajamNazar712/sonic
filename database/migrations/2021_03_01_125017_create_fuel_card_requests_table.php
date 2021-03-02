<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFuelCardRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fuel_card_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->string('card_number')->nullable()->index();
            $table->integer('card_holder_id')->index();
            $table->integer('card_holder_type_id')->index()->nullable();
            $table->integer('card_request_type_id')->index();
            $table->integer('fuel_deduction_type_id')->nullable();
            $table->integer('fuel_type_id')->nullable();
            $table->integer('amount')->nullable();
            $table->integer('requested_by')->index();
            $table->integer('approved_by')->index()->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->integer('status')->default(0);
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
        Schema::dropIfExists('fuel_card_requests');
    }
}
