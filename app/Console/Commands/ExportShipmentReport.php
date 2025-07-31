<?php

namespace App\Console\Commands;

use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ExportShipmentReport extends Command
{
    protected $signature = 'export:shipment-report';
    protected $description = 'Run custom query, export CSV, and email it';

    public function handle()
    {
        // $start = new DateTime('2024-07-01');
        // $end   = new DateTime('2024-10-31');

        // // $interval = new DateInterval('P5D');
        // // $period = new DatePeriod($start, $interval, (clone $end)->modify('+1 day'));

        // // foreach ($period as $chunkStart) {
        // //     $chunkEnd = (clone $chunkStart)->add($interval)->modify('-1 day');
        // //     if ($chunkEnd > $end) $chunkEnd = $end;

        // //     $rows = DB::select("
        // //     SELECT 
        // //       s.tracking_number AS `Tracking Number`, 
        // //       CASE WHEN s.booking_type_id = 4 THEN CONCAT(u.name, ' (', usi.poc, ')') ELSE u.name END AS `Shipper`, 
        // //       seg.name AS `Segment`, 
        // //       seg_sub.name AS `Sub Segment`, 
        // //       ss.name AS `Status`, 
        // //       bt.booking_type AS `Service Type`, 
        // //       oc.name AS `Origin`, 
        // //       dc.name AS `Destination`, 
        // //       h.name AS `Hub`, 
        // //       sm.mode AS `Shipping Mode`, 
        // //       sj.created_at AS `Rider Pickup Date`,
        // //       s.updated_at AS `Current Status Date`,
        // //       sj_arrival.created_at AS `Origin Arrival Date Time`  
        // //     FROM shipments s 
        // //     LEFT JOIN users u ON u.id = s.user_id 
        // //     LEFT JOIN segments seg ON seg.id = u.segment_id 
        // //     LEFT JOIN sub_category_segments seg_sub ON seg_sub.id = u.sub_segment_id 
        // //     LEFT JOIN shipment_status ss ON ss.id = s.shipper_status_id 
        // //     LEFT JOIN user_shipping_infos usi ON s.pickup_address_id = usi.id 
        // //     LEFT JOIN cities oc ON oc.id = usi.city_id 
        // //     LEFT JOIN zones z ON z.id = oc.zone_id 
        // //     LEFT JOIN cities dc ON dc.id = s.consignee_city_id 
        // //     LEFT JOIN cities h ON h.id = dc.hub_id 
        // //     LEFT JOIN zone_class_cities zcc 
        // //       ON zcc.zone_id = z.id AND zcc.city_id = dc.id
        // //       AND zcc.zone_classification_id = IF(s.shipping_mode_id IN (1,4),1,2)
        // //     LEFT JOIN shipping_modes sm ON sm.id = s.shipping_mode_id 
        // //     LEFT JOIN booking_types bt ON bt.id = s.booking_type_id 
        // //     JOIN shipments_journey sj 
        // //       ON sj.shipment_id = s.id AND sj.shipper_status_id = 53
        // //     JOIN shipments_journey sj_arrival 
        // //       ON sj_arrival.shipment_id = s.id AND sj_arrival.shipper_status_id = 2
        // //     WHERE
        // //       sj.verification = 1
        // //       AND s.shipper_status_id NOT IN (1, 17)
        // //       AND u.id NOT IN (8761, 9358)
        // //       AND s.created_at BETWEEN ? AND ?
        // // ", [
        // //         $chunkStart->format('Y-m-d 00:00:00'),
        // //         $chunkEnd->format('Y-m-d 23:59:59'),
        // //     ]);

        // //     if (empty($rows)) {
        // //         $this->info("No records between {$chunkStart->format('Y-m-d')} and {$chunkEnd->format('Y-m-d')}");
        // //         continue;
        // //     }

        // //     $fileName = "shipment_{$chunkStart->format('Ymd')}_{$chunkEnd->format('Ymd')}.csv";
        // //     $filePath = storage_path("app/exports/{$fileName}");
        // //     if (!is_dir(dirname($filePath))) mkdir(dirname($filePath), 0777, true);

        // //     $fp = fopen($filePath, 'w');
        // //     fputcsv($fp, array_keys((array)$rows[0]));
        // //     foreach ($rows as $row) {
        // //         fputcsv($fp, array_values((array)$row));
        // //     }
        // //     fclose($fp);
        // //     $this->info("✔ Created {$fileName} (" . count($rows) . " rows)");

        // //     Mail::raw("Shipment records for {$chunkStart->format('Y-m-d')} to {$chunkEnd->format('Y-m-d')}", function ($msg) use ($filePath, $fileName) {
        // //         $msg->to('anas.mazhar@logiserves.com')
        // //             ->subject("Shipment Report {$fileName}")
        // //             ->attach($filePath, ['as' => $fileName, 'mime' => 'text/csv']);
        // //     });

        // //     $this->info("📧 Email sent with {$fileName}");
        // // }

        // $this->info("✅ All 5‑day chunks processed!");
        //     $start = new DateTime('2024-07-01');
        //     $end   = new DateTime('2024-08-31');

        //     $rows = DB::select("
        // SELECT 
        //     s.id,
        //     s.tracking_number, 
        //     s.consignee_address AS Consignee_Address, 
        //     s.consignee_phone_number_1 AS Phone, 
        //     s.consignee_name AS NAME, 
        //     s.`actual_weight` AS Weight, 

        //     (
        //         SELECT COUNT(*) 
        //         FROM shipment_pieces sp 
        //         WHERE sp.shipment_id = s.id
        //     ) AS Pcs, 

        //     s.amount AS COD, 

        //     (
        //         SELECT SUM(si.quantity) 
        //         FROM shipment_items si 
        //         WHERE si.shipment_id = sj.shipment_id
        //     ) AS Quantity, 

        //     sj.`reference_1_id` AS Delivery_Number, 
        //     r.trax_id AS Rider_Employee_Id, 
        //     sj.updated_at AS `Date`,
        //     ca.name AS hub

        // FROM shipments_journey AS sj
        // JOIN shipments AS s ON s.id = sj.shipment_id
        // JOIN riders AS r ON sj.reference_2_id = r.id
        // left JOIN city_areas AS ca ON  ca.`id` = r.`area_id`

        // WHERE sj.created_at BETWEEN ? AND ?
        //   AND sj.shipper_status_id = 5
        //   AND sj.city_id = 202", [
        //         $start->format('Y-m-d 00:00:00'),
        //         $end->format('Y-m-d 23:59:59'),
        //     ]);
        // Define date range
        $start = Carbon::parse('2024-01-01');
        $end = Carbon::parse('2025-07-26');

        // Run query
        $rows =  DB::select("
                        SELECT 
                            u.id AS `Account ID`,
                            u.name AS `Account Name`,
                            u.address AS `Address`,
                            u.email AS `POC Email`,
                            u.poc AS `POC Name`,
                            u.phone AS `POC Phone Number`,
                            us.name AS `Status`,
                            a.name AS 'Sale Person',
                            seg.name AS 'Segment',
                            seg_sub.name AS 'Sub Segment',
                            c.name AS 'Origin',
                            usi.pickup_address AS 'PickUp Address',
                            DATE_FORMAT(s.created_at, '%Y-%m') AS `Month`,
                            COUNT(s.id) AS `Total Shipments`,
                            SUM(s.chargeable_weight) AS 'Total weight',
                            SUM(s.weight_charges) AS 'Total weight charges'
                        FROM shipments AS s
                        JOIN users AS u ON u.id = s.user_id
                        JOIN cities AS c ON c.id = u.city_id AND c.zone_id IN (1,17,19,20,21)
                        JOIN user_statuses AS us ON us.id = u.status
                        JOIN user_shipping_infos AS usi ON usi.id = s.pickup_address_id
                        JOIN sale_person_tags AS spt ON spt.user_id = u.id
                        JOIN admins AS a ON a.id = spt.admin_id
                        LEFT JOIN segments AS seg ON seg.id = u.segment_id
                        LEFT JOIN sub_category_segments AS seg_sub ON seg_sub.id = u.sub_segment_id
                        WHERE DATE(s.created_at) BETWEEN ? AND ?
                        GROUP BY s.user_id, DATE_FORMAT(s.created_at, '%Y-%m')
                        ORDER BY u.name, `Month`
                    ", ['2024-01-01', '2025-07-30']);
        if (empty($rows)) {
            $this->info("❌ No records found.");
            return;
        }

        // File name and path
        $fileName = "rider_shipments_{$start->format('Ymd')}_{$end->format('Ymd')}.csv";
        $filePath = storage_path("app/exports/{$fileName}");
        if (!is_dir(dirname($filePath))) mkdir(dirname($filePath), 0777, true);

        // Write CSV
        $fp = fopen($filePath, 'w');
        fputcsv($fp, array_keys((array)$rows[0]));
        foreach ($rows as $row) {
            fputcsv($fp, array_values((array)$row));
        }
        fclose($fp);

        $this->info("✔ Exported {$fileName}");

        $zipPath = storage_path("app/exports/{$fileName}.zip");
        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
            $zip->addFile($filePath, $fileName); // file inside zip should have .csv name
            $zip->close();
            $this->info("✔ Compressed {$fileName} to ZIP");
        } else {
            $this->error("❌ Failed to create ZIP archive");
            return;
        }

        // ✅ Email the zipped file
        Mail::raw("Rider shipment data from {$start->format('Y-m-d')} to {$end->format('Y-m-d')}", function ($msg) use ($zipPath, $fileName) {
            $msg->to('anas.mazhar@logiserves.com')
                ->subject("Shipment Export - {$fileName}")
                ->attach($zipPath, ['as' => "{$fileName}.zip", 'mime' => 'application/zip']);
        });


        $this->info("📧 Email sent with attachment.");
    }
}
