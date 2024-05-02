<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCronDonePayment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('cron_done_payment')) {
            // If the table doesn't exist, create it
            Schema::create('cron_done_payment', function (Blueprint $table) {
                $table->increments('id');
                $table->bigInteger('tracking_number')->nullable();
                $table->tinyInteger('status')->nullable()->default(1);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cron_done_payment');
    }
}
