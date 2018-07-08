<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateAddStatusReturnNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('return_notes', function (Blueprint $table) {
            $table->integer('route_id')->after('rider_id');
            $table->boolean('status')->default(0);
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
            $table->dropColumn('route_id');
            $table->dropColumn('status');
        });
    }
}
