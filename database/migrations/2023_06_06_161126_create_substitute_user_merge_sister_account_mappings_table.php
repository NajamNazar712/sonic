<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubstituteUserMergeSisterAccountMappingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('substitute_user_merge_sister_account_mappings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('substitute_user_id')->index('substitute_user_id');
            $table->integer('merged_head_id')->index('merged_head_id');
            $table->integer('head_user_id')->index('head_user_id');
            $table->integer('sister_user_id')->index('sister_user_id');
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
        Schema::dropIfExists('substitute_user_merge_sister_account_mappings');
    }
}
