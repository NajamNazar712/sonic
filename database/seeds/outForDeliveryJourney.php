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
            // 20220241204896,
            // 22320241184675,
            // 14420241177211,
            // 20220241218625,
            15920241182732,
            22320241192407,
            22320241186515,
            20220241252499,
            22320241205428,
            22320241189459,
            15820241171061,
            28420241175345,
            20220241244877,
            14420241194686,
            15820241186308,
            14420241180443,
            15820241186501,
            28420241220346,
            20220241278512,
            20220241257080,
            17420241188596,
            20220241267376,
            20220241254852,
            20220241275732,
            22320241191988,
            22320241185349,
            28420241222515,
            20220241257541,
            20220241281024,
            20220241297275,
            22320241193864,
            20220241260216,
            22320241175514,
            20220241259069,
            20220241291848,
            20220241282879,
            22320241169926,
            20220241264781,
            20220241258304
            ];
        
        if ($shipmentId) {
            $shipmentId = Shipment::whereIn('tracking_number', $shipmentId)->where('shipper_status_id',5)->get();
            echo count($shipmentId);
            foreach ($shipmentId as $shipment) {
                DeliveryController::add_shipments_in_receive(2310715,$shipment->id);

                // $deliveryNoteId = DeliveryNoteShipment::where('shipment_id', $shipment->id)->latest()->first();
                // $delivertNote   = DeliveryNote::find($deliveryNoteId->delivery_note_id);
                
                // if($delivertNote->request_note_id){
                //     $rider_for_delivery = RiderDeliveryNoteRequest::find($delivertNote->request_note_id);
                // }
                // ShipmentsJourneyController::add($shipment->id, 5, 5, null, null,null, 346, $deliveryNoteId->delivery_note_id, $rider_for_delivery->rider_id);
            }
        }
    }
}
