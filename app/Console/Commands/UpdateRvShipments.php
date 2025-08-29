<?php

namespace App\Console\Commands;

use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Models\RvShipmentAssignAgent;
use App\Http\Models\Shipment;
use App\RvShipmentAgent;
use Illuminate\Console\Command;

class UpdateRvShipments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shipments:update-rv-sar {tracking_number?}';

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
    }

    protected function processShipment($shipment)
    {
        $rvshipments = RvShipmentAssignAgent::where('shipment_id', $shipment->id);

        Shipment::where('id', $shipment->id)->update([
            'shipper_status_id'   => 65,
            'consignee_status_id' => 65,
        ]);

        ShipmentsJourneyController::add(
            $shipment->id,
            65,
            65,
            null,
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
