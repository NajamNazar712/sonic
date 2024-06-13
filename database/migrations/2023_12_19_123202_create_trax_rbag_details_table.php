<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTraxRbagDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trax_rbag_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('origin_id');
            $table->integer('destination_id');
            $table->integer('pbag_id');
            $table->string('handling_inst')->nullable();
            $table->decimal('weight')->nullable();
            $table->integer('no_piece')->nullable();
            $table->integer('from_pcs')->nullable();
            $table->integer('to_pcs')->nullable();
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
        Schema::dropIfExists('trax_rbag_details');
    }
}
