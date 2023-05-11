<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeePenaltyHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_penalty_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('employee_id')->index();
            $table->date('date')->nullable();
            $table->integer('attendence_id')->index();
            $table->tinyInteger('status')->index();
            $table->integer('updated_by')->nullable()->index();
            $table->index('updated_at');
            $table->string('reject_reason')->nullable();
            $table->integer('no_of_late')->nullable();
            $table->integer('leave_without_pay')->nullable();
            $table->integer('leave_deduction')->nullable();
            $table->integer('deduction_count')->nullable();
            $table->boolean('is_current_record')->default(0);
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
        Schema::dropIfExists('employee_penalty_histories');
    }
}
