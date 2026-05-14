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
        Schema::create('pending_t_payment_cycles', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->index();
            $table->string('value');
            $table->integer('added_by')->index();
            $table->dateTime('added_at');
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
        Schema::dropIfExists('pending_t_payment_cycles');
    }
};
