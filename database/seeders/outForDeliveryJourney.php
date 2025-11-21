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
            22317456953573,
            15817456969959,
            20217456826319,
            20217456851496,
            20217456928303,
            20217456865085,
            20217456864989,
            20217456892049,
            20217456859847,
            20217456875013,
            20217456867378,
            14417456947729,
            20217456868500,
            20217456946308,
            20217456889746,
            20217456922737,
            22317456960118,
            152991645800,
            20217456871516,
            20217456849409,
            20217456877647,
            20217456867459,
            20217456880333,
            20217456892062,
            20217456909848,
            20217456900268,
            20217456881079,
            20217456885639,
            22328856963949,
            22317456921329,
            22317456981764,
            20217456895506,
            20217456937683,
            20217456874372,
            20217456923238,
            20217456863891,
            22317456980802,
            22317456984058,
            17417456999566,
            20217456918386,
            20217456925139,
            20217456938819,
            20217456935065,
            152991644891,
            152991644922,
            20217456857977,
            20217456912773,
            20217456820662,
            20217456861586,
            20217456908592,
            20217456917221,
            20217456900480,
            20217456851598,
            20217456862666,
            20217456903119
        ];
        if ($shipmentId) {
            $shipmentId = Shipment::whereIn('tracking_number', $shipmentId)
            ->where('shipper_status_id', 5)
            ->get();
            echo count($shipmentId);
            $serial = 6;

            foreach ($shipmentId as $shipment) {

                $deliveryNoteId = DeliveryNoteShipment::where('shipment_id', $shipment->id)->latest()->first();
                if (!$deliveryNoteId) {
                    $deliveryNoteId =  new DeliveryNoteShipment();
                    $deliveryNoteId->delivery_note_id = 3238510;
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
