<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUserDocumentAttachmentTableAddNewColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_document_attachments', function (Blueprint $table) {
            $table->timestamp('rejected_at')->nullable();
            $table->integer('rejected_by')->index()->nullable();
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
            $table->dropColumn('rejected_at');
            $table->dropColumn('rejected_by');
        });
    }
}
