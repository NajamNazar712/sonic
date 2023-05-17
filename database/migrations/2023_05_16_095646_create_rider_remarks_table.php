<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRiderRemarksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rider_remarks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('rider_id')->index();
            $table->longText('rider_remarks');
            $table->longText('response_1')->nullable();
            $table->integer('rider_remarks_status_id')->default(1);
            $table->longText('response_2')->nullable();
            $table->integer('updated_by')->nullable()->index();
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
        Schema::dropIfExists('rider_remarks');
    }
}
