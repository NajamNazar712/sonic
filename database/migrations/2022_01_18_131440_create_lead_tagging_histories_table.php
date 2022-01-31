<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeadTaggingHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lead_tagging_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sale_person_id')->index();
            $table->integer('lead_id')->index();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
            //1 for tagged 0 for untagged
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lead_tagging_histories');
    }
}
