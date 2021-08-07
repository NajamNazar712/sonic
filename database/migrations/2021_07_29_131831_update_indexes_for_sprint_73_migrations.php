<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateIndexesForSprint73Migrations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shipment_details', function (Blueprint $table) {
            $table->index('shipment_id');
            $table->index('is_open');
        });

        Schema::table('incidence_monitoring_status_histories', function (Blueprint $table) {
            $table->index('incidence_monitoring_id','incidence_m_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shipment_details', function (Blueprint $table) {
            $table->dropIndex(['shipment_id']);
            $table->dropIndex(['is_open']);
        });

        Schema::table('incidence_monitoring_status_histories', function (Blueprint $table) {
            $table->dropIndex(['incidence_monitoring_id','incidence_m_id']);
        });
    }
}
