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
        Schema::create('user_document_attachments_deleted', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('filled_and_signed_pdf')->nullable();
            $table->string('signed_acknowledgement_pdf')->nullable();
            $table->string('cnic_front_image')->nullable();
            $table->string('cnic_back_image')->nullable();
            $table->string('blank_cheque_image')->nullable();
            $table->timestamp('uploaded_at')->nullable();
            $table->integer('uploaded_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->integer('approved_by')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->integer('rejected_by')->index()->nullable();
            $table->string('e_sign_image')->nullable();
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
        Schema::dropIfExists('user_document_attachments_deleted');
    }
};
