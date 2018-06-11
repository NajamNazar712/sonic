<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePickupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('pickups', 'pickup_requests');

        Schema::table('pickup_requests', function (Blueprint $table) {
            $table->dropColumn('rider_id');
            $table->dropColumn('assigned_date');
            $table->renameColumn('status_id', 'status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pickup_requests', function (Blueprint $table) {
            $table->integer('rider_id')->nullable()->after('pickup_date');
            $table->timestamp('assigned_date')->nullable()->after('rider_id');
            $table->renameColumn('status', 'status_id');
        });

        Schema::rename('pickup_requests', 'pickups');
    }
}
