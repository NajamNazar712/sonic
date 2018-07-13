<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateAddEditColumnDisputesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('disputes', function (Blueprint $table) {
            $table->renameColumn('admin_id','raised_by');
            $table->boolean('raised_by_status')->default(0)->after('admin_id')->comment('0 admin, 1 for shipper');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('disputes', function (Blueprint $table) {
            $table->renameColumn('raised_by','admin_id');
            $table->dropColumn('raised_by_status');
        });
    }
}
