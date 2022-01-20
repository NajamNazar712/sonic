<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReattemptPercentageForShippersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reattempt_percentage_for_shippers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->index();
            $table->decimal('percentage');
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
        Schema::dropIfExists('reattempt_percentage_for_shippers');
    }
}
