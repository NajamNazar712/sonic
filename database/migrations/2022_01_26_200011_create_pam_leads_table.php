<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePamLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pam_leads', function (Blueprint $table) {
            $table->increments('id');
            $table->string('lead_id')->unique();
            $table->string('name');
            $table->string('phone');
            $table->string('video_link')->nullable();
            $table->string('images')->nullable();
            $table->integer('origin_id')->index();
            $table->integer('destination_id')->index();
            $table->integer('location_type');
            $table->integer('case_type');
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
        Schema::dropIfExists('pam_leads');
    }
}
