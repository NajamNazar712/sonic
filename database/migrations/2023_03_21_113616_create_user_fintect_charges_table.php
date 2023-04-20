<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserFintectChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_fintech_charges', function (Blueprint $table) {
            $table->increments('id');
            $table->Integer('user_id');
            $table->Integer('status')->default('1');
            $table->string('fintech_charges');
            $table->Integer('added_by');
            $table->Integer('updated_by');
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
        Schema::dropIfExists('user_fintect_charges');
    }
}
