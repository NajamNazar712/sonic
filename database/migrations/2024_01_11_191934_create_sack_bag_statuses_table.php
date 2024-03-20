<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSackBagStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sack_bag_statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('reference_id')->nullable();;
            $table->string('reference_type')->nullable();
            $table->string('desc')->nullable();
            $table->tinyInteger('status')->default(1);
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
        Schema::dropIfExists('sack_bag_statuses');
    }
}
