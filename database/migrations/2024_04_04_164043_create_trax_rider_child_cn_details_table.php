<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTraxRiderChildCnDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trax_rider_child_cn_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('child_cn_issue_id')->nullable()->index();
            $table->integer('cn_number')->nullable();
            $table->tinyInteger('is_used')->default(0);
            $table->tinyInteger('is_hold')->default(0);
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
        Schema::dropIfExists('trax_rider_child_cn_details');
    }
}
