<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCrmCommentsLenght extends Migration
{
    public function up()
    {
        Schema::table('crm_comments', function (Blueprint $table) {
            $table->text('comment')->change();
        });
    }

    public function down()
    {
        Schema::table('crm_comment', function (Blueprint $table) {
            $table->string('comment')->change();
        });
    }
}
