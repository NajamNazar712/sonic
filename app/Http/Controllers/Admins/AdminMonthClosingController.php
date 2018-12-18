<?php

namespace App\Http\Controllers\Admins;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\ShipmentStatus;
use App\Http\Models\ShippingMode;
use App\Http\Models\BookingType;
use App\Http\Models\Shipment;

use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;



class AdminMonthClosingController extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function month_closing_index(){
        $shipment_status = ShipmentStatus::select('id','name')->get();
        $shipping_mode = ShippingMode::all();
        $service_type = BookingType::all();
        return view('admin.month_closing.index')->with(['shipment_status'=>$shipment_status,'shipping_mode'=>$shipping_mode,'service_type'=>$service_type]);;
    }

    public function month_closing_list(Request $request){
        $shipments = Shipment::join('users as u', 'shipments.user_id', '=', 'u.id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->join('cities as h' ,'dc.hub_id', '=' , 'h.id')
            ->join('shipping_modes as sm','sm.id','=','shipments.shipping_mode_id')
            ->join('booking_types as bt','bt.id','=','shipments.booking_type_id')
            ->join('shipment_status as ss','ss.id','=','shipments.shipper_status_id')
            ->leftJoin('shipments_journey', function ($join) {
                $join->on('shipments_journey.shipment_id', '=', 'shipments.id')
                    ->where('shipments_journey.id','=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id)'));
            })
            ->leftJoin('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where('sj.created_at','=',
                        DB::raw('(select max(created_at) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)'));
            })
            ->leftJoin('shipment_status_reason as ssr','ssr.id','=','shipments_journey.status_reason_id')
            ->select('shipments.id as shId','shipments.tracking_number','shipments.tracking_number as tracking','u.name as shipper','u.phone as shipper_phone1','u.phone2 as shipper_phone2','oc.name as origin','dc.name as destination','shipments.order_id','h.name as hub','shipments.consignee_name','shipments.consignee_phone_number_1','shipments.consignee_phone_number_2','shipments.consignee_address','shipments.amount','sm.mode','bt.booking_type as service_type','ss.name as status','ssr.name as reason','shipments_journey.remarks as remarks','shipments_journey.created_at as status_date','shipments_journey.created_at as last_status_date','sj.created_at as arrival')
            ->where('shipments.shipper_status_id', 51)
            ->groupBy('shipments.id');

        if (session('role_id') != 1) {
            $shipments = $shipments->whereIn('dc.hub_id', session('hubs'));
        }

        return Datatables::of($shipments)
            ->editColumn('tracking_number',function ($shipments){
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->editColumn('consignee_phone',function ($shipper){
                return "$shipper->consignee_phone_number_1 | $shipper->consignee_phone_number_2";
            })
            ->filterColumn('consignee_phone',function ($query,$keyword){
                $keyword = strtolower($keyword);
                if ($keyword != '') {
                    $query->where('shipments.consignee_phone_number_1', 'like', '%'.$keyword.'%')->orWhere('shipments.consignee_phone_number_2', 'like', '%'.$keyword.'%');
                }

                else {
                    $query->whereRaw('false');
                }
            })

            ->orderColumn('consignee_phone', 'shipments.consignee_phone_number_1 $1, shipments.consignee_phone_number_2 $1')
            ->editColumn('status_date',function ($shipments){
                if($shipments->status_date) {
                    if (2 - ((new \Carbon\Carbon($shipments->status_date, 'UTC'))->diffInDays()) < 0) {
                        return "<span class='danger font-weight-bold'>" . $shipments->status_date . "</span>";
                    } else {
                        return $shipments->status_date;
                    }
                }else{
                    return " - ";
                }
            })
            ->editColumn('arrival',function($shipments){
                if($shipments->arrival){
                    return $shipments->arrival;
                }else{
                    return " - ";
                }
            })
            ->filterColumn('status',function ($query,$keyword){

                if ($keyword != '') {
                    $query->where('ss.id',$keyword);
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->make(true);
    }
}
