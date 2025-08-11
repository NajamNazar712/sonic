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
        Schema::create('retail_wallet_users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 191);
            $table->string('email', 191)->unique();
            $table->string('phone', 191);
            $table->string('cnic', 191);
            $table->integer('wallet_id');
            $table->integer('user_id');
            $table->integer('substitute_user_id')->default(0);
            $table->tinyInteger('status')->default(0);
            $table->tinyInteger('finova_account_type')->default(0);
            $table->timestamps();
            $table->softDeletes();

            // Additional indexes
            $table->index('name', 'wallet_users_name_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('retail_wallet_users');
    }
};
