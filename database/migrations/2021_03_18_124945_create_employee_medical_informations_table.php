<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeMedicalInformationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_medical_informations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('employee_id')->index();
            $table->string('Name');
            $table->integer('relationship_id')->index();
            $table->timestamp('date_of_birth')->nullable();
            $table->integer('marital_status')->index();
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
        Schema::dropIfExists('employee_medical_informations');
    }
}
