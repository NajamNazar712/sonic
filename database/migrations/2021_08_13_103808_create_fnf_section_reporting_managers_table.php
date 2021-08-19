<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFnfSectionReportingManagersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fnf_section_reporting_managers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('fnf_id')->index();
            $table->double('overtime', 8, 2)->nullable();
            $table->double('holiday', 8, 2)->nullable();
            $table->double('pickup_incentive', 8, 2)->nullable();
            $table->double('fixed_incentive', 8, 2)->nullable();
            $table->double('delivery_incentive', 8, 2)->nullable();
            $table->double('extra_duty', 8, 2)->nullable();
            $table->double('iou', 8, 2)->nullable();
            $table->double('penalty', 8, 2)->nullable();
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
        Schema::dropIfExists('fnf_section_reporting_managers');
    }
}
