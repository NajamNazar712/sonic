<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRiderCategoryByPassesTable extends Migration
{

    public function up()
    {
        Schema::create('rider_category_by_passes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('rider_category_id')->index();
            $table->integer('rider_id')->index();
            $table->string('reason')->nullable();
            $table->integer('status');
            $table->string('requested_by')->nullable();
            $table->string('requested_at')->nullable();
            $table->string('approved_by')->nullable();
            $table->string('approved_at')->nullable();
            $table->timestamps();
        });
    }


    public function down()
    {
        Schema::dropIfExists('rider_category_by_passes');
    }
}
