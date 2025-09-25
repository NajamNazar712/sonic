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
        Schema::table('pudo_pickup_shipments', function (Blueprint $table) {
            $table->unsignedInteger('hub_id')->index()->nullable()->after('retail_address_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pudo_pickup_shipments', function (Blueprint $table) {
            $table->dropColumn('hub_id');
        });
    }
};
