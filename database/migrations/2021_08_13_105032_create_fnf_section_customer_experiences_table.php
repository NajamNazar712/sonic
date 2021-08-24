<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFnfSectionCustomerExperiencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fnf_section_customer_experiences', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('fnf_id')->index();
            $table->double('call_deduction', 8, 2)->nullable();
            $table->double('parcel', 8, 2)->nullable();
            $table->double('fake_status', 8, 2)->nullable();
            $table->double('month_closing', 8, 2)->nullable();
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
        Schema::dropIfExists('fnf_section_customer_experiences');
    }
}
