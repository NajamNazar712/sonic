<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTraxChildCnIssueToRidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trax_child_cn_issue_to_riders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('child_admin_store_id')->nullable()->index();
            $table->string('company_code')->nullable();
            $table->integer('rider_id')->index();
            $table->integer('arae_code')->nullable();
            $table->bigInteger('cn_from');
            $table->bigInteger('cn_to');
            $table->integer('quantity')->nullable();
            $table->date('issue_date')->index();
            $table->smallInteger('status')->default(1);
            $table->integer('created_by')->nullable()->index();
            $table->integer('updated_by')->nullable()->index();
            $table->softDeletes();
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
        Schema::dropIfExists('trax_child_cn_issue_to_riders');
    }
}
