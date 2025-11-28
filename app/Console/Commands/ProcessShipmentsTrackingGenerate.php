<?php

namespace App\Console\Commands;

use App\Http\Controllers\Webhook\ShipmentStatusWebhookController;
use App\Http\Models\Shipment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessShipmentsTrackingGenerate extends Command
{
    protected $signature = 'shipments:missing-tracking-generate';
    protected $description = 'Fetch origin/destination of given shipments and store into table';

    public function handle()
    {
        // Put all shipment IDs here
        $shipmentIds = [
            57765502,
            57764628,
            57764627,
            57763719,
            57763718,
            57763190,
            57762374,
            57761442,
            57760278,
            57760277,
            57759357,
            57758258,
            57754572
        ];

        $shipments = Shipment::whereIn('id', $shipmentIds)->get();

        foreach ($shipments as $shipment) {

            $tracking_number =
                $shipment->pickup_address->city->id .
                $shipment->consignee_city_id .
                str_pad($shipment->id, 6, '0', STR_PAD_LEFT);

            // FIX 1: correct variable
            $shipment->tracking_number = $tracking_number;

            // save
            $shipment->save();
            // Debug result (but do not stop execution)
            $this->info("Processed Shipment ID: {$shipment->id} with Tracking Number: {$shipment->tracking_number}");
            // Webhook
            ShipmentStatusWebhookController::webhook_subscription($shipment->id, 1);
        }

        return Command::SUCCESS;
    }
}
