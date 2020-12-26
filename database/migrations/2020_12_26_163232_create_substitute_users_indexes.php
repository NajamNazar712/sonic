<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubstituteUsersIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('substitute_users', function (Blueprint $table) {
            $table->index('restriction');
        });

        Schema::table('substitute_user_receiving_sheets', function (Blueprint $table) {
            $table->index('substitute_user_id');
            $table->index('receiving_sheet_id');
        });

        Schema::table('substitute_user_shipments', function (Blueprint $table) {
            $table->index('substitute_user_id');
            $table->index('shipment_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('substitute_users', function (Blueprint $table) {
            $table->dropIndex(['restriction']);
        });

        Schema::table('substitute_user_receiving_sheets', function (Blueprint $table) {
            $table->dropIndex(['substitute_user_id']);
            $table->dropIndex(['receiving_sheet_id']);
        });

        Schema::table('substitute_user_shipments', function (Blueprint $table) {
            $table->dropIndex(['substitute_user_id']);
            $table->dropIndex(['shipment_id']);
        });
    }
}
