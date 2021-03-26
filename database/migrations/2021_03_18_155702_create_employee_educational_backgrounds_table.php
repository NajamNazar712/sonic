<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeEducationalBackgroundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_educational_backgrounds', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('employee_id')->index();
            $table->string('name');
            $table->string('degree');
            $table->string('grade');
            $table->timestamp('passing_year');
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
        Schema::dropIfExists('employee_educational_backgrounds');
    }
}
