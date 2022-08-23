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

    public function overall_sales_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 149);
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
        return view('admin.reports.overall_sales')->with(['shippers' => $shippers, 'cities' => $cities, 'hubs' => $hubs, 'statuses' => $statuses, 'sales_persons' => $sales_persons, 'business_categories' => $business_categories, 'shipping_modes' => $shipping_modes]);
    }
    public function overall_sales_list(Request $request)
    {
        $connection = 'reports';

        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 150);
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
            ->join('booking_types as bt','bt.id','=','shipments.booking_type_id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->leftjoin('user_shipping_infos AS rsi', 'shipments.return_address_id', '=', 'rsi.id')
            ->leftjoin('cities AS rc', 'rsi.city_id', '=', 'rc.id')
            ->leftjoin('zones as z', 'z.id', '=', 'oc.zone_id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->join('cities as h' ,'dc.hub_id', '=' , 'h.id')
            ->leftjoin('zone_class_cities as zcc', function($join) use ($connection) {
                $join->on('z.id', '=', 'zcc.zone_id')
                    ->on('dc.id', '=', 'zcc.city_id')
                    ->on('zone_classification_id', '=', DB::connection($connection)->raw('IF (shipments.shipping_mode_id IN (1, 4), 1, 2)'));
            })
            ->join('shipping_modes as sm', 'sm.id', '=', 'shipments.shipping_mode_id')
            ->leftjoin('shipment_payment_status as sps', 'shipments.payment_status_id', '=' , 'sps.id')
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
            ->leftJoin('shipment_status_reason as ssr', 'ssr.id', '=', 'sjr.status_reason_id')
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
            ->leftjoin('shipment_items as si', function ($join) use ($connection) {
                $join->on('si.shipment_id', '=', 'shipments.id')
                    ->where('si.id', '=',
                        DB::connection($connection)->raw('(select max(id) from shipment_items where shipment_items.shipment_id = shipments.id and shipment_items.type = 0)'));
            })
            ->leftjoin('sale_person_tags as spt', function ($join) {
                $join->on('spt.user_id', '=', 'shipments.user_id')
                    ->leftjoin('admins as adsp','adsp.id','=','spt.admin_id')
                    ->where('spt.status','=',0);
            })
            ->leftjoin('products as p','p.id','=','si.product_type_id')
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
            ->select('invoices.invoice_number','r.name as ridername','ssr.name as reason','sjr.remarks as remark','p.product_name as category','si.description as description','shipments.id as shipment_id','shipments.tracking_number','shipments.order_id as order_id','shipments.tracking_number as tracking_number_link','u.id as account_no','u.name as shipper','usi.pickup_address as shipper_address','ss.name as current_status','bt.booking_type as service_type','sj.created_at as arrival_date','oc.name as origin','dc.name as destination','h.name as hub','shipments.amount as s_collection_amount','sps.name as payment_status','pps.amount as p_collection_amount','shipments.actual_weight','shipments.weight_charges','shipments.cash_handling_charges','shipments.insurance_charges','shipments.return_charges','shipments.replacement_charges','shipments.fuel_surcharge','shipments.try_and_buy_charges','shipments.packaging_material_charges','pps.gst as p_gst','pps.charges as p_total_charges','pps.payable as p_net_payable','dps.amount as d_collection_amount','dps.gst as d_gst','dps.charges as d_total_charges','dps.payable as d_net_payable', 'sm.mode as shipping_mode','sm.id as shipping_mode_id','shipments.chargeable_weight','dr.created_at as delivered_or_returned','z.name as zone','zcc.class', 'oc.id as origin_city_id', 'dc.id as destination_city_id', 'dps.done_payment_id as payment_id', 'shipments.booking_type_id', 'usi.poc', 'adsp.name as sales_person', 'shipments.shipper_status_id as shipment_status', 'shipments.nsa_osa_charges', 'u.account_type_id as account_type_id', 'pis.gst as pis_gst', 'is.gst as is_gst','shipments.packaging_charges', 'dr.received_or_refused_by', 'shipments.special_instructions','shipments.intercept_charges','bc.name as business_shipment_type','ibs.international_tracking_number','usi.vendor', 'dr.shipper_status_id as dr_status_id', 'shipments.shipment_type','rc.name as return_city',DB::raw('(select count(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 5) as total_attempt'), 'dr.cnic as dr_cnic', 'dr.relation as dr_relation')
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
            ->editColumn('return_charges', function ($shipment) {
                if ($shipment->dr_status_id != 20) {
                    return "-";
                } else {
                    return number_format($shipment->return_charges, 2);
                }
            })
            ->editColumn('replacement_charges', function ($shipment) {
                if ($shipment->dr_status_id == 20) {
                    return "-";
                } else {
                    return number_format($shipment->replacement_charges, 2);
                }
            })
            ->editColumn('try_and_buy_charges', function ($shipment) {
                if ($shipment->dr_status_id == 20) {
                    return "-";
                } else {
                    return number_format($shipment->try_and_buy_charges, 2);
                }
            })
            ->editColumn('nsa_osa_charges', function ($shipment) {
                return number_format($shipment->nsa_osa_charges, 2);
            })
            ->editColumn('packaging_material_charges', function ($shipment) {
                return number_format($shipment->packaging_material_charges, 2);
            })
            ->editColumn('p_total_charges', function ($shipment) {
                return number_format($shipment->p_total_charges, 2);
            })
            ->editColumn('d_total_charges', function ($shipment) {
                return number_format($shipment->d_total_charges, 2);
            })
            ->editColumn('p_net_payable', function ($shipment) {
                return number_format($shipment->p_net_payable, 2);
            })
            ->editColumn('d_net_payable', function ($shipment) {
                return number_format($shipment->d_net_payable, 2);
            })
            ->editColumn('d_gst', function ($shipment) {
                return number_format($shipment->d_gst, 2);
            })
            ->editColumn('packaging_charges', function ($shipment) {
                return number_format($shipment->packaging_charges, 2);
            })
            ->editColumn('intercept_charges', function ($shipment) {
                return number_format($shipment->intercept_charges, 2);
            })
            ->editColumn('tracking_number_link', function ($shipments) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->editColumn('d_collection_amount', function ($shipment) {
                return number_format($shipment->d_collection_amount);
            })
            ->editColumn('shipper', function ($shipment) {
                if ($shipment->booking_type_id == 4) {
                    return $shipment->shipper . ' (' . $shipment->poc . ')';
                } else {
                    return $shipment->shipper;
                }
            })
            ->editColumn('p_collection_amount', function ($sale) {
                $amount = '';
                if ($sale->p_collection_amount != null) {
                    $amount = $sale->p_collection_amount;
                } else if ($sale->d_collection_amount != null) {
                    $amount = $sale->d_collection_amount;
                } else {
                    $amount = $sale->s_collection_amount;
                }
                return number_format($amount);
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
            ->addColumn('estimated_charges', function ($sale) {
                $estimated = '';
                $estimated = (($sale->weight_charges != null) ? $sale->weight_charges : 0) + (($sale->cash_handling_charges != null) ? $sale->cash_handling_charges : 0) + (($sale->insurance_charges != null) ? $sale->insurance_charges : 0) + (($sale->insurance_charges != null) ? $sale->insurance_charges : 0) + (($sale->return_charges != null) ? $sale->return_charges : 0) + (($sale->replacement_charges != null) ? $sale->replacement_charges : 0) + (($sale->fuel_surcharge != null) ? $sale->fuel_surcharge : 0) + (($sale->try_and_buy_charges != null) ? $sale->try_and_buy_charges : 0) + (($sale->packaging_material_charges != null) ? $sale->packaging_material_charges : 0) + (($sale->intercept_charges != null) ? $sale->intercept_charges : 0);
                return number_format((float)$estimated, 2);
            })
            ->editColumn('p_net_payable', function ($sale) {
                $payable = '';
                if ($sale->p_net_payable != null) {
                    $payable = $sale->p_net_payable;
                } else if ($sale->d_net_payable != null) {
                    $payable = $sale->d_net_payable;
                }
                return number_format((float)$payable, 2);
            })
            ->addColumn('class', function ($sale) {
                $class = '';

                if ($sale->origin_city_id != $sale->destination_city_id) {
                    switch ($sale->class) {
                        case 0:
                            $class = 'Class A';
                            break;
                        case 1:
                            $class = 'Class B';
                            break;
                        case 2:
                            $class = 'Class C';
                            break;
                        case 3:
                            $class = 'Class D';
                            break;
                    }
                } else {
                    $class = 'Local';
                }

                return $class;
            })
            ->editColumn('delivered_or_returned', function ($sale) {
                if (in_array($sale->shipment_status, [14, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 45, 46, 25])) {
                    return $sale->delivered_or_returned;
                } else {
                    return '';
                }
            })
            ->editColumn('received_or_refused_by', function ($sale) {
                if (in_array($sale->shipment_status, [14, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 45, 46, 25, 22, 23, 24, 44, 47, 48, 57, 60])) {
                    $received_or_refused_by = '';
                    if($sale->received_or_refused_by){
                        $received_or_refused_by = $sale->received_or_refused_by;
                    }
                    if($sale->dr_cnic){
                        $received_or_refused_by .= "|".$sale->dr_cnic;
                    }
                    if($sale->dr_relation){
                        $received_or_refused_by .= "|".$sale->dr_relation;
                    }
                    return $received_or_refused_by;
                } else {
                    return '';
                }
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
