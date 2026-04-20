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
        Schema::create('t_payment_cycles', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->index()->unique();
            $table->string('value');
            $table->integer('added_by')->index();
            $table->dateTime('added_at');
            $table->integer('approved_by')->index();
            $table->dateTime('approved_at');
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
        Schema::dropIfExists('t_payment_cycles');
    }
};
