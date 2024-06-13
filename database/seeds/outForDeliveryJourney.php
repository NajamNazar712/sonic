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
            20243837728269, 202341037903970, 22343837745360, 22343837836916, 17443837817013, 22343837816461, 22343837964296, 20243837710038, 20243837755618, 22343837921784, 20243837824473, 20243837778489, 223341037787297, 20243837822515, 20246237433431, 20243837970484, 31543837882839, 20243837866360, 38343837947967, 38343837945417, 202341037873630, 284341037695791, 44343837839194, 31543837947076, 22343837811316, 22343837906236, 202341037699874, 22343837780846, 28343837837726, 288341037917747, 31543837882605, 15843837922277, 22343837726180, 22343837916321, 223341037915837
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
