<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeConfirmationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_confirmations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('employee_id')->index();
            $table->integer('probation_form')->default(0);
            $table->integer('status')->default(1); 
            $table->date('probation_end_date')->nullable();
            $table->integer('increment')->default(0);
            $table->string('approve_reason')->nullable();
            $table->string('reject_reason')->nullable();
            $table->integer('approve_by_lm')->index()->nullable();
            $table->date('approve_by_lm_at')->nullable();
            $table->integer('approve_by_hod')->index()->nullable();
            $table->date('approve_by_hod_at')->nullable();
            $table->integer('approve_by_hr')->index()->nullable();
            $table->date('approve_by_hr_at')->nullable();
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
        Schema::dropIfExists('employee_confirmations');
    }
}
