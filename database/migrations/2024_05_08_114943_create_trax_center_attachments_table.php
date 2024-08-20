<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTraxCenterAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trax_center_attachments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('retail_trax_center_id')->nullable();
            $table->integer('advance_amount')->nullable();
            $table->integer('rental')->nullable();
            $table->string('landlord_name')->nullable();
            $table->string('landlord_contact_number')->nullable();
            $table->string('shop_address')->nullable();
            $table->string('agreement_start_date')->nullable();
            $table->string('agreement_end_date')->nullable();
            $table->string('attachment_1')->nullable();
            $table->string('attachment_2')->nullable();
            $table->string('attachment_3')->nullable();
            $table->string('attachment_4')->nullable();
            $table->string('attachment_5')->nullable();
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
        Schema::dropIfExists('trax_center_attachments');
    }
}
