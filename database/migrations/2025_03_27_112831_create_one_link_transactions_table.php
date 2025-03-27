<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('one_link_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('rrn');
            $table->string('stan');
            $table->string('date_time');
            $table->string('message_id')->nullable();
            $table->string('original_rrn');
            $table->string('original_stan');
            $table->string('original_rtp_id');
            $table->string('merchant_id');
            $table->string('sub_dept');
            $table->string('status');
            $table->decimal('original_instructed_amount', 10, 2)->nullable();
            $table->decimal('net_amount', 10, 2)->nullable();
            $table->string('iban')->nullable();
            $table->string('account_title')->nullable();
            $table->string('longitude')->nullable();
            $table->string('latitude')->nullable();
            $table->tinyInteger('nature_id')->comment('1 = Merchant Notification, 2 = Payment Notification');
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('one_link_transactions');
    }
};
