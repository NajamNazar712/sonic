<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateSalepersonTargetRenameColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sale_person_targets', function (Blueprint $table) {
            $table->renameColumn('target_week', 'target_month');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sale_person_targets', function (Blueprint $table) {
            $table->renameColumn('target_month', 'target_week');
        });
    }
}
