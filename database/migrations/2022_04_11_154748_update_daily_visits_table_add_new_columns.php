<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateDailyVisitsTableAddNewColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('daily_visits', function (Blueprint $table) {
            $table->integer('shipper_id')->index();
            $table->integer('rating_id')->index()->nullable();
            $table->text('comment')->nullable();
            $table->boolean('rated')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
