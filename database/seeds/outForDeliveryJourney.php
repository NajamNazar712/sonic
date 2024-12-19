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
        $shipmentId = [22314446468977, 27114446413572, 19514446412700, 485243431617, 14422346413845, 22314446402099, 15814446466130, 17414446418619, 17414446419658, 19514446451651, 20214446438897, 22314446400292, 22314446409657, 22314446411586, 22314446413612, 22314446468255, 22314446475657, 25114446436239, 28814446415152, 28814446416660, 29314446414670, 38314446485787, 14414446463423, 14414446471380];
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
