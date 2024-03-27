<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTraxCnReceiveAdminStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trax_cn_receive_admin_stores', function (Blueprint $table) {
            $table->increments('id');
            $table->string('company_code')->nullable();
            $table->string('area_code')->nullable();
            $table->integer('product_id')->nullable();
            $table->bigInteger('cn_from');
            $table->bigInteger('cn_to');
            $table->integer('quantity')->nullable();
            $table->smallInteger('item_type')->nullable();
            $table->integer('user_id')->nullable();
            $table->smallInteger('status')->default(1);
            $table->date('receive_date');
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
        Schema::dropIfExists('trax_cn_receive_admin_stores');
    }
}
