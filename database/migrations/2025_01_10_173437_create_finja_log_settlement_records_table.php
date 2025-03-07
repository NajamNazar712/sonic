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
        Schema::create('finja_log_settlement_records', function (Blueprint $table) {
            $table->id();
            $table->integer('shipment_id')->index();
            $table->boolean('wallet_log_updated')->default(0);
            $table->boolean('wallet_settlement_updated')->default(0);
            $table->boolean('wallet_adjustment_updated')->default(0);
            $table->timestamp('wallet_log_updated_at')->nullabe();
            $table->timestamp('wallet_settlement_updated_at')->nullabe();
            $table->timestamp('wallet_adjustment_updated_at')->nullabe();
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
        Schema::dropIfExists('finja_log_settlement_records');
    }
};
