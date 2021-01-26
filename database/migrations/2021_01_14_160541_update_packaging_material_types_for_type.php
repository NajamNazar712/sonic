<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePackagingMaterialTypesForType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('packaging_material_types', function (Blueprint $table) {
            $table->tinyInteger('packaging_type')->default(3)->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('packaging_material_types', function (Blueprint $table) {
            $table->dropColumn('packaging_type');
        });
    }
}
