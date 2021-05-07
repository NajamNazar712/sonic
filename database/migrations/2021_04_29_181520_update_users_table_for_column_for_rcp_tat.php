<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUsersTableForColumnForRcpTat extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->bigInteger('rcp_tat_option_id')->default(3)->index();
            $table->bigInteger('rcp_tat_updated_by')->nullable()->default(null)->index();
            $table->timestamp('rcp_tat_updated_at')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('rcp_tat_option_id');
            $table->dropColumn('rcp_tat_updated_by');
            $table->dropColumn('rcp_tat_updated_at');
        });
    }
}
