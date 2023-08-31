<?php

use App\Http\Models\ShipmentsJourney;
use Illuminate\Database\Seeder;
use App\Http\Models\Admin\DeliveryNote;
use App\Http\Models\Admin\DeliveryNoteShipment;
use App\Http\Models\Shipment;

class UpdateDNCCAmountAndShipmentLastMileAppReportReason extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $delivery_note_ids = [1634250, 1634276];
        foreach ($delivery_note_ids as $delivery_note_id){
            $delivery_note = DeliveryNote::find($delivery_note_id);
            if($delivery_note){
                $delivery_note_shipments = DeliveryNoteShipment::where('delivery_note_id', $delivery_note->id);
                if($delivery_note_shipments->exists()){
                    $dncc_amount = 0;
                    $delivered_count = 0;
                    $delivery_note_shipments = $delivery_note_shipments->get();
                    foreach ($delivery_note_shipments as $delivery_note_shipment){
                        $shipment = Shipment::find($delivery_note_shipment->shipment_id);
                        if($shipment){
                            $max_shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->orderBy('id', 'desc')->first();
                            if($max_shipment_journey){
                                $shipment->shipper_status_id = $max_shipment_journey->shipper_status_id;
                                $shipment->consignee_status_id = $max_shipment_journey->shipper_status_id;

                                if(in_array($delivery_note_shipment->status, [6,7])){
                                    $shipment->received_amount = $shipment->amount;
                                    $dncc_amount = $dncc_amount + $shipment->amount;
                                    $delivered_count++;
                                }
                                $shipment->save();
                            }
                        }
                    }
                    $delivery_note->received_cod_amount = $dncc_amount;
                    $delivery_note->delivered_shipments = $delivered_count;
                    $delivery_note->save();
                }
            }
        }
    }
}
