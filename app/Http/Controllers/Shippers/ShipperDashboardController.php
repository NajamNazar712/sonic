<?php

namespace App\Http\Controllers\Shippers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Admins\AdminPickupsController;
use App\Http\Controllers\ShipmentsJourneyController;

use App\Http\Models\DisputeType;
use App\Http\Models\PackagingCharge;
use App\Http\Models\Shipment;
use App\Http\Models\City;

use Auth;

use Yajra\Datatables\Datatables;
use Carbon\Carbon;

//use Illuminate\Support\Facades\Auth;

class ShipperDashboardController extends Controller
{
    public function __construct() {
      $this->middleware('auth:web,substitute_users');

      $this->middleware('Permission');
    }

    public function access_denied() {
        return view('client.access_denied');
    }

    public function orders_index() {
        $stats = array();
        $graph = array();
        $graph_dates = array();

        $stats['total'] = Shipment::where('user_id', session('user_id'))->count();
        $stats['booked'] = Shipment::where('user_id', session('user_id'))->where('shipper_status_id',1)->count();
        $stats['received'] = Shipment::where('user_id', session('user_id'))->whereIn('shipper_status_id',[2,3,4])->count();
        $stats['delivered'] = Shipment::where('user_id', session('user_id'))->whereIn('shipper_status_id',[14,16, 30, 36,37,39,40,41,47])->count();
        $stats['return'] = Shipment::where('user_id', session('user_id'))->whereIn('shipper_status_id',[20,21,22,23,24,25,26,27,28,29,31,32,33,34,35,38,42,43,44,45,46])->count();
        $stats['pending'] = Shipment::where('user_id', session('user_id'))->whereIn('shipper_status_id',[5,6,7,8,9,10,11,12,13,15,18,19])->count();

//        $graph_dates['current'] = Carbon::now();
//        $graph_dates['old_date'] = Carbon::now()->subDays(29);
//
//        for ($counter = 29; $counter >= 0; $counter--) {
//            $date = Carbon::now()->subDays($counter);
//            $comparison_date = $date->toDateString();
//            $graph['dates'][] = $date->format('d M');
//            $graph['booked'][] = Shipment::whereDate('created_at', $comparison_date)->where('user_id', session('user_id'))->where('shipper_status_id',1)->count();
//            $graph['received'][] = Shipment::whereDate('created_at', $comparison_date)->where('user_id', session('user_id'))->whereIn('shipper_status_id',[2,3,4])->count();
//            $graph['delivered'][] = Shipment::whereDate('created_at', $comparison_date)->where('user_id', session('user_id'))->whereIn('shipper_status_id',[14,16, 30, 36,37,39,40,41,47])->count();
//            $graph['pending'][] = Shipment::whereDate('created_at', $comparison_date)->where('user_id', session('user_id'))->whereIn('shipper_status_id',[5,6,7,8,9,10,11,12,13,15,18,19])->count();
//            $graph['return'][] = Shipment::whereDate('created_at', $comparison_date)->where('user_id', session('user_id'))->whereIn('shipper_status_id',[20,21,22,23,24,25,26,27,28,29,31,32,33,34,35,38,42,43,44,45,46])->count();
//        }

        $cities = City::where('status',1)->select('id','name')->get();
        $dispute_types = DisputeType::whereIn('id',[5,9])->get();

        // return $cities;
//      return view('client.dashboard')->with(['stats'=>$stats,'graph'=>$graph,'dates'=>$graph_dates,'cities'=>$cities,'dispute_types'=>$dispute_types]);
      return view('client.dashboard')->with(['stats'=>$stats,'cities'=>$cities,'dispute_types'=>$dispute_types]);
    }
    public function orders_list(Request $request) {
        $shipments = Shipment::join('users as u', 'shipments.user_id', '=', 'u.id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->join('cities as h' ,'dc.hub_id', '=' , 'h.id')
            ->join('shipping_modes as sm','sm.id','=','shipments.shipping_mode_id')
            ->join('booking_types as bt','bt.id','=','shipments.booking_type_id')
            ->join('shipment_status as ss','ss.id','=','shipments.shipper_status_id')
            ->leftjoin('shipment_items as si','si.shipment_id','=','shipments.id')
            ->leftjoin('products as p','p.id','=','si.product_type_id')
            ->select(['shipments.id as shipment_id','shipments.tracking_number as tracking_number','shipments.order_id','bt.booking_type as service_type','ss.name as status','oc.name as origin','dc.name as destination','shipments.consignee_name','shipments.consignee_phone_number_1 as phone1','shipments.consignee_phone_number_2 as phone2','shipments.consignee_address','shipments.amount','p.product_name as product_type','shipments.created_at as booking_date','shipments.special_instructions as instructions','shipments.shipper_status_id'])
            ->where('shipments.user_id', session('user_id'))
            ->groupBy('shipments.id');

        return Datatables::of($shipments)
            ->editColumn('tracking_number', function ($shipments) {
                $route = route('cod.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->editColumn('phone1',function ($shipments){
                return $shipments->phone1."<br>".$shipments->phone2;
            })
            ->addColumn('action',function ($shipments) {
                $view_charges_button = '<button type="button" class="dropdown-item view_charges"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Charges</div></button>';
                $cancel_button = '<button type="button" class="dropdown-item cancel_order"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-crosshair"></i></div><div class="col-9 offset-1">Cancel</div></button>';
                $dispute_button = '<button type="button" class="dropdown-item dispute_modal"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-alert-circle"></i></div><div class="col-9 offset-1">Dispute</div></button>';

                if ($shipments->shipper_status_id != 17) {
                    $options = FALSE;

                    $dropdown = '
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                            <div class="dropdown-menu dropdown-menu-sm">
                    ';


                    if ($shipments->shipper_status_id > 1) {
                        $dropdown .= $view_charges_button;

                        $options = TRUE;
                    }

                    if ($shipments->shipper_status_id == 1 && (session('user_type') == 1 || in_array(2, session('permissions')))) {
                        $dropdown .= $cancel_button;

                        $options = TRUE;
                    }

                    if (session('user_type') == 1 || in_array(6, session('permissions'))) {
                        $dropdown .= $dispute_button;

                        $options = TRUE;
                    }


                    $dropdown .= '
                            </div>
                        </div>
                    ';

                    if ($options) {
                        return $dropdown;
                    }
                    else {
                        return '';
                    }
                }
                else {
                    return '';
                }
            })
            ->make(true);
    }
    public function order_cancel(Request $request){
        $shipment_id = $request->shipment_id;
        if($shipment_id){
            $shipment = Shipment::where('id',$shipment_id)->where('user_id', session('user_id'));
            if($shipment->exists()){
                $shipment = $shipment->first();
                $shipment->shipper_status_id = 17;
                $shipment->consignee_status_id = 17;
                $shipment->save();

                AdminPickupsController::cancel($shipment_id);

                ShipmentsJourneyController::add($shipment_id, 17, 17, NULL, NULL, session('user_id'), NULL);

                return response()->json(['status'=>1,'success'=>'Shipment has been cancelled successfully']);
            }else{
                return response()->json(['status'=>0,'error'=>'Shipment not found']);
            }
        }
    }
    public function get_shipment_charges(Request $request){
        $shipment_id = $request->shipment_id;
        $shipment = Shipment::find($shipment_id);
        $returnHTML = view('client/components/shipment_charges')->with(['shipment'=>$shipment])->render();
        return response()->json($returnHTML);
    }
    public function ecommerce() {
      return view('client.ecommerce');
    }
    public function orderList() {
      return view('client.order_management');
    }
    public function orderPending() {
      return view('client.pending_booked_orders');
    }
    public function statistics_search(Request $request){
        $graph = array();
        $destination = $request->destination;
        $current_date = $request->current_date;
        $old_date = $request->old_date;
        $date = $old_date;
        $dates = array();
        $dates[] = $date;
        while ($date != $current_date) {
            $date = date('Y-m-d H:i:s', strtotime($date . ' +1 day'));
            $dates[] = $date;
        }
        foreach ($dates as $this_date) {
            $comparison_date = $this_date;
            if($destination == ''){
                $graph['dates'][] = Carbon::parse($this_date)->format('d M');
                $graph['booked'][] = Shipment::whereDate('created_at', $comparison_date)->where('user_id', session('user_id'))->where('shipper_status_id',1)->count();
                $graph['received'][] = Shipment::whereDate('created_at', $comparison_date)->where('user_id', session('user_id'))->whereIn('shipper_status_id',[2,3,4])->count();
                $graph['delivered'][] = Shipment::whereDate('created_at', $comparison_date)->where('user_id', session('user_id'))->whereIn('shipper_status_id',[14,16, 30, 36,37,39,40,41,47])->count();
                $graph['pending'][] = Shipment::whereDate('created_at', $comparison_date)->where('user_id', session('user_id'))->whereIn('shipper_status_id',[5,6,7,8,9,10,11,12,13,15,18,19])->count();
                $graph['return'][] = Shipment::whereDate('created_at', $comparison_date)->where('user_id', session('user_id'))->whereIn('shipper_status_id',[20,21,22,23,24,25,26,27,28,29,31,32,33,34,35,38,42,43,44,45,46])->count();
            }else{
                $graph['dates'][] = Carbon::parse($this_date)->format('d M');
                $graph['booked'][] = Shipment::whereDate('created_at', $comparison_date)->where(['user_id'=> session('user_id'),'consignee_city_id'=>$destination,'shipper_status_id'=>1])->count();
                $graph['received'][] = Shipment::whereDate('created_at', $comparison_date)->where(['user_id'=> session('user_id'),'consignee_city_id'=>$destination])->whereIn('shipper_status_id',[2,3,4])->count();
                $graph['delivered'][] = Shipment::whereDate('created_at', $comparison_date)->where(['user_id'=> session('user_id'),'consignee_city_id'=>$destination])->whereIn('shipper_status_id',[14,16, 30, 36,37,39,40,41,47])->count();
                $graph['pending'][] = Shipment::whereDate('created_at', $comparison_date)->where(['user_id'=> session('user_id'),'consignee_city_id'=>$destination])->whereIn('shipper_status_id',[5,6,7,8,9,10,11,12,13,15,18,19])->count();
                $graph['return'][] = Shipment::whereDate('created_at', $comparison_date)->where(['user_id'=> session('user_id'),'consignee_city_id'=>$destination])->whereIn('shipper_status_id',[20,21,22,23,24,25,26,27,28,29,31,32,33,34,35,38,42,43,44,45,46])->count();
            }

        }
        return response()->json(['status'=>1,'graph'=>$graph]);
    }
}