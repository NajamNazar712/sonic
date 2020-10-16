<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminUserRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_user_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone_number');
            $table->string('cnic');
            $table->string('department');
            $table->integer('default_hub_id')->nullable();
            $table->timestamp('request_created_at')->nullable();
            $table->integer('request_added_by')->nullable();
            $table->timestamp('verified_by_hr_at')->nullable();
            $table->integer('verified_by_hr')->nullable();
            $table->tinyinteger('status')->default(0);
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
        Schema::dropIfExists('admin_user_requests');
    }
}
