<?php

namespace App\Console\Commands;

use DateTime;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ExportShipmentReport extends Command
{
    protected $signature = 'export:shipment-report';
    protected $description = 'Run custom query, export CSV, and email it';

    public function handle()
    {
        $start = new DateTime('2024-07-01');
        $end   = new DateTime('2024-10-31');
        $interval = new DateInterval('P5D');
        $period = new DatePeriod($start, $interval, (clone $end)->modify('+1 day'));

        foreach ($period as $chunkStart) {
            $chunkEnd = (clone $chunkStart)->add($interval)->modify('-1 day');
            if ($chunkEnd > $end) $chunkEnd = $end;

            $rows = DB::select("
            SELECT 
              s.tracking_number AS `Tracking Number`, 
              CASE WHEN s.booking_type_id = 4 THEN CONCAT(u.name, ' (', usi.poc, ')') ELSE u.name END AS `Shipper`, 
              seg.name AS `Segment`, 
              seg_sub.name AS `Sub Segment`, 
              ss.name AS `Status`, 
              bt.booking_type AS `Service Type`, 
              oc.name AS `Origin`, 
              dc.name AS `Destination`, 
              h.name AS `Hub`, 
              sm.mode AS `Shipping Mode`, 
              sj.created_at AS `Rider Pickup Date`,
              s.updated_at AS `Current Status Date`,
              sj_arrival.created_at AS `Origin Arrival Date Time`  
            FROM shipments s 
            LEFT JOIN users u ON u.id = s.user_id 
            LEFT JOIN segments seg ON seg.id = u.segment_id 
            LEFT JOIN sub_category_segments seg_sub ON seg_sub.id = u.sub_segment_id 
            LEFT JOIN shipment_status ss ON ss.id = s.shipper_status_id 
            LEFT JOIN user_shipping_infos usi ON s.pickup_address_id = usi.id 
            LEFT JOIN cities oc ON oc.id = usi.city_id 
            LEFT JOIN zones z ON z.id = oc.zone_id 
            LEFT JOIN cities dc ON dc.id = s.consignee_city_id 
            LEFT JOIN cities h ON h.id = dc.hub_id 
            LEFT JOIN zone_class_cities zcc 
              ON zcc.zone_id = z.id AND zcc.city_id = dc.id
              AND zcc.zone_classification_id = IF(s.shipping_mode_id IN (1,4),1,2)
            LEFT JOIN shipping_modes sm ON sm.id = s.shipping_mode_id 
            LEFT JOIN booking_types bt ON bt.id = s.booking_type_id 
            JOIN shipments_journey sj 
              ON sj.shipment_id = s.id AND sj.shipper_status_id = 53
            JOIN shipments_journey sj_arrival 
              ON sj_arrival.shipment_id = s.id AND sj_arrival.shipper_status_id = 2
            WHERE
              sj.verification = 1
              AND s.shipper_status_id NOT IN (1, 17)
              AND u.id NOT IN (8761, 9358)
              AND s.created_at BETWEEN ? AND ?
        ", [
                $chunkStart->format('Y-m-d 00:00:00'),
                $chunkEnd->format('Y-m-d 23:59:59'),
            ]);

            if (empty($rows)) {
                $this->info("No records between {$chunkStart->format('Y-m-d')} and {$chunkEnd->format('Y-m-d')}");
                continue;
            }

            $fileName = "shipment_{$chunkStart->format('Ymd')}_{$chunkEnd->format('Ymd')}.csv";
            $filePath = storage_path("app/exports/{$fileName}");
            if (!is_dir(dirname($filePath))) mkdir(dirname($filePath), 0777, true);

            $fp = fopen($filePath, 'w');
            fputcsv($fp, array_keys((array)$rows[0]));
            foreach ($rows as $row) {
                fputcsv($fp, array_values((array)$row));
            }
            fclose($fp);
            $this->info("✔ Created {$fileName} (" . count($rows) . " rows)");

            Mail::raw("Shipment records for {$chunkStart->format('Y-m-d')} to {$chunkEnd->format('Y-m-d')}", function ($msg) use ($filePath, $fileName) {
                $msg->to('anas.mazhar@logiserves.com')
                    ->subject("Shipment Report {$fileName}")
                    ->attach($filePath, ['as' => $fileName, 'mime' => 'text/csv']);
            });

            $this->info("📧 Email sent with {$fileName}");
        }

        $this->info("✅ All 5‑day chunks processed!");
    }
}
