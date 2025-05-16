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
        Schema::table('finga_api_logs', function (Blueprint $table) {
            $table->string('nature')->nullable()->default(null)->change(); 
            $table->integer('nature_id')->default(0)->after('nature')->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('finga_api_logs', function (Blueprint $table) {
            $table->dropColumn('nature_id');
        });
    }
};
