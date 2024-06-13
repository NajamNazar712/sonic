<?php

use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Models\Admin\DeliveryNoteShipment;
use App\Http\Models\Shipment;
use Illuminate\Database\Seeder;

class reasonValidationRequiredJourney extends Seeder
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
            22343837944832
            ];
        echo count($shipmentId);
        if ($shipmentId) {
            $shipmentId = Shipment::whereIn('tracking_number', $shipmentId)->get();

            foreach ($shipmentId as $shipment) {
                $shipment->shipper_status_id = 12;
                $shipment->consignee_status_id = 12;
                $shipment->save();
                $deliveryNoteId = DeliveryNoteShipment::where('shipment_id', $shipment->id)->latest()->first();
                $deliveryNoteId->status = 1;
                $deliveryNoteId->save();
                ShipmentsJourneyController::add($shipment->id, 12, 12, null, null, null, 346, $deliveryNoteId->delivery_note_id,null,0);
            }
        }
    }
}
