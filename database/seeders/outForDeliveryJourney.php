<?php

namespace Database\Seeders;

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
            20221655464412,
            22349055519168,
            20221655522947,
            28821655506193,
            20221655480795];
        if ($shipmentId) {
            $shipmentId = Shipment::whereIn('tracking_number', $shipmentId)
            ->where('shipper_status_id', 5)
            ->get();
            echo count($shipmentId);
            $serial = 40;

            foreach ($shipmentId as $shipment) {

                $deliveryNoteId = DeliveryNoteShipment::where('shipment_id', $shipment->id)->latest()->first();
                if (!$deliveryNoteId) {
                    $deliveryNoteId =  new DeliveryNoteShipment();
                    $deliveryNoteId->delivery_note_id = 2590709;
                    $deliveryNoteId->status = 1;
                    $deliveryNoteId->shipment_id = $shipment->id;
                    $deliveryNoteId->notification = 1;
                    $deliveryNoteId->rider_information = 1;
                    $deliveryNoteId->ordering = $serial;
                    $deliveryNoteId->save();
                    $serial++;
                }
                $delivertNote   = DeliveryNote::find($deliveryNoteId->delivery_note_id);

                if ($delivertNote->request_note_id) {
                    $rider_for_delivery = RiderDeliveryNoteRequest::find($delivertNote->request_note_id);
                }
                ShipmentsJourneyController::add($shipment->id, 5, 5, null, null, null, 346, $deliveryNoteId->delivery_note_id, $rider_for_delivery->rider_id);
            }
        }
    }
}
