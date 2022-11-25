<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateBookingDestinationMappingsMakeStatusIndex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('booking_destination_mappings', function (Blueprint $table) {
            $table->index('status');
        });

        Schema::table('cities', function (Blueprint $table) {
            $table->index('name');
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
        Schema::table('booking_destination_mappings', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });

        Schema::table('cities', function (Blueprint $table) {
            $table->dropIndex(['name']);
        });
    }
}
