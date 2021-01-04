<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRetailFranchisesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('retail_franchises', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('phone_no');
            $table->string('email');
            $table->string('cnic');
            $table->integer('default_hub')->index();
            $table->integer('updated_by')->index();
            $table->integer('status')->index();
            $table->string('code')->nullbale();
            $table->string('location_latitude')->nullable();
            $table->string('location_longitude')->nullable();
            $table->integer('user_id')->nullable();
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
        Schema::dropIfExists('retail_franchises');
    }
}
