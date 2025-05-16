<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('issue_sack_bag_origins', function (Blueprint $table) {
            $table->timestamp('inactive_at')->nullable()->after('updated_at')->index();
            $table->integer('inactive_by')->nullable()->after('inactive_at')->index();
            $table->integer('active_by')->nullable()->after('inactive_by')->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('issue_sack_bag_origins', function (Blueprint $table) {
            $table->dropColumn('inactive_at');
            $table->dropColumn('inactive_by');
            $table->dropColumn('active_by');
        });
    }
};
