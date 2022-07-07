<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateAddColumnsForReferenceIdPackagingMaterialRequest extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('packaging_material_requests', function (Blueprint $table) {
            $table->string('reference_id')->nullable()->after('poc');
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
            $table->dropColumn('reference_id');
        });
    }
}
