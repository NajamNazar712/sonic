<?php

use App\Http\Models\Admin\DeliveryNoteStationDepositNote;
use App\Http\Models\Admin\StationDepositNote;
use App\Http\Models\Admin\StationDepositNoteAdjustment;
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
        $first_sdn_number = 115248;
        $adjustment_amount = 0;
        $station_deposit_note_adjustments = StationDepositNoteAdjustment::where('sdn_id', 115248);
        if($station_deposit_note_adjustments->exists()){
            $adjustment_amount = $station_deposit_note_adjustments->sum('amount');
        }

        dd($adjustment_amount);

        $station_deposit_notes = StationDepositNote::where('id', '>=', $first_sdn_number);
        if($station_deposit_notes->exists()){
            $station_deposit_notes = $station_deposit_notes->get();
            foreach ($station_deposit_notes as $station_deposit_note){
                $delivery_note_ids = DeliveryNoteStationDepositNote::where('station_deposit_note_id', $station_deposit_note->id)->pluck('delivery_note_id')->toArray();
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
    }
}
