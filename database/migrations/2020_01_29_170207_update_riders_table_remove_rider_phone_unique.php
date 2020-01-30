<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateRidersTableRemoveRiderPhoneUnique extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('riders', function (Blueprint $table) {
            $table->dropUnique('riders_phone_unique');
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
            $table->unique('phone', 'riders_phone_unique');
        });
    }
}
