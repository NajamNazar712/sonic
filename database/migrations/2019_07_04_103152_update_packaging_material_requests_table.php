<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePackagingMaterialRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('packaging_material_requests', function (Blueprint $table) {
            $table->renameColumn('status', 'status_id')->default(1)->change();
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
            $table->renameColumn('status_id', 'status')->default(1)->change();
        });
    }
}
