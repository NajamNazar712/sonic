<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUpdateAddColumnAdminIdToHandoverResponsibilities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::table('handover_responsibilities', function (Blueprint $table) {
            $table->integer('admin_id')->index()->nullable()->after('hub_id');
            $table->string('name')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('handover_responsibilities', function (Blueprint $table) {
            $table->dropColumn('admin_id');
        });
    }
}
