<?php

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
use Illuminate\Database\Seeder;

class outForDeliveryJourney extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $shipmentId = [
            202341037853105
            ];
        echo count($shipmentId);
        if ($shipmentId) {
            $shipmentId = Shipment::whereIn('tracking_number', $shipmentId)->get();

            foreach ($shipmentId as $shipment) {
                $deliveryNoteId = DeliveryNoteShipment::where('shipment_id', $shipment->id)->latest()->first();
                $delivertNote   = DeliveryNote::find($deliveryNoteId->delivery_note_id);
                
                if($delivertNote->request_note_id){
                    $rider_for_delivery = RiderDeliveryNoteRequest::find($delivertNote->request_note_id);
                }
                ShipmentsJourneyController::add($shipment->id, 5, 5, null, null,null, 346, $deliveryNoteId->delivery_note_id, $rider_for_delivery->rider_id);
            }
        }
    }
}
