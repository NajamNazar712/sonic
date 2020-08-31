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
        $shipment = Shipment::where('tracking_number',$request->tracking_number)->where('shipper_status_id', 1)->where('pieces', '>', 1);
        if($shipment->exists()) {
            $shipment = $shipment->first();
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
}
