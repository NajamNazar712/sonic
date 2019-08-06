<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OperationForecastsIndexColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('operations_forecast_last_updated_times', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
        });

        Schema::table('operation_forecasts', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('shipper_status_id');
            $table->index('booking_type_id');
            $table->index('hub_id');
            $table->index('count');
        });

        Schema::table('operation_forecast_shipments', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('operation_forecast_id');
            $table->index('booking_type_id');
            $table->index('hub_id');
            $table->index('weight_range_id');
            $table->index('shipment_id');
        });

        Schema::table('operation_forecast_weight_ranges', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
        });

        Schema::table('operations_outgoing_pickup_requests', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('pickup_request_id');
            $table->index('booking_type_id');
            $table->index('hub_id');
            $table->index('shipments_count');
        });

        Schema::table('operations_outgoing_pickup_request_shipments', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('operation_outgoing_forecast_id', 'operation_outgoing_forecast_id_index');
            $table->index('booking_type_id');
            $table->index('hub_id');
            $table->index('weight_range_id');
            $table->index('shipment_id');
        });

        Schema::table('operations_outgoing_top_customers', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('user_id');
            $table->index('shipments_count');
        });

        Schema::table('operations_outgoing_top_customers_shipments', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('customer_id');
            $table->index('shipment_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('operations_forecast_last_updated_times', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['shipper_status_id']);
            $table->dropIndex(['booking_type_id']);
            $table->dropIndex(['hub_id']);
            $table->dropIndex(['count']);
        });

        Schema::table('operation_forecasts', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['shipper_status_id']);
            $table->dropIndex(['booking_type_id']);
            $table->dropIndex(['hub_id']);
            $table->dropIndex(['count']);
        });

        Schema::table('operation_forecast_shipments', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['operation_forecast_id']);
            $table->dropIndex(['booking_type_id']);
            $table->dropIndex(['hub_id']);
            $table->dropIndex(['weight_range_id']);
            $table->dropIndex(['shipment_id']);
        });

        Schema::table('operation_forecast_weight_ranges', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
        });

        Schema::table('operations_outgoing_pickup_requests', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['pickup_request_id']);
            $table->dropIndex(['booking_type_id']);
            $table->dropIndex(['hub_id']);
            $table->dropIndex(['shipments_count']);
        });

        Schema::table('operations_outgoing_pickup_request_shipments', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['operation_outgoing_forecast_id']);
            $table->dropIndex(['booking_type_id']);
            $table->dropIndex(['hub_id']);
            $table->dropIndex(['weight_range_id']);
            $table->dropIndex(['shipment_id']);
        });

        Schema::table('operations_outgoing_top_customers', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['shipments_count']);
        });

        Schema::table('operations_outgoing_top_customers_shipments', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['customer_id']);
            $table->dropIndex(['shipment_id']);
        });
    }
}
