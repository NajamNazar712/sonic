<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRetailPickupNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('retail_pickup_notes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pickup_address_id')->index();
            $table->integer('hub_id')->index();
            $table->integer('retail_user_id')->index();
            $table->integer('pickup_request_id');
            $table->integer('rider_id')->nullable()->index();
            $table->integer('shipments');
            $table->integer('amount');
            $table->integer('assigned_by')->nullable()->index();
            $table->timestamp('assigned_at')->nullable()->index();
            $table->integer('status')->default(1)->index();
            $table->integer('cash_collected_by')->nullable()->index();
            $table->timestamp('cash_collected_at')->nullable()->index();
            $table->integer('pncc_status')->default(0)->index();
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
        Schema::dropIfExists('retail_pickup_notes');
    }
}
