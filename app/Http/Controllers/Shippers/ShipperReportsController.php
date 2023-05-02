<?php

namespace App\Http\Controllers\Shippers;

use App\Http\Controllers\Controller;
use App\Http\Models\Admin\AdminRole;
use App\Http\Models\BookingType;
use App\Http\Models\City;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentsJourney;
use App\Http\Models\ShipmentStatus;
use App\Http\Models\ShipmentStatusReason;
use App\Http\Models\Shipper\User;
use App\Http\Models\ShippingMode;
use App\Http\Models\Zone;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;

class ShipperReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:web,substitute_users');

        $this->middleware('Permission');
    }

    public function sales_index()
    {
        $service_types = BookingType::where('id', '!=', 4)->get();
        $shipping_modes = DB::connection('reports')->table('shipping_modes')->select('id', 'mode')->get();
        $cities = DB::connection('reports')->table('cities')->select('id', 'name')->get();
        $statuses = DB::connection('reports')->table('shipment_status')->whereNotIn('id', [1, 17])->get();
        return view('client.reports.sales_report')->with(['cities' => $cities, 'statuses' => $statuses, 'shipping_modes' => $shipping_modes, 'service_types' => $service_types]);
    }
    public function sales_list(Request $request)
    {
        if (!in_array(session('user_id'), [167, 1159, 2035, 3324, 4740, 4758, 5982, 10104, 14110, 7762])) {
            $connection = 'reports';
        } else {
            $connection = 'mysql';
        }

        if (empty($request->get('search_tracking')) && empty($request->get('search_date_from')) && empty($request->get('dr_search_date_from'))) {
            $sales = DB::connection($connection)->table('shipments')->whereRaw('FALSE');
            $datatable = Datatables::of($sales);
            return $datatable->make(true);
        }

        $sales = DB::connection($connection)->table('shipments')->join('users as u', 'u.id', '=', 'shipments.user_id')
            ->leftJoin('booking_types as bt', 'bt.id', '=', 'shipments.booking_type_id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->leftjoin('shipment_payment_status as sps', 'shipments.payment_status_id', '=', 'sps.id')
            ->leftJoin('shipping_modes as sm', 'sm.id', '=', 'shipments.shipping_mode_id')
            ->leftJoin('shipments_journey as sj', function ($join) use ($connection) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where('sj.shipper_status_id', 2)
                    ->where('sj.id', '=',
                        DB::connection($connection)->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)'));
            })
            ->leftJoin('shipments_journey as cj', function ($join) use ($connection) {
                $join->on('cj.shipment_id', '=', 'shipments.id')
                    ->where('cj.id', '=',
                        DB::connection($connection)->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.verification = 1)'));
            })

            ->leftJoin('shipments_journey as sjreason', function ($join) {
                $join->on('sjreason.shipment_id', '=', 'shipments.id')
                    ->whereIn('shipments.shipper_status_id', [20, 21, 22, 23, 24, 25, 44, 47, 48, 57, 60])
                    ->where('sjreason.id', '=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id IN (12, 20) and shipments_journey.verification = 1 and shipments_journey.status_reason_id is not null)'));
            })
            ->leftJoin('shipment_status_reason as ssreason', 'ssreason.id', '=', 'cj.status_reason_id')

            ->leftJoin('shipment_status as ss', 'ss.id', '=', 'cj.shipper_status_id')
            ->leftJoin('pending_payment_shipments as pps', function ($join) use ($connection) {
                $join->on('pps.shipment_id', '=', 'shipments.id')
                    ->where('pps.id', '=',
                        DB::connection($connection)->raw('(select max(id) from pending_payment_shipments where pending_payment_shipments.shipment_id = shipments.id and pending_payment_shipments.type != 2)'));
            })
            ->leftJoin('done_payment_shipments as dps', function ($join) use ($connection) {
                $join->on('dps.shipment_id', '=', 'shipments.id')
                    ->where('dps.id', '=',
                        DB::connection($connection)->raw('(select max(id) from done_payment_shipments where done_payment_shipments.shipment_id = shipments.id and done_payment_shipments.type != 2)'));
            })
            ->leftjoin('shipment_items as si', function ($join) use ($connection) {
                $join->on('si.shipment_id', '=', 'shipments.id')
                    ->where('si.id', '=',
                        DB::connection($connection)->raw('(select max(id) from shipment_items where shipment_items.shipment_id = shipments.id and shipment_items.type = 0)'));
            })
            ->leftjoin('products as p', 'p.id', '=', 'si.product_type_id')
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
            ->leftJoin('shipment_order_dates as sod', 'shipments.id', '=', 'sod.shipment_id')
            ->leftJoin('shipment_shipper_references as ssr', 'shipments.id', '=', 'ssr.shipment_id');

        if (!empty($request->get('dr_search_date_from')) && !empty($request->get('dr_search_date_to'))) {
            $from = $request->get('dr_search_date_from');
            $to = $request->get('dr_search_date_to');

            $ids = FALSE;

            $from_id = DB::connection($connection)->table('shipments_journey')->select('id')->where('created_at', '>=', $from);
            if ($from_id->exists()) {
                $from_id = $from_id->first()->id;

                $to_id = DB::connection($connection)->table('shipments_journey')->select(DB::raw('MAX(id) as id'))->where('created_at', '>=', $from)->where('created_at', '<=', $to);

                if ($to_id->exists()) {
                    $to_id = $to_id->first()->id;

                    $ids = TRUE;
                }
            }
            if ($ids) {
                $sales->leftJoin('shipments_journey as dr', function ($join) use ($from, $to, $from_id, $to_id) {
                    $join->on('dr.shipment_id', '=', 'shipments.id')
                        ->whereIn('dr.shipper_status_id', [14,25,30,36,37])
                        ->where('dr.id', '=',
                            DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id In(14,25,30,36,37) and shipments_journey.verification = 1 and shipments_journey.created_at between "' . $from . '" and "' . $to . '" and shipments_journey.id >= ' . $from_id . ' and shipments_journey.id <= ' . $to_id . ')'));
                });

                $sales->whereBetween('dr.created_at', [$from, $to]);

                $sales->where('dr.id', '>=', $from_id)
                    ->where('dr.id', '<=', $to_id);
            }
            else {
                $sales->leftJoin('shipments_journey as dr', function ($join) use ($from, $to) {
                    $join->on('dr.shipment_id', '=', 'shipments.id')
                        ->whereIn('dr.shipper_status_id', [14,25,30,36,37])
                        ->where('dr.id', '=',
                            DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id In(14,25,30,36,37) and shipments_journey.verification = 1 and shipments_journey.created_at between "' . $from . '" and "' . $to . '")'));
                });

                $sales->whereBetween('dr.created_at', [$from, $to]);
            }
        } else {
            $sales->leftJoin('shipments_journey as dr', function ($join) use ($connection) {
                $join->on('dr.shipment_id', '=', 'shipments.id')
                    ->whereIn('dr.shipper_status_id', [14,25,30,36,37])
                    ->where('dr.id', '=',
                        DB::connection($connection)->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id In(14, 25, 30, 36, 37) and shipments_journey.verification = 1)'));
            });
        }

        $sales->select('p.product_name as product_name', 'ssreason.name as reason_name', 'si.description as description', 'shipments.tracking_number', 'shipments.order_id as order_id', 'u.id as account_no', 'u.name as shipper', 'ss.name as current_status', 'bt.booking_type as service_type', 'sj.created_at as arrival_date', 'oc.name as origin', 'dc.name as destination', 'shipments.amount as s_collection_amount', 'sps.name as payment_status', 'pps.charges as p_total_charges','pps.amount as p_total_amount', 'shipments.actual_weight', 'shipments.weight_charges', 'shipments.cash_handling_charges', 'dps.amount as d_collection_amount', 'sm.mode as shipping_mode', 'dr.created_at as delivered_or_returned', 'dr.received_or_refused_by', 'shipments.consignee_name', 'shipments.consignee_phone_number_1', 'shipments.consignee_phone_number_2', 'sod.order_date as order_date', 'shipments.estimated_weight', 'ssr.reference_1 as reference_1', 'ssr.reference_2 as reference_2', 'ssr.reference_3 as reference_3', 'ssr.reference_4 as reference_4', 'ssr.reference_5 as reference_5', 'dr.shipper_status_id as dr_status_id', 'usi.vendor', 'dps.done_payment_id as payment_id', 'shipments.shipper_status_id as shipment_status', 'shipments.chargeable_weight', 'shipments.insurance_charges', 'shipments.packaging_material_charges', 'shipments.fuel_surcharge', 'shipments.return_charges', 'shipments.replacement_charges', 'shipments.try_and_buy_charges', 'shipments.nsa_osa_charges', 'shipments.gst', 'shipments.intercept_charges', 'shipments.packaging_charges', 'pps.payable as p_net_payable', 'dps.charges as d_total_charges','dps.payable as d_net_payable', 'pps.gst as p_gst', 'dps.gst as d_gst','u.account_type_id as account_type_id', 'pis.gst as pis_gst', 'is.gst as is_gst','usi.pickup_address as pickup_address','dr.cnic as dr_cnic', 'dr.relation as dr_relation', 'shipments.consignee_address as consignee_address')
            ->whereNotIn('shipments.shipper_status_id', [1, 17]);

        if (session('user_type') == 2) {
            if (session('restriction') == 1) {
                $sales = $sales->join('substitute_user_shipments as sus', function ($join) {
                    $join->on('sus.shipment_id', '=', 'shipments.id')
                        ->where('sus.substitute_user_id', '=', Auth::id());
                });
            }
        }
//                ->where('u.id', session('user_id'))
        //                ->orwhereIn('shipments.user_id', session('sister_users'));
        $sales = $sales->where(function ($query) {
            $query->where('shipments.user_id', session('user_id'))
                ->orwhereIn('shipments.user_id', session('sister_users'));
        });

        $datatable = Datatables::of($sales)
            ->editColumn('s_collection_amount', function ($shipment) {
                return number_format($shipment->s_collection_amount);
            })
            ->editColumn('weight_charges', function ($shipment) {
                return number_format($shipment->weight_charges, 2);
            })
            ->editColumn('insurance_charges', function($shipment){
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
            ->editColumn('return_charges', function($shipment){
                if ($shipment->dr_status_id != 20) {
                    return "-";
                }
                else {
                    return number_format($shipment->return_charges, 2);
                }
            })
            ->editColumn('replacement_charges', function($shipment){
                if ($shipment->dr_status_id == 20) {
                    return "-";
                }
                else {
                    return number_format($shipment->replacement_charges, 2);
                }
            })
            ->editColumn('try_and_buy_charges', function($shipment){
                if ($shipment->dr_status_id == 20) {
                    return "-";
                }
                else {
                    return number_format($shipment->try_and_buy_charges, 2);
                }
            })
            ->editColumn('nsa_osa_charges', function($shipment){
                return number_format($shipment->nsa_osa_charges, 2);
            })
            ->editColumn('packaging_material_charges', function($shipment){
                return number_format($shipment->packaging_material_charges, 2);
            })
            ->editColumn('packaging_charges', function($shipment){
                return number_format($shipment->packaging_charges, 2);
            })
            ->editColumn('intercept_charges', function($shipment){
                return number_format($shipment->intercept_charges, 2);
            })
            ->editColumn('p_total_charges',function($sale){
                $total = '';
                if($sale->p_total_charges != null){
                    $total = $sale->p_total_charges;
                }else if($sale->d_total_charges != null){
                    $total = $sale->d_total_charges;
                }
                return number_format((float)$total, 2);
            })
            ->addColumn('estimated_charges',function($sale){
                $estimated = '';
                $estimated = (($sale->weight_charges != null)? $sale->weight_charges:0) + (($sale->cash_handling_charges != null)? $sale->cash_handling_charges:0) + (($sale->insurance_charges != null)? $sale->insurance_charges:0) + (($sale->insurance_charges != null)? $sale->insurance_charges:0) + (($sale->return_charges != null)? $sale->return_charges:0) + (($sale->replacement_charges != null)? $sale->replacement_charges:0) + (($sale->fuel_surcharge != null)? $sale->fuel_surcharge:0) + (($sale->try_and_buy_charges != null)? $sale->try_and_buy_charges:0) + (($sale->packaging_material_charges != null)? $sale->packaging_material_charges:0) + (($sale->intercept_charges != null)? $sale->intercept_charges:0);
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
            })
            ->editColumn('p_gst',function($sale){
                $gst = '';
                if($sale->account_type_id == 1){
                    if($sale->p_gst != null){
                        $gst = $sale->p_gst;
                    }else if($sale->d_gst != null){
                        $gst = $sale->d_gst;
                    }
                }
                else{
                    if($sale->pis_gst != null){
                        $gst = $sale->pis_gst;
                    }else if($sale->is_gst != null){
                        $gst = $sale->is_gst;
                    }
                }
                return number_format((float)$gst, 2);
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
        if ($origin = $request->get('search_origin')) {
            $datatable->where('oc.id', '=', $origin);
        }
        if ($destination = $request->get('search_destination')) {
            $datatable->where('dc.id', '=', $destination);
        }
        if ($status = $request->get('search_status')) {
            $datatable->where('ss.id', '=', $status);
        }
        if ($mode = $request->get('search_shipping_mode')) {
            $datatable->where('sm.id', '=', $mode);
        }
        if ($service_type = $request->get('search_service_type')) {
            $datatable->where('shipments.booking_type_id', '=', $service_type);
        }
        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $datatable->whereBetween('sj.created_at', [$from, $to]);

            $from_id = DB::connection($connection)->table('shipments_journey')->select('id')->where('created_at', '>=', $from);
            if ($from_id->exists()) {
                $from_id = $from_id->first()->id;

                $to_id = DB::connection($connection)->table('shipments_journey')->select(DB::raw('MAX(id) as id'))->where('created_at', '>=', $from)->where('created_at', '<=', $to);

                if ($to_id->exists()) {
                    $to_id = $to_id->first()->id;

                    $datatable->where('sj.id', '>=', $from_id)
                        ->where('sj.id', '<=', $to_id);
                }
            }
        }

        return $datatable->make(true);
    }
    public function summary_data(Request $request)
    {
        $stats = array();
        $from = $request->from_date;
        $to = $request->to_date;
        $origin = $request->origin;
        $user = $request->user;
        $destination = $request->destination;
        $today = Carbon::now()->endOfDay();
        $thirtyDays = Carbon::now()->subDays(29)->startOfDay();
        if ($from == null || $to == null) {
            $today = Carbon::now()->endOfDay();
            $thirtyDays = Carbon::now()->subDays(29)->startOfDay();
        } else {
            $thirtyDays = $from;
            $today = $to;
        }
        $restriction = false;
        if (session('user_type') == 2) {
            if (session('restriction') == 1) {
                $restriction = true;
            }
        }
        $stats['total'] = DB::connection('reports')->table('shipments')->whereBetween('shipments.created_at', [$thirtyDays, $today])->where('user_id', $user);

        $stats['booked'] = DB::connection('reports')->table('shipments')->where('shipper_status_id', 1)->whereBetween('shipments.created_at', [$thirtyDays, $today])->where('user_id', $user);
        $stats['canceled'] = DB::connection('reports')->table('shipments')->where('shipper_status_id', 17)->whereBetween('shipments.created_at', [$thirtyDays, $today])->where('user_id', $user);
        $stats['received'] = DB::connection('reports')->table('shipments')->whereIn('shipper_status_id', [2, 3, 4])->whereBetween('shipments.created_at', [$thirtyDays, $today])->where('user_id', $user);
        $stats['delivered'] = DB::connection('reports')->table('shipments')->whereIn('shipper_status_id', [14, 16, 30, 36, 37, 39, 40, 41, 47])->whereBetween('shipments.created_at', [$thirtyDays, $today])->where('user_id', $user);
        $stats['return'] = DB::connection('reports')->table('shipments')->whereIn('shipper_status_id', [20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 31, 32, 33, 34, 35, 38, 42, 43, 44, 45, 46, 50])->whereBetween('shipments.created_at', [$thirtyDays, $today])->where('user_id', $user);
        $stats['in_process'] = DB::connection('reports')->table('shipments')->whereIn('shipper_status_id', [5, 6, 7, 8, 9, 10, 11, 12, 13, 15, 18, 19, 49, 52])->whereBetween('shipments.created_at', [$thirtyDays, $today])->where('user_id', $user);
        if ($restriction == true) {
            $stats['total'] = $stats['total']->join('substitute_user_shipments as sus', function ($join) {
                $join->on('sus.shipment_id', '=', 'shipments.id')
                    ->where('sus.substitute_user_id', '=', Auth::id());
            });
            $stats['booked'] = $stats['booked']->join('substitute_user_shipments as sus', function ($join) {
                $join->on('sus.shipment_id', '=', 'shipments.id')
                    ->where('sus.substitute_user_id', '=', Auth::id());
            });
            $stats['canceled'] = $stats['canceled']->join('substitute_user_shipments as sus', function ($join) {
                $join->on('sus.shipment_id', '=', 'shipments.id')
                    ->where('sus.substitute_user_id', '=', Auth::id());
            });
            $stats['received'] = $stats['received']->join('substitute_user_shipments as sus', function ($join) {
                $join->on('sus.shipment_id', '=', 'shipments.id')
                    ->where('sus.substitute_user_id', '=', Auth::id());
            });
            $stats['delivered'] = $stats['delivered']->join('substitute_user_shipments as sus', function ($join) {
                $join->on('sus.shipment_id', '=', 'shipments.id')
                    ->where('sus.substitute_user_id', '=', Auth::id());
            });
            $stats['return'] = $stats['return']->join('substitute_user_shipments as sus', function ($join) {
                $join->on('sus.shipment_id', '=', 'shipments.id')
                    ->where('sus.substitute_user_id', '=', Auth::id());
            });
            $stats['in_process'] = $stats['in_process']->join('substitute_user_shipments as sus', function ($join) {
                $join->on('sus.shipment_id', '=', 'shipments.id')
                    ->where('sus.substitute_user_id', '=', Auth::id());
            });
        }
        if ($origin) {
            $stats['total'] = $stats['total']->whereExists(function ($query) use ($origin) {
                $query->from('user_shipping_infos')
                    ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                    ->whereExists(function ($sub_query) use ($origin) {
                        $sub_query->from('cities')
                            ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                            ->where('cities.id', $origin);
                    });
            });
            $stats['booked'] = $stats['booked']->whereExists(function ($query) use ($origin) {
                $query->from('user_shipping_infos')
                    ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                    ->whereExists(function ($sub_query) use ($origin) {
                        $sub_query->from('cities')
                            ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                            ->where('cities.id', $origin);
                    });
            });
            $stats['canceled'] = $stats['canceled']->whereExists(function ($query) use ($origin) {
                $query->from('user_shipping_infos')
                    ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                    ->whereExists(function ($sub_query) use ($origin) {
                        $sub_query->from('cities')
                            ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                            ->where('cities.id', $origin);
                    });
            });
            $stats['received'] = $stats['received']->whereExists(function ($query) use ($origin) {
                $query->from('user_shipping_infos')
                    ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                    ->whereExists(function ($sub_query) use ($origin) {
                        $sub_query->from('cities')
                            ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                            ->where('cities.id', $origin);
                    });
            });
            $stats['delivered'] = $stats['delivered']->whereExists(function ($query) use ($origin) {
                $query->from('user_shipping_infos')
                    ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                    ->whereExists(function ($sub_query) use ($origin) {
                        $sub_query->from('cities')
                            ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                            ->where('cities.id', $origin);
                    });
            });
            $stats['return'] = $stats['return']->whereExists(function ($query) use ($origin) {
                $query->from('user_shipping_infos')
                    ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                    ->whereExists(function ($sub_query) use ($origin) {
                        $sub_query->from('cities')
                            ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                            ->where('cities.id', $origin);
                    });
            });
            $stats['in_process'] = $stats['in_process']->whereExists(function ($query) use ($origin) {
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
    public function summary_index()
    {
        $stats = array();
        $today = Carbon::now()->endOfDay();
        $thirtyDays = Carbon::now()->subDays(29)->startOfDay();
        $restriction = false;
        if (session('user_type') == 2) {
            if (session('restriction') == 1) {
                $restriction = true;
            }
        }
        $stats['total'] = DB::connection('reports')->table('shipments')->whereBetween('shipments.created_at', [$thirtyDays, $today])->where('user_id', session('user_id'));
        $stats['booked'] = DB::connection('reports')->table('shipments')->where('shipper_status_id', 1)->whereBetween('shipments.created_at', [$thirtyDays, $today])->where('user_id', session('user_id'));
        $stats['canceled'] = DB::connection('reports')->table('shipments')->where('shipper_status_id', 17)->whereBetween('shipments.created_at', [$thirtyDays, $today])->where('user_id', session('user_id'));
        $stats['received'] = DB::connection('reports')->table('shipments')->whereIn('shipper_status_id', [2, 3, 4])->whereBetween('shipments.created_at', [$thirtyDays, $today])->where('user_id', session('user_id'));
        $stats['delivered'] = DB::connection('reports')->table('shipments')->whereIn('shipper_status_id', [14, 16, 30, 36, 37, 39, 40, 41, 47])->whereBetween('shipments.created_at', [$thirtyDays, $today])->where('user_id', session('user_id'));
        $stats['return'] = DB::connection('reports')->table('shipments')->whereIn('shipper_status_id', [20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 31, 32, 33, 34, 35, 38, 42, 43, 44, 45, 46, 50])->whereBetween('shipments.created_at', [$thirtyDays, $today])->where('user_id', session('user_id'));
        $stats['in_process'] = DB::connection('reports')->table('shipments')->whereIn('shipper_status_id', [5, 6, 7, 8, 9, 10, 11, 12, 13, 15, 18, 19, 49, 52])->whereBetween('shipments.created_at', [$thirtyDays, $today])->where('user_id', session('user_id'));
        if ($restriction == true) {
            $stats['total'] = $stats['total']->join('substitute_user_shipments as sus', function ($join) {
                $join->on('sus.shipment_id', '=', 'shipments.id')
                    ->where('sus.substitute_user_id', '=', Auth::id());
            });
            $stats['booked'] = $stats['booked']->join('substitute_user_shipments as sus', function ($join) {
                $join->on('sus.shipment_id', '=', 'shipments.id')
                    ->where('sus.substitute_user_id', '=', Auth::id());
            });
            $stats['canceled'] = $stats['canceled']->join('substitute_user_shipments as sus', function ($join) {
                $join->on('sus.shipment_id', '=', 'shipments.id')
                    ->where('sus.substitute_user_id', '=', Auth::id());
            });
            $stats['received'] = $stats['received']->join('substitute_user_shipments as sus', function ($join) {
                $join->on('sus.shipment_id', '=', 'shipments.id')
                    ->where('sus.substitute_user_id', '=', Auth::id());
            });
            $stats['delivered'] = $stats['delivered']->join('substitute_user_shipments as sus', function ($join) {
                $join->on('sus.shipment_id', '=', 'shipments.id')
                    ->where('sus.substitute_user_id', '=', Auth::id());
            });
            $stats['return'] = $stats['return']->join('substitute_user_shipments as sus', function ($join) {
                $join->on('sus.shipment_id', '=', 'shipments.id')
                    ->where('sus.substitute_user_id', '=', Auth::id());
            });
            $stats['in_process'] = $stats['in_process']->join('substitute_user_shipments as sus', function ($join) {
                $join->on('sus.shipment_id', '=', 'shipments.id')
                    ->where('sus.substitute_user_id', '=', Auth::id());
            });
        }
        $stats['total'] = number_format($stats['total']->count());
        $stats['booked'] = number_format($stats['booked']->count());
        $stats['canceled'] = number_format($stats['canceled']->count());
        $stats['received'] = number_format($stats['received']->count());
        $stats['delivered'] = number_format($stats['delivered']->count());
        $stats['return'] = number_format($stats['return']->count());
        $stats['in_process'] = number_format($stats['in_process']->count());
        $cities = DB::connection('reports')->table('cities')->select(['id', 'name'])->get();
        $sister_users = DB::connection('reports')->table('merged_sister_account_mappings')->leftjoin('users as u', 'u.id', '=', 'merged_sister_account_mappings.sister_user_id')->where('head_user_id', session('user_id'))->select('u.id', 'u.name')->get();
        $user = DB::connection('reports')->table('users')->select('id', 'name')->where('id', session('user_id'))->first();
        return view('client.reports.summary')->with(['stats' => $stats, 'cities' => $cities, 'today' => $today, 'thirtyday' => $thirtyDays, 'user' => $user, 'sister_users' => $sister_users]);
    }

    public function summary_list(Request $request)
    {
        $shipments = DB::connection('reports')->table('shipments')->join('users as u', 'u.id', '=', 'shipments.user_id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->leftJoin('booking_types as bt', 'bt.id', '=', 'shipments.booking_type_id')
            ->leftjoin('shipment_payment_status as sps', 'shipments.payment_status_id', '=', 'sps.id')
            ->leftJoin('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where('sj.id', '=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2 and shipments_journey.verification = 1)'));
            })
            ->leftJoin('shipments_journey as cj', function ($join) {
                $join->on('cj.shipment_id', '=', 'shipments.id')
                    ->where('cj.id', '=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.verification = 1)'));
            })
            ->join('shipment_status as ss', 'ss.id', '=', 'cj.shipper_status_id')
            ->leftjoin('shipment_items as si', function ($join) {
                $join->on('si.shipment_id', '=', 'shipments.id')
                    ->where('si.type', '=', 0);
            })
            ->leftjoin('products as p', 'p.id', '=', 'si.product_type_id')
            ->leftJoin('shipments_journey as sjrr', function ($join) {
                $join->on('sjrr.shipment_id', '=', 'shipments.id')
                    ->whereIn('shipments.shipper_status_id', [20, 21, 22, 23, 24, 25, 44, 47, 48, 57, 60])
                    ->where('sjrr.id', '=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id IN (12, 20) and shipments_journey.verification = 1 and shipments_journey.status_reason_id is not null)'));
            })
            ->leftJoin('shipment_status_reason as ssr', 'ssr.id', '=', 'sjrr.status_reason_id')
            ->select(['u.name as user_name', 'shipments.id as shipment_id', 'shipments.order_id', 'shipments.tracking_number', 'shipments.amount as collection_amount', 'shipments.actual_weight', 'shipments.weight_charges', 'shipments.cash_handling_charges', 'ss.name as current_status', 'sps.name as payment_status', 'bt.booking_type as service_type', 'p.product_name', 'si.description', 'sj.created_at as arrival_date', 'oc.name as origin', 'dc.name as destination', 'shipments.consignee_name as consignee_name', 'shipments.consignee_phone_number_1 as consignee_phone', 'ssr.name as return_reason', 'shipments.created_at as booking_date']);

        if (session('user_type') == 2) {
            if (session('restriction') == 1) {
                $shipments = $shipments->join('substitute_user_shipments as sus', function ($join) {
                    $join->on('sus.shipment_id', '=', 'shipments.id')
                        ->where('sus.substitute_user_id', '=', Auth::id());
                });
            }
        }
        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $shipments = $shipments->whereBetween('shipments.created_at', [$from, $to]);

            $from_id = DB::connection('reports')->table('shipments')->select('id')->where('created_at', '>=', $from);
            if ($from_id->exists()) {
                $from_id = $from_id->first()->id;

                $to_id = DB::connection('reports')->table('shipments')->select(DB::raw('MAX(id) as id'))->where('created_at', '>=', $from)->where('created_at', '<=', $to);

                if ($to_id->exists()) {
                    $to_id = $to_id->first()->id;

                    $shipments->where('shipments.id', '>=', $from_id)
                        ->where('shipments.id', '<=', $to_id);
                }
            }
        }

        $datatable = Datatables::of($shipments)
            ->addColumn('tracking_number_link', function ($shipments) {
                $route = route('cod.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->editColumn('collection_amount', function ($shipments) {
                return number_format($shipments->collection_amount);
            });
        if ($user = $request->get('search_user')) {
            $datatable->where('shipments.user_id', $user);
        } else {
            $datatable->where('shipments.user_id', session('user_id'));
        }
        if ($origin = $request->get('search_origin')) {
            $datatable->where('oc.id', '=', $origin);
        }
        if ($destination = $request->get('search_destination')) {
            $datatable->where('dc.id', '=', $destination);
        }
        if ($card = $request->get('cards_filter')) {
            switch ($card) {
                case 'total':
                    $datatable->whereBetween('shipments.shipper_status_id', [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 15, 18, 19, 49, 52, 14, 16, 30, 36, 37, 39, 40, 41, 47, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 31, 32, 33, 34, 35, 38, 42, 43, 44, 45, 46, 50, 17]);
                    break;
                case 'booked':
                    $datatable->where('shipments.shipper_status_id', 1);
                    break;
                case 'received':
                    $datatable->whereIn('shipments.shipper_status_id', [2, 3, 4]);
                    break;
                case 'delivered':
                    $datatable->whereIn('shipments.shipper_status_id', [14, 16, 30, 36, 37, 39, 40, 41, 47]);
                    break;
                case 'returned':
                    $datatable->whereIn('shipments.shipper_status_id', [20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 31, 32, 33, 34, 35, 38, 42, 43, 44, 45, 46, 50]);
                    break;
                case 'in_process':
                    $datatable->whereIn('shipments.shipper_status_id', [5, 6, 7, 8, 9, 10, 11, 12, 13, 15, 18, 19, 49, 52]);
                    break;
                case 'cancelled':
                    $datatable->where('shipments.shipper_status_id', 17);
                    break;
            }

        }
        return $datatable->make(true);

    }

    public function adjustments_index()
    {
        return view('client.reports.adjustments');
    }

    public function adjustments_list(Request $request)
    {
        $adjustments = DB::connection('reports')->table('adjustment_logs')
            ->leftjoin('done_payment_shipments as dps', 'dps.id', '=', 'adjustment_logs.done_id')
            ->leftjoin('shipments as s', 's.id', '=', 'adjustment_logs.shipment_id')
            ->leftjoin('users as u', 'u.id', '=', 's.user_id')
            ->leftjoin('adjustment_types as at', 'at.id', '=', 'adjustment_logs.adjustment_type_id')
            ->select('adjustment_logs.id as adjustment_id', 'adjustment_logs.adjustment_amount as adjustment_amount', 'adjustment_logs.remarks as remarks', 's.tracking_number as tracking_number', 'at.name as adjustment_type', 'adjustment_logs.created_at as created_at', 'u.name as shipper_name', 'dps.done_payment_id as done_payment_id')
            ->whereIn('adjustment_logs.type', [1, 2])
            ->where('s.user_id', session('user_id'));

        if (session('user_type') == 2) {
            if (session('restriction') == 1) {
                $adjustments = $adjustments->join('substitute_user_shipments as sus', function ($join) {
                    $join->on('sus.shipment_id', '=', 's.id')
                        ->where('sus.substitute_user_id', '=', Auth::id());
                });
            }
        }
        $datatable = Datatables::of($adjustments)
            ->addColumn('adjustment_id_padded', function ($adjustment) {
                $padded_id = str_pad($adjustment->adjustment_id, 6, '0', STR_PAD_LEFT);
                return $padded_id;
            })
            ->addColumn('tracking_number_link', function ($shipments) {
                $route = route('cod.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->addColumn('done_payment_link', function ($shipments) {
                if ($shipments->done_payment_id != null) {
                    return '<button class="btn btn-sm btn-outline-info align-middle"><i class="la la-lg la-print align-middle"></i> <span class="align-middle">' . str_pad($shipments->done_payment_id, 6, '0', STR_PAD_LEFT) . '</span></button>';
                } else {
                    return "-";
                }
            });

        if ($tracking_number = $request->get('search_tracking')) {
            $datatable->where('s.tracking_number', '=', $tracking_number);
        }
        if ($type = $request->get('search_type')) {
            $datatable->where('adjustment_logs.type', '=', $type);
        }
        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $datatable = $datatable->whereBetween('adjustment_logs.created_at', [$from, $to]);
        }
        return $datatable->make(true);
    }

    public function delivery_and_return_index()
    {
        $link = '';

        if (session('user_id') == 7762) {
            $link = 'https://app.powerbi.com/view?r=eyJrIjoiY2U4ZmNhNzUtYjlhNy00NDQ0LWE0OGUtYzJhYTBhNjEzZjNjIiwidCI6IjkwYzY4NjAzLTEzNTgtNGViYi04OWEwLTRmMmFlMzlmMzJjMiIsImMiOjl9';
        } else if (in_array(session('user_id'), [167, 1159, 2035])) {
            $link = 'https://app.powerbi.com/view?r=eyJrIjoiMDZhOTcxMDctMzhiOS00NzcxLWE1M2EtYzQwOTFlMGI2MDJjIiwidCI6IjkwYzY4NjAzLTEzNTgtNGViYi04OWEwLTRmMmFlMzlmMzJjMiIsImMiOjl9';
        }

        return view('client.reports.delivery_and_return_report')->with(['link' => $link]);
    }

    public function sales_telenor_index()
    {
        $shipping_modes = DB::connection('mysql')->table('shipping_modes')->select('id', 'mode')->get();
        $cities = DB::connection('mysql')->table('cities')->select('id', 'name')->get();
        $statuses = DB::connection('mysql')->table('shipment_status')->whereNotIn('id', [17])->get();
        return view('client.reports.sales_report_telenor')->with(['cities' => $cities, 'statuses' => $statuses, 'shipping_modes' => $shipping_modes]);
    }
    public function sales_telenor_list(Request $request)
    {
        $connection = 'mysql';

        $sales = DB::connection($connection)->table('shipments')->join('users as u', 'u.id', '=', 'shipments.user_id')
            ->join('shipment_status as ss', 'ss.id', '=', 'shipments.shipper_status_id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->leftJoin('shipments_journey as sj', function ($join) use ($connection) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where('sj.id', '=',
                        DB::connection($connection)->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id IN (1,2) and shipments_journey.verification = 1)'));
            })
            ->leftJoin('shipments_journey as sjb', function ($join) use ($connection) {
                $join->on('sjb.shipment_id', '=', 'shipments.id')
                    ->where('sjb.id', '=',
                        DB::connection($connection)->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 1 and shipments_journey.verification = 1)'));
            })
            ->leftJoin('shipments_journey as dr', function ($join) use ($connection) {
                $join->on('dr.shipment_id', '=', 'shipments.id')
                    ->where('dr.id', '=',
                        DB::connection($connection)->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id In (14, 25, 30, 36, 37) AND shipments_journey.verification = 1)'));
            })
            ->leftJoin('shipments_journey as sjrr', function ($join) use ($connection) {
                $join->on('sjrr.shipment_id', '=', 'shipments.id')
                    ->whereIn('shipments.shipper_status_id', [20, 21, 22, 23, 24, 25, 44, 47, 48, 57, 60])
                    ->where('sjrr.id', '=',
                        DB::connection($connection)->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id IN (12, 20) and shipments_journey.verification = 1 and shipments_journey.status_reason_id is not null)'));
            })
            ->leftJoin('shipment_status_reason as ssr', 'ssr.id', '=', 'sjrr.status_reason_id')
            ->select('shipments.tracking_number', 'shipments.order_id as order_id', 'ss.name as current_status', 'sj.created_at as arrival_date', 'oc.name as origin', 'dc.name as destination', 'shipments.actual_weight', 'dr.created_at as delivered_or_returned', 'shipments.consignee_name', 'shipments.consignee_phone_number_1', 'ssr.name as return_reason', 'dr.shipper_status_id', 'sjb.created_at as booking_date')
            ->whereNotIn('shipments.shipper_status_id', [17]);

        if (session('user_type') == 2) {
            if (session('restriction') == 1) {
                $sales = $sales->join('substitute_user_shipments as sus', function ($join) {
                    $join->on('sus.shipment_id', '=', 'shipments.id')
                        ->where('sus.substitute_user_id', '=', Auth::id());
                });
            }
        }

        $sales = $sales->where(function ($query) {
            $query->where('shipments.user_id', session('user_id'))
                ->orWhereIn('shipments.user_id', session('sister_users'));
        });

        $datatable = Datatables::of($sales)
            ->editColumn('arrival_date', function ($sales) {
                if($sales->arrival_date == $sales->booking_date){
                    return '-';
                }else{
                    return $sales->arrival_date;
                }
            })
            ->addColumn('delivery_within_15_days', function ($sales) {
                if ($sales->shipper_status_id != 25) {
                    $delivered_date = Carbon::parse($sales->delivered_or_returned)->startOfDay();

                    $arrival_date = Carbon::parse($sales->arrival_date)->startOfDay();

                    $days = $delivered_date->diffInDays($arrival_date);

                    if ($days <= 15) {
                        return 'YES';
                    } else {
                        return 'NO';
                    }
                } else {
                    return '';
                }
            });

        if ($tracking = $request->get('search_tracking')) {
            $datatable->where('shipments.tracking_number', '=', $tracking);
        }

        if ($origin = $request->get('search_origin')) {
            $datatable->where('oc.id', '=', $origin);
        }

        if ($destination = $request->get('search_destination')) {
            $datatable->where('dc.id', '=', $destination);
        }

        if ($status = $request->get('search_status')) {
            $datatable->where('ss.id', '=', $status);
        }

        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $datatable->whereBetween('sj.created_at', [$from, $to]);
        }

        if ($request->get('dr_search_date_from') && $request->get('dr_search_date_to')) {
            $from = $request->get('dr_search_date_from');
            $to = $request->get('dr_search_date_to');
            $datatable->whereBetween('dr.created_at', [$from, $to]);
        }

        return $datatable->make(true);
    }

    public function confirmation_shipments_index()
    {
        $shipment_status = ShipmentStatus::select('id', 'name')->get();
        $return_confirm_reason_ids = DB::table('shipment_status_shipment_status_reason')->where('shipment_status_id', 20)->where('shipment_status_reason_id', '<>', 2)->pluck('shipment_status_reason_id')->toArray();
        $return_confirm_reasons = ShipmentStatusReason::whereIn('id', $return_confirm_reason_ids)->select('id', 'name')->get();
        $agents = AdminRole::leftjoin('admins as a', 'a.role_id', '=', 'admin_roles.id')->where('admin_roles.department_id', 3)->get();
        return view('client.reports.confirmation_pending_report')->with(['shipment_status' => $shipment_status, 'return_confirm_reasons' => $return_confirm_reasons, 'agents' => $agents]);
    }
    public function confirmation_shipments_list(Request $request)
    {
        $shipments = Shipment::join('users as u', 'shipments.user_id', '=', 'u.id')
            ->join('shipment_status as ss', 'ss.id', '=', 'shipments.shipper_status_id')
            ->leftJoin('shipments_journey', function ($join) {
                $join->on('shipments_journey.shipment_id', '=', 'shipments.id')
                    ->where('shipments_journey.id', '=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id)'));
            })
            ->leftJoin('shipments_journey as sret', function ($join) {
                $join->on('sret.shipment_id', '=', 'shipments.id')
                    ->where('sret.id', '=',
                        DB::raw('(select min(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 12 and shipments_journey.verification = 1)'));
            })
            ->leftJoin('shipment_status_reason as ssr', 'ssr.id', '=', 'sret.status_reason_id')
            ->select('shipments.id as shipment_id', 'shipments.id as shId', 'shipments.shipper_status_id', 'shipments.tracking_number', 'shipments.tracking_number as tracking', 'ss.name as status', 'ssr.id as reason_id', 'ssr.name as reason', 'sret.remarks as remarks', 'sret.created_at as status_date')
//            ->whereIn('shipments.shipper_status_id', [12, 20, 13, 54, 55, 5, 23])
            ->where('sret.verification', 1)
            ->where('u.id', session('user_id'))
            ->groupBy('shipments.id');
        if (session('department_id') == 7) {
            if (!in_array(session('id'), session('sale_users_bypass'))) {
                $shipments = $shipments->where(function ($query) {
                    $query->whereIn('u.id', session('tagged_shippers'));
                });
            }
        }

        $datatable = Datatables::of($shipments)
            ->editColumn('tracking', function ($shipments) {
                $route = route('cod.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            });
        if ($tracking_numbers = $request->get('tracking_numbers')) {
            $datatable->whereIn('shipments.tracking_number', explode(',', $tracking_numbers));
        }
        if ($request->get('dr_search_date_from') && $request->get('dr_search_date_to')) {
            $from = $request->get('dr_search_date_from');
            $to = $request->get('dr_search_date_to');
            $datatable->whereBetween('sret.created_at', [$from, $to]);
        }
        return $datatable->make(true);
    }

    public function daraz_mis_index()
    {
        // $sister_accounts = User::whereIn('id',session('sister_users'))->select('id', 'name')->get();
        $sister_accounts = DB::connection('reports')->table('merged_sister_account_mappings')->leftjoin('users as u', 'u.id', '=', 'merged_sister_account_mappings.sister_user_id')->where('head_user_id', session('user_id'))->select('u.id', 'u.name')->get();

        return view('client.reports.daraz_mis', compact('sister_accounts'));

    }

    public function daraz_mis_list(Request $request){
        $shipment = DB::connection('reports')->table('shipments')->join('users as u','u.id','=','shipments.user_id')
            ->join('shipment_status as ss','ss.id','=','shipments.shipper_status_id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->leftJoin('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where('sj.id','=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2 and shipments_journey.verification = 1)'));
            })
            ->leftJoin('shipments_journey as dr', function ($join) {
                $join->on('dr.shipment_id', '=', 'shipments.id')
                    ->where('dr.id', '=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id In (14, 25, 30, 36, 37) AND shipments_journey.verification = 1)'));
            })
            ->leftJoin('shipments_journey as atmpdate', function ($join) {
                $join->on('atmpdate.shipment_id', '=', 'shipments.id')
                    ->where('atmpdate.id', '=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 5 AND shipments_journey.verification = 1)'));
            })
            ->leftJoin('shipments_journey as sjrr', function ($join) {
                $join->on('sjrr.shipment_id', '=', 'shipments.id')
                    ->whereIn('shipments.shipper_status_id', [20, 21, 22, 23, 24, 25, 44, 47, 48, 57, 60])
                    ->where('sjrr.id', '=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id IN (12, 20) and shipments_journey.verification = 1 and shipments_journey.status_reason_id is not null)'));
            })
            ->leftJoin('shipment_status_reason as ssr', 'ssr.id', '=', 'sjrr.status_reason_id')

            ->select('shipments.tracking_number','shipments.return_address_id','shipments.shipper_status_id','sj.created_at as arrival_date','ss.name as status_name','ss.name as current_status','shipments.actual_weight', 'ssr.name as return_reason', 'atmpdate.created_at as last_attempt_date', 'dr.created_at as delivered_or_returned', 'dr.received_or_refused_by','u.name as shipper_name','shipments.id as shipment_id','u.id as shipper_id','shipments.shipper_status_id as status_id','shipments.created_at as created_at','dr.cnic', 'dr.relation');

        $shipment = $shipment->where(function ($query) {
            $query->where('shipments.user_id', 7306)
                ->orWhereIn('shipments.user_id', session('sister_users'));
        });


        $datatable = Datatables::of($shipment)
            ->addColumn('rider_remarks', function($shipment) {
                $rider_status = ShipmentsJourney::where('shipment_id',$shipment->shipment_id)->whereNotNull('rider_id')->get()->last();
                if ($rider_status) {
                    return $rider_status->remarks;
                } else {
                    return '-';
                }
            })
            ->addColumn('last_reason', function($shipment) {
                $last_reason = ShipmentsJourney::where('shipment_id',$shipment->shipment_id)->whereNotNull('status_reason_id')->get()->last();
                if($last_reason){
                    return $last_reason->shipment_status_reason->name ?? "-";
                } else {
                    return '-';
                }
                // ($rider_status->remarks) ? $rider_status->remarks : '-'

            })
            ->addColumn('attempts', function($shipment) {
                $reattempt_count = ShipmentsJourney::where('shipment_id', $shipment->shipment_id)
                    ->where('shipper_status_id','=',5)
                    ->where('verification','=',1)
                    ->select(DB::raw('count(shipment_id) as reattempts'))
                    ->get()->first();
                if($reattempt_count){
                    if($reattempt_count->reattempts-1 == -1){
                        return 0;
                    } else {
                        return $reattempt_count->reattempts - 1;
                    }
                } else {
                    return '-';
                }
            })
            ->addColumn('return_attempts', function($shipment) {
                $reattempt_count = ShipmentsJourney::where('shipment_id', $shipment->shipment_id)
                    ->where('shipper_status_id','=',23)
                    ->where('verification','=',1)
                    ->select(DB::raw('count(shipment_id) as return_attempts'))
                    ->get()->first();
                if($reattempt_count){
                    if($reattempt_count->return_attempts-1 == -1){
                        return 0;
                    }else{
                        return $reattempt_count->return_attempts-1;
                    }
                }else{
                    return '-';
                }

            })
            ->addColumn('return_attempt_time', function($shipment) {
                $reattempt_time = ShipmentsJourney::where('shipment_id', $shipment->shipment_id)
                    ->where('shipper_status_id','=',23)
                    ->where('verification','=',1)
                    ->select(DB::raw('count(shipment_id) as count, max(created_at) as created_at'))
                    ->latest()->first();
                if($reattempt_time){
                    if($reattempt_time->count > 1){
                        return $reattempt_time->created_at;
                        }else{
                        return '';
                    }
                }else{
                    return '-';
                }
            })
            ->addColumn('return_status', function ($shipment) {
                if($shipment->shipper_status_id == 25){

                    if(in_array($shipment->return_address_id, [31076, 31078, 31079, 31080, 33761,  1082, 31083, 31084, 31085, 33760, 31086,  31087, 31088, 31089, 33762])){
                        return 'Return to Daraz Warehouse';
                    }else{
                        return 'Return to Vendor';
    
                    }
                }else{
                    return '-';
                }
            })
            ->addColumn('tracking_number_link', function ($shipment) {
                $route = route('cod.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipment->tracking_number' class='tracking' target='_blank'>$shipment->tracking_number</a></u>";
            })
            ->editColumn('shipper_name', function ($shipment) {
                if($shipment->shipper_id == 7306){
                    return '-';
                }else{
                    return $shipment->shipper_name;
                }
            })
            ->editColumn('received_or_refused_by', function ($shipment) {
                    $received_or_refused_by = '';
                    if($shipment->received_or_refused_by){
                        $received_or_refused_by = $shipment->received_or_refused_by;
                    }
                    if($shipment->cnic){
                        $received_or_refused_by .= "|".$shipment->cnic;
                    }
                    if($shipment->relation){
                        $received_or_refused_by .= "|".$shipment->relation;
                    }
                    return $received_or_refused_by;
            })
            ->editColumn('current_status', function ($shipment) {
                if($shipment->status_id == 2 ||$shipment->status_id == 3 ||$shipment->status_id == 4 ||$shipment->status_id == 5 ||$shipment->status_id == 8 ||$shipment->status_id == 9 ||$shipment->status_id == 12 ||$shipment->status_id == 6 ||$shipment->status_id == 7 ||$shipment->status_id == 10 || $shipment->status_id == 11 || $shipment->status_id == 13  || $shipment->status_id == 15 || $shipment->status_id == 49 || $shipment->status_id == 52 || $shipment->status_id == 54  || $shipment->status_id == 55 || $shipment->status_id == 61 || $shipment->status_id == 62 || $shipment->status_id == 63){
                    return 'In Process';
                }elseif($shipment->status_id == 14){
                    return 'Delivered';
                }elseif($shipment->status_id == 20 ||$shipment->status_id == 21 ||$shipment->status_id == 22 ||$shipment->status_id == 23 ||$shipment->status_id == 24 || $shipment->status_id == 48 || $shipment->status_id == 44 || $shipment->status_id == 47 || $shipment->status_id == 57 || $shipment->status_id == 60){
                    return 'Return In Process';
                }elseif($shipment->status_id == 25){
                    return 'Returned';
                }else{
                    return $shipment->current_status;
                }
            });


        // if($tracking = $request->get('search_tracking')){
        //     $datatable->where('shipments.tracking_number', '=', $tracking);
        // }
        if ($tracking = $request->get('search_tracking')) {
            $tracking_numbers = explode(',', $tracking);
            $datatable->whereIn('shipments.tracking_number', $tracking_numbers);
        }
        if ($search_user = $request->get('search_user')) {
            $datatable->where('shipments.user_id', $search_user);
        }

        if ($origin = $request->get('search_origin')) {
            $datatable->where('oc.id', '=', $origin);
        }

        if ($destination = $request->get('search_destination')) {
            $datatable->where('dc.id', '=', $destination);
        }

        if ($status = $request->get('search_status')) {
            $datatable->where('ss.id', '=', $status);
        }

        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $datatable->whereBetween('sj.created_at', [$from, $to]);
        }
        if ($request->get('booking_date_from') && $request->get('booking_date_to')) {
            $from = $request->get('booking_date_from');
            $to = $request->get('booking_date_to');
            $datatable->whereBetween('shipments.created_at', [$from, $to]);
        }
        
        if ($request->get('dr_search_date_from') && $request->get('dr_search_date_to')) {
            $from = $request->get('dr_search_date_from');
            $to = $request->get('dr_search_date_to');
            $datatable->whereBetween('dr.created_at', [$from, $to]);
        }

        return $datatable->make(true);
    }

    public function weight_reconciliation_index()
    {
        $shipping_modes = ShippingMode::all();

        // $hubs = City::where('status', 1)->where('hub', 1)->get(['id', 'name']);
        // $zones = Zone::where('status', 1)->get(['id', 'name']);
        return view('client.reports.weight_reconciliation')->with(['shipping_modes' => $shipping_modes]);
    }

    public function weight_reconciliation_list(Request $request)
    {
        $shipments = DB::connection('reports')->table('shipments')->join('users as u', 'shipments.user_id', '=', 'u.id')
            ->leftJoin('shipping_modes as sm', 'shipments.shipping_mode_id', '=', 'sm.id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->leftJoin('shipments_journey as bkg_date', function ($join) {
                $join->on('bkg_date.shipment_id', '=', 'shipments.id')
                    ->where('bkg_date.id', '=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 1)'));
            })
            ->leftJoin('shipments_journey as arv_date', function ($join) {
                $join->on('arv_date.shipment_id', '=', 'shipments.id')
                    ->where('arv_date.id', '=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)'));
            })
            ->select(['shipments.id as shId','shipments.weight_charges', 'shipments.tracking_number', 'u.name as shipper', 'oc.name as origin', 'dc.name as destination', 'bkg_date.created_at as booking_date', 'arv_date.created_at as arrival_date', 'sm.mode as shipping_mode', 'shipments.estimated_weight', 'shipments.actual_weight', 'shipments.length', 'shipments.breadth', 'shipments.height'])
            ->whereNotNull('shipments.actual_weight');

        $shipments = $shipments->where(function ($query) {
            $query->where('shipments.user_id', session('user_id'));
        });

        $datatable = Datatables::of($shipments)
            ->editColumn('tracking_number_link', function ($shipment) {
                $route = route('cod.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipment->tracking_number' class='tracking' target='_blank'>$shipment->tracking_number</a></u>";
            })
            ->addColumn('difference', function ($shipment) {
                $difference = round($shipment->actual_weight - $shipment->estimated_weight, 2);
                return $difference;
            })
            ->addColumn('weighted_as', function ($shipment) {
                if ($shipment->length != null && $shipment->breadth != null && $shipment->height != null) {
                    return 'Volumetric';
                } else {
                    return 'Dense';
                }
            });

        // if ($tracking_numbers = $request->get('tracking_numbers')) {
        //     $datatable->whereIn('shipments.tracking_number', explode(',', $tracking_numbers));
        // }
        if ($search_shipping_mode = $request->get('search_shipping_mode')) {
            $datatable->where('sm.id', $search_shipping_mode);
        }
        if ($tracking_numbers = $request->get('tracking_numbers')) {
            $datatable->where('shipments.tracking_number', '=', $tracking_numbers);
        }
        if ($hub = $request->get('search_hub')) {
            $datatable->where('dc.hub_id', $hub);
        }
        if ($zone = $request->get('search_zone')) {
            $datatable->where('dc.zone_id', $zone);
        }
        if ($weighted_as = $request->get('weighted_as')) {
            if ($weighted_as == 1) {
                $datatable->whereNull('shipments.length')->whereNull('shipments.breadth')->whereNull('shipments.height');
            } else {
                $datatable->whereNotNull('shipments.length')->whereNotNull('shipments.breadth')->whereNotNull('shipments.height');
            }
        }
        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $datatable->whereBetween('arv_date.created_at', [$from, $to]);
        }
        return $datatable->make(true);
    }

    public function mms_index()
    {
        if (in_array(session('user_id'), [15636, 16292, 15587, 17363, 17747, 3324, 1091, 10104, 20040, 22343, 22395, 22230, 22946, 14110, 19507, 14781])) {
            $cities = DB::connection('reports')->table('cities')->select('id', 'name')->get();
            $hubs = DB::connection('reports')->table('cities')->where('hub', 1)->select('id', 'name')->get();
            $statuses = DB::connection('reports')->table('shipment_status')->whereNotIn('id', [1, 17])->get();
//            $shipping_modes = DB::connection('reports')->table('shipping_modes')->get(['id', 'mode']);
//            $business_categories = DB::connection('reports')->table('business_categories')->select('id', 'name')->get();
            return view('client.reports.mms')->with(['cities' => $cities, 'hubs' => $hubs, 'statuses' => $statuses]);
        } else{
            return redirect()->back()->with('error', 'Not Found!');
        }

    }
    public function mms_list(Request $request)
    {
        if (in_array(session('user_id'), [15636, 16292, 15587, 17363, 17747, 3324, 1091, 10104, 20040, 22343, 22395, 22230, 22946, 14110, 19507, 14781])) {
            $connection = 'reports';
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
                ->where('u.id', session('user_id'))
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
        else{
            return redirect()->back()->with('error', 'Not Found!');
        }
    }


    public function rider_pickup_index(){
        $hubs = DB::connection('reports')->table('cities')->select('id', 'name')->where('hub', 1)->where('status', 1)->get();

        return view('client.reports.rider_pickup_report')->with(['hubs' => $hubs]);
    }

    public function rider_pickup_list(Request $request){

        $rider_pickup = DB::connection('reports')->table('v2_pickup_notes')
            ->join('riders as r', 'r.id', '=', 'v2_pickup_notes.rider_id')
            ->join('v2_pickup_note_requests as pnr', 'pnr.pickup_note_id', '=', 'v2_pickup_notes.id')
            ->join('v2_pickup_requests as pr', 'pr.id', '=', 'pnr.pickup_request_id')
            ->join('cities as c', 'c.id', '=', 'pr.city_id')
            ->leftjoin('v2_pickup_request_shipments as prs', 'prs.pickup_request_id', '=', 'pr.id')
            ->leftjoin('shipments as s', 's.id', '=', 'prs.shipment_id')
            ->leftjoin('shipments_journey as arrsh', function ($join) {
                $join->on('arrsh.shipment_id', '=', 'prs.shipment_id')
                    ->where('arrsh.id', '=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = prs.shipment_id and shipments_journey.reference_1_id = pr.id and shipments_journey.shipper_status_id = 2 and verification = 1)'));
            })
            ->leftjoin('shipments_journey as total_s', function ($join) {
                $join->on('total_s.shipment_id', '=', 'prs.shipment_id')
                    ->where('total_s.id', '=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = prs.shipment_id and shipments_journey.reference_1_id = pr.id and shipments_journey.shipper_status_id = 53 and verification = 1)'));
            })

            ->select('v2_pickup_notes.status','pr.id as pickup_request_id','v2_pickup_notes.id','v2_pickup_notes.id as pickup_note_id','v2_pickup_notes.created_at as date','r.trax_id as rider_id','r.name as rider_name','c.name as origin','prs.shipment_id as shipment_id', DB::raw('count(arrsh.id) as arrived_shipments'), DB::raw('count(total_s.id) as scanned_shipments'))
            ->where('v2_pickup_notes.status',1)
            ->where('s.user_id', '=', session('user_id'))
            ->groupBy('v2_pickup_notes.id');

        $datatables = Datatables::of($rider_pickup)
            ->addColumn('scanned_shipments_btn', function ($entry) {
                $function = "scanned_shipments_popup('".$entry->id."')";
                if ($entry->scanned_shipments > 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle" onclick="'.$function.'" >' . $entry->scanned_shipments . '</button>';
                } else {
                    return 0;
                }
            })
            ->addColumn('arrived_shipments_btn', function ($entry) {
                $function = "arrived_shipments_popup('".$entry->id."')";
                if ($entry->arrived_shipments > 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle" onclick="'.$function.'" >' . $entry->arrived_shipments . '</button>';
                } else {
                    return 0;
                }
            })
            ->addColumn('without_scan_shipments_btn', function ($entry) {

                $function = "without_scan_shipments_popup('".$entry->id."')";
                if ($entry->arrived_shipments > 0) {

                    return '<button class="btn btn-sm btn-outline-info align-middle" onclick="'.$function.'" >' . ($entry->arrived_shipments-$entry->scanned_shipments) . '</button>';
                } else {
                    return 0;
                }
            })
            ->addColumn('without_scan_shipments', function ($entry) {

                if ($entry->arrived_shipments > 0) {

                    return  ($entry->arrived_shipments-$entry->scanned_shipments) ;
                } else {
                    return 0;
                }
            })

            ->addColumn('pickup_note_id_padded', function ($entry) {
                if ($entry->pickup_note_id != null) {
                    return str_pad($entry->pickup_note_id, 6, '0', STR_PAD_LEFT);
                }
                return '';
            });

        if($hub = $request->get('search_hub')){
            $datatables = $datatables->where('c.hub_id', '=', $hub);
        }

        if ($request->get('search_from') && $request->get('search_to')) {
            $from = $request->get('search_from');
            $to = $request->get('search_to');
            $datatables = $datatables->whereBetween('v2_pickup_notes.created_at', [$from,$to]);
        }

        return $datatables->make(true);
    }

    public function rider_pickup_scanned_shipments(Request $request){
        $shipments =  DB::connection('reports')->table('v2_pickup_notes')
            ->join('riders as r', 'r.id', '=', 'v2_pickup_notes.rider_id')
            ->join('v2_pickup_note_requests as pnr', 'pnr.pickup_note_id', '=', 'v2_pickup_notes.id')
            ->join('v2_pickup_requests as pr', 'pr.id', '=', 'pnr.pickup_request_id')
            ->join('v2_pickup_request_shipments as prs', 'prs.pickup_request_id', '=', 'pr.id')
            ->join('shipments as s', 's.id', '=', 'prs.shipment_id')
            ->join('shipments_journey as total_s', function ($join) {
                $join->on('total_s.shipment_id', '=', 'prs.shipment_id')
                    ->where('total_s.id', '=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = prs.shipment_id and shipments_journey.reference_1_id = pr.id and shipments_journey.shipper_status_id = 53 and verification = 1)'));
            })->select('s.tracking_number','s.id')
            ->where('v2_pickup_notes.id',$request->id)
            ->get();

        return response()->json(['status' => 1, 'data' => $shipments]);


    }

    public function rider_pickup_arrived_shipments(Request $request){
        $shipments =  DB::connection('reports')->table('v2_pickup_notes')
            ->join('riders as r', 'r.id', '=', 'v2_pickup_notes.rider_id')
            ->join('v2_pickup_note_requests as pnr', 'pnr.pickup_note_id', '=', 'v2_pickup_notes.id')
            ->join('v2_pickup_requests as pr', 'pr.id', '=', 'pnr.pickup_request_id')
            ->join('v2_pickup_request_shipments as prs', 'prs.pickup_request_id', '=', 'pr.id')
            ->join('shipments as s', 's.id', '=', 'prs.shipment_id')
            ->join('shipments_journey as arrsh', function ($join) {
                $join->on('arrsh.shipment_id', '=', 'prs.shipment_id')
                    ->where('arrsh.id', '=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = prs.shipment_id and shipments_journey.reference_1_id = pr.id and shipments_journey.shipper_status_id = 2 and verification = 1)'));
            })->select('s.tracking_number','s.id')
            ->where('v2_pickup_notes.id',$request->id)
            ->get();

        return response()->json(['status' => 1, 'data' => $shipments]);

    }

    public function rider_pickup_without_scan_shipments(Request $request){
        $scanned_shipments =  DB::connection('reports')->table('v2_pickup_notes')
            ->join('riders as r', 'r.id', '=', 'v2_pickup_notes.rider_id')
            ->join('v2_pickup_note_requests as pnr', 'pnr.pickup_note_id', '=', 'v2_pickup_notes.id')
            ->join('v2_pickup_requests as pr', 'pr.id', '=', 'pnr.pickup_request_id')
            ->join('v2_pickup_request_shipments as prs', 'prs.pickup_request_id', '=', 'pr.id')
            ->join('shipments as s', 's.id', '=', 'prs.shipment_id')
            ->join('shipments_journey as total_s', function ($join) {
                $join->on('total_s.shipment_id', '=', 'prs.shipment_id')
                    ->where('total_s.id', '=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = prs.shipment_id and shipments_journey.reference_1_id = pr.id and shipments_journey.shipper_status_id = 53 and verification = 1)'));
            })->select('s.tracking_number','s.id')
            ->where('v2_pickup_notes.id',$request->id)
            ->pluck('id')->toArray();

        $shipments =  DB::connection('reports')->table('v2_pickup_notes')
            ->join('riders as r', 'r.id', '=', 'v2_pickup_notes.rider_id')
            ->join('v2_pickup_note_requests as pnr', 'pnr.pickup_note_id', '=', 'v2_pickup_notes.id')
            ->join('v2_pickup_requests as pr', 'pr.id', '=', 'pnr.pickup_request_id')
            ->join('v2_pickup_request_shipments as prs', 'prs.pickup_request_id', '=', 'pr.id')
            ->join('shipments as s', 's.id', '=', 'prs.shipment_id')
            ->join('shipments_journey as arrsh', function ($join) {
                $join->on('arrsh.shipment_id', '=', 'prs.shipment_id')
                    ->where('arrsh.id', '=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = prs.shipment_id and shipments_journey.reference_1_id = pr.id and shipments_journey.shipper_status_id = 2 and verification = 1)'));
            })->select('s.tracking_number','s.id')
            ->where('v2_pickup_notes.id',$request->id)
            ->whereNotIn('s.id',$scanned_shipments)
            ->get();



        return response()->json(['status' => 1, 'data' => $shipments]);
    }
}
