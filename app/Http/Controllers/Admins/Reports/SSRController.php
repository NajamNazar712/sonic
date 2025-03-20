<?php

namespace App\Http\Controllers\Admins\Reports;

use App\Http\Controllers\Admins\ActivityTrailController;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;

class SSRController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function ssr_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 581);
        if (session('department_id') == 7 && (!in_array(session('id'), session('sale_users_bypass')))) {
            $shippers = DB::connection('reports')->table('users')->whereIn('id', session('tagged_shippers'))->whereIn('status', [3, 4])->select('id', 'name')->get();
        } else {
            $shippers = DB::connection('reports')->table('users')->whereIn('status', [3, 4])->select('id', 'name')->get();
        }

        $cities = DB::connection('reports')->table('cities')->select('id', 'name')->get();
        $hubs = DB::connection('reports')->table('cities')->where('hub', 1)->select('id', 'name')->get();
        $statuses = DB::connection('reports')->table('shipment_status')->whereNotIn('id', [1, 17])->get();
        $sales_persons = DB::connection('reports')->table('admins')->join('admin_roles as ar', 'admins.role_id', '=', 'ar.id')->select(['admins.id', 'admins.name'])->where('ar.department_id', 7)->get();
        $shipping_modes = DB::connection('reports')->table('shipping_modes')->get(['id', 'mode']);
        $business_categories = DB::connection('reports')->table('business_categories')->select('id', 'name')->get();
        return view('admin.reports.ssr')->with(['shippers' => $shippers, 'cities' => $cities, 'hubs' => $hubs, 'statuses' => $statuses, 'sales_persons' => $sales_persons, 'business_categories' => $business_categories, 'shipping_modes' => $shipping_modes]);
    }
    public function ssr_list(Request $request)
    {
        $connection = 'reports_2';

        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 582);
        }
        $arrival_from = Carbon::parse($request->arrival_time_from)->format('H:i:s');
        $arrival_to = Carbon::parse($request->arrival_time_to)->format('H:i:s');

        $from = $request->get('search_date_from');
        $from = Carbon::parse($from)->toDateTimeString();
        $to = $request->get('search_date_to');
        $to = Carbon::parse($to)->toDateTimeString();

        $from = str_replace('00:00:00', $arrival_from, $from);
        $to = str_replace('00:00:00', $arrival_to, $to);

        $from_new = Carbon::parse($from)->subMonths(6)->toDateTimeString();

        $date_from_delivered_return = $request->get('search_date_from_delivered_return');
        $date_to_delivered_return = $request->get('search_date_to_delivered_return');

        // Only parse if the date is not null
        $date_from_delivered_return = $date_from_delivered_return ? Carbon::parse($date_from_delivered_return)->toDateTimeString() : null;
        $date_to_delivered_return = $date_to_delivered_return ? Carbon::parse($date_to_delivered_return)->toDateTimeString() : null;

        // Replace '00:00:00' with actual arrival time if the date exists
        if ($date_from_delivered_return && strpos($date_from_delivered_return, '00:00:00') !== false) {
            $date_from_delivered_return = str_replace('00:00:00', $arrival_from, $date_from_delivered_return);
        }
        if ($date_to_delivered_return && strpos($date_to_delivered_return, '00:00:00') !== false) {
            $date_to_delivered_return = str_replace('00:00:00', $arrival_to, $date_to_delivered_return);
        }

        $sales = DB::connection($connection)->table('shipments')
            ->join('users as u','u.id','=','shipments.user_id')
            ->join('shipment_status as ss','ss.id','=','shipments.shipper_status_id')
            ->join('booking_types as bt','bt.id','=','shipments.booking_type_id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('user_shipping_infos AS rsi', 'shipments.return_address_id', '=', 'rsi.id')
            ->join('cities AS rc', 'rsi.city_id', '=', 'rc.id')
            ->join('zones as z', 'z.id', '=', 'oc.zone_id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->join('cities as h' ,'dc.hub_id', '=' , 'h.id')
            ->join('shipping_modes as sm', 'sm.id', '=', 'shipments.shipping_mode_id')
            ->join('business_categories as bc', 'bc.id', '=', 'shipments.business_category_id')
            ->join('cities as sc', 'u.city_id', '=', 'sc.id')
            ->join('zones as sz', 'sz.id', '=', 'sc.zone_id')
            ->leftJoin('shipments_journey as sj', function ($join) use ($connection) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where('sj.id','=',
                        DB::connection($connection)->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)'));
            })
            ->leftJoin('shipments_journey as dr', function ($join) use ($connection) {
                $join->on('dr.shipment_id', '=', 'shipments.id')
                    ->where('dr.id','=',
                        DB::connection($connection)->raw('(
                         select max(id) from shipments_journey sj2 where sj2.shipment_id = shipments.id and sj2.shipper_status_id NOT IN (14, 25, 30, 36, 37)
                         AND sj2.verification = 1
                        )'));
            })
            ->leftjoin('riders as r', 'r.id', '=', 'sj.rider_id')
            ->leftJoin('pending_payment_shipments as pps', function ($join) use ($connection) {
                $join->on('pps.shipment_id', '=', 'shipments.id')
                    ->where('pps.type', '!=',2 );
            })
            ->leftJoin('done_payment_shipments as dps', function ($join) use ($connection) {
                $join->on('dps.shipment_id', '=', 'shipments.id')
                    ->where('dps.type', '!=',2 );
            })
            ->leftjoin('sale_person_tags as spt', function ($join) {
                $join->on('spt.user_id', '=', 'shipments.user_id')
                    ->leftjoin('admins as adsp','adsp.id','=','spt.admin_id')
                    ->where('spt.status','=',0);
            })
            ->leftJoin('pending_invoice_shipments as pis', function ($join) use ($connection) {
                $join->on('pis.shipment_id', '=', 'shipments.id')
                    ->where('pis.type', '!=',2 );
            })
            ->leftJoin('invoice_shipments as iss', function ($join) use ($connection) {
                $join->on('iss.shipment_id', '=', 'shipments.id')
                    ->where('iss.type', '!=',2 );
            })
            ->leftjoin('invoices' ,'iss.invoice_id', '=' , 'invoices.id')

            ->select(
                'shipments.id as shipment_id',
                'shipments.tracking_number',
                'shipments.tracking_number as tracking_number_link',
                'u.id as account_no',
                'u.name as shipper',
                'sj.created_at as arrival_date',
                'shipments.actual_weight',
                'shipments.chargeable_weight',
                'shipments.weight_charges',
                'shipments.cash_handling_charges',
                'shipments.insurance_charges',
                'shipments.fuel_surcharge',
                'shipments.packaging_material_charges',
                DB::raw('SUM(pps.gst) as p_gst'),
                DB::raw('SUM(pps.charges) as p_total_charges'),
                DB::raw('SUM(dps.amount) as d_collection_amount'),
                DB::raw('SUM(dps.gst) as d_gst'),
                DB::raw('SUM(dps.charges) as d_total_charges'),
                DB::raw('SUM(dps.payable) as d_net_payable'),
                DB::raw('SUM(iss.gst) as is_gst'),
                DB::raw('SUM(pis.gst) as pis_gst'),
                'sm.id as shipping_mode_id',
                'z.name as zone',
                'oc.id as origin_city_id',
                'oc.name as origin_city_name',
                'dc.id as destination_city_id',
                'dc.name as destination_city_name',
                'dps.done_payment_id as payment_id',
                'shipments.booking_type_id',
                'usi.poc',
                'adsp.name as sales_person',
                'shipments.shipper_status_id as shipment_status',
                'u.account_type_id as account_type_id',
                'shipments.packaging_charges',
                'dr.shipper_status_id as dr_status_id',
                'sz.name as shipper_zone',
                'dr.created_at as delivered_or_returned'
            )
            ->whereNotIn('shipments.shipper_status_id',[1,17])
            ->wherebetween('shipments.created_at',[$from_new,$to])
            ->whereNotIn('u.id', [8761, 9358])
            ->groupBy('shipments.id');
            // ->whereBetween('sj.created_at', [$from,$to])
            ;

        if ($date_from_delivered_return && $date_to_delivered_return) {
            $sales->whereBetween('dr.created_at', [$date_from_delivered_return, $date_to_delivered_return]);
        } else {
            $sales->whereBetween('sj.created_at', [$from, $to]);
        }

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

        // if (!$request->get('search_date_from') && !$request->get('search_date_to')) {
        //     $now = Carbon::now();
        //     $yesterday = Carbon::now()->subDays(3);
        //     $sales = $sales->whereBetween('sj.created_at', [$yesterday,$now]);
        // }

        if (session('role_id') != 1 && (!in_array(session('id'), session('sale_users_bypass')))) {
            if (session('department_id') == 7) {
                $sales = $sales->whereIn('u.id', session('tagged_shippers'));
            } else {
                $sales = $sales->whereIn('dc.hub_id', session('hubs'));
            }
        }

        if ($tracking = $request->get('search_tracking')) {
            $sales->where('shipments.tracking_number', '=', $tracking);
        }
        if ($sales_person = $request->get('search_sales_person')) {
            $sales->where('adsp.id', '=', $sales_person);
        }
        if ($search_shipper = $request->get('search_shipper')) {
            $sales->where('shipments.user_id', '=', $search_shipper);
        }
        if ($search_shippers = $request->get('search_shippers')) {
            $sales->whereIn('shipments.user_id', $search_shippers);
        }
        if ($mode = $request->get('search_shipping_mode')) {
            $sales->where('sm.id', '=', $mode);
        }
        if ($origin = $request->get('search_origin')) {
            $sales->where('oc.id', '=', $origin);
        }
        if ($destination = $request->get('search_destination')) {
            $sales->where('dc.id', '=', $destination);
        }
        if ($hub = $request->get('search_hub')) {
            $sales->where('h.id', '=', $hub);
        }
        if ($status = $request->get('search_status')) {
            $sales->where('ss.id', '=', $status);
        }
        if ($search_business_category = $request->get('search_business_category')) {
            $sales->where('shipments.business_category_id', '=', $search_business_category);
        }

        $datatable = Datatables::of($sales)
            ->addColumn('attempts', function ($shipment) {
                $out_for_delivery = DB::connection('reports')->table('shipments_journey')->where('shipment_id', $shipment->shipment_id)->where('shipper_status_id', 5)->count();
                return $out_for_delivery;
            })
            ->editColumn('account_no', function ($shipments) {
                return str_pad($shipments->account_no, 6, '0', STR_PAD_LEFT);
            })
            ->editColumn('insurance_charges', function ($shipment) {
                return number_format($shipment->insurance_charges, 2);
            })
            ->editColumn('cash_handling_charges', function ($shipment) {
                if ($shipment->dr_status_id == 20) {
                    return "-";
                } else {
                    if ($shipment->cash_handling_charges != null) {
                        return number_format($shipment->cash_handling_charges, 2);
                    } else {
                        return "-";
                    }
                }
            })
            ->editColumn('packaging_material_charges', function ($shipment) {
                return number_format($shipment->packaging_material_charges, 2);
            })
            ->editColumn('packaging_charges', function ($shipment) {
                return number_format($shipment->packaging_charges, 2);
            })
            ->editColumn('tracking_number_link', function ($shipments) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->editColumn('shipper', function ($shipment) {
                if ($shipment->booking_type_id == 4) {
                    return $shipment->shipper . ' (' . $shipment->poc . ')';
                } else {
                    return $shipment->shipper;
                }
            })
            ->editColumn('p_gst', function ($sale) {
                $gst = '';
                if ($sale->account_type_id == 1) {
                    if ($sale->p_gst != null) {
                        $gst = $sale->p_gst;
                    } else if ($sale->d_gst != null) {
                        $gst = $sale->d_gst;
                    }
                } else {
                    if ($sale->pis_gst != null) {
                        $gst = $sale->pis_gst;
                    } else if ($sale->is_gst != null) {
                        $gst = $sale->is_gst;
                    }
                }
                return number_format((float)$gst, 2);
            })
            ->editColumn('p_total_charges', function ($sale) {
                $total = '';
                if ($sale->p_total_charges != null) {
                    $total = $sale->p_total_charges;
                } else if ($sale->d_total_charges != null) {
                    $total = $sale->d_total_charges;
                }
                return number_format((float)$total, 2);
            })
            ->editColumn('delivered_or_returned', function ($sale) {
                if (in_array($sale->shipment_status, [14, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 45, 46, 25])) {
                    return $sale->delivered_or_returned;
                } else {
                    return '-';
                }
            });

        return $datatable
        ->rawColumns(['tracking_number_link'])
        ->make(true);
    }

}
