<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHandoverResponsibilitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('handover_responsibilities', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('hub_id');
            $table->string('name');
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->integer('status_id');
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
        Schema::dropIfExists('handover_responsibilities');
    }
}
