<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ExportShipmentReport extends Command
{
    protected $signature = 'export:shipment-report';
    protected $description = 'Run custom query, export CSV, and email it';

    public function handle()
    {
        // Build your SQL query
        $rows = DB::select("
            SELECT 
              s.id,
              s.tracking_number, 
              s.consignee_address AS Consignee_Address, 
              s.consignee_phone_number_1 AS Phone,
              s.consignee_name AS Name,
              s.weight_charges AS Weight,
              (
                SELECT COUNT(*) 
                FROM shipment_pieces sp 
                WHERE sp.shipment_id = s.id
              ) AS Pcs,
              s.amount AS COD,
              (
                SELECT SUM(si.quantity)
                FROM shipment_items si 
                WHERE si.shipment_id = s.id
              ) AS Quantity,
              rd.delivery_note_id AS Delivery_Number,
              rwds.trax_id AS Rider_Employee_Id,
              rwds.delivery_date AS Delivery_Date
            FROM shipments s
            JOIN rider_deliveries rd ON rd.shipment_id = s.id
            JOIN rider_wise_delivery_note_summaries rwds ON rwds.rider_id = rd.rider_id
            WHERE s.created_at BETWEEN '2024-08-01 00:00:00' AND '2024-08-31 23:59:59'
              AND s.shipper_status_id NOT IN (1,17)
        ");

        if (empty($rows)) {
            $this->info("No data to export.");
            return;
        }

        // Prepare CSV
        $filename = "shipment_report_" . now()->format('Ymd_His') . ".csv";
        $path = storage_path("app/exports/{$filename}");
        // Ensure directory exists
        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0777, true);
        }

        $fp = fopen($path, 'w');
        // Write header row
        fputcsv($fp, array_keys((array)$rows[0]));
        // Write data rows
        foreach ($rows as $row) {
            fputcsv($fp, array_values((array)$row));
        }
        fclose($fp);  // PHP’s fputcsv docs: formats & writes CSV correctly :contentReference[oaicite:4]{index=4}

        $this->info("CSV generated at: $path");

        // Send CSV via email
        Mail::raw('Shipment report attached.', function ($msg) use ($path, $filename) {
            $msg->to('anas.mazhar@logiserves.com')
                ->subject('Shipment CSV Report')
                ->attach($path, [
                    'as' => $filename,
                    'mime' => 'text/csv',
                ]);
        });

        $this->info("✅ Email sent with attachment.");
    }
}
