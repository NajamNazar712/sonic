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
        Schema::create('payment_requisition_approvals', function (Blueprint $table) {
            $table->id();
            $table->integer('payment_requisition_id')->index();
            $table->integer('approver_role_id')->index(); 
            $table->integer('dept_id')->index(); 
            $table->tinyInteger('level')->default(1); 
            $table->string('status');
            $table->integer('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('remarks')->nullable();
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
        Schema::dropIfExists('payment_requisition_approvals');
    }
};
