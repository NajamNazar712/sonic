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
        $connection = 'reports';

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

        $sales = DB::connection($connection)->table('shipments')->join('users as u','u.id','=','shipments.user_id')
            ->join('shipment_status as ss','ss.id','=','shipments.shipper_status_id')
            ->leftJoin('booking_types as bt','bt.id','=','shipments.booking_type_id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->leftjoin('user_shipping_infos AS rsi', 'shipments.return_address_id', '=', 'rsi.id')
            ->leftjoin('cities AS rc', 'rsi.city_id', '=', 'rc.id')
            ->leftjoin('zones as z', 'z.id', '=', 'oc.zone_id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->join('cities as h' ,'dc.hub_id', '=' , 'h.id')
            ->leftJoin('shipping_modes as sm', 'sm.id', '=', 'shipments.shipping_mode_id')
            ->leftJoin('shipments_journey as sj', function ($join) use ($connection) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where('sj.id','=',
                        DB::connection($connection)->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)'));
            })
            ->leftJoin('pending_payment_shipments as pps', function ($join) use ($connection) {
                $join->on('pps.shipment_id', '=', 'shipments.id')
                    ->where('pps.id','=',
                        DB::connection($connection)->raw('(select max(id) from pending_payment_shipments where pending_payment_shipments.shipment_id = shipments.id and pending_payment_shipments.type != 2)'));
            })
            ->leftJoin('done_payment_shipments as dps', function ($join) use ($connection) {
                $join->on('dps.shipment_id', '=', 'shipments.id')
                    ->where('dps.id','=',
                        DB::connection($connection)->raw('(select max(id) from done_payment_shipments where done_payment_shipments.shipment_id = shipments.id and done_payment_shipments.type != 2)'));
            })
            ->leftJoin('shipments_journey as dr', function ($join) use ($connection) {
                $join->on('dr.shipment_id', '=', 'shipments.id')
                    ->where('dr.id','=',
                        DB::connection($connection)->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id In(14,25,30,36,37) and shipments_journey.verification = 1)'));
            })

            ->leftjoin('sale_person_tags as spt', function ($join) {
                $join->on('spt.user_id', '=', 'shipments.user_id')
                    ->leftjoin('admins as adsp','adsp.id','=','spt.admin_id')
                    ->where('spt.status','=',0);
            })
            ->leftJoin('pending_invoice_shipments as pis', function ($join) use ($connection) {
                $join->on('pis.shipment_id', '=', 'shipments.id')
                    ->where('pis.id','=',
                        DB::connection($connection)->raw('(select max(id) from pending_invoice_shipments where pending_invoice_shipments.shipment_id = shipments.id and pending_invoice_shipments.type != 2)'));
            })
            ->leftJoin('invoice_shipments as is', function ($join) use ($connection) {
                $join->on('is.shipment_id', '=', 'shipments.id')
                    ->where('is.id','=',
                        DB::connection($connection)->raw('(select max(id) from invoice_shipments where invoice_shipments.shipment_id = shipments.id and invoice_shipments.type != 2)'));
            })
            ->leftjoin('invoices' ,'is.invoice_id', '=' , 'invoices.id')
            ->leftjoin('international_shipments as ibs', 'ibs.shipment_id', '=', 'shipments.id')
            ->leftjoin('riders as r', 'r.id', '=', 'sj.rider_id')
            ->join('business_categories as bc', 'bc.id', '=', 'shipments.business_category_id')
            ->select('shipments.id as shipment_id','shipments.tracking_number','shipments.tracking_number as tracking_number_link','u.id as account_no','u.name as shipper','sj.created_at as arrival_date','shipments.actual_weight','shipments.weight_charges','shipments.cash_handling_charges','shipments.insurance_charges','shipments.fuel_surcharge','shipments.packaging_material_charges','pps.gst as p_gst','pps.charges as p_total_charges','dps.amount as d_collection_amount','dps.gst as d_gst','dps.charges as d_total_charges','dps.payable as d_net_payable','sm.id as shipping_mode_id','shipments.chargeable_weight','z.name as zone', 'oc.id as origin_city_id', 'oc.name as origin_city_name', 'dc.id as destination_city_id', 'dc.name as destination_city_name', 'dps.done_payment_id as payment_id', 'shipments.booking_type_id', 'usi.poc', 'adsp.name as sales_person', 'shipments.shipper_status_id as shipment_status', 'u.account_type_id as account_type_id', 'pis.gst as pis_gst', 'is.gst as is_gst','shipments.packaging_charges', 'dr.shipper_status_id as dr_status_id')
            ->whereNotIn('shipments.shipper_status_id',[1,17])
            ->whereNotIn('u.id', [8761, 9358])
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

        if (session('role_id') != 1 && (!in_array(session('id'), session('sale_users_bypass')))) {
            if (session('department_id') == 7) {
                $sales = $sales->whereIn('u.id', session('tagged_shippers'));
            } else {
                $sales = $sales->whereIn('dc.hub_id', session('hubs'));
            }
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
            });

        if ($tracking = $request->get('search_tracking')) {
            $datatable->where('shipments.tracking_number', '=', $tracking);
        }
        if ($sales_person = $request->get('search_sales_person')) {
            $datatable->where('adsp.id', '=', $sales_person);
        }
        if ($search_shipper = $request->get('search_shipper')) {
            $datatable->where('shipments.user_id', '=', $search_shipper);
        }
        if ($search_shippers = $request->get('search_shippers')) {
            $datatable->whereIn('shipments.user_id', $search_shippers);
        }
        if ($mode = $request->get('search_shipping_mode')) {
            $datatable->where('sm.id', '=', $mode);
        }
        if ($origin = $request->get('search_origin')) {
            $datatable->where('oc.id', '=', $origin);
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
        if ($search_business_category = $request->get('search_business_category')) {
            $datatable->where('shipments.business_category_id', '=', $search_business_category);
        }
        return $datatable->make(true);
    }

}
