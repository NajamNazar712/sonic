<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUpdateAddColumnInLeadUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('request_custom_quotation')->nullable()->default(null)->after('blocked_at');
            $table->boolean('on_board_status')->nullable()->default(null)->after('request_custom_quotation');
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
            $table->dropColumn('request_custom_quotation');
            $table->dropColumn('on_board_status');
        });    
    }
}
