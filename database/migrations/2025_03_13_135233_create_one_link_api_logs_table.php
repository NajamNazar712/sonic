<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('one_link_api_logs', function (Blueprint $table) {
            $table->id();
            $table->string('endpoint');
            $table->text('request_data')->nullable();
            $table->text('response_data')->nullable();
            $table->string('status')->default('pending'); 
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('one_link_api_logs');
    }
};
