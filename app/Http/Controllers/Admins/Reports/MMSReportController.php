<?php

namespace App\Http\Controllers\Admins\Reports;

use App\Http\Controllers\Admins\ActivityTrailController;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Shipper\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;

class MMSReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 566);
        $shippers = DB::connection('reports')->table('users')->whereIn('id', [15636, 16292, 15587, 17363, 17747, 3324, 1091, 10104, 20040, 22343, 22395, 22230, 22946, 14110, 19507, 14781])->whereIn('status', [3, 4])->select('id', 'name')->get();
        /*dd($shippers->toArray());*/
        $cities = DB::connection('reports')->table('cities')->select('id', 'name')->get();
        $hubs = DB::connection('reports')->table('cities')->where('hub', 1)->select('id', 'name')->get();
        $statuses = DB::connection('reports')->table('shipment_status')->whereNotIn('id', [1, 17])->get();
        $shipping_modes = DB::connection('reports')->table('shipping_modes')->get(['id', 'mode']);
        $business_categories = DB::connection('reports')->table('business_categories')->select('id', 'name')->get();

        $shippers = GlobalSettings::where('type','mms_setting')->select('text')->first();
        $shippers = explode(',',$shippers->text);
        $shippers = User::whereIn('id',$shippers)->select('id','name')->get();


        return view('admin.reports.mms')->with(['shippers' => $shippers, 'cities' => $cities, 'hubs' => $hubs, 'statuses' => $statuses]);
    }
    public function list(Request $request)
    {
        $connection = 'reports';

        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 567);
        }
        $arrival_from = Carbon::parse($request->arrival_time_from)->format('H:i:s');
        $arrival_to = Carbon::parse($request->arrival_time_to)->format('H:i:s');

        $from = $request->get('search_date_from');
        $from = Carbon::parse($from)->toDateTimeString();
        $to = $request->get('search_date_to');
        $to = Carbon::parse($to)->toDateTimeString();

        $from = str_replace('00:00:00', $arrival_from, $from);
        $to = str_replace('00:00:00', $arrival_to, $to);

        $sales = DB::connection($connection)->table('shipments')->join('users as u','u.id','=','shipments.user_id')
            ->join('shipment_status as ss','ss.id','=','shipments.shipper_status_id')
            ->leftJoin('booking_types as bt','bt.id','=','shipments.booking_type_id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->leftjoin('zones as z', 'z.id', '=', 'dc.zone_id')
            ->join('cities as h' ,'dc.hub_id', '=' , 'h.id')
            ->leftjoin('zone_class_cities as zcc', function($join) use ($connection) {
                $join->on('z.id', '=', 'zcc.zone_id')
                    ->on('dc.id', '=', 'zcc.city_id')
                    ->on('zone_classification_id', '=', DB::connection($connection)->raw('IF (shipments.shipping_mode_id IN (1, 4), 1, 2)'));
            })
            ->leftJoin('shipments_journey as sj', function ($join) use ($connection) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where('sj.id','=',
                        DB::connection($connection)->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)'));
            })
            ->leftJoin('shipments_journey as sjr', function ($join) use ($connection) {
                $join->on('sjr.shipment_id', '=', 'shipments.id')
                    ->whereIn('shipments.shipper_status_id', [20, 21, 22, 23, 24, 25, 44, 47, 48, 57, 60])
                    ->where('sjr.id','=',
                        DB::connection($connection)->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id IN (12, 20) and shipments_journey.verification = 1 and shipments_journey.status_reason_id is not null)'));
            })
            ->leftJoin('shipments_journey as dr', function ($join) use ($connection) {
                $join->on('dr.shipment_id', '=', 'shipments.id')
                    ->where('dr.id','=',
                        DB::connection($connection)->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id In(14,25,30,36,37,38) and shipments_journey.verification = 1)'));
            })
            ->leftjoin('shipment_items as si', function ($join) use ($connection) {
                $join->on('si.shipment_id', '=', 'shipments.id')
                    ->where('si.id', '=',
                        DB::connection($connection)->raw('(select max(id) from shipment_items where shipment_items.shipment_id = shipments.id and shipment_items.type = 0)'));
            })
            ->leftJoin('shipment_status_reason as ssr', 'ssr.id', '=', 'sjr.status_reason_id')
            ->select('shipments.id as shipment_id','shipments.tracking_number','shipments.order_id as order_id','shipments.tracking_number as tracking_number_link', 'shipments.consignee_name','u.name as shipper','usi.pickup_address as shipper_address','ss.name as current_status','sj.created_at as arrival_date', 'shipments.created_at as booking_date','dc.name as destination','h.name as hub', 'dr.created_at as delivered_or_returned','z.name as zone', 'dc.id as destination_city_id', 'shipments.shipper_status_id as shipment_status', 'dr.received_or_refused_by', 'dr.cnic', 'dr.relation','ssr.name as reason', 'shipments.consignee_address', 'shipments.consignee_phone_number_1', 'shipments.consignee_phone_number_2')
            ->whereNotIn('shipments.shipper_status_id',[1,17])
            ->whereIn('u.id', [15636, 16292, 15587, 17363, 17747, 3324, 1091, 10104, 20040, 22343, 22395, 22230, 22946, 14110, 19507, 14781])
            ->whereBetween('sj.created_at', [$from,$to]);

        $from_id = DB::connection($connection)->table('shipments_journey')->select('id')->where('created_at', '>=', $from);
        if ($from_id->exists()) {
            $from_id = $from_id->first()->id;

            $to_id = DB::connection($connection)->table('shipments_journey')->select(DB::raw('MAX(id) as id'))->where('created_at', '>=', $from)->where('created_at', '<=', $to);

            if ($to_id->exists()) {
                $to_id = $to_id->first()->id;

                $sales->where('sj.id', '>=', $from_id)
                    ->where('sj.id', '<=', $to_id);
            }
        }

//        if (!$request->get('search_date_from') && !$request->get('search_date_to')) {
//            $now = Carbon::now();
//            $yesterday = Carbon::now()->subDays(3);
//            $sales = $sales->whereBetween('sj.created_at', [$yesterday,$now]);
//        }

        if (session('role_id') != 1) {
            if (session('department_id') == 7 && !in_array(session('id'), session('sale_users_bypass'))) {
                $sales = $sales->whereIn('u.id', session('tagged_shippers'));
            } else {
                $sales = $sales->whereIn('dc.hub_id', session('hubs'));
            }
        }

        $datatable = Datatables::of($sales)
            ->addColumn('aging', function ($shipments){
                $from = Carbon::parse($shipments->arrival_date);
                $days = Carbon::now()->diffInDays($from);
                if ($days == 0) {
                    return "-";
                } else {
                    return $days;
                }
            })
            ->editColumn('tracking_number_link', function ($shipments) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->editColumn('delivered_or_returned', function ($sale) {
                if (in_array($sale->shipment_status, [14, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 45, 46, 25])) {
                    return $sale->delivered_or_returned;
                } else {
                    return '';
                }
            })
            ->editColumn('received_or_refused_by', function ($sale) {
                if (in_array($sale->shipment_status, [14, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 45, 46, 25])) {
                    $received_or_refused_by = '';
                    if($sale->received_or_refused_by){
                        $received_or_refused_by = $sale->received_or_refused_by;
                    }
                    return $received_or_refused_by;
                } else {
                    return '';
                }
            })

            ->editColumn('relation', function ($sale) {
                if (in_array($sale->shipment_status, [14, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 45, 46, 25])) {
                    $relation = '';
                    if($sale->relation){
                        $relation = $sale->relation;
                    }
                    return $relation;
                } else {
                    return '';
                }
            })

            ->editColumn('cnic', function ($sale) {
                if (in_array($sale->shipment_status, [14, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 45, 46, 25])) {
                    $cnic = '';
                    if($sale->cnic){
                        $cnic = $sale->cnic;
                    }
                    return $cnic;
                } else {
                    return '';
                }
            })
            ->addColumn('consignee_phone', function ($shipments) {
                return $shipments->consignee_phone_number_1 . "<br>" . $shipments->consignee_phone_number_2;
            })
            ->filterColumn('consignee_phone', function ($query, $keyword) {
                $keyword = strtolower($keyword);

                $keyword = str_replace('-', '', $keyword);

                if ($keyword != '') {
                    $query->where(function ($sub_query) use ($keyword) {
                        $sub_query->where('shipments.consignee_phone_number_1', 'like', '%' . $keyword . '%')
                            ->orWhere('shipments.consignee_phone_number_2', 'like', '%' . $keyword . '%');
                    });
                } else {
                    $query->whereRaw('false');
                }
            })
            ->orderColumn('consignee_phone', 'shipments.consignee_phone_number_1 $1, shipments.consignee_phone_number_2 $1');

        if ($tracking = $request->get('search_tracking')) {
            $datatable->where('shipments.tracking_number', '=', $tracking);
        }

        if ($search_shipper = $request->get('search_shipper')) {
            $datatable->where('shipments.user_id', '=', $search_shipper);
        }
        if ($destination = $request->get('search_destination')) {
            $datatable->where('dc.id', '=', $destination);
        }
        if ($hub = $request->get('search_hub')) {
            $datatable->where('h.id', '=', $hub);
        }
        if ($status = $request->get('search_status')) {
            $datatable->where('ss.id', '=', $status);
        }

        return $datatable->make(true);
    }
}
