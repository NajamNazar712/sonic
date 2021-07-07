<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInternationalEconomyRateStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('international_economy_rate_statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->index();
            $table->boolean('status')->default('1'); // 1 - Updated 2 - Approved 3 - Rejected
            $table->string("reject_reason")->nullable();
            $table->integer('updated_by')->index();
            $table->timestamp('updated_on');
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
        Schema::dropIfExists('international_economy_rate_statuses');
    }
}
