<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubstituteUserPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('substitute_user_permissions', function (Blueprint $table) {
            $table->integer('substitute_user_id');
            $table->integer('permission_id');
            $table->primary(['substitute_user_id', 'permission_id'], 'subsitute_user_permission_primary');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('substitute_user_permissions');
    }
}
