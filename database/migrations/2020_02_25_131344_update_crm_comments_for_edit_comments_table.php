<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCrmCommentsForEditCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('crm_comments', function (Blueprint $table) {
            $table->timestamp('comment_updated_at')->nullable();
            $table->integer('comment_updated_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('crm_comments', function (Blueprint $table) {
            $table->dropColumn('comment_updated_at');
            $table->dropColumn('comment_updated_by');
        });
    }
}
