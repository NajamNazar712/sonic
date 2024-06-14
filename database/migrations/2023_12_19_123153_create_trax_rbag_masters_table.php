<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTraxRbagMastersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trax_rbag_masters', function (Blueprint $table) {
            $table->increments('id');
            $table->string('rbag_no')->nullable();
            $table->integer('origin_id');
            $table->integer('destination_id');
            $table->integer('quantity')->nullable();
            $table->date('rbag_date');
            $table->integer('rbag_type')->nullable();
            $table->string('rbag_seal')->nullable();
            $table->string('rmb_barcode')->nullable();
            $table->integer('pbag_qty')->nullable();
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
        Schema::dropIfExists('trax_rbag_masters');
    }
}
