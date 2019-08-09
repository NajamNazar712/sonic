<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMergedSisterAccountMappingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merged_sister_account_mappings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('merged_head_id');
            $table->integer('head_user_id');
            $table->integer('sister_user_id');
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
        Schema::dropIfExists('merged_sister_account_mappings');
    }
}
