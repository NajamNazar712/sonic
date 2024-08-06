<?php

use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Models\Admin\DeliveryNote;
use App\Http\Models\Admin\DeliveryNoteShipment;
use App\Http\Models\Rider\RiderDeliveryNoteRequest;
use App\Http\Models\Shipment;
use Illuminate\Database\Seeder;

class deliveredUnsuccessfull extends Seeder
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
            22320234315335
            ];
        echo count($shipmentId);
        if ($shipmentId) {
            $shipmentId = Shipment::whereIn('tracking_number', $shipmentId)->get();

            foreach ($shipmentId as $shipment) {
                $shipment->shipper_status_id = 8;
                $shipment->consignee_status_id = 8;
                $shipment->save();
                $deliveryNoteId = DeliveryNoteShipment::where('shipment_id', $shipment->id)->latest()->first();
                // $delivertNote   = DeliveryNote::find($deliveryNoteId->delivery_note_id);

                // if ($delivertNote->request_note_id) {
                //     $rider_for_delivery = RiderDeliveryNoteRequest::find($delivertNote->request_note_id);
                // }
                ShipmentsJourneyController::add($shipment->id, 8, 8, null, null, null, 346, $deliveryNoteId->delivery_note_id, null);
            }
        }
    }
}
