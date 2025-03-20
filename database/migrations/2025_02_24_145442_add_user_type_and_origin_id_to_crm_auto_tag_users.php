<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('crm_auto_tag_users', function (Blueprint $table) {
            $table->tinyInteger('user_type')->nullable();
            $table->integer('origin_id')->index()->nullable();
        });
    }

    public function down()
    {
        Schema::table('crm_auto_tag_users', function (Blueprint $table) {
            $table->dropColumn(['user_type', 'origin_id']);
        });
    }
};

