<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateReturnAssignedShipmentsTableAddIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('return_assigned_shipments', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('admin_id');
            $table->index('shipment_id');
            $table->index('status');
            $table->index('assigned_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('return_assigned_shipments', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['admin_id']);
            $table->dropIndex(['shipment_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['assigned_by']);
        });
    }
}
