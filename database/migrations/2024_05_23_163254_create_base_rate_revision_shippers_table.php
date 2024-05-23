<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBaseRateRevisionShippersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('base_rate_revision_shippers', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('base_rate_revision_id');
            $table->foreign('base_rate_revision_id')->references('id')->on('base_rate_revisions');
            $table->unsignedInteger('shipper_id');
            $table->float('rate_change_percent');
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
        Schema::dropIfExists('base_rate_revision_shippers');
    }
}
