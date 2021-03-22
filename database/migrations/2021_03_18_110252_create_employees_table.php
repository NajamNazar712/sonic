<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('phone_number')->index();
            $table->string('trax_id')->nullable()->index();
            $table->string('pin');
            $table->string('city_id')->index();
            $table->string('cnic');
            $table->integer('employee_gender_id')->default(1);
            $table->integer('employee_type_id')->index();
            $table->tinyInteger('status_id')->index()->default(1);
            $table->tinyInteger('request_status_id')->index()->default(1);
            $table->string('guardian_name')->nullable();
            $table->string('blood_group')->nullable();
            $table->string('personal_email')->nullable();
            $table->string('address')->nullable();
            $table->string('emergency_contact')->nullable();
            $table->timestamp('cnic_issue_date')->nullable();
            $table->timestamp('cnic_expiry_date')->nullable();
            $table->integer('designation')->index()->nullable();
            $table->integer('department_id')->nullable()->index();
            $table->string('official_email')->nullable();
            $table->string('official_phone_number')->nullable()->index();
            $table->integer('request_updated_by')->nullable()->index();
            $table->timestamp('request_updated_at')->nullable();
            $table->integer('approved_rejected_by')->nullable()->index();
            $table->timestamp('approved_at')->nullable();
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
        Schema::dropIfExists('employees');
    }
}
