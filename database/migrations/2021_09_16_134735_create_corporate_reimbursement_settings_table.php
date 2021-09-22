<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCorporateReimbursementSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('corporate_reimbursement_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->index();
            $table->boolean('setting_on')->default(0);
            $table->boolean('setting_display')->default(0); // Dummy value to show on admin side
            $table->date('starting_date')->nullable();
            $table->date('ending_date')->nullable();
            $table->tinyInteger('status')->default('2'); // 1 Means Updated 2 Means Approved
            $table->bigInteger('updated_by')->nullable();
            $table->bigInteger('approved_by')->nullable();
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
        Schema::dropIfExists('corporate_reimbursement_settings');
    }
}
