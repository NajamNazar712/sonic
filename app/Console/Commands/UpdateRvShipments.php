<?php 
namespace App\Console\Commands;

use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Models\RvShipmentAssignAgent;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentsJourney;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class UpdateRvShipments extends Command
{
    protected $signature = 'shipments:update-rv-sar {tracking_number?} {--file= : Path to Excel file containing tracking numbers}';
    protected $description = 'Update RV shipments and handle missing SAR shipments';

    public function handle()
    {
        $trackingInput = $this->argument('tracking_number');
        
        if ($trackingInput) {
            // Handle tracking number input
            $trackingNumbers = array_map('trim', explode(',', $trackingInput));
            $this->processShipmentsByTrackingNumbers($trackingNumbers);
        }
        
        // If no tracking number is provided, check the file option
        $filePath = base_path($this->option('file'));

        if ($filePath) {
            // Handle Excel file input
            $this->processShipmentsByFile($filePath);
        }

        // If neither tracking number nor file is provided, process shipments by shipper_status_id 12
        if (!$trackingInput && !$filePath) {
            $this->processMissingShipments();
        }
    }

    protected function processShipmentsByTrackingNumbers($trackingNumbers)
    {
        $shipments = Shipment::whereIn('tracking_number', $trackingNumbers)
                             ->where('shipper_status_id', 12)
                             ->get();

        if ($shipments->isEmpty()) {
            $this->info("❌ No shipments found for given tracking number(s).");
            return;
        }

        foreach ($shipments as $shipment) {
            $this->processShipment($shipment);
        }
    }

    protected function processShipmentsByFile($filePath)
    {
        if (!file_exists($filePath)) {
            $this->error("❌ File not found: {$filePath}");
            return;
        }

        try {
            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();
            $trackingNumbers = [];

            foreach ($rows as $row) {
                if (!empty($row[0]) && strtolower($row[0]) !== 'tracking_number') {
                    $trackingNumbers[] = trim($row[0]);
                }
            }

            $totalRecords = count($trackingNumbers);
            $this->info("📊 Total tracking numbers found in Excel: {$totalRecords}");

            if ($totalRecords === 0) {
                $this->error("❌ No valid tracking numbers found in Excel file. Execution stopped.");
                return;
            }

            // ✅ Process in chunks of 500
            $chunks = array_chunk($trackingNumbers, 500);
            foreach ($chunks as $index => $chunk) {
                $this->info("🚀 Processing chunk " . ($index + 1) . " of " . count($chunks) . " (Records: " . count($chunk) . ")");
                $this->processShipmentsByTrackingNumbers($chunk);
            }

            $this->info("🎉 All records processed successfully!");
        } catch (\Exception $e) {
            $this->error("❌ Failed to read Excel file: " . $e->getMessage());
            return;
        }
    }

    protected function processMissingShipments()
    {
        $yesterday = Carbon::now()->subDay()->format('Y-m-d');
        // Get shipments with shipper_status_id = 12 and no associated RvShipmentTicket with permanent_disable = 1
        $missingShipments =  DB::table('shipments as s')
            ->join('rv_shipment_tickets as rst', function ($join) {
                $join->on('s.id', '=', 'rst.shipment_id')
                    ->where('rst.permanent_disable', 1);
            })
            ->join('shipments_journey as sj', 'sj.shipment_id', '=', 's.id')
            ->where('sj.shipper_status_id', 12)
            ->where('sj.verification', 1)
            ->where('sj.created_at', '>=',  $yesterday  . ' 00:00:00')
            ->where('sj.updated_at', '<=',  $yesterday  . ' 23:59:59')
            ->where('s.shipper_status_id', 12)
            ->select('s.id')
            ->get();

        if ($missingShipments->isEmpty()) {
            $this->info("❌ No missing shipments found.");
            return;
        }

        foreach ($missingShipments as $shipment) {
            $this->processShipment($shipment);
        }
    }

    protected function processShipment($shipment)
    {
        // Your logic to process each shipment
        $rvShipments = RvShipmentAssignAgent::where('shipment_id', $shipment->id);

        Shipment::where('id', $shipment->id)->update([
            'shipper_status_id'   => 65,
            'consignee_status_id' => 65,
        ]);

        ShipmentsJourneyController::add(
            $shipment->id,
            65,
            65,
            ShipmentsJourney::where('shipment_id', $shipment->id)
                           ->where('shipper_status_id', 12)
                           ->latest()
                           ->first()->status_reason_id,
            null,
            $shipment->user_id,
            346
        );

        NotificationsController::send(220, $rvShipments);

        RvShipmentAssignAgent::where('shipment_id', $shipment->id)
            ->update([
                'agent_id'                  => 346,
                'rv_state_id'               => 2,
                'rv_assign_agent_status_id' => 7,
                'unresponsive_count'        => 3,
                'unresponsive_email_count'  => 1,
                'unresponsive_email_time'   => now(),
            ]);

        $this->info("✅ Shipment {$shipment->tracking_number} (ID: {$shipment->id}) updated successfully.");
    }
}
