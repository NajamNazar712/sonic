<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFnfSectionFinancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fnf_section_finances', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('fnf_id')->index();
            $table->double('advance_salary', 8, 2)->nullable();
            $table->double('loan_outstanding', 8, 2)->nullable();
            $table->double('short_cash', 8, 2)->nullable();
            $table->double('cod_recovery', 8, 2)->nullable();
            $table->double('iou', 8, 2)->nullable();
            $table->double('tax', 8, 2)->nullable();
            $table->string('comments')->nullable();
            $table->integer('created_by')->nullable()->index();
            $table->integer('status_id')->index();
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
        Schema::dropIfExists('fnf_section_finances');
    }
}
