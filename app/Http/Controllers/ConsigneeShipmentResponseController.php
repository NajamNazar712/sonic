<?php

namespace App\Http\Controllers;

use App\Http\Models\Admin\DeliveryNote;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentsJourney;
use Illuminate\Http\Request;

class ConsigneeShipmentResponseController extends Controller
{
    public function index(Request $request){
        $tracking_number = $request->route('tracking_number');
        $delivery_note_id = $request->route('delivery_note_id');

        if(!$tracking_number || !$delivery_note_id){
            return view('errors.404');
        }

        $tracking_number = (int) $tracking_number;
        $delivery_note_id = (int) $delivery_note_id;
        if(is_int($tracking_number) && is_int($delivery_note_id)){
            $shipment = Shipment::where('tracking_number', $tracking_number);
            if($shipment->exists()){
                $shipment = $shipment->select('id')->first();

                $delivery_note = DeliveryNote::where('id', $delivery_note_id)->where('status', 0);
                if($delivery_note->exists()){
                    $delivery_note = $delivery_note->first();

                    $delivery_note_shipment = $delivery_note->delivery_note_shipments->where('shipment_id', $shipment->id)->first();
                    if($delivery_note_shipment){
                        if($delivery_note_shipment->fake_status == 0){
                            $delivery_note_shipment->fake_status = 1;
                            $delivery_note_shipment->save();
                        }
                    }
                    return view('shipment_status_feedback');
                }
                return view('errors.404');
            }
            return view('errors.404');
        }
        return view('errors.404');
    }
}
