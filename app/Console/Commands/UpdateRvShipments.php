<?php

namespace App\Console\Commands;

use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Models\RvShipmentAssignAgent;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentsJourney;
use App\RvShipmentAgent;
use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\IOFactory;

class UpdateRvShipments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shipments:update-rv-sar {tracking_number?} {--file= : Path to Excel file containing tracking numbers}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $trackingInput = $this->argument('tracking_number');
        if ($trackingInput) {
            // Split comma-separated tracking numbers into an array
            $trackingNumbers = array_map('trim', explode(',', $trackingInput));

            $shipments = Shipment::whereIn('tracking_number', $trackingNumbers)->get();

            if ($shipments->isEmpty()) {
                $this->info("❌ No shipments found for given tracking number(s): {$trackingInput}");
                return;
            }

            foreach ($shipments as $shipment) {
                $this->processShipment($shipment);
            }
        }
        $filePath = base_path($this->option('file'));

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

                $shipments = Shipment::whereIn('tracking_number', $chunk)->get();

                if ($shipments->isEmpty()) {
                    $this->warn("⚠️ No shipments found for chunk " . ($index + 1));
                    continue;
                }

                foreach ($shipments as $shipment) {
                    $this->processShipment($shipment);
                }

                $this->info("✅ Finished chunk " . ($index + 1));
            }

            $this->info("🎉 All {$totalRecords} records processed successfully!");
        } catch (\Exception $e) {
            $this->error("❌ Failed to read Excel file: " . $e->getMessage());
            return;
        }
    }

    protected function processShipment($shipment)
    {
        // $shipmentsJourney = ShipmentsJourney::where(['shipment_id'=>$shipment->id,'shipper_status_id'=>65])->latest()->first();
        // $shipmentsJourney->status_reason_id = ShipmentsJourney::where('shipment_id', $shipment->id)->where('shipper_status_id', 12)->latest()->first()->status_reason_id;
        // $shipmentsJourney->save();
        // dd($shipmentsJourney);
        $rvshipments = RvShipmentAssignAgent::where('shipment_id', $shipment->id);

        Shipment::where('id', $shipment->id)->update([
            'shipper_status_id'   => 65,
            'consignee_status_id' => 65,
        ]);

        ShipmentsJourneyController::add(
            $shipment->id,
            65,
            65,
            ShipmentsJourney::where('shipment_id', $shipment->id)->where('shipper_status_id', 12)->latest()->first()->status_reason_id,
            null,
            $shipment->user_id,
            346
        );

        NotificationsController::send(220, $rvshipments);

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
