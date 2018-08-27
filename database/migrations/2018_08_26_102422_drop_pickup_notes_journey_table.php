<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropPickupNotesJourneyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('pickup_notes_journey');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pickup_notes_journey', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('pickup_id');
            $table->integer('status_id');
            $table->text('remarks')->nullable(FALSE)->default(NULL);
            $table->integer('admin_id')->nullable(FALSE)->default(NULL);
        });
    }
}
