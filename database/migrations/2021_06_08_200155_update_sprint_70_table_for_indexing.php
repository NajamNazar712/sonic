<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateSprint70TableForIndexing extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fleets', function (Blueprint $table) {
            $table->index('tracking_id');
            $table->index('reg_number');
            $table->index('vehicle_type_id');
            $table->index('status');
        });
        Schema::table('consignee_otps', function (Blueprint $table) {
            $table->index('phone_number');
        });
        Schema::table('consignee_users', function (Blueprint $table) {
            $table->index('phone_number_1');
            $table->index('phone_number_2');
        });
        Schema::table('employees', function (Blueprint $table) {
            $table->index('forget_pin_status');
        });
        Schema::table('route_management_junctions', function (Blueprint $table) {
            $table->index('route_management_id');
            $table->index('junction_id');
        });
        Schema::table('route_managements', function (Blueprint $table) {
            $table->index('route_code');
            $table->index('route_title');
            $table->index('starting_point_id');
            $table->index('end_point_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
