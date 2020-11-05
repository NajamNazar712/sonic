<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUserDocumentAttachmentsTableForUploadedAt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_document_attachments', function (Blueprint $table) {
            $table->timestamp('uploaded_at')->nullable();
            $table->integer('uploaded_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->integer('approved_by')->nullable();
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
            $table->dropColumn('uploaded_at');
            $table->dropColumn('uploaded_by');
            $table->dropColumn('approved_at');
            $table->dropColumn('approved_by');
        });
    }
}
