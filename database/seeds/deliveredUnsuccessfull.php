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
            27140240011429, 22340240172190, 22340240077969, 22327539961275, 22318840186553, 22318840078450, 22318840078092, 22318840078090, 22318840077836, 22318839991828, 22318839991776, 22318839991767, 22318839961479, 22318839956646, 22318839956504, 22318839956495, 22318839954060, 20240240077007, 20240239322932, 20231340054024, 20218840050583, 152991384287, 14440240169544
            ];
        echo count($shipmentId);
        if ($shipmentId) {
            $shipmentId = Shipment::whereIn('tracking_number', $shipmentId)->get();

            foreach ($shipmentId as $shipment) {
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
