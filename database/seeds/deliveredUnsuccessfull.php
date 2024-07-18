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
            485243407407, 341030240227780, 28430240164920, 27130240208046, 22330240259009, 22330240251099, 22330240250830, 22330240248188, 22330240244904, 22330240241676, 22330240241315, 22330240236450, 22330240198041, 22330240159535, 22330240143412, 22330240143399, 22330240078350, 22330240077205, 22330240075646, 22330240074471, 22330240074161, 22311440129051, 22311440125586, 22311440069298, 21330240210317, 20230240227516, 20230240227012, 20230240226332, 20230240225066, 20230240224828, 20230240203459, 20230240199314, 20230240194067, 20230240190183, 20230240172113, 20230240171805, 20230240168895, 20230240167815, 20230240163772, 20230240156339, 20230240154174, 20230240146764, 20230240145050, 20230240089053, 20230240035748, 20211739943949, 17430240224715, 14430240221502, 14430240160917, 14430240061791
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
