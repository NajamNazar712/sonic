<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncLatestShipments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:latest-shipments';
    

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync last 1 year shipment_journey data into latest_shipments_journey table';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $from = Carbon::now()->subYear()->startOfDay(); // last 1 year
        $to   = Carbon::now()->endOfDay();

        // Insert latest per (shipment_id + status_id)
        $sql = "
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
            )
            SELECT 
                sj.shipment_id,
                sj.shipper_status_id,
                sj.consignee_status_id,
                sj.status_reason_id,
                sj.remarks,
                sj.user_id,
                sj.admin_id,
                sj.rider_id,
                sj.city_id,
                sj.reference_1_id,
                sj.reference_2_id,
                sj.received_or_refused_by,
                sj.verification,
                sj.ip_address,
                sj.relation,
                sj.cnic,
                sj.created_at,
                sj.updated_at
            FROM shipments_journey sj
            INNER JOIN (
                SELECT shipment_id, shipper_status_id, MAX(id) AS max_id
                FROM shipments_journey
                WHERE created_at BETWEEN ? AND ?
                GROUP BY shipment_id, shipper_status_id
            ) latest
            ON sj.id = latest.max_id
            ON DUPLICATE KEY UPDATE
                consignee_status_id = VALUES(consignee_status_id),
                status_reason_id = VALUES(status_reason_id),
                remarks = VALUES(remarks),
                user_id = VALUES(user_id),
                admin_id = VALUES(admin_id),
                rider_id = VALUES(rider_id),
                city_id = VALUES(city_id),
                reference_1_id = VALUES(reference_1_id),
                reference_2_id = VALUES(reference_2_id),
                received_or_refused_by = VALUES(received_or_refused_by),
                verification = VALUES(verification),
                ip_address = VALUES(ip_address),
                relation = VALUES(relation),
                cnic = VALUES(cnic),
                created_at = VALUES(created_at),
                updated_at = VALUES(updated_at)
        ";

        DB::statement($sql, [$from, $to]);
    }
}
