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
            152991398469,
            20217441693579,
            20217441698228,
            22317441632015,
            22317441685266,
            17417441645505,
            14417441685455,
            20217441655041,
            20217441625494,
            152991398548,
            14417441572791,
            20217441575601,
            22317441583019,
            20217441590779,
            22317441597613,
            20217441554806,
            20217441544054,
            28317441504536,
            22317441501922,
            14417441414725
            ];
        
        if ($shipmentId) {
            $shipmentId = Shipment::whereIn('tracking_number', $shipmentId)->get();
            echo count($shipmentId);
            $serial = 53; 

            foreach ($shipmentId as $shipment) {
                $shipment->shipper_status_id = 8;
                $shipment->consignee_status_id = 8;
                $shipment->save();
                $deliveryNoteId = DeliveryNoteShipment::where('shipment_id', $shipment->id)->latest()->first();
                if(!$deliveryNoteId){
                    $deliveryNoteId =  new DeliveryNoteShipment();
                    $deliveryNoteId->delivery_note_id = 2333515;
                    $deliveryNoteId->status = 1;
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
                ShipmentsJourneyController::add($shipment->id, 8, 8, null, null,null, 346, $deliveryNoteId->delivery_note_id, $rider_for_delivery->rider_id);
            }
        }
    }
}
