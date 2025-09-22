<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        DB::unprepared("
        CREATE TRIGGER trg_after_insert_shipments_journey
        AFTER INSERT ON shipments_journey
        FOR EACH ROW
        BEGIN
            INSERT INTO latest_shipments_journey (
                shipment_id,
                shipper_status_id,
                consignee_status_id,
                status_reason_id,
                remarks,
                user_id,
                admin_id,
                rider_id,
                city_id,
                reference_1_id,
                reference_2_id,
                received_or_refused_by,
                verification,
                ip_address,
                relation,
                cnic,
                created_at,
                updated_at
            ) VALUES (
                NEW.shipment_id,
                NEW.shipper_status_id,
                NEW.consignee_status_id,
                NEW.status_reason_id,
                NEW.remarks,
                NEW.user_id,
                NEW.admin_id,
                NEW.rider_id,
                NEW.city_id,
                NEW.reference_1_id,
                NEW.reference_2_id,
                NEW.received_or_refused_by,
                NEW.verification,
                NEW.ip_address,
                NEW.relation,
                NEW.cnic,
                NEW.created_at,
                NEW.updated_at
            )
            ON DUPLICATE KEY UPDATE
                shipper_status_id = NEW.shipper_status_id,
                consignee_status_id = NEW.consignee_status_id,
                status_reason_id = NEW.status_reason_id,
                remarks = NEW.remarks,
                user_id = NEW.user_id,
                admin_id = NEW.admin_id,
                rider_id = NEW.rider_id,
                city_id = NEW.city_id,
                reference_1_id = NEW.reference_1_id,
                reference_2_id = NEW.reference_2_id,
                received_or_refused_by = NEW.received_or_refused_by,
                verification = NEW.verification,
                ip_address = NEW.ip_address,
                relation = NEW.relation,
                cnic = NEW.cnic,
                updated_at = NEW.updated_at;
        END
    ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        DB::unprepared("DROP TRIGGER IF EXISTS trg_after_insert_shipments_journey");
    }
};
