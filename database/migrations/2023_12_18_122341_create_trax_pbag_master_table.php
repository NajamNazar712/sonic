<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTraxPbagMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trax_pbag_masters', function (Blueprint $table) {
            $table->increments('id');
            $table->string('pbag_manifest_no')->nullable();
            $table->integer('origin_id');
            $table->integer('destination_id');
            $table->date('pbag_date');
            $table->integer('pbag_type')->nullable();
            $table->string('barcode_mfst_no')->nullable();
            $table->string('sack_bag_no')->nullable();
            $table->integer('product_id');
            $table->integer('quantity')->nullable();
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
        Schema::dropIfExists('trax_pbag_master');
    }
}
