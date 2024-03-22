<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTraxPbagDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trax_pbag_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pbag_master_id');
            $table->integer('cn_number');
            $table->integer('product_id');
            $table->integer('service_id')->nullable();
            $table->integer('origin_id');
            $table->integer('destination_id');
            $table->string('handling_inst')->nullable();
            $table->integer('no_piece')->nullable();
            $table->decimal('weight')->nullable();
            $table->decimal('amount')->nullable();
            $table->tinyInteger('user_type')->nullable(); // 1 - Admin, 2 - Rider, 0 -> shipper
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
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
        Schema::dropIfExists('trax_pbag_detail');
    }
}
