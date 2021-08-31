<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserDocumentAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_document_attachments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('cnic_front_image')->nullable();
            $table->string('cnic_back_image')->nullable();
            $table->string('blank_cheque_image')->nullable();
            $table->timestamps();
        });
        Schema::table('users', function (Blueprint $table) {
            $table->integer('documents_status')->default(0);
            $table->string('documents_status_reason', 250)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_document_attachments');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('documents_status');
            $table->dropColumn('documents_status_reason');
        });
    }
}
