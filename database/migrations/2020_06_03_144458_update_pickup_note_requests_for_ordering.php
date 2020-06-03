<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePickupNoteRequestsForOrdering extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('v2_pickup_note_requests', function (Blueprint $table) {
            $table->integer('status')->default(0);
            $table->integer('ordering')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('v2_pickup_note_requests', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('ordering');
        });
    }
}
