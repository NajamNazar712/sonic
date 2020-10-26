<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminUserRequestHubsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_user_request_hubs', function (Blueprint $table) {
            $table->integer('admin_user_requests_id');
            $table->integer('hubs_id');
            $table->primary(['admin_user_requests_id', 'hubs_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin_user_request_hubs');
    }
}
