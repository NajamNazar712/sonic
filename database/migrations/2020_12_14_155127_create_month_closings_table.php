<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMonthClosingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('month_closings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shipment_id')->index();
            $table->tinyInteger('status_id')->default(1);
            $table->integer('added_by')->index();
            $table->timestamp('closing_date')->nullable();
            $table->tinyInteger('closing_type_id')->nullable();
            $table->integer('updated_by')->nullable()->index();
            $table->timestamp('closing_updated_at')->index();
            $table->string('remarks')->nullable();
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
        Schema::dropIfExists('month_closings');
    }
}
