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
    public function up(): void
    {
        Schema::create('user_settled_payment_banks', function (Blueprint $table) {
            $table->id();
            $table->string('done_payment_id'); // FK to done_payments table
            $table->string('user_id'); // FK to users table
            $table->string('bank_id'); // Bank must exist
            $table->string('bank_branch'); 
            $table->string('account_title');
            $table->string('account_number');
            $table->string('iban')->nullable();
            $table->timestamps();

            // Ensure one bank record per done payment per user
            $table->unique(['done_payment_id'], 'unique_done_payment_user');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_settled_payment_banks');
    }
};
