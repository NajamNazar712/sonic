<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIssueSackbagOriginsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('issue_sack_bag_origins', function (Blueprint $table) {
            $table->increments('id');
            $table->string('sack_bag_no');
            $table->integer('origin');
            $table->integer('user_id');
            $table->string('remarks')->nullable();
            $table->tinyInteger('status')->default(1); //1 active
            $table->tinyInteger('action')->default(0);
            $table->tinyInteger('type')->nullable(); //1 admin
            $table->integer('created_by')->nullable();
            $table->integer('udpated_by')->nullable();
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
        Schema::dropIfExists('issue_sackbag_origins');
    }
}
