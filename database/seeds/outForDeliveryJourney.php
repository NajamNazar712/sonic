<?php

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
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Request;

class outForDeliveryJourney extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Request $request)
    {
        //
        $shipmentId = [
            152991398548,
            20217441625494,
            22317441632015,
            22317441685266,
            17417441645505,
            14417441685455,
            20217441655041,
            20217441698228,
            22317441680155,
            22317441636846,
            20217441696456,
            22317441688096,
            27117441749103,
            27117441769266,
            27117441742645,
            20217441676536,
            20217441658153,
            22317441724802,
            20217441652316,
            20217441645810,
            20217441623600,
            20217441692371,
            20217441670699,
            20217441693579,
            20217441711522,
            20217441698993,
            22317441676443,
            152991398469,
            ];
        
        if ($shipmentId) {
            $shipmentId = Shipment::whereIn('tracking_number', $shipmentId)->where('shipper_status_id', 5)->get();
            echo count($shipmentId);
            $serial = 27;

            foreach ($shipmentId as $shipment) {

                $deliveryNoteId = DeliveryNoteShipment::where('shipment_id', $shipment->id)->latest()->first();
                if(!$deliveryNoteId){
                    $deliveryNoteId =  new DeliveryNoteShipment();
                    $deliveryNoteId->delivery_note_id = 2333515;
                    $deliveryNoteId->shipment_id = $shipment->id;
                    $deliveryNoteId->notification = 1;
                    $deliveryNoteId->rider_information = 1;
                    $deliveryNoteId->ordering = $serial;
                    $deliveryNoteId->save();
                    $serial++;
                }
                $delivertNote   = DeliveryNote::find($deliveryNoteId->delivery_note_id);
                
                if($delivertNote->request_note_id){
                    $rider_for_delivery = RiderDeliveryNoteRequest::find($delivertNote->request_note_id);
                }
                ShipmentsJourneyController::add($shipment->id, 5, 5, null, null,null, 346, $deliveryNoteId->delivery_note_id, $rider_for_delivery->rider_id);
            }
        }
    }
}
