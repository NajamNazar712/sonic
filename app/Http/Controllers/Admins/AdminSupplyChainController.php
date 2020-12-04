<?php

namespace App\Http\Controllers\Admins;

use App\http\Models\Admins\ShipmentOnHold;
use App\Http\Models\City;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentStatus;
use http\Env\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;

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
        $shipment = Shipment::where('tracking_number', $request->tracking_number);
        if($shipment->exists()){
            $shipment = $shipment->first();
            $on_hold_shipment = ShipmentOnHold::where('shipment_id', $shipment->id);
            if($on_hold_shipment->exists()){
                return response()->json(['status' => 1, 'error' => 'Shipment already updated as On-Hold!']);
            }
            else{
                $hub = City::find($shipment->consignee_city->hub_id)->name;
                return response()->json(['status' => 0, 'success' => 'Shipment found', 'shId' => $shipment->id, 'tracking_number' => $shipment->tracking_number, 'destination' => $shipment->consignee_city->name, 'hub' => $hub, 'consignee_name' => $shipment->consignee_name, 'phone' => $shipment->consignee_phone_number_1, 'address' => $shipment->consignee_address, 'amount' => number_format($shipment->amount), 'service_type' => $shipment->booking_type->booking_type, 'shipment_status' => $shipment->status_shipper->name]);
            }
        }
        else{
            return response()->json(['status' => 1, 'error' => 'Shipment not found!']);
        }
    }

    public function shipment_on_hold_store(Request $request){
        $shipment_ids = explode(',', $request->shipment_ids);
        $dispatch_date = $request->dispatch_date_formatted;
        $delivery_date = $request->delivery_date_formatted;
        if(count($shipment_ids) > 0){
            if($dispatch_date != '' && $dispatch_date != NULL){
                if($delivery_date != '' && $delivery_date != NULL){
                    foreach ($shipment_ids as $shipment_id){
                        $on_hold_shipment = new ShipmentOnHold();
                        $on_hold_shipment->shipment_id = $shipment_id;
                        $on_hold_shipment->delivery_date = $delivery_date;
                        $on_hold_shipment->dispatch_date = $dispatch_date;
                        $on_hold_shipment->added_by = Auth::id();
                        $on_hold_shipment->save();
                    }
                    return redirect()->back()->with('success', 'Shipments added as On-Hold');
                }
                else{
                    return redirect()->back()->with('error', 'Delivery date not Selected');
                }
            }
            else{
                return redirect()->back()->with('error', 'Dispatch date not Selected');
            }
        }
        else{
            return redirect()->back()->with('error', 'No Shipment Selected');
        }
    }

    public function shipment_on_hold_history(){

        $shipment_status = ShipmentStatus::select('id','name')->get();

        return view('admin.supply_chain.shipment_on_hold_history')->with(['shipment_status'=>$shipment_status]);
    }

    public function shipment_on_hold_history_list(Request $request){
        $shipment_on_hold_history = ShipmentOnHold::join('shipments as s', 'shipment_on_hold.shipment_id', '=', 's.id')
            ->select('s.tracking_number', 'shipment_on_hold.delivery_date', 'shipment_on_hold.dispatch_date', 'shipment_on_hold.status', 'shipment_on_hold.added_by', 'shipment_on_hold.created_at', 'shipment_on_hold.updated_at');

        $datatable = Datatables::of($shipment_on_hold_history);

        return $datatable->make(true);
    }
}
