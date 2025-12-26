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
        Schema::create('negative_payable_allow_shipper_zero_cod_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->index();
            $table->integer('added_by')->index();
            $table->timestamp('added_at');
            $table->integer('removed_by')->nullable()->index();
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
        Schema::dropIfExists('negative_payable_allow_shipper_zero_cod_logs');
    }
};
