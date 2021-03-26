<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeReferencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_references', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('employee_id')->index();
            $table->string('name')->nullable();
            $table->string('occupation')->nullable();
            $table->string('relationship')->nullable();
            $table->string('years')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('email')->nullable();
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
        Schema::dropIfExists('employee_references');
    }
}
