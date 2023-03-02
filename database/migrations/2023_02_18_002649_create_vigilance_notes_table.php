<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVigilanceNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vigilance_notes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('rider_id')->index();
            $table->tinyInteger('vigilance_note_type_id')->index();
            $table->integer('total_shipments_count')->index();
            $table->integer('verify_shipments_count')->index();
            $table->integer('excess_shipments_count')->index();
            $table->integer('created_by')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vigilance_notes');
    }
}
