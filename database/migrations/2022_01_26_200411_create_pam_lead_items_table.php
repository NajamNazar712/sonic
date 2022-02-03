<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePamLeadItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pam_lead_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('lead_id')->index();
            $table->integer('item_id')->index();
            $table->integer('quantity');
            $table->float('length');
            $table->float('width');
            $table->float('height');
            $table->float('weight');
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
        Schema::dropIfExists('pam_lead_items');
    }
}
