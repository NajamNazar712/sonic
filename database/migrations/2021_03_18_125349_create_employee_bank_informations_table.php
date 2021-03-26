<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeBankInformationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_bank_informations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('employee_id')->index();
            $table->string('account_title')->nullable();
            $table->string('branch_code')->nullable();
            $table->string('account_no')->nullable();
            $table->integer('bank_id')->nullable();
            $table->string('branch_name')->nullable();
            $table->string('iban')->nullable();
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
        Schema::dropIfExists('employee_bank_informations');
    }
}
