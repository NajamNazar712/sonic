<?php

namespace App\Http\Controllers\Admins;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReturnController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }
    public function return_view(){
        return view('admin.return.index');
    }
    public function return_marked_list(Request $request){
        $status = array(2, 4, 6, 7, 8, 9, 13, 15); //for pending deliveries
        $normal = 2;
        $shipments = Shipment::join('users as u', 'shipments.user_id', '=', 'u.id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->join('cities as h' ,'dc.hub_id', '=' , 'h.id')
            ->join('shipping_modes as sm','sm.id','=','shipments.shipping_mode_id')
            ->join('booking_types as bt','bt.id','=','shipments.booking_type_id')
            ->join('shipment_status as ss','ss.id','=','shipments.shipper_status_id')
            ->leftJoin('shipments_journey', function ($join) {
                $join->on('shipments_journey.created_at','=',
                    DB::raw('(select max(created_at) from shipments_journey where shipments_journey.shipment_id = shipments.id order by shipments_journey.created_at desc limit 1)'));
            })
            ->leftJoin('shipments_journey as sj', function ($join) {
                $join->on('sj.created_at','=',
                    DB::raw('(select created_at from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)'));
            })
            ->leftJoin('shipment_status_reason as ssr','ssr.id','=','shipments_journey.status_reason_id')
            ->select('shipments.id as shId','shipments.tracking_number','u.name as shipper','oc.name as origin','dc.name as destination','h.name as hub','shipments.consignee_name','shipments.consignee_phone_number_1 as phone','shipments.consignee_address','shipments.amount','sm.mode','bt.booking_type as service_type','ss.name as status','ssr.name as reason','shipments_journey.remarks as remarks','shipments_journey.created_at as status_date','sj.created_at as arrival')

//            ->whereRaw('IF (shipments.shipper_status_id = 2, (shipments.consignee_city_id = usi.city_id), TRUE)')
            ->where('shipments.shipper_status_id',12)
            ->groupBy('shipments.id');

        return Datatables::of($shipments)
            ->editColumn('status_date',function ($shipments){
                if($shipments->status_date) {
                    if (2 - ((new \Carbon\Carbon($shipments->status_date, 'UTC'))->diffInDays()) < 0) {
                        $older = Carbon::parse($shipments->status_date)->format('d/m/Y H:i A');
                        return "<span class='danger font-weight-bold'>$older</span>";
                    } else {
                        return Carbon::parse($shipments->status_date)->format('d/m/Y H:i A');
                    }
                }else{
                    return " - ";
                }
            })
            ->editColumn('arrival',function($shipments){
                if($shipments->arrival){
                    return Carbon::parse($shipments->arrival)->format('d/m/Y H:i A');
                }else{
                    return " - ";
                }
            })
            ->addColumn("action", function ($result) {
                return " <span class='dropdown'>
                                            <button type='button' class='btn btn-success dropdown-toggle' data-toggle='dropdown'
                                                    aria-haspopup='true' aria-expanded='false'><i class='ft-settings'></i></button>
                                            <div class='dropdown-menu open-left arrow'>
                                              <a href='#' class='dropdown-item' data-target-id=''><i class='ft-plus-circle primary'></i> Dispute</a>                                         
                                            </div></span>";
            })
            ->make(true);
    }
}
