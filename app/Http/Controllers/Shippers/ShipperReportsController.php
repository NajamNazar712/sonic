<?php

namespace App\Http\Controllers\Shippers;

use App\Http\Models\City;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;

class ShipperReportsController extends Controller
{
    public function __construct() {
        $this->middleware('auth:web,substitute_users');

      $this->middleware('Permission');
    }
    public function qsr_index(){
        return view('client.reports.qsr_report');
    }
    public function qsr_list(Request $request){

        $shipments = Shipment::join('users as u', 'shipments.user_id', '=', 'u.id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
//            ->join('cities as h' ,'dc.hub_id', '=' , 'h.id')
            ->join('booking_types as bt','bt.id','=','shipments.booking_type_id')
            ->join('shipment_status as ss','ss.id','=','shipments.shipper_status_id')
            ->leftJoin('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where('sj.created_at','=',
                        DB::raw('(select max(created_at) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)'));
            })
            ->select(['shipments.id as shId','shipments.tracking_number','u.name as shipper','ss.name as history_status','bt.booking_type as service_type','sj.created_at as arrival','oc.name as origin','dc.name as destination','shipments.amount'])
            ->whereNotIn('shipments.shipper_status_id',[1,14,16,17,36,39,40,41,43,47])
        ->where('shipments.user_id',Auth::id());
        $datatable = Datatables::of($shipments)
            ->addColumn('aging',function ($shipments){
                $now = Carbon::now();
                $days = Carbon::now()->diffInDays($shipments->arrival);
                if($days == 0){
                    return "-";
                }else{
                    return $days;
                }
            });
//        if ($shipper = $request->get('search_shipper')) {
//            $datatable->where('u.id', '=', $shipper);
//        }
//        if ($origin = $request->get('search_origin')) {
//            $datatable->where('oc.id', '=', $origin);
//        }
//        if ($destination = $request->get('search_destination')) {
//            $datatable->where('dc.id', '=', $destination);
//        }
//        if ($hub = $request->get('search_hub')) {
//            $datatable->where('h.id', '=', $hub);
//        }
        if ($request->get('search_from') && $request->get('search_to')) {
            $from = $request->get('search_from');
            $to = $request->get('search_to');
            $datatable->whereBetween('sj.created_at', [$from,$to]);
        }

        return $datatable->make(true);
    }
    public function sales_index(){
        $cities = City::all('id','name');
        $statuses = ShipmentStatus::whereNotIn('id',[1,7,17,18])->get();
        return view('client.reports.sales_report')->with(['cities'=>$cities,'statuses'=>$statuses]);
    }
    public function sales_list(Request $request){
            $sales = Shipment::join('users as u','u.id','=','shipments.user_id')
                ->join('shipment_status as ss','ss.id','=','shipments.shipper_status_id')
                ->join('booking_types as bt','bt.id','=','shipments.booking_type_id')
                ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
                ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
                ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
                ->leftjoin('shipment_payment_status as sps', 'shipments.payment_status_id', '=' , 'sps.id')
                ->leftJoin('shipments_journey as sj', function ($join) {
                    $join->on('sj.shipment_id', '=', 'shipments.id')
                        ->where('sj.created_at','=',
                            DB::raw('(select max(created_at) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)'));
                })
                ->leftjoin('pending_payment_shipments as pps', 'shipments.id', '=', 'pps.shipment_id')
                ->select('shipments.tracking_number','u.id as account_no','u.name as shipper','ss.name as current_status','bt.booking_type as service_type','sj.created_at as arrival_date','oc.name as origin','dc.name as destination','sps.name as payment_status','shipments.amount as collection_amount','shipments.actual_weight','shipments.weight_charges','shipments.cash_handling_charges','shipments.insurance_charges','shipments.return_charges','shipments.replacement_charges','shipments.fuel_surcharge','shipments.packaging_material_charges','pps.gst','pps.charges as total_charges','pps.payable as net_payable')
                ->whereNotIn('shipments.shipper_status_id',[1,7,17,18])
                ->where('u.id',Auth::id());

            $datatable = Datatables::of($sales);
            if($tracking = $request->get('search_tracking')){
                $datatable->where('shipments.tracking_number', '=', $tracking);
            }
            if($origin = $request->get('search_origin')){
                $datatable->where('oc.id', '=', $origin);
            }
            if($destination = $request->get('search_destination')){
                $datatable->where('dc.id', '=', $destination);
            }
            if($status = $request->get('search_status')){
                $datatable->where('ss.id', '=', $status);
            }
            if ($request->get('search_date_from') && $request->get('search_date_to')) {
                $from = $request->get('search_date_from');
                $to = $request->get('search_date_to');
                $datatable->whereBetween('sj.created_at', [$from,$to]);
            }
            return $datatable->make(true);
    }


}

