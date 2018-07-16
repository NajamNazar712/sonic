<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Shipment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class SamedayController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }
    public function sameday_index(){
        $shipments = Shipment::join('users as u', 'shipments.user_id', '=', 'u.id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->join('cities as h' ,'dc.hub_id', '=' , 'h.id')
            ->join('shipping_modes as sm','sm.id','=','shipments.shipping_mode_id')
            ->join('booking_types as bt','bt.id','=','shipments.booking_type_id')
            ->join('shipment_items as si','si.shipment_id','=','shipments.id')
            ->join('products as p','p.id','=','si.product_type_id')
            ->join('shipping_mode_same_day_timings as sms','sms.id','=','shipments.same_day_timing_id')
            ->join('shipment_status as ss','ss.id','=','shipments.shipper_status_id')
            ->leftJoin('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where('sj.created_at','=',
                        DB::raw('(select max(created_at) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)'));
            })
            ->leftJoin('shipments_journey as regular_delivery', function ($join) {
                $join->on('regular_delivery.shipment_id', '=', 'shipments.id')
                    ->where('regular_delivery.created_at','=',
                        DB::raw('(select max(created_at) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 14)'));
            })
            ->leftJoin('shipments_journey as replacement_delivery', function ($join) {
                $join->on('replacement_delivery.shipment_id', '=', 'shipments.id')
                    ->where('replacement_delivery.created_at','=',
                        DB::raw('(select max(created_at) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 30)'));
            })
            ->leftJoin('shipments_journey as trybuy_delivery', function ($join) {
                $join->on('trybuy_delivery.shipment_id', '=', 'shipments.id')
                    ->where('trybuy_delivery.created_at','=',
                        DB::raw('(select max(created_at) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 36)'));
            })
            ->select('shipments.id as shId','shipments.tracking_number','u.name as shipper','oc.name as origin','dc.name as destination','h.name as hub','shipments.consignee_name','shipments.consignee_phone_number_1 as consignee_phone','shipments.consignee_address','shipments.amount','sm.mode','bt.booking_type as service_type','p.product_name','sms.timing','sj.created_at as arrival','shipments.created_at as booked_date','regular_delivery.created_at as regular_delivered','trybuy_delivery.created_at as trybuy_delivered','replacement_delivery.created_at as replacement_delivered')

            ->where('shipments.shipping_mode_id',4)
            ->groupBy('shipments.id')->get();
        return $shipments;
        return view('admin.sameday.index');
    }
    public function sameday_list(Request $request){

    }
}
