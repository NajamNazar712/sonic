<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCrmRequestForLatLong extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('crm_requests', function (Blueprint $table) {
            $table->string('address', 255)->nullable();
            $table->decimal('address_latitude', 10, 6)->nullable();
            $table->decimal('address_longitude', 10, 6)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('crm_requests', function (Blueprint $table) {
            $table->dropColumn('address');
            $table->dropColumn('address_latitude');
            $table->dropColumn('address_longitude');
        });
    }
}
