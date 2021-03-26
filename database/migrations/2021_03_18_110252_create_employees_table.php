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
            $table->string('trax_id')->nullable()->index();
            $table->string('name');
            $table->integer('employee_gender_id')->default(1);
            $table->integer('city_id')->index();
            $table->string('cnic');
            $table->string('phone_number')->index();
            $table->integer('employee_type_id')->index();
            $table->integer('user_request_id')->nullable()->index();
            $table->integer('rider_request_id')->nullable()->index();
            $table->tinyInteger('request_status_id')->index()->default(1);
            $table->tinyInteger('status_id')->index()->default(0);
            $table->string('guardian_name')->nullable();
            $table->integer('religion_id')->index()->nullable();
            $table->integer('nationality_id')->index()->nullable();
            $table->integer('domicile_id')->index()->nullable();
            $table->integer('marital_status_id')->index()->nullable();
            $table->string('blood_group')->nullable();
            $table->string('personal_email')->nullable();
            $table->string('address')->nullable();
            $table->string('emergency_contact')->nullable();
            $table->timestamp('cnic_issue_date')->nullable();
            $table->timestamp('cnic_expiry_date')->nullable();
            $table->integer('designation_id')->index()->nullable();
            $table->integer('department_id')->nullable()->index();
            $table->integer('zone_id')->nullable()->index();
            $table->string('official_email')->nullable();
            $table->string('official_phone_number')->nullable()->index();
            $table->string('sonic_id')->nullable()->index();
            $table->string('pin')->nullable();
            $table->integer('request_updated_by')->nullable()->index();
            $table->integer('approved_rejected_by')->nullable()->index();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('request_updated_at')->nullable();
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
