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
        Schema::create('api_handshake_logs', function (Blueprint $table) {
            $table->id();
            $table->string('service_name');              // marco
            $table->string('endpoint');
            $table->string('method', 10);
            $table->string('reference_id')->nullable();  // sku_id / order_id
            $table->json('request_payload')->nullable();
            $table->json('response_payload')->nullable();
            $table->integer('response_status')->nullable();
            $table->enum('status', ['pending', 'success', 'failed'])->default('pending');
            $table->text('error_message')->nullable();
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
        Schema::dropIfExists('api_handshake_logs');
    }
};
