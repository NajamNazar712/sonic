<?php

namespace App\Http\Controllers\Shippers;

use App\Http\Models\PackagingCharge;
use App\Http\Models\Shipment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Yajra\Datatables\Datatables;

//use Illuminate\Support\Facades\Auth;

class ShipperDashboardController extends Controller
{
    public function __construct() {
      $this->middleware('auth');
    }

    public function orders_index() {
        $booked = Shipment::where('user_id',Auth::id())->count();
        $received = Shipment::where('user_id',Auth::id())->whereIn('shipper_status_id',[2,3,4])->count();
        $delivered = Shipment::where('user_id',Auth::id())->whereIn('shipper_status_id',[14,16, 30, 36,37,39,40,41,47])->count();
        $pending = Shipment::where('user_id',Auth::id())->whereIn('shipper_status_id',[5,6,7,8,9,10,11,12,13,15,18,19])->count();
        $return = Shipment::where('user_id',Auth::id())->whereIn('shipper_status_id',[20,21,22,23,24,25,26,27,28,29,31,32,33,34,35,38,42,43,44,45,46])->count();
      return view('client.dashboard')->with(['booked'=>$booked,'received'=>$received,'delivered'=>$delivered,'pending'=>$pending,'return'=>$return]);
    }
    public function orders_list(Request $request){
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
            ->select(['shipments.id as shipment_id','shipments.tracking_number as tracking_number','shipments.order_id','bt.booking_type as service_type','ss.name as status','oc.name as origin','dc.name as destination','shipments.consignee_name','shipments.consignee_phone_number_1 as phone1','shipments.consignee_phone_number_2 as phone2','shipments.consignee_address','shipments.amount','p.product_name as product_type','shipments.created_at as booking_date','shipments.shipper_status_id'])
            ->where('shipments.user_id',Auth::id())
            ->orderBy('shipments.id','desc')
            ->groupBy('shipments.id');
        return Datatables::of($shipments)
            ->editColumn('tracking_number', function ($shipments) {
                return "<a href='#' class='tracking'><u>$shipments->tracking_number</u></a>";
            })
            ->editColumn('phone1',function ($shipments){
                return $shipments->phone1."<br>".$shipments->phone2;
            })
            ->editColumn('booking_date', function ($shipments) {
                return $shipments->booking_date ? with(new Carbon($shipments->booking_date))->format('d/m/Y h:i:s A') : '';

            })
            ->addColumn('action',function ($shipments){
                $drop = " <span class='dropdown'>
                                            <button type='button' class='btn btn-success dropdown-toggle' data-toggle='dropdown'
                                                    aria-haspopup='true' aria-expanded='false'><i class='ft-settings'></i></button>
                                            <div class='dropdown-menu open-left arrow'>";
                if($shipments->shipper_status_id > 1) {
                    $drop .= "<a href='#' class='dropdown-item view_charges'><i class='ft-plus-circle primary'></i> View Charges</a>";
                }
                if($shipments->shipper_status_id == 1){
                    $drop .= "<a href='#' class='dropdown-item cancel_order'><i class='ft-crosshair primary'></i> Cancel</a>";
                }
                $drop .= "</div></span>";
                return $drop;
            })
            ->make(true);
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
}