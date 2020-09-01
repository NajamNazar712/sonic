<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Shipment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminShipmentPieceController extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function hold_index(){
        return view('admin.shipment_pieces.index');
    }
    public function hold_shipment_details(Request $request){
        $shipment = Shipment::where('tracking_number',$request->tracking_number);
        if($shipment->exists()) {
            $shipment = $shipment->first();
            if($shipment->pieces <= 1){
                return response()->json(['status' => 1, 'error' => 'Shipment doesn\'t have Multiple Pieces!']);
            }

            if($shipment->shipper_status_id != 1){
                return response()->json(['status' => 1, 'error' => 'Shipment not on Booked status anymore!']);
            }

            $details = array();
            $details['id'] = $shipment->id;
            $details['tracking_number'] = $shipment->tracking_number;
            $details['amount'] = $shipment->amount;
            $details['shipper'] = $shipment->user->name;
            $details['origin'] = $shipment->pickup_address->city->name;
            $details['destination'] = $shipment->consignee_city->name;
            $details['pieces'] = $shipment->pieces;
            return  response()->json(['status' => 0, 'details' => $details]);
        }else{
            return response()->json(['status' => 1, 'error' => 'Shipment not found!']);
        }
    }

    public function hold_shipment_submit(Request $request){
        return $request;
    }
}
