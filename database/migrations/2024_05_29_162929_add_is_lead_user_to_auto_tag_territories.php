<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsLeadUserToAutoTagTerritories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('auto_tag_territories', function (Blueprint $table) {
            $table->tinyInteger('is_lead_user')->nullable()->after('admin_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('auto_tag_territories', function (Blueprint $table) {
            $table->dropColumn('is_lead_user');
        });
    }
}
