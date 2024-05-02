<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTraxCnIssueToRidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trax_cn_issue_to_riders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('admin_store_id')->nullable();
            $table->string('company_code')->nullable();
            $table->integer('rider_id');
            $table->integer('product_id');
            $table->integer('arae_code')->nullable();
            $table->bigInteger('cn_from');
            $table->bigInteger('cn_to');
            $table->integer('quantity')->nullable();
            $table->date('issue_date');
            $table->smallInteger('item_type')->nullable();
            $table->smallInteger('status')->default(1);
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
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
        Schema::dropIfExists('trax_cn_issue_to_riders');
    }
}
