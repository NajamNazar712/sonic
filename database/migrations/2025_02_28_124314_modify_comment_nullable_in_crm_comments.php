<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('crm_comments', function (Blueprint $table) {
            $table->text('comment')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('crm_comments', function (Blueprint $table) {
            $table->text('comment')->nullable(false)->change();
        });
    }
};
