<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('payment_mode_logs')) {
            Schema::create('payment_mode_logs', function (Blueprint $table) {
                $table->id();
                $table->integer('shipment_id')->index();
                $table->integer('user_id')->index();
                $table->integer('payment_mode_id');
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
        Schema::dropIfExists('payment_mode_logs');
    }
};
