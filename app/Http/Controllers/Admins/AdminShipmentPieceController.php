<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Models\Shipment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;

class AdminShipmentPieceController extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function hold_add_index(){
        return view('admin.shipment_pieces.add');
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
        $shipment_ids = explode(',', $request->shipment_ids);
        if(count($shipment_ids) > 0){
            foreach ($shipment_ids as $shipment_id){
                $shipment = Shipment::find($shipment_id);
                if($shipment){
                    $shipment->shipper_status_id = 62;
                    $shipment->consignee_status_id = 62;
                    $shipment->save();
                    ShipmentsJourneyController::add($shipment_id, 62, 62, NULL, NULL, NULL, Auth::id());
                }
            }

            return redirect()->back()->with('success', 'Shipments successfully updated!');

        }
        return redirect()->back()->with('error', 'No Shipments Selected!');
    }

    public function hold_index(){
        return view('admin.shipment_pieces.list');
    }
}
