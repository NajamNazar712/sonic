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
        Schema::create('transfer_note_shipments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transfer_note_id')->index();
            $table->unsignedBigInteger('shipment_id')->index();
            $table->unsignedBigInteger('notification')->nullable();
            $table->tinyInteger('rider_information')->nullable();
            $table->text('remarks')->nullable();
            $table->unsignedTinyInteger('status_id')->default(1);
            $table->unsignedBigInteger('last_status_updated_by')->nullable();
            $table->timestamp('last_status_updated_at')->nullable();
            $table->tinyInteger('last_status_updated_type')->default(1)->comment('1-Admin,2-Rider,3-Retail');
            $table->tinyInteger('ordering')->default(0);
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
        Schema::dropIfExists('transfer_note_shipments');
    }
};
