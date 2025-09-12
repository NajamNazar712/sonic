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
        Schema::create('corporate_user_on_delivered_invoice_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->index();
            $table->integer('admin_id')->index();
            $table->integer('rate_type_id');
            $table->boolean('status');
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
        Schema::dropIfExists('corporate_user_on_delivered_invoice_logs');
    }
};
