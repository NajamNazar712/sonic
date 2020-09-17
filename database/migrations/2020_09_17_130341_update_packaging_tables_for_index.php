<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePackagingTablesForIndex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('packaging_material_requests', function (Blueprint $table) {
            $table->index('requested_by');
        });
        Schema::table('packaging_material_request_details', function (Blueprint $table) {
            $table->index('packaging_material_request_id', 'packaging_id_index');
            $table->index('type_id');
            $table->index('type_size_id');
            $table->index('quantity');
            $table->index('created_at');
            $table->index('updated_at');
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
            $table->dropIndex(['requested_by']);
        });
        Schema::table('packaging_material_requests', function (Blueprint $table) {
            $table->dropIndex(['packaging_material_request_id']);
            $table->dropIndex(['type_id']);
            $table->dropIndex(['type_size_id']);
            $table->dropIndex(['quantity']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
        });
    }
}
