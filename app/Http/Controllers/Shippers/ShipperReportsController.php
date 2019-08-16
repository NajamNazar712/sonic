<?php

namespace App\Http\Controllers\Shippers;

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

        $shipments = DB::connection('reports')->table('shipments')->join('users as u', 'shipments.user_id', '=', 'u.id')
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
            ->where('shipments.user_id', session('user_id'))
            ->orwhereIn('shipments.user_id', session('sister_users'));
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
        $cities = DB::connection('reports')->table('cities')->select('id','name')->get();
        $statuses = DB::connection('reports')->table('shipment_status')->whereNotIn('id',[1,17])->get();
        return view('client.reports.sales_report')->with(['cities'=>$cities,'statuses'=>$statuses]);
    }
    public function sales_list(Request $request){
            $sales = DB::connection('reports')->table('shipments')->join('users as u','u.id','=','shipments.user_id')
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
                ->where('u.id', session('user_id'))
                ->orwhereIn('shipments.user_id', session('sister_users'));

            $datatable = Datatables::of($sales)
                ->editColumn('s_collection_amount', function($shipment){
                    return number_format($shipment->s_collection_amount);
                })

                ->editColumn('d_collection_amount', function($shipment){
                    return number_format($shipment->d_collection_amount);
                })
                ->editColumn('weight_charges', function($shipment){
                    return number_format($shipment->weight_charges, 2);
                })
                ->editColumn('cash_handling_charges', function($shipment){
                    return number_format($shipment->cash_handling_charges, 2);
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
    public function summary_data(Request $request){
        $stats = array();
        $from = $request->from_date;
        $to = $request->to_date;
        $origin = $request->origin;
        $user = $request->user;
        $destination = $request->destination;
        if($from == null || $to == null){
            $today = Carbon::now()->endOfDay();
            $thirtyDays = Carbon::now()->subDays(29)->startOfDay();
        }else{
            $thirtyDays = $from;
            $today = $to;
        }
        $today = Carbon::now()->endOfDay();
        $thirtyDays = Carbon::now()->subDays(29)->startOfDay();
        $stats['total'] = DB::connection('reports')->table('shipments')->whereBetween('created_at',[$thirtyDays,$today])->where('user_id', $user);
        $stats['booked'] = DB::connection('reports')->table('shipments')->where('shipper_status_id',1)->whereBetween('created_at',[$thirtyDays,$today])->where('user_id', $user);
        $stats['canceled'] = DB::connection('reports')->table('shipments')->where('shipper_status_id',17)->whereBetween('created_at',[$thirtyDays,$today])->where('user_id', $user);
        $stats['received'] = DB::connection('reports')->table('shipments')->whereIn('shipper_status_id',[2,3,4])->whereBetween('created_at',[$thirtyDays,$today])->where('user_id', $user);
        $stats['delivered'] = DB::connection('reports')->table('shipments')->whereIn('shipper_status_id',[14,16, 30, 36,37,39,40,41,47])->whereBetween('created_at',[$thirtyDays,$today])->where('user_id', $user);
        $stats['return'] = DB::connection('reports')->table('shipments')->whereIn('shipper_status_id',[20,21,22,23,24,25,26,27,28,29,31,32,33,34,35,38,42,43,44,45,46,50])->whereBetween('created_at',[$thirtyDays,$today])->where('user_id', $user);
        $stats['in_process'] = DB::connection('reports')->table('shipments')->whereIn('shipper_status_id',[5,6,7,8,9,10,11,12,13,15,18,19,49,52])->whereBetween('created_at',[$thirtyDays,$today])->where('user_id', $user);

        if ($origin) {
            $stats['total'] = $stats['total']->whereExists(function($query) use ($origin) {
                $query->from('user_shipping_infos')
                    ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                    ->whereExists(function ($sub_query) use ($origin) {
                        $sub_query->from('cities')
                            ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                            ->where('cities.id', $origin);
                    });
            });
            $stats['booked'] = $stats['booked']->whereExists(function($query) use ($origin) {
                $query->from('user_shipping_infos')
                    ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                    ->whereExists(function ($sub_query) use ($origin) {
                        $sub_query->from('cities')
                            ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                            ->where('cities.id', $origin);
                    });
            });
            $stats['canceled'] = $stats['canceled']->whereExists(function($query) use ($origin) {
                $query->from('user_shipping_infos')
                    ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                    ->whereExists(function ($sub_query) use ($origin) {
                        $sub_query->from('cities')
                            ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                            ->where('cities.id', $origin);
                    });
            });
            $stats['received'] = $stats['received']->whereExists(function($query) use ($origin) {
                $query->from('user_shipping_infos')
                    ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                    ->whereExists(function ($sub_query) use ($origin) {
                        $sub_query->from('cities')
                            ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                            ->where('cities.id', $origin);
                    });
            });
            $stats['delivered'] = $stats['delivered']->whereExists(function($query) use ($origin) {
                $query->from('user_shipping_infos')
                    ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                    ->whereExists(function ($sub_query) use ($origin) {
                        $sub_query->from('cities')
                            ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                            ->where('cities.id', $origin);
                    });
            });
            $stats['return'] = $stats['return']->whereExists(function($query) use ($origin) {
                $query->from('user_shipping_infos')
                    ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                    ->whereExists(function ($sub_query) use ($origin) {
                        $sub_query->from('cities')
                            ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                            ->where('cities.id', $origin);
                    });
            });
            $stats['in_process'] = $stats['in_process']->whereExists(function($query) use ($origin) {
                $query->from('user_shipping_infos')
                    ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                    ->whereExists(function ($sub_query) use ($origin) {
                        $sub_query->from('cities')
                            ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                            ->where('cities.id', $origin);
                    });
            });
        }

        if ($destination) {
            $stats['total'] = $stats['total']->where('consignee_city_id', $destination);
            $stats['booked'] = $stats['booked']->where('consignee_city_id', $destination);
            $stats['received'] = $stats['received']->where('consignee_city_id', $destination);
            $stats['canceled'] = $stats['canceled']->where('consignee_city_id', $destination);
            $stats['delivered'] = $stats['delivered']->where('consignee_city_id', $destination);
            $stats['return'] = $stats['return']->where('consignee_city_id', $destination);
            $stats['in_process'] = $stats['in_process']->where('consignee_city_id', $destination);
        }

        $stats['total'] = number_format($stats['total']->count());
        $stats['booked'] = number_format($stats['booked']->count());
        $stats['canceled'] = number_format($stats['canceled']->count());
        $stats['received'] = number_format($stats['received']->count());
        $stats['delivered'] = number_format($stats['delivered']->count());
        $stats['return'] = number_format($stats['return']->count());
        $stats['in_process'] = number_format($stats['in_process']->count());
        return response()->json(['status' => 1, 'stats' => $stats]);
    }
    public function summary_index(){
        $stats = array();
        $today = Carbon::now()->endOfDay();
        $thirtyDays = Carbon::now()->subDays(29)->startOfDay();
        $stats['total'] = DB::connection('reports')->table('shipments')->whereBetween('created_at',[$thirtyDays,$today])->where('user_id', session('user_id'));
        $stats['booked'] = DB::connection('reports')->table('shipments')->where('shipper_status_id',1)->whereBetween('created_at',[$thirtyDays,$today])->where('user_id', session('user_id'));
        $stats['canceled'] = DB::connection('reports')->table('shipments')->where('shipper_status_id',17)->whereBetween('created_at',[$thirtyDays,$today])->where('user_id', session('user_id'));
        $stats['received'] = DB::connection('reports')->table('shipments')->whereIn('shipper_status_id',[2,3,4])->whereBetween('created_at',[$thirtyDays,$today])->where('user_id', session('user_id'));
        $stats['delivered'] = DB::connection('reports')->table('shipments')->whereIn('shipper_status_id',[14,16, 30, 36,37,39,40,41,47])->whereBetween('created_at',[$thirtyDays,$today])->where('user_id', session('user_id'));
        $stats['return'] = DB::connection('reports')->table('shipments')->whereIn('shipper_status_id',[20,21,22,23,24,25,26,27,28,29,31,32,33,34,35,38,42,43,44,45,46,50])->whereBetween('created_at',[$thirtyDays,$today])->where('user_id', session('user_id'));
        $stats['in_process'] = DB::connection('reports')->table('shipments')->whereIn('shipper_status_id',[5,6,7,8,9,10,11,12,13,15,18,19,49,52])->whereBetween('created_at',[$thirtyDays,$today])->where('user_id', session('user_id'));
        $stats['total'] = number_format($stats['total']->count());
        $stats['booked'] = number_format($stats['booked']->count());
        $stats['canceled'] = number_format($stats['canceled']->count());
        $stats['received'] = number_format($stats['received']->count());
        $stats['delivered'] = number_format($stats['delivered']->count());
        $stats['return'] = number_format($stats['return']->count());
        $stats['in_process'] = number_format($stats['in_process']->count());
        $cities = DB::connection('reports')->table('cities')->select(['id','name'])->get();
        $sister_users = DB::connection('reports')->table('merged_sister_account_mappings')->leftjoin('users as u', 'u.id', '=', 'merged_sister_account_mappings.sister_user_id')->where('head_user_id', session('user_id'))->select('u.id', 'u.name')->get();
        $user = DB::connection('reports')->table('users')->select('id', 'name')->where('id', session('user_id'))->first();
        return view('client.reports.summary')->with(['stats' => $stats, 'cities' => $cities, 'today' => $today, 'thirtyday' => $thirtyDays, 'user' => $user, 'sister_users' => $sister_users]);
    }

    public function summary_list(Request $request){
        $shipments = DB::connection('reports')->table('shipments')->join('users as u','u.id','=','shipments.user_id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->join('booking_types as bt','bt.id','=','shipments.booking_type_id')
            ->join('shipment_status as ss','ss.id','=','shipments.shipper_status_id')
            ->leftjoin('shipment_payment_status as sps', 'shipments.payment_status_id', '=' , 'sps.id')
            ->leftJoin('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where('sj.id','=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)'));
            })
            ->leftjoin('shipment_items as si', function ($join) {
                $join->on('si.shipment_id', '=', 'shipments.id')
                    ->where('si.type','=',0);
            })
            ->leftjoin('products as p','p.id','=','si.product_type_id')
            ->select(['u.name as user_name', 'shipments.id as shipment_id','shipments.order_id','shipments.tracking_number','shipments.amount as collection_amount','shipments.actual_weight','shipments.weight_charges','shipments.cash_handling_charges','ss.name as current_status','sps.name as payment_status','bt.booking_type as service_type','p.product_name','si.description','sj.created_at as arrival_date','oc.name as origin','dc.name as destination']);
            if ($request->get('search_date_from') && $request->get('search_date_to')) {
                $from = $request->get('search_date_from');
                $to = $request->get('search_date_to');
                $shipments = $shipments->whereBetween('shipments.created_at', [$from,$to]);
            }

        $datatable = Datatables::of($shipments)
            ->addColumn('tracking_number_link', function ($shipments) {
                $route = route('cod.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->editColumn('collection_amount', function ($shipments){
                return number_format($shipments->collection_amount);
            });
            if($user = $request->get('search_user')){
                $datatable->where('shipments.user_id', $user);
            }
            else{
                $datatable->where('shipments.user_id', session('user_id'));
            }
            if($origin = $request->get('search_origin')){
                $datatable->where('oc.id', '=', $origin);
            }
            if($destination = $request->get('search_destination')){
                $datatable->where('dc.id', '=', $destination);
            }
            if($card = $request->get('cards_filter')){
                switch ($card) {
                    case 'total':
                        $today = Carbon::now()->endOfDay();
                        $thirtyDays = Carbon::now()->subDays(29)->startOfDay();
                $datatable->whereBetween('shipments.created_at',[$thirtyDays,$today]);
                        break;
                    case 'booked':
                        $datatable->where('shipments.shipper_status_id',1);
                        break;
                    case 'received':
                        $datatable->whereIn('shipments.shipper_status_id',[2,3,4]);
                        break;
                    case 'delivered':
                        $datatable->whereIn('shipments.shipper_status_id',[14,16, 30, 36,37,39,40,41,47]);
                        break;
                    case 'returned':
                        $datatable->whereIn('shipments.shipper_status_id',[20,21,22,23,24,25,26,27,28,29,31,32,33,34,35,38,42,43,44,45,46,50]);
                        break;
                    case 'in_process':
                        $datatable->whereIn('shipments.shipper_status_id',[5,6,7,8,9,10,11,12,13,15,18,19,49,52]);
                        break;
                    case 'cancelled':
                        $datatable->where('shipments.shipper_status_id',17);
                        break;
                }


            }
            return $datatable->make(true);

    }
}

