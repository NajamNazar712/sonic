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
            ->editColumn('amount', function($shipment){
                return number_format($shipment->amount);
            })
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
        $statuses = ShipmentStatus::whereNotIn('id',[1,17])->get();
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
                ->leftJoin('pending_payment_shipments as pps', function ($join) {
                    $join->on('pps.shipment_id', '=', 'shipments.id')
                        ->where('pps.created_at','=',
                            DB::raw('(select max(created_at) from pending_payment_shipments where pending_payment_shipments.shipment_id = shipments.id)'));
                })
                ->leftJoin('done_payment_shipments as dps', function ($join) {
                    $join->on('dps.shipment_id', '=', 'shipments.id')
                        ->where('dps.created_at','=',
                            DB::raw('(select max(created_at) from done_payment_shipments where done_payment_shipments.shipment_id = shipments.id)'));
                })
                ->leftjoin('shipment_items as si', function ($join) {
                    $join->on('si.shipment_id', '=', 'shipments.id')
                        ->where('si.type','=',0);
                })
                ->leftjoin('products as p','p.id','=','si.product_type_id')
                ->select('p.product_name as product_name','si.description as description','shipments.tracking_number','shipments.order_id as order_id','u.id as account_no','u.name as shipper','ss.name as current_status','bt.booking_type as service_type','sj.created_at as arrival_date','oc.name as origin','dc.name as destination','shipments.amount as s_collection_amount','sps.name as payment_status','pps.amount as p_collection_amount','shipments.actual_weight','shipments.weight_charges','shipments.cash_handling_charges','dps.amount as d_collection_amount')
                ->whereNotIn('shipments.shipper_status_id',[1,17])
                ->where('u.id', session('user_id'));

            $datatable = Datatables::of($sales)
                ->editColumn('s_collection_amount', function($shipment){
                    return number_format($shipment->s_collection_amount);
                })

                ->editColumn('d_collection_amount', function($shipment){
                    return number_format($shipment->d_collection_amount);
                })
                ->editColumn('weight_charges', function($shipment){
                    return number_format($shipment->weight_charges);
                })
                ->editColumn('cash_handling_charges', function($shipment){
                    return number_format($shipment->cash_handling_charges);
                })
            ->editColumn('p_collection_amount',function($sale){
                $amount = '';
                if($sale->p_collection_amount != null){
                    $amount = $sale->p_collection_amount;
                }else if($sale->d_collection_amount != null){
                    $amount = $sale->d_collection_amount;
                }else{
                    $amount = $sale->s_collection_amount;
                }
                return number_format($amount);
            });

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

    public function summary_index(){
        $stats = array();
        $today = Carbon::now()->endOfDay();
        $thirtyDays = Carbon::now()->subDays(29)->startOfDay();
        $stats['total'] = Shipment::whereBetween('created_at',[$thirtyDays,$today])->where('user_id', session('user_id'));
        $stats['booked'] = Shipment::where('shipper_status_id',1)->whereBetween('created_at',[$thirtyDays,$today])->where('user_id', session('user_id'));
        $stats['canceled'] = Shipment::where('shipper_status_id',17)->whereBetween('created_at',[$thirtyDays,$today])->where('user_id', session('user_id'));
        $stats['received'] = Shipment::whereIn('shipper_status_id',[2,3,4])->whereBetween('created_at',[$thirtyDays,$today])->where('user_id', session('user_id'));
        $stats['delivered'] = Shipment::whereIn('shipper_status_id',[14,16, 30, 36,37,39,40,41,47])->whereBetween('created_at',[$thirtyDays,$today])->where('user_id', session('user_id'));
        $stats['return'] = Shipment::whereIn('shipper_status_id',[20,21,22,23,24,25,26,27,28,29,31,32,33,34,35,38,42,43,44,45,46,50])->whereBetween('created_at',[$thirtyDays,$today])->where('user_id', session('user_id'));
        $stats['in_process'] = Shipment::whereIn('shipper_status_id',[5,6,7,8,9,10,11,12,13,15,18,19,49,52])->whereBetween('created_at',[$thirtyDays,$today])->where('user_id', session('user_id'));
        $stats['total'] = number_format($stats['total']->count());
        $stats['booked'] = number_format($stats['booked']->count());
        $stats['canceled'] = number_format($stats['canceled']->count());
        $stats['received'] = number_format($stats['received']->count());
        $stats['delivered'] = number_format($stats['delivered']->count());
        $stats['return'] = number_format($stats['return']->count());
        $stats['in_process'] = number_format($stats['in_process']->count());
        return view('client.reports.summary')->with(['stats' => $stats]);
    }

}

