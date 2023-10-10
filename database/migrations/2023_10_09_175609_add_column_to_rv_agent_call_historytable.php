<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToRvAgentCallHistorytable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rv_agent_call_histories', function (Blueprint $table) {
            $table->integer('updated_type_id')->after('remarks');;
            $table->integer('updated_by_id')->index()->after('updated_type_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rv_agent_call_histories', function (Blueprint $table) {
            $table->dropColumn(['updated_type_id','updated_by_id']);
        });
    }
}
