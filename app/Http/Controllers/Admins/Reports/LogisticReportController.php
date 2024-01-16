<?php

namespace App\Http\Controllers\Admins\Reports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Admins\ActivityTrailController;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Shipper\User;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;


class LogisticReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }
    public function index() {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 718); //trail ID
        $shippers_ids = array();
        $setting = GlobalSettings::where('type', 'logistic_setting')->select('text')->first();
        if ($setting) {
            $shippers_ids = explode(',', $setting->text);
        }

        $shippers = User::whereIn('id', $shippers_ids)->select('id', 'name')->get();

        $cities = DB::connection('reports')->table('cities')->select('id', 'name')->get();
        $hubs = DB::connection('reports')->table('cities')->where('hub', 1)->select('id', 'name')->get();
        $statuses = DB::connection('reports')->table('shipment_status')->where('id', '!=', 17)->get();
        $shipping_modes = DB::connection('reports')->table('shipping_modes')->get(['id', 'mode']);
        $business_categories = DB::connection('reports')->table('business_categories')->select('id', 'name')->get();

        return view('admin.reports.logistic')->with(['shippers' => $shippers, 'cities' => $cities, 'hubs' => $hubs, 'statuses' => $statuses]);
    }

    public function list(Request $request)
    {
        $connection = 'reports';

        $shippers = GlobalSettings::where('type','logistic_setting')->select('text')->first();
        $special_shippers = explode(',', $shippers->text);

        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 719); //trail ID
        }

        $from = $request->get('search_date_from');
        $from = Carbon::parse($from)->toDateTimeString();
        $to = $request->get('search_date_to');
        $to = Carbon::parse($to)->toDateTimeString();

        $from_id = null;
        $to_id = null;
        $sj_from_id = 168982787;



        if ($from != null && $to != null) {
            
            $from_id = DB::connection($connection)->table('shipments')->select('id')->where('created_at', '>=', $from);
            if ($from_id->exists()) {
                $from_id = $from_id->first()->id;
                
                $to_id = DB::connection($connection)->table('shipments')->select(DB::raw('MAX(id) as id'))->where('created_at', '>=', $from)->where('created_at', '<=', $to);
                
                if ($to_id->exists()) {
                    $to_id = $to_id->first()->id;
                }
            }
            $sj_from_id = DB::connection($connection)->table('shipments_journey')->select('id')->where('created_at', '>=', $from);
            if ($sj_from_id->exists()) {
                $sj_from_id = $sj_from_id->first()->id;
            }
            else{
                // $sj_from_id = DB::connection($connection)->table('shipments_journey')->select('id')->where('created_at', '>=', $current_date)->first()->id;
                $sj_from_id = DB::connection($connection)->table('shipments_journey')->select('id')->latest()->first()->id;
            }
        }

        $sales = DB::connection($connection)->table('shipments')->join('users as u','u.id','=','shipments.user_id')
        ->join('shipment_status as ss','ss.id','=','shipments.shipper_status_id')
        ->join('booking_types as bt','bt.id','=','shipments.booking_type_id')
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
            ->leftJoin('shipments_journey as sj', function ($join) use ($connection, $sj_from_id) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->whereIn('sj.shipper_status_id', [1,2])
                    ->where('sj.id', '=',
                        DB::connection($connection)->raw("(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id IN(1,2) and shipments_journey.id >= $sj_from_id)"));
            })
            ->leftJoin('shipments_journey as sjr', function ($join) use ($connection, $sj_from_id) {
                $join->on('sjr.shipment_id', '=', 'shipments.id')
                    ->whereIn('shipments.shipper_status_id', [20, 21, 22, 23, 24, 25, 44, 47, 48, 57, 60])
                    ->where('sjr.id','=',
                        DB::connection($connection)->raw("(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id IN (12, 20) and shipments_journey.verification = 1 and shipments_journey.status_reason_id is not null and shipments_journey.id >= $sj_from_id)"));
            })
            ->leftJoin('shipments_journey as dr', function ($join) use ($connection, $sj_from_id) {
                $join->on('dr.shipment_id', '=', 'shipments.id')
                    ->where('dr.id','=',
                        DB::connection($connection)->raw("(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id In(14,25,30,36,37,38) and shipments_journey.verification = 1 and shipments_journey.id >= $sj_from_id)"));
            })
            ->leftjoin('shipment_items as si', function ($join) use ($connection) {
                $join->on('si.shipment_id', '=', 'shipments.id')
                    ->where('si.id', '=',
                        DB::connection($connection)->raw('(select max(id) from shipment_items where shipment_items.shipment_id = shipments.id and shipment_items.type = 0)'));
            })
            ->leftJoin('shipment_status_reason as ssr', 'ssr.id', '=', 'sjr.status_reason_id')
            ->select('shipments.id as shipment_id','shipments.tracking_number','shipments.order_id as order_id','shipments.tracking_number as tracking_number_link', 'shipments.consignee_name','u.name as shipper','usi.pickup_address as shipper_address','ss.name as current_status','sj.created_at as arrival_date', 'shipments.created_at as booking_date','dc.name as destination','h.name as hub', 'dr.created_at as delivered_or_returned','z.name as zone', 'dc.id as destination_city_id', 'shipments.shipper_status_id as shipment_status', 'shipments.consignee_address', 'shipments.consignee_phone_number_1', 'shipments.consignee_phone_number_2', 'si.description','si.quantity','shipments.pieces','shipments.estimated_weight', 'oc.name as origin')
            ->where('shipments.shipper_status_id', '!=', 17)
            ->whereIn('u.id', $special_shippers)
            ->whereBetween('sj.created_at', [$from,$to]);
   
        
        if($from != null && $to != null) {
            $sales = $sales->whereBetween('shipments.created_at', [$from, $to]);
            
            if ($from_id != null && $to_id != null) {
                $sales->where('shipments.id', '>=', $from_id)
                    ->where('shipments.id', '<=', $to_id);
            }
        }

        if (session('role_id') != 1) {
            if (session('department_id') == 7 && !in_array(session('id'), session('sale_users_bypass'))) {
                $sales = $sales->whereIn('u.id', session('tagged_shippers'));
            } else {
                $sales = $sales->whereIn('dc.hub_id', session('hubs'));
            }
        }

        $datatable = Datatables::of($sales)
            ->addColumn('aging', function ($shipments){
                $from = Carbon::parse($shipments->booking_date);
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
            
            ->addColumn('consignee_phone', function ($shipments) {
                return $shipments->consignee_phone_number_1 . "<br>" . $shipments->consignee_phone_number_2;
            })
            ->addColumn('consignee_phone_excel', function ($shipments) {
                return $shipments->consignee_phone_number_1 . "," . $shipments->consignee_phone_number_2;
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

        $search_shipper = $request->get('search_shipper');
        if ($search_shipper) {
            $whereInArray = [];

            if (is_array($search_shipper)) {
                foreach ($search_shipper as $shipper) {
                    $whereInArray[] = $shipper;
                }
            } else {
                $whereInArray[] = $search_shipper;
            }

            $datatable->whereIn('shipments.user_id', $whereInArray);
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
