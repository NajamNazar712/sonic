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
        Schema::create('payment_requisitions', function (Blueprint $table) {
            $table->id();
            $table->integer('requester_id')->index();
            $table->string('invoice_no')->nullable();
            $table->string('payee_name');
            $table->string('ntn_cnic')->nullable();
            $table->integer('bank_id')->index(); // foreign key from banks table
            $table->string('bank_title');
            $table->string('iban');
            $table->decimal('amount', 15, 2)->default(0);
            $table->integer('account_of_id')->index(); // foreign key from accounts table
            $table->integer('account_of_id')->index(); // foreign key from accounts table
            $table->integer('requester_department_id')->index(); // foreign key from departments table
            $table->text('description')->nullable();
            $table->integer('status')->nullable();
            $table->string('cheque_no')->nullable();
            $table->string('document1')->nullable();
            $table->string('document2')->nullable();
            $table->string('document3')->nullable();
            $table->string('document4')->nullable();
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
        Schema::dropIfExists('payment_requisitions');
    }
};
