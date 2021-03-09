<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateReturnNoteShipmentsForAppUpdate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('return_note_shipments', function (Blueprint $table) {
            $table->tinyInteger('update_type')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('return_note_shipments', function (Blueprint $table) {
            $table->dropColumn('update_type');
        });
    }
}
