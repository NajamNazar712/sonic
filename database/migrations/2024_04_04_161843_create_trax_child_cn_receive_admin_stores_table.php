<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTraxChildCnReceiveAdminStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trax_child_cn_receive_admin_stores', function (Blueprint $table) {
            $table->increments('id');
            $table->string('company_code')->nullable();
            $table->string('area_code')->nullable();
            $table->bigInteger('cn_from');
            $table->bigInteger('cn_to');
            $table->integer('quantity')->nullable();
            $table->date('receive_date')->index();
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
        Schema::dropIfExists('trax_child_cn_receive_admin_stores');
    }
}
