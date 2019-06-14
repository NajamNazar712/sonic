<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCrmRequestCaseNatureAndTypeHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crm_request_case_nature_and_type_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('crm_request_id');
            $table->integer('case_nature_id');
            $table->integer('case_nature_type_id')->nullable();
            $table->integer('edited_by');
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
        Schema::dropIfExists('crm_request_case_nature_and_type_histories');
    }
}
