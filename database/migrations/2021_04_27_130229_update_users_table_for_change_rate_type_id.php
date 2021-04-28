<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUsersTableForChangeRateTypeId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('rate_type_id_changed_by')->nullable()->index();
            $table->integer('rate_type_id_status')->nullable()->index();
            $table->integer('new_rate_type_id')->nullable()->index();
            $table->timestamp('rate_type_id_changed_at')->nullable()->index();
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
            $table->dropColumn('rate_type_id_changed_by');
            $table->dropColumn('rate_type_id_status');
            $table->dropColumn('new_rate_type_id');
            $table->dropColumn('rate_type_id_changed_at');
        });
    }
}
