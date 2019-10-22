<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateRidersTableAddPinAndApiToken extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('riders', function (Blueprint $table) {
            $table->string('pin')->default(bcrypt(5412));
            $table->unique('phone', 'riders_phone_unique');
            $table->string('api_token', 100)->nullable()->default(NULL);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('riders', function (Blueprint $table) {
            $table->dropColumn('pin');
            $table->dropUnique('riders_phone_unique');
            $table->dropColumn('api_token');
        });
    }
}
