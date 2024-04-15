<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShipperInterceptExcludesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipper_intercept_excludes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->index();
            $table->boolean('exclude_shipper')->default(false);
            $table->boolean('different_consignee')->default(false);
            $table->boolean('same_consignee')->default(false);
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
        Schema::dropIfExists('shipper_intercept_excludes');
    }
}
