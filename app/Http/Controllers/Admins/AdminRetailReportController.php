<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Rider;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use DB;
use Yajra\Datatables\Datatables;

class AdminRetailReportController extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function sales_index(){
        ActivityTrailController::createActivityTrailLog(Auth::id(),239);

        $retail_centers = DB::connection('reports')->table('retail_trax_centers')->where('status', 1)->select('id','name')->get();
        $retail_franchises = DB::connection('reports')->table('retail_franchises')->where('status', 1)->select('id','name')->get();


        $cities = DB::connection('reports')->table('cities')->select('id','name')->get();
        $hubs = DB::connection('reports')->table('cities')->where('hub',1)->select('id','name')->get();
        $statuses = DB::connection('reports')->table('shipment_status')->whereNotIn('id',[1,17])->get();
        $sales_persons = DB::connection('reports')->table('admins')->join('admin_roles as ar', 'admins.role_id', '=', 'ar.id')->select(['admins.id', 'admins.name'])->where('ar.department_id', 7)->get();

        $business_categories = DB::connection('reports')->table('business_categories')->select('id', 'name')->get();
        return view('admin.reports.retail.sales')->with(['retail_centers' => $retail_centers, 'retail_franchises' => $retail_franchises ,'cities'=>$cities,'hubs'=>$hubs,'statuses'=>$statuses]);
    }
    public function sales_list(Request $request){
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),240);
        }

        $from = $request->get('search_date_from');
        $from = Carbon::parse($from)->setTimeFromTimeString('05:59:59');
        $to = $request->get('search_date_to');
        $to = Carbon::parse($to)->addDay()->setTimeFromTimeString('06:00:00');

        $sales = DB::connection('reports')->table('shipments')->join('retail_shipments as rs', 'rs.shipment_id', '=','shipments.id')
            ->leftjoin('retail_users as ru','ru.id','=','rs.retail_user_id')
            ->leftjoin('retail_shipper_infos as rsi', 'rsi.id', '=', 'rs.shipper_account_no')
            ->leftJoin('retail_franchises as rf', function ($join) {
                $join->on('rf.id', '=', 'ru.category_id')
                    ->where('ru.category','=', DB::raw(1));
            })
            ->leftJoin('retail_trax_centers as rc', function ($join) {
                $join->on('rc.id', '=', 'ru.category_id')
                    ->where('ru.category','=', DB::raw(2));
            })
            ->join('shipment_status as ss','ss.id','=','shipments.shipper_status_id')
            ->join('retail_shipping_modes as rsm','rsm.id','=','rs.shipping_mode')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->leftjoin('retail_trax_centers as rtc', 'rtc.pickup_address_id', '=', 'shipments.pickup_address_id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->leftjoin('zones as oz', 'oz.id', '=', 'oc.zone_id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->leftjoin('zones as dz', 'dz.id', '=', 'dc.zone_id')
            ->join('cities as h' ,'dc.hub_id', '=' , 'h.id')
            ->leftjoin('shipment_payment_status as sps', 'shipments.payment_status_id', '=' , 'sps.id')
            ->leftjoin('retail_pickup_note_shipments as pns',function($join){
                $join->on('pns.shipment_id','=','shipments.id')
                    ->where('pns.retail_pickup_note_id','=',
                        DB::connection('reports')->raw('(select max(retail_pickup_note_id) from retail_pickup_note_shipments where retail_pickup_note_shipments.shipment_id = shipments.id)'));
            })
//            ->leftjoin('delivery_note_station_deposit_notes as dnsdn', 'ds.delivery_note_id', '=', 'dnsdn.delivery_note_id')
            ->leftJoin('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where('sj.id','=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)'));
            })
            ->leftJoin('retail_pending_payment_shipments as pps', function ($join) {
                $join->on('pps.shipment_id', '=', 'shipments.id')
                    ->where('pps.id','=',
                        DB::connection('reports')->raw('(select max(id) from retail_pending_payment_shipments where retail_pending_payment_shipments.shipment_id = shipments.id and retail_pending_payment_shipments.type != 2)'));
            })
            ->leftJoin('retail_done_payment_shipments as dps', function ($join) {
                $join->on('dps.shipment_id', '=', 'shipments.id')
                    ->where('dps.id','=',
                        DB::connection('reports')->raw('(select max(id) from retail_done_payment_shipments where retail_done_payment_shipments.shipment_id = shipments.id and retail_done_payment_shipments.type != 2)'));
            })
            ->leftJoin('shipments_journey as dr', function ($join) {
                $join->on('dr.shipment_id', '=', 'shipments.id')
                    ->where('dr.id','=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id In(14,20,30,36,37) and shipments_journey.verification = 1)'));
            })
            ->leftjoin('products as p','p.id','=','rs.product_type_id')
            ->leftjoin('retail_references as rref','rref.shipment_id','=','shipments.id')
            ->select('p.product_name as category','shipments.id as shipment_id','shipments.tracking_number','shipments.tracking_number as tracking_number_link', 'ru.name as booked_by', 'ru.category as retail_category','ru.id as booked_by_id', 'rsi.shipper_name', 'rf.id as franchise_account_id','rf.name as franchise', 'rc.id as retail_account_id','rc.name as retail_center','ss.name as current_status','rsm.name as service_type','sj.created_at as arrival_date','oc.name as origin','dc.name as destination','h.name as hub', 'oz.name as origin_zone', 'dz.name as destination_zone','shipments.amount as collection_amount','sps.name as payment_status','pps.amount as p_collection_amount','shipments.actual_weight','rs.weight_charges','rs.cash_handling_charges','rs.fuel_surcharge','rs.total_charges as total_charges', 'rs.gst as gst','pps.payable as p_net_payable','dps.amount as d_collection_amount','dps.payable as d_net_payable','dr.created_at as delivered_or_returned', 'dps.retail_done_payment_id as payment_id', 'shipments.shipper_status_id as shipment_status' , 'dr.shipper_status_id as dr_status_id', 'pns.retail_pickup_note_id as pncc_id','rtc.name as retail_trax_center_name', 'rref.ref as retail_reference')
            ->whereNotIn('shipments.shipper_status_id',[1,17])
            ->whereBetween('sj.created_at', [$from,$to])
            ->where('shipments.shipment_type', 2);
//        if (!$request->get('search_date_from') && !$request->get('search_date_to')) {
//            $now = Carbon::now();
//            $yesterday = Carbon::now()->subDays(3);
//            $sales = $sales->whereBetween('sj.created_at', [$yesterday,$now]);
//        }


        /*if (session('role_id') != 1 && session('role_id') != 4) {
            if (session('department_id') == 7) {
                $sales = $sales->whereIn('u.id', session('tagged_shippers'));
            }
            else {
                $sales = $sales->whereIn('dc.hub_id', session('hubs'));
            }
        }*/

        $datatable = Datatables::of($sales)
            ->addColumn('attempts', function($shipment){
                $out_for_delivery = DB::connection('reports')->table('shipments_journey')->where('shipment_id',$shipment->shipment_id)->where('shipper_status_id',5)->count();
                return $out_for_delivery;
            })

            ->editColumn('booked_by_id', function ($shipment) {
                return str_pad($shipment->booked_by_id, 6, '0', STR_PAD_LEFT);
            })

            ->editColumn('total_charges', function($shipment){
                return number_format($shipment->total_charges, 2);
            })
            ->editColumn('gst', function($shipment){
                return number_format($shipment->gst, 2);
            })
            ->editColumn('p_net_payable', function($shipment){
                return number_format($shipment->p_net_payable, 2);
            })
            ->editColumn('d_net_payable', function($shipment){
                return number_format($shipment->d_net_payable, 2);
            })
            ->editColumn('tracking_number_link', function ($shipments) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->editColumn('collection_amount', function($shipment){
                return number_format($shipment->collection_amount);
            })
            ->addColumn('franchise_center', function ($shipment) {
                if($shipment->retail_category){

                    if ($shipment->retail_category == 2) {
                        return $shipment->retail_center;
                    }
                    else {
                        return $shipment->franchise;
                    }
                }
                else{
                    return $shipment->retail_trax_center_name;
                }
            })
            ->addColumn('estimated_charges',function($sale){
                $estimated = '';
                $estimated = (($sale->weight_charges != null)? $sale->weight_charges:0) + (($sale->fuel_surcharge != null)? $sale->fuel_surcharge:0);
                return number_format((float)$estimated, 2);
            })
            ->editColumn('p_net_payable',function($sale){
                $payable = '';
                if($sale->p_net_payable != null){
                    $payable = $sale->p_net_payable;
                }else if($sale->d_net_payable != null){
                    $payable = $sale->d_net_payable;
                }
                return number_format((float)$payable, 2);
            });

        if($tracking = $request->get('search_tracking')){
            $datatable->where('shipments.tracking_number', '=', $tracking);
        }
        if($center = $request->get('search_retail_center')){
            $datatable->where('rf.id', '=', $center);
        }
        if($franchise = $request->get('search_retail_franchise')){
            $datatable->where('rf.id', '=', $franchise);
        }
        if($origin = $request->get('search_origin')){
            $datatable->where('oc.id', '=', $origin);
        }
        if($destination = $request->get('search_destination')){
            $datatable->where('dc.id', '=', $destination);
        }
        if($hub = $request->get('search_hub')){
            $datatable->where('h.id', '=', $hub);
        }
        if($status = $request->get('search_status')){
            $datatable->where('ss.id', '=', $status);
        }
        return $datatable->make(true);
    }
}
