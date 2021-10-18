<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\ShipmentScanningJourneyController;
use App\Http\Models\Admin\DeliveryNoteStationDepositNote;
use App\Http\Models\Admin\ShipmentOnHold;
use App\Http\Models\City;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentStatus;
use http\Env\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;
use function foo\func;
use App\Http\Controllers\Admins\ActivityTrailController;

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
            if(in_array($shipment->shipper_status_id, [2, 4])){
                $on_hold_shipment = ShipmentOnHold::where('shipment_id', $shipment->id);
                if($on_hold_shipment->exists()){
                    return response()->json(['status' => 1, 'error' => 'Shipment already updated as On-Hold!']);
                }
                else{
                    $hub = City::find($shipment->consignee_city->hub_id)->name;
                    ShipmentScanningJourneyController::add($shipment->id,23,1,Auth::id(),null,null);
                    return response()->json(['status' => 0, 'success' => 'Shipment found', 'shId' => $shipment->id, 'tracking_number' => $shipment->tracking_number, 'destination' => $shipment->consignee_city->name, 'hub' => $hub, 'consignee_name' => $shipment->consignee_name, 'phone' => $shipment->consignee_phone_number_1, 'address' => $shipment->consignee_address, 'amount' => number_format($shipment->amount), 'service_type' => $shipment->booking_type->booking_type, 'shipment_status' => $shipment->status_shipper->name]);
                }
            }
            else{
                return response()->json(['status' => 1, 'error' => 'Shipment can\'t be marked as On-Hold!']);
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
        ActivityTrailController::createActivityTrailLog(Auth::id(),18);
        $shipment_status = ShipmentStatus::select('id','name')->get();

        return view('admin.supply_chain.shipment_on_hold_history')->with(['shipment_status'=>$shipment_status]);
    }

    public function shipment_on_hold_history_list(Request $request)
    {
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),78);
        }
        $shipment_on_hold_history = ShipmentOnHold::join('shipments as s', 'shipment_on_hold.shipment_id', '=', 's.id')
            ->join('shipment_status as ss', 's.shipper_status_id', '=', 'ss.id')
            ->join('user_shipping_infos AS usi', 's.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', 's.consignee_city_id', '=', 'dc.id')
            ->join('admins as a', 'shipment_on_hold.added_by', '=', 'a.id')
            ->join('users as u', 's.user_id', '=', 'u.id')
            ->leftJoin('shipments_journey as sja', function ($join) {
                $join->on('sja.shipment_id', '=', 's.id')
                    ->where('sja.id','=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = s.id and shipments_journey.shipper_status_id = 2)'));
            })
            ->leftJoin('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 's.id')
                    ->where('sj.id','=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = s.id)'));
            })
            ->select('shipment_on_hold.id as id', 's.tracking_number', 'shipment_on_hold.delivery_date', 'shipment_on_hold.dispatch_date', 'ss.name as status', 'a.name as added_by', 'shipment_on_hold.created_at', 'shipment_on_hold.status as shipment_on_hold_status', 'u.name as shipper_name', 'oc.name as origin', 'dc.name as destination', 'sj.created_at as last_status_date', 'sja.created_at as arrival_status_date');

        $datatable = Datatables::of($shipment_on_hold_history)
            ->editColumn('tracking_number_link',function ($stock_requests){
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$stock_requests->tracking_number' class='tracking' target='_blank'>$stock_requests->tracking_number</a></u>";
            })
            ->addColumn('action', function ($allow_dispatch) {
                if ($allow_dispatch->shipment_on_hold_status) {
                    if (session('role_id') == 1 || in_array(404, session('permissions'))) {
                        $allow_dispatch_button = '<button type="button" class="dropdown-item allow_dispatch_notes"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-list"></i></div><div class="col-9 offset-1">Allow Dispatch/Delivery</div></button>';
                        $dropdown = '
                              <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                                <div class="dropdown-menu dropdown-menu-sm">
                            ';
                        $dropdown .= $allow_dispatch_button;
                        $dropdown .= '
                    </div>
                  </div>
                ';
                    return $dropdown;
                    }else {
                        return '';
                    }
                } else {
                    return '';
                }
            })
            ->editColumn('shipment_on_hold_status', function($on_hold){
                if ($on_hold->shipment_on_hold_status == 0){
                    $on_hold->shipment_on_hold_status = 'Allowed';
                } else{
                    $on_hold->shipment_on_hold_status = 'On-Hold';
                }

                return $on_hold->shipment_on_hold_status;
            });


        return $datatable->make(true);
    }

    public function allow_dispatch_delivery(Request $request){

        $id = $request->id;
        if ($id != null) {

            $shipment_on_hold = ShipmentOnHold::find($id);
            if ($shipment_on_hold) {
                $shipment_on_hold->status = 0;
                $shipment_on_hold->save();

                return response()->json(['status' => 0, 'success' => 'Shipment removed from On-Hold successfully!']);

            } else {
                return response()->json(['status' => 1, 'error' => 'Shipment Not Found']);
            }

        } else {
            return response()->json(['status' => 1, 'error' => 'Shipment Not Found']);
        }
    }
}
