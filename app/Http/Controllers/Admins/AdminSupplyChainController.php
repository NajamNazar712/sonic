<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Shipment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminSupplyChainController extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function shipment_on_hold_index(){
        return view('admin.supply_chain.shipment_on_hold');
    }

    public function shipment_on_hold_details(Request $request){
        $shipment = Shipment::where('tracking_number')
        return response()->json(['status' => 0, 'shId' => $shipment->id, 'tracking_number' => $shipment->tracking_number, 'destination' => $destination, 'hub' => $hub, 'consignee_name' => $shipment->consignee_name, 'phone' => $shipment->consignee_phone_number_1, 'address' => $shipment->consignee_address, 'amount' => number_format($shipment->amount), 'service_type' => $service, 'shipment_status' => $status]);
    }
}
