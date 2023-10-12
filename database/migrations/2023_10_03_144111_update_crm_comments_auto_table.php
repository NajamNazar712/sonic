<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCrmCommentsAutoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('crm_comments', function (Blueprint $table) {
            $table->boolean('manual_comment')->after('comment')->default(0);
        });
    }

    public function down()
    {
        Schema::table('crm_comments', function (Blueprint $table) {
            $table->dropColumn('manual_comment');
        });
    }
}
