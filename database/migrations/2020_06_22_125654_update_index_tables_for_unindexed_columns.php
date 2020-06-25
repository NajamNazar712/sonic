<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateIndexTablesForUnindexedColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('handovers', function (Blueprint $table) {
            $table->index('created_by');
            $table->index('received_by');
            $table->index('from');
            $table->index('to');
            $table->index('hub');
            $table->index('status_id');
            $table->index('shipments');
            $table->index('received');
            $table->index('received_at');
            $table->index('created_at');
            $table->index('updated_at');
        });
        Schema::table('handover_shipments', function (Blueprint $table) {
            $table->index('handover_id');
            $table->index('shipment_id');
            $table->index('status');
            $table->index('created_at');
            $table->index('updated_at');
        });
        Schema::table('handover_shipments_journeys', function (Blueprint $table) {
            $table->index('handover_id');
            $table->index('shipment_id');
            $table->index('status');
            $table->index('created_at');
            $table->index('updated_at');
        });
        Schema::table('handover_responsibilities', function (Blueprint $table) {
            $table->index('hub_id');
            $table->index('created_by');
            $table->index('updated_by');
            $table->index('status');
            $table->index('created_at');
            $table->index('updated_at');
        });
        Schema::table('cargo_consignment_excels', function (Blueprint $table) {
            $table->index('shipments');
            $table->index('cargoes');
            $table->index('created_by');
            $table->index('created_at');
            $table->index('updated_at');
        });
        Schema::table('cargo_consignment_shipment_excels', function (Blueprint $table) {
            $table->index('cargo_consignment_excel_id', 'cargo_consignment_excel_id_index');
            $table->index('cargo_consignment_id');
            $table->index('shipment_id');
            $table->index('created_at');
            $table->index('updated_at');
        });
        Schema::table('shipment_pieces', function (Blueprint $table) {
            $table->index('shipment_id');
            $table->index('tracking_number');
            $table->index('numbering');
            $table->index('created_at');
            $table->index('updated_at');
        });
        Schema::table('shipments', function (Blueprint $table) {
            $table->index('pieces');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('handovers', function (Blueprint $table) {
            $table->dropIndex(['created_by']);
            $table->dropIndex(['received_by']);
            $table->dropIndex(['from']);
            $table->dropIndex(['to']);
            $table->dropIndex(['hub']);
            $table->dropIndex(['status_id']);
            $table->dropIndex(['shipments']);
            $table->dropIndex(['received']);
            $table->dropIndex(['received_at']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
        });
        Schema::table('handover_shipments', function (Blueprint $table) {
            $table->dropIndex(['handover_id']);
            $table->dropIndex(['shipment_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
        });
        Schema::table('handover_shipments_journeys', function (Blueprint $table) {
            $table->dropIndex(['handover_id']);
            $table->dropIndex(['shipment_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
        });
        Schema::table('handover_responsibilities', function (Blueprint $table) {
            $table->dropIndex(['hub_id']);
            $table->dropIndex(['created_by']);
            $table->dropIndex(['updated_by']);
            $table->dropIndex(['status']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
        });
        Schema::table('cargo_consignment_excels', function (Blueprint $table) {
            $table->dropIndex(['shipments']);
            $table->dropIndex(['cargoes']);
            $table->dropIndex(['created_by']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
        });
        Schema::table('cargo_consignment_shipment_excels', function (Blueprint $table) {
            $table->dropIndex(['cargo_consignment_excel_id']);
            $table->dropIndex(['cargo_consignment_id']);
            $table->dropIndex(['shipment_id']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
        });
        Schema::table('shipment_pieces', function (Blueprint $table) {
            $table->dropIndex(['shipment_id']);
            $table->dropIndex(['tracking_number']);
            $table->dropIndex(['numbering']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
        });
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropIndex(['pieces']);
        });
    }
}
