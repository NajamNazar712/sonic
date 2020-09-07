<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShipmentPiecesRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipment_pieces_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shipment_id');
            $table->integer('added_by');
            $table->integer('status')->default(1)->comment = '1 - Pending, 2 - Resolved';
            $table->integer('request_status_id')->nullable();
            $table->integer('last_updated_by_admin')->nullable();
            $table->integer('last_updated_by_user')->nullable();
            $table->timestamp('last_updated_at')->nullable();
            $table->integer('department_id')->nullable();
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
        Schema::dropIfExists('shipment_pieces_requests');
    }
}
