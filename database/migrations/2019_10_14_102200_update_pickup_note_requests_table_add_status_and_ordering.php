<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePickupNoteRequestsTableAddStatusAndOrdering extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pickup_note_requests', function (Blueprint $table) {
            $table->integer('status')->default(0);
            $table->integer('ordering')->nullable()->default(NULL);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pickup_note_requests', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('ordering');
        });
    }
}
