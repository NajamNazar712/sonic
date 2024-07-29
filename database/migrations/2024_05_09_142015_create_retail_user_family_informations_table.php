<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRetailUserFamilyInformationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('retail_user_family_informations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('retail_user_id')->nullable();
            $table->string('retail_user_trax_id')->nullable();
            $table->string('family_member_name')->nullable();
            $table->integer('family_member_type')->nullable();
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
        Schema::dropIfExists('retail_user_family_informations');
    }
}
