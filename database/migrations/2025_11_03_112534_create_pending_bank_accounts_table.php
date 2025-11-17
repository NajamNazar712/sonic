<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pending_bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->integer('bank_name');
            $table->string('bank_branch');
            $table->integer('bank_id');
            $table->integer('bank_city');
            $table->string('account_no');
            $table->string('account_title');
            $table->string('iban_no')->nullable();
            $table->string('blank_cheque_image')->nullable();
            // Status fields
            $table->boolean('status')->default(0)->comment('0 = pending, 1 = approved');
            $table->boolean('email_status')->default(0)->comment('0 = not sent, 1 = sent');

            $table->timestamps();

            // Optional: Add foreign key if user table exists
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pending_bank_accounts');
    }
};
