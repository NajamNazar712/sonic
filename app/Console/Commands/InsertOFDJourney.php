<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\DeliveryController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Controllers\Webhook\ShipmentStatusWebhookController;
use App\Http\Models\Admin\DeliveryNote;
use App\Http\Models\Admin\DeliveryNoteShipment;
use App\Http\Models\ConsigneeUser;
use App\Http\Models\Rider\RiderDeliveryNoteRequest;
use App\Http\Models\Rider\RiderDeliveryNoteRequestShipment;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentsJourney;
use App\Http\Models\ShipperShipmentsSubscription;
use Illuminate\Console\Command;

class InsertOFDJourney extends Command
{
    protected $signature = 'ofd:insert 
                            {tracking_numbers : Comma-separated tracking numbers}
                            {delivery_note_id : Delivery Note ID}
                            {start_serial : Starting serial number}';

    protected $description = 'Insert OFD journey based on tracking number, delivery note, and serial no';

    public function handle()
    {
        $trackingNumbers = explode(',', $this->argument('tracking_numbers'));
        $deliveryNoteId  = $this->argument('delivery_note_id');
        $serial          = (int) $this->argument('start_serial');

        $this->info("Processing OFD journey insert...");
        $this->info("Tracking Numbers: " . implode(', ', $trackingNumbers));
        $this->info("Delivery Note ID: $deliveryNoteId");
        $this->info("Start Serial: $serial");

        // Fetch Shipments
        $shipments = Shipment::whereIn('tracking_number', $trackingNumbers)
            ->where('shipper_status_id', 5)
            ->get();

        if ($shipments->isEmpty()) {
            $this->error("No shipments found with these tracking numbers and status 5.");
            return;
        }

        $deliveryNote = DeliveryNote::find($deliveryNoteId);
        if (!$deliveryNote) {
            $this->error("Delivery Note ID not found!");
            return;
        }

        $riderId = null;

        if ($deliveryNote->request_note_id) {
            $request = RiderDeliveryNoteRequest::find($deliveryNote->request_note_id);
            $riderId = $request?->rider_id;
        }

        foreach ($shipments as $shipment) {

            // Check if DNS already exists
            $dns = DeliveryNoteShipment::where('shipment_id', $shipment->id)->latest()->first();

            if (!$dns) {
                $dns = new DeliveryNoteShipment();
                $dns->delivery_note_id   = $deliveryNoteId;
                $dns->status             = 1;
                $dns->shipment_id        = $shipment->id;
                $dns->notification       = 1;
                $dns->rider_information  = 1;
                $dns->ordering           = $serial;
                $dns->save();

                $this->info("Created DNS for Shipment {$shipment->tracking_number} → Serial {$serial}");
                $serial++;
            }

            // Insert OFD Journey
            ShipmentsJourneyController::add(
                $shipment->id,
                5,
                5,
                null,
                null,
                null,
                346,                       // OFD status
                $deliveryNoteId,
                $riderId
            );

            $this->info("Inserted OFD Journey for Shipment {$shipment->tracking_number}");
        }

        $this->info("=== COMPLETED SUCCESSFULLY ===");
    }
}
