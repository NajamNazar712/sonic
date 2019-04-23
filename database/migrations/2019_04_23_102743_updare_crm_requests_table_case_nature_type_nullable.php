<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdareCrmRequestsTableCaseNatureTypeNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('crm_requests', function (Blueprint $table) {
            $table->integer('case_nature_type_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('crm_requests', function (Blueprint $table) {
            $table->integer('case_nature_type_id')->nullable(false)->change();
        });
    }
}
