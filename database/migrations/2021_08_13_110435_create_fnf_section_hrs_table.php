<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFnfSectionHrsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fnf_section_hrs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('fnf_id')->index();
            $table->double('medical', 8, 2)->nullable();
            $table->double('notice_period', 8, 2)->nullable();
            $table->double('penalty', 8, 2)->nullable();
            $table->string('van_deduction')->nullable();
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
        Schema::dropIfExists('fnf_section_hrs');
    }
}
