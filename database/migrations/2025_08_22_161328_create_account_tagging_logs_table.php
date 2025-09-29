<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_tagging_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('account_id')->index();
            $table->integer('changed_by_id')->nullable()->index();
            $table->integer('prev_sales_user_id')->nullable()->index();
            $table->integer('new_sales_user_id')->nullable()->index();
            $table->smallInteger('type')->default('1');
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
        Schema::dropIfExists('account_tagging_logs');
    }
};
