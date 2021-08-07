<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateFtlRequestTableForCalculatedCharges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ftl_requests', function (Blueprint $table) {
            $table->integer('calculated_charges')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ftl_requests', function (Blueprint $table) {
            $table->dropColumn('calculated_charges');
        });
    }
}
