<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeLeavesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_leaves', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('employee_id')->index();
            $table->integer('employee_type_id')->index();
            $table->integer('reporter_id')->index();
            $table->date('from');
            $table->date('to')->nullable();
            $table->integer('updated_by')->index()->nullable();
            $table->string('applied_reason', 500);
            $table->string('rejected_reason', 500)->nullable();
            $table->integer('status')->default(1)->index();
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
        Schema::dropIfExists('employee_leaves');
    }
}
