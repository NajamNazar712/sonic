<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateReturnNotesForCreatedViaAppBit extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('return_notes', function (Blueprint $table) {
            $table->tinyInteger('created_via_app')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('return_notes', function (Blueprint $table) {
            $table->dropColumn('created_via_app');
        });
    }
}
