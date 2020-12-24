<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMonthClosingResponsiblesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('month_closing_responsibles', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('month_closing_id')->index();
            $table->integer('responsible_person_id')->index();
            $table->integer('amount');
            $table->integer('added_by')->index();
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
        Schema::dropIfExists('month_closing_responsibles');
    }
}
