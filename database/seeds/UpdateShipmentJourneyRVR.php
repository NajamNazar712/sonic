<?php

use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Models\Admin\DeliveryNote;
use App\Http\Models\Admin\DeliveryNoteShipment;
use App\Http\Models\Rider\RiderDeliveryNoteRequest;
use App\Http\Models\Shipment;
use App\Jobs\ProcessRvShipmentTicket;
use Illuminate\Database\Seeder;

class UpdateShipmentJourneyRVR extends Seeder
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
            15943837832485,
            144341037818475,
            22343837891313,
            22343837996777,
            22343837978485
        ];
        echo count($shipmentId);
        if ($shipmentId) {
            $shipmentId = Shipment::whereIn('tracking_number', $shipmentId)->get();

            foreach ($shipmentId as $shipment) {
                $deliveryNoteId = DeliveryNoteShipment::where('shipment_id', $shipment->id)->latest()->first();
                $delivertNote   = DeliveryNote::find($deliveryNoteId->delivery_note_id);
                $shipment->shipper_status_id = 12;
                $shipment->consignee_status_id = 12;
                $shipment->save();
                if ($delivertNote->request_note_id) {
                    $rider_for_delivery = RiderDeliveryNoteRequest::find($delivertNote->request_note_id);
                }
                ShipmentsJourneyController::add($shipment->id, 12, 12, 1, null, null, 346, $deliveryNoteId->delivery_note_id, $rider_for_delivery->rider_id);
                $rvData = [
                    'shipment_id' => $shipment->id,
                    'shipper_status_id' => 12,
                    'status_reason_id' => 1,
                    'shipment_user_id' => $shipment->user_id,
                    'call_count' => 0
                ];
                dispatch(new ProcessRvShipmentTicket($rvData)); 
            }
        }
    }
}
