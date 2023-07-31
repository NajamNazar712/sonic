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
            $table->decimal('fintech_charges', 8, 2);
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
        Schema::dropIfExists('users_fintech_charges');
    }
}
