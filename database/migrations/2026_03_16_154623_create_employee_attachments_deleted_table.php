<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_attachments_deleted', function (Blueprint $table) {
            $table->id();
            $table->integer('employee_id')->index();
            $table->string('cv')->nullable();
            $table->string('cnic')->nullable();
            $table->string('photo')->nullable();
            $table->string('academic')->nullable();
            $table->string('experience')->nullable();
            $table->string('last_pay_slip')->nullable();
            $table->string('nikkah_nama')->nullable();
            $table->string('cnic_spouse')->nullable();
            $table->string('child_b_form')->nullable();
            $table->string('cnic_nominee')->nullable();
            $table->string('utility_bill')->nullable();
            $table->string('affidavit')->nullable();
            $table->string('cheque')->nullable();
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
        Schema::dropIfExists('employee_attachments_deleted');
    }
};
