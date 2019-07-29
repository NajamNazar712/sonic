<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePackagingMaterialRequests extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('packaging_material_requests', function (Blueprint $table) {
            $table->dropColumn('small_flyers');
            $table->dropColumn('medium_flyers');
            $table->dropColumn('large_flyers');
            $table->dropColumn('boxes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('packaging_material_requests', function (Blueprint $table) {
            $table->integer('small_flyers');
            $table->integer('medium_flyers');
            $table->integer('large_flyers');
            $table->integer('boxes');
        });
    }
}
