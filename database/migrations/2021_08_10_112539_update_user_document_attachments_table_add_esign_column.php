<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUserDocumentAttachmentsTableAddEsignColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_document_attachments', function (Blueprint $table) {
            $table->string('e_sign_image')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_document_attachments', function (Blueprint $table) {
            $table->dropColumn('e_sign_image');
        });
    }
}
