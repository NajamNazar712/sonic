<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Admins\ActivityTrailController;
use App\Http\Models\Admin\AgentCallMonitoring;
use App\Http\Models\Admin\AgentDay;
use App\Http\Models\Admin\AdminRole;
use App\Http\Models\Admin\AgentDayLog;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Admin\OperationRidersCategory;
use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\DeliveryNoteShipment;
use App\Http\Models\Admin\MasterCargo\Bag;
use App\Http\Models\Admin\MasterCargo\BagStatus;
use App\Http\Models\Admin\SalePersonTag;
use App\Http\Models\Admin\StationDepositNote;
use App\Http\Models\BanksList;
use App\Http\Models\City;
use App\Http\Models\CorporateDefaultInsuranceCharge;
use App\Http\Models\CorporateInsuranceCharge;
use App\Http\Models\CRM\CrmRequestAgentHistory;
use App\Http\Models\CRM\CrmRequestRating;
use App\Http\Models\CRM\CrmRequest;
use App\Http\Models\CRM\CrmRequestStatusHistory;
use App\Http\Models\CrmAgent;
use App\Http\Models\Excel_reports\Debriefing;
use App\Http\Models\InsuranceCharge;
use App\Http\Models\MultipleSaleLead;
use App\Http\Models\Rider;
use App\Http\Models\ShipmentsJourney;
use App\Http\Models\ShipmentStatus;
use App\Http\Models\ShipmentStatusReason;
use App\Http\Models\ShippingMode;
use App\Http\Models\StationRecoveryReport;
use App\Http\Models\StationRecoveryReportDeposit;
use App\Http\Models\Shipment;
use App\Http\Models\V2Pickup\V2RiderPickup;
use App\Http\Models\Zone;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\Admin\DeliveryNote;
use App\Http\Models\Admin\MonthClosingResponsible;
use App\Http\Models\Admin\ReturnNote;
use App\Http\Models\Admin\ReturnNoteShipment;
use App\Http\Models\Shipper\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use PHPExcel_Cell;
use PHPExcel_Style_Fill;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Yajra\Datatables\Datatables;
use App\Http\Models\Admin\OSAChargesLog;
use App\Http\Models\Admin\ReturnRevertLog;
use App\Http\Models\CRM\CRMCount;


class AdminReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function qsr_index(Request $request)
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 137);
        if (session('department_id') == 7 && (!in_array(session('id'), session('sale_users_bypass')))) {
            $shippers = DB::connection('reports')->table('users')->whereIn('id', session('tagged_shippers'))->whereIn('status', [3, 4])->select('id', 'name')->get();
        } else {
            $shippers = DB::connection('reports')->table('users')->whereIn('status', [3, 4])->select('id', 'name')->get();
        }

        $cities = DB::connection('reports')->table('cities')->select('id', 'name')->get();
        $hubs = DB::connection('reports')->table('cities')->where('hub', 1)->select('id', 'name')->get();
        $shipping_modes = DB::connection('reports')->table('shipping_modes')->get();
        $types = [1 => 'Sales', 2 => 'CX'];
        $shipment_status = ShipmentStatus::where('id' ,'>' ,0)->select('id','name')->get();
//        dd($shipment_status);
        return view('admin.reports.qsr_report')->with(['shippers' => $shippers, 'cities' => $cities, 'hubs' => $hubs, 'shippimg_modes' => $shipping_modes, 'types' => $types,'shipment_status' => $shipment_status]);
    }

    public function qsr_list(Request $request)
    {
        $connection = 'reports';
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 138);
        }
        $shipments = DB::connection('reports')->table('shipments')->join('users as u', 'shipments.user_id', '=', 'u.id')
            ->join('shipping_modes as sm', 'shipments.shipping_mode_id', '=', 'sm.id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->join('cities as h', 'dc.hub_id', '=', 'h.id')
            ->leftjoin('user_shipping_infos AS rsi', 'shipments.return_address_id', '=', 'rsi.id')
            ->leftjoin('cities AS rc', 'rsi.city_id', '=', 'rc.id')
            ->leftjoin('zones as z', 'z.id', '=', 'h.zone_id')
            ->join('booking_types as bt', 'bt.id', '=', 'shipments.booking_type_id')
            ->join('shipment_status as ss', 'ss.id', '=', 'shipments.shipper_status_id')
            ->leftJoin('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where('sj.id', '=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)'));
            })
            ->leftJoin('shipments_journey as journey', function ($join) {
                $join->on('journey.shipment_id', '=', 'shipments.id')
                    ->where('journey.id', '=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id)'));
            })
            ->leftjoin('shipment_status_reason as ssr', 'ssr.id', '=', 'journey.status_reason_id')
            ->leftjoin('shipment_items as si', function ($join) {
                $join->on('si.shipment_id', '=', 'shipments.id')
                    ->where('si.type', '=', 0);
            })
            ->leftJoin('shipments_journey as sjr', function ($join) use ($connection) {
                $join->on('sjr.shipment_id', '=', 'shipments.id')
                    ->where('sjr.id', '=',
                        DB::connection($connection)->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id In(20,21,22,23,24,25,47,48,60))'));
            })
            //->leftJoin('shipment_status_reason as ssr', 'ssr.id', '=', 'sjr.status_reason_id')
            ->leftjoin('products as p', 'p.id', '=', 'si.product_type_id')
            ->select(['z.name  as zone', 'p.product_name as product_type', 'si.description as description', 'ssr.name as reason', 'sjr.remarks as remarks', 'ss.name as status', 'shipments.id as shId', 'shipments.tracking_number', 'shipments.tracking_number as tracking_number_link', 'u.name as shipper', 'ss.name as history_status', 'bt.booking_type as service_type', 'sj.created_at as arrival', 'oc.name as origin', 'dc.name as destination', 'h.name as hub', 'shipments.amount', 'journey.created_at as last_status_date', 'shipments.consignee_name as name', 'shipments.booking_type_id', 'shipments.created_at', 'usi.poc', 'u.id as account_no', 'sm.mode as shipping_mode', 'shipments.order_id as order_id', 'rc.name as return_city', DB::raw('(select count(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 5) as total_attempt')]);

        $type = $request->get('search_types');

        if ($type && $type == 2) {
            $shipments = $shipments->whereNotIn('shipments.shipper_status_id', [1, 14, 17, 25, 31, 36, 38]);
        } else {
            $shipments = $shipments->whereNotIn('shipments.shipper_status_id', [1, 14, 17, 25, 31, 36, 38, 51]);
        }

        if (session('role_id') != 1) {
            $shipments = $shipments->where(function ($query) {
                $query->where(function ($sub_query) {
                    $sub_query->whereIn('dc.hub_id', session('hubs'));
                })
                    ->orWhere(function ($sub_query) {
                        $sub_query->whereIn('oc.hub_id', session('hubs'));
                    });
            });
        }
        if (session('department_id') == 7) {
            if (!in_array(session('id'), session('sale_users_bypass'))) {
                $shipments = $shipments->whereIn('shipments.user_id', session('tagged_shippers'));
            }
        }
        $datatable = Datatables::of($shipments)
            ->editColumn('tracking_number_link', function ($shipments) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->editColumn('order_id', function ($shipment) {
                return ($shipment->order_id) ? $shipment->order_id : '-';
            })
            ->editColumn('amount', function ($shipment) {
                return number_format($shipment->amount);
            })
            ->editColumn('account_no', function ($shipments) {
                return str_pad($shipments->account_no, 6, '0', STR_PAD_LEFT);
            })
            ->editColumn('shipper', function ($shipment) {
                if ($shipment->booking_type_id == 4) {
                    return $shipment->shipper . ' (' . $shipment->poc . ')';
                } else {
                    return $shipment->shipper;
                }
            })
            ->filterColumn('u.name', function ($query, $keyword) {
                $query->where(function ($sub_query) use ($keyword) {
                    $sub_query->where('shipments.booking_type_id', '!=', 4)
                        ->where('u.name', 'like', '%' . $keyword . '%');
                })
                    ->orWhere(function ($sub_query) use ($keyword) {
                        $sub_query->where('shipments.booking_type_id', '=', 4)
                            ->where('usi.poc', 'like', '%' . $keyword . '%');
                    });
            })
            ->addColumn('aging', function ($shipments) {

                $days = Carbon::now()->diffInDays($shipments->arrival);
                if ($days == 0) {
                    return "-";
                } else {
                    return $days;
                }
            })
            ->addColumn('aging_last_status', function ($shipments) {

                $days = Carbon::now()->diffInDays($shipments->last_status_date);
                if ($days == 0) {
                    return "-";
                } else {
                    return $days;
                }
            });
        /*if ($shipper = $request->get('search_shipper')) {
            $datatable->where('u.id', '=', $shipper);
        }*/
        if ($search_shipper = $request->get('search_shipper')) {
            $datatable->where('shipments.user_id', $search_shipper);
        }
        if ($search_shippers = $request->get('search_shippers')) {
            $datatable->whereIn('shipments.user_id', $search_shippers);
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
        if ($shipping_mode = $request->get('search_shipping_mode')) {
            $datatable->where('sm.id', '=', $shipping_mode);
        }
        if ($search_qsr = $request->get('search_qsr')) {
            if ($search_qsr != 3) {
                if ($search_qsr == 1) {
                    $datatable->whereIn('shipments.shipper_status_id', [2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 15, 18, 49, 51, 52, 54, 55]);
                }
                if ($search_qsr == 2) {
                    $datatable->whereIn('shipments.shipper_status_id', [20, 21, 22, 23, 24, 26, 27, 28, 29, 32, 33, 34, 35, 37, 44, 45, 46, 47, 48, 50]);
                }
            }
        }
        if ($request->get('search_from') && $request->get('search_to')) {
            $from = $request->get('search_from');
            $to = $request->get('search_to');
            $datatable->whereBetween('sj.created_at', [$from, $to]);
        }
        if ($status_id = $request->get('search_shipment_status')) {
//            dd($status_id);
            $datatable->where('ss.id', '=', $status_id);
        }

        return $datatable->make(true);
    }

    public function return_note_index(Request $request)
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 131);
        $riders = DB::connection('reports')->table('riders')->get(['id', 'name']);
        $admins = DB::connection('reports')->table('admins')->get(['id', 'name']);
        $hubs = DB::connection('reports')->table('cities')->where('hub', 1)->select('id', 'name')->get();
        $shipping_modes = DB::connection('reports')->table('shipping_modes')->get(['id', 'mode']);
        return view('admin.reports.return_notes_report')->with(['riders' => $riders, 'admins' => $admins, 'hubs' => $hubs, 'shipping_modes' => $shipping_modes]);
    }

    public function return_note_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 132);
        }
        $return_note = DB::connection('reports')->table('return_notes')
            ->join('cities AS oc', 'return_notes.hub_id', '=', 'oc.id')
            ->join('riders', 'riders.id', '=', 'return_notes.rider_id')
            ->leftjoin('return_note_shipments as rns', 'rns.return_note_id', '=', 'return_notes.id')
            ->leftjoin('shipments', 'shipments.id', '=', 'rns.shipment_id')
            ->join('admins as cr', 'cr.id', '=', 'return_notes.admin_id')
            ->leftjoin('admins as up', 'up.id', '=', 'return_notes.updated_by')
            ->select(['return_notes.id', 'return_notes.id as return_note_id', 'return_notes.id as return_note_link', 'up.name as updated_by', 'return_notes.shipments_count', 'return_notes.shipments_count as shipments_count_link', 'return_notes.updated_at', 'return_notes.updated_at as submission_date', 'riders.name as rider', 'cr.name as created_by', 'return_notes.created_at as created_at', 'return_notes.image', DB::raw('(SELECT COUNT(id) FROM shipments_journey where shipper_status_id = 25 and reference_1_id = return_notes.id and verification = 1 ) as delivered_to_shipper_count'), DB::raw('(SELECT COUNT(id) FROM shipments_journey where shipper_status_id = 25 and reference_1_id = return_notes.id and verification = 1 ) as delivered_to_shipper_count_link')])->groupBy('return_notes.id');
        if (session('role_id') != 1 || !in_array(session('id'), session('sale_users_bypass'))) {
            $return_note = $return_note->whereIn('return_notes.hub_id', session('hubs'));
        }
        if (session('department_id') == 7) {
            if (!in_array(session('id'), session('sale_users_bypass'))) {
                $return_note = $return_note->whereIn('shipments.user_id', session('tagged_shippers'));
            }
        }

        if (session('department_id') == 8) {
            $return_note = $return_note->where('shipments.shipment_type',2);
        }
        $return = Datatables::of($return_note)
            ->addColumn('aging', function ($return_note) {
                return ($return_note->submission_date && $return_note->created_at) ? with(new Carbon($return_note->submission_date, 'UTC'))->diffInDays($return_note->created_at) : '-';
            })
            ->editColumn('return_note_id', function ($return_note) {
                return str_pad($return_note->return_note_id, 6, '0', STR_PAD_LEFT);
            })
            ->editColumn('return_note_link', function ($return_note) {
                return '<button class="btn btn-sm btn-outline-info align-middle print"><i class="la la-lg la-print align-middle"></i> <span class="align-middle">' . str_pad($return_note->id, 6, '0', STR_PAD_LEFT) . '</span></button>';
            })
            ->editColumn('delivered_to_shipper_count', function ($deliveries) {
                if ($deliveries->delivered_to_shipper_count != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $deliveries->delivered_to_shipper_count . '</button>';
                } else {

                    return '-';
                }
            })
            ->addColumn('image', function ($return_note) {
                if ($return_note->delivered_to_shipper_count != 0) {
                    return "<a href='#' class='btn btn-block btn-outline-info mr-1 image-popup'><i class='la la-image'></i></a>";
                } else {

                    return '-';
                }
            })
            ->editColumn('shipments_count_link', function ($return_note) {
                if ($return_note->shipments_count != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $return_note->shipments_count . '</button>';
                } else {
                    return 0;
                }
            });

        if ($rn_no = $request->get('search_rn_no')) {
            $return->where('return_notes.id', '=', $rn_no);
        }
        if ($tracking = $request->get('search_tracking')) {
            $return->join('return_note_shipments as rnst', 'rnst.return_note_id', '=', 'return_notes.id')
                ->join('shipments as s', 'rnst.shipment_id', '=', 's.id')
                ->where('s.tracking_number', '=', $tracking);
        }
        if ($rider = $request->get('search_rider')) {
            $return->where('riders.id', '=', $rider);
        }
        if ($hub = $request->get('search_hub')) {
            $return->where('oc.id', '=', $hub);
        }
        if ($mode = $request->get('search_shipping_mode')) {
            $return->where('shipments.booking_type_id', '=', $mode);
        }
        if ($created_by = $request->get('search_created_by')) {
            $return->where('cr.id', '=', $created_by);
        }
        if ($submitted_by = $request->get('search_submitted_by')) {
            $return->where('up.id', '=', $submitted_by);
        }
        if ($submission_date = $request->get('search_submission')) {
            $return->whereDate('return_notes.updated_at', $submission_date);
        }
        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $return->whereBetween('return_notes.created_at', [$from, $to]);
        }
        return $return->make(true);
    }

    //Return Note Print
    public function history_delivered_shipments(Request $request)
    {
        $return_note_id = $request->input('delivered_to_shipper_count');
        $return_note_shipments = ShipmentsJourney::where('reference_1_id', $return_note_id)->where('shipper_status_id', 25)->where('verification', 1)->pluck('shipment_id')->toArray();
        $shipments = array();
        if (count($return_note_shipments) > 0) {
            foreach ($return_note_shipments as $shipment_id) {
                $shipment = Shipment::find($shipment_id);
                $shipments[] = $shipment->tracking_number;
            }
            return ['status' => 0, 'success' => 'Return Note Shipments', 'shipments' => $shipments];
        } else {
            return ['status' => 0, 'success' => 'No Return Note Shipments', 'shipments' => FALSE];
        }
    }

    public function return_note_shipments(Request $request)
    {
        $return_note_id = $request->input('return_note_id');
        $return_note_shipments = DB::connection('reports')->table('return_note_shipments')->where('return_note_id', $return_note_id)->get();
        $shipments = array();
        if ($return_note_shipments->count() != 0) {
            foreach ($return_note_shipments as $return_note_shipment) {
                $shipment = DB::connection('reports')->table('shipments')->find($return_note_shipment->shipment_id);
                $shipments[] = $shipment->tracking_number;
            }
            return ['status' => 0, 'success' => 'Return Note Shipments', 'shipments' => $shipments];
        } else {
            return ['status' => 0, 'success' => 'No Return Note Shipments', 'shipments' => FALSE];
        }
    }

    public function pickup_note_index(Request $request)
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 123);
        $riders = DB::connection('reports')->table('riders')->get(['id', 'name']);
        $admins = DB::connection('reports')->table('admins')->get(['id', 'name']);
        $cities = DB::connection('reports')->table('cities')->get(['id', 'name']);
        $shipping_modes = DB::connection('reports')->table('shipping_modes')->get(['id', 'mode']);
        return view('admin.reports.pickup_notes_report')->with(['riders' => $riders, 'admins' => $admins, 'cities' => $cities, 'shipping_modes' => $shipping_modes]);
    }

    public function pickup_note_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 124);
        }
        $pickup_note = DB::connection('reports')->table('pickup_notes')->join('cities', 'cities.id', '=', 'pickup_notes.city_id')
            ->leftjoin('riders', 'riders.id', '=', 'pickup_notes.rider_id')
            ->leftjoin('admins as ab', 'ab.id', '=', 'pickup_notes.assigned_by_user_id')
            ->leftjoin('admins as up', 'up.id', '=', 'pickup_notes.updated_by')
            ->leftjoin('pickup_note_requests as pnr', 'pnr.pickup_note_id', '=', 'pickup_notes.id')
            ->leftjoin('pickup_requests as pr', 'pr.id', '=', 'pnr.pickup_request_id')
            ->select(['pr.shipper_id', 'pickup_notes.id as pn_id', 'pickup_notes.id as pickup_note_no', 'cities.name as city', 'pickup_notes.pickups', DB::connection('reports')->raw('(select SUM(received) as received from pickup_requests where pickup_requests.id in (select pickup_request_id from pickup_note_requests where pickup_note_id = pickup_notes.id)) AS received'), 'riders.name as rider', 'pickup_notes.created_at as assigned_date', 'ab.name as assigned_by', 'pickup_notes.updated_at as completed_date', 'up.name as completed_by'])
            ->where('pickup_notes.status_id', 4);
        if (session('role_id') != 1) {
            $pickup_note = $pickup_note->whereIn('cities.hub_id', session('hubs'));
        }
        if (session('department_id') == 7) {
            if (!in_array(session('id'), session('sale_users_bypass'))) {
                $pickup_note = $pickup_note->whereIn('pr.shipper_id', session('tagged_shippers'));
            }
        }
        $pickup_note = Datatables::of($pickup_note)
            ->editColumn('pn_id', function ($pickup_note) {
                return str_pad($pickup_note->pn_id, 6, '0', STR_PAD_LEFT);
            })
            ->editColumn('pickup_note_no', function ($pickup_note) {
                return '<button class="btn btn-sm btn-outline-info align-middle print"><i class="la la-lg la-print align-middle"></i> <span class="align-middle">' . str_pad($pickup_note->pickup_note_no, 6, '0', STR_PAD_LEFT) . '</span></button>';
            })
            ->addColumn('aging', function ($pickup_note) {
                $assigned_date = Carbon::parse($pickup_note->assigned_date)->startOfDay();

                $completed_date = Carbon::parse($pickup_note->completed_date)->startOfDay();

                return $assigned_date->diffInDays($completed_date) . 'd';
            })
            ->addColumn('bookings_link', function ($pickup_notes) {
                if ($pickup_notes->received != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $pickup_notes->received . '</button>';
                } else {
                    return 0;
                }
            });

        if ($pn_no = $request->get('search_pn_no')) {
            $pickup_note->where('pickup_notes.id', '=', $pn_no);
        }
        if ($assigned_by = $request->get('search_assigned_by')) {
            $pickup_note->where('ab.id', '=', $assigned_by);
        }
        if ($rider = $request->get('search_rider')) {
            $pickup_note->where('riders.id', '=', $rider);
        }
        if ($city = $request->get('search_city')) {
            $pickup_note->where('cities.id', '=', $city);
        }
        if ($shipping_mode = $request->get('search_shipping_mode')) {
            $pickup_note = $pickup_note->leftjoin('pickup_request_received_shipments as prrs', 'prrs.pickup_request_id', '=', 'pr.id')
                ->leftjoin('shipments as s', 's.id', '=', 'prrs.shipment_id')
                ->where('s.booking_type_id', '=', $shipping_mode)
                ->groupBy('pickup_notes.id');
        }
        if ($submitted_by = $request->get('search_completed_by')) {
            $pickup_note->where('up.id', '=', $submitted_by);
        }
        if ($submission_date = $request->get('search_completed_date')) {
            $pickup_note->whereDate('pickup_notes.updated_at', $submission_date);
        }
        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $pickup_note->whereBetween('pickup_notes.created_at', [$from, $to]);
        }
        return $pickup_note->make(true);
    }

    public function pickup_note_bookings(Request $request)
    {
        $pickup_note_id = $request->input('pickup_note_id');

        $pickup_note_requests = DB::connection('reports')->table('pickup_note_requests')->where('pickup_note_id', $pickup_note_id)->get();

        if ($pickup_note_requests->count() != 0) {
            $pickup_request_all_received_shipments = array();
            $bookings = array();

            foreach ($pickup_note_requests as $pickup_note_request) {
                $pickup_request = DB::connection('reports')->table('pickup_requests')->find($pickup_note_request->pickup_request_id);
                $shipper = DB::connection('reports')->table('users')->find($pickup_request->shipper_id)->name;
                $pickup_request_all_received_shipments[$shipper] = DB::connection('reports')->table('pickup_request_received_shipments')->where('pickup_request_id', $pickup_request->id)->get();
                if ($pickup_request_all_received_shipments[$shipper]->count() != 0) {

                    foreach ($pickup_request_all_received_shipments[$shipper] as $all_shipments) {
                        $shipment = $all_shipments->shipment_id;
                        $shipment_details = DB::connection('reports')->table('shipments')->find($shipment);
                        $bookings[$shipper][] = $shipment_details->tracking_number;
                    }
                }

            }
            return ['status' => 0, 'success' => 'Booked Shipments', 'booked' => $bookings];

        } else {
            return ['status' => 0, 'success' => 'No Pickup requests found', 'booked' => FALSE];
        }


    }

    public function cargo_received_index(Request $request)
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 127);
        $shippimg_modes = DB::connection('reports')->table('shipping_modes')->get();
        $cities = DB::connection('reports')->table('cities')->select('id', 'name')->where('hub', 1)->get();
        return view('admin.reports.cargo_received_report')->with(['cities' => $cities, 'shippimg_modes' => $shippimg_modes]);
    }

    public function cargo_received_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 128);
        }
        $cargo_received = DB::connection('reports')->table('cargo_consignments')->join('cities as oc', 'oc.id', '=', 'cargo_consignments.origin_hub_id')
            ->join('cities as h', 'h.id', '=', 'cargo_consignments.destination_hub_id')
            ->join('shipping_modes as sm', 'sm.id', '=', 'cargo_consignments.shipping_mode_id')
            ->join('admins as si', 'si.id', '=', 'cargo_consignments.sender_id')
            ->join('admins as ri', 'ri.id', '=', 'cargo_consignments.receiver_id')
            ->select(['cargo_consignments.id as cargo_id', 'cargo_consignments.id as cargo_id_link', 'oc.name as origin', 'h.name as destination', 'cargo_consignments.shipments', 'cargo_consignments.shipments as shipments_link', 'sm.mode as shipping_mode', 'cargo_consignments.shipments_weight', DB::connection('reports')->raw('(SELECT SUM(`s`.`chargeable_weight`) FROM `shipments` AS `s` INNER JOIN `cargo_consignment_shipments` AS `css` ON `s`.`id` = `css`.`shipment_id` WHERE `css`.`cargo_consignment_id` = `cargo_consignments`.`id`) AS `chargeable_weight`'), 'cargo_consignments.actual_weight', 'cargo_consignments.vendor_weight', 'cargo_consignments.created_at as transit_at', 'si.name as transit_by', 'ri.name as received_by', 'cargo_consignments.updated_at as received_at', 'cargo_consignments.received_shipments', 'cargo_consignments.type as cargo_type', 'cargo_consignments.seal_number'])
            ->where('cargo_consignments.status_id', 3);
        if (session('role_id') != 1) {
            $cargo_received = $cargo_received->where(function ($query) {
                $query->whereIn('oc.hub_id', session('hubs'))->orWhereIn('h.hub_id', session('hubs'));
            });
        }
        $cargo = Datatables::of($cargo_received)
            ->addColumn('aging', function ($cargo_received) {
                return ($cargo_received->received_at && $cargo_received->transit_at) ? Carbon::parse($cargo_received->received_at)->diffInDays($cargo_received->transit_at) : '-';
            })
            ->editColumn('cargo_id_link', function ($cargo_received) {
                $print_cargo = "<u><a href='javascript:void(0);' class='cargo_print'>" . str_pad($cargo_received->cargo_id, 6, '0', STR_PAD_LEFT) . "</a></u>";
                return $print_cargo;
            })
            ->editColumn('cargo_type', function ($cargo_received) {
                if ($cargo_received->cargo_type == 1) {
                    return 'Normal';
                } else {
                    return 'Return';
                }
            })
            ->editColumn('shipments_link', function ($cargo_received) {
                return '<button class="btn btn-sm btn-outline-info align-middle">' . $cargo_received->shipments . '</button>';
            });

        if ($cargo_no = $request->get('search_cargo_no')) {
            $cargo->where('cargo_consignments.id', '=', $cargo_no);
        }
        if ($tracking = $request->get('search_tracking')) {
            $cargo->join('cargo_consignment_shipments as ccs', 'ccs.cargo_consignment_id', '=', 'cargo_consignments.id')
                ->join('shipments as s', 'ccs.shipment_id', '=', 's.id')
                ->where('s.tracking_number', '=', $tracking);
        }
        if ($origin = $request->get('search_origin')) {
            $cargo->where('oc.id', '=', $origin);
        }
        if ($destination = $request->get('search_destination')) {
            $cargo->where('h.id', '=', $destination);
        }
        if ($mode = $request->get('search_shippimg_modes')) {
            $cargo->where('sm.id', '=', $mode);
        }
        if ($transit_date = $request->get('search_transit_date')) {
            $cargo->whereDate('cargo_consignments.created_at', $transit_date);
        }
        if ($received_date = $request->get('search_received_date')) {
            $cargo->whereDate('cargo_consignments.updated_at', $received_date);
        }
        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $cargo->whereBetween('cargo_consignments.created_at', [$from, $to]);
        }
        if ($cargo_type = $request->get('cargo_type')) {
            if ($cargo_type != 0) {
                $cargo->where('cargo_consignments.type', $cargo_type);
            }
        }
        return $cargo->make(true);
    }

    public function cargo_shipments(Request $request)
    {
        $tracking_numbers = array();

        $cargo_consignments_shipments = DB::connection('reports')->table('cargo_consignment_shipments')->where('cargo_consignment_id', $request->id)->get();

        foreach ($cargo_consignments_shipments as $cargo_consignments_shipment) {
            $shipment = DB::connection('reports')->table('shipments')->find($cargo_consignments_shipment->shipment_id);

            $tracking_number = array();

            $tracking_number['tracking_number'] = $shipment->tracking_number;
            $tracking_number['estimated_weight'] = $shipment->estimated_weight;
            $tracking_number['actual_weight'] = $shipment->actual_weight;
            $tracking_number['chargeable_weight'] = ($shipment->chargeable_weight) ? $shipment->chargeable_weight : '';

            $tracking_numbers[] = $tracking_number;
        }

        return $tracking_numbers;
    }


    public function multiple_iban_index(Request $request)
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 125);
        $iban_no = DB::connection('reports')->table('user_bank_infos')->get();
        $shippers = DB::connection('reports')->table('users')->whereIn('status', [3, 4])->select('id', 'name')->get();
        return view('admin.reports.multiple_IBAN_no_change')->with(['iban' => $iban_no, 'shippers' => $shippers]);
    }

    public function multiple_iban_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 126);
        }
        $iban_received = DB::connection('reports')->table('user_bank_infos')->join('users as u', 'u.id', '=', 'user_bank_infos.user_id')
            ->join('cities AS oc', 'user_bank_infos.city_id', '=', 'oc.id')
            ->join('banks_lists AS bl', 'bl.id', '=', 'user_bank_infos.bank_name')
            ->select(['user_bank_infos.id as account_id', 'u.name as shipper', 'bl.name as bankname',
                'user_bank_infos.bank_branch', 'user_bank_infos.account_no', 'user_bank_infos.account_title',
                'oc.name as city', 'user_bank_infos.iban', 'user_bank_infos.default_bank as default', 'user_bank_infos.created_at as bank_added_at'
            ]);
        $user_bank = Datatables::of($iban_received)
            ->editColumn('default', function ($bank) {
                if ($bank->default == 0) {
                    return "No";
                } else {
                    return "Yes";
                }
            });

        if ($ibanNo = $request->get('search_iban_no')) {
            $user_bank->where('user_bank_infos.iban', '=', $ibanNo);
        }
        if ($shipper = $request->get('search_shipper')) {
            $user_bank->where('u.id', '=', $shipper);
        }
        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $user_bank->whereBetween('user_bank_infos.created_at', [$from, $to]);
        }


        return $user_bank->make(true);

    }


    public function lead_time_index(Request $request)
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 135);
//        $shippers = User$generator::all(['id','name']);
        $cities = DB::connection('reports')->table('cities')->get(['id', 'name']);
        $shipper = DB::connection('reports')->table('users')->get(['id', 'name']);
        $hubs = DB::connection('reports')->table('cities')->select(['id', 'name'])->where('hub', 1)->get();
        $statuses = DB::connection('reports')->table('shipment_status')->get(['id', 'name']);
        $shipping_modes = DB::connection('reports')->table('shipping_modes')->get();
        return view('admin.reports.lead_time_report')->with(['cities' => $cities, 'statuses' => $statuses, 'hubs' => $hubs, 'shipper' => $shipper, 'shipping_modes' => $shipping_modes]);
    }

    public function lead_time_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 136);
        }
        $shipments = DB::connection('reports')->table('shipments')->join('users as u', 'u.id', '=', 'shipments.user_id')
//            ->join('user_bank_infos as ubi','ubi.user_id','=','u.id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->join('cities as h', 'dc.hub_id', '=', 'h.id')
            ->join('shipment_status as ss', 'ss.id', '=', 'shipments.shipper_status_id')
            ->join('shipping_modes', 'shipping_modes.id', '=', 'shipments.shipping_mode_id')
            ->leftjoin('shipment_payment_status as sps', 'shipments.payment_status_id', '=', 'sps.id')
            ->leftJoin('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where('sj.id', '=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)'));
            })
            ->leftJoin('shipments_journey as radd', function ($join) {
                $join->on('radd.shipment_id', '=', 'shipments.id')
                    ->where('radd.id', '=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 4)'));
            })
            ->leftJoin('shipments_journey as fstatus', function ($join) {
                $join->on('fstatus.shipment_id', '=', 'shipments.id')
                    ->where('fstatus.id', '>',
                        DB::connection('reports')->raw('(select min(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 5)'));
            })
            ->leftJoin('shipments_journey as lstatus', function ($join) {
                $join->on('lstatus.shipment_id', '=', 'shipments.id')
                    ->where('lstatus.id', '=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.verification = 0)'));
            })
            ->leftJoin('shipments_journey as dd', function ($join) {
                $join->on('dd.shipment_id', '=', 'shipments.id')
                    ->where('dd.id', '=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id In(14,16,30,36) )'));
            })
            ->leftJoin('shipments_journey as rc', function ($join) {
                $join->on('rc.shipment_id', '=', 'shipments.id')
                    ->where('rc.id', '=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id In(20,42))'));
            })
            ->leftJoin('shipments_journey as rrad', function ($join) {
                $join->on('rrad.shipment_id', '=', 'shipments.id')
                    ->where('rrad.id', '=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 22)'));
            })
            ->leftJoin('shipments_journey as rds', function ($join) {
                $join->on('rds.shipment_id', '=', 'shipments.id')
                    ->where('rds.id', '=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id In(24,25,29,31,35,38))'));
            })
            ->leftJoin('shipments_journey as pd', function ($join) {
                $join->on('pd.shipment_id', '=', 'shipments.id')
                    ->where('pd.id', '=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id In(39,40,41,43))'));
            })
            ->leftJoin('shipments_journey as ret_or_del', function ($join) {
                $join->on('ret_or_del.shipment_id', '=', 'shipments.id')
                    ->where('ret_or_del.id', '=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id In(14,20,30,36,37,42))'));
            })
            ->leftJoin('shipments_journey as lj', function ($join) {
                $join->on('lj.shipment_id', '=', 'shipments.id')
                    ->where('lj.id', '=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id)'));
            })
            ->leftJoin('delivery_note_shipments as dns', function ($join) {
                $join->on('dns.shipment_id', '=', 'shipments.id')
                    ->where('dns.delivery_note_id', '=',
                        DB::connection('reports')->raw('(select min(delivery_note_id) from delivery_note_shipments where delivery_note_shipments.shipment_id = shipments.id)'));
            })
            ->leftJoin('shipments_journey as fatstatus', function ($join) {
                $join->on('fatstatus.shipment_id', '=', 'shipments.id')
                    ->where('fatstatus.id', '=',
                        DB::connection('reports')->raw('(select min(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 5)'));
            })
            ->leftJoin('shipment_status as fs', 'fs.id', '=', 'fstatus.shipper_status_id')
            ->leftJoin('shipment_status as ls', 'ls.id', '=', 'lstatus.shipper_status_id')
            ->leftJoin('shipment_status as rdss', 'rdss.id', '=', 'rds.shipper_status_id')
            ->leftjoin('delivery_note_shipments as dnss', function ($join) {
                $join->on('dnss.shipment_id', '=', 'shipments.id')
                    ->where('dnss.delivery_note_id', '=',
                        DB::connection('reports')->raw('(select max(delivery_note_id) from delivery_note_shipments where delivery_note_shipments.shipment_id = shipments.id)'));
            })
//            ->leftjoin('shipment_status as sss','sss.id','=','lsj.shipper_status_id')
            ->leftjoin('cargo_consignment_shipments as ccs', function ($join) {
                $join->on('ccs.shipment_id', '=', 'shipments.id')
                    ->leftjoin('cargo_consignments as cc', 'cc.id', '=', 'ccs.cargo_consignment_id')
                    ->leftjoin('cargo_consignment_junction_receivals as ccjr', 'ccjr.junction_id', '=', 'cc.junction_hub_1_id');
            })
//            ->leftjoin('transport_mode_vendor as tmv','tmv.id','=','cc.transport_mode_vendor_id')
            ->leftjoin('delivery_note_shipments as dnssaa', function ($join) {
                $join->on('dnssaa.shipment_id', '=', 'shipments.id')
                    ->where('dnssaa.delivery_note_id', '=',
                        DB::connection('reports')->raw('(select max(delivery_note_id) from delivery_note_shipments where delivery_note_shipments.shipment_id = shipments.id)'));
            })
            ->leftjoin('delivery_note_shipments as fdnsv', function ($join) {
                $join->on('fdnsv.shipment_id', '=', 'shipments.id')
                    ->where('fdnsv.delivery_note_id', '=',
                        DB::connection('reports')->raw('(select min(delivery_note_id) from delivery_note_shipments where delivery_note_shipments.shipment_id = shipments.id)'));
            })
//            ->leftjoin('shipments_journey as lss','lss.shipper_status_id','=',[7, 8, 9, 10, 11, 12, 15, 18])
            ->leftjoin('delivery_note_shipments as ldnsv', function ($join) {
                $join->on('ldnsv.shipment_id', '=', 'shipments.id')
                    ->where('ldnsv.delivery_note_id', '=',
                        DB::connection('reports')->raw('(select max(delivery_note_id) from delivery_note_shipments where delivery_note_shipments.shipment_id = shipments.id)'));
            })
            ->leftjoin('shipments_journey as fsjv', function ($join) {
                $join->on('fsjv.reference_1_id', '=', 'fdnsv.delivery_note_id')
                    ->where('fsjv.id', '=', DB::connection('reports')->raw('(select min(id) from shipments_journey where shipments_journey.reference_1_id = fdnsv.delivery_note_id and shipments_journey.shipper_status_id > 5 and shipments_journey.verification = 1)'));
            })
            ->leftjoin('shipment_status as fssv', 'fssv.id', '=', 'fsjv.shipper_status_id')
            ->leftjoin('shipments_journey as lsjv', function ($join) {
                $join->on('lsjv.reference_1_id', '=', 'ldnsv.delivery_note_id')
                    ->where('lsjv.id', '=', DB::connection('reports')->raw('(select max(id) from shipments_journey  where shipments_journey.reference_1_id = ldnsv.delivery_note_id and shipments_journey.verification = 1)'));
            })
            ->leftjoin('cargo_consignment_shipments as cccc', function ($join) {
                $join->on('cccc.shipment_id', '=', 'shipments.id')
                    ->where('cccc.id', '=', DB::connection('reports')->raw('(select min(id) from cargo_consignment_shipments where cargo_consignment_shipments.shipment_id = shipments.id)'));
            })
            ->leftjoin('cargo_consignment_shipments as ccrc', function ($join) {
                $join->on('ccrc.shipment_id', '=', 'shipments.id')
                    ->where('ccrc.id', '=', DB::connection('reports')->raw('(select max(id) from cargo_consignment_shipments where cargo_consignment_shipments.shipment_id = shipments.id)'));
            })->leftjoin('cargo_consignments as ccss', 'ccss.id', '=', 'cccc.cargo_consignment_id')
            ->leftjoin('cargo_consignments as ccssr', 'ccssr.id', '=', 'ccrc.cargo_consignment_id')
            ->leftjoin('shipment_status as lssv', 'lssv.id', '=', 'lsjv.shipper_status_id')
            ->select('fatstatus.created_at as first_attempt', 'ccjr.created_at as junction', 'cc.transport_mode_vendor_id as vendor', 'fssv.name as first_verification', 'lssv.name as last_verification', 'fsjv.created_at as verification_status_date', 'lsjv.created_at as last_verification_status_date', 'dns.delivery_note_id as first_delivery_note_id', 'dnss.delivery_note_id as last_delivery_note_id', 'shipments.id as Shipment_id', 'shipments.tracking_number', 'shipments.created_at as cd', 'shipments.tracking_number as tracking_number_link', 'shipments.shipping_mode_id', 'u.id as account_no', 'u.name as shipper', 'oc.name as origin', 'dc.name as destination', 'h.name as hub', 'ss.name as current_status', 'sj.created_at as arrival_date', 'radd.created_at as reached_at_destination', 'fstatus.created_at as first_status_date', 'lstatus.created_at as last_status_date', 'fs.name as first_status', 'ls.name as last_status', 'dd.created_at as delivered_date', 'rc.created_at as return_confirm', 'rrad.created_at as return_reached_at_destination', 'rds.created_at as return_delivered_date', 'rdss.name as return_delivered_status', 'pd.created_at as payment_done_date', 'shipments.shipper_status_id', 'ret_or_del.shipper_status_id as return_check', 'lj.created_at as latest_journey_date', 'sps.name as payment_status', 'shipments.booking_type_id', 'usi.poc', 'ccss.id as cargo_number', 'ccss.created_at as cargo_date_time', 'ccssr.type as return_type', 'ccssr.id as return_cargo_number', 'ccssr.created_at as return_cargo_date_time', 'shipping_modes.mode as shipping_mode')
            ->groupBy('shipments.id');
        if (session('role_id') != 1) {
            $shipments = $shipments->whereIn('dc.hub_id', session('hubs'));
        }
        $lead_time = Datatables::of($shipments)
            ->editColumn('account_no', function ($shipments) {
                return str_pad($shipments->account_no, 6, '0', STR_PAD_LEFT);
            })
            ->editcolumn('vendor', function ($shipments) {
                if ($shipments->vendor != null) {
                    $name = DB::connection('reports')->table('transport_mode_vendors')->where('transport_mode_vendors.id', $shipments->vendor)->first();
                    return $name->name;
                } else {
                    return "-";
                }
            })
            ->editColumn('return_cargo_number', function ($shipment) {
                if ($shipment->return_type == 2) {
                    return $shipment->return_cargo_number;
                } else {
                    return "-";
                }
            })
            ->editColumn('return_cargo_date_time', function ($shipment) {
                if ($shipment->return_type == 2) {
                    return $shipment->return_cargo_date_time;
                } else {
                    return "-";
                }
            })
            ->editColumn('cargo_number', function ($shipment) {
                if ($shipment->cargo_number != null) {
                    return $shipment->cargo_number;
                } else {
                    return "-";
                }
            })
            ->editColumn('cargo_date_time', function ($shipment) {
                if ($shipment->cargo_date_time != null) {
                    return $shipment->cargo_date_time;
                } else {
                    return "-";
                }
            })
            ->editColumn('shipper', function ($shipment) {
                if ($shipment->booking_type_id == 4) {
                    return $shipment->shipper . ' (' . $shipment->poc . ')';
                } else {
                    return $shipment->shipper;
                }
            })
            ->filterColumn('u.name', function ($query, $keyword) {
                $query->where(function ($sub_query) use ($keyword) {
                    $sub_query->where('shipments.booking_type_id', '!=', 4)
                        ->where('u.name', 'like', '%' . $keyword . '%');
                })
                    ->orWhere(function ($sub_query) use ($keyword) {
                        $sub_query->where('shipments.booking_type_id', '=', 4)
                            ->where('usi.poc', 'like', '%' . $keyword . '%');
                    });
            })
            ->editColumn('tracking_number_link', function ($shipments) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->filterColumn('shipper', function ($query, $keyword) {
                return $query->where('u.name', '=', $keyword);
            })
            ->addColumn('transit_tat', function ($shipments) {
                return ($shipments->arrival_date && $shipments->reached_at_destination) ? with((new Carbon($shipments->arrival_date, 'UTC'))->diffInWeekendDays($shipments->reached_at_destination) - (new Carbon($shipments->arrival_date, 'UTC'))->diffInDaysFiltered(function (Carbon $date) {
                        $date->isSunday();
                    }, $shipments->reached_at_destination)) : '-';
            })
            ->addColumn('attempt_tat', function ($shipments) {
                return ($shipments->arrival_date && $shipments->first_status_date) ? with((new Carbon($shipments->arrival_date, 'UTC'))->diffInWeekendDays($shipments->first_status_date) - (new Carbon($shipments->arrival_date, 'UTC'))->diffInDaysFiltered(function (Carbon $date) {
                        $date->isSunday();
                    }, $shipments->first_status_date)) : '-';
            })
            ->addColumn('delivered_tat', function ($shipments) {
                return ($shipments->arrival_date && $shipments->delivered_date) ? with((new Carbon($shipments->arrival_date, 'UTC'))->diffInWeekendDays($shipments->delivered_date) - (new Carbon($shipments->arrival_date, 'UTC'))->diffInDaysFiltered(function (Carbon $date) {
                        $date->isSunday();
                    }, $shipments->delivered_date)) : '-';
            })
            ->addColumn('dispatch_tat', function ($shipments) {
                return ($shipments->reached_at_destination && $shipments->first_status_date) ? with((new Carbon($shipments->reached_at_destination, 'UTC'))->diffInWeekendDays($shipments->first_status_date) - (new Carbon($shipments->reached_at_destination, 'UTC'))->diffInDaysFiltered(function (Carbon $date) {
                        $date->isSunday();
                    }, $shipments->first_status_date)) : '-';
            })
            ->addColumn('return_transit_tat', function ($shipments) {
                return ($shipments->return_confirm && $shipments->return_reached_at_destination) ? with((new Carbon($shipments->return_confirm, 'UTC'))->diffInWeekendDays($shipments->return_reached_at_destination) - (new Carbon($shipments->return_confirm, 'UTC'))->diffInDaysFiltered(function (Carbon $date) {
                        $date->isSunday();
                    }, $shipments->return_reached_at_destination)) : '-';
            })
            ->addColumn('return_dispatch_tat', function ($shipments) {
                return ($shipments->return_delivered_date && $shipments->return_reached_at_destination) ? with((new Carbon($shipments->return_reached_at_destination, 'UTC'))->diffInWeekendDays($shipments->return_delivered_date) - (new Carbon($shipments->return_reached_at_destination, 'UTC'))->diffInDaysFiltered(function (Carbon $date) {
                        $date->isSunday();
                    }, $shipments->return_delivered_date)) : '-';
            })
            ->addColumn('return_tat', function ($shipments) {
                return ($shipments->return_confirm && $shipments->return_delivered_date) ? with((new Carbon($shipments->return_confirm, 'UTC'))->diffInWeekendDays($shipments->return_delivered_date) - (new Carbon($shipments->return_confirm, 'UTC'))->diffInDaysFiltered(function (Carbon $date) {
                        $date->isSunday();
                    }, $shipments->return_delivered_date)) : '-';
            })
            ->addColumn('payment_tat', function ($shipments) {
                $return = array(20, 42);
                if (in_array($shipments->return_check, $return)) {
                    return ($shipments->return_delivered_date && $shipments->payment_done_date) ? with((new Carbon($shipments->return_delivered_date, 'UTC'))->diffInWeekendDays($shipments->payment_done_date) - (new Carbon($shipments->return_delivered_date, 'UTC'))->diffInDaysFiltered(function (Carbon $date) {
                            $date->isSunday();
                        }, $shipments->payment_done_date)) : '-';
                } else {
                    return ($shipments->delivered_date && $shipments->payment_done_date) ? with((new Carbon($shipments->delivered_date, 'UTC'))->diffInWeekendDays($shipments->payment_done_date) - (new Carbon($shipments->delivered_date, 'UTC'))->diffInDaysFiltered(function (Carbon $date) {
                            $date->isSunday();
                        }, $shipments->payment_done_date)) : '-';
                }
            })
            ->addColumn('total_tat', function ($shipments) {
                return ($shipments->arrival_date && $shipments->latest_journey_date) ? with((new Carbon($shipments->arrival_date, 'UTC'))->diffInWeekDays($shipments->latest_journey_date) - (new Carbon($shipments->arrival_date, 'UTC'))->diffInDaysFiltered(function (Carbon $date) {
                        $date->isSunday();
                    }, $shipments->latest_journey_date)) : '-';
            });
        if ($tracking = $request->get('search_tracking_no')) {
            $lead_time->where('shipments.tracking_number', '=', $tracking);
        }
        if ($shipper = $request->get('search_shipper')) {
            $lead_time->where('u.id', '=', $shipper);
        }
        if ($origin = $request->get('search_origin')) {
            $lead_time->where('oc.id', '=', $origin);
        }
        if ($destination = $request->get('search_destination')) {
            $lead_time->where('dc.id', '=', $destination);
        }
        if ($hub = $request->get('search_hub')) {
            $lead_time->where('h.id', '=', $hub);
        }
        if ($status = $request->get('search_status')) {
            $lead_time->where('ss.id', '=', $status);
        }
        if ($mode = $request->get('search_shipping_mode')) {
            $lead_time->where('shipments.shipping_mode_id', '=', $mode);
        }
        if ($request->get('search_from') && $request->get('search_to')) {
            $from = $request->get('search_from');
            $to = $request->get('search_to');
            $lead_time->whereBetween('sj.created_at', [$from, $to]);
        }
        return $lead_time->make(true);
    }

    public function qa_index(Request $request)
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 139);
        $shipping_modes = DB::connection('reports')->table('shipping_modes')->get();
        return view('admin.reports.qa_report')->with('shipping_modes', $shipping_modes);
    }

    public function qa_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 140);
        }
        $from = $request->search_date_from;
        $to = $request->search_date_to;
        $mode = $request->search_shipping_mode;
        $qa_data = array();
        if (session('role_id') != 1) {
            $stations = DB::connection('reports')->table('cities')->whereIn('hub_id', session('hubs'))->get();
        } else {
            $stations = DB::connection('reports')->table('cities')->where('hub', 1)->select('id', 'name')->get();
        }
        foreach ($stations as $hub) {
            $qa_data[$hub->name]['cargo_pending'] = DB::connection('reports')->table('shipments')
                ->whereExists(function ($query) use ($hub) {
                    $query->from('user_shipping_infos')
                        ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                        ->whereExists(function ($sub_query) use ($hub) {
                            $sub_query->from('cities')
                                ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                                ->where('cities.id', $hub->id);
                        });
                })
                ->whereExists(function ($query) use ($hub) {
                    $query->from('cities')
                        ->where('shipments.consignee_city_id', '=', DB::raw('`cities`.`id`'))
                        ->where('hub_id', '=', $hub->id);
                })->whereExists(function ($query) use ($from, $to) {
                    $query->from('shipments_journey')
                        ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                        ->whereBetween('created_at', [$from, $to])
                        ->where('shipper_status_id', 2);
                })->where('shipments.shipping_mode_id', '=', $mode)->count();
            $qa_data[$hub->name]['cargo_resolved'] = DB::connection('reports')->table('shipments')->whereExists(function ($query) use ($hub) {
                $query->from('user_shipping_infos')
                    ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                    ->whereExists(function ($sub_query) use ($hub) {
                        $sub_query->from('cities')
                            ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                            ->where('cities.id', $hub->id);
                    });
            })
                ->whereExists(function ($query) use ($hub) {
                    $query->from('cities')
                        ->where('shipments.consignee_city_id', '=', DB::raw('`cities`.`id`'))
                        ->where('hub_id', '=', $hub->id);
                })->whereExists(function ($query) use ($from, $to) {
                    $query->from('shipments_journey')
                        ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                        ->whereBetween('created_at', [$from, $to])
                        ->where('shipper_status_id', 3);
                })->where('shipments.shipping_mode_id', '=', $mode)->count();
            $qa_data[$hub->name]['cargo_transit_pending'] = DB::connection('reports')->table('cargo_consignments')->whereBetween('created_at', [$from, $to])->where('status_id', '!=', 3)->where('origin_hub_id', $hub->id)->where('cargo_consignments.shipping_mode_id', '=', $mode)->count();
            $qa_data[$hub->name]['cargo_transit_resolved'] = DB::connection('reports')->table('cargo_consignments')->whereBetween('updated_at', [$from, $to])->where('status_id', '=', 3)->where('origin_hub_id', $hub->id)->where('cargo_consignments.shipping_mode_id', '=', $mode)->count();
            $pending_status = array(2, 4, 6, 7, 8, 9, 13, 15); //for pending deliveries
            $not_pending_status = array(1, 3, 5, 10, 11, 12, 14, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47); //for pending deliveries
            $qa_data[$hub->name]['deliveries_pending'] = DB::connection('reports')->table('shipments')->whereExists(function ($query) use ($hub) {
                $query->from('cities')
                    ->where('shipments.consignee_city_id', '=', DB::raw('`cities`.`id`'))
                    ->where('hub_id', '=', $hub->id);
            })
                ->whereNotExists(function ($query) use ($from, $to, $not_pending_status) {
                    $query->from('shipments_journey')
                        ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                        ->whereBetween('created_at', [$from, $to])
                        ->whereIn('shipper_status_id', $not_pending_status);
                })
                ->where('shipments.shipping_mode_id', '=', $mode)
                ->count();

            $qa_data[$hub->name]['deliveries_resolved'] = DB::connection('reports')->table('shipments')->whereExists(function ($query) use ($hub) {
                $query->from('cities')
                    ->where('shipments.consignee_city_id', '=', DB::raw('`cities`.`id`'))
                    ->where('hub_id', '=', $hub->id);
            })
                ->whereExists(function ($query) use ($from, $to) {
                    $query->from('shipments_journey')
                        ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                        ->whereBetween('created_at', [$from, $to])
                        ->where('shipper_status_id', 5);
                })
                ->where('shipments.shipping_mode_id', '=', $mode)->count();
            $qa_data[$hub->name]['receive_deliveries_pending'] = DB::connection('reports')->table('delivery_notes')->whereBetween('created_at', [$from, $to])->where('status', 0)->count();
            $qa_data[$hub->name]['receive_deliveries_resolved'] = DB::connection('reports')->table('delivery_notes')->whereBetween('created_at', [$from, $to])->where('status', 1)->count();
            $qa_data[$hub->name]['return_marked_pending'] = DB::connection('reports')->table('shipments')->whereExists(function ($query) use ($hub) {
                $query->from('cities')
                    ->where('shipments.consignee_city_id', '=', DB::raw('`cities`.`id`'))
                    ->where('hub_id', '=', $hub->id);
            })
                ->whereExists(function ($query) use ($from, $to) {
                    $query->from('shipments_journey')
                        ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                        ->whereBetween('created_at', [$from, $to])
                        ->where('shipper_status_id', 12);
                })->where('shipments.shipping_mode_id', '=', $mode)->count();
            $qa_data[$hub->name]['return_marked_resolved'] = DB::connection('reports')->table('shipments')->whereExists(function ($query) use ($hub) {
                $query->from('cities')
                    ->where('shipments.consignee_city_id', '=', DB::raw('`cities`.`id`'))
                    ->where('hub_id', '=', $hub->id);
            })
                ->whereExists(function ($query) use ($from, $to) {
                    $query->from('shipments_journey')
                        ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                        ->whereBetween('created_at', [$from, $to])
                        ->whereIn('shipper_status_id', [13, 20]);
                })->where('shipments.shipping_mode_id', '=', $mode)->count();
            $qa_data[$hub->name]['return_confirmed_pending'] = DB::connection('reports')->table('shipments')->whereExists(function ($query) use ($hub) {
                $query->from('cities')
                    ->where('shipments.consignee_city_id', '=', DB::raw('`cities`.`id`'))
                    ->where('hub_id', '=', $hub->id);
            })
                ->whereExists(function ($query) use ($from, $to) {
                    $query->from('shipments_journey')
                        ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                        ->whereBetween('created_at', [$from, $to])
                        ->where('shipper_status_id', 20);
                })->where('shipments.shipping_mode_id', '=', $mode)->count();
            $qa_data[$hub->name]['return_confirmed_resolved'] = DB::connection('reports')->table('shipments')->whereExists(function ($query) use ($hub) {
                $query->from('cities')
                    ->where('shipments.consignee_city_id', '=', DB::raw('`cities`.`id`'))
                    ->where('hub_id', '=', $hub->id);
            })
                ->whereExists(function ($query) use ($from, $to) {
                    $query->from('shipments_journey')
                        ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                        ->whereBetween('created_at', [$from, $to])
                        ->whereIn('shipper_status_id', [21, 23]);
                })->where('shipments.shipping_mode_id', '=', $mode)->count();
            $qa_data[$hub->name]['return_cargo_pending'] = DB::connection('reports')->table('shipments')->whereExists(function ($query) use ($hub) {
                $query->from('user_shipping_infos')
                    ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                    ->whereExists(function ($sub_query) use ($hub) {
                        $sub_query->from('cities')
                            ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                            ->where('cities.id', $hub->id);
                    });
            })
                ->whereExists(function ($query) use ($from, $to) {
                    $query->from('shipments_journey')
                        ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                        ->whereBetween('created_at', [$from, $to])
                        ->whereIn('shipper_status_id', [21, 26, 32]);
                })->where('shipments.shipping_mode_id', '=', $mode)->count();
            $qa_data[$hub->name]['return_cargo_resolved'] = DB::connection('reports')->table('shipments')->whereExists(function ($query) use ($hub) {
                $query->from('user_shipping_infos')
                    ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                    ->whereExists(function ($sub_query) use ($hub) {
                        $sub_query->from('cities')
                            ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                            ->where('cities.id', $hub->id);
                    });
            })
                ->whereExists(function ($query) use ($from, $to) {
                    $query->from('shipments_journey')
                        ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                        ->whereBetween('created_at', [$from, $to])
                        ->whereIn('shipper_status_id', [22, 27, 33]);
                })->where('shipments.shipping_mode_id', '=', $mode)->count();
            $qa_data[$hub->name]['return_delivery_pending'] = DB::connection('reports')->table('shipments')->whereExists(function ($query) use ($hub) {
                $query->from('user_shipping_infos')
                    ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                    ->whereExists(function ($sub_query) use ($hub) {
                        $sub_query->from('cities')
                            ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                            ->where('cities.id', $hub->id);
                    });
            })
                ->whereExists(function ($query) use ($from, $to) {
                    $query->from('shipments_journey')
                        ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                        ->whereBetween('created_at', [$from, $to])
                        ->whereIn('shipper_status_id', [22, 24, 27, 29, 33, 35]);
                })->where('shipments.shipping_mode_id', '=', $mode)->count();
            $qa_data[$hub->name]['return_delivery_resolved'] = DB::connection('reports')->table('shipments')->whereExists(function ($query) use ($hub) {
                $query->from('user_shipping_infos')
                    ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                    ->whereExists(function ($sub_query) use ($hub) {
                        $sub_query->from('cities')
                            ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                            ->where('cities.id', $hub->id);
                    });
            })
                ->whereExists(function ($query) use ($from, $to) {
                    $query->from('shipments_journey')
                        ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                        ->whereBetween('created_at', [$from, $to])
                        ->whereIn('shipper_status_id', [22, 24, 27, 29, 33, 35]);
                })->where('shipments.shipping_mode_id', '=', $mode)->count();
            $qa_data[$hub->name]['return_receive_pending'] = DB::connection('reports')->table('return_notes')->whereBetween('created_at', [$from, $to])->where('hub_id', $hub->id)->where('status', 0)->count();
            $qa_data[$hub->name]['return_receive_resolved'] = DB::connection('reports')->table('return_notes')->whereBetween('updated_at', [$from, $to])->where('hub_id', $hub->id)->where('status', 1)->count();
        }

        return $qa_data;
    }

    public function outstanding_shipments_index(Request $request)
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 133);
        $hubs = DB::connection('reports')->table('cities')->where('hub', 1)->select('id', 'name')->get();
        $shipping_modes = DB::connection('reports')->table('shipping_modes')->get(['id', 'mode']);
        return view('admin.reports.outstanding_shipments_report')->with(['hubs' => $hubs, 'shipping_modes' => $shipping_modes]);
    }

    public function outstanding_shipments_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 134);
        }
        if (Auth::id() == 3) {
            $connection = 'mysql';
        } else {
            $connection = 'reports';
        }

        $count = DB::connection($connection)->table('delivery_note_shipments');

        if (session('role_id') != 1 || $request->get('hub') || $request->get('search_shipping_mode') || !in_array(session('id'), session('sale_users_bypass'))) {
            $count = $count->join('shipments as s', 'delivery_note_shipments.shipment_id', '=', 's.id');
        }

        if (session('role_id') != 1 || $request->get('hub') || !in_array(session('id'), session('sale_users_bypass'))) {
            $count = $count->join('cities as dc', 's.consignee_city_id', '=', 'dc.id')->whereIn('dc.hub_id', session('hubs'));
        }

        if ($recovery_status = $request->get('search_recovery_status')) {
            if ($recovery_status == 0) {
                $count = $count->whereIn('delivery_note_shipments.status', [4, 5, 6, 7, 11]);
            } else if ($recovery_status == 1) {
                $count = $count->whereIn('delivery_note_shipments.status', [4, 5, 6]);
            } else if ($recovery_status == 7) {
                $count = $count->where('delivery_note_shipments.status', '=', 7);
            } else if ($recovery_status == 11) {
                $count = $count->where('delivery_note_shipments.status', '=', 11);
            }
        } else {
            $count = $count->whereIn('delivery_note_shipments.status', [4, 5, 6, 7, 11]);
        }

        if ($mode = $request->get('search_shipping_mode')) {
            $count = $count->where('s.booking_type_id', '=', $mode);
        }
        if ($hub = $request->get('hub')) {
            $count = $count->join('cities as hc', 'dc.hub_id', '=', 'hc.id')->where('hc.id', '=', $hub);
        }

        if ($request->get('delivery_date_from') || $request->get('delivery_date_to')) {
            $count = $count->join('shipments_journey as sjd', function ($join) use ($connection) {
                $join->on('sjd.shipment_id', '=', 'delivery_note_shipments.shipment_id')
                    ->where('sjd.id', '=', DB::connection($connection)->raw('(SELECT MAX(id) FROM shipments_journey WHERE shipments_journey.shipment_id = delivery_note_shipments.shipment_id AND shipments_journey.shipper_status_id IN (14, 16, 30, 36))'));
            });
        }

        if ($delivery_date_from = $request->get('delivery_date_from')) {
            $count = $count->where('sjd.created_at', '>=', $delivery_date_from);
        }

        if ($delivery_date_to = $request->get('delivery_date_to')) {
            $count = $count->where('sjd.created_at', '<', Carbon::parse($delivery_date_to)->addDay()->toDateTimeString());
        }

        $count = $count->count();

        $shipments = DB::connection($connection)->table('delivery_note_shipments')->join('shipments as s', 'delivery_note_shipments.shipment_id', '=', 's.id')
            ->join('cities as dc', 's.consignee_city_id', '=', 'dc.id')
            ->join('delivery_notes as delivery_note', 'delivery_note_shipments.delivery_note_id', '=', 'delivery_note.id')
            ->join('riders as rider', 'delivery_note.rider_id', '=', 'rider.id')
            ->join('cities as hc', 'dc.hub_id', '=', 'hc.id')
            ->join('users as u', 's.user_id', '=', 'u.id')
            ->join('booking_types as bt', 's.booking_type_id', '=', 'bt.id')
            ->join('user_shipping_infos AS usi', 's.pickup_address_id', '=', 'usi.id')
            ->leftjoin('shipment_payment_status as sps', 's.payment_status_id', '=', 'sps.id')
            ->leftjoin('shipments_journey as sj', function ($join) use ($connection) {
                $join->on('sj.shipment_id', '=', 's.id')
                    ->where('sj.id', '=', DB::connection($connection)->raw('(SELECT MAX(id) FROM shipments_journey WHERE shipment_id = s.id)'));
            })
            ->leftjoin('shipments_journey as sod', function ($join) use ($connection) {
                $join->on('sod.shipment_id', '=', 's.id')
                    ->where('sod.id', '=', DB::connection($connection)->raw('(SELECT MAX(id) FROM shipments_journey WHERE shipment_id = s.id and shipments_journey.verification = 0 and shipments_journey.reference_1_id = delivery_note.id)'));
            })
            ->leftjoin('shipments_journey as svd', function ($join) use ($connection) {
                $join->on('svd.shipment_id', '=', 's.id')
                    ->where('svd.id', '=', DB::connection($connection)->raw('(SELECT MAX(id) FROM shipments_journey WHERE shipment_id = s.id and shipments_journey.verification = 1 and shipments_journey.reference_1_id = delivery_note.id and shipments_journey.shipper_status_id != 5)'));
            })
            ->join('shipments_journey as sjd', function ($join) use ($connection) {
                $join->on('sjd.shipment_id', '=', 's.id')
                    ->where('sjd.id', '=', DB::connection($connection)->raw('(SELECT MAX(id) FROM shipments_journey WHERE shipments_journey.shipment_id = s.id AND shipments_journey.shipper_status_id IN (14, 16, 30, 36))'));
            })
            ->join('shipment_status as ss', 'sj.shipper_status_id', '=', 'ss.id')
            ->leftjoin('delivery_note_station_deposit_notes as dnsdn', 'delivery_note_shipments.delivery_note_id', '=', 'dnsdn.delivery_note_id')
            ->select('s.id', 's.tracking_number', 's.consignee_name as consignee', 's.consignee_address as address', 'dc.name as destination', 'hc.name as hub', 'u.name as shipper', 'bt.booking_type as service_type', 's.amount', 's.amount as sum_amount', 'ss.name as current_status', 'sod.created_at as operation_status_date', 'svd.created_at as verification_status_date', 'sj.remarks', 'delivery_note_shipments.delivery_note_id as dncc', 'delivery_note_shipments.delivery_note_id as dncc_link', 'dnsdn.station_deposit_note_id as sdn', 'dnsdn.station_deposit_note_id as sdn_link', 'sjd.created_at as delivered_at', 'delivery_note_shipments.status as recovery_status', 'sps.name as payment_status', 'rider.name as rider_name', 's.booking_type_id', 'usi.poc', 'u.id as account_no')
            ->whereIn('delivery_note_shipments.status', [4, 5, 6, 7, 8, 11])
            ->where('s.booking_type_id', '!=', 4)
            ->whereIn('sj.shipper_status_id', [14, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 45, 46]);
        if (session('role_id') != 1 || in_array(session('id'), session('sale_users_bypass'))) {
            $shipments = $shipments->whereIn('dc.hub_id', session('hubs'));
        }
        $datatables = Datatables::of($shipments)
            ->setTotalRecords($count)
            ->setRowAttr([
                'data-dncc' => function ($shipments) {
                    return $shipments->dncc;
                },
                'data-sdn' => function ($shipments) {
                    return $shipments->sdn;
                },
            ])
            ->editColumn('tracking_number_link', function ($shipments) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->editColumn('amount', function ($shipment) {
                return number_format($shipment->amount);
            })
            ->editColumn('dncc', function ($shipments) {
                if ($shipments->dncc) {
                    return str_pad($shipments->dncc, 6, '0', STR_PAD_LEFT);
                } else {
                    return '';
                }
            })
            ->editColumn('account_no', function ($shipments) {
                return str_pad($shipments->account_no, 6, '0', STR_PAD_LEFT);
            })
            ->editColumn('shipper', function ($shipment) {
                if ($shipment->booking_type_id == 4) {
                    return $shipment->shipper . ' (' . $shipment->poc . ')';
                } else {
                    return $shipment->shipper;
                }
            })
            ->filterColumn('u.name', function ($query, $keyword) {
                $query->where(function ($sub_query) use ($keyword) {
                    $sub_query->where('shipments.booking_type_id', '!=', 4)
                        ->where('u.name', 'like', '%' . $keyword . '%');
                })
                    ->orWhere(function ($sub_query) use ($keyword) {
                        $sub_query->where('shipments.booking_type_id', '=', 4)
                            ->where('usi.poc', 'like', '%' . $keyword . '%');
                    });
            })
            ->editColumn('dncc_link', function ($shipments) {
                return '<button class="btn btn-sm btn-outline-info align-middle print"><i class="la la-lg la-print align-middle"></i> <span class="align-middle">' . str_pad($shipments->dncc, 6, '0', STR_PAD_LEFT) . '</span></button>';
            })
            ->editColumn('sdn_link', function ($shipments) {
                return '<button class="btn btn-sm btn-outline-info align-middle print"><i class="la la-lg la-print align-middle"></i> <span class="align-middle">' . str_pad($shipments->sdn, 6, '0', STR_PAD_LEFT) . '</span></button>';
            })
            ->editColumn('sdn', function ($shipments) {
                if ($shipments->sdn) {
                    return str_pad($shipments->sdn, 6, '0', STR_PAD_LEFT);
                } else {
                    return '';
                }
            })
            ->addColumn('aging', function ($shipment) {
                $updated_at = Carbon::parse($shipment->operation_status_date)->startOfDay();

                $now = Carbon::now()->startOfDay();

                return $updated_at->diffInDays($now) . 'd';
            })
            ->filterColumn('aging', function ($query, $keyword) {
                $search = str_replace('d', '', str_replace(' ', '', $keyword));

                if (filter_var($search, FILTER_VALIDATE_INT)) {
                    $date = Carbon::now();

                    $date = $date->subDays($search);

                    $query->whereDate('sj.updated_at', '>=', $date->toDateString());
                } else {
                    $query->whereRaw($search);
                }
            })
            ->editColumn('recovery_status', function ($shipment) {
                if (in_array($shipment->recovery_status, [4, 5, 6])) {
                    return "Outstanding";
                } else if ($shipment->recovery_status == 7) {
                    return "Resolved";
                } else if ($shipment->recovery_status == 8) {
                    return "Payment Adjusted";
                } else if ($shipment->recovery_status == 11) {
                    return "Revert Requested";
                }
            });
        if ($recovery_status = $request->get('search_recovery_status')) {
            if ($recovery_status == 0) {
                $datatables->whereIn('delivery_note_shipments.status', [4, 5, 6, 7, 11]);
            } else if ($recovery_status == 1) {
                $datatables->whereIn('delivery_note_shipments.status', [4, 5, 6]);
            } else if ($recovery_status == 7) {
                $datatables->where('delivery_note_shipments.status', '=', 7);
            } else if ($recovery_status == 11) {
                $datatables->where('delivery_note_shipments.status', '=', 11);
            }
        }

        if ($mode = $request->get('search_shipping_mode')) {
            $datatables->where('s.booking_type_id', '=', $mode);
        }
        if ($hub = $request->get('hub')) {
            $datatables->where('hc.id', '=', $hub);
        }

        if ($delivery_date_from = $request->get('delivery_date_from')) {
            $datatables->where('sjd.created_at', '>=', $delivery_date_from);
        }

        if ($delivery_date_to = $request->get('delivery_date_to')) {
            $datatables->where('sjd.created_at', '<', Carbon::parse($delivery_date_to)->addDay()->toDateTimeString());
        }

        return $datatables->make(true);
    }

    public function daily_pickup_sales_index(Request $request)
    {

        ActivityTrailController::createActivityTrailLog(Auth::id(), 145);
        if (session('role_id') == 1) {
            $cities = DB::connection('reports')->table('cities')->select('id', 'name')->where('pickup', 1)->get();
        } else {
            $cities = DB::connection('reports')->table('cities')->select('id', 'name')->where('pickup', 1)->whereIn('hub_id', session('hubs'))->get();
        }
        $sales = '';
        if (session('department_id') == 7) {
            $sales = DB::connection('reports')->table('admins')->whereExists(function ($query) {
                $query->from('admin_roles')
                    ->where('admins.role_id', '=', DB::raw('`admin_roles`.`id`'))
                    ->where('department_id', '=', 7);
            })->where('status', 1)->select('id', 'name')->get();
        }
        $shipping_mode = ShippingMode::select('id', 'mode')->get();
        return view('admin.reports.daily_pickup_sales_report')->with(['cities' => $cities, 'sales_persons' => $sales, 'shipping_mode' => $shipping_mode]);
    }

    public function daily_pickup_sales_export_to_excel(Request $request)
    {

        ActivityTrailController::createActivityTrailLog(Auth::id(), 146);
        $response = self::daily_pickup_sales_report_create($request->city, $request->date, $request->sales_person, $request->sales_tagging);

//         if($response['status']){
        return $response;
//         }

    }

    function array_sort($array, $on, $order = SORT_DESC)
    {

        $new_array = array();
        $sortable_array = array();

        if (count($array) > 0) {
            foreach ($array as $k => $v) {
                if (is_array($v)) {
                    foreach ($v as $k2 => $v2) {
                        if ($k2 == $on) {
                            $sortable_array[$k] = $v2;
                        }
                    }
                } else {
                    $sortable_array[$k] = $v;
                }
            }

            switch ($order) {
                case SORT_ASC:
                    asort($sortable_array);
                    break;
                case SORT_DESC:
                    arsort($sortable_array);
                    break;
            }

            foreach ($sortable_array as $k => $v) {
                $new_array[$k] = $array[$k];
            }
        }

        return $new_array;
    }

    static public function daily_pickup_sales_report_create($search_city = NULL, $date, $sales_person = NULL, $sales_tagging = FALSE)
    {

        $date_from = Carbon::createFromFormat("Y-m-d H:i:s", $date)->format('Y-m-d 06:00A');
        $next_day = Carbon::parse($date)->addDay(1);
        $date_to = Carbon::createFromFormat("Y-m-d H:i:s", $next_day)->format('Y-m-d 05:59A');
        $only_date = Carbon::parse($date)->toDateString();
        $hubs = array();
        $city = array();
        if ($sales_tagging == TRUE) {

            if (session('role_id') == 1) {
                if ($search_city != null) {

                    $city = DB::connection('reports')->table('cities')->where('id', $search_city)->select('id', 'name')->first();
                    $search_city_hub = $city->id;
                    $hubs[] = $city;
                } else {
                    $search_city_hub = '';
                    $hubs = DB::connection('reports')->table('cities')->where('pickup', 1)->select('id', 'name')->get();
                }
            } else {
                if ($search_city != null) {

                    $city = DB::connection('reports')->table('cities')->where('id', $search_city)->select('id', 'name')->first();
                    $search_city_hub = $city->id;
                    $hubs[] = $city;
                } else {
                    $search_city_hub = '';
                    $hubs = DB::connection('reports')->table('cities')->whereIn('hub_id', session('hubs'))->where('pickup', 1)->select('id', 'name')->get();
                }
            }
        } else {
            $search_city_hub = '';
            $hubs = DB::connection('reports')->table('cities')->where('pickup', 1)->select('id', 'name')->get();
        }
        $details = array();
        $shipping_wise_details = array();
        $sorted_details_array = array();
        $sorted_shipper_array = array();
        $details_shipper = array();
        $shippers = array();
        $details['header'] = ['S. No.', 'Origin ' . $only_date, 'No. of Parcels Booked', 'No of Parcels Received', 'Revenue without GST', 'Avg/Parcel Revenue', 'Actual Weight', 'Avg. Actual Weight/Parcel', 'Avg. Revenue On Actual Weight', 'Chargeable Weight', 'Avg. Chargeable Weight/Parcel', 'Avg. Revenue On Chargeable Weight', 'Collection Amount', 'Avg. Amount Collection', '% Rev. on Amount Collection'];
        $sort_support_array = array();
        $total_booked = 0;
        $total_received = 0;
        $total_cod_collection = 0;
        $total_actual_weight = 0;
        $total_avg_actual_weight = 0;
        $total_avg_rev_actual_weight = 0;
        $total_chargeable_weight = 0;
        $total_avg_chargeable_weight = 0;
        $total_avg_rev_chargeable_weight = 0;
        $total_revenue_wo_gst = 0;
        $total_avg_revenue = 0;
        $total_avg_cash_collection = 0;
        $total_rev_on_cash_collection = 0;
        $serial_number_hubs = 0;
        $booked = 0;
        $received = 0;
        $revenue_wo_gst = 0;
        $cod_collection = 0;
        $actual_weight = 0;
        $chargeable_weight = 0;

        foreach ($hubs as $hub) {
            if ($sales_tagging == TRUE) {

                if (session('department_id') != 7) {
                    $booked = DB::connection('reports')->table('shipments')
                    ->join('user_shipping_infos as usi', 'usi.id', 'shipments.pickup_address_id')
                    ->where('shipments.packaging_material_request', 0)
                    ->where('shipments.user_id', '!=', 1690)
                    ->whereBetween('shipments.created_at', [$date_from, $date_to])
                    ->where('usi.city_id', $hub->id)
                    ->count();

                    $received = DB::connection('reports')->table('shipments_journey')
                    ->join('shipments as s', 's.id', 'shipments_journey.shipment_id')
                    ->join('user_shipping_infos as usi', 'usi.id', 's.pickup_address_id')
                    ->where('s.packaging_material_request', 0)
                    ->where('s.user_id', '!=', 1690)
                    ->whereBetween('shipments_journey.created_at', [$date_from, $date_to])
                    ->where('shipments_journey.shipper_status_id', 2)
                    ->where('usi.city_id', $hub->id)
                    ->count();

                    if($received > 0){
                        $shipments_data = array();

                        $shipments_data = DB::connection('reports')->table('shipments_journey')
                        ->join('shipments as s', 's.id', 'shipments_journey.shipment_id')
                        ->join('user_shipping_infos as usi', 'usi.id', 's.pickup_address_id')
                        ->where('s.packaging_material_request', 0)
                        ->where('s.user_id', '!=', 1690)
                        ->whereBetween('shipments_journey.created_at', [$date_from, $date_to])
                        ->where('shipments_journey.shipper_status_id', 2)
                        ->where('usi.city_id', $hub->id)
                        ->select(DB::connection('reports')->raw('SUM(IFNULL(weight_charges,0) + IFNULL(cash_handling_charges,0) + IFNULL(insurance_charges,0) + IFNULL(return_charges,0) + IFNULL(fuel_surcharge,0) + IFNULL(replacement_charges,0) + IFNULL(try_and_buy_charges,0)) as revenue_wo_gst'), DB::connection('reports')->raw('SUM(amount) as cod_collection'), DB::connection('reports')->raw('SUM(actual_weight) as actual_weight'), DB::connection('reports')->raw('SUM(chargeable_weight) as chargeable_weight'))->first();

                        $revenue_wo_gst = $shipments_data->revenue_wo_gst;
                        $cod_collection = $shipments_data->cod_collection;
                        $actual_weight = $shipments_data->actual_weight;
                        $chargeable_weight = $shipments_data->chargeable_weight;
                    }
                } else {
                    if (!in_array(session('id'), session('sale_users_bypass'))) {
                        $booked = DB::connection('reports')->table('shipments')
                        ->join('user_shipping_infos as usi', 'usi.id', 'shipments.pickup_address_id')
                        ->where('shipments.packaging_material_request', 0)
                        ->where('shipments.user_id', '!=', 1690)
                        ->whereBetween('shipments.created_at', [$date_from, $date_to])
                        ->where('usi.city_id', $hub->id)
                        ->whereIn('shipments.user_id', session('tagged_shippers'))
                        ->count();

                        $received = DB::connection('reports')->table('shipments_journey')
                        ->join('shipments as s', 's.id', 'shipments_journey.shipment_id')
                        ->join('user_shipping_infos as usi', 'usi.id', 's.pickup_address_id')
                        ->where('s.packaging_material_request', 0)
                        ->where('s.user_id', '!=', 1690)
                        ->whereBetween('shipments_journey.created_at', [$date_from, $date_to])
                        ->where('shipments_journey.shipper_status_id', 2)
                        ->where('usi.city_id', $hub->id)
                        ->whereIn('s.user_id', session('tagged_shippers'))
                        ->count();

                        if($received > 0){
                            $shipments_data = array();

                            $shipments_data = DB::connection('reports')->table('shipments_journey')
                            ->join('shipments as s', 's.id', 'shipments_journey.shipment_id')
                            ->join('user_shipping_infos as usi', 'usi.id', 's.pickup_address_id')
                            ->where('s.packaging_material_request', 0)
                            ->where('s.user_id', '!=', 1690)
                            ->whereBetween('shipments_journey.created_at', [$date_from, $date_to])
                            ->where('shipments_journey.shipper_status_id', 2)
                            ->where('usi.city_id', $hub->id)
                            ->whereIn('s.user_id', session('tagged_shippers'))
                            ->select(DB::connection('reports')->raw('SUM(IFNULL(weight_charges,0) + IFNULL(cash_handling_charges,0) + IFNULL(insurance_charges,0) + IFNULL(return_charges,0) + IFNULL(fuel_surcharge,0) + IFNULL(replacement_charges,0) + IFNULL(try_and_buy_charges,0)) as revenue_wo_gst'), DB::connection('reports')->raw('SUM(amount) as cod_collection'), DB::connection('reports')->raw('SUM(actual_weight) as actual_weight'), DB::connection('reports')->raw('SUM(chargeable_weight) as chargeable_weight'))->first();

                            $revenue_wo_gst = $shipments_data->revenue_wo_gst;
                            $cod_collection = $shipments_data->cod_collection;
                            $actual_weight = $shipments_data->actual_weight;
                            $chargeable_weight = $shipments_data->chargeable_weight;
                        }
                    } else {
                        if ($sales_person != null) {
                            $tagged_shippers = DB::connection('reports')->table('sale_person_tags')->where('admin_id', $sales_person)->select('user_id')->pluck('user_id')->toArray();

                            $assigned_admins = DB::connection('reports')->table('multiple_sale_leads')->leftjoin('multiple_sale_taggings as mst', 'mst.lead_id', '=', 'multiple_sale_leads.id')
                                ->leftjoin('sale_person_tags as spt', 'spt.admin_id', '=', 'mst.admin_id')
                                ->where('multiple_sale_leads.admin_id', $sales_person)
                                ->where('spt.status', 0)
                                ->whereNotNull('spt.user_id')->select('spt.user_id')->pluck('spt.user_id')->toArray();
                            if ($assigned_admins) {
                                $tagged_shippers = array_merge($tagged_shippers, $assigned_admins);
                            }

                            $booked = DB::connection('reports')->table('shipments')
                            ->join('user_shipping_infos as usi', 'usi.id', 'shipments.pickup_address_id')
                            ->where('shipments.packaging_material_request', 0)
                            ->where('shipments.user_id', '!=', 1690)
                            ->whereBetween('shipments.created_at', [$date_from, $date_to])
                            ->where('usi.city_id', $hub->id)
                            ->whereIn('shipments.user_id', $tagged_shippers)
                            ->count();

                            $received = DB::connection('reports')->table('shipments_journey')
                            ->join('shipments as s', 's.id', 'shipments_journey.shipment_id')
                            ->join('user_shipping_infos as usi', 'usi.id', 's.pickup_address_id')
                            ->where('s.packaging_material_request', 0)
                            ->where('s.user_id', '!=', 1690)
                            ->whereBetween('shipments_journey.created_at', [$date_from, $date_to])
                            ->where('shipments_journey.shipper_status_id', 2)
                            ->where('usi.city_id', $hub->id)
                            ->whereIn('s.user_id', $tagged_shippers)
                            ->count();

                            if($received > 0){
                                $shipments_data = array();

                                $shipments_data = DB::connection('reports')->table('shipments_journey')
                                ->join('shipments as s', 's.id', 'shipments_journey.shipment_id')
                                ->join('user_shipping_infos as usi', 'usi.id', 's.pickup_address_id')
                                ->where('s.packaging_material_request', 0)
                                ->where('s.user_id', '!=', 1690)
                                ->whereBetween('shipments_journey.created_at', [$date_from, $date_to])
                                ->where('shipments_journey.shipper_status_id', 2)
                                ->where('usi.city_id', $hub->id)
                                ->whereIn('s.user_id', $tagged_shippers)
                                ->select(DB::connection('reports')->raw('SUM(IFNULL(weight_charges,0) + IFNULL(cash_handling_charges,0) + IFNULL(insurance_charges,0) + IFNULL(return_charges,0) + IFNULL(fuel_surcharge,0) + IFNULL(replacement_charges,0) + IFNULL(try_and_buy_charges,0)) as revenue_wo_gst'), DB::connection('reports')->raw('SUM(amount) as cod_collection'), DB::connection('reports')->raw('SUM(actual_weight) as actual_weight'), DB::connection('reports')->raw('SUM(chargeable_weight) as chargeable_weight'))->first();

                                $revenue_wo_gst = $shipments_data->revenue_wo_gst;
                                $cod_collection = $shipments_data->cod_collection;
                                $actual_weight = $shipments_data->actual_weight;
                                $chargeable_weight = $shipments_data->chargeable_weight;
                            }
                        } else {
                            $booked = DB::connection('reports')->table('shipments')
                            ->join('user_shipping_infos as usi', 'usi.id', 'shipments.pickup_address_id')
                            ->where('shipments.packaging_material_request', 0)
                            ->where('shipments.user_id', '!=', 1690)
                            ->whereBetween('shipments.created_at', [$date_from, $date_to])
                            ->where('usi.city_id', $hub->id)
                            ->whereIn('shipments.user_id', session('tagged_shippers'))
                            ->count();

                            $received = DB::connection('reports')->table('shipments_journey')
                            ->join('shipments as s', 's.id', 'shipments_journey.shipment_id')
                            ->join('user_shipping_infos as usi', 'usi.id', 's.pickup_address_id')
                            ->where('s.packaging_material_request', 0)
                            ->where('s.user_id', '!=', 1690)
                            ->whereBetween('shipments_journey.created_at', [$date_from, $date_to])
                            ->where('shipments_journey.shipper_status_id', 2)
                            ->where('usi.city_id', $hub->id)
                            ->whereIn('s.user_id', session('tagged_shippers'))
                            ->count();

                            if($received > 0){
                                $shipments_data = array();

                                $shipments_data = DB::connection('reports')->table('shipments_journey')
                                ->join('shipments as s', 's.id', 'shipments_journey.shipment_id')
                                ->join('user_shipping_infos as usi', 'usi.id', 's.pickup_address_id')
                                ->where('s.packaging_material_request', 0)
                                ->where('s.user_id', '!=', 1690)
                                ->whereBetween('shipments_journey.created_at', [$date_from, $date_to])
                                ->where('shipments_journey.shipper_status_id', 2)
                                ->where('usi.city_id', $hub->id)
                                ->whereIn('s.user_id', session('tagged_shippers'))
                                ->select(DB::connection('reports')->raw('SUM(IFNULL(weight_charges,0) + IFNULL(cash_handling_charges,0) + IFNULL(insurance_charges,0) + IFNULL(return_charges,0) + IFNULL(fuel_surcharge,0) + IFNULL(replacement_charges,0) + IFNULL(try_and_buy_charges,0)) as revenue_wo_gst'), DB::connection('reports')->raw('SUM(amount) as cod_collection'), DB::connection('reports')->raw('SUM(actual_weight) as actual_weight'), DB::connection('reports')->raw('SUM(chargeable_weight) as chargeable_weight'))->first();

                                $revenue_wo_gst = $shipments_data->revenue_wo_gst;
                                $cod_collection = $shipments_data->cod_collection;
                                $actual_weight = $shipments_data->actual_weight;
                                $chargeable_weight = $shipments_data->chargeable_weight;
                            }
                        }
                    }
                }

            } else {
                $booked = DB::connection('reports')->table('shipments')
                ->join('user_shipping_infos as usi', 'usi.id', 'shipments.pickup_address_id')
                ->where('shipments.packaging_material_request', 0)
                ->where('shipments.user_id', '!=', 1690)
                ->whereBetween('shipments.created_at', [$date_from, $date_to])
                ->where('usi.city_id', $hub->id)
                ->count();

                $received = DB::connection('reports')->table('shipments_journey')
                ->join('shipments as s', 's.id', 'shipments_journey.shipment_id')
                ->join('user_shipping_infos as usi', 'usi.id', 's.pickup_address_id')
                ->where('s.packaging_material_request', 0)
                ->where('s.user_id', '!=', 1690)
                ->whereBetween('shipments_journey.created_at', [$date_from, $date_to])
                ->where('shipments_journey.shipper_status_id', 2)
                ->where('usi.city_id', $hub->id)
                ->count();
            }
            if ($received > 0) {
                $shipments_data = array();
                $shipments_data = DB::connection('reports')->table('shipments_journey')
                ->join('shipments as s', 's.id', 'shipments_journey.shipment_id')
                ->join('user_shipping_infos as usi', 'usi.id', 's.pickup_address_id')
                ->where('s.packaging_material_request', 0)
                ->where('s.user_id', '!=', 1690)
                ->whereBetween('shipments_journey.created_at', [$date_from, $date_to])
                ->where('shipments_journey.shipper_status_id', 2)
                ->where('usi.city_id', $hub->id)
                ->select(DB::connection('reports')->raw('SUM(IFNULL(weight_charges,0) + IFNULL(cash_handling_charges,0) + IFNULL(insurance_charges,0) + IFNULL(return_charges,0) + IFNULL(fuel_surcharge,0) + IFNULL(replacement_charges,0) + IFNULL(try_and_buy_charges,0)) as revenue_wo_gst'), DB::connection('reports')->raw('SUM(amount) as cod_collection'), DB::connection('reports')->raw('SUM(actual_weight) as actual_weight'), DB::connection('reports')->raw('SUM(chargeable_weight) as chargeable_weight'))->first();

                $revenue_wo_gst = $shipments_data->revenue_wo_gst;
                $cod_collection = $shipments_data->cod_collection;
                $actual_weight = $shipments_data->actual_weight;
                $chargeable_weight = $shipments_data->chargeable_weight;
                $row = array();
                $avg_revenue = ($received != 0) ? $revenue_wo_gst / $received : 0;
                $avg_cash_collection = ($received != 0) ? $cod_collection / $received : 0;
                $rev_on_cash_collection = (($avg_cash_collection != 0) ? $avg_revenue / $avg_cash_collection : 0) * 100;
                //changes add columns
                $avg_actual_weight = ($received != 0) ? $actual_weight / $received : 0;
                $avg_rev_actual_weight = ($actual_weight != 0 && $revenue_wo_gst != 0) ? $revenue_wo_gst / $actual_weight : 0;
                $avg_chargeable_weight = ($received != 0) ? $chargeable_weight / $received : 0;
                $avg_rev_chargeable_weight = ($chargeable_weight != 0 && $revenue_wo_gst != 0) ? $revenue_wo_gst / $chargeable_weight : 0;

                //end changes
                $row['serials'] = $serial_number_hubs;
                $row['hub'] = $hub->name;
                $row['booked'] = number_format($booked);
                $row['received'] = number_format($received);
                $row['revenue_wo_gst'] = number_format($revenue_wo_gst);
                $avg_rev = round($avg_revenue);
                $row['average_revenue'] = number_format($avg_rev);
                $row['actual_weight'] = number_format($actual_weight);
                $string_avg_actual_weight = (string)$avg_actual_weight;
                $row['avg_actual_weight'] = number_format((float)$string_avg_actual_weight, 2, '.', '');
                $row['avg_rev_actual_weight'] = round($avg_rev_actual_weight);

                $row['chargeable_weight'] = number_format($chargeable_weight);
                $string_avg_chargeable_weight = (string)$avg_chargeable_weight;
                $row['avg_chargeable_weight'] = number_format((float)$string_avg_chargeable_weight, 2, '.', '');
                $row['avg_rev_chargeable_weight'] = round($avg_rev_chargeable_weight);
                $row['cod_collection'] = number_format($cod_collection);

                $avg_cc = round($avg_cash_collection);
                $row['average_cash_collection'] = number_format($avg_cc);
                $rev_occ = round($rev_on_cash_collection);
                $row['revenue_cash_collection'] = number_format($rev_occ) . '%';
                $sort_support_array[] = $received;

                $sorted_details_array[] = $row;
                $total_booked += $booked;
                $total_received += $received;
                $total_revenue_wo_gst += $revenue_wo_gst;
                $total_cod_collection += $cod_collection;
                $total_actual_weight += $actual_weight;
//                $total_avg_actual_weight += $avg_actual_weight;
//                $total_avg_rev_actual_weight += $avg_rev_actual_weight;
                $total_chargeable_weight += $chargeable_weight;
//                $total_avg_chargeable_weight += $avg_chargeable_weight;
//                $total_avg_rev_chargeable_weight += $avg_rev_chargeable_weight;
//                $total_avg_revenue += $avg_rev;

//                $total_rev_on_cash_collection += $rev_occ;

            }

        }
        $total_avg_revenue = ($total_received != 0) ? $total_revenue_wo_gst / $total_received : 0;
        $total_avg_actual_weight = ($total_received != 0) ? $total_actual_weight / $total_received : 0;
        $total_avg_rev_actual_weight = ($total_actual_weight != 0) ? $total_revenue_wo_gst / $total_actual_weight : 0;
        $total_avg_chargeable_weight = ($total_received != 0) ? $total_chargeable_weight / $total_received : 0;
        $total_avg_rev_chargeable_weight = ($total_chargeable_weight != 0) ? $total_revenue_wo_gst / $total_chargeable_weight : 0;
        $total_avg_cash_collection = ($total_received != 0) ? $total_cod_collection / $total_received : 0;
        $total_rev_on_cash_collection = ($total_cod_collection != 0) ? $total_revenue_wo_gst / $total_cod_collection : 0;
        $total_rev_on_cash_collection = $total_rev_on_cash_collection * 100;
        array_multisort($sort_support_array, SORT_DESC, $sorted_details_array);
        $counter = 1;
        foreach ($sorted_details_array as $item) {
            $item['serials'] = $counter;
            $details[] = $item;
            $counter++;
        }
        $details[] = ['Grand Total', 'Origin ' . $only_date, number_format($total_booked), number_format($total_received), number_format($total_revenue_wo_gst), number_format($total_avg_revenue), number_format($total_actual_weight), number_format((float)$total_avg_actual_weight, 2, '.', ''), round($total_avg_rev_actual_weight), number_format($total_chargeable_weight), number_format((float)$total_avg_chargeable_weight, 2, '.', ''), round($total_avg_rev_chargeable_weight), number_format($total_cod_collection), number_format($total_avg_cash_collection), number_format($total_rev_on_cash_collection) . '%'];

        $details_shipper['header'] = ['S. No.', 'Origin', 'Sales Person', 'Shipper Name(s) (Account No(s))', 'No. of Parcels Booked', 'No of Parcels Received', 'Revenue without GST', 'Avg/Parcel Revenue', 'Actual Weight', 'Avg. Actual Weight/Parcel', 'Avg. Revenue On Actual Weight', 'Chargeable Weight', 'Avg. Chargeable Weight/Parcel', 'Avg. Revenue On Chargeable Weight', 'Collection Amount', 'Avg. Amount Collection', '% Rev. on Amount Collection'];

//        $details_shipper['header'] = ['S. No.','DSR '.$only_date, 'No. of Parcels Booked','No of Parcels Received','Revenue without GST','Collection Amount','Actual Weight','Chargeable Weight','Avg/Parcel Revenue','Avg. Amount Collection','% Rev. on Amount Collection'];
        $serial_number_shippers = 0;

        if ($sales_tagging == TRUE) {
            if ($search_city != null) {
                $pickup_request_shippers = DB::connection('reports')->table('shipments_journey')
                ->join('shipments as s', 's.id', 'shipments_journey.shipment_id')
                ->join('user_shipping_infos as usi', 'usi.id', 's.pickup_address_id')
                ->where('s.packaging_material_request', 0)
                ->where('s.user_id', '!=', 1690)
                ->whereBetween('shipments_journey.created_at', [$date_from, $date_to])
                ->where('shipments_journey.shipper_status_id', 2)
                ->where('usi.city_id', $search_city_hub);

                if ($pickup_request_shippers->exists()) {
                    $pickup_request_shippers_ids = $pickup_request_shippers->pluck('s.user_id')->toArray();
                    $pickup_request_shippers_ids = array_unique($pickup_request_shippers_ids);

                    if (session('department_id') != 7) {
                        $shippers[$search_city_hub] = DB::connection('reports')->table('users')->select('id', 'name')->whereIn('id', $pickup_request_shippers_ids)->whereIn('status', [3, 4])->get();
                    } else {
                        if (!in_array(session('id'), session('sale_users_bypass'))) {
                            $shippers[$search_city_hub] = DB::connection('reports')->table('users')->select('id', 'name')->whereIn('id', $pickup_request_shippers_ids)->whereIn('status', [3, 4])->whereIn('id', session('tagged_shippers'))->get();
                        } else {
                            $shippers[$search_city_hub] = DB::connection('reports')->table('users')->select('id', 'name')->whereIn('id', $pickup_request_shippers_ids)->whereIn('status', [3, 4])->get();
                        }
                    }

                }
            } else {
                foreach ($hubs as $hub) {
                    $pickup_request_shippers = DB::connection('reports')->table('shipments_journey')
                    ->join('shipments as s', 's.id', 'shipments_journey.shipment_id')
                    ->join('user_shipping_infos as usi', 'usi.id', 's.pickup_address_id')
                    ->where('s.packaging_material_request', 0)
                    ->where('s.user_id', '!=', 1690)
                    ->whereBetween('shipments_journey.created_at', [$date_from, $date_to])
                    ->where('shipments_journey.shipper_status_id', 2)
                    ->where('usi.city_id', $hub->id);

                    if ($pickup_request_shippers->exists()) {
                        $pickup_request_shippers_ids = $pickup_request_shippers->pluck('s.user_id')->toArray();

                        $pickup_request_shippers_ids = array_unique($pickup_request_shippers_ids);

                        if (session('department_id') != 7) {


                            $shippers[$hub->id] = DB::connection('reports')->table('users')->select('id', 'name')->whereIn('id', $pickup_request_shippers_ids)->whereIn('status', [3, 4])->get();
//                        }
                        } else {
                            if (!in_array(session('id'), session('sale_users_bypass'))) {

                                $shippers[$hub->id] = DB::connection('reports')->table('users')->select('id', 'name')->whereIn('id', $pickup_request_shippers_ids)->whereIn('status', [3, 4])->whereIn('id', session('tagged_shippers'))->get();
//                            }


                            } else {
                                if ($sales_person != null) {

                                    $shippers[$hub->id] = DB::connection('reports')->table('users')->whereExists(function ($query) use ($sales_person) {
                                        $query->from('sale_person_tags')
                                            ->where('users.id', '=', DB::raw('`sale_person_tags`.`user_id`'))
                                            ->where('admin_id', '=', $sales_person);
                                    })->whereIn('id', $pickup_request_shippers_ids)->whereIn('status', [3, 4])->select('id', 'name')->get();
//                                }

                                } else {
                                    $shippers[$hub->id] = DB::connection('reports')->table('users')->select('id', 'name')->whereIn('id', $pickup_request_shippers_ids)->whereIn('status', [3, 4])->get();

                                }

                            }
                        }
                    }

                }

            }
        } else {
            foreach ($hubs as $hub) {
                $pickup_request_shippers = DB::connection('reports')->table('shipments_journey')
                ->join('shipments as s', 's.id', 'shipments_journey.shipment_id')
                ->join('user_shipping_infos as usi', 'usi.id', 's.pickup_address_id')
                ->where('s.packaging_material_request', 0)
                ->where('s.user_id', '!=', 1690)
                ->whereBetween('shipments_journey.created_at', [$date_from, $date_to])
                ->where('shipments_journey.shipper_status_id', 2)
                ->where('usi.city_id', $hub->id);

                if ($pickup_request_shippers->exists()) {
                    $pickup_request_shippers_ids = $pickup_request_shippers->pluck('s.user_id')->toArray();

                    $pickup_request_shippers_ids = array_unique($pickup_request_shippers_ids);

                    $shippers[$hub->id] = DB::connection('reports')->table('users')->select('id', 'name')->whereIn('id', $pickup_request_shippers_ids)->whereIn('status', [3, 4])->get();
                }

            }
        }

        $shipper_sort_support_array = array();
        $total_shipper_booked = 0;
        $total_shipper_received = 0;
        $total_shipper_cod_collection = 0;
        $total_shipper_actual_weight = 0;
        $total_shipper_avg_actual_weight = 0;
        $total_shipper_avg_rev_actual_weight = 0;
        $total_shipper_chargeable_weight = 0;
        $total_shipper_avg_chargeable_weight = 0;
        $total_shipper_avg_rev_chargeable_weight = 0;
        $total_shipper_revenue_wo_gst = 0;
        $total_shipper_avg_revenue = 0;
        $total_shipper_avg_cash_collection = 0;
        $total_shipper_rev_on_cash_collection = 0;
//        $shipper_sales_person = '';


        if (count($shippers) > 0) {
            if ($search_city != null) {
                $origin_name = DB::connection('reports')->table('cities')->where('id', $search_city_hub)->select('name')->first();
                $origin_name = $origin_name->name;

            }
            foreach ($shippers as $origin => $shipper_row) {
                foreach ($shipper_row as $shipper) {

                    $shipper_booked = 0;
                    $shipper_received = 0;
                    $shipper_rev_wo_gst = 0;
                    $shipper_cod = 0;
                    $shipper_actual_weight = 0;
                    $shipper_chargeable_weight = 0;
                    if ($search_city == null) {
                        $origin_name = DB::connection('reports')->table('cities')->where('id', $origin)->select('name')->first();
                        $origin_name = $origin_name->name;
                    }

                    $shipper_sales_person_name = '';
                    $shipper_sales_person = SalePersonTag::where('user_id', $shipper->id)->where('status', 0);
                    if ($shipper_sales_person->exists()) {
                        $shipper_sales_person = $shipper_sales_person->first();
                        $shipper_sales_person_name = Admin::find($shipper_sales_person->admin_id)->name;
                    }
                    if ($sales_tagging == TRUE) {
                        if ($search_city != null) {
                            $shipper_booked = DB::connection('reports')->table('shipments')
                            ->join('user_shipping_infos as usi', 'usi.id', 'shipments.pickup_address_id')
                            ->where('shipments.packaging_material_request', 0)
                            ->whereBetween('shipments.created_at', [$date_from, $date_to])
                            ->where('usi.city_id', $search_city_hub)
                            ->where('shipments.user_id', $shipper->id)
                            ->count();

                            $shipper_received = DB::connection('reports')->table('shipments_journey')
                            ->join('shipments as s', 's.id', 'shipments_journey.shipment_id')
                            ->join('user_shipping_infos as usi', 'usi.id', 's.pickup_address_id')
                            ->where('s.packaging_material_request', 0)
                            ->whereBetween('shipments_journey.created_at', [$date_from, $date_to])
                            ->where('shipments_journey.shipper_status_id', 2)
                            ->where('usi.city_id', $search_city_hub)
                            ->where('s.user_id', $shipper->id)
                            ->count();

                            if ($shipper_received > 0) {
                                $shipment_data = array();

                                $shipment_data = DB::connection('reports')->table('shipments_journey')
                                ->join('shipments as s', 's.id', 'shipments_journey.shipment_id')
                                ->join('user_shipping_infos as usi', 'usi.id', 's.pickup_address_id')
                                ->where('s.packaging_material_request', 0)
                                ->whereBetween('shipments_journey.created_at', [$date_from, $date_to])
                                ->where('shipments_journey.shipper_status_id', 2)
                                ->where('usi.city_id', $search_city_hub)
                                ->where('s.user_id', $shipper->id)
                                ->select(DB::connection('reports')->raw('SUM(IFNULL(weight_charges,0) + IFNULL(cash_handling_charges,0) + IFNULL(insurance_charges,0) + IFNULL(return_charges,0) + IFNULL(fuel_surcharge,0) + IFNULL(replacement_charges,0) + IFNULL(try_and_buy_charges,0)) as revenue_wo_gst'), DB::connection('reports')->raw('SUM(amount) as shipper_cod'), DB::connection('reports')->raw('SUM(actual_weight) as shipper_actual_weight'), DB::connection('reports')->raw('SUM(chargeable_weight) as shipper_chargeable_weight'))->first();

                                $shipper_rev_wo_gst = $shipment_data->revenue_wo_gst;
                                $shipper_cod = $shipment_data->shipper_cod;
                                $shipper_actual_weight = $shipment_data->shipper_actual_weight;
                                $shipper_chargeable_weight = $shipment_data->shipper_chargeable_weight;
                            }
                        } else {
                            $shipper_booked = DB::connection('reports')->table('shipments')
                            ->join('user_shipping_infos as usi', 'usi.id', 'shipments.pickup_address_id')
                            ->where('shipments.packaging_material_request', 0)
                            ->whereBetween('shipments.created_at', [$date_from, $date_to])
                            ->where('usi.city_id', $origin)
                            ->where('s.user_id', $shipper->id)
                            ->count();

                            $shipper_received = DB::connection('reports')->table('shipments_journey')
                            ->join('shipments as s', 's.id', 'shipments_journey.shipment_id')
                            ->join('user_shipping_infos as usi', 'usi.id', 's.pickup_address_id')
                            ->where('s.packaging_material_request', 0)
                            ->whereBetween('shipments_journey.created_at', [$date_from, $date_to])
                            ->where('shipments_journey.shipper_status_id', 2)
                            ->where('usi.city_id', $origin)
                            ->where('s.user_id', $shipper->id)
                            ->count();

                            if ($shipper_received > 0) {
                                $shipment_data = array();

                                $shipment_data = DB::connection('reports')->table('shipments_journey')
                                ->join('shipments as s', 's.id', 'shipments_journey.shipment_id')
                                ->join('user_shipping_infos as usi', 'usi.id', 's.pickup_address_id')
                                ->where('s.packaging_material_request', 0)
                                ->whereBetween('shipments_journey.created_at', [$date_from, $date_to])
                                ->where('shipments_journey.shipper_status_id', 2)
                                ->where('usi.city_id', $origin)
                                ->where('s.user_id', $shipper->id)
                                ->select(DB::connection('reports')->raw('SUM(IFNULL(weight_charges,0) + IFNULL(cash_handling_charges,0) + IFNULL(insurance_charges,0) + IFNULL(return_charges,0) + IFNULL(fuel_surcharge,0) + IFNULL(replacement_charges,0) + IFNULL(try_and_buy_charges,0)) as revenue_wo_gst'), DB::connection('reports')->raw('SUM(amount) as shipper_cod'), DB::connection('reports')->raw('SUM(actual_weight) as shipper_actual_weight'), DB::connection('reports')->raw('SUM(chargeable_weight) as shipper_chargeable_weight'))->first();

                                $shipper_rev_wo_gst = $shipment_data->revenue_wo_gst;
                                $shipper_cod = $shipment_data->shipper_cod;
                                $shipper_actual_weight = $shipment_data->shipper_actual_weight;
                                $shipper_chargeable_weight = $shipment_data->shipper_chargeable_weight;
                            }
                        }

                    } else {
                        $shipper_booked = DB::connection('reports')->table('shipments')
                        ->join('user_shipping_infos as usi', 'usi.id', 'shipments.pickup_address_id')
                        ->where('shipments.packaging_material_request', 0)
                        ->whereBetween('shipments.created_at', [$date_from, $date_to])
                        ->where('usi.city_id', $origin)
                        ->where('s.user_id', $shipper->id)
                        ->count();

                        $shipper_received = DB::connection('reports')->table('shipments_journey')
                        ->join('shipments as s', 's.id', 'shipments_journey.shipment_id')
                        ->join('user_shipping_infos as usi', 'usi.id', 's.pickup_address_id')
                        ->where('s.packaging_material_request', 0)
                        ->whereBetween('shipments_journey.created_at', [$date_from, $date_to])
                        ->where('shipments_journey.shipper_status_id', 2)
                        ->where('usi.city_id', $origin)
                        ->where('s.user_id', $shipper->id)
                        ->count();

                        if ($shipper_received > 0) {
                            $shipment_data = array();

                            $shipment_data = DB::connection('reports')->table('shipments_journey')
                            ->join('shipments as s', 's.id', 'shipments_journey.shipment_id')
                            ->join('user_shipping_infos as usi', 'usi.id', 's.pickup_address_id')
                            ->where('s.packaging_material_request', 0)
                            ->whereBetween('shipments_journey.created_at', [$date_from, $date_to])
                            ->where('shipments_journey.shipper_status_id', 2)
                            ->where('usi.city_id', $origin)
                            ->where('s.user_id', $shipper->id)
                            ->select(DB::connection('reports')->raw('SUM(IFNULL(weight_charges,0) + IFNULL(cash_handling_charges,0) + IFNULL(insurance_charges,0) + IFNULL(return_charges,0) + IFNULL(fuel_surcharge,0) + IFNULL(replacement_charges,0) + IFNULL(try_and_buy_charges,0)) as revenue_wo_gst'), DB::connection('reports')->raw('SUM(amount) as shipper_cod'), DB::connection('reports')->raw('SUM(actual_weight) as shipper_actual_weight'), DB::connection('reports')->raw('SUM(chargeable_weight) as shipper_chargeable_weight'))->first();

                            $shipper_rev_wo_gst = $shipment_data->revenue_wo_gst;
                            $shipper_cod = $shipment_data->shipper_cod;
                            $shipper_actual_weight = $shipment_data->shipper_actual_weight;
                            $shipper_chargeable_weight = $shipment_data->shipper_chargeable_weight;
                        }
                    }

                    $shipper_avg_revenue = ($shipper_received != 0) ? $shipper_rev_wo_gst / $shipper_received : 0;
                    $shipper_avg_cash_collection = ($shipper_received != 0) ? $shipper_cod / $shipper_received : 0;
                    $shipper_rev_on_cash_collection = (($shipper_avg_cash_collection != 0) ? $shipper_avg_revenue / $shipper_avg_cash_collection : 0) * 100;

                    //changes add columns
                    $shipper_avg_actual_weight = ($shipper_received != 0) ? $shipper_actual_weight / $shipper_received : 0;
                    $shipper_avg_rev_actual_weight = ($shipper_actual_weight != 0 && $shipper_rev_wo_gst != 0) ? $shipper_rev_wo_gst / $shipper_actual_weight : 0;
                    $shipper_avg_chargeable_weight = ($shipper_received != 0) ? $shipper_chargeable_weight / $shipper_received : 0;
                    $shipper_avg_rev_chargeable_weight = ($shipper_chargeable_weight != 0 && $shipper_rev_wo_gst != 0) ? $shipper_rev_wo_gst / $shipper_chargeable_weight : 0;

                    //end
                    $shipper_row = array();
                    $shipper_row['shipper_serial'] = $serial_number_shippers;
                    $shipper_row['origin_name'] = $origin_name;
                    $shipper_row['shipper_sale_person'] = $shipper_sales_person_name;
                    $shipper_row['name'] = $shipper->name . ' (' . str_pad($shipper->id, 6, '0', STR_PAD_LEFT) . ')';
                    $shipper_row['shipper_booked'] = number_format($shipper_booked);
                    $shipper_row['shipper_received'] = number_format($shipper_received);
                    $shipper_row['shipper_rev_wo_gst'] = number_format($shipper_rev_wo_gst);
                    $s_avg_revenue = round($shipper_avg_revenue);
                    $shipper_row['shipper_avg_revenue'] = number_format($s_avg_revenue);
                    $shipper_row['shipper_actual_weight'] = number_format($shipper_actual_weight);
                    $shipper_row['shipper_avg_actual_weight'] = number_format((float)$shipper_avg_actual_weight, 2, '.', '');
                    $shipper_row['$shipper_avg_rev_actual_weight'] = round($shipper_avg_rev_actual_weight);
                    $shipper_row['shipper_chargeable_weight'] = number_format($shipper_chargeable_weight);
                    $shipper_row['shipper_avg_chargeable_weight'] = number_format((float)$shipper_avg_chargeable_weight, 2, '.', '');
                    $shipper_row['shipper_avg_rev_chargeable_weight'] = round($shipper_avg_rev_chargeable_weight);
                    $shipper_row['shipper_cod'] = number_format($shipper_cod);

                    $avg_cash_coll = round($shipper_avg_cash_collection);
                    $shipper_row['shipper_avg_cc'] = number_format($avg_cash_coll);
                    $avg_rev_cc = round($shipper_rev_on_cash_collection);
                    $shipper_row['shipper_rcc'] = number_format($avg_rev_cc) . '%';

                    $sorted_shipper_array[] = $shipper_row;
                    $shipper_sort_support_array[] = $shipper_received;
                    $total_shipper_booked += $shipper_booked;
                    $total_shipper_received += $shipper_received;
                    $total_shipper_cod_collection += $shipper_cod;
                    $total_shipper_revenue_wo_gst += $shipper_rev_wo_gst;
                    $total_shipper_actual_weight += $shipper_actual_weight;
//                $total_shipper_avg_actual_weight += $shipper_avg_actual_weight;
//                $total_shipper_avg_rev_actual_weight += $shipper_avg_rev_actual_weight;
                    $total_shipper_chargeable_weight += $shipper_chargeable_weight;
//                $total_shipper_avg_chargeable_weight += $shipper_avg_chargeable_weight;
//                $total_shipper_avg_rev_chargeable_weight += $shipper_avg_rev_chargeable_weight;
//                $total_shipper_avg_revenue += $s_avg_revenue;

//                $total_shipper_rev_on_cash_collection += $avg_rev_cc;


//                $serial_number_shippers++;
                }
            }
        }

        $total_shipper_avg_revenue = ($total_shipper_received != 0) ? $total_shipper_revenue_wo_gst / $total_shipper_received : 0;
        $total_shipper_avg_actual_weight = ($total_shipper_received != 0) ? $total_shipper_actual_weight / $total_shipper_received : 0;
        $total_shipper_avg_rev_actual_weight = ($total_shipper_actual_weight != 0) ? $total_shipper_revenue_wo_gst / $total_shipper_actual_weight : 0;
        $total_shipper_avg_chargeable_weight = ($total_shipper_received != 0) ? $total_shipper_chargeable_weight / $total_shipper_received : 0;
        $total_shipper_avg_rev_chargeable_weight = ($total_shipper_chargeable_weight != 0) ? $total_shipper_revenue_wo_gst / $total_shipper_chargeable_weight : 0;
        $total_shipper_avg_cash_collection = ($total_shipper_received != 0) ? $total_shipper_cod_collection / $total_shipper_received : 0;

        $total_shipper_rev_on_cash_collection = ($total_shipper_cod_collection != 0) ? $total_shipper_revenue_wo_gst / $total_shipper_cod_collection : 0;
        $total_shipper_rev_on_cash_collection = $total_shipper_rev_on_cash_collection * 100;
        array_multisort($shipper_sort_support_array, SORT_DESC, $sorted_shipper_array);
        $shipper_counter = 1;
        foreach ($sorted_shipper_array as $shipper) {
            $shipper['shipper_serial'] = $shipper_counter;
            $details_shipper[] = $shipper;
            $shipper_counter++;
        }
        $details_shipper[] = ['Grand Total', '', '', 'DSR ' . $only_date, number_format($total_shipper_booked), number_format($total_shipper_received), number_format($total_shipper_revenue_wo_gst), number_format($total_shipper_avg_revenue), number_format($total_shipper_actual_weight), number_format((float)$total_shipper_avg_actual_weight, 2, '.', ''), round($total_shipper_avg_rev_actual_weight), number_format($total_shipper_chargeable_weight), number_format((float)$total_shipper_avg_chargeable_weight, 2, '.', ''), round($total_shipper_avg_rev_chargeable_weight), number_format($total_shipper_cod_collection), number_format($total_shipper_avg_cash_collection), number_format($total_shipper_rev_on_cash_collection) . '%'];

        //shipping_mode_wise
        $shipping_mode_wise_header['header'] = ['S. No.', 'Shipping Mode', 'No. of Parcels Booked', 'No of Parcels Received', 'Revenue without GST', 'Avg/Parcel Revenue', 'Actual Weight', 'Avg Actual Weight/Parcel', 'Avg Revenue on Actual Weight', 'Chargeable Weight', 'Avg. Chargeable Weight/Parcel', 'Avg. Revenue On Chargeable Weight', 'Collection Amount', 'Avg. Amount Collection', '% Rev. on Amount Collection'];

        $shipping_mode_wise_details = self::daily_pickup_sales_shipping_mode_wise($date_from, $date_to, $search_city, $sales_tagging, $sales_person);

        $total_shipping_booked = 0;
        $total_shipping_received = 0;
        $total_shipping_revenue_wo_gst = 0;
        $total_shipping_avg_parcel_revenue = 0;
        $total_shipping_actual_weight = 0;
        $total_shipping_avg_actual_weight = 0;
        $total_shipping_avg_rev_actual_weight = 0;
        $total_shipping_chargeable_weight = 0;
        $total_shipping_avg_chargeable_weight = 0;
        $total_shipping_avg_rev_chargeable_weight = 0;
        $total_shipping_collection_amount = 0;
        $total_shipping_avg_amount_collection = 0;
        $total_shipping_rev_amount_collection = 0;
        $total_avg_revenue = 0;
        $total_avg_actual_weight = 0;
        $total_avg_rev_actual_weight = 0;
        $total_avg_chargeable_weight = 0;
        $shipping_mode_wise_data = array();
        foreach ($shipping_mode_wise_details as $shipping_mode_total) {
            $total_shipping_booked += $shipping_mode_total['booked'];
            $total_shipping_received += $shipping_mode_total['received'];
            $total_shipping_revenue_wo_gst += $shipping_mode_total['revenue_wo_gst'];
            $total_shipping_avg_parcel_revenue += $shipping_mode_total['avg_parcel_rev'];
            $total_shipping_actual_weight += $shipping_mode_total['actual_weight'];
            $total_shipping_avg_actual_weight += $shipping_mode_total['avg_actual_weight'];
            $total_shipping_avg_rev_actual_weight += $shipping_mode_total['avg_rev_actual_weight'];
            $total_shipping_chargeable_weight += $shipping_mode_total['chargeable_weight'];
            $total_shipping_avg_chargeable_weight += $shipping_mode_total['avg_chargeable_weight'];
            $total_shipping_avg_rev_chargeable_weight += $shipping_mode_total['avg_rev_chargeable_weight'];
            $total_shipping_collection_amount += $shipping_mode_total['collection_amount'];
            $total_shipping_avg_amount_collection += $shipping_mode_total['avg_amount_collection'];
            $total_shipping_rev_amount_collection += $shipping_mode_total['revenue_amount_collection'];

            $shipping_mode_wise_data[] = [$shipping_mode_total['serial'], $shipping_mode_total['mode'], number_format(round($shipping_mode_total['booked'])), $shipping_mode_total['received'], number_format($shipping_mode_total['revenue_wo_gst']), number_format($shipping_mode_total['avg_parcel_rev']), number_format($shipping_mode_total['actual_weight']), number_format($shipping_mode_total['avg_actual_weight']), round($shipping_mode_total['avg_rev_actual_weight']), number_format($shipping_mode_total['chargeable_weight']), number_format($shipping_mode_total['avg_chargeable_weight']), round($shipping_mode_total['avg_rev_chargeable_weight']), $shipping_mode_total['collection_amount'], $shipping_mode_total['avg_amount_collection'], round($shipping_mode_total['revenue_amount_collection']) . '%'];
        }

        $total_shipping_avg_parcel_revenue = ($total_shipping_received != 0) ? $total_shipping_revenue_wo_gst / $total_shipping_received : 0;
        $total_avg_actual_weight = ($total_shipping_received != 0) ? $total_shipping_actual_weight / $total_shipping_received : 0;
        $total_avg_rev_actual_weight = ($total_shipping_actual_weight != 0) ? $total_shipping_revenue_wo_gst / $total_shipping_actual_weight : 0;
        $total_avg_chargeable_weight = ($total_shipping_received != 0) ? $total_shipping_chargeable_weight / $total_shipping_received : 0;
        $total_avg_rev_chargeable_weight = ($total_shipping_chargeable_weight != 0) ? $total_shipping_revenue_wo_gst / $total_shipping_chargeable_weight : 0;
        $total_avg_cash_collection = ($total_shipping_received != 0) ? $total_shipping_collection_amount / $total_shipping_received : 0;
        $total_rev_on_cash_collection = ($total_shipping_collection_amount != 0) ? $total_shipping_revenue_wo_gst / $total_shipping_collection_amount : 0;
        $total_rev_on_cash_collection = $total_rev_on_cash_collection * 100;

        $shipping_mode_wise_footer[] = ['Grand Total.', ' ', number_format(round($total_shipping_booked)), $total_shipping_received, number_format(round($total_shipping_revenue_wo_gst)), number_format($total_shipping_avg_parcel_revenue), number_format($total_shipping_actual_weight), number_format($total_avg_actual_weight), number_format($total_avg_rev_actual_weight), number_format($total_shipping_chargeable_weight), number_format($total_avg_chargeable_weight), number_format($total_avg_rev_chargeable_weight), $total_shipping_collection_amount, number_format($total_avg_cash_collection), number_format($total_rev_on_cash_collection) . '%'];

        $shipping_mode_wise_details = array_merge($shipping_mode_wise_header, $shipping_mode_wise_data, $shipping_mode_wise_footer);

        $spreadsheet = new Spreadsheet();
        $cell_st = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => array(
                'allBorders' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => array('argb' => '000000'),
                )
            ),
        ];
        $total_cell_st = [
            'font' => ['bold' => true],
            'borders' => ['bottom' => ['style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM]],
            'borders' => array(
                'allBorders' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => array('argb' => '000000'),
                ),
            )
        ];
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->getDefaultColumnDimension()->setWidth(20);
        $sheet->getStyle('D3:R3')
            ->getFill()
            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('CECECE');
        $sheet->getStyle('D3:R3')->getAlignment()->setWrapText(true);
        $sheet->getStyle('D3:R3')->applyFromArray($cell_st);
        $sheet->fromArray($shipping_mode_wise_details, NULL, 'D3', true);
        $sheet->getStyle('D8:R8')->applyFromArray($total_cell_st);
        $count_shipping_mode = count($shipping_mode_wise_details);
        $count_shipping_mode += 7;
        /*  $count_hubs = count($hubs);
          $count_hub_rows = count($details);
          $count_hub_rows += 2;

          $count_hubs += 3;
          $total_shipper_rows = count($details_shipper);
          $total_shipper_rows += $count_hubs;
          $total_shipper_rows = $total_shipper_rows - 1;*/

        //details
        $total_style_cell = "D$count_shipping_mode" . ":R" . $count_shipping_mode;
        $sheet->getStyle($total_style_cell)->applyFromArray($total_cell_st);
        $sheet->getStyle($total_style_cell)->getAlignment()->setWrapText(true);
        $sheet->getStyle($total_style_cell)->applyFromArray($cell_st);
        $sheet->getStyle($total_style_cell)
            ->getFill()
            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('CECECE');
        $shipper_cell = 'D' . $count_shipping_mode; //D13
        $shipper_last_cell = 'R' . $count_shipping_mode; //R13
        $sheet->fromArray($details, NULL, $shipper_cell, true);
        $count_details = count($details);
        $total_hub = $count_shipping_mode + $count_details - 1;
        $total_style_cell = "D$total_hub" . ":R" . $total_hub;
        $sheet->getStyle($total_style_cell)->applyFromArray($total_cell_st);
        $count_details = $count_details + $count_shipping_mode + 4;

        //shipper_details
        $total_style_cell = "D$count_details" . ":T" . $count_details;
        $sheet->getStyle($total_style_cell)->applyFromArray($total_cell_st);
        $sheet->getStyle($total_style_cell)->applyFromArray($cell_st);
        $sheet->getStyle($total_style_cell)->getAlignment()->setWrapText(true);


        $sheet->getStyle($total_style_cell)
            ->getFill()
            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('CECECE');
        $details_shipper_cell = 'D' . $count_details;
        $details_shipper_last_cell = 'R' . $count_details;
        // $sheet->fromArray($details_shipper,NULL,'D15',true);
        $sheet->fromArray($details_shipper, NULL, $details_shipper_cell, true);
        $total_shipper_count = count($details_shipper);
        $total_shipper = $total_shipper_count + $count_details - 1;
        $total_style_cell = "D$total_shipper" . ":T" . $total_shipper;
        $sheet->getStyle($total_style_cell)->applyFromArray($total_cell_st);
        $sheet->setTitle('Daily Pickup Sales Report');

        /* $hub_all_rows = "D3".":R".$count_hub_rows;

         $sheet->getStyle($hub_all_rows)->applyFromArray($cell_st);

         $shipper_style_cell = "D$count_hubs".":T".$count_hubs;
         $shipper_all_rows = "D$count_hubs".":T".$total_shipper_rows;*/

        /* $total_style_cell = "D$count_hub_rows".":R".$count_hub_rows;
         $total_shipper_style_cell = "D$total_shipper_rows".":T".$total_shipper_rows;
         $sheet->getStyle($shipper_style_cell)
             ->getFill()
             ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
             ->getStartColor()
             ->setRGB('CECECE');
 //        $sheet->getStyle($total_style_cell)->applyFromArray($total_cell_st);
         $sheet->getStyle($shipper_all_rows)->applyFromArray($cell_st);*/

        /* $sheet->getStyle($shipper_style_cell)->getAlignment()->setWrapText(true);*/
//        $sheet->getStyle($total_shipper_style_cell)->applyFromArray($total_cell_st);

        /* $set_shipper_actual_number_format = 'K'.$count_hubs.':K'.$total_shipper_rows;
         $set_shipper_chargeable_number_format = 'N'.$count_hubs.':N'.$total_shipper_rows;*/
        /* $sheet->getStyle($set_shipper_actual_number_format)->getNumberFormat()->setFormatCode('0.00');
         $sheet->getStyle($set_shipper_chargeable_number_format)->getNumberFormat()->setFormatCode('0.00');*/


        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="daily_pickup_sales_report.xlsx"');
        header('Cache-Control: max-age=0');
        $date_file_name = Carbon::parse($date)->format('Y_m_d');
        $time_string = Carbon::now()->toTimeString();
        $time_string = Carbon::parse($time_string)->format('h_i_s');
        $city_name = '';
        $file_name_without_path = '';
        if ($search_city != null) {
            $city_name = $city->name;
        }
        if ($sales_tagging == TRUE) {
            $file_name_without_path = "reports/daily_pickup_sales_report_" . $date_file_name . '_' . $city_name . $time_string . ".xlsx";
            $file_name = public_path() . '/' . $file_name_without_path;
        } else {
            if ($search_city != null) {
                $file_name_without_path = "reports/daily_pickup_sales_report_" . $date_file_name . '_' . $city_name . $time_string . ".xlsx";
                $file_name = public_path() . "/reports/daily_pickup_sales_report_" . $date_file_name . '_' . $city_name . $time_string . ".xlsx";
            } else {
                $file_name_without_path = "reports/daily_pickup_sales_report_" . $date_file_name . '_' . $time_string . ".xlsx";
                $file_name = public_path() . "/reports/daily_pickup_sales_report_" . $date_file_name . '_' . $time_string . ".xlsx";
            }

        }
        $writer->save($file_name);

        if ($sales_tagging == TRUE) {
            return ['status' => 1, 'file_name' => $file_name_without_path];
        } else {
            return url('/') . '/' . $file_name_without_path;
        }
    }
//    public function daily_pickup_sales_download(Request $request){
////        $file_name = "/reports/daily_pickup_sales_report_".Auth::id().".xlsx";
//        $file = $request->file;
//        $headers = array('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',);
//        return Response::download($file, 'daily_pickup_sales_report.xlsx',$headers);
//    }

    public function customer_sales_index(Request $request)
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 147);
        if (session('role_id') == 1) {
            $shippers = DB::connection('reports')->table('users')->where('status', '>=', 3)->get();
            $hubs = DB::connection('reports')->table('cities')->select('id', 'name')->where('hub', 1)->get();
        } else {
            $hubs = DB::connection('reports')->table('cities')->select('id', 'name')->whereIn('id', session('hubs'))->get();
            if (session('department_id') != 7) {
                $shippers = DB::connection('reports')->table('users')->where('status', 3)->whereExists(function ($query) {
                    $query->from('cities')
                        ->where('users.city_id', '=', DB::raw('`cities`.`id`'))
                        ->whereIn('hub_id', session('hubs'));
                })->get();

            } else {
                if (!in_array(session('id'), session('sale_users_bypass'))) {
                    $shippers = DB::connection('reports')->table('users')->where('status', 3)->whereIn('id', session('tagged_shippers'))->whereExists(function ($query) {
                        $query->from('cities')
                            ->where('users.city_id', '=', DB::raw('`cities`.`id`'))
                            ->whereIn('hub_id', session('hubs'));
                    })->get();
                } else {
                    $shippers = DB::connection('reports')->table('users')->where('status', 3)->whereExists(function ($query) {
                        $query->from('cities')
                            ->where('users.city_id', '=', DB::raw('`cities`.`id`'))
                            ->whereIn('hub_id', session('hubs'));
                    })->get();

                }
            }
        }
        return view('admin.reports.customer_sales_report')->with(['hubs' => $hubs, 'shippers' => $shippers]);
    }

    private function get_months($date1, $date2)
    {
        $time1 = strtotime($date1);
        $time2 = strtotime($date2);
        $my = date('mY', $time2);

//        $months = array(date('F', $time1));
        $f = '';

        while ($time1 < $time2) {
            $time1 = strtotime((date('Y-m-d', $time1) . ' +15days'));
            if (date('F', $time1) != $f) {
                $f = date('F', $time1);
                if (date('mY', $time1) != $my && ($time1 < $time2))
                    $months[] = date('F Y', $time1);
            }
        }

        $months[] = date('F Y', $time2);
        return $months;
    }

    public function customer_sales_export_to_excel(Request $request)
    {
        $hub = $request->city;
        $shipper_filter = $request->shipper;
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $months_array = array();
        $months_array = $this->get_months($from_date, $to_date);
        $from_time = Carbon::createFromTime(7, 59, 59)->format('H:i:s');
        $to_time = Carbon::createFromTime(8, 0, 0)->format('H:i:s');

        if (session('role_id') == 1) {
            if ($hub != null) {
                $city = DB::connection('reports')->table('cities')->where('id', $hub)->select('id', 'name')->get();
            } else {
                $city = DB::connection('reports')->table('cities')->where('hub', 1)->select('id', 'name')->get();
            }
        } else {
            if ($hub != null) {
                $city = DB::connection('reports')->table('cities')->where('id', $hub)->select('id', 'name')->get();
            } else {
                $city = DB::connection('reports')->table('cities')->whereIn('id', session('hubs'))->select('id', 'name')->get();
            }

        }


        $details = array();
        $shippers = array();
//        unset($months_array[0]);

        $details['header'] = ['Origin', 'Client Account No.', 'Client Name'];
        $details['subheader'] = ['Parcels', 'Weight', 'Collection Amount', 'Revenue'];

        foreach ($months_array as $month) {
            $details['months'][] = $month;
        }
        $hubs = array();
        $users = array();
        foreach ($city as $c) {
            $details['hubs'][$c->id] = $c->name;
            if ($shipper_filter != '') {
                $shippers = DB::connection('reports')->table('users')->where('id', $shipper_filter)->whereExists(function ($query) use ($c) {
                    $query->from('cities')
                        ->where('users.city_id', '=', DB::raw('`cities`.`id`'))
                        ->where('hub_id', '=', $c->id);
                });
            } else {
                if (session('department_id') != 7) {
                    $shippers = DB::connection('reports')->table('users')->whereExists(function ($query) use ($c) {
                        $query->from('cities')
                            ->where('users.city_id', '=', DB::raw('`cities`.`id`'))
                            ->where('hub_id', '=', $c->id);
                    });
                } else {
                    if (!in_array(session('id'), session('sale_users_bypass'))) {
                        $shippers = DB::connection('reports')->table('users')->whereExists(function ($query) use ($c) {
                            $query->from('cities')
                                ->where('users.city_id', '=', DB::raw('`cities`.`id`'))
                                ->where('hub_id', '=', $c->id);
                        })->whereIn('users.id', session('tagged_shippers'));
                    } else {
                        $shippers = DB::connection('reports')->table('users')->whereExists(function ($query) use ($c) {
                            $query->from('cities')
                                ->where('users.city_id', '=', DB::raw('`cities`.`id`'))
                                ->where('hub_id', '=', $c->id);
                        });
                    }
                }

            }

            if ($shippers->exists()) {
                foreach ($shippers->get() as $key => $s) {
                    $details['shipper'][$c->id][$s->id] = $s->name;
                    foreach ($months_array as $month) {

                        $firstDayOfMonth = Carbon::parse($month)->firstOfMonth()->format('Y-m-d 05:59A');
                        $firstDayOfNextMonth = Carbon::parse($month)->addMonth()->format('Y-m-d 06:00A');

                        $details['parcels'][$s->id][$month] = DB::connection('reports')->table('shipments')->where('user_id', $s->id)
                            ->whereExists(function ($query) use ($firstDayOfMonth, $firstDayOfNextMonth) {
                                $query->from('shipments_journey')
                                    ->where('shipments.id', '=', DB::raw('`shipments_journey`.`shipment_id`'))
                                    ->whereBetween('created_at', [$firstDayOfMonth, $firstDayOfNextMonth])
                                    ->where('shipper_status_id', 2);
                            })->count();
                        $details['weight'][$s->id][$month] = DB::connection('reports')->table('shipments')->where('user_id', $s->id)->whereExists(function ($query) use ($firstDayOfMonth, $firstDayOfNextMonth) {
                            $query->from('shipments_journey')
                                ->where('shipments.id', '=', DB::raw('`shipments_journey`.`shipment_id`'))
                                ->whereBetween('created_at', [$firstDayOfMonth, $firstDayOfNextMonth])
                                ->where('shipper_status_id', 2);
                        })->sum('actual_weight');
                        $details['amount'][$s->id][$month] = number_format(DB::connection('reports')->table('shipments')->where('user_id', $s->id)->whereExists(function ($query) use ($firstDayOfMonth, $firstDayOfNextMonth) {
                            $query->from('shipments_journey')
                                ->where('shipments.id', '=', DB::raw('`shipments_journey`.`shipment_id`'))
                                ->whereBetween('created_at', [$firstDayOfMonth, $firstDayOfNextMonth])
                                ->where('shipper_status_id', 2);
                        })->sum('amount'));
                        $details['revenue'][$s->id][$month] = number_format(DB::connection('reports')->table('shipments')->where('user_id', $s->id)->whereExists(function ($query) use ($firstDayOfMonth, $firstDayOfNextMonth) {
                            $query->from('shipments_journey')
                                ->where('shipments.id', '=', DB::raw('`shipments_journey`.`shipment_id`'))
                                ->whereBetween('created_at', [$firstDayOfMonth, $firstDayOfNextMonth])
                                ->where('shipper_status_id', 2);
                        })->sum(DB::connection('reports')->raw('IFNULL(weight_charges,0) + IFNULL(cash_handling_charges,0) + IFNULL(insurance_charges,0) + IFNULL(return_charges,0) + IFNULL(fuel_surcharge,0) + IFNULL(replacement_charges,0) + IFNULL(try_and_buy_charges,0)')));

                    }
                }
            }

        }


        //echo "<pre>";print_r($details);echo "</pre>";die();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $cell_st = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => ['bottom' => ['style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM]]
        ];
        $sheet->getStyle('A1:B1')->applyFromArray($cell_st);

        $cellIndexcol1 = 4;
        $cellIndexcol2 = 7;

        foreach ($details['months'] as $key => $name) {
            $cellIndex1 = Coordinate::stringFromColumnIndex($cellIndexcol1);
            $cellIndex2 = Coordinate::stringFromColumnIndex($cellIndexcol2);
            $cellIndex11 = $cellIndex1 . '1';
            $cellIndex12 = $cellIndex2 . '1';
            $sheet->mergeCells("$cellIndex11:$cellIndex12");
            $sheet->getStyle("$cellIndex11:$cellIndex12")->applyFromArray($cell_st);
            $sheet->setCellValue($cellIndex11, $name);
            $sheet->fromArray($details['subheader'], NULL, $cellIndex1 . '2');

            $cellIndexcol1 += 4;
            $cellIndexcol2 += 4;

        }
        $sheet->fromArray($details['header'], NULL, 'A1');
        $col = 4;
        $parcelIndex = 4;
        $weightIndex = 5;
        $codIndex = 6;
        $revenueIndex = 7;
        foreach ($details['hubs'] as $key => $h) {

            $sheet->setCellValue('A' . $col, $h);
            $sheet->getStyle('A' . $col)->applyFromArray($cell_st);

            if (isset($details['shipper']) && !empty($details['shipper'])) {
                foreach ($details['shipper'] as $hkey => $client) {
                    foreach ($client as $ship_key => $cli) {
                        if ($hkey == $key) {
                            $sheet->setCellValue('B' . $col, str_pad($ship_key, 6, '0', STR_PAD_LEFT));
                            $sheet->setCellValue('C' . $col, $cli);
                            $parcelIndex = 4;
                            $weightIndex = 5;
                            $codIndex = 6;
                            $revenueIndex = 7;
                            foreach ($details['months'] as $m) {
                                $parcelIndexl = Coordinate::stringFromColumnIndex($parcelIndex);
                                $weightIndexl = Coordinate::stringFromColumnIndex($weightIndex);
                                $codIndexl = Coordinate::stringFromColumnIndex($codIndex);
                                $revenueIndexl = Coordinate::stringFromColumnIndex($revenueIndex);
                                $sheet->setCellValue($parcelIndexl . $col, $details['parcels'][$ship_key][$m]);
                                $sheet->setCellValue($weightIndexl . $col, $details['weight'][$ship_key][$m]);
                                $sheet->setCellValue($codIndexl . $col, $details['amount'][$ship_key][$m]);
                                $sheet->setCellValue($revenueIndexl . $col, $details['revenue'][$ship_key][$m]);
                                $parcelIndex += 4;
                                $weightIndex += 4;
                                $codIndex += 4;
                                $revenueIndex += 4;
                            }
                            $col++;
                        }
                    }


                }
            } else {
                return response()->json(['failure' => 0, 'error' => 'No data found!']);
            }
            $col++;
        }
        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="customer_sales_report.xlsx"');
        header('Cache-Control: max-age=0');
        $file_name = "reports/customer_sales_report" . Auth::id() . ".xlsx";
        $writer->save("$file_name");
        return response()->json(['success' => 1, 'file' => 'customer_sales_report.xlsx']);
    }

    public function customer_sales_download(Request $request)
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 148);
        $file_name = "/reports/customer_sales_report" . Auth::id() . ".xlsx";

        $file = public_path() . $file_name;
        $headers = array('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',);
        return Response::download($file, 'customer_sales_report.xlsx', $headers);
    }

    public function completed_delivery_notes_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 129);
        $riders = DB::connection('reports')->table('riders')->get(['id', 'name', 'cnic']);
        $admins = DB::connection('reports')->table('admins')->get(['id', 'name']);
        $hubs = DB::connection('reports')->table('cities')->where('hub', 1)->select('id', 'name')->get();
        $shipping_modes = DB::connection('reports')->table('shipping_modes')->get(['id', 'mode']);
        $couriers = DB::connection('reports')->table('rider_categories')->get(['id', 'name']);
        return view('admin.reports.completed_delivery_notes_report')->with(['riders' => $riders, 'admins' => $admins, 'hubs' => $hubs, 'shipping_modes' => $shipping_modes, 'couriers' => $couriers]);
    }

    public function completed_delivery_notes_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 130);
        }
        $deliveries = DB::connection('reports')->table('delivery_notes')->
        join('cities AS oc', 'delivery_notes.hub_id', '=', 'oc.id')
            ->leftjoin('delivery_note_shipments as dns', 'dns.delivery_note_id', '=', 'delivery_notes.id')
            ->leftjoin('shipments', 'shipments.id', '=', 'dns.shipment_id')
            ->join('riders', 'delivery_notes.rider_id', '=', 'riders.id')
            ->join('rider_categories', 'rider_categories.id', '=', 'riders.rider_category_id')
            ->join('routes', 'delivery_notes.route_id', '=', 'routes.id')
            ->leftjoin('admins as ccb', 'delivery_notes.cash_collected_by', '=', 'ccb.id')
            ->join('admins', 'admins.id', '=', 'delivery_notes.admin_id')
            ->leftjoin('admins as ub', 'ub.id', '=', 'delivery_notes.updated_by')
            ->leftjoin('admins as vb', 'vb.id', '=', 'delivery_notes.verified_by')
            ->select(['delivery_notes.id as delivery_note', 'delivery_notes.id as delivery_note_id', 'oc.id as hub_id', 'oc.name as hub', 'riders.name as rider', 'routes.code as route', 'routes.start', 'routes.end', 'admins.name as assignee', 'ub.name as updated_by', 'delivery_notes.updated_at as updated_at', 'delivery_notes.delivered_shipments', 'delivery_notes.created_at as created_at', 'delivery_notes.total_cod_amount as amount', 'delivery_notes.shipments_count', 'delivery_notes.last_updated_at', 'vb.name as verified_by', 'delivery_notes.status_updated_at as status_updated', 'delivery_notes.status_verified_at as status_verified', 'delivery_notes.cash_collected_by', 'ccb.name as cash_collected', 'delivery_notes.cash_collected_at', 'delivery_notes.special_rider', 'delivery_notes.special_rider_name', 'delivery_notes.special_rider_phone', 'riders.cnic as cni', 'rider_categories.name as category'])
            ->where('delivery_notes.status', 1)->groupBy('delivery_notes.id');
        if (session('role_id') != 1 || !in_array(session('id'), session('sale_users_bypass'))) {
            $deliveries = $deliveries->whereIn('delivery_notes.hub_id', session('hubs'));
        }
        if (session('department_id') == 7) {
            if (!in_array(session('id'), session('sale_users_bypass'))) {
                $deliveries = $deliveries->whereIn('shipments.user_id', session('tagged_shippers'));
            }
        }
        if (session('department_id') == 8) {
            $deliveries = $deliveries->where('shipments.shipment_type',2);
        }

        $datatable = Datatables::of($deliveries)
            ->addColumn('aging_create_update', function ($deliveries) {
                return ($deliveries->created_at && $deliveries->updated_at) ? Carbon::parse($deliveries->updated_at)->diffInDays($deliveries->created_at) : '-';
            })
            ->editColumn('rider', function ($rider) {
                if ($rider->special_rider) {
                    return $rider->rider . ' (' . $rider->special_rider_name . ')';
                } else {
                    return $rider->rider;
                }
            })
            ->editColumn('amount', function ($shipment) {
                return number_format($shipment->amount);
            })
            ->addColumn('aging_update_verified', function ($deliveries) {
                return ($deliveries->updated_at && $deliveries->status_verified) ? Carbon::parse($deliveries->status_verified)->diffInDays($deliveries->updated_at) : '-';
            })
            ->addColumn('aging_create_verified', function ($deliveries) {
                return ($deliveries->created_at && $deliveries->status_verified) ? Carbon::parse($deliveries->status_verified)->diffInDays($deliveries->created_at) : '-';
            })
            ->editColumn('delivery_note', function ($deliveries) {
                return str_pad($deliveries->delivery_note, 6, '0', STR_PAD_LEFT);
            })
            ->editColumn('delivery_note_link', function ($deliveries) {
                return '<button class="btn btn-sm btn-outline-info align-middle print"><i class="la la-lg la-print align-middle"></i> <span class="align-middle">' . str_pad($deliveries->delivery_note, 6, '0', STR_PAD_LEFT) . '</span></button>';
            })
            ->editColumn('shipments_count_link', function ($deliveries) {
                if ($deliveries->shipments_count != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $deliveries->shipments_count . '</button>';
                } else {
                    return 0;
                }
            })
            ->editColumn('delivered_shipments_link', function ($deliveries) {
                if ($deliveries->delivered_shipments != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $deliveries->delivered_shipments . '</button>';
                } else {
                    return 0;
                }
            })
            ->editColumn('route', function ($rider) {
                return $rider->route . ' (' . $rider->start . ' to ' . $rider->end . ')';
            })
            ->filterColumn('route', function ($query, $keyword) {
                $keyword = strtolower($keyword);
                if ($keyword != '') {
                    $query->where('routes.code', 'like', '%' . $keyword . '%')->orWhere('routes.start', 'like', '%' . $keyword . '%')->orWhere('routes.end', 'like', '%' . $keyword . '%');
                } else {
                    $query->whereRaw('false');
                }
            });
        if ($rn_no = $request->get('search_dn_no')) {
            $datatable->where('delivery_notes.id', '=', $rn_no);
        }
        if ($tracking = $request->get('search_tracking')) {
            $datatable->join('delivery_note_shipments as rns', 'rns.delivery_note_id', '=', 'delivery_notes.id')
                ->join('shipments as s', 'rns.shipment_id', '=', 's.id')
                ->where('s.tracking_number', '=', $tracking);
        }
        if ($rider = $request->get('search_rider')) {
            $datatable->where('riders.id', '=', $rider);
        }
        if ($rider = $request->get('rider_cnic')) {
            $datatable->where('riders.id', '=', $rider);
        }
        if ($created_by = $request->get('search_assigned_by')) {
            $datatable->where('admins.id', '=', $created_by);
        }
        if ($submitted_by = $request->get('search_updated_by')) {
            $datatable->where('ub.id', '=', $submitted_by);
        }
        if ($hub = $request->get('search_hub')) {
            $datatable->where('oc.id', '=', $hub);
        }
        if ($mode = $request->get('search_shipping_mode')) {
            $datatable->where('shipments.booking_type_id', '=', $mode);
        }
        if ($courier_id = $request->get('courier_id')) {
            $datatable->where('rider_categories.id', '=', $courier_id);
        }
        if ($submission_date = $request->get('search_submission')) {
            $datatable->whereDate('delivery_notes.updated_at', $submission_date);
        }
        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $datatable->whereBetween('delivery_notes.created_at', [$from, $to]);
        }
        if ($request->get('update_date_from') && $request->get('update_date_to')) {
            $from = $request->get('update_date_from');
            $to = $request->get('update_date_to');
            $datatable->whereBetween('delivery_notes.status_updated_at', [$from, $to]);
        }
        return $datatable->make(true);

    }

    //completed_delivery_note
    public function completed_shipments(Request $request)
    {
        $delivery_note_id = $request->input('delivery_note_id');
        $delivery_note_shipments = DB::connection('reports')->table('delivery_note_shipments')->where('delivery_note_id', $delivery_note_id)->get();
        $shipments = array();
        if ($delivery_note_shipments->count() != 0) {
            foreach ($delivery_note_shipments as $delivery_note_shipment) {
                $shipment = DB::connection('reports')->table('shipments')->find($delivery_note_shipment->shipment_id);
                $shipments[] = $shipment->tracking_number;
            }
            return ['status' => 0, 'success' => 'Delivery Note Shipments', 'shipments' => $shipments];
        } else {
            return ['status' => 0, 'success' => 'No Delivery Note Shipments', 'shipments' => FALSE];
        }
    }

    public function completed_shipments_delivered(Request $request)
    {
        $delivery_note_id = $request->input('delivery_note_id');
        $delivery_note_shipments = DB::connection('reports')->table('delivery_note_shipments')->where('delivery_note_id', $delivery_note_id)->where('status', '>', 1)->get();
        $shipments = array();
        if ($delivery_note_shipments->count() != 0) {
            foreach ($delivery_note_shipments as $delivery_note_shipment) {
                $shipment = DB::connection('reports')->table('shipments')->find($delivery_note_shipment->shipment_id);
                $shipments[] = $shipment->tracking_number;
            }
            return ['status' => 0, 'success' => 'Delivery Note Shipments', 'shipments' => $shipments];
        } else {
            return ['status' => 0, 'success' => 'No Delivery Note Shipments', 'shipments' => FALSE];
        }
    }

    public function customer_retention_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 143);
        if (session('role_id') == 1) {
            $shippers = DB::connection('reports')->table('users')->where('status', '>=', 3)->get();
            $hubs = DB::connection('reports')->table('cities')->select('id', 'name')->where('hub', 1)->get();
        } else {
            if (session('department_id') != 7) {
                $shippers = DB::connection('reports')->table('users')->whereExists(function ($query) {
                    $query->from('cities')
                        ->where('users.city_id', '=', DB::raw('`cities`.`id`'))
                        ->whereIn('hub_id', session('hubs'));
                })->where('status', '>=', 3)->get();
                $hubs = DB::connection('reports')->table('cities')->select('id', 'name')->whereIn('id', session('hubs'))->get();
            } else {
                if (!in_array(session('id'), session('sale_users_bypass'))) {
                    $shippers = DB::connection('reports')->table('users')->whereIn('id', session('tagged_shippers'))->where('status', '>=', 3)->get();
                    $hubs = DB::connection('reports')->table('cities')->select('id', 'name')->whereIn('id', session('hubs'))->get();
                } else {
                    $shippers = DB::connection('reports')->table('users')->whereExists(function ($query) {
                        $query->from('cities')
                            ->where('users.city_id', '=', DB::raw('`cities`.`id`'))
                            ->whereIn('hub_id', session('hubs'));
                    })->where('status', '>=', 3)->get();
                    $hubs = DB::connection('reports')->table('cities')->select('id', 'name')->whereIn('id', session('hubs'))->get();
                }
            }
        }

        return view('admin.reports.customer_retention_report')->with(['hubs' => $hubs, 'shippers' => $shippers]);
    }

    public function customer_retention_export_to_excel(Request $request)
    {
        $hub = $request->city;
        $shipper_filter = $request->shipper;
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $months_array = array();
        $months_array = $this->get_months($from_date, $to_date);
//        unset($months_array[0]);


        $details = array();
        $shippers = array();
        $shippers['header'] = ['S.No', 'Client Account No.', 'Client Name'];


        foreach ($months_array as $month) {

            $first_date = Carbon::parse($month)->firstOfMonth();
            $last_date = Carbon::parse($month)->lastOfMonth()->endOfDay();
            if ($hub != null) {
                if (session('department_id') != 7) {
                    $details['s'][$month] = DB::connection('reports')->table('users')->whereDate('activated_at', '<=', $first_date)->where('status', 3)->where('city_id', $hub)->count();
                    $details['e'][$month] = DB::connection('reports')->table('users')->whereDate('activated_at', '<=', $last_date)->where('status', 3)->where('city_id', $hub)->count();
                    $details['n'][$month] = DB::connection('reports')->table('users')->whereBetween('activated_at', [$first_date, $last_date])->where('status', 3)->where('city_id', $hub)->count();
                } else {
                    if (!in_array(session('id'), session('sale_users_bypass'))) {
                        $details['s'][$month] = DB::connection('reports')->table('users')->whereDate('activated_at', '<=', $first_date)->where('status', 3)->where('city_id', $hub)->whereIn('id', session('tagged_shippers'))->count();
                        $details['e'][$month] = DB::connection('reports')->table('users')->whereDate('activated_at', '<=', $last_date)->where('status', 3)->where('city_id', $hub)->whereIn('id', session('tagged_shippers'))->count();
                        $details['n'][$month] = DB::connection('reports')->table('users')->whereBetween('activated_at', [$first_date, $last_date])->where('status', 3)->where('city_id', $hub)->whereIn('id', session('tagged_shippers'))->count();
                    } else {
                        $details['s'][$month] = DB::connection('reports')->table('users')->whereDate('activated_at', '<=', $first_date)->where('status', 3)->where('city_id', $hub)->count();
                        $details['e'][$month] = DB::connection('reports')->table('users')->whereDate('activated_at', '<=', $last_date)->where('status', 3)->where('city_id', $hub)->count();
                        $details['n'][$month] = DB::connection('reports')->table('users')->whereBetween('activated_at', [$first_date, $last_date])->where('status', 3)->where('city_id', $hub)->count();
                    }
                }
            } else {
                if (session('department_id') != 7) {
                    $details['s'][$month] = number_format(DB::connection('reports')->table('users')->whereDate('activated_at', '<=', $first_date)->where('status', 3)->count());
                    $details['e'][$month] = number_format(DB::connection('reports')->table('users')->whereDate('activated_at', '<=', $last_date)->where('status', 3)->count());
                    $details['n'][$month] = number_format(DB::connection('reports')->table('users')->whereBetween('activated_at', [$first_date, $last_date])->where('status', 3)->count());
                } else {
                    if (!in_array(session('id'), session('sale_users_bypass'))) {
                        $details['s'][$month] = number_format(DB::connection('reports')->table('users')->whereDate('activated_at', '<=', $first_date)->where('status', 3)->whereIn('id', session('tagged_shippers'))->count());
                        $details['e'][$month] = number_format(DB::connection('reports')->table('users')->whereDate('activated_at', '<=', $last_date)->where('status', 3)->whereIn('id', session('tagged_shippers'))->count());
                        $details['n'][$month] = number_format(DB::connection('reports')->table('users')->whereBetween('activated_at', [$first_date, $last_date])->where('status', 3)->whereIn('id', session('tagged_shippers'))->count());
                    } else {
                        $details['s'][$month] = number_format(DB::connection('reports')->table('users')->whereDate('activated_at', '<=', $first_date)->where('status', 3)->count());
                        $details['e'][$month] = number_format(DB::connection('reports')->table('users')->whereDate('activated_at', '<=', $last_date)->where('status', 3)->count());
                        $details['n'][$month] = number_format(DB::connection('reports')->table('users')->whereBetween('activated_at', [$first_date, $last_date])->where('status', 3)->count());
                    }
                }
            }

            $n = ($details['s'][$month] != 0) ? $details['s'][$month] : 0;
            $details['crr'][$month] = ($n != 0) ? (($details['e'][$month] - $details['n'][$month]) / $n) * 100 : '-';

            $shippers['header'][] = $month;

        }

        if ($hub != null) {
            if (session('role_id') == 1 || in_array($hub, session('hubs'))) {
                if (session('department_id') == 7) {
                    if (!in_array(session('id'), session('sale_users_bypass'))) {
                        $shippers['shipper'] = DB::connection('reports')->table('users')->where('status', '>=', 3)->whereIn('id', session('tagged_shippers'))->get();
                    } else {
                        $shippers['shipper'] = DB::connection('reports')->table('users')->whereExists(function ($query) use ($hub) {
                            $query->from('cities')
                                ->where('users.city_id', '=', 'cities.id')
                                ->where('hub_id', '=', $hub);
                        })->where('status', '>=', 3)->get();
                    }
                } else {

                    $shippers['shipper'] = DB::connection('reports')->table('users')->whereExists(function ($query) use ($hub) {
                        $query->from('cities')
                            ->where('users.city_id', '=', 'cities.id')
                            ->where('hub_id', '=', $hub);
                    })->where('status', '>=', 3)->get();
                }
            } else {
                $shippers['shipper'] = '';
            }
        } else {
            if (session('role_id') == 1) {
                $shippers['shipper'] = DB::connection('reports')->table('users')->where('status', '>=', 3)->get();
            } else {
                if (session('department_id') == 7) {
                    if (!in_array(session('id'), session('sale_users_bypass'))) {
                        $shippers['shipper'] = DB::connection('reports')->table('users')->where('status', '>=', 3)->whereIn('id', session('tagged_shippers'))->get();
                    } else {
                        $shippers['shipper'] = DB::connection('reports')->table('users')->whereExists(function ($query) {
                            $query->from('cities')
                                ->where('users.city_id', '=', 'cities.id')
                                ->where('hub_id', '=', session('hubs'));
                        })->where('status', '>=', 3)->get();
                    }
                } else {
                    $shippers['shipper'] = DB::connection('reports')->table('users')->whereExists(function ($query) {
                        $query->from('cities')
                            ->where('users.city_id', '=', 'cities.id')
                            ->where('hub_id', '=', session('hubs'));
                    })->where('status', '>=', 3)->get();
                }
            }
        }


        if ($shipper_filter != null) {
            if (session('role_id') == 1) {
                $client_exist = DB::connection('reports')->table('users')->where('id', $shipper_filter);
                if ($client_exist->exists()) {
                    $shippers['shipper'] = $client_exist->get();
                }
            } else {
                $client_exist = DB::connection('reports')->table('users')->where('id', $shipper_filter)
                    ->whereExists(function ($query) {
                        $query->from('cities')
                            ->where('users.city_id', '=', 'cities.id')
                            ->where('hub_id', '=', session('hubs'));
                    })->where('status', '>=', 3);
                if ($client_exist->exists()) {
                    $shippers['shipper'] = $client_exist->get();
                }
            }

        }

        if (count($shippers['shipper']) > 0) {
            foreach ($shippers['shipper'] as $client) {
                $shippers['name'][$client->id] = $client->name;
                foreach ($months_array as $month) {
                    $thisMonth = Carbon::parse($month)->month;
                    $thisYear = Carbon::parse($month)->year;
                    $shippers['parcels'][$client->id][$month] = DB::connection('reports')->table('shipments')->where('user_id', $client->id)
                        ->whereExists(function ($query) use ($thisMonth, $thisYear) {
                            $query->from('shipments_journey')
                                ->where('shipments.id', '=', DB::raw('`shipments_journey`.`shipment_id`'))
                                ->whereMonth('created_at', $thisMonth)
                                ->whereYear('created_at', $thisYear)
                                ->where('shipper_status_id', 2);
                        })->count();
                }
            }
        }
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $style = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => array(
                'outline' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                    'color' => array('argb' => '000000'),
                ),
            ),
        ];
        $sheet->getDefaultColumnDimension()->setWidth(20);
        $sheet->getStyle('A2:Z2')->applyFromArray($style);
        $sheet->getStyle('A4')->applyFromArray($style);
//        $sheet->getStyle('A5:D5')->applyFromArray($style);
//        $sheet->getStyle('A6:D9')->applyFromArray($style);
        $sheet->mergeCells('A2:Z2');
//        $sheet->getStyle('A10:D10')->applyFromArray($style);
        $sheet->getStyle('A12:A12')->applyFromArray($style);
        $sheet->setCellValue('A2', 'Customer Retention Report');
        $sheet->setCellValue('A4', 'Summary');
        $sheet->setCellValue('A5', 'Months');
        $sheet->setCellValue('A7', 'S');
        $sheet->setCellValue('A8', 'E');
        $sheet->setCellValue('A9', 'N');
        $sheet->setCellValue('A10', 'CRR');
        $sheet->setCellValue('A12', 'Details');
        $monthIndexcol1 = 2;
        $monthRow = 5;
        foreach ($months_array as $month) {
            $cellIndex1 = Coordinate::stringFromColumnIndex($monthIndexcol1);
            $sheet->setCellValue($cellIndex1 . $monthRow, $month);
            $sheet->setCellValue($cellIndex1 . '7', $details['s'][$month]);
            $sheet->setCellValue($cellIndex1 . '8', $details['e'][$month]);
            $sheet->setCellValue($cellIndex1 . '9', $details['n'][$month]);
            $sheet->setCellValue($cellIndex1 . '10', $details['crr'][$month]);
            $monthIndexcol1 += 1;
        }
        $sheet->fromArray($shippers['header'], NULL, 'A13');
        $col = 14;
        $serials = 1;
        $dateIndex = 4;
        if (count($shippers['shipper']) > 0) {
            foreach ($shippers['name'] as $id => $shipper) {
                $sheet->setCellValue('A' . $col, $serials);
                $sheet->setCellValue('B' . $col, str_pad($id, 6, '0', STR_PAD_LEFT));
                $sheet->setCellValue('C' . $col, $shipper);
                foreach ($months_array as $m) {
                    $cellIndexShipper = Coordinate::stringFromColumnIndex($dateIndex);
                    $sheet->setCellValue($cellIndexShipper . $col, $shippers['parcels'][$id][$m]);
                    $dateIndex++;
                }
//            $sheet->setCellValue($dateIndex.$col,$shippers['total'][$id]);
                $col++;
                $serials++;
                $dateIndex = 4;
            }
        }
        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="customer_retention_report.xlsx"');
        header('Cache-Control: max-age=0');
        $file_name = "reports/customer_retention_report" . Auth::id() . ".xlsx";
        $writer->save("$file_name");
        return response()->json(['success' => 1, 'file' => 'customer_retention_report.xlsx']);

    }

    public function customer_retention_download(Request $request)
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 144);
        $file_name = "/reports/customer_retention_report" . Auth::id() . ".xlsx";
        $file = public_path() . $file_name;
        $headers = array('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',);
        return Response::download($file, 'customer_retention_report.xlsx', $headers);
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
                ->where('sj.shipper_status_id', 2)
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
                ->whereIn('dr.shipper_status_id', [14,25,30,36,37])
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
        ->select('invoices.invoice_number','r.name as ridername','ssr.name as reason','sjr.remarks as remark','p.product_name as category','si.description as description','shipments.id as shipment_id','shipments.tracking_number','shipments.order_id as order_id','shipments.tracking_number as tracking_number_link','u.id as account_no','u.name as shipper','usi.pickup_address as shipper_address','ss.name as current_status','bt.booking_type as service_type','sj.created_at as arrival_date','oc.name as origin','dc.name as destination','h.name as hub','shipments.amount as s_collection_amount','sps.name as payment_status','pps.amount as p_collection_amount','shipments.actual_weight','shipments.weight_charges','shipments.cash_handling_charges','shipments.insurance_charges','shipments.return_charges','shipments.replacement_charges','shipments.fuel_surcharge','shipments.try_and_buy_charges','shipments.packaging_material_charges','pps.gst as p_gst','pps.charges as p_total_charges','pps.payable as p_net_payable','dps.amount as d_collection_amount','dps.gst as d_gst','dps.charges as d_total_charges','dps.payable as d_net_payable', 'sm.mode as shipping_mode','sm.id as shipping_mode_id','shipments.chargeable_weight','dr.created_at as delivered_or_returned','z.name as zone','zcc.class', 'oc.id as origin_city_id', 'dc.id as destination_city_id', 'dps.done_payment_id as payment_id', 'shipments.booking_type_id', 'usi.poc', 'adsp.name as sales_person', 'shipments.shipper_status_id as shipment_status', 'shipments.nsa_osa_charges', 'u.account_type_id as account_type_id', 'pis.gst as pis_gst', 'is.gst as is_gst','shipments.packaging_charges', 'dr.received_or_refused_by', 'shipments.special_instructions','shipments.intercept_charges','bc.name as business_shipment_type','ibs.international_tracking_number','usi.vendor', 'dr.shipper_status_id as dr_status_id', 'shipments.shipment_type','rc.name as return_city',DB::raw('(select count(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 5) as total_attempt'), 'dr.cnic as dr_cnic', 'dr.relation as dr_relation', 'shipments.consignee_address as consignee_address')
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

    public function sales_person_performance_index()
    {

        ActivityTrailController::createActivityTrailLog(Auth::id(), 151);
        if (session('role_id') == 1) {
            $sales_persons = DB::connection('reports')->table('admins')->join('admin_roles as ar', 'admins.role_id', '=', 'ar.id')->select(['admins.id', 'admins.name'])->where('ar.department_id', 7)->get();
            $hubs = DB::connection('reports')->table('cities')->select('id', 'name')->where('hub', 1)->get();
        } else {
            if (session('department_id') != 7) {
                $sales_persons = DB::connection('reports')->table('admins')->join('admin_roles as ar', 'admins.role_id', '=', 'ar.id')->select(['admins.id', 'admins.name'])->where('ar.department_id', 7)->get();
                $hubs = DB::connection('reports')->table('cities')->select('id', 'name')->whereIn('id', session('hubs'))->get();
            } else {
                if (!in_array(session('id'), session('sale_users_bypass'))) {
                    $admins = array();
                    $admins[0] = Auth::id();
                    $tagged_admins = DB::connection('reports')->table('multiple_sale_leads')->leftjoin('multiple_sale_taggings as mst', 'mst.lead_id', '=', 'multiple_sale_leads.id')->select('mst.admin_id')->where('multiple_sale_leads.admin_id', Auth::id())->whereNotNull('mst.admin_id')->pluck('mst.admin_id')->toArray();
                    if ($tagged_admins) {
                        $admins = array_merge($admins, $tagged_admins);
                    }
                    $sales_persons = DB::connection('reports')->table('admins')->whereIn('id', $admins)->select('id', 'name')->get();
                    $hubs = DB::connection('reports')->table('cities')->select('id', 'name')->whereIn('id', session('hubs'))->get();
                } else {
                    $sales_persons = DB::connection('reports')->table('admins')->join('admin_roles as ar', 'admins.role_id', '=', 'ar.id')->select(['admins.id', 'admins.name'])->where('ar.department_id', 7)->get();
                    $hubs = DB::connection('reports')->table('cities')->select('id', 'name')->whereIn('id', session('hubs'))->get();
                }
            }
        }
        return view('admin.reports.sales_person_performance_report')->with(['hubs' => $hubs, 'sales_persons' => $sales_persons]);
    }

    public function sales_person_performance_export_to_excel(Request $request)
    {
        $hub = $request->city;
        $sales_person_filter = $request->sales_person;
        $start_date = $request->from_date;
        $current_date = $request->to_date;

        $start_date = Carbon::parse($start_date);
        $current_date = Carbon::parse($current_date);
        $account_status = array();
        if ($request->account == '') {
            $account_status = [3, 4, 5];
        } else if ($request->account == 3) {
            $account_status = [3];
        } else if ($request->account == 4) {
            $account_status = [4];
        } else if ($request->account == 5) {
            $account_status = [5];
        }

        $dates = [];

        for ($d = $start_date; $d->lte($current_date); $d->addDay()) {
            $dates[] = $d->format('Y-m-d');
        }

        if ($sales_person_filter != null) {
            $sales_person = DB::connection('reports')->table('admins')->where('id', $sales_person_filter)->get();

        } else {
            if (session('role_id') == 1) {
                $sales_person = DB::connection('reports')->table('admins')->join('admin_roles as ar', 'admins.role_id', '=', 'ar.id')->select(['admins.id', 'admins.name'])->where('ar.department_id', 7)->get();
            } else {
                if (session('department_id') != 7) {
                    $sales_person = DB::connection('reports')->table('admins')->join('admin_roles as ar', 'admins.role_id', '=', 'ar.id')->select(['admins.id', 'admins.name'])->where('ar.department_id', 7)->get();
                }else{
                    if(!in_array(session('id'), session('sale_users_bypass'))){
                        $sales_person = DB::connection('reports')->table('admins')->where('id', Auth::id())->get();
                    } else {
                        $sales_person = DB::connection('reports')->table('admins')->join('admin_roles as ar', 'admins.role_id', '=', 'ar.id')->select(['admins.id', 'admins.name'])->where('ar.department_id', 7)->get();
                    }
                }
            }
        }

        $details = array();
        $date_sums = array();

        foreach ($dates as $date) {
            $details['dates'][] = $date;

            $date_sums[$date] = 0;
        }
        $overall_sum = 0;
        $sales_persons_data = array();
        $shippers = array();

        $details['header'] = ['Sales Persons', 'Client Account No', 'Client Name'];
//        $details['subheader'] = ['Parcels', 'Weight','Collection Amount','Revenue' ];

        foreach ($sales_person as $person) {
            $sales_persons_data[$person->id]['name'] = $person->name;

            $tagged_shippers = DB::connection('reports')->table('sale_person_tags')->where('admin_id', $person->id)->where('status', 0)->select('user_id')->get();
            foreach ($tagged_shippers as $shipper) {
                if ($hub != null) {

                    $user = DB::connection('reports')->table('users')->whereExists(function ($query) use ($hub) {
                        $query->from('user_shipping_infos')
                            ->where('user_shipping_infos.user_id', '=', DB::raw('`users`.`id`'))
                            ->where('city_id', '=', $hub);
                    })->where('id', $shipper->user_id)->whereIn('status', $account_status)->first();
                } else {

                    $user = DB::connection('reports')->table('users')->where('id', $shipper->user_id)->whereIn('status', $account_status)->first();
                }
                if ($user) {

                    $sales_persons_data[$person->id]['shipper'][$user->id] = $user->name;
//                    $sales_persons_data[$person->id]['account'][$user->id] = str_pad($user->id, 6, '0', STR_PAD_LEFT);
                    foreach ($dates as $date) {
                        $custom_date_from = Carbon::parse($date);
                        $custom_date_to = Carbon::parse($date);

                        $custom_date_from_time = $custom_date_from->setTimeFromTimeString('05:59:59');

                        $custom_date_to_time = $custom_date_to->addDay()->setTimeFromTimeString('06:00:00');

                        if ($hub != null) {
                            $sum = DB::connection('reports')->table('shipments')->whereExists(function ($query) use ($custom_date_from_time, $custom_date_to_time) {
                                $query->from('shipments_journey')
                                    ->where('shipments.id', '=', DB::raw('`shipments_journey`.`shipment_id`'))
                                    ->whereBetween('created_at', [$custom_date_from_time, $custom_date_to_time])
                                    ->where('shipper_status_id', 2);
                            })->whereExists(function ($query) use ($hub) {
                                $query->from('user_shipping_infos')
                                    ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                                    ->whereExists(function ($sub_query) use ($hub) {
                                        $sub_query->from('cities')
                                            ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                                            ->where('cities.id', $hub);
                                    });
                            })->where('shipments.user_id', $user->id)->count();
                        } else {
                            $sum = DB::connection('reports')->table('shipments')->whereExists(function ($query) use ($custom_date_from_time, $custom_date_to_time) {
                                $query->from('shipments_journey')
                                    ->where('shipments.id', '=', DB::raw('`shipments_journey`.`shipment_id`'))
                                    ->whereBetween('created_at', [$custom_date_from_time, $custom_date_to_time])
                                    ->where('shipper_status_id', 2);
                            })->where('shipments.user_id', $user->id)->count();
                        }


                        $sales_persons_data[$person->id]['pickups'][$user->id][] = $sum;

                        $date_sums[$date] += $sum;
                        $overall_sum += $sum;
                    }

                    $sum_of_pickups = array_sum($sales_persons_data[$person->id]['pickups'][$user->id]);
                    array_push($sales_persons_data[$person->id]['pickups'][$user->id], number_format($sum_of_pickups));
                }

            }
        }
//        return $sales_persons_data;

        array_push($date_sums, $overall_sum);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getDefaultColumnDimension()->setWidth(20);
        $cell_st = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => ['bottom' => ['style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM]]
        ];
        $footer_cell_st = [
            'font' => ['bold' => true]
        ];

        $admin_index = 2;
        $shipper_index = 2;
        $pickup_index = 2;
        $pickup_col_index = 4;

        foreach ($sales_persons_data as $sales_persons) {


            if (!empty($sales_persons['shipper'])) {
                $sheet->setCellValue('A' . $admin_index, $sales_persons['name']);
                foreach ($sales_persons['shipper'] as $key => $person) {

                    $sheet->setCellValue('B' . $shipper_index, str_pad($key, 6, '0', STR_PAD_LEFT));
                    $sheet->setCellValue('C' . $shipper_index, $person);

                    $shipper_index++;
                    $admin_index++;

                    foreach ($sales_persons['pickups'][$key] as $id => $pickup) {

//                        foreach ($pickup as $n){
                        $cellIndex = Coordinate::stringFromColumnIndex($pickup_col_index);
                        $sheet->setCellValue($cellIndex . $pickup_index, $pickup);
                        $pickup_col_index++;
//                            $pickup_index++;
//                        }

                    }

                    $pickup_col_index = 4;
                    $pickup_index++;
                }

            }

        }
        $pickup_index += 2;
        $date_sum_col_index = 4;
        $date_sum_index = $pickup_index;
        $sheet->setCellValue('A' . $date_sum_index, "Grand Total");
        foreach ($date_sums as $date => $sum) {
            $cellIndex = Coordinate::stringFromColumnIndex($date_sum_col_index);
            $sheet->setCellValue($cellIndex . $date_sum_index, $sum);

            $date_sum_col_index++;
        }

        $cellIndexcol1 = 4;
        foreach ($details['dates'] as $key => $name) {
            $cellIndex1 = Coordinate::stringFromColumnIndex($cellIndexcol1);
            $cellIndex11 = $cellIndex1 . '1';
            $sheet->setCellValue($cellIndex11, $name);
            $cellIndexcol1 += 1;

        }
        $grand_total_index = Coordinate::stringFromColumnIndex($cellIndexcol1);
        $grand_total_header_index = $grand_total_index . '1';
        $sheet->setCellValue($grand_total_header_index, 'Grand Total');
        $header_column_range = "A1:" . $grand_total_header_index;
        $sheet->getStyle($header_column_range)->applyFromArray($cell_st);        //header style
        $sales_column_range = "A1:A" . $pickup_index;
        $shipper_column_range = "B1:B" . $pickup_index;
        $grand_total_last_column_range = "A" . $pickup_index . ":" . $grand_total_index . $pickup_index;
        $sheet->getStyle($grand_total_last_column_range)->applyFromArray($footer_cell_st);        //header style
        $sheet->getStyle($sales_column_range)->applyFromArray($cell_st);        //header style
        $sheet->getStyle($shipper_column_range)->applyFromArray($cell_st);        //header style
        $sheet->fromArray($details['header'], NULL, 'A1');
        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="sales_person_performance.xlsx"');
        header('Cache-Control: max-age=0');
        $file_name = "reports/sales_person_performance" . Auth::id() . ".xlsx";
        $writer->save("$file_name");
        return response()->json(['success' => 1, 'file' => 'sales_person_performance.xlsx']);

    }

    public function sales_person_performance_download(Request $request)
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 152);
        $file_name = "/reports/sales_person_performance" . Auth::id() . ".xlsx";

        $file = public_path() . $file_name;
        $headers = array('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',);
        return Response::download($file, 'sales_person_performance.xlsx', $headers);
    }

    public function negative_balance_customers_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 153);
        $shipping_modes = DB::connection('reports')->table('shipping_modes')->get(['id', 'mode']);
        $salesperson = Admin::join('admin_roles as ar', 'admins.role_id', '=', 'ar.id')->select(['admins.name', 'admins.id'])->where('status', 1)->where('ar.department_id', 7)->get();
        $shippers = User::all();
        return view('admin.reports.invoice_for_negative_balance_customers')->with(['shipping_modes' => $shipping_modes, 'salesperson' => $salesperson, 'shippers' => $shippers]);

    }

    public function negative_balance_customers_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 154);
        }
        $date = Carbon::now();
        $from_date = $date->subDays(7)->startOfDay()->toDateTimeString();

        $negative = DB::connection('reports')->table('pending_payment_shipments')->join('shipments as s', 's.id', '=', 'pending_payment_shipments.shipment_id')
            ->join('users as u', 'u.id', '=', 's.user_id')
            ->leftjoin('sale_person_tags as st', 'st.user_id', '=', 'u.id')
            ->leftjoin('admins as a', 'a.id', '=', 'st.admin_id')
            ->select('a.name as sales_person', 'u.id as account_no', 'u.name as name', 'u.phone as phone', 'pending_payment_shipments.amount as amount', 'pending_payment_shipments.charges as charges', DB::raw('SUM(pending_payment_shipments.payable) AS overall_payable'), DB::raw("(select max(id) from shipments where shipments.user_id = s.user_id and shipments.created_at > '" . $from_date . "') as shipment_exist"))
            ->where('st.status', 0)
            ->groupBy('u.id')
            ->having('overall_payable', '<', 0);

        $datatable = Datatables::of($negative)
            ->setRowAttr([
                'class' => function ($user) {
                    if ($user->shipment_exist == null) {
                        return 'bg-warning';
                    }
                }
            ])
            ->editColumn('amount', function ($shipment) {
                return number_format($shipment->amount);
            })
            ->editColumn('charges', function ($shipment) {
                return number_format($shipment->charges, 2);
            })
            ->editColumn('overall_payable', function ($shipment) {
                return number_format($shipment->overall_payable, 2);
            })
            ->addColumn('account_no', function ($user) {
                return str_pad($user->account_no, 6, '0', STR_PAD_LEFT);
            })
            ->addColumn('status', function ($user) {
                if ($user->shipment_exist == null) {
                    return 'Highlighted';
                } else {
                    return 'Non highlighted';
                }
            });

        if ($mode = $request->get('search_shipping_mode')) {
            $datatable->where('s.booking_type_id', '=', $mode);
        }

        if ($shipper = $request->get('search_shipper')) {
            $datatable->where('u.id', '=', $shipper);
        }

        if ($sale_persons = $request->get('sale_persons')) {
            $datatable = $datatable->whereIn('a.id', $sale_persons);
        }
        if ($status = $request->get('status_select')) {
            if ($status == 1) {
                $datatable = $datatable->havingRaw('shipment_exist is null');
            } else {
                $datatable = $datatable->havingRaw('shipment_exist is not null');
            }
        }

        return $datatable->make(true);

    }

    public function call_verification_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 157);
        $shipping_modes = DB::connection('reports')->table('shipping_modes')->get(['id', 'mode']);
        return view('admin.reports.call_verification_report')->with(['shipping_modes' => $shipping_modes]);

    }

    public function call_verification_list(request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 158);
        }
        $call_verification_report = DB::connection('reports')->table('delivery_notes')->leftjoin('delivery_note_shipments as dns', 'dns.delivery_note_id', '=', 'delivery_notes.id')
            ->join('shipments as s', 's.id', '=', 'dns.shipment_id')
            ->join('admins as ad', 'ad.id', '=', 'delivery_notes.verified_by')
            ->join('cities as c', 'c.id', '=', 'delivery_notes.hub_id')
            ->leftjoin('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 'dns.shipment_id')
                    ->where('sj.id', '=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = dns.shipment_id and shipments_journey.reference_1_id = dns.delivery_note_id and shipments_journey.verification = 1)'));
            })
            ->leftjoin('shipment_status as ss', 'ss.id', '=', 'sj.shipper_status_id')
            ->select('c.name as hub_name', 'sj.shipper_status_id as shipper_status_id', 's.id as ship_id', 's.tracking_number as tracking_no', 's.tracking_number as tracking_number', 'delivery_notes.id as delivery_note_id', 'ss.name as status', 'ad.name as status_verified_by', 'delivery_notes.status_verified_at as status_verified_at', 'dns.call_verification as call_verification_status')->where('delivery_notes.verified_by', '!=', null);
        $datatable = Datatables::of($call_verification_report)
            ->editcolumn('call_verification_status', function ($data) {
                if ($data->call_verification_status == 0) {
                    return 'Not Ticked';
                } else {
                    return 'Ticked';
                }
            })
            ->editColumn('delivery_note_id', function ($deliveries) {
                return str_pad($deliveries->delivery_note_id, 6, '0', STR_PAD_LEFT);
            })
            ->editColumn('tracking_no', function ($shipments) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_no' class='tracking' target='_blank'>$shipments->tracking_no</a></u>";
            });
        if ($mode = $request->get('search_shipping_mode')) {
            $datatable->where('s.booking_type_id', '=', $mode);
        }
        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $datatable->whereBetween('sj.created_at', [$from, $to]);
        }
        return $datatable->make(true);

    }

    public function petty_cash_statements_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 155);
        $hubs = DB::connection('reports')->table('cities')->where('hub', 1)->select('id', 'name')->get();
        $heads = DB::connection('reports')->table('petty_cash_account_heads')->select('id', 'name')->get();
        $titles = DB::connection('reports')->table('petty_cash_account_titles')->select('id', 'name')->get();
        return view('admin.reports.petty_cash_statement')->with(['hubs' => $hubs, 'heads' => $heads, 'titles' => $titles]);
    }

    public function petty_cash_statements_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 156);
        }
        $petty = DB::connection('reports')->table('petty_cash_statement_details')->join('petty_cash_statements as pcs', 'pcs.id', '=', 'petty_cash_statement_details.petty_cash_statement_id')
            ->leftjoin('cities as dc', 'dc.id', '=', 'petty_cash_statement_details.city_id')
            ->leftjoin('cities as h', 'h.id', '=', 'pcs.hub_id')
            ->leftjoin('admins as employee', 'employee.id', '=', 'petty_cash_statement_details.employee_id')
            ->leftjoin('station_deposit_notes as sdn', 'sdn.id', '=', 'pcs.sdn_id')
            ->join('admins as cb', 'cb.id', '=', 'pcs.created_by')
            ->leftjoin('admins as sub', 'sub.id', '=', 'petty_cash_statement_details.updated_by')
            ->leftjoin('delivery_notes as dn', 'dn.id', '=', 'petty_cash_statement_details.dncc_id')
            ->leftjoin('petty_cash_account_heads as pch', 'pch.id', '=', 'petty_cash_statement_details.account_head_id')
            ->leftjoin('petty_cash_account_titles as pct', 'pct.id', '=', 'petty_cash_statement_details.account_title_id')
            ->leftjoin('shipments', 'shipments.id', '=', 'pcs.shipment_id')
            ->leftjoin('admins as chb', 'chb.id', '=', 'pcs.checked_by')
            ->select('pcs.id as statement_id', 'pcs.id as statement_link', 'dc.name as entry_city', 'petty_cash_statement_details.date as entry_date', 'pcs.date as p_entry_date', 'pch.name as account_head', 'pct.name as account_title', 'petty_cash_statement_details.expense_details', 'petty_cash_statement_details.amount', 'petty_cash_statement_details.reference_no as entry_reference_no', 'petty_cash_statement_details.remarks', 'petty_cash_statement_details.status', 'pcs.reference_no as statement_reference_no', 'h.name as hub_name', 'cb.name as created_by', 'pcs.created_at', 'petty_cash_statement_details.station_amount', 'petty_cash_statement_details.operation_amount', 'petty_cash_statement_details.finance_amount', 'shipments.tracking_number', 'pcs.checked_at', 'chb.name as checked_by', 'employee.trax_id as employee_id', 'petty_cash_statement_details.employee_name', 'petty_cash_statement_details.employee_designation', 'sdn.id as sdn_id', 'sdn.dncc_count', 'petty_cash_statement_details.dncc_id as delivery_note', 'petty_cash_statement_details.delivered_shipments as delivered_shipments', 'dn.received_cod_amount as delivery_note_amount');
//            ->where('petty_cash_statements.status','<',3);

        if (session('role_id') != 1) {
            $petty = $petty->where(function ($query) {
                $query->whereIn('pcs.origin_hub_id', session('hubs'))
                    ->orWhereIn('pcs.destination_hub_id', session('hubs'))
                    ->orWhere('pcs.created_by', Auth::id())
                    ->orWhereIn('pcs.hub_id', session('hubs'));
            });
        }

        $petty = Datatables::of($petty)
            ->editColumn('statement_link', function ($petty) {
                return '<button class="btn btn-sm btn-outline-info align-middle"><i class="la la-lg la-print align-middle"></i> <span class="align-middle">' . $petty->statement_link . '</span></button>';
            })
            ->addColumn('petty_cash_statement_link', function ($petty) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$petty->tracking_number' class='tracking' target='_blank'>$petty->tracking_number</a></u>";
            })
            ->addColumn('entry_date', function ($petty) {
                if ($petty->entry_date != "0000-00-00 00:00:00") {
                    return Carbon::parse($petty->entry_date)->toDateString();
                } else {
                    return Carbon::parse($petty->p_entry_date)->toDateString();
                }
            })
            ->editColumn('sdn_id_link', function ($sdn) {
                if ($sdn->sdn_id != null) {
                    return "<a href='javascript:void(0);' class='printSDN' data-sdn_id='" . $sdn->sdn_id . "'><u>" . str_pad($sdn->sdn_id, 6, '0', STR_PAD_LEFT) . "</u></a>";
                } else {
                    return "-";
                }
            })
            ->addColumn('sdn_id_padded', function ($sdn) {
                if ($sdn->sdn_id != null) {
                    return str_pad($sdn->sdn_id, 6, '0', STR_PAD_LEFT);
                } else {
                    return "-";
                }
            })
            ->addColumn('dncc_link', function ($pickup_notes) {
                if ($pickup_notes->dncc_count != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle" data-sdn_id="' . $pickup_notes->sdn_id . '">' . $pickup_notes->dncc_count . '</button>';
                } else {
                    return 0;
                }
            })
            ->editColumn('amount', function ($shipment) {
                return number_format($shipment->amount);
            })
            ->editColumn('status', function ($petty) {
                $status = '';
                if ($petty->status == 0) {
                    $status = 'Created';
                } else if ($petty->status == 1) {
                    $status = 'Rejected';
                } else if ($petty->status == 2) {
                    $status = 'Approved';
                }
                return $status;
            });
        if ($hub = $request->get('search_hub')) {
            $petty->where('h.id', '=', $hub);
        }
        if ($status = $request->get('search_status')) {
            if ($status == 3) {
                $petty->where('petty_cash_statement_details.status', '=', 0);
            } else {
                $petty->where('petty_cash_statement_details.status', $status);
            }

        }
        if ($search_date = $request->get('search_date_created')) {
            $petty->whereDate('pcs.created_at', $search_date);
        }
        if ($head = $request->get('search_head')) {
            $petty->where('pch.id', '=', $head);
        }
        if ($title = $request->get('search_title')) {
            $petty->where('pct.id', '=', $title);
        }
        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $petty->whereBetween('petty_cash_statement_details.created_at', [$from, $to]);
        }

        if ($request->get('checked_search_date_from') && $request->get('checked_search_date_to')) {
            $checked_from = $request->get('checked_search_date_from');
            $checked_to = $request->get('checked_search_date_to');
            $petty->whereBetween('pcs.checked_at', [$checked_from, $checked_to]);
        }
        return $petty->make(true);
    }

    public function fake_status_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 159);
        $riders = DB::connection('reports')->table('riders')->get(['id', 'name']);
        $hubs = DB::connection('reports')->table('cities')->where('hub', 1)->select('id', 'name')->get();
        $shipping_modes = DB::connection('reports')->table('shipping_modes')->get(['id', 'mode']);
        $zones = DB::table('zones')->get();
        return view('admin.reports.fake_statuses_report')->with(['riders' => $riders, 'hubs' => $hubs, 'shipping_modes' => $shipping_modes, 'zones' => $zones]);
    }

    public function fake_status_list(request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 160);
        }
        $delivery_note = DB::connection('reports')->table('delivery_notes')->join('delivery_note_shipments as dns', 'dns.delivery_note_id', '=', 'delivery_notes.id')
            ->leftjoin('shipments as s', 's.id', '=', 'dns.shipment_id')
            ->leftjoin('riders as r', 'r.id', '=', 'delivery_notes.rider_id')
            ->leftjoin('cities as c', 'c.id', '=', 'r.city_id')
            ->leftjoin('admin_hubs as ah', 'ah.hub_id', '=', 'c.hub_id')
            ->leftjoin('zones as z', 'z.id', '=', 'c.zone_id')
            ->select('z.name as zone_name', 'r.name as rider_name', 'c.name as rider_city', 'delivery_notes.id as delivery_note_id', 'delivery_notes.created_at', 'delivery_notes.status_verified_at as verified_at', 'delivery_notes.shipments_count as total_shipments', 'delivery_notes.delivered_shipments as delivered_shipments', DB::connection('reports')->raw('(select count(shipment_id) from delivery_note_shipments where delivery_note_shipments.delivery_note_id = delivery_notes.id and delivery_note_shipments.fake_status = 1) as shipment_fake_status'))
            ->where('dns.fake_status', 1)
            ->where('ah.admin_id', Auth::id())
            ->groupBy('delivery_notes.id');


        $datatables = Datatables::of($delivery_note)
            ->editColumn('delivery_note_id', function ($deliveries) {
                return str_pad($deliveries->delivery_note_id, 6, '0', STR_PAD_LEFT);
            })
            ->editColumn('shipments_count_link', function ($deliveries) {
                if ($deliveries->total_shipments != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $deliveries->total_shipments . '</button>';
                } else {
                    return 0;
                }
            })
            ->editColumn('shipment_fake_status_link', function ($deliveries) {
                if ($deliveries->shipment_fake_status != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $deliveries->shipment_fake_status . '</button>';
                } else {
                    return 0;
                }
            })
            ->editColumn('undelivered_shipments_link', function ($deliveries) {
                $undelivered_shipments = $deliveries->total_shipments - $deliveries->delivered_shipments;
                if ($undelivered_shipments != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $undelivered_shipments . '</button>';
                } else {
                    return 0;
                }
            });
        if ($rider = $request->get('rider')) {
            $datatables->where('r.id', '=', $rider);
        }
        if ($hub = $request->get('hub')) {
            $datatables->where('delivery_notes.hub_id', $hub);
        }
        if ($tracking_number = $request->get('search_tracking_no')) {
            $datatables->where('s.tracking_number', $tracking_number);
        }
        if ($mode = $request->get('search_shipping_mode')) {
            $datatables->where('s.booking_type_id', '=', $mode);
        }
        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $datatables->where(function ($query) use ($from, $to) {
                $query->where(function ($sub_query) use ($from, $to) {
                    $sub_query->WhereNull('delivery_notes.status_verified_at')
                        ->whereBetween('delivery_notes.created_at', [$from, $to]);
                })
                    ->orwhere(function ($sub_query) use ($from, $to) {
                        $sub_query->WhereNotNull('delivery_notes.status_verified_at')
                            ->whereBetween('delivery_notes.status_verified_at', [$from, $to]);
                    });
            });
//            $datatables->whereBetween('delivery_notes.created_at', [$from,$to]);
        }
        return $datatables->make(true);
    }

    public function fake_status_shipments_total(Request $request)
    {
        $delivery_note_id = $request->input('delivery_note_id');
        $delivery_note_shipments = DB::connection('reports')->table('delivery_note_shipments')->where('delivery_note_id', $delivery_note_id)->get();
        $shipments = array();
        if ($delivery_note_shipments->count() != 0) {
            foreach ($delivery_note_shipments as $delivery_note_shipment) {
                $shipment = DB::connection('reports')->table('shipments')->find($delivery_note_shipment->shipment_id);
                $shipments[] = $shipment->tracking_number;
            }
            return ['status' => 0, 'success' => 'Delivery Note Shipments', 'shipments' => $shipments];
        } else {
            return ['status' => 0, 'success' => 'No Delivery Note Shipments', 'shipments' => FALSE];
        }
    }

    public function fake_status_shipments_undelivered(Request $request)
    {
        $delivery_note_id = $request->input('delivery_note_id');
        $delivery_note_shipments = DB::connection('reports')->table('delivery_note_shipments')->where('delivery_note_id', $delivery_note_id)->where('status', '=', 1)->get();
        $shipments = array();
        if ($delivery_note_shipments->count() != 0) {
            foreach ($delivery_note_shipments as $delivery_note_shipment) {
                $shipment = DB::connection('reports')->table('shipments')->find($delivery_note_shipment->shipment_id);
                $shipments[] = $shipment->tracking_number;
            }
            return ['status' => 0, 'success' => 'Delivery Note Shipments', 'shipments' => $shipments];
        } else {
            return ['status' => 0, 'success' => 'No Delivery Note Shipments', 'shipments' => FALSE];
        }
    }

    public function fake_status_shipments(Request $request)
    {
        $delivery_note_id = $request->input('delivery_note_id');
        $delivery_note_shipments = DB::connection('reports')->table('delivery_note_shipments')->where('delivery_note_id', $delivery_note_id)->where('fake_status', '=', 1)->get();

        $shipments = array();
        if ($delivery_note_shipments->count() != 0) {
            foreach ($delivery_note_shipments as $delivery_note_shipment) {
                $shipment = DB::connection('reports')->table('shipments')->find($delivery_note_shipment->shipment_id);
                $shipments[] = $shipment->tracking_number;
            }
            return ['status' => 0, 'success' => 'Delivery Note Shipments', 'shipments' => $shipments];
        } else {
            return ['status' => 0, 'success' => 'No Delivery Note Shipments', 'shipments' => FALSE];
        }
    }

    public function debriefing_index()
    {

        ActivityTrailController::createActivityTrailLog(Auth::id(), 161);
        $hubs = DB::table('cities')->where('hub', 1)->select('id', 'name')->get();
        $zones = DB::table('zones')->get();
        $shipping_modes = DB::connection('reports')->table('shipping_modes')->get();

        return view('admin.reports.debriefing_report')->with(['hubs' => $hubs, 'zones' => $zones, 'shipping_modes' => $shipping_modes]);
    }

    static function debriefing_data($date, $hub, $zone, $export = FALSE, $mode)
    {

        $settings = DB::table('global_settings')->where('type', 'debriefing_report_arrival_cut_off_time')->first();

        if ($settings) {
            $arrival_cut_off_time = $settings->setting_value;
        } else {
            $arrival_cut_off_time = 12;
        }

        $settings = DB::table('global_settings')->where('type', 'debriefing_report_day_cut_off_time')->first();

        if ($settings) {
            $day_cut_off_time = $settings->setting_value;
        } else {
            $day_cut_off_time = 12;
        }

        $hubs = DB::table('cities')->where('hub', 1)->where('business_category_id', 1)->select('id', 'name');

        if ($hub) {
            $hubs = $hubs->where('hub_id', '=', $hub);
        }

        if ($zone) {
            $hubs = $hubs->where('zone_id', '=', $zone);
        }

        if ($hubs->exists()) {
            $hubs = $hubs->get();

            if ($date) {
                $from_month = Carbon::parse($date)->subDays(30)->addHour($day_cut_off_time)->toDateTimeString();
                $from = Carbon::parse($date)->addHour($day_cut_off_time)->toDateTimeString();
                $to = Carbon::parse($date)->addDay()->addHour($day_cut_off_time)->subSecond()->toDateTimeString();
            } else {
                $from_month = Carbon::today()->subDays(30)->addHour($day_cut_off_time)->toDateTimeString();
                $from = Carbon::today()->addHour($day_cut_off_time)->toDateTimeString();
                $to = Carbon::tomorrow()->addHour($day_cut_off_time)->subSecond()->toDateTimeString();
            }

            $from_month_id = DB::table('shipments_journey')->select(DB::raw('MIN(id) as id'))->where('verification', 1)->where('created_at', '>=', $from_month)->first()->id;
            $from_id = DB::table('shipments_journey')->select(DB::raw('MIN(id) as id'))->where('verification', 1)->where('created_at', '>=', $from)->first()->id;
            $to_id = DB::table('shipments_journey')->select(DB::raw('MAX(id) as id'))->where('verification', 1)->where('created_at', '>=', $from)->where('created_at', '<=', $to)->first()->id;

            $dn_ids = DB::table('delivery_notes')->select('id')->where('status', 1)->whereBetween('status_verified_at', [$from, $to])->get()->pluck('id');

            $types = ['delivered', 'delivery_unsucessful', 'on_hold', 'status_not_attempted', 'fake_status', 'confirmation_pending', 'delivery_note_pending', 'delivery_tomorrow'];

            $counts = array();

            if ($export) {
                $shipments = array();
            }

            foreach ($hubs as $hub) {
                foreach ($types as $type) {
                    $rows = DB::table('cities');

                    if ($type == 'status_not_attempted' || $type == 'delivery_tomorrow') {
                        $rows = $rows->join('shipments as s', function ($join) {
                            $join->where(function ($query) {
                                $query->where('cities.id', '=', DB::raw('s.consignee_city_id'))
                                    ->orWhere(function ($sub_query) {
                                        $sub_query->on('cities.id', '=', DB::raw('(select usii.city_id from user_shipping_infos as usii where usii.id = s.pickup_address_id)'));
                                    });
                            });
                        })
                            ->join('user_shipping_infos as usi', 'usi.id', '=', 's.pickup_address_id')
                            ->join('cities as pc', 'usi.city_id', '=', 'pc.id')
                            ->leftjoin('cities as sch', 's.consignee_city_id', '=', 'sch.id')
                            ->leftjoin('zone_class_cities as zcc', function ($join) {
                                $join->on('pc.zone_id', '=', 'zcc.zone_id')
                                    ->on('s.consignee_city_id', '=', 'zcc.city_id');
                            });
                    } else {
                        $rows = $rows->join('shipments as s', 'cities.id', '=', 's.consignee_city_id');
                    }

                    if ($type == 'status_not_attempted') {
                        $rows = $rows->join('shipments_journey as sj', function ($join) use ($from_month_id, $to_id) {
                            $join->on('s.id', '=', 'sj.shipment_id')
                                ->where('sj.id', '=', DB::raw('(select max(shipments_journey.id) from shipments_journey where shipments_journey.shipment_id = s.id and shipments_journey.verification = 1 and shipments_journey.id >= "' . $from_month_id . '" and shipments_journey.id < "' . $to_id . '")'));
                        });
                    } else if ($type == 'fake_status') {
                        $rows = $rows->join('delivery_note_shipments as dns', function ($join) use ($dn_ids) {
                            $join->on('s.id', '=', 'dns.shipment_id')
                                ->whereIn('dns.delivery_note_id', $dn_ids);
                        });
                    } else if ($type == 'delivery_note_pending') {
                        $rows = $rows->join('shipments_journey as sj', function ($join) use ($from_month_id, $to_id) {
                            $join->on('s.id', '=', 'sj.shipment_id')
                                ->where('sj.id', '=', DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = s.id and shipments_journey.verification = 1 and shipments_journey.id >= "' . $from_month_id . '" and shipments_journey.id < "' . $to_id . '")'));
                        });
                    } else {
                        $rows = $rows->join('shipments_journey as sj', function ($join) use ($from_id, $to_id) {
                            $join->on('s.id', '=', 'sj.shipment_id')
                                ->where('sj.id', '=', DB::raw('(select max(shipments_journey.id) from shipments_journey where shipments_journey.shipment_id = s.id and shipments_journey.verification = 1 and shipments_journey.id >= "' . $from_id . '" and shipments_journey.id <= "' . $to_id . '")'));
                        });
                    }
                    if ($type == 'delivered') {
                        $rows = $rows->whereIn('sj.shipper_status_id', [14, 30, 36, 37]);
                    } else if ($type == 'delivery_unsucessful') {
                        $rows = $rows->where(function ($sub_query) use ($from) {
                            $sub_query->where('sj.shipper_status_id', '=', 8);
                        });
                    } else if ($type == 'on_hold') {
                        $rows = $rows->whereIn('sj.shipper_status_id', [9, 10, 11, 15]);
                    } else if ($type == 'status_not_attempted') {
                        $rows = $rows->where(function ($query) use ($arrival_cut_off_time, $from) {
                            $query->where(function ($sub_query) {
                                $sub_query->where('cities.id', '=', DB::raw('s.consignee_city_id'))
                                    ->where('sj.shipper_status_id', '=', 7);
                            })
                                ->orWhere(function ($sub_query) use ($arrival_cut_off_time, $from) {
                                    $sub_query->where(function ($sub_sub_query) use ($arrival_cut_off_time, $from) {
                                        $sub_sub_query->where(function ($sub_sub_sub_query) use ($arrival_cut_off_time) {
                                            $sub_sub_sub_query->where(function ($sub_sub_sub_sub_query) use ($arrival_cut_off_time) {
                                                $sub_sub_sub_sub_query->where(function ($sub_sub_sub_sub_sub_query) {
                                                    $sub_sub_sub_sub_sub_query->where('usi.city_id', '=', DB::raw('s.consignee_city_id'))
                                                        ->orWhereNull('zcc.class')
                                                        ->orWhereIn('zcc.class', [0, 1]);
                                                })
                                                    ->where(function ($sub_sub_sub_sub_sub_sub_sub_query) {
                                                        $sub_sub_sub_sub_sub_sub_sub_query->where('cities.id', '=', DB::raw('usi.city_id'))
                                                            ->where('sj.shipper_status_id', '=', 2);
                                                    });
                                            });
                                        })
                                            ->where(function ($sub_sub_sub_query) use ($arrival_cut_off_time, $from) {
                                                $sub_sub_sub_query->whereRaw('date(`sj`.`created_at`) < date(?)', [$from])
                                                    ->orWhere(function ($sub_sub_sub_sub_query) use ($arrival_cut_off_time, $from) {
                                                        $sub_sub_sub_sub_query->whereRaw('date(`sj`.`created_at`) = date(?)', [$from])
                                                            ->whereRaw('hour(`sj`.`created_at`) < ?', [$arrival_cut_off_time]);
                                                    });
                                            });
                                    });
                                })
                                ->orWhere(function ($sub_query) use ($arrival_cut_off_time, $from) {
                                    $sub_query->where('cities.id', '=', DB::raw('s.consignee_city_id'))
                                        ->whereIn('sj.shipper_status_id', [8, 13])
                                        ->whereRaw('date(`sj`.`created_at`) < date(?)', [$from]);
                                });
                            // ->orWhere(function ($sub_query) use ($arrival_cut_off_time, $from) {
                            //     $sub_query->where(function ($sub_sub_query) {
                            //         $sub_sub_query->where('usi.city_id', '=', DB::raw('sch.hub_id'))
                            //             ->whereIn('sj.shipper_status_id', [2,6,7,8,9,11,12,13,15])
                            //             ->whereDate(DB::raw('DATE_ADD(sj.created_at, INTERVAL cities.attempt_tat + IF ((WEEK(sj.created_at) <> WEEK(DATE_ADD(sj.created_at, INTERVAL cities.attempt_tat DAY))) OR (WEEKDAY(DATE_ADD(sj.created_at, INTERVAL cities.attempt_tat DAY)) IN (6)), 1 , 0) DAY)'), '<', Carbon::today());
                            //     })
                            //     ->orWhere(function ($sub_sub_sub_query) {
                            //         $sub_sub_sub_query->where('usi.city_id', '!=', DB::raw('sch.hub_id'))
                            //             ->whereIn('sj.shipper_status_id', [4,6,7,8,9,11,12,13,15])
                            //             ->whereDate(DB::raw('DATE_ADD(sj.created_at, INTERVAL cities.attempt_tat + IF ((WEEK(sj.created_at) <> WEEK(DATE_ADD(sj.created_at, INTERVAL cities.attempt_tat DAY))) OR (WEEKDAY(DATE_ADD(sj.created_at, INTERVAL cities.attempt_tat DAY)) IN (6)), 1 , 0) DAY)'), '<', Carbon::today());
                            //     });
                            // });
                        });
                    } else if ($type == 'confirmation_pending') {
                        $rows = $rows->where('sj.shipper_status_id', '=', 12);
                    } else if ($type == 'fake_status') {
                        $rows = $rows->where('dns.fake_status', '=', 1);
                    } else if ($type == 'delivery_note_pending') {
                        $rows = $rows->where('sj.shipper_status_id', '=', 5);
                    } else if ($type == 'delivery_tomorrow') {
                        $rows = $rows->where(function ($query) use ($arrival_cut_off_time, $from) {
                            $query->where(function ($sub_query) use ($arrival_cut_off_time) {
                                $sub_query->where(function ($sub_sub_query) {
                                    $sub_sub_query->where(function ($sub_sub_sub_query) {
                                        $sub_sub_sub_query->where('usi.city_id', '=', DB::raw('s.consignee_city_id'))
                                            ->where('cities.id', '=', DB::raw('usi.city_id'))
                                            ->where('sj.shipper_status_id', 2);
                                    })
                                        ->orWhere(function ($sub_sub_sub_query) {
                                            $sub_sub_sub_query->where('usi.city_id', '!=', DB::raw('s.consignee_city_id'))
                                                ->where('cities.id', '=', DB::raw('s.consignee_city_id'))
                                                ->where('sj.shipper_status_id', 4);
                                        });
                                })
                                    ->where(function ($sub_sub_query) use ($arrival_cut_off_time) {
                                        $sub_sub_query->where(function ($sub_sub_sub_query) use ($arrival_cut_off_time) {
                                            $sub_sub_sub_query->whereRaw('hour(`sj`.`created_at`) >= ?', [$arrival_cut_off_time])
                                                ->orWhereNull('zcc.class')
                                                ->orWhereIn('zcc.class', [2, 3]);
                                        });
                                    });
                            })
                                ->orWhere(function ($sub_query) use ($from) {
                                    $sub_query->where('cities.id', '=', DB::raw('s.consignee_city_id'))
                                        ->where('sj.shipper_status_id', '=', 13)
                                        ->whereRaw('date(`sj`.`created_at`) = date(?)', [$from]);
                                });
                            // ->orWhere(function ($sub_query) use ($arrival_cut_off_time, $from) {
                            //     $sub_query->where(function ($sub_sub_query) {
                            //         $sub_sub_query->where('usi.city_id', '=', DB::raw('sch.hub_id'))
                            //             ->whereIn('sj.shipper_status_id', [2,6,7,8,9,11,12,13,15])
                            //             ->whereDate(DB::raw('DATE_ADD(sj.created_at, INTERVAL cities.attempt_tat + IF ((WEEK(sj.created_at) <> WEEK(DATE_ADD(sj.created_at, INTERVAL cities.attempt_tat DAY))) OR (WEEKDAY(DATE_ADD(sj.created_at, INTERVAL cities.attempt_tat DAY)) IN (6)), 1 , 0) DAY)'), '>=', Carbon::today());
                            //     })
                            //     ->orWhere(function ($sub_sub_sub_query) {
                            //         $sub_sub_sub_query->where('usi.city_id', '!=', DB::raw('sch.hub_id'))
                            //             ->whereIn('sj.shipper_status_id', [4,6,7,8,9,11,12,13,15])
                            //             ->whereDate(DB::raw('DATE_ADD(sj.created_at, INTERVAL cities.attempt_tat + IF ((WEEK(sj.created_at) <> WEEK(DATE_ADD(sj.created_at, INTERVAL cities.attempt_tat DAY))) OR (WEEKDAY(DATE_ADD(sj.created_at, INTERVAL cities.attempt_tat DAY)) IN (6)), 1 , 0) DAY)'), '>=', Carbon::today());
                            //     });
                            // });
                        });
                    }

                    $rows = $rows->select('s.tracking_number')
                        ->where('cities.hub_id', $hub->id);

                    if ($mode) {
                        $rows = $rows->where('s.shipping_mode_id', '=', $mode);
                    }

                    if ($rows->exists()) {
                        $rows = $rows->groupBy('s.id');

                        $rows = $rows->get();

                        $counts[$hub->name][$type] = $rows->count();

                        if (!isset($counts['Grand Total'][$type])) {
                            $counts['Grand Total'][$type] = 0;
                        }

                        $counts['Grand Total'][$type] += $rows->count();

                        if ($type != 'fake_status') {
                            if ($type != 'delivery_note_pending' && $type != 'delivery_tomorrow') {
                                if (!isset($counts[$hub->name]['total_1'])) {
                                    $counts[$hub->name]['total_1'] = 0;
                                }

                                $counts[$hub->name]['total_1'] += $counts[$hub->name][$type];

                                if (!isset($counts['Grand Total']['total_1'])) {
                                    $counts['Grand Total']['total_1'] = 0;
                                }

                                $counts['Grand Total']['total_1'] += $counts[$hub->name][$type];
                            }

                            if ($type != 'delivery_tomorrow') {
                                if (!isset($counts[$hub->name]['total_2'])) {
                                    $counts[$hub->name]['total_2'] = 0;
                                }

                                $counts[$hub->name]['total_2'] += $counts[$hub->name][$type];

                                if (!isset($counts['Grand Total']['total_2'])) {
                                    $counts['Grand Total']['total_2'] = 0;
                                }

                                $counts['Grand Total']['total_2'] += $counts[$hub->name][$type];
                            }

                            if (!isset($counts[$hub->name]['grand_total'])) {
                                $counts[$hub->name]['grand_total'] = 0;
                            }

                            $counts[$hub->name]['grand_total'] += $counts[$hub->name][$type];

                            if (!isset($counts['Grand Total']['grand_total'])) {
                                $counts['Grand Total']['grand_total'] = 0;
                            }

                            $counts['Grand Total']['grand_total'] += $counts[$hub->name][$type];
                        }

                        if ($export) {
                            $shipments[$hub->name][$type] = array();

                            foreach ($rows as $row) {
                                $shipments[$hub->name][$type][] = $row->tracking_number;
                            }
                        }
                    } else {
                        $counts[$hub->name][$type] = 0;

                        if (!isset($counts['Grand Total'][$type])) {
                            $counts['Grand Total'][$type] = 0;
                        }
                    }
                }

                foreach ($counts as $hub => $count) {
                    if (isset($count['total_1']) && $count['total_1']) {
                        $counts[$hub]['total_1_ratio'] = round(($count['delivered'] / $count['total_1']) * 100);

                        if (!$export) {
                            $counts[$hub]['total_1_ratio'] .= '%';
                        } else {
                            $counts[$hub]['total_1_ratio'] = ($counts[$hub]['total_1_ratio'] / 100);
                        }
                    } else {
                        $counts[$hub]['total_1'] = 0;

                        if (!$export) {
                            $counts[$hub]['total_1_ratio'] = '0%';
                        } else {
                            $counts[$hub]['total_1_ratio'] = 0;
                        }
                    }

                    if (isset($count['total_2']) && $count['total_2']) {
                        $counts[$hub]['total_2_ratio'] = round(($count['delivered'] / $count['total_2']) * 100);

                        if (!$export) {
                            $counts[$hub]['total_2_ratio'] .= '%';
                        } else {
                            $counts[$hub]['total_2_ratio'] = ($counts[$hub]['total_2_ratio'] / 100);
                        }
                    } else {
                        $counts[$hub]['total_2'] = 0;

                        if (!$export) {
                            $counts[$hub]['total_2_ratio'] = '0%';
                        } else {
                            $counts[$hub]['total_2_ratio'] = 0;
                        }
                    }

                    if (isset($count['grand_total']) && $count['grand_total']) {
                        $counts[$hub]['grand_total_ratio'] = round(($count['delivered'] / $count['grand_total']) * 100);

                        if (!$export) {
                            $counts[$hub]['grand_total_ratio'] .= '%';
                        } else {
                            $counts[$hub]['grand_total_ratio'] = ($counts[$hub]['grand_total_ratio'] / 100);
                        }
                    } else {
                        $counts[$hub]['grand_total'] = 0;

                        if (!$export) {
                            $counts[$hub]['grand_total_ratio'] = '0%';
                        } else {
                            $counts[$hub]['grand_total_ratio'] = 0;
                        }
                    }
                }
            }

            if (!$export) {
                return ['status' => 0, 'success' => 'Shipments Found', 'counts' => $counts];
            } else {
                $grand_total_counts = $counts['Grand Total'];
                unset($counts['Grand Total']);
                $counts['Grand Total'] = $grand_total_counts;

                return ['status' => 0, 'success' => 'Shipments Found', 'counts' => $counts, 'shipments' => $shipments];
            }
        } else {
            return ['status' => 1, 'error' => 'No Shipments Found'];
        }
    }

    public function debriefing_list(Request $request)
    {
        $date = $request->get('search_date');
        $hub = $request->get('search_hub');
        $zone = $request->get('search_zone');
        $mode = $request->get('search_shipping_mode');

        return self::debriefing_data($date, $hub, $zone, NULL, $mode);
    }

    static public function debriefing_export_file($date, $hub, $zone, $mode, $report_type)
    {
        $file_name = 'debriefing_report_';

        $file_name .= $date;

        if ($hub) {
            $file_name .= '_' . $hub;
        }

        if ($zone) {
            $file_name .= '_' . $zone;
        }

        $file_name .= '.xlsx';

        $details = array();

        $details[] = ['Hubs', 'Delivered', 'Delivery Unsuccessful', 'On Hold', 'Status Not Attempted', 'Fake Status', 'Confirmation Pending', 'Total', 'Ratio', 'Delivery Note Pending', 'Total', 'Ratio', 'Delivery Tomorrow', 'Grand Total', 'Ratio'];

        $result = self::debriefing_data($date, $hub, $zone, TRUE, $mode);

        if ($result['status'] == 0) {
            $types = ['delivered', 'delivery_unsucessful', 'on_hold', 'status_not_attempted', 'fake_status', 'confirmation_pending', 'total_1', 'total_1_ratio', 'delivery_note_pending', 'total_2', 'total_2_ratio', 'delivery_tomorrow', 'grand_total', 'grand_total_ratio'];

            $type_names = ['delivered' => 'Delivered', 'delivery_unsucessful' => 'Delivery Unsuccessful', 'on_hold' => 'On Hold', 'status_not_attempted' => 'Status Not Attempted', 'fake_status' => 'Fake Status', 'confirmation_pending' => 'Confirmation Pending', 'total_1' => 'Total', 'total_1_ratio' => 'Ratio', 'delivery_note_pending' => 'Delivery Note Pending', 'total_2' => 'Total', 'total_2_ratio' => 'Ratio', 'delivery_tomorrow' => 'Delivery Tomorrow', 'grand_total' => 'Grand Total', 'grand_total_ratio' => 'Ratio'];
            foreach ($result['counts'] as $hub => $count) {
                $row = array();

                $row[] = $hub;

                foreach ($types as $type) {
                    if ($count[$type]) {
                        $row[] = $count[$type];
                    } else {
                        $row[] = '0';
                    }
                }

                $details[] = $row;
            }

            $spreadsheet = new Spreadsheet();

            $spreadsheet->getActiveSheet()->getStyle('B')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
            $spreadsheet->getActiveSheet()->getStyle('C')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
            $spreadsheet->getActiveSheet()->getStyle('D')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
            $spreadsheet->getActiveSheet()->getStyle('E')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
            $spreadsheet->getActiveSheet()->getStyle('F')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
            $spreadsheet->getActiveSheet()->getStyle('G')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
            $spreadsheet->getActiveSheet()->getStyle('H')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
            $spreadsheet->getActiveSheet()->getStyle('I')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE);
            $spreadsheet->getActiveSheet()->getStyle('J')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
            $spreadsheet->getActiveSheet()->getStyle('K')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
            $spreadsheet->getActiveSheet()->getStyle('L')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE);
            $spreadsheet->getActiveSheet()->getStyle('M')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
            $spreadsheet->getActiveSheet()->getStyle('N')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
            $spreadsheet->getActiveSheet()->getStyle('O')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE);

            $spreadsheet->getActiveSheet()->getStyle('A')->getFont()->setBold(TRUE)->getColor()->setARGB('0000FF');
            $spreadsheet->getActiveSheet()->getStyle('H')->getFont()->setBold(TRUE)->getColor()->setARGB('0000FF');
            $spreadsheet->getActiveSheet()->getStyle('K')->getFont()->setBold(TRUE)->getColor()->setARGB('0000FF');
            $spreadsheet->getActiveSheet()->getStyle('N')->getFont()->setBold(TRUE)->getColor()->setARGB('0000FF');
            $spreadsheet->getActiveSheet()->getStyle('I')->getFont()->setBold(TRUE)->getColor()->setARGB('0000FF');
            $spreadsheet->getActiveSheet()->getStyle('L')->getFont()->setBold(TRUE)->getColor()->setARGB('0000FF');
            $spreadsheet->getActiveSheet()->getStyle('O')->getFont()->setBold(TRUE)->getColor()->setARGB('0000FF');
            $spreadsheet->getActiveSheet()->getStyle('E')->getFont()->getColor()->setARGB('FFFF0000');
            $spreadsheet->getActiveSheet()->getStyle('F')->getFont()->getColor()->setARGB('FFFF0000');

            $spreadsheet->getActiveSheet()->setTitle('Overall')->fromArray($details, NULL);

            $highest_row = $spreadsheet->getActiveSheet()->getHighestRow();
            $highest_row = 'A' . $highest_row . ':O' . $highest_row;

            $spreadsheet->getActiveSheet()->getStyle('A1:O1')->getFont()->setBold(TRUE);
            $spreadsheet->getActiveSheet()->getStyle($highest_row)->getFont()->setBold(TRUE);

            $types = array();

            foreach ($result['shipments'] as $hub => $types) {
                $details = array();

                if (count($types) > 1) {
                    foreach ($types as $type => $tracking_numbers) {
                        $detail = array();

                        $detail[] = $type_names[$type];

                        foreach ($tracking_numbers as $tracking_number) {
                            $detail[] = $tracking_number;
                        }

                        $details[] = $detail;
                    }

                    $details = array_map(null, ...$details);
                } else {
                    foreach ($types as $type => $tracking_numbers) {
                        $details[] = [$type_names[$type]];

                        foreach ($tracking_numbers as $tracking_number) {
                            $details[] = [$tracking_number];
                        }
                    }
                }

                $spreadsheet->createSheet()->setTitle($hub);

                $spreadsheet->setActiveSheetIndexByName($hub);

                for ($counter = 1; $counter <= count($types); $counter++) {
                    $column_name = Coordinate::stringFromColumnIndex($counter);

                    $spreadsheet->getActiveSheet()->getStyle($column_name)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);

                    $spreadsheet->getActiveSheet()->getColumnDimension($column_name)->setWidth(15);
                }

                $spreadsheet->getActiveSheet()->setTitle($hub)->fromArray($details);
            }

            $spreadsheet->setActiveSheetIndex(0);

            $writer = new Xlsx($spreadsheet);
        } else {
            $spreadsheet = new Spreadsheet();
            $spreadsheet->getActiveSheet()->setTitle('Overall')->fromArray($details);

            $writer = new Xlsx($spreadsheet);
        }

        if ($report_type == 0) {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $file_name . '"');
            header('Cache-Control: max-age=0');

            $writer->save('php://output');
        } else if ($report_type == 1) {
            foreach ($result as $index => $res) {
                if ($index != 'status' && $index != 'success' && $index != 'Grand Total' && $index != 'shipments') {
                    foreach ($res as $hub_name => $data) {
                        if ($hub_name != 'Grand Total') {
                            $hub = City::where('name', $hub_name)->first();
                            Debriefing::where('hub', $hub->id)->delete();
                            $debriefing = new Debriefing();
                            $debriefing->hub = $hub->id;
                            $debriefing->zone_id = $hub->zone_id;
                            $debriefing->delivered = $data['delivered'];
                            $debriefing->delivery_unsuccessful = $data['delivery_unsucessful'];
                            $debriefing->on_hold = $data['on_hold'];
                            $debriefing->status_not_attempted = $data['status_not_attempted'];
                            $debriefing->fake_status = $data['fake_status'];
                            $debriefing->confirmation_pending = $data['confirmation_pending'];
                            $debriefing->delivery_note_pending = $data['delivery_note_pending'];
                            $debriefing->delivery_tomorrow = $data['delivery_tomorrow'];
                            $debriefing->total_1 = $data['total_1'];
                            $debriefing->total_1_ratio = $data['total_1_ratio'];
                            $debriefing->total_2 = $data['total_2'];
                            $debriefing->total_2_ratio = $data['total_2_ratio'];
                            $debriefing->grand_total = $data['grand_total'];
                            $debriefing->grand_total_ratio = $data['grand_total_ratio'];
                            $debriefing->save();
                        }
                    }
                }
            }
            ob_start();
            $writer->save('php://output');
            $contents = ob_get_contents();
            ob_end_clean();
            Storage::disk('public')->put('/reports/debriefing/hubs/' . $file_name, $contents);
        } else if ($report_type == 2) {
            ob_start();
            $writer->save('php://output');
            $contents = ob_get_contents();
            ob_end_clean();
            Storage::disk('public')->put('/reports/debriefing/zones/' . $file_name, $contents);
        } else if ($report_type == 3) {
            ob_start();
            $writer->save('php://output');
            $contents = ob_get_contents();
            ob_end_clean();
            Storage::disk('public')->put('/reports/debriefing/overall/' . $file_name, $contents);
        }
    }

    public function debriefing_export(Request $request)
    {
        $date = $request->get('search_date');
        $hub = $request->get('search_hub');
        $zone = $request->get('search_zone');
        $mode = $request->get('search_shipping_mode');
        ActivityTrailController::createActivityTrailLog(Auth::id(), 162);
        return self::debriefing_export_file($date, $hub, $zone, $mode, 0);
    }

    static public function debriefing_hub_wise_report($date)
    {
        $hubs = DB::table('cities')->where('hub', 1)->pluck('id');
        if ($hubs) {
            $zone = NULL;
            $mode = NULL;

            foreach ($hubs as $hub_id) {
                self::debriefing_export_file($date, $hub_id, $zone, $mode, 1);
            }
        }
        return true;
    }

    static public function debriefing_zone_wise_report($date)
    {
        $hubs = NULL;
        $mode = NULL;

        $zone = DB::table('zones')->where('status', 1)->pluck('id');
        if ($zone) {
            foreach ($zone as $zone_id) {
                AdminReportsController::debriefing_export_file($date, $hubs, $zone_id, $mode, 2);
            }
        }
        return true;
    }

    static public function debriefing_overall_report($date)
    {
        $hubs = NULL;
        $mode = NULL;

        $zone = NULL;

        AdminReportsController::debriefing_export_file($date, $hubs, $zone, $mode, 3);

        return true;
    }

    static public function debriefing_archive_directory()
    {


        $hub_files = File::glob(public_path() . '/storage/reports/debriefing/hubs/*.*');
        $now = Carbon::now();
        foreach ($hub_files as $file) {
            if (is_file($file)) {
                $created = date("F d Y H:i:s.", filemtime($file));
                $file_name = pathinfo($file);
                if ($now->diffInDays($created) > 7) {
                    File::delete($file);
                }
            }
        }

        $zone_files = File::glob(public_path() . '/storage/reports/debriefing/zones/*.*');
        $now = Carbon::now();
        foreach ($zone_files as $file) {
            if (is_file($file)) {
                $created = date("F d Y H:i:s.", filemtime($file));
                $file_name = pathinfo($file);
                if ($now->diffInDays($created) > 7) {
                    File::delete($file);
                }
            }
        }

        $overall_files = File::glob(public_path() . '/storage/reports/debriefing/overall/*.*');
        $now = Carbon::now();
        foreach ($overall_files as $file) {
            if (is_file($file)) {
                $created = date("F d Y H:i:s.", filemtime($file));
                $file_name = pathinfo($file);
                if ($now->diffInDays($created) > 7) {
                    File::delete($file);
                }
            }
        }
    }

    public function cargo_returns_shipment_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 163);
        $cities = DB::connection('reports')->table('cities')->select('id', 'name')->where('hub', 1)->get();
        $shipping_modes = DB::connection('reports')->table('shipping_modes')->get(['id', 'mode']);
        return view('admin.reports.cargo_returns_shipment_report')->with(['cities' => $cities, 'shipping_modes' => $shipping_modes]);
    }

    public function cargo_returns_shipment_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 164);
        }
        $cargo_returns_Shipment = DB::connection('reports')->table('shipments')->join('shipments_journey as sj', function ($join) {
            $join->on('sj.shipment_id', '=', 'shipments.id')
                ->where('sj.id', '=', DB::connection('reports')->raw('(select max(id) from shipments_journey where shipment_id = shipments.id and shipments_journey.shipper_status_id in (20, 30, 37))'));
        })
            ->leftjoin('shipments_journey as sjc', function ($join) {
                $join->on('sjc.shipment_id', '=', 'shipments.id')
                    ->where('sjc.id', '=', DB::connection('reports')->raw('(select max(id) from shipments_journey where shipment_id = shipments.id and shipments_journey.shipper_status_id in (21, 26, 32))'));
            })
            ->leftjoin('cargo_consignments as cc', 'cc.id', '=', 'sjc.reference_1_id')
            ->leftjoin('cargo_consignment_shipments as ccs', function ($join) {
                $join->on('ccs.shipment_id', '=', 'shipments.id')
                    ->where('ccs.cargo_consignment_id', '=', 'cc.id');
            })
            ->join('user_shipping_infos as usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities as shipment_origin_city', 'shipment_origin_city.id', '=', 'usi.city_id')
            ->join('cities as shipment_origin_hub', 'shipment_origin_hub.id', '=', 'shipment_origin_city.hub_id')
            ->join('cities as shipment_destination_city', function ($join) {
                $join->on('shipments.consignee_city_id', '=', 'shipment_destination_city.id')
                    ->on('shipment_origin_city.hub_id', '!=', 'shipment_destination_city.hub_id');
            })
            ->join('cities as shipment_destination_hub', 'shipment_destination_hub.id', '=', 'shipment_destination_city.hub_id')
            ->join('shipment_status as ss', 'ss.id', '=', 'shipments.shipper_status_id')
            ->select('shipments.tracking_number as tracking_number', 'ss.name as status', 'ss.id as status_id', 'sj.created_at as return_confirm_date', 'sjc.created_at as dispatching_aging', 'cc.id as cargo_no', 'cc.created_at as cargo_creation_date', 'shipment_origin_city.name as shipment_origin_city_name', 'shipment_origin_hub.name as shipment_origin_hub_name', 'shipment_destination_city.name as shipment_destination_city_name', 'shipment_destination_hub.name as shipment_destination_hub_name')
            ->whereIn('shipments.shipper_status_id', [20, 21, 26, 30, 32, 37]);
        $cargo_returns_Shipment = Datatables::of($cargo_returns_Shipment)
            ->editColumn('dispatching_aging', function ($shipments) {
                $from = Carbon::parse($shipments->dispatching_aging);
                $days = Carbon::now()->diffInDays($from);
                if ($days == 0) {
                    return "-";
                } else {
                    return $days;
                }
            })
            ->addColumn('cargo_id_padded_link', function ($shipments) {
                if ($shipments->cargo_no != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle print"><i class="la la-lg la-print align-middle"></i> <span class="align-middle">' . str_pad($shipments->cargo_no, 6, '0', STR_PAD_LEFT) . '</span></button>';
                } else {
                    return '-';
                }
            })
            ->editColumn('tracking_number_link', function ($shipments) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->editColumn('return_confirm_aging', function ($shipments) {
                $from = Carbon::parse($shipments->return_confirm_date);
                $days = Carbon::now()->diffInDays($from);
                if ($days == 0) {
                    return "-";
                } else {
                    return $days;
                }
            });

        if ($origin = $request->get('search_origin')) {
            $cargo_returns_Shipment->where('shipment_destination_hub.id', '=', $origin);
        }


        if ($destination = $request->get('search_destination')) {
            $cargo_returns_Shipment->where('shipment_origin_hub.id', '=', $destination);
        }

        if ($status = $request->get('search_status')) {
            $cargo_returns_Shipment->where('ss.id', '=', $status);
        }

        if ($mode = $request->get('search_shipping_mode')) {
            $cargo_returns_Shipment->where('shipments.booking_type_id', '=', $mode);
        }

        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');

            $cargo_returns_Shipment->whereBetween('sj.created_at', [$from, $to]);
        }
        return $cargo_returns_Shipment->make(true);
    }


    public function return_reattempt_ratio_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 165);
        $cities = DB::connection('reports')->table('cities')->where('status', 1)->get();
        $shipping_modes = DB::connection('reports')->table('shipping_modes')->get(['id', 'mode']);
        return view('admin.reports.return_reattempt_ratio')->with(['cities' => $cities, 'shipping_modes' => $shipping_modes]);
    }

    public function return_reattempt_ratio_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 166);
        }
        $shipments = DB::connection('reports')->table('return_reattempt_ratios')->join('shipments', 'shipments.id', '=', 'return_reattempt_ratios.shipment_id')
            ->join('users as u', 'shipments.user_id', '=', 'u.id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->join('cities as h', 'dc.hub_id', '=', 'h.id')
            ->join('booking_types as bt', 'bt.id', '=', 'shipments.booking_type_id')
            ->join('shipment_status as ss', 'ss.id', '=', 'shipments.shipper_status_id')
            ->leftJoin('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where('sj.id', '=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)'));
            })
            ->leftJoin('shipments_journey as journey', function ($join) {
                $join->on('journey.shipment_id', '=', 'shipments.id')
                    ->where('journey.id', '=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id)'));
            })
            ->select(['shipments.id as shId', 'shipments.tracking_number', 'shipments.tracking_number as tracking_number_link', 'u.name as shipper', 'u.poc as poc', 'ss.name as current_status', 'bt.booking_type as service_type', 'sj.created_at as arrival', 'oc.name as origin', 'dc.name as destination', 'h.name as hub', 'shipments.amount', 'journey.created_at as current_status_date', 'shipments.booking_type_id', 'return_reattempt_ratios.return_confirm_date', 'return_reattempt_ratios.created_at as reattempt_date', 'u.id as account_no']);
        if (session('role_id') != 1) {
            $shipments = $shipments->whereIn('dc.hub_id', session('hubs'));
        }

        $datatable = Datatables::of($shipments)
            ->editColumn('tracking_number_link', function ($shipments) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->editColumn('amount', function ($shipment) {
                return number_format($shipment->amount);
            })
            ->editColumn('account_no', function ($shipments) {
                return str_pad($shipments->account_no, 6, '0', STR_PAD_LEFT);
            })
            ->editColumn('shipper', function ($shipment) {
                if ($shipment->booking_type_id == 4) {
                    return $shipment->shipper . ' (' . $shipment->poc . ')';
                } else {
                    return $shipment->shipper;
                }
            })
            ->addColumn('reversion_aging', function ($shipments) {

                $days = Carbon::parse($shipments->return_confirm_date)->diffInDays($shipments->reattempt_date);
                if ($days == 0) {
                    return "-";
                } else {
                    return $days;
                }
            })
            ->addColumn('aging_current_status', function ($shipments) {

                $days = Carbon::parse($shipments->reattempt_date)->diffInDays($shipments->current_status_date);
                if ($days == 0) {
                    return "-";
                } else {
                    return $days;
                }
            });
        if ($city = $request->get('search_city')) {
            $datatable->where('dc.id', '=', $city);
        }

        if ($mode = $request->get('search_shipping_mode')) {
            $datatable->where('shipments.booking_type_id', '=', $mode);
        }

        if ($request->get('search_from') && $request->get('search_to')) {
            $from = $request->get('search_from');
            $to = $request->get('search_to');
            $datatable->whereBetween('return_confirm_date', [$from, $to]);
        }

        return $datatable->make(true);
    }

    public function multiple_payment_report_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 167);
        return view('admin.reports.multiple_payment_report');
    }

    public function multiple_payment_report_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 168);
        }
        $payments = DB::connection('reports')->table('done_payment_shipments')->join('shipments as s', 's.id', '=', 'done_payment_shipments.shipment_id')->select(['s.tracking_number as tracking_number', 's.actual_weight as actual_weight', 's.cash_handling_charges as cash_handling_charges', 's.insurance_charges as insurance_charges', 's.return_charges as return_charges', 's.fuel_surcharge as fuel_surcharge', 's.replacement_charges as replacement_charges', 's.packaging_material_charges as packaging_material_charges', 'done_payment_shipments.done_payment_id as payment_id', 'done_payment_shipments.gst as gst', 'done_payment_shipments.amount as amount', 'done_payment_shipments.payable as total_payable', 'done_payment_shipments.type as status', 's.nsa_osa_charges as nsa_osa_charges']);
        $datatable = Datatables::of($payments)
            ->addColumn('id_padded', function ($shipments) {
                return str_pad($shipments->payment_id, 6, '0', STR_PAD_LEFT);
            })
            ->editColumn('payment_id_link', function ($shipments) {
                return '<button class="btn btn-sm btn-outline-info align-middle"><i class="la la-lg la-print align-middle"></i> <span class="align-middle">' . str_pad($shipments->payment_id, 6, '0', STR_PAD_LEFT) . '</span></button>';
            })
            ->editColumn('cash_handling_charges', function ($shipments) {
                return number_format($shipments->cash_handling_charges, 2);
            })
            ->editColumn('insurance_charges', function ($shipments) {
                return number_format($shipments->insurance_charges, 2);
            })
            ->editColumn('return_charges', function ($shipments) {
                return number_format($shipments->return_charges, 2);
            })
            ->editColumn('fuel_surcharge', function ($shipments) {
                return number_format($shipments->fuel_surcharge, 2);
            })
            ->editColumn('replacement_charges', function ($shipments) {
                return number_format($shipments->replacement_charges, 2);
            })
            ->editColumn('nsa_osa_charges', function ($shipment) {
                return number_format($shipment->nsa_osa_charges, 2);
            })
            ->editColumn('packaging_material_charges', function ($shipments) {
                return number_format($shipments->packaging_material_charges, 2);
            })
            ->editColumn('gst', function ($shipments) {
                return number_format($shipments->gst, 2);
            })
            ->editColumn('amount', function ($shipments) {
                return number_format($shipments->amount, 2);
            })
            ->editColumn('total_payable', function ($shipments) {
                return number_format($shipments->total_payable, 2);
            })
            ->editColumn('tracking_number_link', function ($shipments) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->editColumn('status', function ($shipments) {
                if ($shipments->status == 0) {
                    return 'Delivered';
                } elseif ($shipments->status == 1) {
                    return 'Returned';
                } else {
                    return 'Adjusted';
                }

            });
        if ($tracking_number = $request->get('tracking_number')) {
            $datatable->where('s.tracking_number', '=', $tracking_number);
        }
        return $datatable->make(true);
    }

    public function revenue_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 169);
        $shippers = DB::connection('reports')->table('users')->whereIn('status', [3, 4])->select('id', 'name')->get();
        $cities = DB::connection('reports')->table('cities')->select('id', 'name')->get();
        $hubs = DB::connection('reports')->table('cities')->where('hub', 1)->select('id', 'name')->get();
        $statuses = DB::connection('reports')->table('shipment_status')->whereNotIn('id', [1, 17])->get();
        $shipping_modes = DB::connection('reports')->table('shipping_modes')->get(['id', 'mode']);
        $business_categories = DB::connection('reports')->table('business_categories')->select('id', 'name')->get();
        return view('admin.reports.revenue')->with(['business_categories' => $business_categories, 'shippers' => $shippers, 'cities' => $cities, 'hubs' => $hubs, 'statuses' => $statuses, 'shipping_modes' => $shipping_modes]);
    }

    public function revenue_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 170);
        }

        if (Auth::id() == 3) {
            $connection = 'mysql';
        } else {
            $connection = 'reports';
        }

        $count = DB::connection($connection)->table('shipments')
            ->join('users as u', 'u.id', '=', 'shipments.user_id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id');

        if ($request->get('dr_search_date_from') && $request->get('dr_search_date_to')) {
            $from = $request->get('dr_search_date_from');
            $to = $request->get('dr_search_date_to');

            $count = $count->join('shipments_journey as dr', function ($join) use ($from, $to, $connection) {
                $join->on('dr.shipment_id', '=', 'shipments.id')
                    ->whereIn('dr.shipper_status_id', [14,25,30,36,37])
                    ->where('dr.id', '=',
                        DB::connection($connection)->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id In(14,20,30,36,37) and shipments_journey.verification = 1 and shipments_journey.created_at between "' . $from . '" and "' . $to . '")'));
            })
                ->whereBetween('dr.created_at', [$from, $to]);
        }

        if ($tracking = $request->get('search_tracking')) {
            $count = $count->where('shipments.tracking_number', '=', $tracking);
        }
        if ($shipper = $request->get('search_shipper')) {
            $count = $count->where('u.id', '=', $shipper);
        }
        if ($origin = $request->get('search_origin')) {
            $count = $count->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
                ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
                ->where('oc.id', '=', $origin);
        }
        if ($destination = $request->get('search_destination')) {
            $count = $count->where('dc.id', '=', $destination);
        }
        if ($mode = $request->get('search_shipping_mode')) {
            $count = $count->where('shipments.booking_type_id', '=', $mode);
        }
        if ($hub = $request->get('search_hub')) {
            $count = $count->join('cities as h', 'dc.hub_id', '=', 'h.id')
                ->where('h.id', '=', $hub);
        }
        if ($status = $request->get('search_status')) {
            $count = $count->join('shipment_status as ss', 'ss.id', '=', 'shipments.shipper_status_id')
                ->where('ss.id', '=', $status);
        }

        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');

            $count = $count->join('shipments_journey as sj', function ($join) use ($connection) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where('sj.shipper_status_id', 2)
                    ->where('sj.id', '=',
                        DB::connection($connection)->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)'));
            })
                ->whereBetween('sj.created_at', [$from, $to]);
        }

        if ($search_business_category = $request->get('search_business_category')) {
            $count = $count->where('shipments.business_category_id', '=', $search_business_category);
        }

        if (!($request->get('search_date_from') && $request->get('search_date_to')) && !($request->get('dr_search_date_from') && $request->get('dr_search_date_to'))) {
            $count = $count->whereRaw('false');
        }

        $count = $count->whereNotIn('shipments.shipper_status_id', [1, 17])
            ->whereNotIn('u.id', [8761, 9358]);

        if (session('role_id') != 1 && (!in_array(session('id'), session('sale_users_bypass')))) {
            if (session('department_id') == 7) {
                $count = $count->whereIn('u.id', session('tagged_shippers'));
            } else {
                $count = $count->whereIn('dc.hub_id', session('hubs'));
            }
        }

        $count = $count->count();

        $sales = DB::connection($connection)->table('shipments')
            ->join('users as u', 'u.id', '=', 'shipments.user_id')
            ->join('shipment_status as ss', 'ss.id', '=', 'shipments.shipper_status_id')
            ->join('booking_types as bt', 'bt.id', '=', 'shipments.booking_type_id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->leftjoin('zones as z', 'z.id', '=', 'oc.zone_id')
            ->leftjoin('user_shipping_infos as rsi', 'shipments.return_address_id', '=', 'rsi.id')
            ->leftjoin('cities as rc', 'rsi.city_id', '=', 'rc.id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->join('cities as h', 'dc.hub_id', '=', 'h.id')
            ->join('business_categories as bc', 'shipments.business_category_id', '=', 'bc.id')
            ->leftjoin('zone_class_cities as zcc', function ($join) use ($connection) {
                $join->on('z.id', '=', 'zcc.zone_id')
                    ->on('dc.id', '=', 'zcc.city_id')
                    ->on('zone_classification_id', '=', DB::connection($connection)->raw('IF (shipments.shipping_mode_id IN (1, 4), 1, 2)'));
            })
            ->join('shipping_modes as sm', 'sm.id', '=', 'shipments.shipping_mode_id')
            ->leftjoin('shipment_payment_status as sps', 'shipments.payment_status_id', '=', 'sps.id')
            ->leftjoin('delivery_note_shipments as ds', function ($join) {
                $join->on('ds.shipment_id', '=', 'shipments.id')
                    ->where('ds.delivery_note_id', '=',
                        DB::connection('reports')->raw('(select max(delivery_note_id) from delivery_note_shipments where delivery_note_shipments.shipment_id = shipments.id and delivery_note_shipments.status > 3 and  delivery_note_shipments.status != 8)'));
            })
            ->leftjoin('delivery_note_station_deposit_notes as dnsdn', 'ds.delivery_note_id', '=', 'dnsdn.delivery_note_id')
            ->leftJoin('shipments_journey as sj', function ($join) use ($connection) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where('sj.shipper_status_id', 2)
                    ->where('sj.id', '=',
                        DB::connection($connection)->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)'));
            })
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
            ->leftJoin('pending_invoice_shipments as pis', function ($join) use ($connection) {
                $join->on('pis.shipment_id', '=', 'shipments.id')
                    ->where('pis.id', '=',
                        DB::connection($connection)->raw('(select max(id) from pending_invoice_shipments where pending_invoice_shipments.shipment_id = shipments.id and pending_invoice_shipments.type != 2)'));
            })
            ->leftJoin('invoice_shipments as is', function ($join) use ($connection) {
                $join->on('is.shipment_id', '=', 'shipments.id')
                    ->where('is.id', '=',
                        DB::connection($connection)->raw('(select max(id) from invoice_shipments where invoice_shipments.shipment_id = shipments.id and invoice_shipments.type != 2)'));
            })
            ->leftjoin('invoices', 'is.invoice_id', '=', 'invoices.id');
//        if (!$request->get('search_date_from') && !$request->get('search_date_to')) {
//            $now = Carbon::now();
//            $yesterday = Carbon::now()->subDays(3);
//            $sales = $sales->whereBetween('sj.created_at', [$yesterday,$now]);
//        }

        if ($request->get('dr_search_date_from') && $request->get('dr_search_date_to')) {
            $from = $request->get('dr_search_date_from');
            $to = $request->get('dr_search_date_to');

            $sales->join('shipments_journey as dr', function ($join) use ($from, $to, $connection) {
                $join->on('dr.shipment_id', '=', 'shipments.id')
                    ->whereIn('dr.shipper_status_id', [14,25,30,36,37])
                    ->where('dr.id', '=',
                        DB::connection($connection)->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id In(14,20,30,36,37) and shipments_journey.verification = 1 and shipments_journey.created_at between "' . $from . '" and "' . $to . '")'));
            });
            $sales->whereBetween('dr.created_at', [$from, $to]);
        } else {
            $sales->leftJoin('shipments_journey as dr', function ($join) use ($connection) {
                $join->on('dr.shipment_id', '=', 'shipments.id')
                    ->whereIn('dr.shipper_status_id', [14,25,30,36,37])
                    ->where('dr.id', '=',
                        DB::connection($connection)->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id In(14,20,30,36,37) and shipments_journey.verification = 1)'));
            });
        }

        $sales->select('shipments.tracking_number', 'shipments.order_id as order_id', 'shipments.tracking_number as tracking_number_link', 'u.id as account_no', 'u.name as shipper', 'ss.name as current_status', 'bt.booking_type as service_type', 'sj.created_at as arrival_date', 'oc.name as origin', 'dc.name as destination', 'h.name as hub', 'shipments.amount as s_collection_amount', 'sps.name as payment_status', 'pps.amount as p_collection_amount', 'shipments.actual_weight', 'shipments.weight_charges', 'shipments.cash_handling_charges', 'shipments.insurance_charges', 'shipments.return_charges', 'shipments.replacement_charges', 'shipments.fuel_surcharge', 'shipments.try_and_buy_charges', 'shipments.packaging_material_charges', 'pps.gst as p_gst', 'pps.charges as p_total_charges', 'pps.payable as p_net_payable', 'dps.amount as d_collection_amount', 'dps.gst as d_gst', 'dps.charges as d_total_charges', 'dps.payable as d_net_payable', 'sm.mode as shipping_mode', 'shipments.chargeable_weight', 'dr.created_at as delivered_or_returned', 'z.name as zone', 'zcc.class', 'oc.id as origin_city_id', 'dc.id as destination_city_id', 'dnsdn.station_deposit_note_id as sdn_id', 'dps.done_payment_id as payment_id', 'shipments.booking_type_id', 'usi.poc', 'shipments.shipper_status_id as shipment_status', 'shipments.nsa_osa_charges', 'u.account_type_id as account_type_id', 'pis.gst as pis_gst', 'is.gst as is_gst', 'shipments.packaging_charges', 'shipments.intercept_charges', 'bc.name', 'dr.shipper_status_id as dr_status_id', 'shipments.shipment_type', 'invoices.invoice_number', 'rc.name as return_city')
            ->whereNotIn('shipments.shipper_status_id', [1, 17])
            ->whereNotIn('u.id', [8761, 9358]);

        if (session('role_id') != 1 && (!in_array(session('id'), session('sale_users_bypass')))) {
            if (session('department_id') == 7) {
                $sales = $sales->whereIn('u.id', session('tagged_shippers'));
            } else {
                $sales = $sales->whereIn('dc.hub_id', session('hubs'));
            }
        }

        $datatable = Datatables::of($sales)
            ->setTotalRecords($count)
            ->editColumn('insurance_charges', function ($shipment) {
                return number_format($shipment->insurance_charges, 2);
            })
            ->addColumn('invoice_number_button', function ($sales) {
                return '<button class="btn btn-sm btn-outline-info align-middle">' . $sales->invoice_number . '</button>';
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
            ->editColumn('account_no', function ($shipments) {
                return str_pad($shipments->account_no, 6, '0', STR_PAD_LEFT);
            })
            ->editColumn('return_charges', function ($shipment) {
                if ($shipment->dr_status_id != 20) {
                    return "-";
                } else {
                    return number_format($shipment->return_charges, 2);
                }
            })
            ->editColumn('intercept_charges', function ($shipment) {
                return number_format($shipment->intercept_charges, 2);
            })
            ->editColumn('weight_charges', function ($shipment) {
                return number_format($shipment->weight_charges, 2);
            })
            ->editColumn('fuel_surcharge', function ($shipment) {
                return number_format($shipment->fuel_surcharge, 2);
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
            ->editColumn('tracking_number_link', function ($shipments) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->editColumn('s_collection_amount', function ($shipment) {
                return number_format($shipment->s_collection_amount, 2);
            })
            ->editColumn('d_collection_amount', function ($shipment) {
                return number_format($shipment->d_collection_amount, 2);
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
                if (in_array($sale->shipment_status, [14, 20, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 45, 46, 25])) {
                    return $sale->delivered_or_returned;
                } else {
                    return '';
                }
            });

        if ($tracking = $request->get('search_tracking')) {
            $datatable->where('shipments.tracking_number', '=', $tracking);
        }
        if ($shipper = $request->get('search_shipper')) {
            $datatable->where('u.id', '=', $shipper);
        }
        if ($origin = $request->get('search_origin')) {
            $datatable->where('oc.id', '=', $origin);
        }
        if ($destination = $request->get('search_destination')) {
            $datatable->where('dc.id', '=', $destination);
        }
        if ($mode = $request->get('search_shipping_mode')) {
            $datatable->where('shipments.booking_type_id', '=', $mode);
        }
        if ($hub = $request->get('search_hub')) {
            $datatable->where('h.id', '=', $hub);
        }
        if ($status = $request->get('search_status')) {
            $datatable->where('ss.id', '=', $status);
        }

        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $datatable->whereBetween('sj.created_at', [$from, $to]);
        }

        if (!($request->get('search_date_from') && $request->get('search_date_to')) && !($request->get('dr_search_date_from') && $request->get('dr_search_date_to'))) {
            $datatable->whereRaw('false');
        }

        if ($search_business_category = $request->get('search_business_category')) {
            $datatable->where('shipments.business_category_id', '=', $search_business_category);
        }
        return $datatable->make(true);
    }

    public static function revenue_excel_download()
    {

//        $from = Carbon::today()->subMonth(1)->firstOfMonth()->toDateTimeString();
//        $to = Carbon::today()->subMonth(1)->endOfMonth()->toDateTimeString();
        $date = Carbon::parse('01-12-2022 00:00:00')->toDateTimeString();
        $from = Carbon::parse($date)->addDays(25)->endOfDay()->toDateTimeString();
        $to = Carbon::parse($date)->addDays(31)->endOfDay()->toDateTimeString();

        //Next

        /*        $from = Carbon::today()->subMonth(1)->toDateTimeString();
                $to = Carbon::parse($from)->addMonth(1)->addDay(1)->endOfDay()->toDateTimeString();*/

        $sales = DB::connection('reports')->table('shipments')->join('users as u', 'u.id', '=', 'shipments.user_id')
            ->join('shipment_status as ss', 'ss.id', '=', 'shipments.shipper_status_id')
            ->join('booking_types as bt', 'bt.id', '=', 'shipments.booking_type_id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->leftjoin('zones as z', 'z.id', '=', 'oc.zone_id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->join('cities as h', 'dc.hub_id', '=', 'h.id')
            ->join('business_categories as bc', 'shipments.business_category_id', '=', 'bc.id')
            ->leftjoin('zone_class_cities as zcc', function ($join) {
                $join->on('z.id', '=', 'zcc.zone_id')
                    ->on('dc.id', '=', 'zcc.city_id')
                    ->on('zone_classification_id', '=', DB::connection('reports')->raw('IF (shipments.shipping_mode_id IN (1, 4), 1, 2)'));
            })
            ->join('shipping_modes as sm', 'sm.id', '=', 'shipments.shipping_mode_id')
            ->leftjoin('shipment_payment_status as sps', 'shipments.payment_status_id', '=', 'sps.id')
            ->leftjoin('delivery_note_shipments as ds', function ($join) {
                $join->on('ds.shipment_id', '=', 'shipments.id')
                    ->where('ds.delivery_note_id', '=',
                        DB::connection('reports')->raw('(select max(delivery_note_id) from delivery_note_shipments where delivery_note_shipments.shipment_id = shipments.id and delivery_note_shipments.status > 3 and  delivery_note_shipments.status != 8)'));
            })
            ->leftjoin('delivery_note_station_deposit_notes as dnsdn', 'ds.delivery_note_id', '=', 'dnsdn.delivery_note_id')
            ->leftJoin('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where('sj.id', '=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)'));
            })
            ->leftJoin('pending_payment_shipments as pps', function ($join) {
                $join->on('pps.shipment_id', '=', 'shipments.id')
                    ->where('pps.id', '=',
                        DB::connection('reports')->raw('(select max(id) from pending_payment_shipments where pending_payment_shipments.shipment_id = shipments.id and pending_payment_shipments.type != 2)'));
            })
            ->leftJoin('done_payment_shipments as dps', function ($join) {
                $join->on('dps.shipment_id', '=', 'shipments.id')
                    ->where('dps.id', '=',
                        DB::connection('reports')->raw('(select max(id) from done_payment_shipments where done_payment_shipments.shipment_id = shipments.id and done_payment_shipments.type != 2)'));
            })
            ->leftJoin('pending_invoice_shipments as pis', function ($join) {
                $join->on('pis.shipment_id', '=', 'shipments.id')
                    ->where('pis.id', '=',
                        DB::connection('reports')->raw('(select max(id) from pending_invoice_shipments where pending_invoice_shipments.shipment_id = shipments.id and pending_invoice_shipments.type != 2)'));
            })
            ->leftJoin('invoice_shipments as is', function ($join) {
                $join->on('is.shipment_id', '=', 'shipments.id')
                    ->where('is.id', '=',
                        DB::connection('reports')->raw('(select max(id) from invoice_shipments where invoice_shipments.shipment_id = shipments.id and invoice_shipments.type != 2)'));
            })
            ->leftJoin('shipments_journey as dr', function ($join) {
                $join->on('dr.shipment_id', '=', 'shipments.id')
                    ->where('dr.id', '=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id In(14,20,30,36,37) and shipments_journey.verification = 1)'));
            })
            ->select('shipments.tracking_number', 'u.id as account_no', 'bc.name as buisness_category', 'u.name as shipper', 'shipments.order_id as order_id', 'ss.name as current_status', 'sps.name as payment_status', 'dps.done_payment_id as payment_number', 'dnsdn.station_deposit_note_id as sdn_number', 'bt.booking_type as service_type', 'sj.created_at as arrival_date', 'oc.name as origin', 'dc.name as destination', 'h.name as hub', 'z.name as zone', 'zcc.class', 'sm.mode as shipping_mode', 'pps.amount as p_collection_amount', 'shipments.actual_weight', 'shipments.chargeable_weight', 'shipments.weight_charges', 'shipments.cash_handling_charges', 'shipments.insurance_charges', 'shipments.packaging_material_charges', 'shipments.fuel_surcharge', 'shipments.return_charges', 'shipments.replacement_charges', 'shipments.packaging_charges', 'shipments.try_and_buy_charges', 'shipments.nsa_osa_charges', 'shipments.intercept_charges', 'pps.gst as p_gst', 'pps.charges as p_total_charges', 'pps.payable as p_net_payable', 'shipments.amount as s_collection_amount', 'dps.amount as d_collection_amount', 'dps.gst as d_gst', 'dps.charges as d_total_charges', 'dps.payable as d_net_payable', 'dr.created_at as delivered_or_returned', 'oc.id as origin_city_id', 'dc.id as destination_city_id', 'shipments.booking_type_id', 'usi.poc', 'shipments.shipper_status_id as shipment_status', 'u.account_type_id as account_type_id', 'pis.gst as pis_gst', 'is.gst as is_gst', 'dr.shipper_status_id as dr_status_id', 'shipments.shipment_type')
            ->whereNotIn('shipments.shipper_status_id', [1, 17])
            ->whereNotIn('u.id', [8761, 9358])
            ->whereBetween('sj.created_at', [$from, $to])
            ->get();


        $filename = 'sonic_monthly_shipper_revenue_report.xlsx';

        $details = array();

        $details[] = ['S.No.', 'Tracking Number', 'Account No.', 'Business Category', 'Shipper', 'Order Id', 'Status', 'Payment Status', 'Payment Number', 'SDN Number', 'Service Type', 'Arrival Date', 'Origin', 'Destination', 'Hub', 'Zone', 'Class', 'Shipping Mode', 'Collection Amount', 'Actual Weight', 'Chargeable Weight', 'Weight Charges', 'Cash Handling Charges', 'Insurance Charges', 'Packaging Charges', 'Fuel Surcharge', 'Return Charges', 'Replacement Charges', 'Packing Charges', 'Try & Buy Charges', 'NSA/OSA Charges', 'Intercept Charges', 'GST', 'Total Charges', 'Estimated Charges', 'Net Payable', 'Delivered/Returned Date'];

        $serial_number = 1;
        foreach ($sales as $index => $sale) {
            if ($sale->dr_status_id == 20) {
                $cash_handling_charges = "-";
                $return_charges = number_format($sale->return_charges, 2);
                $replacement_charges = "-";
                $try_and_buy_charges = "-";
            } else {
                if ($sale->cash_handling_charges != null) {
                    $cash_handling_charges = number_format($sale->cash_handling_charges, 2);
                } else {
                    $cash_handling_charges = "-";
                }
                $return_charges = "-";
                $replacement_charges = number_format($sale->replacement_charges, 2);
                $try_and_buy_charges = number_format($sale->try_and_buy_charges, 2);
            }

            $insurance_charges = number_format($sale->insurance_charges, 2);
            $account_number = str_pad($sale->account_no, 6, '0', STR_PAD_LEFT);
            $intercept_charges = number_format($sale->intercept_charges, 2);
            $weight_charges = number_format($sale->weight_charges, 2);
            $fuel_surcharge = number_format($sale->fuel_surcharge, 2);
            $nsa_osa_charges = number_format($sale->nsa_osa_charges, 2);
            $packaging_material_charges = number_format($sale->packaging_material_charges, 2);
            $p_total_charges = number_format($sale->p_total_charges, 2);
            $d_total_charges = number_format($sale->d_total_charges, 2);
            $p_net_payable = number_format($sale->p_net_payable, 2);
            $d_net_payable = number_format($sale->d_net_payable, 2);
            $d_gst = number_format($sale->d_gst, 2);
            $packaging_charges = number_format($sale->packaging_charges, 2);
            $s_collection_amount = number_format($sale->s_collection_amount, 2);
            $d_collection_amount = number_format($sale->d_collection_amount, 2);
            if ($sale->booking_type_id == 4) {
                $shipper = $sale->shipper . ' (' . $sale->poc . ')';
            } else {
                $shipper = $sale->shipper;
            }
            $amount = '';
            if ($sale->p_collection_amount != null) {
                $amount = $sale->p_collection_amount;
            } else if ($sale->d_collection_amount != null) {
                $amount = $sale->d_collection_amount;
            } else {
                $amount = $sale->s_collection_amount;
            }
            $collection_amount = number_format($amount);
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
            $gst = number_format((float)$gst, 2);
            $total = '';
            if ($sale->p_total_charges != null) {
                $total = $sale->p_total_charges;
            } else if ($sale->d_total_charges != null) {
                $total = $sale->d_total_charges;
            }
            $total_charges = number_format((float)$total, 2);
            $estimated = '';
            $estimated = (($sale->weight_charges != null) ? $sale->weight_charges : 0) + (($sale->cash_handling_charges != null) ? $sale->cash_handling_charges : 0) + (($sale->insurance_charges != null) ? $sale->insurance_charges : 0) + (($sale->insurance_charges != null) ? $sale->insurance_charges : 0) + (($sale->return_charges != null) ? $sale->return_charges : 0) + (($sale->replacement_charges != null) ? $sale->replacement_charges : 0) + (($sale->fuel_surcharge != null) ? $sale->fuel_surcharge : 0) + (($sale->try_and_buy_charges != null) ? $sale->try_and_buy_charges : 0) + (($sale->packaging_material_charges != null) ? $sale->packaging_material_charges : 0) + (($sale->intercept_charges != null) ? $sale->intercept_charges : 0);
            $estimated_charges = number_format((float)$estimated, 2);
            $payable = '';
            if ($sale->p_net_payable != null) {
                $payable = $sale->p_net_payable;
            } else if ($sale->d_net_payable != null) {
                $payable = $sale->d_net_payable;
            }
            $net_payable = number_format((float)$payable, 2);
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

            $row = array();

            $row[] = $serial_number;
            $row[] = $sale->tracking_number;
            $row[] = $account_number;
            $row[] = $sale->buisness_category;
            $row[] = $shipper;
            $row[] = $sale->order_id;
            $row[] = $sale->current_status;
            $row[] = $sale->payment_status;
            $row[] = $sale->payment_number;
            $row[] = $sale->sdn_number;
            $row[] = $sale->service_type;
            $row[] = $sale->arrival_date;
            $row[] = $sale->origin;
            $row[] = $sale->destination;
            $row[] = $sale->hub;
            $row[] = $sale->zone;
            $row[] = $class;
            $row[] = $sale->shipping_mode;
            $row[] = $collection_amount;
            $row[] = $sale->actual_weight;
            $row[] = $sale->chargeable_weight;
            $row[] = $weight_charges;
            $row[] = $cash_handling_charges;
            $row[] = $insurance_charges;
            $row[] = $packaging_material_charges;
            $row[] = $fuel_surcharge;
            $row[] = $return_charges;
            $row[] = $replacement_charges;
            $row[] = $packaging_charges;
            $row[] = $try_and_buy_charges;
            $row[] = $nsa_osa_charges;
            $row[] = $intercept_charges;
            $row[] = $gst;
            $row[] = $total_charges;
            $row[] = $estimated_charges;
            $row[] = $net_payable;
            $row[] = $sale->delivered_or_returned;

            $details[] = $row;
            $serial_number++;
        }

        $spreadsheet = new Spreadsheet();

        $spreadsheet->getActiveSheet()->getStyle('B')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('C')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('F')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('I')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('J')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('S')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('T')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('U')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('V')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('W')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('X')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('Y')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('Z')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('AA')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('AB')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('AC')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('AD')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('AE')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('AF')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('AG')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('AH')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('AI')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('AJ')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);

        $spreadsheet->getActiveSheet()->getStyle('A1:AK1')->getFont()->setBold(TRUE);

        $spreadsheet->getActiveSheet()->fromArray($details);

        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        ob_start();
        $writer->save('php://output');
        $contents = ob_get_contents();
        ob_end_clean();
        Storage::disk('public')->put('/reports/revenue/' . $filename, $contents);
        return true;
    }

    public static function revenue_by_delivery_date_excel_download()
    {

        /*$from = Carbon::today()->subMonth(1)->firstOfMonth()->toDateTimeString();
        $to = Carbon::today()->subMonth(1)->endOfMonth()->toDateTimeString();*/
        $from = Carbon::today()->firstOfMonth()->toDateTimeString();
        $to = Carbon::parse($from)->addDays(24)->endOfDay()->toDateTimeString();

        //Next

/*        $from = Carbon::today()->subMonth(1)->toDateTimeString();
        $to = Carbon::parse($from)->addMonth(1)->addDay(1)->endOfDay()->toDateTimeString();*/
        $sales = DB::connection('reports')->table('shipments')->join('users as u', 'u.id', '=', 'shipments.user_id')
            ->join('shipment_status as ss', 'ss.id', '=', 'shipments.shipper_status_id')
            ->join('booking_types as bt', 'bt.id', '=', 'shipments.booking_type_id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->leftjoin('zones as z', 'z.id', '=', 'oc.zone_id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->join('cities as h', 'dc.hub_id', '=', 'h.id')
            ->join('business_categories as bc', 'shipments.business_category_id', '=', 'bc.id')
            ->leftjoin('zone_class_cities as zcc', function ($join) {
                $join->on('z.id', '=', 'zcc.zone_id')
                    ->on('dc.id', '=', 'zcc.city_id')
                    ->on('zone_classification_id', '=', DB::connection('reports')->raw('IF (shipments.shipping_mode_id IN (1, 4), 1, 2)'));
            })
            ->join('shipping_modes as sm', 'sm.id', '=', 'shipments.shipping_mode_id')
            ->leftjoin('shipment_payment_status as sps', 'shipments.payment_status_id', '=', 'sps.id')
            ->leftjoin('delivery_note_shipments as ds', function ($join) {
                $join->on('ds.shipment_id', '=', 'shipments.id')
                    ->where('ds.delivery_note_id', '=',
                        DB::connection('reports')->raw('(select max(delivery_note_id) from delivery_note_shipments where delivery_note_shipments.shipment_id = shipments.id and delivery_note_shipments.status > 3 and  delivery_note_shipments.status != 8)'));
            })
            ->leftjoin('delivery_note_station_deposit_notes as dnsdn', 'ds.delivery_note_id', '=', 'dnsdn.delivery_note_id')
            ->leftJoin('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where('sj.id', '=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)'));
            })
            ->leftJoin('pending_payment_shipments as pps', function ($join) {
                $join->on('pps.shipment_id', '=', 'shipments.id')
                    ->where('pps.id', '=',
                        DB::connection('reports')->raw('(select max(id) from pending_payment_shipments where pending_payment_shipments.shipment_id = shipments.id and pending_payment_shipments.type != 2)'));
            })
            ->leftJoin('done_payment_shipments as dps', function ($join) {
                $join->on('dps.shipment_id', '=', 'shipments.id')
                    ->where('dps.id', '=',
                        DB::connection('reports')->raw('(select max(id) from done_payment_shipments where done_payment_shipments.shipment_id = shipments.id and done_payment_shipments.type != 2)'));
            })
            ->leftJoin('pending_invoice_shipments as pis', function ($join) {
                $join->on('pis.shipment_id', '=', 'shipments.id')
                    ->where('pis.id', '=',
                        DB::connection('reports')->raw('(select max(id) from pending_invoice_shipments where pending_invoice_shipments.shipment_id = shipments.id and pending_invoice_shipments.type != 2)'));
            })
            ->leftJoin('invoice_shipments as is', function ($join) {
                $join->on('is.shipment_id', '=', 'shipments.id')
                    ->where('is.id', '=',
                        DB::connection('reports')->raw('(select max(id) from invoice_shipments where invoice_shipments.shipment_id = shipments.id and invoice_shipments.type != 2)'));
            })
            ->leftJoin('shipments_journey as dr', function ($join) {
                $join->on('dr.shipment_id', '=', 'shipments.id')
                    ->where('dr.id', '=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id In(14,20,30,36,37) and shipments_journey.verification = 1)'));
            })
            ->select('shipments.tracking_number', 'u.id as account_no', 'bc.name as buisness_category', 'u.name as shipper', 'shipments.order_id as order_id', 'ss.name as current_status', 'sps.name as payment_status', 'dps.done_payment_id as payment_number', 'dnsdn.station_deposit_note_id as sdn_number', 'bt.booking_type as service_type', 'sj.created_at as arrival_date', 'oc.name as origin', 'dc.name as destination', 'h.name as hub', 'z.name as zone', 'zcc.class', 'sm.mode as shipping_mode', 'pps.amount as p_collection_amount', 'shipments.actual_weight', 'shipments.chargeable_weight', 'shipments.weight_charges', 'shipments.cash_handling_charges', 'shipments.insurance_charges', 'shipments.packaging_material_charges', 'shipments.fuel_surcharge', 'shipments.return_charges', 'shipments.replacement_charges', 'shipments.packaging_charges', 'shipments.try_and_buy_charges', 'shipments.nsa_osa_charges', 'shipments.intercept_charges', 'pps.gst as p_gst', 'pps.charges as p_total_charges', 'pps.payable as p_net_payable', 'shipments.amount as s_collection_amount', 'dps.amount as d_collection_amount', 'dps.gst as d_gst', 'dps.charges as d_total_charges', 'dps.payable as d_net_payable', 'dr.created_at as delivered_or_returned', 'oc.id as origin_city_id', 'dc.id as destination_city_id', 'shipments.booking_type_id', 'usi.poc', 'shipments.shipper_status_id as shipment_status', 'u.account_type_id as account_type_id', 'pis.gst as pis_gst', 'is.gst as is_gst', 'dr.shipper_status_id as dr_status_id', 'shipments.shipment_type')
            ->whereNotIn('shipments.shipper_status_id', [1, 17])
            ->whereNotIn('u.id', [8761, 9358])
            ->whereBetween('dr.created_at', [$from, $to])
            ->get();


        $filename = 'sonic_monthly_revenue_by_delivery_return_report.xlsx';

        $details = array();

        $details[] = ['S.No.', 'Tracking Number', 'Account No.', 'Business Category', 'Shipper', 'Order Id', 'Status', 'Payment Status', 'Payment Number', 'SDN Number', 'Service Type', 'Arrival Date', 'Origin', 'Destination', 'Hub', 'Zone', 'Class', 'Shipping Mode', 'Collection Amount', 'Actual Weight', 'Chargeable Weight', 'Weight Charges', 'Cash Handling Charges', 'Insurance Charges', 'Packaging Charges', 'Fuel Surcharge', 'Return Charges', 'Replacement Charges', 'Packing Charges', 'Try & Buy Charges', 'NSA/OSA Charges', 'Intercept Charges', 'GST', 'Total Charges', 'Estimated Charges', 'Net Payable', 'Delivered/Returned Date'];

        $serial_number = 1;
        foreach ($sales as $index => $sale) {
            if ($sale->dr_status_id == 20) {
                $cash_handling_charges = "-";
                $return_charges = number_format($sale->return_charges, 2);
                $replacement_charges = "-";
                $try_and_buy_charges = "-";
            } else {
                if ($sale->cash_handling_charges != null) {
                    $cash_handling_charges = number_format($sale->cash_handling_charges, 2);
                } else {
                    $cash_handling_charges = "-";
                }
                $return_charges = "-";
                $replacement_charges = number_format($sale->replacement_charges, 2);
                $try_and_buy_charges = number_format($sale->try_and_buy_charges, 2);
            }

            $insurance_charges = number_format($sale->insurance_charges, 2);
            $account_number = str_pad($sale->account_no, 6, '0', STR_PAD_LEFT);
            $intercept_charges = number_format($sale->intercept_charges, 2);
            $weight_charges = number_format($sale->weight_charges, 2);
            $fuel_surcharge = number_format($sale->fuel_surcharge, 2);
            $nsa_osa_charges = number_format($sale->nsa_osa_charges, 2);
            $packaging_material_charges = number_format($sale->packaging_material_charges, 2);
            $p_total_charges = number_format($sale->p_total_charges, 2);
            $d_total_charges = number_format($sale->d_total_charges, 2);
            $p_net_payable = number_format($sale->p_net_payable, 2);
            $d_net_payable = number_format($sale->d_net_payable, 2);
            $d_gst = number_format($sale->d_gst, 2);
            $packaging_charges = number_format($sale->packaging_charges, 2);
            $s_collection_amount = number_format($sale->s_collection_amount, 2);
            $d_collection_amount = number_format($sale->d_collection_amount, 2);
            if ($sale->booking_type_id == 4) {
                $shipper = $sale->shipper . ' (' . $sale->poc . ')';
            } else {
                $shipper = $sale->shipper;
            }
            $amount = '';
            if ($sale->p_collection_amount != null) {
                $amount = $sale->p_collection_amount;
            } else if ($sale->d_collection_amount != null) {
                $amount = $sale->d_collection_amount;
            } else {
                $amount = $sale->s_collection_amount;
            }
            $collection_amount = number_format($amount);
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
            $gst = number_format((float)$gst, 2);
            $total = '';
            if ($sale->p_total_charges != null) {
                $total = $sale->p_total_charges;
            } else if ($sale->d_total_charges != null) {
                $total = $sale->d_total_charges;
            }
            $total_charges = number_format((float)$total, 2);
            $estimated = '';
            $estimated = (($sale->weight_charges != null) ? $sale->weight_charges : 0) + (($sale->cash_handling_charges != null) ? $sale->cash_handling_charges : 0) + (($sale->insurance_charges != null) ? $sale->insurance_charges : 0) + (($sale->insurance_charges != null) ? $sale->insurance_charges : 0) + (($sale->return_charges != null) ? $sale->return_charges : 0) + (($sale->replacement_charges != null) ? $sale->replacement_charges : 0) + (($sale->fuel_surcharge != null) ? $sale->fuel_surcharge : 0) + (($sale->try_and_buy_charges != null) ? $sale->try_and_buy_charges : 0) + (($sale->packaging_material_charges != null) ? $sale->packaging_material_charges : 0) + (($sale->intercept_charges != null) ? $sale->intercept_charges : 0);
            $estimated_charges = number_format((float)$estimated, 2);
            $payable = '';
            if ($sale->p_net_payable != null) {
                $payable = $sale->p_net_payable;
            } else if ($sale->d_net_payable != null) {
                $payable = $sale->d_net_payable;
            }
            $net_payable = number_format((float)$payable, 2);
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

            $row = array();

            $row[] = $serial_number;
            $row[] = $sale->tracking_number;
            $row[] = $account_number;
            $row[] = $sale->buisness_category;
            $row[] = $shipper;
            $row[] = $sale->order_id;
            $row[] = $sale->current_status;
            $row[] = $sale->payment_status;
            $row[] = $sale->payment_number;
            $row[] = $sale->sdn_number;
            $row[] = $sale->service_type;
            $row[] = $sale->arrival_date;
            $row[] = $sale->origin;
            $row[] = $sale->destination;
            $row[] = $sale->hub;
            $row[] = $sale->zone;
            $row[] = $class;
            $row[] = $sale->shipping_mode;
            $row[] = $collection_amount;
            $row[] = $sale->actual_weight;
            $row[] = $sale->chargeable_weight;
            $row[] = $weight_charges;
            $row[] = $cash_handling_charges;
            $row[] = $insurance_charges;
            $row[] = $packaging_material_charges;
            $row[] = $fuel_surcharge;
            $row[] = $return_charges;
            $row[] = $replacement_charges;
            $row[] = $packaging_charges;
            $row[] = $try_and_buy_charges;
            $row[] = $nsa_osa_charges;
            $row[] = $intercept_charges;
            $row[] = $gst;
            $row[] = $total_charges;
            $row[] = $estimated_charges;
            $row[] = $net_payable;
            $row[] = $sale->delivered_or_returned;

            $details[] = $row;
            $serial_number++;
        }

        $spreadsheet = new Spreadsheet();

        $spreadsheet->getActiveSheet()->getStyle('B')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('C')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('F')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('I')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('J')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('S')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('T')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('U')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('V')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('W')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('X')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('Y')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('Z')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('AA')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('AB')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('AC')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('AD')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('AE')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('AF')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('AG')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('AH')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('AI')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('AJ')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);

        $spreadsheet->getActiveSheet()->getStyle('A1:AK1')->getFont()->setBold(TRUE);

        $spreadsheet->getActiveSheet()->fromArray($details);

        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        ob_start();
        $writer->save('php://output');
        $contents = ob_get_contents();
        ob_end_clean();
        Storage::disk('public')->put('/reports/revenue/' . $filename, $contents);
        return true;
    }

    public function gst_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 171);
        return view('admin.reports.gst_report');
    }

    public function gst_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 172);
        }
        $gst = DB::connection('reports')->table('done_payments')->leftjoin('done_payment_shipments as dps', 'dps.done_payment_id', '=', 'done_payments.id')
            ->leftjoin('users as u', 'done_payments.user_id', '=', 'u.id')
            ->select('u.id as account_no', 'u.name as user_name', 'u.ntn_no as ntn_number', DB::connection('reports')->raw('SUM(dps.charges) as w_o_gst'), DB::connection('reports')->raw('SUM(dps.gst) as gst'), DB::connection('reports')->raw('SUM(dps.payable) as total_charges'))
            ->groupBy('done_payments.user_id');
        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $gst->whereBetween('dps.created_at', [$from, $to]);
        }
        $gst_corporate = DB::connection('reports')->table('invoices')->leftjoin('invoice_shipments as is', 'is.invoice_id', '=', 'invoices.id')
            ->leftjoin('users as u', 'invoices.user_id', '=', 'u.id')
            ->select('u.id as account_no', 'u.name as user_name', 'u.ntn_no as ntn_number', DB::connection('reports')->raw('SUM(is.charges) as w_o_gst'), DB::connection('reports')->raw('SUM(is.gst) as gst'), DB::connection('reports')->raw('SUM(is.invoice_amount) as total_charges'))
            ->union($gst)
            ->where('u.account_type_id', 2)
            ->groupBy('invoices.user_id');
        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $gst_corporate->whereBetween('is.created_at', [$from, $to]);
        }

        $datatables = Datatables::of($gst_corporate)
            ->editColumn('account_no', function ($gst) {
                return str_pad($gst->account_no, 6, '0', STR_PAD_LEFT);
            })
            ->editColumn('ntn_number', function ($gst) {
                if ($gst->ntn_number) {
                    return $gst->ntn_number;
                } else {
                    return "-";
                }
            })
            ->editColumn('total_charges', function ($gst) {
                return number_format($gst->total_charges, 2);
            })
            ->editColumn('w_o_gst', function ($gst) {
                return number_format($gst->w_o_gst, 2);
            })
            ->editColumn('gst', function ($gst) {
                return number_format($gst->gst, 2);
            });
        return $datatables->make(true);
    }

    public function crm_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 173);
        $shippers = DB::connection('reports')->table('users')->whereIn('status', [3, 4])->select('id', 'name')->get();
        $cities = DB::connection('reports')->table('cities')->select('id', 'name')->get();
        $hubs = DB::connection('reports')->table('cities')->where('hub', 1)->select('id', 'name')->get();
        $zones = DB::connection('reports')->table('zones')->get();
        $agents = DB::connection('reports')->table('admin_roles')->leftjoin('admins as a', 'a.role_id', '=', 'admin_roles.id')
            ->where('admin_roles.department_id', 3)->get();
        $case_natures = DB::connection('reports')->table('crm_request_case_nature')->select('id', 'name')->get();
        $case_nature_types = DB::connection('reports')->table('crm_request_case_nature_types')->select('id', 'type')->get();
        $statuses = DB::connection('reports')->table('crm_request_statuses')->select('id', 'name')->whereNotIn('id', [6, 7])->get();
        $shipping_modes = DB::connection('reports')->table('shipping_modes')->get(['id', 'mode']);
        return view('admin.reports.crm_report')->with(['shippers' => $shippers, 'cities' => $cities, 'hubs' => $hubs, 'agents' => $agents, 'case_natures' => $case_natures, 'case_nature_types' => $case_nature_types, 'statuses' => $statuses, 'shipping_modes' => $shipping_modes, 'zones' => $zones]);
    }

    public function crm_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 174);
        }
        $crm = DB::connection('reports')->table('crm_requests')->leftjoin('shipments as s', 's.id', '=', 'crm_requests.shipment_id')
            ->leftjoin('shipment_status as ss', 'ss.id', '=', 's.shipper_status_id')
            ->leftjoin('crm_request_statuses as crs', 'crs.id', '=', 'crm_requests.status_id')
            ->leftjoin('crm_request_case_nature as crcn', 'crcn.id', '=', 'crm_requests.case_nature_id')
            ->leftjoin('crm_request_case_nature_types as crcnt', 'crcnt.id', '=', 'crm_requests.case_nature_type_id')
            ->leftjoin('users as u', 'u.id', '=', 'crm_requests.shipper_id')
            ->leftjoin('user_shipping_infos AS usi', 'usi.id', '=', 's.pickup_address_id')
            ->leftjoin('cities as oc', 'oc.id', '=', 'usi.city_id')
            ->leftjoin('cities as dc', 'dc.id', '=', 's.consignee_city_id')
            ->leftjoin('cities as h', 'h.id', '=', 'dc.hub_id')
            ->leftjoin('zones as z', 'z.id', '=', 'dc.zone_id')
            ->leftjoin('crm_request_channels as crc', 'crc.id', '=', 'crm_requests.channel_id')
            ->leftjoin('admins as a', 'a.id', '=', 'crm_requests.agent_id')
            ->leftJoin('admins as al', function ($join) {
                $join->on('al.id', '=', 'crm_requests.launched_by_id')
                    ->where('crm_requests.launched_by', '=', DB::raw(0));
            })
            ->leftjoin('admin_roles as ar', 'ar.id', '=', 'al.role_id')
            ->leftjoin('admin_departments as ad', 'ad.id', '=', 'ar.department_id')
            ->leftJoin('users as us', function ($join) {
                $join->on('us.id', '=', 'crm_requests.launched_by_id')
                    ->where('crm_requests.launched_by', '=', DB::raw(1));
            })
            ->leftJoin('substitute_users as su', function ($join) {
                $join->on('su.id', '=', 'crm_requests.launched_by_id')
                    ->where('crm_requests.launched_by', '=', DB::raw(2));
            })
            ->leftjoin('crm_request_agent_histories as crah', function ($join) {
                $join->on('crah.crm_request_id', '=', 'crm_requests.id')
                    ->where('crah.id', '=', DB::connection('reports')->raw('(select max(id) from crm_request_agent_histories where crm_request_id = crm_requests.id and agent_id = crm_requests.agent_id)'));
            })
            ->leftjoin('crm_request_status_histories as crshv', function ($join) {
                $join->on('crshv.crm_request_id', '=', 'crm_requests.id')
                    ->where('crshv.id', '=', DB::connection('reports')->raw('(select min(id) from crm_request_status_histories where crm_request_id = crm_requests.id and status_id = 6)'));
            })
            ->leftjoin('crm_request_status_histories as crshiv', function ($join) {
                $join->on('crshiv.crm_request_id', '=', 'crm_requests.id')
                    ->where('crshiv.id', '=', DB::connection('reports')->raw('(select min(id) from crm_request_status_histories where crm_request_id = crm_requests.id and status_id = 7)'));
            })
            ->leftjoin('crm_request_status_histories as crshr', function ($join) {
                $join->on('crshr.crm_request_id', '=', 'crm_requests.id')
                    ->where('crshr.id', '=', DB::connection('reports')->raw('(select max(id) from crm_request_status_histories where crm_request_id = crm_requests.id and status_id = 3)'));
            })
            ->leftjoin('crm_request_status_histories as crshc', function ($join) {
                $join->on('crshc.crm_request_id', '=', 'crm_requests.id')
                    ->where('crshc.id', '=', DB::connection('reports')->raw('(select max(id) from crm_request_status_histories where crm_request_id = crm_requests.id and status_id = 4)'));
            })
            ->leftJoin('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 'crm_requests.shipment_id')
                    ->where('sj.id', '=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = crm_requests.shipment_id and shipments_journey.shipper_status_id = 2)'));
            })
            ->leftjoin('crm_request_taggings as crt', 'crt.crm_request_id', '=', 'crm_requests.id')
            ->leftjoin('crm_request_tagging_histories as crth', function ($join) {
                $join->on('crth.crm_request_id', '=', 'crm_requests.id')
                    ->where('crth.id', '=',
                        DB::raw('(select max(id) from crm_request_tagging_histories where crm_request_tagging_histories.crm_request_id = crm_requests.id)'));
            })
            ->leftjoin('crm_request_status_histories as crsh', function ($join) {
                $join->on('crsh.crm_request_id', '=', 'crm_requests.id')
                    ->where('crsh.created_at', '=', DB::raw('(select max(created_at) from crm_request_status_histories where crm_request_status_histories.crm_request_id = crm_requests.id and crm_request_status_histories.status_id = 5)'));
            })
            ->leftjoin('admins as crta', 'crta.id', '=', 'crt.tagged_id')
            ->leftjoin('admin_departments as crtad', 'crtad.id', '=', 'crt.tagged_id')
            ->leftjoin('cities as crtadh', 'crtadh.id', '=', 'crt.hub_id')
            ->leftjoin('adjustment_logs as adjustment', function ($join) {
                $join->on('adjustment.shipment_id', '=', 'crm_requests.shipment_id')
                    ->where('adjustment.created_at', '=', DB::raw('(select max(created_at) from adjustment_logs where adjustment_logs.shipment_id = crm_requests.shipment_id and adjustment_logs.adjustment_type_id IN (4,6,7,8,9,10,11) )'));
            })
            ->leftjoin('change_shipment_weight_logs', function ($join) {
                $join->on('change_shipment_weight_logs.shipment_id', '=', 'crm_requests.shipment_id')
                    ->where('change_shipment_weight_logs.created_at', '=', DB::raw('(select max(created_at) from change_shipment_weight_logs where change_shipment_weight_logs.shipment_id= crm_requests.shipment_id)'));
            })
            ->leftjoin('crm_comments as ccs', function ($join) {
                $join->on('ccs.crm_request_id', '=', 'crm_requests.id')
                    ->where('ccs.id', '=', DB::raw('(select max(id) from crm_comments where crm_comments.crm_request_id = crm_requests.id AND crm_comments.comment_type = 1)'));
            })
            ->leftjoin('admins as accs', 'accs.id', '=', 'ccs.comment_by_id')
            ->leftjoin('crm_comments as ccse', function ($join) {
                $join->on('ccse.crm_request_id', '=', 'crm_requests.id')
                    ->where('ccse.id', '=', DB::raw('(select max(id) from crm_comments where crm_comments.crm_request_id = crm_requests.id AND crm_comments.comment_type = 0)'));
            })
            ->leftJoin('shipments_journey as sjcc', function ($join) {
                $join->on('sjcc.shipment_id', '=', 'crm_requests.shipment_id')
                    ->where('sjcc.id', '=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = crm_requests.shipment_id and shipments_journey.shipper_status_id = 51)'));
            })
            ->leftjoin('crm_request_feedbacks as crf', 'crf.crm_request_id', '=', 'crm_requests.id')
            ->leftjoin('crm_request_ratings as crr', 'crr.id', '=', 'crf.rating_id')
            
            ->leftjoin('month_closings as mc', 'mc.shipment_id', '=', 's.id')
            
            
            ->select('ccse.created_at as last_comment_date_external', 'ccse.comment as last_comment_external','crm_requests.id as request_number', 's.tracking_number as tracking_number','crsh.created_at as reopen_date','crcn.id as case_nature_id','crcn.name as case_nature','crcnt.type as case_nature_type', 'crm_requests.description as description', 'u.name as shipper_name', 'oc.name as origin', 'dc.name as destination', 'h.name as hub', 'crc.channel as channel', 'a.name as agent', 'al.name as name', 'us.name as shipper', 'su.name as sub_shipper', 'crm_requests.launched_by as launched_by_type', 'crm_requests.created_at as launched_date', 'crah.created_at as assigned_date', 'crshv.created_at as valid_date', 'crshiv.created_at as invalid_date', 'crshr.created_at as resolved_date', 'crshc.created_at as closed_date', 'crm_requests.status_id as current_status_id', 'crs.name as request_status', 'sj.created_at as arrival_date', 'ss.name as status', 'crta.name as tagged_to_admin', 'crtad.name as tagged_to_department', 'crtadh.name as tagged_to_hub', 'crt.crm_request_tagging_type_id as tagging_type', 'crth.created_at as tagged_at', 'z.name as zone','s.amount as cod_amount','adjustment.adjustment_amount as adjusted_amount','change_shipment_weight_logs.new_charges as weight_charges', 'ccs.comment as last_comment', 'ccs.created_at as last_comment_date', 'ccs.comment_by as last_comment_by', 'accs.name as last_comment_admin','ad.name as admin_department','sjcc.remarks as case_closed_remark', 'crr.name as rating', 'crr.code as rating_code','mc.id as month_closing_id')
            ->groupBy('crm_requests.id');

        if (session('department_id') == 8) {
            $crm = $crm->where('s.shipment_type',2);
        }

        $datatable = Datatables::of($crm)
            ->editColumn('tagged_to', function ($crm_request) {
                if ($crm_request->tagging_type == 2) {
                    return $crm_request->tagged_to_admin;
                } else if ($crm_request->tagging_type == 1) {
                    return $crm_request->tagged_to_department;
                } else {
                    return '-';
                }
            })
            ->editColumn('tagged_at', function ($crm_request) {
                if ($crm_request->tagging_type != null) {
                    return $crm_request->tagged_at;
                } else {
                    return '-';
                }
            })
            ->editColumn('tagged_hub', function ($crm_request) {
                if ($crm_request->tagging_type == 1) {
                    return $crm_request->tagged_to_hub;
                } else {
                    return '-';
                }
            })
            ->editColumn('tagged_aging', function ($crm_request) {
                Carbon::setWeekendDays([
                    Carbon::SUNDAY,
                ]);
                if ($crm_request->tagged_at) {
                    if ($crm_request->resolved_date) {
                        $tagged_at = Carbon::parse($crm_request->tagged_at);
                        $resolved_date = Carbon::parse($crm_request->resolved_date);
                        $days = $resolved_date->diffInWeekdays($tagged_at);
                        if ($days <= 0) {
                            return '-';
                        } else {
                            return $days;
                        }
                    } else {
                        return '-';
                    }
                } else {
                    return '-';
                }
            })
            ->editColumn('tracking_number_link', function ($crm_request) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$crm_request->tracking_number' class='tracking' target='_blank'>$crm_request->tracking_number</a></u>";
            })
            ->editColumn('valid_invalid_status', function ($crm_request) {
                if ($crm_request->valid_date != null) {
                    return 'Valid';
                } else if ($crm_request->invalid_date != null) {
                    return 'Invalid';
                } else {
                    return '-';
                }
            })
            ->editColumn('valid_invalid_date', function ($crm_request) {
                if ($crm_request->valid_date != null) {
                    return $crm_request->valid_date;
                } else if ($crm_request->invalid_date != null) {
                    return $crm_request->invalid_date;
                } else {
                    return '-';
                }
            })
            ->editColumn('request_number', function ($crm_request) {
                return str_pad($crm_request->request_number, 6, '0', STR_PAD_LEFT);
            })
            ->addColumn('id_padded_link', function ($crm_request) {
                return '<u><a href=' . route('admin.crm.request.details', ['id' => $crm_request->request_number]) . ' target="_blank">' . str_pad($crm_request->request_number, 6, '0', STR_PAD_LEFT) . '</a></u>';
            })
            ->addColumn('launched_by_name', function ($requests) {
                $name = '';
                if ($requests->launched_by_type == 0) {
                    $name = $requests->name;
                } else if ($requests->launched_by_type == 1) {
                    $name = $requests->shipper;
                } else {
                    $name = $requests->sub_shipper;
                }
                return $name;
            })
            ->editColumn('admin_department', function ($requests) {
                $name = '';
                if ($requests->launched_by_type == 0) {
                    $name = $requests->admin_department;
                } else {
                    $name = '-';
                }
                return $name;
            })
            ->filterColumn('launched_by_name', function ($query, $keyword) {
                $keyword = strtolower($keyword);

                if ($keyword != '') {
                    $query->where(function ($sub_query) use ($keyword) {
                        $sub_query->where('crm_requests.launched_by', '=', 0)
                            ->where('a.name', 'like', '%' . $keyword . '%');
                    })
                        ->orWhere(function ($sub_query) use ($keyword) {
                            $sub_query->where('crm_requests.launched_by', '=', 1)
                                ->where('us.name', 'like', '%' . $keyword . '%');
                        })
                        ->orWhere(function ($sub_query) use ($keyword) {
                            $sub_query->where('crm_requests.launched_by', '=', 2)
                                ->where('su.name', 'like', '%' . $keyword . '%');
                        });
                } else {
                    $query->whereRaw('false');
                }
            })
            ->orderColumn('launched_by_name', DB::connection('reports')->raw('IF (crm_requests.launched_by = 0, a.name, IF (crm_requests.launched_by = 1, us.name, IF (crm_requests.launched_by = 2, su.name, "")))') . ' $1')
            ->editColumn('launched_by_type', function ($requests) {
                if ($requests->launched_by_type == 0) {
                    return 'Admin';
                } else if ($requests->launched_by_type == 1) {
                    return 'Shipper';
                } else {
                    return 'Shipper Substitute User';
                }
            })
            ->editColumn('closed_date', function ($requests) {
                if ($requests->current_status_id == 4) {
                    return $requests->closed_date;
                } else {
                    return '-';
                }
            })
            ->addColumn('launched_to_today', function ($requests) {

                if ($requests->launched_date) {
                    Carbon::setWeekendDays([
                        Carbon::SUNDAY,
                    ]);

                    $launched_date = Carbon::parse($requests->launched_date);
                    $today = Carbon::now();
                    $days = $launched_date->diffInDays($today);
                    if ($days <= 0) {
                        return '-';
                    } else {
                        return $days . 'days';
                    }

                } else {
                    return '-';
                }
            })
            ->editColumn('case_closed_remark', function ($requests) {

                if ($requests->case_closed_remark)
                    return $requests->case_closed_remark;
                else
                    return '-';
            })
            ->editColumn('resolved_date', function ($requests) {
                if ($requests->current_status_id == 3 || $requests->current_status_id == 4) {
                    return $requests->resolved_date;
                } else {
                    return '-';
                }
            })
            ->editColumn('last_comment_name', function ($requests) {
                if ($requests->last_comment_by == 0) {
                    return $requests->last_comment_admin;
                } else if ($requests->last_comment_by == 1) {
                    return $requests->last_comment_shipper;
                } else {
                    return '-';
                }
            })
            ->editColumn('last_comment_date', function ($requests) {
                if ($requests->last_comment_date != null) {
                    return $requests->last_comment_date;
                } else {
                    return '-';
                }
            })
            ->filterColumn('last_comment_name', function ($query, $keyword) {
                $keyword = strtolower($keyword);

                if ($keyword != '') {
                    $query->where(function ($sub_query) use ($keyword) {
                        $sub_query->where('ccs.comment_by', '=', 0)
                            ->where('accs.name', 'like', '%' . $keyword . '%');
                    });

                } else {
                    $query->whereRaw('false');
                }
            })
            ->orderColumn('last_comment_name', DB::raw('accs.name') . ' $1')
            ->editColumn('last_comment', function ($requests) {
                if ($requests->last_comment != null) {
                    return $requests->last_comment;
                } else {
                    return '-';
                }
            })->addColumn('adjusted_percentage', function ($requests) {
                if($requests->adjusted_amount != null){

                    if($requests->cod_amount > 0){
    
                        return number_format(($requests->adjusted_amount/ $requests->cod_amount)*100,2);
                    }else{
                        return '-';
                    }
                }else{
                    return '-';
                }
            })->addColumn('remaining_percentage', function ($requests) {
                if($requests->adjusted_amount != null){

                    if($requests->cod_amount > 0){
                        if(($requests->adjusted_amount/ $requests->cod_amount)*100 == 0){
                            return '100';
                        }else{
                            return number_format(100 - (($requests->adjusted_amount/ $requests->cod_amount)*100) ,2);
                        }
                    }else{
                        return '-';
                    }
                }else{
                    return '-';
                }
            })->addColumn('responsibe_person_name', function ($requests) {
                $month_closing_responsible = MonthClosingResponsible::where('month_closing_id',$requests->month_closing_id);
                if($month_closing_responsible->exists()){
                    $month_closing_responsible = $month_closing_responsible->get();
                    $responsible = '';
                    $counter = 0;
                    foreach ($month_closing_responsible as $value) {
                        if($value->admin == 1){
                            $admin = Admin::find($value->responsible_person_id);
                            if($counter > 0){
                                $responsible .= ','.$admin->name.'( '.($admin->designation_id != null ? $admin->Edesignation->name : '').' ) ';

                            }else{
                                $responsible .= $admin->name.'( '.($admin->designation_id != null ? $admin->Edesignation->name : '').' ) ';
                            }
                            $counter++;

                        }else{
                            if($counter > 0){
                                $responsible .= ','.Rider::find($value->responsible_person_id)->name.'( Rider )';

                            }else{
                                $responsible .= Rider::find($value->responsible_person_id)->name.'( Rider )';

                            }
                            $counter++;

                        }  
                    }
                    return $responsible;
                }else{
                    return '-';
                }
            })->addColumn('responsibe_person_hub', function ($requests) {
                $month_closing_responsible = MonthClosingResponsible::where('month_closing_id',$requests->month_closing_id);
                if($month_closing_responsible->exists()){
                    $month_closing_responsible = $month_closing_responsible->get();
                    $hub = '';
                    $counter = 0;
                    foreach ($month_closing_responsible as $value) {
                        if($value->admin == 1){
                            $admin = Admin::find($value->responsible_person_id);
                            if($counter > 0){
                                $hub .= ', '.($admin->default_hub_id != null ? $admin->city->hub_city->name : '');

                            }else{
                                $hub .= ($admin->default_hub_id != null ? $admin->city->hub_city->name : '');
                            }
                            $counter++;
                        }else{
                            if($counter > 0){
                                $hub .= ','.Rider::find($value->responsible_person_id)->city->hub_city->name;

                            }else{
                                $hub .= Rider::find($value->responsible_person_id)->city->hub_city->name;

                            }
                            $counter++;
                        }  
                    }
                    return $hub;
                }else{
                    return '-';
                }
            })->addColumn('claim_adjustment_status', function ($requests) {
                if($requests->case_nature_id == 4){
                    if($requests->adjusted_amount != null){
                        return 'Valid';
                    }else{
                        return 'Invalid';
                    }
                }else{
                    return '-';
                }

            });
        if ($tracking = $request->get('search_tracking_no')) {
            $tracking_numbers = explode(',', $tracking);
            $datatable->whereIn('s.tracking_number', $tracking_numbers);
        }
        if ($rnumber = $request->get('search_request_number')) {
            $rnumber = explode(',', $rnumber);
            $datatable->whereIn('crm_requests.id', $rnumber);
        }
        if ($shipper = $request->get('search_shipper')) {
            $datatable->where('u.id', '=', $shipper);
        }
//        if($origin = $request->get('search_origin')){
//            $datatable->where('oc.id', '=', $origin);
//        }
        if ($destination = $request->get('search_destination')) {
            $datatable->where('dc.id', '=', $destination);
        }
        if ($hub = $request->get('search_hub')) {
            $datatable->where('h.id', '=', $hub);
        }
        if ($zone = $request->get('search_zone')) {
            $datatable->where('z.id', '=', $zone);
        }
        if ($case_nature = $request->get('search_case_nature')) {
            $datatable->where('crcn.id', '=', $case_nature);
        }
        if ($case_nature_type = $request->get('search_case_nature_type')) {
            $datatable->where('crcnt.id', '=', $case_nature_type);
        }

        if ($mode = $request->get('search_shipping_mode')) {
            $datatable->where('s.booking_type_id', '=', $mode);
        }
        if ($agent = $request->get('search_agent')) {
            $datatable->where('a.id', '=', $agent);
        }
        if ($status = $request->get('search_status')) {
            $datatable->whereIn('crs.id', $status);
        }
        if ($request->get('search_from') && $request->get('search_to')) {
            $from = $request->get('search_from');
            $to = $request->get('search_to');
            $datatable->whereBetween('crm_requests.created_at', [$from, $to]);
        }

        return $datatable->make(true);
    }

    public function summary_index(Request $request)
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 175);
        $today = Carbon::now()->endOfDay();
        $thirtyDays = Carbon::now()->subDays(30)->startOfDay();
        $shippers = DB::connection('reports')->table('users')->where('status', '>=', 3)->get();
        $hubs = DB::connection('reports')->table('cities')->select('id', 'name')->get();
        $shipping_modes = DB::connection('reports')->table('shipping_modes')->get(['id', 'mode']);

        return view('admin.reports.summary')->with(['hubs' => $hubs, 'shippers' => $shippers, 'today' => $today, 'thirtyday' => $thirtyDays, 'shipping_modes' => $shipping_modes]);
    }

    public function summary_data(Request $request)
    {
        $stats = array();
        $shipper = $request->shipper;
        $from = $request->from_date;
        $to = $request->to_date;
        $origin = $request->origin;
        $destination = $request->destination;
        if ($from == null || $to == null) {
            $toDays = Carbon::now()->endOfDay();
            $fromDays = Carbon::now()->subDays(30)->startOfDay();
        } else {
            $fromDays = $from;
            $toDays = $to;
        }

        $stats['total'] = DB::connection('reports')->table('shipments')->whereBetween('created_at', [$fromDays, $toDays])->where('user_id', $shipper);
        $stats['booked'] = DB::connection('reports')->table('shipments')->where('shipper_status_id', 1)->whereBetween('created_at', [$fromDays, $toDays])->where('user_id', $shipper);
        $stats['canceled'] = DB::connection('reports')->table('shipments')->where('shipper_status_id', 17)->whereBetween('created_at', [$fromDays, $toDays])->where('user_id', $shipper);
        $stats['received'] = DB::connection('reports')->table('shipments')->whereIn('shipper_status_id', [2, 3, 4])->whereBetween('created_at', [$fromDays, $toDays])->where('user_id', $shipper);
        $stats['delivered'] = DB::connection('reports')->table('shipments')->whereIn('shipper_status_id', [14, 16, 30, 36, 37, 39, 40, 41, 47])->whereBetween('created_at', [$fromDays, $toDays])->where('user_id', $shipper);
        $stats['return'] = DB::connection('reports')->table('shipments')->whereIn('shipper_status_id', [25])->whereBetween('created_at', [$fromDays, $toDays])->where('user_id', $shipper);
        $stats['return_intransit'] = DB::connection('reports')->table('shipments')->where('shipper_status_id', 21)->whereBetween('created_at', [$fromDays, $toDays])->where('user_id', $shipper);
        $stats['in_process'] = DB::connection('reports')->table('shipments')->whereIn('shipper_status_id', [5, 6, 7, 8, 9, 10, 11, 12, 13, 15, 18, 19, 49, 52])->whereBetween('created_at', [$fromDays, $toDays])->where('user_id', $shipper);
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

            $stats['return_intransit'] = $stats['return_intransit']->whereExists(function ($query) use ($origin) {
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
            $stats['return_intransit'] = $stats['return_intransit']->where('consignee_city_id', $destination);
            $stats['in_process'] = $stats['in_process']->where('consignee_city_id', $destination);
        }
        $stats['total'] = number_format($stats['total']->count());
        $stats['booked'] = number_format($stats['booked']->count());
        $stats['canceled'] = number_format($stats['canceled']->count());
        $stats['received'] = number_format($stats['received']->count());
        $stats['delivered'] = number_format($stats['delivered']->count());
        $stats['return'] = number_format($stats['return']->count());
        $stats['return_intransit'] = number_format($stats['return_intransit']->count());
        $stats['in_process'] = number_format($stats['in_process']->count());


        return response()->json(['status' => 1, 'stats' => $stats]);

    }

    public function summary_list(Request $request)
    {
        $connection = 'reports';
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 176);
        }
        $shipments = DB::connection('reports')->table('shipments')->join('users as u', 'u.id', '=', 'shipments.user_id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->join('cities as h', 'dc.hub_id', '=', 'h.id')
            ->join('booking_types as bt', 'bt.id', '=', 'shipments.booking_type_id')
            ->join('shipment_status as ss', 'ss.id', '=', 'shipments.shipper_status_id')
            ->leftjoin('shipment_payment_status as sps', 'shipments.payment_status_id', '=', 'sps.id')
            ->leftJoin('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where('sj.id', '=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)'));
            })
            ->leftJoin('shipments_journey as sju', function ($join) {
                $join->on('sju.shipment_id', '=', 'shipments.id')
                    ->where('sju.id', '=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id)'));
            })
            ->leftjoin('shipment_items as si', function ($join) {
                $join->on('si.shipment_id', '=', 'shipments.id')
                    ->where('si.type', '=', 0);
            })
            ->leftJoin('shipments_journey as sjr', function ($join) use ($connection) {
                $join->on('sjr.shipment_id', '=', 'shipments.id')
                    ->whereIn('shipments.shipper_status_id', [20, 21, 22, 23, 24, 25, 44, 47, 48, 57, 60])
                    ->where('sjr.id', '=',
                        DB::connection($connection)->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id IN (12, 20) and shipments_journey.verification = 1 and shipments_journey.status_reason_id is not null)'));
            })
            ->leftJoin('shipment_status_reason as ssr', 'ssr.id', '=', 'sjr.status_reason_id')
            ->select(['ssr.name as reason', 'sjr.remarks as remark', 'shipments.id as shipment_id', 'shipments.order_id', 'shipments.tracking_number', 'shipments.amount as collection_amount', 'ss.name as current_status', 'sps.name as payment_status', 'bt.booking_type as service_type', 'sj.created_at as arrival_date', 'oc.name as origin', 'dc.name as destination', 'u.name as shipper', 'shipments.consignee_name', 'shipments.consignee_phone_number_1 as phone1', 'shipments.consignee_phone_number_2 as phone2', 'shipments.consignee_address', 'shipments.created_at as booking_date', 'usi.vendor', DB::raw('(select count(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 5) as total_attempt'), 'h.name as hub', 'sju.created_at as last_status_date']);
        /*  if( $request->get('search_shipper')){
              $shipments->where('shipments.user_id', '=',$request->get('search_shipper'));
          }else{
              $shipments->where('shipments.user_id', '=', null);
          }*/
        if ($search_shipper = $request->get('search_shipper')) {
            $shipments = $shipments->where('shipments.user_id', $search_shipper);
        } else {
            $shipments->where('shipments.user_id', '=', null);
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
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->editColumn('phone', function ($shipments) {
                return $shipments->phone1 . "<br>" . $shipments->phone2;
            })
            ->filterColumn('phone', function ($query, $keyword) {
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
            ->orderColumn('phone', 'shipments.consignee_phone_number_1 $1, shipments.consignee_phone_number_2 $1')
            ->editColumn('collection_amount', function ($shipments) {
                return number_format($shipments->collection_amount);
            });
//        if($shipper = $request->get('search_shipper')){
//            $datatable->where('shipments.user_id','=', $shipper);
//        }
        if ($origin = $request->get('search_origin')) {
            $datatable->where('oc.id', '=', $origin);
        }
        if ($destination = $request->get('search_destination')) {
            $datatable->where('dc.id', '=', $destination);
        }
        if ($mode = $request->get('search_shipping_mode')) {
            $datatable->where('shipments.booking_type_id', '=', $mode);
        }
        if ($card = $request->get('cards_filter')) {
            switch ($card) {
                case 'total':
                    $today = Carbon::now()->endOfDay();
                    $thirtyDays = Carbon::now()->subDays(30)->startOfDay();
                    $datatable->whereBetween('shipments.created_at', [$thirtyDays, $today]);
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
                    $datatable->whereIn('shipments.shipper_status_id', [25]);
                    break;
                case 'returned_intransit':
                    $datatable->where('shipments.shipper_status_id', 21);
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

    public function account_activation_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 177);
        return view('admin.reports.account_activation');
    }

    public function account_activation_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 178);
        }
        $users = DB::connection('reports')->table('users')->join('sale_person_tags as spt', 'spt.user_id', '=', 'users.id')
            ->join('admins AS sp', 'sp.id', '=', 'spt.admin_id')
            ->select(['users.id', 'users.name as shipper', 'sp.name as sale_person', 'users.activated_at'])
            ->where('spt.status', '=', 0)
            ->where('users.status', '=', 3);
        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $users = $users->whereBetween('users.activated_at', [$from, $to]);
        }
        $datatable = Datatables::of($users)->make(true);
        return $datatable;
    }

    public function adjustments_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 179);
        $shippers = DB::connection('reports')->table('users')->where('status', '>=', 3)->get();
        $shipping_modes = DB::connection('reports')->table('shipping_modes')->get(['id', 'mode']);
        return view('admin.reports.adjustments')->with(['shippers' => $shippers, 'shipping_modes' => $shipping_modes]);
    }

    public function adjustments_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 180);
        }
        $adjustments = DB::connection('reports')->table('adjustment_logs')
            ->leftjoin('done_payment_shipments as dps', 'dps.id', '=', 'adjustment_logs.done_id')
            ->leftjoin('shipments as s', 's.id', '=', 'adjustment_logs.shipment_id')
            ->leftjoin('users as u', 'u.id', '=', 's.user_id')
            ->join('user_shipping_infos AS usi', 's.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', 's.consignee_city_id', '=', 'dc.id')
            ->leftjoin('adjustment_types as at', 'at.id', '=', 'adjustment_logs.adjustment_type_id')
            ->leftjoin('admins as a', 'a.id', '=', 'adjustment_logs.admin_id')
            ->select('adjustment_logs.id as adjustment_id', 'adjustment_logs.adjustment_amount as adjustment_amount', 'adjustment_logs.remarks as remarks', 's.tracking_number as tracking_number', 'at.name as adjustment_type', 'adjustment_logs.created_at as created_at', 'a.name as created_by', 'u.name as shipper_name', 'dps.done_payment_id as done_payment_id', 'oc.name as origin', 'dc.name as destination')
            ->whereIn('adjustment_logs.type', [1, 2]);
        $datatable = Datatables::of($adjustments)
            ->addColumn('adjustment_id_padded', function ($adjustment) {
                $padded_id = str_pad($adjustment->adjustment_id, 6, '0', STR_PAD_LEFT);
                return $padded_id;
            })
            ->addColumn('tracking_number_link', function ($shipments) {
                $route = route('admin.tracking.index');
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
        if ($shipper = $request->get('search_shipper')) {
            $datatable->where('u.id', '=', $shipper);
        }
        if ($type = $request->get('search_type')) {
            $datatable->where('adjustment_logs.type', '=', $type);
        }
        if ($mode = $request->get('search_shipping_mode')) {
            $datatable->where('s.booking_type_id', '=', $mode);
        }
        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $datatable = $datatable->whereBetween('adjustment_logs.created_at', [$from, $to]);
        }
        return $datatable->make(true);
    }

    public function sdn_index(Request $request)
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 181);
        $hubs = DB::connection('reports')->table('cities')->where('hub', 1)->select('id', 'name')->get();
        return view('admin.reports.station_deposit_notes')->with(['hubs' => $hubs]);
    }

    public function sdn_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 182);
        }
        $sdn = StationDepositNote::
        join('cities AS oc', 'station_deposit_notes.hub_id', '=', 'oc.id')
            ->join('admins', 'admins.id', '=', 'station_deposit_notes.deposited_by')
            ->leftjoin('admins as a', 'a.id', '=', 'station_deposit_notes.status_updated_by')
            ->select(['a.name as resolved_by', 'station_deposit_notes.id as sdn', 'station_deposit_notes.id as sdn_id', 'oc.name as hub', 'station_deposit_notes.dncc_count', 'station_deposit_notes.dncc_count as dncc_link', 'station_deposit_notes.sdn_delivered_shipments', 'station_deposit_notes.sdn_delivered_shipments as delivered_shipments_link', 'station_deposit_notes.sdn_amount', 'station_deposit_notes.sdn_net_amount', 'admins.name as deposited_by', 'station_deposit_notes.created_at', 'station_deposit_notes.deposit_slip', 'station_deposit_notes.status', 'station_deposit_notes.deposit_slip_status', 'station_deposit_notes.sdn_deposit_amount', 'station_deposit_notes.adjustment_amount', 'station_deposit_notes.adjustment_date', 'station_deposit_notes.adjustment_ref', 'station_deposit_notes.created_at']);

        if (session('role_id') != 1) {
            $sdn = $sdn->whereIn('oc.hub_id', session('hubs'));
        }

        $datatable = Datatables::of($sdn)
            ->editColumn('sdn', function ($sdn) {
                return "<a href='javascript:void(0);' class='printSDN'><u>" . str_pad($sdn->sdn_id, 6, '0', STR_PAD_LEFT) . "</u></a>";
            })
            ->editColumn('sdn_amount', function ($shipment) {
                return number_format($shipment->sdn_amount);
            })
            ->editColumn('sdn_net_amount', function ($shipment) {
                return number_format($shipment->sdn_net_amount);
            })
            ->editColumn('sdn_deposit_amount', function ($shipment) {
                if ($shipment->sdn_deposit_amount) {
                    return number_format($shipment->sdn_deposit_amount);
                } else {
                    return '-';
                }
            })
            ->addColumn('sdn_adjustment_amount', function ($shipment) {
                if ($shipment->adjustment_amount) {
                    return number_format($shipment->adjustment_amount);
                } else {
                    return '-';
                }
            })
            ->addColumn('sdn_id_padded', function ($sdn) {
                return str_pad($sdn->sdn_id, 6, '0', STR_PAD_LEFT);
            })
            ->filterColumn('station_deposit_notes.id', function ($query, $keyword) {
                return $query->where('station_deposit_notes.id', '=', $keyword);
            })
            ->addColumn('deposit_slip', function ($sdn) {
                if ($sdn->deposit_slip == null && $sdn->deposit_slip_status == 1) {
                    return '<a class="btn btn-sm btn-outline-info align-middle deposit_slip_view" href="#"><i class="la la-lg la-image align-middle"></i> <span class="align-middle">View</span></a>';

                } else if ($sdn->deposit_slip != null) {
                    return '<a class="btn btn-sm btn-outline-info align-middle" href="' . asset('uploads/sdn/' . $sdn->deposit_slip) . '" target="_blank"><i class="la la-lg la-image align-middle"></i> <span class="align-middle">View</span></a>';
                } else {
                    return '-';
                }
            })
            ->editColumn('dncc_link', function ($sdn) {
                if ($sdn->dncc_count != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $sdn->dncc_count . '</button>';
                } else {
                    return 0;
                }
            })
            ->editColumn('delivered_shipments_link', function ($sdn) {
                if ($sdn->sdn_delivered_shipments != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $sdn->sdn_delivered_shipments . '</button>';
                } else {
                    return 0;
                }
            })
            ->addColumn('difference_amount', function ($sdn) {
                $diff_amount = '';
                $diff_amount = $sdn->sdn_amount - $sdn->sdn_deposit_amount;
                return number_format($diff_amount);
            })
            ->editColumn('status', function ($sdn) {
                if ($sdn->status == 0) {
                    return 'Created';
                } else if ($sdn->status == 1) {
                    return 'Deposited';
                } else if ($sdn->status == 2) {
                    return 'Resolved';
                } else {
                    return 'Closed';
                }
            })
            ->filterColumn('status', function ($query, $keyword) {
                if ($keyword == 0) {
                    $query->where('station_deposit_notes.status', '=', $keyword);
                } else if ($keyword == 1) {
                    $query->where('station_deposit_notes.status', '=', $keyword);
                } else if ($keyword == 2) {
                    $query->where('station_deposit_notes.status', '=', $keyword);
                } else {
                    $query->whereRaw('false');
                }
            });
        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $datatable->whereBetween('station_deposit_notes.created_at', [$from, $to]);
        }
        if ($sdn_no = $request->get('search_sdn_no')) {
            $datatable->where('station_deposit_notes.id', '=', $sdn_no);
        }
        if ($hub = $request->get('search_hub')) {
            $datatable->where('oc.id', '=', $hub);
        }
        return $datatable->make(true);
    }

    public function account_edit_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 183);
        return view('admin.reports.account_edit');
    }

    public function account_edit_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 184);
        }
        $users = DB::connection('reports')->table('users')->join('sale_person_tags as spt', 'spt.user_id', '=', 'users.id')
            ->join('admins AS sp', 'sp.id', '=', 'spt.admin_id')
            ->where('spt.status', '=', 0);

        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            if ($request->get('search_account_type') == 1) {
                $users = $users->leftJoin('history_rate_statuses', 'history_rate_statuses.user_id', '=', 'users.id')
                    ->select(['users.id', 'users.name as shipper', 'users.account_type_id', 'sp.name as sale_person', 'users.activated_at', 'history_rate_statuses.created_at as rates_edit_at'])
                    ->where('users.account_type_id', '=', 1)->whereBetween('history_rate_statuses.created_at', [$from, $to]);
            } else {
                $users = $users->leftJoin('history_corporate_rate_statuses', 'history_corporate_rate_statuses.user_id', '=', 'users.id')
                    ->select(['users.id', 'users.name as shipper', 'users.account_type_id', 'sp.name as sale_person', 'users.activated_at', 'history_corporate_rate_statuses.created_at as rates_edit_at'])
                    ->where('users.account_type_id', '=', 2)->whereBetween('history_corporate_rate_statuses.created_at', [$from, $to]);
            }

        }

        $datatable = Datatables::of($users);


        return $datatable->make(true);
    }

    public function bank_history_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 185);
        $shippers = User::where('status', 3)->select('id', 'name')->get();
        return view('admin.reports.shipper_bank_history')->with(['shippers' => $shippers]);
    }

    public function bank_history_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 186);
        }
        $users = DB::connection('reports')->table('history_shipper_bank_accounts')
            ->join('users', 'users.id', '=', 'history_shipper_bank_accounts.user_id')
            ->join('banks_lists', 'history_shipper_bank_accounts.bank_id', '=', 'banks_lists.id')
            ->select('users.id as account_id', 'users.name as shipper', 'banks_lists.name as bank_name', 'history_shipper_bank_accounts.created_at as change_date');


        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $users = $users->whereBetween('history_shipper_bank_accounts.created_at', [$from, $to]);
        }
        if ($shipper_id = $request->get('search_shipper')) {
            $users = $users->where('history_shipper_bank_accounts.user_id', '=', $shipper_id);
        }
        $datatable = Datatables::of($users)->make(true);
        return $datatable;
    }

    public function consignee_details_history_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 187);
        return view('admin.reports.consignee_details_history');
    }

    public function consignee_details_history_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 188);
        }
        $shipments = DB::connection('reports')->table('shipment_information_logs')
            ->leftJoin('shipments as s', 's.id', '=', 'shipment_information_logs.shipment_id')
            ->leftJoin('users as u', 'u.id', '=', 's.user_id')
            ->leftJoin('user_shipping_infos AS usi', 's.pickup_address_id', '=', 'usi.id')
            ->leftJoin('cities as oc', 'usi.city_id', '=', 'oc.id')
            ->select('s.tracking_number as tracking_number', 'shipment_information_logs.old_consignee_name as o_name', 'shipment_information_logs.old_consignee_address as o_address', 'shipment_information_logs.old_consignee_phone as o_phone_no', 'shipment_information_logs.old_special_instruction as o_s_instruction', 'shipment_information_logs.new_consignee_name as n_name', 'shipment_information_logs.new_consignee_address as n_address', 'shipment_information_logs.new_consignee_phone as n_phone_no', 'shipment_information_logs.new_special_instruction as n_s_instruction', 'oc.name as origin');
        $datatable = Datatables::of($shipments)
            ->editColumn('tracking_number_link', function ($shipments) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            });

        if ($tracking_number = $request->get('search_tracking_no')) {
            $datatable = $datatable->where('s.tracking_number', '=', $tracking_number);
        }
        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $datatable = $datatable->whereBetween('shipment_information_logs.created_at', [$from, $to]);
        }
        return $datatable->make(true);
    }

    public function booked_and_cancelled_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 189);
        $shippers = DB::connection('reports')->table('users')->get(['id', 'name']);
        $shipping_modes = DB::connection('reports')->table('shipping_modes')->get();
        $service_types = DB::connection('reports')->table('booking_types')->get();
        $statuses = DB::connection('reports')->table('shipment_status')->whereIn('id', [1, 17])->get(['id', 'name']);
        return view('admin.reports.booked_and_cancelled_shipments_report')->with(['shippers' => $shippers, 'shipping_modes' => $shipping_modes, 'service_types' => $service_types, 'statuses' => $statuses]);
    }

    public function booked_and_cancelled_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 190);
        }
        $shipments = DB::connection('reports')->table('shipments')
            ->join('users as u', 'shipments.user_id', '=', 'u.id')
            ->join('shipping_modes as sm', 'shipments.shipping_mode_id', '=', 'sm.id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->join('booking_types as bt', 'bt.id', '=', 'shipments.booking_type_id')
            ->join('shipment_status as ss', 'ss.id', '=', 'shipments.shipper_status_id')
            ->leftJoin('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where('sj.id', '=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id)'));
            })
            ->leftjoin('shipment_items as si', function ($join) {
                $join->on('si.shipment_id', '=', 'shipments.id')
                    ->where('si.type', '=', 0);
            })
            ->select(['si.quantity as item_quantity', 'shipments.tracking_number as tracking_number', 'ss.name as status', 'bt.booking_type as service_type', 'oc.name as origin', 'dc.name as destination', 'sm.mode as shipping_mode', 'sj.remarks as remarks', 'u.name as shipper'])
            ->whereIn('shipments.shipper_status_id', [1, 17]);

        $datatable = Datatables::of($shipments)
            ->editColumn('tracking_number', function ($shipments) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->editColumn('tracking_number_link', function ($shipments) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";

            });
        if ($tracking_numbers = $request->get('tracking_numbers')) {
            $datatable->whereIn('shipments.tracking_number', explode(',', $tracking_numbers));
        }

        if ($shipper = $request->get('search_shipper')) {
            $datatable = $datatable->where('u.id', '=', $shipper);
        }
        if ($search_shipping_mode = $request->get('search_shipping_mode')) {
            $datatable = $datatable->where('sm.id', '=', $search_shipping_mode);
        }
        if ($service_type = $request->get('search_service_type')) {
            $datatable = $datatable->where('bt.id', '=', $service_type);
        }
        if ($status = $request->get('search_status')) {
            $datatable = $datatable->where('ss.id', '=', $status);
        }
        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $datatable = $datatable->whereBetween('shipments.created_at', [$from, $to]);
        }
        return $datatable->make(true);
    }

    public function fake_status_shipments_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 191);
        $riders = DB::connection('reports')->table('riders')->get(['id', 'name']);
        $destinations = DB::connection('reports')->table('cities')->where('hub', 1)->select('id', 'name')->get();
        $hubs = DB::connection('reports')->table('cities')->select('id', 'name')->get();
        $zones = DB::connection('reports')->table('zones')->select('id', 'name')->where('business_category_id', 1)->get();
        $shipping_modes = DB::connection('reports')->table('shipping_modes')->get(['id', 'mode']);
        return view('admin.reports.fake_statuses_shipments_report')->with(['riders' => $riders, 'hubs' => $hubs, 'destinations' => $destinations, 'shipping_modes' => $shipping_modes, 'zones' => $zones]);
    }

    public function fake_status_shipments_list(request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 192);
        }
        $delivery_note = DB::connection('reports')->table('delivery_note_shipments')->join('delivery_notes as dn', 'dn.id', '=', 'delivery_note_shipments.delivery_note_id')
            ->leftjoin('shipments as s', 's.id', '=', 'delivery_note_shipments.shipment_id')
            ->leftjoin('admins as admin', 'admin.id', '=', 'delivery_note_shipments.admin_id')
            ->join('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 's.id')
                    ->where('sj.id', '=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = s.id and shipments_journey.admin_id IS NOT NULL )'));
            })
            ->leftjoin('riders as r', 'r.id', '=', 'dn.rider_id')
            ->leftjoin('cities as dc', 'dc.id', '=', 's.consignee_city_id')
            ->leftjoin('cities as h', 'h.id', '=', 'dc.hub_id')
            ->leftjoin('zones as z', 'z.id', '=', 'dc.zone_id')
            ->leftjoin('users as u', 'u.id', '=', 's.user_id')
            ->leftjoin('admins as a', 'a.id', '=', 'sj.admin_id')
            ->leftjoin('admin_roles as ar', 'ar.id', '=', 'admin.role_id')
            ->leftjoin('admin_departments as ad', 'ad.id', '=', 'ar.department_id')
            ->select('s.tracking_number as tracking_number', 'u.name as shipper', 'r.name as rider_name', 'dc.name as destination', 'h.name as hub', 'delivery_note_shipments.fake_status_updated_at as updated_at', 'delivery_note_shipments.remarks as remarks', 'admin.name as raised_by', 'ad.name as department', 'sj.remarks as debrifer_remark', 'a.name as debrifer_name')
            ->where('delivery_note_shipments.fake_status', 1);


        $datatables = Datatables::of($delivery_note)
            ->editColumn('tracking_number_link', function ($shipments) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            });
        if ($rider = $request->get('rider')) {
            $datatables->where('r.id', '=', $rider);
        }
        if ($hub = $request->get('hub')) {
            $datatables->where('h.id', $hub);
        }
        if ($destination = $request->get('destination')) {
            $datatables->where('dc.id', $destination);
        }
        if ($mode = $request->get('search_shipping_mode')) {
            $datatables->where('s.id', '=', $mode);
        }
        if ($tracking_number = $request->get('search_tracking_no')) {
            $datatables->where('s.tracking_number', $tracking_number);
        }
        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $datatables->whereBetween('delivery_note_shipments.fake_status_updated_at', [$from, $to]);
        }
        if ($zone = $request->get('zone')) {
            $datatables->where('z.id', '=', $zone);
        }
        return $datatables->make(true);
    }

    public function daily_visit_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 193);
        $admins = Admin::where('admins.status', 1)
            ->leftjoin('employee_designations as ed', 'admins.designation_id', 'ed.id')
            ->where('ed.department_id', 7);

        $multiple_sales_tags = MultipleSaleLead::join('multiple_sale_taggings as mst', 'mst.lead_id', '=', 'multiple_sale_leads.id')
            ->where('multiple_sale_leads.admin_id', Auth::id())
            ->pluck('mst.admin_id')->toArray();

        if (session('role_id') != 1 && (!in_array(session('id'), session('sale_users_bypass')))) {
            if(count($multiple_sales_tags) > 0) {
                array_push($multiple_sales_tags, Auth::id());
                $admins = $admins->whereIn('admins.id', $multiple_sales_tags);
            }else{
                $admins = $admins->where('admins.id', Auth::id());
            }
        }
        $admins = $admins->get(['admins.id', 'admins.name']);
        $ratings = CrmRequestRating::all();
        return view('admin.reports.daily_visit_report')->with(['admins' => $admins,'ratings'=>$ratings]);
    }

    public function daily_visit_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 194);
        }
        $daily_visit = DB::connection('reports')->table('daily_visits')
            ->join('daily_visit_lead_statuses as dvls', 'dvls.id', '=', 'daily_visits.lead_status_id')
            ->leftjoin('admins as a', 'a.id', '=', 'daily_visits.admin_id')
            ->leftjoin('cities as c', 'c.id', '=', 'a.default_hub_id')
            ->leftjoin('zones as z', 'z.id', '=', 'c.zone_id')
            ->leftjoin('crm_request_ratings as rate','rate.id','daily_visits.rating_id')
            ->select('a.name as admin', 'daily_visits.company_name as company_name', 'daily_visits.customer_name as customer_name', 'daily_visits.customer_address as customer_address', 'daily_visits.phone_no as phone_no', 'daily_visits.email as email', 'dvls.name as lead_status', 'daily_visits.feedback as feedback', 'daily_visits.latitude as latitude', 'daily_visits.longitude as longitude', 'daily_visits.created_at as created_at', 'daily_visits.business_card_image as business_card_image', 'daily_visits.location_image as location_image', 'c.name as city', 'z.name as zone','rate.name as rating_text','daily_visits.comment as rating_comment','rate.code as rating');

        $multiple_sales_tags = MultipleSaleLead::join('multiple_sale_taggings as mst', 'mst.lead_id', '=', 'multiple_sale_leads.id')
            ->where('multiple_sale_leads.admin_id', Auth::id())
            ->pluck('mst.admin_id')->toArray();

        if (session('role_id') != 1 && (!in_array(session('id'), session('sale_users_bypass')))) {
            if(count($multiple_sales_tags) > 0){
                array_push($multiple_sales_tags, Auth::id());
                $daily_visit = $daily_visit->whereIn('daily_visits.admin_id', $multiple_sales_tags);
            }
            else{
                $daily_visit = $daily_visit->where('daily_visits.admin_id', Auth::id());
            }
        }

        $datatables = Datatables::of($daily_visit)
            ->editColumn('b_c_photo', function ($dvr) {
                $image = '';
                if ($dvr->business_card_image != null) {
                    $image .= '<div class="text-center"><button type="button" class="btn btn-primary btn-sm"><a class="white" href=' . route('admin.daily_visit.business_card', [$dvr->business_card_image]) . ' target="_blank">View</a></button></div>';
                    return $image;
                } else {
                    return '-';
                }
            })
            ->editColumn('l_photo', function ($dvr) {
                $image = '';
                if ($dvr->location_image != null) {
                    $image .= '<div class="text-center"><button type="button" class="btn btn-primary btn-sm"><a class="white" href=' . route('admin.daily_visit.location_photo', [$dvr->location_image]) . ' target="_blank">View</a></button></div>';
                    return $image;
                } else {
                    return '-';
                }
            })
            ->editColumn('location', function ($dvr) {
                $location = '<div class="text-center">';
                if ($dvr->latitude != null && $dvr->longitude != null) {
                    $location .= '<button type="button" class="btn btn-primary btn-sm"><a class="white" href="http://www.google.com/maps/place/' . $dvr->latitude . ',' . $dvr->longitude . '" target="_blank"><i class="la la-map-marker align-middle"></i></a></button>';
                    return $location;
                } else {
                    return '-';
                }
            });

        //AdminUser Filter
        if ($team_member = $request->get('team_member')) {
            $datatables->where('a.id', $team_member);
        }

        if ($rating = $request->get('rating')) {
            $datatables->where('daily_visits.rating_id', $rating);
        }
        //VisitDate filter
        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $datatables->whereBetween('daily_visits.created_at', [$from, $to]);
        }

        return $datatables->make(true);
    }

    public function delivered_shipment_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 195);
        $toDays = Carbon::now();
        $fromDays = Carbon::now()->subDays(29);
        $cities = DB::connection('reports')->table('cities')->select('id', 'name')->get();
        $riders = DB::connection('reports')->table('riders')->get(['id', 'name']);
        $shipping_modes = DB::connection('reports')->table('shipping_modes')->get(['id', 'mode']);
        return view('admin.reports.delivered_shipment_report')->with(['cities' => $cities, 'riders' => $riders, 'fromDays' => $fromDays, 'toDays' => $toDays, 'shipping_modes' => $shipping_modes]);
    }

    public function delivered_shipment_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 196);
        }
        $from = $request->search_date_from;
        $to = $request->search_date_to;
        if ($from == null || $to == null) {
            $toDays = Carbon::now()->endOfDay();
            $fromDays = Carbon::now()->subDays(30)->startOfDay();
        } else {
            $fromDays = $from;
            $toDays = $to;
        }
        $delivered_shipments = DB::connection('reports')->table('riders')
            ->join('rider_categories as rc', 'rc.id', '=', 'riders.rider_category_id')
            ->join('cities as c', 'c.id', '=', 'riders.city_id')
            ->leftjoin('routes as rou', 'rou.id', '=', 'riders.route_id')
            ->leftjoin('delivery_notes as dn', function ($join) use ($fromDays, $toDays) {
                $join->on('dn.rider_id', '=', 'riders.id')
                    ->whereBetween('dn.created_at', [$fromDays, $toDays]);
            })
            ->leftjoin('shipments_journey as sj', function ($join) use ($fromDays, $toDays) {
                $join->on('sj.reference_1_id', '=', 'dn.id')
                    ->where('sj.shipper_status_id', '=', 14)
                    ->where('sj.verification', '=', 1)
                    ->whereBetween('sj.updated_at', [$fromDays, $toDays]);
            })
            ->leftjoin('delivery_note_shipments as dns', 'dns.delivery_note_id', '=', 'dn.id')
            ->leftjoin('shipments as s', 's.id', '=', 'dns.shipment_id')
            ->select('riders.name as courier_name', 'rc.name as courier_type', 'rou.code as route_code', DB::raw('count(s.id) as shipments_count'), DB::raw('count(sj.id) as delivered_shipments_count'), DB::raw('count(s.id)/count(sj.id) as delivery_ratio'), 'c.name as station')
            ->groupBy('riders.id');

        $datatables = Datatables::of($delivered_shipments);

        if ($rider = $request->get('search_rider')) {
            $datatables = $datatables->where('riders.id', '=', $rider);
        }
        if ($station = $request->get('search_station')) {
            $datatables = $datatables->where('c.id', '=', $station);
        }
        if ($mode = $request->get('search_shipping_mode')) {
            $datatables->where('s.booking_type_id', '=', $mode);
        }
        return $datatables->make(true);
    }

    public function route_distribution_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 197);
        $hubs = DB::connection('reports')->table('cities')->select('id', 'name')->where('hub', 1)->where('status', 1)->get();
        $destination_cities = DB::connection('reports')->table('cities')->select('id', 'name')->where('status', 1)->get();
        $zones = DB::connection('reports')->table('zones')->select('id', 'name')->get();
        $riders_cat = OperationRidersCategory::all();
        $riders = DB::connection('reports')->table('riders')->get(['id', 'name']);

        return view('admin.reports.route_distribution_summary_report')->with(['hubs' => $hubs, 'destination_cities' => $destination_cities, 'zones' => $zones, 'riders' => $riders, 'riders_cat' => $riders_cat]);
    }

    public function route_distribution_list(Request $request)
    {

        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),198);
        }

        $count = DB::connection('reports')->table('delivery_notes')
            ->join('riders as r', 'r.id', '=', 'delivery_notes.rider_id')
            ->leftjoin('cities as c', 'c.id', '=', 'delivery_notes.hub_id');

        if($rider = $request->get('search_rider')){
            $count = $count->where('r.id', '=', $rider);
        }
        if($hub = $request->get('search_hub')){
            $count = $count->where('c.hub_id', '=', $hub);
        }
        if($zone = $request->get('search_zone')){
            $count = $count->where('c.zone_id', '=', $zone);
        }
        if($destination = $request->get('search_destination')){
            $count = $count->where('c.id', '=', $destination);
        }
        if ($search_rider_cat = $request->get('search_rider_cat')) {
            $count = $count->where('r.operation_rider_id', $search_rider_cat);
        }

        $count = $count->groupBy('r.id')->count();

        $route_distribution_summary = DB::connection('reports')->table('delivery_notes')
            ->join('riders as r', 'r.id', '=', 'delivery_notes.rider_id')
            ->join('rider_types as rt', 'r.rider_type_id', '=', 'rt.id')
            ->leftjoin('operation_riders_categories as rd', 'r.operation_rider_id', '=', 'rd.id')
            ->leftjoin('cities as c', 'c.id', '=', 'delivery_notes.hub_id')
            ->leftjoin('delivery_note_shipments as dns', 'dns.delivery_note_id', '=', 'delivery_notes.id')
            ->leftJoin('shipments as s', 's.id', '=', 'dns.shipment_id')
            ->leftJoin('shipments_journey as ds', function ($join) {
                $join->on('ds.shipment_id', '=', 's.id')
                    ->where('ds.id', '=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = s.id and shipments_journey.reference_1_id = delivery_notes.id and shipments_journey.shipper_status_id in (14, 30, 36, 37) and verification = 1)'));
            })
            ->leftJoin('shipments_journey as cps', function ($join) {
                $join->on('cps.shipment_id', '=', 's.id')
                    ->where('cps.id', '=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = s.id and shipments_journey.reference_1_id = delivery_notes.id and shipments_journey.shipper_status_id = 12 and verification = 1)'));
            })
            ->leftJoin('shipments_journey as us', function ($join) {
                $join->on('us.shipment_id', '=', 's.id')
                    ->where('us.id', '=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = s.id and shipments_journey.reference_1_id = delivery_notes.id and shipments_journey.shipper_status_id in (7,8,9,12,15,18,56) and verification = 1)'));
            })
            ->select('r.name as courier_name', DB::raw('count(s.id) as shipments_count'), DB::raw('count(ds.id) as delivered_shipments'), DB::raw('count(cps.id) as confirmation_pending_shipments'), DB::raw('count(us.id) as undelivered_shipments'), 'c.name as hub',DB::raw('count(DISTINCT delivery_notes.id) as dn_no_count'),DB::raw('GROUP_CONCAT(DISTINCT delivery_notes.id) as dn_ids'),'rt.name as rider_type')
            ->groupBy('r.id');

        $datatables = Datatables::of($route_distribution_summary)
            ->setTotalRecords($count)
            ->addColumn('dn_no', function ($entry) {
                $function = "dn_no_pop('".$entry->dn_ids."')";
                if ($entry->dn_no_count > 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle" onclick="'.$function.'" >' . $entry->dn_no_count . '</button>';
                } else {
                    return 0;
                }

            })
            ->addColumn('dncc_amount',function($entry) {
                $dn_ids = explode(',',$entry->dn_ids);
                return DB::connection('reports')->table('delivery_notes')->whereIn('id',$dn_ids )->sum('received_cod_amount');
            })
            ->addColumn('delivered_shipments_per', function ($entry) {
                if ($entry->shipments_count) {
                    return round(($entry->delivered_shipments / $entry->shipments_count) * 100, 2);
                }
                else {
                    return '';
                }
            })
            ->addColumn('undelivered_shipments_per', function ($entry) {
                if ($entry->shipments_count) {
                    return round(($entry->undelivered_shipments / $entry->shipments_count) * 100, 2);
                }
                else {
                    return '';
                }
            })
            ->addColumn('confirmation_pending_shipments_per', function ($entry) {
                if ($entry->shipments_count) {
                    return round(($entry->confirmation_pending_shipments / $entry->shipments_count) * 100, 2);
                }
                else {
                    return '';
                }
            })
            ->addColumn('pending_shipments', function ($entry) {
                if ($entry->shipments_count) {
                    return ($entry->shipments_count - ($entry->undelivered_shipments + $entry->delivered_shipments));
                }
                else {
                    return '';
                }
            })
            ->addColumn('pending_shipments_per', function ($entry) {
                if ($entry->shipments_count) {
                    return round((($entry->shipments_count - ($entry->undelivered_shipments + $entry->delivered_shipments)) / $entry->shipments_count) * 100, 2);
                }
                else {
                    return '';
                }
            });

        if($rider = $request->get('search_rider')){
            $datatables = $datatables->where('r.id', '=', $rider);
        }
        if($hub = $request->get('search_hub')){
            $datatables = $datatables->where('c.hub_id', '=', $hub);
        }
        if($zone = $request->get('search_zone')){
            $datatables = $datatables->where('c.zone_id', '=', $zone);
        }
        if($destination = $request->get('search_destination')){
            $datatables = $datatables->where('c.id', '=', $destination);
        }
        if ($search_rider_cat = $request->get('search_rider_cat')) {
            $datatables = $datatables->where('r.operation_rider_id', $search_rider_cat);
        }
        if ($request->get('search_from') && $request->get('search_to')) {
            $from = $request->get('search_from');
            $to = $request->get('search_to');
            $datatables = $datatables->whereBetween('delivery_notes.created_at', [$from,$to]);
        }

        return $datatables->make(true);

    }

    public function destination_delivery_received_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 199);
        $hubs = DB::connection('reports')->table('cities')->select('id', 'name')->where('hub', 1)->get();
        $destination_cities = DB::connection('reports')->table('cities')->select('id', 'name')->where('status', 1)->get();
        $zones = DB::connection('reports')->table('zones')->select('id', 'name')->get();
        return view('admin.reports.arrived_at_destination_out_for_delivery_and_received_report')->with(['hubs' => $hubs, 'destination_cities' => $destination_cities, 'zones' => $zones]);
    }

    public function destination_delivery_received_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 200);
        }
        if ($request->get('search_from') && $request->get('search_to')) {
            $from = $request->get('search_from');
            $to = $request->get('search_to');
        } else {
            $from = null;
            $to = null;
        }
        if ($type = $request->get('shipment_type')) {
            if ($type == 2) {
                $route_distribution_summary = DB::connection('reports')->table('cities')
                    ->leftjoin('user_shipping_infos as usi', 'usi.city_id', '=', 'cities.id')
                    ->leftjoin('shipments as s', 's.pickup_address_id', '=', 'usi.id')
                    ->leftJoin('shipments_journey as bsj', function ($join) use ($from, $to) {
                        $join->on('bsj.shipment_id', '=', 's.id')
                            ->where('bsj.shipper_status_id', 20)
                            ->whereBetween('bsj.created_at', [$from, $to]);
                    })
                    ->leftJoin('shipments_journey as asj', function ($join) use ($from, $to) {
                        $join->on('asj.shipment_id', '=', 's.id')
                            ->whereIn('asj.shipper_status_id', [22, 27])
                            ->whereBetween('asj.created_at', [$from, $to]);
                    })
                    ->leftJoin('shipments_journey as isj', function ($join) use ($from, $to) {
                        $join->on('isj.shipment_id', '=', 's.id')
                            ->whereIn('isj.shipper_status_id', [21, 26])
                            ->whereBetween('isj.created_at', [$from, $to]);
                    })
                    ->leftJoin('shipments_journey as osj', function ($join) use ($from, $to) {
                        $join->on('osj.shipment_id', '=', 's.id')
                            ->whereIn('osj.shipper_status_id', [23, 28])
                            ->whereBetween('osj.created_at', [$from, $to]);
                    })
                    ->leftJoin('shipments_journey as dsj', function ($join) use ($from, $to) {
                        $join->on('dsj.shipment_id', '=', 's.id')
                            ->whereIn('dsj.shipper_status_id', [25, 31])
                            ->whereBetween('dsj.created_at', [$from, $to]);
                    })
                    ->leftJoin('shipments_journey as usj', function ($join) use ($from, $to) {
                        $join->on('usj.shipment_id', '=', 's.id')
                            ->whereIn('usj.shipper_status_id', [24, 47, 48, 29])
                            ->whereBetween('usj.created_at', [$from, $to]);
                    })
                    ->leftJoin('shipments_journey as csj', function ($join) use ($from, $to) {
                        $join->on('csj.shipment_id', '=', 's.id')
                            ->where('csj.shipper_status_id', 12)
                            ->whereBetween('csj.created_at', [null, null]);
                    });

            } else {
                $route_distribution_summary = DB::connection('reports')->table('cities')
                    ->leftjoin('shipments as s', 's.consignee_city_id', '=', 'cities.id')
                    ->leftJoin('shipments_journey as bsj', function ($join) use ($from, $to) {
                        $join->on('bsj.shipment_id', '=', 's.id')
                            ->where('bsj.shipper_status_id', 1)
                            ->whereBetween('bsj.created_at', [$from, $to]);
                    })
                    ->leftJoin('shipments_journey as asj', function ($join) use ($from, $to) {
                        $join->on('asj.shipment_id', '=', 's.id')
                            ->whereIn('asj.shipper_status_id', [2, 4])
                            ->whereBetween('asj.created_at', [$from, $to]);
                    })
                    ->leftJoin('shipments_journey as isj', function ($join) use ($from, $to) {
                        $join->on('isj.shipment_id', '=', 's.id')
                            ->where('isj.shipper_status_id', 3)
                            ->whereBetween('isj.created_at', [$from, $to]);
                    })
                    ->leftJoin('shipments_journey as osj', function ($join) use ($from, $to) {
                        $join->on('osj.shipment_id', '=', 's.id')
                            ->where('osj.shipper_status_id', 5)
                            ->whereBetween('osj.created_at', [$from, $to]);
                    })
                    ->leftJoin('shipments_journey as dsj', function ($join) use ($from, $to) {
                        $join->on('dsj.shipment_id', '=', 's.id')
                            ->where('dsj.shipper_status_id', 14)
                            ->whereBetween('dsj.created_at', [$from, $to]);
                    })
                    ->leftJoin('shipments_journey as usj', function ($join) use ($from, $to) {
                        $join->on('usj.shipment_id', '=', 's.id')
                            ->where('usj.shipper_status_id', '<=', 14)
                            ->whereNotIn('usj.shipper_status_id', [1])
                            ->whereBetween('usj.created_at', [$from, $to]);
                    })
                    ->leftJoin('shipments_journey as csj', function ($join) use ($from, $to) {
                        $join->on('csj.shipment_id', '=', 's.id')
                            ->where('csj.shipper_status_id', 12)
                            ->whereBetween('csj.created_at', [$from, $to]);
                    });
            }
        } else {
            $route_distribution_summary = DB::connection('reports')->table('cities')
                ->leftjoin('shipments as s', 's.consignee_city_id', '=', 'cities.id')
                ->leftJoin('shipments_journey as bsj', function ($join) use ($from, $to) {
                    $join->on('bsj.shipment_id', '=', 's.id')
                        ->where('bsj.shipper_status_id', 1)
                        ->whereBetween('bsj.created_at', [$from, $to]);
                })
                ->leftJoin('shipments_journey as asj', function ($join) use ($from, $to) {
                    $join->on('asj.shipment_id', '=', 's.id')
                        ->whereIn('asj.shipper_status_id', [2, 4])
                        ->whereBetween('asj.created_at', [$from, $to]);
                })
                ->leftJoin('shipments_journey as isj', function ($join) use ($from, $to) {
                    $join->on('isj.shipment_id', '=', 's.id')
                        ->where('isj.shipper_status_id', 3)
                        ->whereBetween('isj.created_at', [$from, $to]);
                })
                ->leftJoin('shipments_journey as osj', function ($join) use ($from, $to) {
                    $join->on('osj.shipment_id', '=', 's.id')
                        ->where('osj.shipper_status_id', 5)
                        ->whereBetween('osj.created_at', [$from, $to]);
                })
                ->leftJoin('shipments_journey as dsj', function ($join) use ($from, $to) {
                    $join->on('dsj.shipment_id', '=', 's.id')
                        ->where('dsj.shipper_status_id', 14)
                        ->whereBetween('dsj.created_at', [$from, $to]);
                })
                ->leftJoin('shipments_journey as usj', function ($join) use ($from, $to) {
                    $join->on('usj.shipment_id', '=', 's.id')
                        ->where('usj.shipper_status_id', '<=', 14)
                        ->whereNotIn('usj.shipper_status_id', [1])
                        ->whereBetween('usj.created_at', [$from, $to]);
                })
                ->leftJoin('shipments_journey as csj', function ($join) use ($from, $to) {
                    $join->on('csj.shipment_id', '=', 's.id')
                        ->where('csj.shipper_status_id', 12)
                        ->whereBetween('csj.created_at', [$from, $to]);
                });
        }
        $route_distribution_summary = $route_distribution_summary
            ->select('cities.name as hub_name', DB::raw('count(bsj.id) as booked'), DB::raw('count(asj.id) as arrived_at_destination'), DB::raw('count(isj.id) as in_transit'), DB::raw('(count(asj.id)-count(isj.id)) as pending_arrived_at_destination'), DB::raw('count(osj.id) as out_for_delivery'), DB::raw('count(dsj.id) as delivered_shipments'), DB::raw('ROUND((count(dsj.id)/count(bsj.id))*100, 2) as delivered_shipments_per'), DB::raw('count(usj.id) as undelivered_shipments'), DB::raw('ROUND((count(usj.id)/count(bsj.id))*100, 2) as undelivered_shipments_per'), DB::raw('count(csj.id) as confirmation_pending_shipments'), DB::raw('ROUND((count(csj.id)/count(bsj.id))*100, 2) as confirmation_pending_shipments_per'))
            ->groupBy('cities.id');


        $datatables = Datatables::of($route_distribution_summary)
            ->editColumn('pending_arrived_at_destination', function ($request) {
                if ($request->pending_arrived_at_destination < 0) {
                    return 0;
                } else {
                    return $request->pending_arrived_at_destination;
                }
            });
        if ($hub = $request->get('search_hub')) {
            $datatables = $datatables->where('cities.id', '=', $hub);
        }
        if ($zone = $request->get('search_zone')) {
            $datatables = $datatables->where('cities.zone_id', '=', $zone);
        }
        if ($destination = $request->get('search_destination')) {
            $datatables = $datatables->where('cities.id', '=', $destination);
        }
        return $datatables->make(true);
    }

    public function cargo_short_received_shipments_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 203);
        return view('admin.reports.cargo_short_received_shipments');
    }

    public function cargo_short_received_shipments_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 204);
        }
        $cargo_consignments_short_received_shipments = DB::connection('reports')->table('cargo_consignments')->leftjoin('cargo_consignment_shipments as css', 'css.cargo_consignment_id', '=', 'cargo_consignments.id')
            ->leftjoin('cities as oc', 'oc.id', '=', 'cargo_consignments.origin_hub_id')
            ->leftjoin('cities as dc', 'dc.id', '=', 'cargo_consignments.destination_hub_id')
            ->leftjoin('shipping_modes as sm', 'sm.id', '=', 'cargo_consignments.shipping_mode_id')
            ->leftjoin('shipments as s', 's.id', '=', 'css.shipment_id')
            ->select('s.tracking_number as tracking_number', 'cargo_consignments.id as cargo', 'oc.name as origin', 'dc.name as destination', 'sm.mode as shipping_mode', 'cargo_consignments.type as cargo_type', 'cargo_consignments.created_at as transited_at')
            ->where('cargo_consignments.status_id', 4)
            ->whereIn('s.shipper_status_id', [3, 21])->get();


        $datatables = Datatables::of($cargo_consignments_short_received_shipments)
            ->editColumn('tracking_number_link', function ($shipments) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->editColumn('cargo_type', function ($shipments) {
                if ($shipments->cargo_type == 1) {
                    return 'Normal';
                } else {
                    return 'Return';
                }
            });
        return $datatables->make(true);
    }

    public function last_mile_status_index()
    {

        ActivityTrailController::createActivityTrailLog(Auth::id(), 205);
        $cities = DB::connection('reports')->table('cities')->get(['id', 'name']);
        $hubs = DB::connection('reports')->table('cities')->select(['id', 'name'])->where('hub', 1)->get();
        $zones = DB::connection('reports')->table('zones')->get();
        $riders = DB::connection('reports')->table('riders')->get(['id', 'name']);
        return view('admin.reports.last_mile_status')->with(['destinations' => $cities, 'hubs' => $hubs, 'zones' => $zones, 'riders' => $riders]);
    }

    public function last_mile_status_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 206);
        }
        $date_from = $request->get('search_date_from');
        $date_to = $request->get('search_date_to');
        $search_destination = $request->get('search_destination');
        $search_hub = $request->get('search_hub');
        $search_zone = $request->get('search_zone');
        $search_rider = $request->get('search_rider');
        $data = self::last_mile_status_data($date_from, $date_to, $search_destination, $search_hub, $search_zone, $search_rider);
        return response()->json(['status' => 0, 'time_slots' => $data]);
    }

    public function last_mile_status_data($from, $to, $destination, $hub, $zone, $rider)
    {
        $data = array();
        $from = Carbon::parse($from)->toDateString();
        $to = Carbon::parse($to)->toDateString();

        if ($hub != null) {
            $hub_cities = DB::connection('reports')->table('cities')->where('hub_id', $hub)->pluck('id')->toArray();
        }
        if ($zone != null) {
            $zone_cities = DB::connection('reports')->table('cities')->where('zone_id', $zone)->pluck('id')->toArray();
        }

        $time_slots = array(1 => '9 AM - 12 PM', 2 => '12 PM - 3 PM', 3 => '3 PM - 6 PM', 4 => '6 PM - 9 PM', 5 => '9 PM - 12 AM', 6 => '12 AM - 9 AM');
        $sum_total_status_updated = 0;
        $sum_out_for_delivery_count = 0;
        $sum_bolt_status_updated = 0;
        $sum_bolt_status_percentage = 0;
        $sum_sonic_status_updated = 0;
        $sum_sonic_status_percentage = 0;
        $sum_out_for_delivery_percentage = 0;
        $sum_total_shipment_deliverd_bolt = 0;
        $sum_total_shipment_undeliverd_bolt = 0;
        $sum_total_shipment_deliverd_sonic = 0;
        $sum_total_shipment_undeliverd_sonic = 0;
        $delivery_note_status = array(7, 8, 9, 10, 11, 12, 14, 15, 18, 29, 30, 35, 56);
        $undelivered_status = array(7, 8, 9, 12, 15, 18, 56, 29, 10, 11, 35);
        $delivered_status = array(14, 30, 36, 37);
        foreach ($time_slots as $id => $slot) {
            $total_status_updated_count = 0;
            $bolt_status_updated_count = 0;
            $sonic_status_updated_count = 0;
            $start_time = NULL;
            $end_time = NULL;
            if ($id == 1) {
                $start_time = '09:00:01';
                $end_time = '12:00:00';
            } else if ($id == 2) {
                $start_time = '12:00:01';
                $end_time = '15:00:00';
            } else if ($id == 3) {
                $start_time = '15:00:01';
                $end_time = '18:00:00';
            } else if ($id == 4) {
                $start_time = '18:00:01';
                $end_time = '21:00:00';
            } else if ($id == 5) {
                $start_time = '21:00:01';
                $end_time = '00:00:00';
            } else if ($id == 6) {
                $start_time = '00:00:01';
                $end_time = '09:00:00';
            }
            $start_time = Carbon::parse($start_time)->toTimeString();
            $end_time = Carbon::parse($end_time)->toTimeString();

            $time_array = array();

            $total_status_updated = ShipmentsJourney::whereNotNull('reference_1_id')->whereIn('shipper_status_id', $delivery_note_status)->where('verification', 1);
            $total_status_updated = $total_status_updated->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to);
            $total_status_updated = $total_status_updated->whereTime('created_at', '>=', $start_time)->whereTime('created_at', '<=', $end_time);
            if ($destination != null) {
                $total_status_updated = $total_status_updated->where('city_id', $destination);
            }
            if ($hub != null) {
                $total_status_updated = $total_status_updated->whereIn('city_id', $hub_cities);
            }
            if ($zone != null) {
                $total_status_updated = $total_status_updated->whereIn('city_id', $zone_cities);
            }
            if ($rider != null) {
                $total_status_updated = $total_status_updated->where('rider_id', $rider);
            }

            $total_status_updated_count = $total_status_updated->distinct('shipment_id')->count('shipment_id');

            $out_for_delivery = DB::connection('reports')->table('shipments_journey')->where('shipper_status_id', 5);
            $out_for_delivery = $out_for_delivery->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to);
            $out_for_delivery = $out_for_delivery->whereTime('created_at', '>=', $start_time)->whereTime('created_at', '<=', $end_time);
            if ($destination != null) {
                $out_for_delivery = $out_for_delivery->where('city_id', $destination);
            }
            if ($hub != null) {
                $out_for_delivery = $out_for_delivery->whereIn('city_id', $hub_cities);
            }
            if ($zone != null) {
                $out_for_delivery = $out_for_delivery->whereIn('city_id', $zone_cities);
            }
            if ($rider != null) {
                $out_for_delivery = $out_for_delivery->where('rider_id', $rider);
            }

            $out_for_delivery_count = $out_for_delivery->distinct('shipment_id')->count('shipment_id');
            $out_for_delivery_percentage = 0;
            if ($out_for_delivery_count > 0) {
                $out_for_delivery_percentage = ($total_status_updated_count / $out_for_delivery_count) * 100;
            }
            $bolt_status_updated = ShipmentsJourney::whereNotNull('reference_1_id')->whereIn('shipper_status_id', $delivery_note_status)->where('verification', 1)->whereNotNull('rider_id');
            $bolt_status_updated = $bolt_status_updated->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to);
            $bolt_status_updated = $bolt_status_updated->whereTime('created_at', '>=', $start_time)->whereTime('created_at', '<=', $end_time);
            if ($destination != null) {
                $bolt_status_updated = $bolt_status_updated->where('city_id', $destination);
            }
            if ($hub != null) {
                $bolt_status_updated = $bolt_status_updated->whereIn('city_id', $hub_cities);
            }
            if ($zone != null) {
                $bolt_status_updated = $bolt_status_updated->whereIn('city_id', $zone_cities);
            }
            if ($rider != null) {
                $bolt_status_updated = $bolt_status_updated->where('rider_id', $rider);
            }
            $bolt_status_updated_count = $bolt_status_updated->distinct('shipment_id')->count('shipment_id');

            $bolt_status_percentage = 0;
            if ($total_status_updated_count > 0) {
                $bolt_status_percentage = ($bolt_status_updated_count / $total_status_updated_count) * 100;
            }
            $out_for_delivery_percentage = 0;
            if ($out_for_delivery_count > 0) {
                $out_for_delivery_percentage = ($total_status_updated_count / $out_for_delivery_count) * 100;
            }
            $sonic_status_updated = ShipmentsJourney::whereNotNull('reference_1_id')->whereIn('shipper_status_id', $delivery_note_status)->where('verification', 1)->whereNull('rider_id');
            $sonic_status_updated = $sonic_status_updated->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to);
            $sonic_status_updated = $sonic_status_updated->whereTime('created_at', '>=', $start_time)->whereTime('created_at', '<=', $end_time);
            if ($destination != null) {
                $sonic_status_updated = $sonic_status_updated->where('city_id', $destination);
            }
            if ($hub != null) {
                $sonic_status_updated = $sonic_status_updated->whereIn('city_id', $hub_cities);
            }
            if ($zone != null) {
                $sonic_status_updated = $sonic_status_updated->whereIn('city_id', $zone_cities);
            }
            if ($rider != null) {
                $sonic_status_updated = $sonic_status_updated->where('rider_id', $rider);
            }
            $sonic_status_updated_count = $sonic_status_updated->distinct('shipment_id')->count('shipment_id');

            $sonic_status_percentage = 0;
            if ($total_status_updated_count > 0) {
                $sonic_status_percentage = ($sonic_status_updated_count / $total_status_updated_count) * 100;
            }

            $bolt_status_undelivered = ShipmentsJourney::whereNotNull('reference_1_id')->whereIn('shipper_status_id', $undelivered_status)->where('verification', 1)->whereNotNull('rider_id');
            $bolt_status_undelivered = $bolt_status_undelivered->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to);
            $bolt_status_undelivered = $bolt_status_undelivered->whereTime('created_at', '>=', $start_time)->whereTime('created_at', '<=', $end_time);
            if ($destination != null) {
                $bolt_status_undelivered = $bolt_status_undelivered->where('city_id', $destination);
            }
            if ($hub != null) {
                $bolt_status_undelivered = $bolt_status_undelivered->whereIn('city_id', $hub_cities);
            }
            if ($zone != null) {
                $bolt_status_undelivered = $bolt_status_undelivered->whereIn('city_id', $zone_cities);
            }
            if ($rider != null) {
                $bolt_status_undelivered = $bolt_status_undelivered->where('rider_id', $rider);
            }
            $bolt_status_undelivered_count = $bolt_status_undelivered->distinct('shipment_id')->count('shipment_id');


            $bolt_status_delivered = ShipmentsJourney::whereNotNull('reference_1_id')->whereIn('shipper_status_id', $delivered_status)->where('verification', 1)->whereNotNull('rider_id');
            $bolt_status_delivered = $bolt_status_delivered->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to);
            $bolt_status_delivered = $bolt_status_delivered->whereTime('created_at', '>=', $start_time)->whereTime('created_at', '<=', $end_time);
            if ($destination != null) {
                $bolt_status_delivered = $bolt_status_delivered->where('city_id', $destination);
            }
            if ($hub != null) {
                $bolt_status_delivered = $bolt_status_delivered->whereIn('city_id', $hub_cities);
            }
            if ($zone != null) {
                $bolt_status_delivered = $bolt_status_delivered->whereIn('city_id', $zone_cities);
            }
            if ($rider != null) {
                $bolt_status_delivered = $bolt_status_delivered->where('rider_id', $rider);
            }
            $bolt_status_delivered_count = $bolt_status_delivered->distinct('shipment_id')->count('shipment_id');


            $sonic_status_undelivered = ShipmentsJourney::whereNotNull('reference_1_id')->whereIn('shipper_status_id', $undelivered_status)->where('verification', 1)->whereNull('rider_id');
            $sonic_status_undelivered = $sonic_status_undelivered->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to);
            $sonic_status_undelivered = $sonic_status_undelivered->whereTime('created_at', '>=', $start_time)->whereTime('created_at', '<=', $end_time);
            if ($destination != null) {
                $sonic_status_undelivered = $sonic_status_undelivered->where('city_id', $destination);
            }
            if ($hub != null) {
                $sonic_status_undelivered = $sonic_status_undelivered->whereIn('city_id', $hub_cities);
            }
            if ($zone != null) {
                $sonic_status_undelivered = $sonic_status_undelivered->whereIn('city_id', $zone_cities);
            }
            if ($rider != null) {
                $sonic_status_undelivered = $sonic_status_undelivered->where('rider_id', $rider);
            }
            $sonic_status_undelivered_count = $sonic_status_undelivered->distinct('shipment_id')->count('shipment_id');

            $sonic_status_delivered = ShipmentsJourney::whereNotNull('reference_1_id')->whereIn('shipper_status_id', $delivered_status)->where('verification', 1)->whereNull('rider_id');
            $sonic_status_delivered = $sonic_status_delivered->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to);
            $sonic_status_delivered = $sonic_status_delivered->whereTime('created_at', '>=', $start_time)->whereTime('created_at', '<=', $end_time);
            if ($destination != null) {
                $sonic_status_delivered = $sonic_status_delivered->where('city_id', $destination);
            }
            if ($hub != null) {
                $sonic_status_delivered = $sonic_status_delivered->whereIn('city_id', $hub_cities);
            }
            if ($zone != null) {
                $sonic_status_delivered = $sonic_status_delivered->whereIn('city_id', $zone_cities);
            }
            if ($rider != null) {
                $sonic_status_delivered = $sonic_status_delivered->where('rider_id', $rider);
            }
            $sonic_status_delivered_count = $sonic_status_delivered->distinct('shipment_id')->count('shipment_id');


            $time_array['time'] = $slot;
            $time_array['total_status_updated'] = $total_status_updated_count;
            $time_array['out_for_delivery_count'] = $out_for_delivery_count;
            $time_array['out_for_delivery_percentage'] = round($out_for_delivery_percentage, 2) . '%';
            $time_array['bolt_status_updated'] = $bolt_status_updated_count;
            $time_array['bolt_status_percentage'] = round($bolt_status_percentage, 2) . '%';
            $time_array['sonic_status_updated'] = $sonic_status_updated_count;
            $time_array['sonic_status_percentage'] = round($sonic_status_percentage, 2) . '%';
            $time_array['total_shipment_deliverd_bolt'] = $bolt_status_delivered_count;
            $time_array['total_shipment_undeliverd_bolt'] = $bolt_status_undelivered_count;
            $time_array['total_shipment_deliverd_sonic'] = $sonic_status_delivered_count;
            $time_array['total_shipment_undeliverd_sonic'] = $sonic_status_undelivered_count;
            $sum_total_status_updated = $sum_total_status_updated + $total_status_updated_count;
            $sum_out_for_delivery_count = $sum_out_for_delivery_count + $out_for_delivery_count;
            $sum_bolt_status_updated = $sum_bolt_status_updated + $bolt_status_updated_count;
            $sum_sonic_status_updated = $sum_sonic_status_updated + $sonic_status_updated_count;
            $sum_total_shipment_deliverd_bolt = $sum_total_shipment_deliverd_bolt + $bolt_status_delivered_count;
            $sum_total_shipment_undeliverd_bolt = $sum_total_shipment_undeliverd_bolt + $bolt_status_undelivered_count;
            $sum_total_shipment_deliverd_sonic = $sum_total_shipment_deliverd_sonic + $sonic_status_delivered_count;
            $sum_total_shipment_undeliverd_sonic = $sum_total_shipment_undeliverd_sonic + $sonic_status_undelivered_count;
            $data[] = $time_array;
        }

        if ($sum_total_status_updated > 0) {
            $sum_bolt_status_percentage = ($sum_bolt_status_updated / $sum_total_status_updated) * 100;
            $sum_sonic_status_percentage = ($sum_sonic_status_updated / $sum_total_status_updated) * 100;
            $sum_out_for_delivery_percentage = ($sum_total_status_updated / $sum_out_for_delivery_count) * 100;

        }

        $data[] = array('time' => 'Total', 'out_for_delivery_count' => $sum_out_for_delivery_count, 'total_status_updated' => $sum_total_status_updated, 'out_for_delivery_percentage' => round($sum_out_for_delivery_percentage, 2) . '%', 'bolt_status_updated' => $sum_bolt_status_updated, 'bolt_status_percentage' => round($sum_bolt_status_percentage, 2) . '%', 'sonic_status_updated' => $sum_sonic_status_updated, 'sonic_status_percentage' => round($sum_sonic_status_percentage, 2) . '%', 'total_shipment_deliverd_bolt' => $sum_total_shipment_deliverd_bolt, 'total_shipment_undeliverd_bolt' => $sum_total_shipment_undeliverd_bolt, 'total_shipment_deliverd_sonic' => $sum_total_shipment_deliverd_sonic, 'total_shipment_undeliverd_sonic' => $sum_total_shipment_undeliverd_sonic);

        return $data;
    }

    public function completed_aging_index(Request $request)
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 209);
        return view('admin.reports.completed_aging_report');
    }

    public function completed_aging_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 210);
        }
        $today = Carbon::now()->startOfDay();
        $aging_report = DB::connection('reports')->table('completed_aging_reports')
            ->join('cities AS c', 'completed_aging_reports.hub_id', '=', 'c.id')
            ->join('zones AS z', 'completed_aging_reports.zone_id', '=', 'z.id')
            ->select(['completed_aging_reports.id as id', 'c.name as hubs', 'z.name as zone', 'completed_aging_reports.count'])
            ->whereDate('completed_aging_reports.date', $today);
        $report = Datatables::of($aging_report);

        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $report->whereBetween('completed_aging_reports.date', [$from, $to]);
        }


        return $report->make(true);

    }

    public function pending_cash_collection_index(Request $request)
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 211);
        return view('admin.reports.pending_cash_collection_report');
    }

    public function pending_cash_collection_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 212);
        }
        $today = Carbon::now()->startOfDay();
        $aging_report = DB::connection('reports')->table('pending_cash_collection_aging_reports')
            ->join('cities AS c', 'pending_cash_collection_aging_reports.hub_id', '=', 'c.id')
            ->join('zones AS z', 'pending_cash_collection_aging_reports.zone_id', '=', 'z.id')
            ->select(['pending_cash_collection_aging_reports.id as id', 'c.name as hubs', 'z.name as zone', 'pending_cash_collection_aging_reports.count'])
            ->whereDate('pending_cash_collection_aging_reports.date', $today);
        $report = Datatables::of($aging_report);

        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $report->whereBetween('pending_cash_collection_aging_reports.created_at', [$from, $to]);
        }


        return $report->make(true);
    }

    public function not_attempted_aging_index(Request $request)
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 215);
        return view('admin.reports.not_attempted_aging_report');
    }

    public function not_attempted_aging_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 216);
        }
        if ($request->has('date')) {
            $date = $request->get('date');
            $date_from = Carbon::parse($date)->startOfDay()->toDateTimeString();
            $date_to = Carbon::parse($date)->endOfDay()->toDateTimeString();
        }
        $not_attempting_aging_report = DB::connection('reports')->table('not_attempted_shipment_agings')
            ->join('cities as h', 'not_attempted_shipment_agings.hub_id', '=', 'h.id')
            ->join('zones as z', 'not_attempted_shipment_agings.zone_id', '=', 'z.id')
            ->select(['h.name as hub', 'not_attempted_shipment_agings.zero as zero', 'not_attempted_shipment_agings.one as one', 'not_attempted_shipment_agings.two as two', 'not_attempted_shipment_agings.three as three', 'not_attempted_shipment_agings.four as four', 'not_attempted_shipment_agings.five as five', 'not_attempted_shipment_agings.six_plus as six_plus'])
            ->whereBetween('not_attempted_shipment_agings.created_at', [$date_from, $date_to]);
        $report = Datatables::of($not_attempting_aging_report);
        return $report->make(true);
    }

    public function station_recovery_index(Request $request)
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 213);
        $banks_list = BanksList::where('status', 1)->select('id', 'name')->get();
        return view('admin.reports.station_recovery')->with('banks_lists', $banks_list);
    }

    public function station_recovery_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 214);
        }
        $station_recovery = DB::connection('reports')->table('station_recovery_reports')->join('cities as h', 'h.id', '=', 'station_recovery_reports.city_id')
            ->join('zones', 'zones.id', '=', 'station_recovery_reports.zone_id')
            ->select('station_recovery_reports.id as recovery_id', 'h.name as hub', 'zones.name as zone', 'station_recovery_reports.delivered_shipments', 'station_recovery_reports.last_day_balance', 'station_recovery_reports.amount', 'station_recovery_reports.total_amount', 'station_recovery_reports.deposit_amount', 'station_recovery_reports.adjustment_amount', 'station_recovery_reports.difference_amount', 'station_recovery_reports.percentage', 'station_recovery_reports.reason', 'station_recovery_reports.date');
        $datatable = Datatables::of($station_recovery)
            ->editColumn('last_day_balance', function ($recovery) {
                return number_format($recovery->last_day_balance);
            })
            ->editColumn('amount', function ($recovery) {
                return number_format($recovery->amount);
            })
            ->editColumn('total_amount', function ($recovery) {
                return number_format($recovery->total_amount);
            })
            ->editColumn('percentage', function ($recovery) {
                if ($recovery->percentage == 0) {
                    return '0%';
                } else {
                    return $recovery->percentage . '%';
                }
            })
            ->addColumn('banks_list', function ($recovery) {
                $banks_list = '';
                if (StationRecoveryReportDeposit::where('station_recovery_report_id', $recovery->recovery_id)->exists()) {
                    $banks = StationRecoveryReportDeposit::where('station_recovery_report_id', $recovery->recovery_id)->get();
                    $bank_ids = '';
                    foreach ($banks as $index => $bank) {
                        $index++;
                        $banklist = BanksList::find($bank->bank_id);
                        $banks_list .= $banklist->name;
                        $bank_ids .= $banklist->id;
                        if ($index != count($banks)) {
                            $banks_list .= ',';
                            $bank_ids .= ',';
                        }
                    }
                    $html = '<input type="hidden" name="bank_ids" value="' . $bank_ids . '">';
                    $banks_list = $banks_list . $html;
                    return $banks_list;
                }
                return $banks_list;
            })
            ->addColumn('banks_list_excel', function ($recovery) {
                $banks_list = '';
                if (StationRecoveryReportDeposit::where('station_recovery_report_id', $recovery->recovery_id)->exists()) {
                    $banks = StationRecoveryReportDeposit::where('station_recovery_report_id', $recovery->recovery_id)->get();
                    $bank_ids = '';
                    foreach ($banks as $index => $bank) {
                        $index++;
                        $banklist = BanksList::find($bank->bank_id);
                        $banks_list .= $banklist->name;
                        $bank_ids .= $banklist->id;
                        if ($index != count($banks)) {
                            $banks_list .= ',';
                            $bank_ids .= ',';
                        }
                    }
                    return $banks_list;
                }
                return $banks_list;
            });
        if ($request->get('search_date')) {
            $date = $request->get('search_date');
            $datatable = $datatable->whereDate('station_recovery_reports.date', $date);
        }
        return $datatable->make(true);

    }

    public function station_recovery_update(Request $request)
    {

        if ($request->form_save == 1) {
            if ($request->has('deposit_amount') && count($request->deposit_amount) > 0) {
                foreach ($request->deposit_amount as $key => $value) {
                    $station_recovery = StationRecoveryReport::find($key);
                    $station_recovery->deposit_amount = $value;
                    $station_recovery->adjustment_amount = $request->adjustment_amount[$key];
                    $station_recovery->reason = $request->reason[$key];
                    $station_recovery->save();
                    $station_recovery->refresh();
                    $difference = $station_recovery->total_amount - $station_recovery->deposit_amount - $station_recovery->adjustment_amount;
                    $station_recovery->difference_amount = $difference;
                    if ($station_recovery->total_amount > 0) {
                        $percentage = (($station_recovery->deposit_amount + $station_recovery->adjustment_amount) / $station_recovery->total_amount) * 100;
                        $station_recovery->percentage = $percentage;
                    }
                    $station_recovery->save();

                    $bank_row = "bank_select.$key";
                    if ($request->has($bank_row)) {
                        StationRecoveryReportDeposit::where('station_recovery_report_id', $key)->delete();
                        foreach ($request->bank_select[$key] as $row => $bank) {
                            $deposit = new StationRecoveryReportDeposit();
                            $deposit->station_recovery_report_id = $key;
                            $deposit->bank_id = $bank;
                            $deposit->admin_id = Auth::id();
                            $deposit->save();
                        }
                    }
                }
                return redirect()->back()->with('success', 'Report updated successfully!');
            }
            return redirect()->back()->with('error', 'Please refresh page and update properly!');

        }
    }

    public function daily_monthly_adjustment_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 217);
        return view('admin.reports.daily_monthly_adjustment');
    }

    public function daily_monthly_adjustment_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 218);
        }
        $adjustments = DB::connection('reports')->table('adjustment_logs')
            ->leftjoin('done_payment_shipments as dps', 'dps.id', '=', 'adjustment_logs.done_id')
            ->leftjoin('shipments as s', 's.id', '=', 'adjustment_logs.shipment_id')
            ->join('cities AS dc', 's.consignee_city_id', '=', 'dc.id')
            ->join('cities AS h', 'dc.hub_id', '=', 'h.id')
            ->select('s.id as shipment_id', 's.tracking_number as tracking_number', 'h.name as hub')
            ->whereIn('adjustment_logs.type', [1, 2]);
        $datatable = Datatables::of($adjustments)
            ->addColumn('tracking_number_link', function ($shipments) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            });
        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $datatable = $datatable->whereBetween('adjustment_logs.created_at', [$from, $to]);
        }
        return $datatable->make(true);
    }

    public function daily_monthly_adjustment_summary_list(Request $request)
    {
        $adjustments_count = DB::connection('reports')->table('adjustment_logs')
            ->leftjoin('done_payment_shipments as dps', 'dps.id', '=', 'adjustment_logs.done_id')
            ->leftjoin('shipments as s', 's.id', '=', 'adjustment_logs.shipment_id')
            ->whereIn('adjustment_logs.type', [1, 2]);

        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $adjustments_count = $adjustments_count->whereBetween('adjustment_logs.created_at', [$from, $to]);
        }
        $adjustments_count = $adjustments_count->count();

        $adjustments = DB::connection('reports')->table('adjustment_logs')
            ->leftjoin('done_payment_shipments as dps', 'dps.id', '=', 'adjustment_logs.done_id')
            ->leftjoin('shipments as s', 's.id', '=', 'adjustment_logs.shipment_id')
            ->join('cities AS dc', 's.consignee_city_id', '=', 'dc.id')
            ->join('cities AS h', 'dc.hub_id', '=', 'h.id')
            ->select('h.name as hub', DB::raw('(select count(s.id)) as shipment_count'), DB::raw('(ROUND((count(s.id)/' . $adjustments_count . ')*100, 0)) as ratio'))
            ->whereIn('adjustment_logs.type', [1, 2])
            ->groupBy('h.id');
        $datatable = Datatables::of($adjustments);
        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $datatable = $datatable->whereBetween('adjustment_logs.created_at', [$from, $to]);
        }
        return $datatable->make(true);
    }

    public function app_efficiency_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 221);
        $riders = Rider::select('id', 'name')->get();
        return view('admin.reports.app_efficiency')->with(['riders' => $riders]);
    }

    public function app_efficiency_list(Request $request)
    {

        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 222);
        }
        $v2_rider_pickups = V2RiderPickup::join('v2_pickup_requests as vpr', 'vpr.id', '=', 'v2_rider_pickups.pickup_request_id')
            ->join('v2_pickup_notes as vpn', 'vpn.id', '=', 'v2_rider_pickups.pickup_note_id')
            ->join('riders as r', 'r.id', '=', 'vpn.rider_id')
            ->join('user_shipping_infos as usi', 'usi.id', '=', 'vpr.pickup_address_id')
            ->join('users as u', 'u.id', '=', 'usi.user_id')
            ->join('cities as c', 'c.id', '=', 'vpr.city_id')
            ->join('cities as ci', 'c.hub_id', '=', 'ci.id')
            ->join('v2_pickup_request_statuses as vprs', 'vprs.id', '=', 'vpr.status_id')
            ->select('v2_rider_pickups.pickup_request_id as request_id', 'v2_rider_pickups.pickup_note_id as note_id', 'u.name as shipper_name', 'r.name as rider', 'usi.vendor as vendor', 'usi.pickup_address as address', 'c.name as city', 'ci.name as hub', 'v2_rider_pickups.created_at', 'vprs.name as status', 'v2_rider_pickups.created_at as created_at', 'r.id as rider_id');

        $datatable = Datatables::of($v2_rider_pickups);

        if ($rider_id = $request->get('rider')) {
            $v2_rider_pickups = $v2_rider_pickups->where('vpr.current_rider_id', '=', $rider_id);
        }
        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $datatable->whereBetween('v2_rider_pickups.created_at', [$from, $to]);
        }
        return $datatable->make(true);
    }

//    public function confirmation_shipments_index(){
//        $shipment_status = ShipmentStatus::select('id','name')->get();
//        $return_confirm_reason_ids = DB::table('shipment_status_shipment_status_reason')->where('shipment_status_id', 20)->where('shipment_status_reason_id','<>', 2)->pluck('shipment_status_reason_id')->toArray();
//        $return_confirm_reasons = ShipmentStatusReason::whereIn('id', $return_confirm_reason_ids)->select('id', 'name')->get();
//        $agents = AdminRole::leftjoin('admins as a', 'a.role_id', '=', 'admin_roles.id')->where('admin_roles.department_id', 3)->get();
//        return view('admin.reports.confirmation_pending_report')->with(['shipment_status'=>$shipment_status, 'return_confirm_reasons' => $return_confirm_reasons, 'agents' => $agents]);
//    }
//
//    public function confirmation_shipments_list(Request $request){
//        $shipments = Shipment::join('users as u', 'shipments.user_id', '=', 'u.id')
//            ->join('shipment_status as ss','ss.id','=','shipments.shipper_status_id')
//            ->leftJoin('shipments_journey', function ($join) {
//                $join->on('shipments_journey.shipment_id', '=', 'shipments.id')
//                    ->where('shipments_journey.id','=',
//                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id)'));
//            })
//            ->leftJoin('shipments_journey as sret', function ($join) {
//                $join->on('sret.shipment_id', '=', 'shipments.id')
//                    ->where('sret.id','=',
//                        DB::raw('(select min(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 12 and shipments_journey.verification = 1)'));
//            })
//            ->leftJoin('shipment_status_reason as ssr','ssr.id','=','sret.status_reason_id')
//            ->select('shipments.id as shipment_id', 'shipments.id as shId', 'shipments.shipper_status_id','shipments.tracking_number','shipments.tracking_number as tracking', 'ss.name as status','ssr.id as reason_id','ssr.name as reason', 'sret.remarks as remarks','sret.created_at as status_date')
////            ->whereIn('shipments.shipper_status_id', [12, 20, 13, 54, 55, 5, 23])
//            ->where('sret.verification', 1)
//            ->groupBy('shipments.id');
//        if(session('department_id') == 7){
//            if(session('role_id') != 4 ){
//                $shipments = $shipments->where(function ($query) {
//                    $query->whereIn('u.id', session('tagged_shippers'));
//                });
//            }
//        }
//
////        if(count($shipments) <2) {
////
////        }
//
//        $datatable = Datatables::of($shipments)
//            ->editColumn('tracking',function ($shipments){
//                $route = route('admin.tracking.index');
//                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
//            });
//        if ($tracking_numbers = $request->get('tracking_numbers')) {
//            $datatable->whereIn('shipments.tracking_number', explode(',', $tracking_numbers));
//        }
//        if ($request->get('dr_search_date_from') && $request->get('dr_search_date_to')) {
//            $from = $request->get('dr_search_date_from');
//            $to = $request->get('dr_search_date_to');
//            $datatable->whereBetween('sret.created_at', [$from,$to]);
//        }
//        return $datatable->make(true);
//    }

    static public function daily_pickup_sales_shipping_mode_wise($date_from, $date_to, $hub = null, $sales_tagging, $sales_person)
    {

        $data = array();
        $shipping_modes = ShippingMode::all();

        if ($sales_tagging == TRUE) {
            if ($hub != null) {
                $city = DB::connection('reports')->table('cities')->select('id', 'name');
                $city = $city->where('id', $hub);
                $city = $city->first();
            }
        } else {
            if ($hub != null) {
                $city = DB::connection('reports')->table('cities')->select('id', 'name');
                $city = $city->where('id', $hub);
                $city = $city->first();
            }
        }
        if ($sales_tagging == true) {
            if ($hub != null) {
                $serial = 1;
                foreach ($shipping_modes as $mode) {

                    $booked = 0;
                    $received = 0;
                    $cod_collection = 0;
                    $actual_weight = 0;
                    $chargeable_weight = 0;
                    $revenue_wo_gst = 0;

                    if (session('department_id') != 7) {
                        $booked = DB::connection('reports')->table('shipments')
                        ->join('user_shipping_infos as usi', 'usi.id', 'shipments.pickup_address_id')
                        ->where('shipments.packaging_material_request', 0)
                        ->where('shipments.user_id', '!=', 1690)
                        ->whereBetween('shipments.created_at', [$date_from, $date_to])
                        ->where('usi.city_id', $city->id)
                        ->where('shipments.shipping_mode_id', $mode->id)
                        ->count();

                        $received = DB::connection('reports')->table('shipments_journey')
                        ->join('shipments as s', 's.id', 'shipments_journey.shipment_id')
                        ->join('user_shipping_infos as usi', 'usi.id', 's.pickup_address_id')
                        ->where('s.packaging_material_request', 0)
                        ->where('s.user_id', '!=', 1690)
                        ->whereBetween('shipments_journey.created_at', [$date_from, $date_to])
                        ->where('shipments_journey.shipper_status_id', 2)
                        ->where('usi.city_id', $city->id)
                        ->where('s.shipping_mode_id', $mode->id)
                        ->count();

                        if($received > 0){
                            $shipments_data = array();

                            $shipments_data = DB::connection('reports')->table('shipments_journey')
                            ->join('shipments as s', 's.id', 'shipments_journey.shipment_id')
                            ->join('user_shipping_infos as usi', 'usi.id', 's.pickup_address_id')
                            ->where('s.packaging_material_request', 0)
                            ->where('s.user_id', '!=', 1690)
                            ->whereBetween('shipments_journey.created_at', [$date_from, $date_to])
                            ->where('shipments_journey.shipper_status_id', 2)
                            ->where('usi.city_id', $city->id)
                            ->where('s.shipping_mode_id', $mode->id)
                            ->select(DB::connection('reports')->raw('SUM(IFNULL(weight_charges,0) + IFNULL(cash_handling_charges,0) + IFNULL(insurance_charges,0) + IFNULL(return_charges,0) + IFNULL(fuel_surcharge,0) + IFNULL(replacement_charges,0) + IFNULL(try_and_buy_charges,0)) as revenue_wo_gst'), DB::connection('reports')->raw('SUM(amount) as cod_collection'), DB::connection('reports')->raw('SUM(actual_weight) as actual_weight'), DB::connection('reports')->raw('SUM(chargeable_weight) as chargeable_weight'))->first();

                            $revenue_wo_gst = $shipments_data->revenue_wo_gst;
                            $actual_weight = $shipments_data->actual_weight;
                            $chargeable_weight = $shipments_data->chargeable_weight;
                            $cod_collection = $shipments_data->cod_collection;
                        }
                    }
                    else {
                        if (!in_array(session('id'), session('sale_users_bypass'))) {
                            $booked = DB::connection('reports')->table('shipments')
                            ->join('user_shipping_infos as usi', 'usi.id', 'shipments.pickup_address_id')
                            ->where('shipments.packaging_material_request', 0)
                            ->where('shipments.user_id', '!=', 1690)
                            ->whereBetween('shipments.created_at', [$date_from, $date_to])
                            ->where('usi.city_id', $city->id)
                            ->whereIn('shipments.user_id', session('tagged_shippers'))
                            ->where('shipments.shipping_mode_id', $mode->id)
                            ->count();

                            $received = DB::connection('reports')->table('shipments_journey')
                            ->join('shipments as s', 's.id', 'shipments_journey.shipment_id')
                            ->join('user_shipping_infos as usi', 'usi.id', 's.pickup_address_id')
                            ->where('s.packaging_material_request', 0)
                            ->where('s.user_id', '!=', 1690)
                            ->whereBetween('shipments_journey.created_at', [$date_from, $date_to])
                            ->where('shipments_journey.shipper_status_id', 2)
                            ->where('usi.city_id', $city->id)
                            ->whereIn('s.user_id', session('tagged_shippers'))
                            ->where('s.shipping_mode_id', $mode->id)
                            ->count();

                            if($received > 0){
                                $shipments_data = array();

                                $shipments_data = DB::connection('reports')->table('shipments_journey')
                                ->join('shipments as s', 's.id', 'shipments_journey.shipment_id')
                                ->join('user_shipping_infos as usi', 'usi.id', 's.pickup_address_id')
                                ->where('s.packaging_material_request', 0)
                                ->where('s.user_id', '!=', 1690)
                                ->whereBetween('shipments_journey.created_at', [$date_from, $date_to])
                                ->where('shipments_journey.shipper_status_id', 2)
                                ->where('usi.city_id', $city->id)
                                ->whereIn('s.user_id', session('tagged_shippers'))
                                ->where('s.shipping_mode_id', $mode->id)
                                ->select(DB::connection('reports')->raw('SUM(IFNULL(weight_charges,0) + IFNULL(cash_handling_charges,0) + IFNULL(insurance_charges,0) + IFNULL(return_charges,0) + IFNULL(fuel_surcharge,0) + IFNULL(replacement_charges,0) + IFNULL(try_and_buy_charges,0)) as revenue_wo_gst'), DB::connection('reports')->raw('SUM(amount) as cod_collection'), DB::connection('reports')->raw('SUM(actual_weight) as actual_weight'), DB::connection('reports')->raw('SUM(chargeable_weight) as chargeable_weight'))->first();

                                $revenue_wo_gst = $shipments_data->revenue_wo_gst;
                                $actual_weight = $shipments_data->actual_weight;
                                $chargeable_weight = $shipments_data->chargeable_weight;
                                $cod_collection = $shipments_data->cod_collection;
                            }
                        }
                        else {
                            if ($sales_person != null) {
                                $tagged_shippers = DB::connection('reports')->table('sale_person_tags')->where('admin_id', $sales_person)->select('user_id')->pluck('user_id')->toArray();

                                $assigned_admins = DB::connection('reports')->table('multiple_sale_leads')->leftjoin('multiple_sale_taggings as mst', 'mst.lead_id', '=', 'multiple_sale_leads.id')
                                    ->leftjoin('sale_person_tags as spt', 'spt.admin_id', '=', 'mst.admin_id')
                                    ->where('multiple_sale_leads.admin_id', $sales_person)
                                    ->where('spt.status', 0)
                                    ->whereNotNull('spt.user_id')->select('spt.user_id')->pluck('spt.user_id')->toArray();
                                if ($assigned_admins) {
                                    $tagged_shippers = array_merge($tagged_shippers, $assigned_admins);
                                }

                                $booked = DB::connection('reports')->table('shipments')
                                ->join('user_shipping_infos as usi', 'usi.id', 'shipments.pickup_address_id')
                                ->where('shipments.packaging_material_request', 0)
                                ->where('shipments.user_id', '!=', 1690)
                                ->whereBetween('shipments.created_at', [$date_from, $date_to])
                                ->where('usi.city_id', $city->id)
                                ->where('shipments.shipping_mode_id', $mode->id)
                                ->whereIn('shipments.user_id', $tagged_shippers)
                                ->count();

                                $received = DB::connection('reports')->table('shipments_journey')
                                ->join('shipments as s', 's.id', 'shipments_journey.shipment_id')
                                ->join('user_shipping_infos as usi', 'usi.id', 's.pickup_address_id')
                                ->where('s.packaging_material_request', 0)
                                ->where('s.user_id', '!=', 1690)
                                ->whereBetween('shipments_journey.created_at', [$date_from, $date_to])
                                ->where('shipments_journey.shipper_status_id', 2)
                                ->where('usi.city_id', $city->id)
                                ->where('s.shipping_mode_id', $mode->id)
                                ->whereIn('s.user_id', $tagged_shippers)
                                ->count();

                                if($received > 0){
                                    $shipments_data = array();

                                    $shipments_data = DB::connection('reports')->table('shipments_journey')
                                    ->join('shipments as s', 's.id', 'shipments_journey.shipment_id')
                                    ->join('user_shipping_infos as usi', 'usi.id', 's.pickup_address_id')
                                    ->where('s.packaging_material_request', 0)
                                    ->where('s.user_id', '!=', 1690)
                                    ->whereBetween('shipments_journey.created_at', [$date_from, $date_to])
                                    ->where('shipments_journey.shipper_status_id', 2)
                                    ->where('usi.city_id', $city->id)
                                    ->where('s.shipping_mode_id', $mode->id)
                                    ->whereIn('s.user_id', $tagged_shippers)
                                    ->select(DB::connection('reports')->raw('SUM(IFNULL(weight_charges,0) + IFNULL(cash_handling_charges,0) + IFNULL(insurance_charges,0) + IFNULL(return_charges,0) + IFNULL(fuel_surcharge,0) + IFNULL(replacement_charges,0) + IFNULL(try_and_buy_charges,0)) as revenue_wo_gst'), DB::connection('reports')->raw('SUM(amount) as cod_collection'), DB::connection('reports')->raw('SUM(actual_weight) as actual_weight'), DB::connection('reports')->raw('SUM(chargeable_weight) as chargeable_weight'))->first();

                                    $revenue_wo_gst = $shipments_data->revenue_wo_gst;
                                    $actual_weight = $shipments_data->actual_weight;
                                    $chargeable_weight = $shipments_data->chargeable_weight;
                                    $cod_collection = $shipments_data->cod_collection;
                                }
                            }
                            else {
                                $booked = DB::connection('reports')->table('shipments')
                                ->join('user_shipping_infos as usi', 'usi.id', 'shipments.pickup_address_id')
                                ->where('shipments.packaging_material_request', 0)
                                ->where('shipments.user_id', '!=', 1690)
                                ->whereBetween('shipments.created_at', [$date_from, $date_to])
                                ->where('usi.city_id', $city->id)
                                ->where('shipments.shipping_mode_id', $mode->id)
                                ->whereIn('shipments.user_id', session('tagged_shippers'))
                                ->count();

                                $received = DB::connection('reports')->table('shipments_journey')
                                ->join('shipments as s', 's.id', 'shipments_journey.shipment_id')
                                ->join('user_shipping_infos as usi', 'usi.id', 's.pickup_address_id')
                                ->where('s.packaging_material_request', 0)
                                ->where('s.user_id', '!=', 1690)
                                ->whereBetween('shipments_journey.created_at', [$date_from, $date_to])
                                ->where('shipments_journey.shipper_status_id', 2)
                                ->where('usi.city_id', $city->id)
                                ->where('s.shipping_mode_id', $mode->id)
                                ->whereIn('s.user_id', session('tagged_shippers'))
                                ->count();

                                if($received > 0){
                                    $shipments_data = array();

                                    $shipments_data = DB::connection('reports')->table('shipments_journey')
                                    ->join('shipments as s', 's.id', 'shipments_journey.shipment_id')
                                    ->join('user_shipping_infos as usi', 'usi.id', 's.pickup_address_id')
                                    ->where('s.packaging_material_request', 0)
                                    ->where('s.user_id', '!=', 1690)
                                    ->whereBetween('shipments_journey.created_at', [$date_from, $date_to])
                                    ->where('shipments_journey.shipper_status_id', 2)
                                    ->where('usi.city_id', $city->id)
                                    ->where('s.shipping_mode_id', $mode->id)
                                    ->whereIn('s.user_id', session('tagged_shippers'))
                                    ->select(DB::connection('reports')->raw('SUM(IFNULL(weight_charges,0) + IFNULL(cash_handling_charges,0) + IFNULL(insurance_charges,0) + IFNULL(return_charges,0) + IFNULL(fuel_surcharge,0) + IFNULL(replacement_charges,0) + IFNULL(try_and_buy_charges,0)) as revenue_wo_gst'), DB::connection('reports')->raw('SUM(amount) as cod_collection'), DB::connection('reports')->raw('SUM(actual_weight) as actual_weight'), DB::connection('reports')->raw('SUM(chargeable_weight) as chargeable_weight'))->first();

                                    $revenue_wo_gst = $shipments_data->revenue_wo_gst;
                                    $actual_weight = $shipments_data->actual_weight;
                                    $chargeable_weight = $shipments_data->chargeable_weight;
                                    $cod_collection = $shipments_data->cod_collection;
                                }
                            }
                        }
                    }

                    $data[$mode->id]['serial'] = $serial;
                    $data[$mode->id]['mode'] = $mode->mode;
                    $data[$mode->id]['booked'] = $booked;
                    $data[$mode->id]['received'] = $received;
                    $data[$mode->id]['revenue_wo_gst'] = $revenue_wo_gst;
                    $data[$mode->id]['avg_parcel_rev'] = ($received != 0) ? $revenue_wo_gst / $received : 0;
                    $data[$mode->id]['actual_weight'] = $actual_weight;
                    $data[$mode->id]['avg_actual_weight'] = ($received != 0) ? $actual_weight / $received : 0;
                    $data[$mode->id]['avg_rev_actual_weight'] = ($actual_weight != 0) ? $revenue_wo_gst / $actual_weight : 0;
                    $data[$mode->id]['chargeable_weight'] = $chargeable_weight;
                    $data[$mode->id]['avg_chargeable_weight'] = ($received != 0) ? $chargeable_weight / $received : 0;
                    $data[$mode->id]['avg_rev_chargeable_weight'] = ($chargeable_weight != 0) ? $revenue_wo_gst / $chargeable_weight : 0;
                    $data[$mode->id]['collection_amount'] = $cod_collection;
                    $data[$mode->id]['avg_amount_collection'] = ($received != 0) ? $cod_collection / $received : 0;
                    $data[$mode->id]['revenue_amount_collection'] = ($cod_collection != 0) ? $revenue_wo_gst / $cod_collection : 0;
                    $data[$mode->id]['revenue_amount_collection'] = $data[$mode->id]['revenue_amount_collection'] * 100;
                    $serial++;

                }
            } else {
                $serial = 1;
                foreach ($shipping_modes as $mode) {

                    $booked = 0;
                    $received = 0;
                    $cod_collection = 0;
                    $actual_weight = 0;
                    $chargeable_weight = 0;
                    $revenue_wo_gst = 0;

                    if (session('department_id') != 7) {
                        $booked = DB::connection('reports')->table('shipments')
                        ->where('shipments.packaging_material_request', 0)
                        ->where('shipments.user_id', '!=', 1690)
                        ->whereBetween('shipments.created_at', [$date_from, $date_to])
                        ->where('shipments.shipping_mode_id', $mode->id)
                        ->count();

                        $received = DB::connection('reports')->table('shipments_journey')
                        ->join('shipments as s', 's.id', 'shipments_journey.shipment_id')
                        ->where('s.packaging_material_request', 0)
                        ->where('s.user_id', '!=', 1690)
                        ->whereBetween('shipments_journey.created_at', [$date_from, $date_to])
                        ->where('shipments_journey.shipper_status_id', 2)
                        ->where('s.shipping_mode_id', $mode->id)
                        ->count();

                        if($received > 0){
                            $shipments_data = array();

                            $shipments_data = DB::connection('reports')->table('shipments_journey')
                            ->join('shipments as s', 's.id', 'shipments_journey.shipment_id')
                            ->where('s.packaging_material_request', 0)
                            ->where('s.user_id', '!=', 1690)
                            ->whereBetween('shipments_journey.created_at', [$date_from, $date_to])
                            ->where('shipments_journey.shipper_status_id', 2)
                            ->where('s.shipping_mode_id', $mode->id)
                            ->select(DB::connection('reports')->raw('SUM(IFNULL(weight_charges,0) + IFNULL(cash_handling_charges,0) + IFNULL(insurance_charges,0) + IFNULL(return_charges,0) + IFNULL(fuel_surcharge,0) + IFNULL(replacement_charges,0) + IFNULL(try_and_buy_charges,0)) as revenue_wo_gst'), DB::connection('reports')->raw('SUM(amount) as cod_collection'), DB::connection('reports')->raw('SUM(actual_weight) as actual_weight'), DB::connection('reports')->raw('SUM(chargeable_weight) as chargeable_weight'))->first();

                            $revenue_wo_gst = $shipments_data->revenue_wo_gst;
                            $actual_weight = $shipments_data->actual_weight;
                            $chargeable_weight = $shipments_data->chargeable_weight;
                            $cod_collection = $shipments_data->cod_collection;
                        }
                    }
                    else {
                        if (!in_array(session('id'), session('sale_users_bypass'))) {
                            $booked = DB::connection('reports')->table('shipments')
                            ->where('shipments.packaging_material_request', 0)
                            ->where('shipments.user_id', '!=', 1690)
                            ->whereBetween('shipments.created_at', [$date_from, $date_to])
                            ->whereIn('shipments.user_id', session('tagged_shippers'))
                            ->where('shipments.shipping_mode_id', $mode->id)
                            ->count();

                            $received = DB::connection('reports')->table('shipments_journey')
                            ->join('shipments as s', 's.id', 'shipments_journey.shipment_id')
                            ->where('s.packaging_material_request', 0)
                            ->where('s.user_id', '!=', 1690)
                            ->whereBetween('shipments_journey.created_at', [$date_from, $date_to])
                            ->where('shipments_journey.shipper_status_id', 2)
                            ->whereIn('s.user_id', session('tagged_shippers'))
                            ->where('s.shipping_mode_id', $mode->id)
                            ->count();

                            if($received > 0){
                                $shipments_data = array();

                                $shipments_data = DB::connection('reports')->table('shipments_journey')
                                ->join('shipments as s', 's.id', 'shipments_journey.shipment_id')
                                ->where('s.packaging_material_request', 0)
                                ->where('s.user_id', '!=', 1690)
                                ->whereBetween('shipments_journey.created_at', [$date_from, $date_to])
                                ->where('shipments_journey.shipper_status_id', 2)
                                ->whereIn('s.user_id', session('tagged_shippers'))
                                ->where('s.shipping_mode_id', $mode->id)
                                ->select(DB::connection('reports')->raw('SUM(IFNULL(weight_charges,0) + IFNULL(cash_handling_charges,0) + IFNULL(insurance_charges,0) + IFNULL(return_charges,0) + IFNULL(fuel_surcharge,0) + IFNULL(replacement_charges,0) + IFNULL(try_and_buy_charges,0)) as revenue_wo_gst'), DB::connection('reports')->raw('SUM(amount) as cod_collection'), DB::connection('reports')->raw('SUM(actual_weight) as actual_weight'), DB::connection('reports')->raw('SUM(chargeable_weight) as chargeable_weight'))->first();

                                $revenue_wo_gst = $shipments_data->revenue_wo_gst;
                                $actual_weight = $shipments_data->actual_weight;
                                $chargeable_weight = $shipments_data->chargeable_weight;
                                $cod_collection = $shipments_data->cod_collection;
                            }
                        }
                        else {
                            if ($sales_person != null) {
                                $tagged_shippers = DB::connection('reports')->table('sale_person_tags')->where('admin_id', $sales_person)->select('user_id')->pluck('user_id')->toArray();

                                $assigned_admins = DB::connection('reports')->table('multiple_sale_leads')->leftjoin('multiple_sale_taggings as mst', 'mst.lead_id', '=', 'multiple_sale_leads.id')
                                    ->leftjoin('sale_person_tags as spt', 'spt.admin_id', '=', 'mst.admin_id')
                                    ->where('multiple_sale_leads.admin_id', $sales_person)
                                    ->where('spt.status', 0)
                                    ->whereNotNull('spt.user_id')->select('spt.user_id')->pluck('spt.user_id')->toArray();
                                if ($assigned_admins) {
                                    $tagged_shippers = array_merge($tagged_shippers, $assigned_admins);
                                }

                                $booked = DB::connection('reports')->table('shipments')
                                ->where('shipments.packaging_material_request', 0)
                                ->where('shipments.user_id', '!=', 1690)
                                ->whereBetween('shipments.created_at', [$date_from, $date_to])
                                ->where('shipments.shipping_mode_id', $mode->id)
                                ->whereIn('shipments.user_id', $tagged_shippers)
                                ->count();

                                $received = DB::connection('reports')->table('shipments_journey')
                                ->join('shipments as s', 's.id', 'shipments_journey.shipment_id')
                                ->where('s.packaging_material_request', 0)
                                ->where('s.user_id', '!=', 1690)
                                ->whereBetween('shipments_journey.created_at', [$date_from, $date_to])
                                ->where('shipments_journey.shipper_status_id', 2)
                                ->where('s.shipping_mode_id', $mode->id)
                                ->whereIn('s.user_id', $tagged_shippers)
                                ->count();

                                if($received > 0){
                                    $shipments_data = array();

                                    $shipments_data = DB::connection('reports')->table('shipments_journey')
                                    ->join('shipments as s', 's.id', 'shipments_journey.shipment_id')
                                    ->where('s.packaging_material_request', 0)
                                    ->where('s.user_id', '!=', 1690)
                                    ->whereBetween('shipments_journey.created_at', [$date_from, $date_to])
                                    ->where('shipments_journey.shipper_status_id', 2)
                                    ->where('s.shipping_mode_id', $mode->id)
                                    ->whereIn('s.user_id', $tagged_shippers)
                                    ->select(DB::connection('reports')->raw('SUM(IFNULL(weight_charges,0) + IFNULL(cash_handling_charges,0) + IFNULL(insurance_charges,0) + IFNULL(return_charges,0) + IFNULL(fuel_surcharge,0) + IFNULL(replacement_charges,0) + IFNULL(try_and_buy_charges,0)) as revenue_wo_gst'), DB::connection('reports')->raw('SUM(amount) as cod_collection'), DB::connection('reports')->raw('SUM(actual_weight) as actual_weight'), DB::connection('reports')->raw('SUM(chargeable_weight) as chargeable_weight'))->first();

                                    $revenue_wo_gst = $shipments_data->revenue_wo_gst;
                                    $actual_weight = $shipments_data->actual_weight;
                                    $chargeable_weight = $shipments_data->chargeable_weight;
                                    $cod_collection = $shipments_data->cod_collection;
                                }
                            }
                            else {
                                $booked = DB::connection('reports')->table('shipments')
                                ->where('shipments.packaging_material_request', 0)
                                ->where('shipments.user_id', '!=', 1690)
                                ->whereBetween('shipments.created_at', [$date_from, $date_to])
                                ->where('shipments.shipping_mode_id', $mode->id)
                                ->whereIn('shipments.user_id', session('tagged_shippers'))
                                ->count();

                                $received = DB::connection('reports')->table('shipments_journey')
                                ->join('shipments as s', 's.id', 'shipments_journey.shipment_id')
                                ->where('s.packaging_material_request', 0)
                                ->where('s.user_id', '!=', 1690)
                                ->whereBetween('shipments_journey.created_at', [$date_from, $date_to])
                                ->where('shipments_journey.shipper_status_id', 2)
                                ->where('s.shipping_mode_id', $mode->id)
                                ->whereIn('s.user_id', session('tagged_shippers'))
                                ->count();

                                if($received > 0){
                                    $shipments_data = array();

                                    $shipments_data = DB::connection('reports')->table('shipments_journey')
                                    ->join('shipments as s', 's.id', 'shipments_journey.shipment_id')
                                    ->where('s.packaging_material_request', 0)
                                    ->where('s.user_id', '!=', 1690)
                                    ->whereBetween('shipments_journey.created_at', [$date_from, $date_to])
                                    ->where('shipments_journey.shipper_status_id', 2)
                                    ->where('s.shipping_mode_id', $mode->id)
                                    ->whereIn('s.user_id', session('tagged_shippers'))
                                    ->select(DB::connection('reports')->raw('SUM(IFNULL(weight_charges,0) + IFNULL(cash_handling_charges,0) + IFNULL(insurance_charges,0) + IFNULL(return_charges,0) + IFNULL(fuel_surcharge,0) + IFNULL(replacement_charges,0) + IFNULL(try_and_buy_charges,0)) as revenue_wo_gst'), DB::connection('reports')->raw('SUM(amount) as cod_collection'), DB::connection('reports')->raw('SUM(actual_weight) as actual_weight'), DB::connection('reports')->raw('SUM(chargeable_weight) as chargeable_weight'))->first();

                                    $revenue_wo_gst = $shipments_data->revenue_wo_gst;
                                    $actual_weight = $shipments_data->actual_weight;
                                    $chargeable_weight = $shipments_data->chargeable_weight;
                                    $cod_collection = $shipments_data->cod_collection;
                                }
                            }
                        }
                    }

                    $data[$mode->id]['serial'] = $serial;
                    $data[$mode->id]['mode'] = $mode->mode;
                    $data[$mode->id]['booked'] = $booked;
                    $data[$mode->id]['received'] = $received;
                    $data[$mode->id]['revenue_wo_gst'] = $revenue_wo_gst;
                    $data[$mode->id]['avg_parcel_rev'] = ($received != 0) ? $revenue_wo_gst / $received : 0;
                    $data[$mode->id]['actual_weight'] = $actual_weight;
                    $data[$mode->id]['avg_actual_weight'] = ($received != 0) ? $actual_weight / $received : 0;
                    $data[$mode->id]['avg_rev_actual_weight'] = ($actual_weight != 0) ? $revenue_wo_gst / $actual_weight : 0;
                    $data[$mode->id]['chargeable_weight'] = $chargeable_weight;
                    $data[$mode->id]['avg_chargeable_weight'] = ($received != 0) ? $chargeable_weight / $received : 0;
                    $data[$mode->id]['avg_rev_chargeable_weight'] = ($chargeable_weight != 0) ? $revenue_wo_gst / $chargeable_weight : 0;
                    $data[$mode->id]['collection_amount'] = $cod_collection;
                    $data[$mode->id]['avg_amount_collection'] = ($received != 0) ? $cod_collection / $received : 0;
                    $data[$mode->id]['revenue_amount_collection'] = ($cod_collection != 0) ? $revenue_wo_gst / $cod_collection : 0;
                    $data[$mode->id]['revenue_amount_collection'] = $data[$mode->id]['revenue_amount_collection'] * 100;
                    $serial++;

                }
            }
        } else {
            if ($hub != null) {
                $serial = 1;
                foreach ($shipping_modes as $mode) {

                    $booked = 0;
                    $received = 0;
                    $cod_collection = 0;
                    $actual_weight = 0;
                    $chargeable_weight = 0;
                    $revenue_wo_gst = 0;

                    $booked = DB::connection('reports')->table('shipments')
                    ->join('user_shipping_infos as usi', 'usi.id', 'shipments.pickup_address_id')
                    ->where('shipments.packaging_material_request', 0)
                    ->where('shipments.user_id', '!=', 1690)
                    ->whereBetween('shipments.created_at', [$date_from, $date_to])
                    ->where('usi.city_id', $city->id)
                    ->where('shipments.shipping_mode_id', $mode->id)
                    ->count();

                    $received = DB::connection('reports')->table('shipments_journey')
                    ->join('shipments as s', 's.id', 'shipments_journey.shipment_id')
                    ->join('user_shipping_infos as usi', 'usi.id', 's.pickup_address_id')
                    ->where('s.packaging_material_request', 0)
                    ->where('s.user_id', '!=', 1690)
                    ->whereBetween('shipments_journey.created_at', [$date_from, $date_to])
                    ->where('shipments_journey.shipper_status_id', 2)
                    ->where('usi.city_id', $city->id)
                    ->where('s.shipping_mode_id', $mode->id)
                    ->count();

                    if($received > 0){
                        $shipments_data = array();

                        $shipments_data = DB::connection('reports')->table('shipments_journey')
                        ->join('shipments as s', 's.id', 'shipments_journey.shipment_id')
                        ->join('user_shipping_infos as usi', 'usi.id', 's.pickup_address_id')
                        ->where('s.packaging_material_request', 0)
                        ->where('s.user_id', '!=', 1690)
                        ->whereBetween('shipments_journey.created_at', [$date_from, $date_to])
                        ->where('shipments_journey.shipper_status_id', 2)
                        ->where('usi.city_id', $city->id)
                        ->where('s.shipping_mode_id', $mode->id)
                        ->select(DB::connection('reports')->raw('SUM(IFNULL(weight_charges,0) + IFNULL(cash_handling_charges,0) + IFNULL(insurance_charges,0) + IFNULL(return_charges,0) + IFNULL(fuel_surcharge,0) + IFNULL(replacement_charges,0) + IFNULL(try_and_buy_charges,0)) as revenue_wo_gst'), DB::connection('reports')->raw('SUM(amount) as cod_collection'), DB::connection('reports')->raw('SUM(actual_weight) as actual_weight'), DB::connection('reports')->raw('SUM(chargeable_weight) as chargeable_weight'))->first();

                        $revenue_wo_gst = $shipments_data->revenue_wo_gst;
                        $actual_weight = $shipments_data->actual_weight;
                        $chargeable_weight = $shipments_data->chargeable_weight;
                        $cod_collection = $shipments_data->cod_collection;
                    }

                    $data[$mode->id]['serial'] = $serial;
                    $data[$mode->id]['mode'] = $mode->mode;
                    $data[$mode->id]['booked'] = $booked;
                    $data[$mode->id]['received'] = $received;
                    $data[$mode->id]['revenue_wo_gst'] = $revenue_wo_gst;
                    $data[$mode->id]['avg_parcel_rev'] = ($received != 0) ? $revenue_wo_gst / $received : 0;
                    $data[$mode->id]['actual_weight'] = $actual_weight;
                    $data[$mode->id]['avg_actual_weight'] = ($received != 0) ? $actual_weight / $received : 0;
                    $data[$mode->id]['avg_rev_actual_weight'] = ($actual_weight != 0) ? $revenue_wo_gst / $actual_weight : 0;
                    $data[$mode->id]['chargeable_weight'] = $chargeable_weight;
                    $data[$mode->id]['avg_chargeable_weight'] = ($received != 0) ? $chargeable_weight / $received : 0;
                    $data[$mode->id]['avg_rev_chargeable_weight'] = ($chargeable_weight != 0) ? $revenue_wo_gst / $chargeable_weight : 0;
                    $data[$mode->id]['collection_amount'] = $cod_collection;
                    $data[$mode->id]['avg_amount_collection'] = ($received != 0) ? $cod_collection / $received : 0;
                    $data[$mode->id]['revenue_amount_collection'] = ($received != 0) ? $revenue_wo_gst / $cod_collection : 0;
                    $data[$mode->id]['revenue_amount_collection'] = $data[$mode->id]['revenue_amount_collection'] * 100;
                    $serial++;

                }
            } else {
                $serial = 1;
                foreach ($shipping_modes as $mode) {

                    $booked = 0;
                    $received = 0;
                    $cod_collection = 0;
                    $actual_weight = 0;
                    $chargeable_weight = 0;
                    $revenue_wo_gst = 0;

                    $booked = DB::connection('reports')->table('shipments')
                    ->where('shipments.packaging_material_request', 0)
                    ->where('shipments.user_id', '!=', 1690)
                    ->whereBetween('shipments.created_at', [$date_from, $date_to])
                    ->where('shipments.shipping_mode_id', $mode->id)
                    ->count();

                    $received = DB::connection('reports')->table('shipments_journey')
                    ->join('shipments as s', 's.id', 'shipments_journey.shipment_id')
                    ->where('s.packaging_material_request', 0)
                    ->where('s.user_id', '!=', 1690)
                    ->whereBetween('shipments_journey.created_at', [$date_from, $date_to])
                    ->where('shipments_journey.shipper_status_id', 2)
                    ->where('s.shipping_mode_id', $mode->id)
                    ->count();

                    if($received > 0){
                        $shipments_data = array();

                        $shipments_data = DB::connection('reports')->table('shipments_journey')
                        ->join('shipments as s', 's.id', 'shipments_journey.shipment_id')
                        ->where('s.packaging_material_request', 0)
                        ->where('s.user_id', '!=', 1690)
                        ->whereBetween('shipments_journey.created_at', [$date_from, $date_to])
                        ->where('shipments_journey.shipper_status_id', 2)
                        ->where('s.shipping_mode_id', $mode->id)
                        ->select(DB::connection('reports')->raw('SUM(IFNULL(weight_charges,0) + IFNULL(cash_handling_charges,0) + IFNULL(insurance_charges,0) + IFNULL(return_charges,0) + IFNULL(fuel_surcharge,0) + IFNULL(replacement_charges,0) + IFNULL(try_and_buy_charges,0)) as revenue_wo_gst'), DB::connection('reports')->raw('SUM(amount) as cod_collection'), DB::connection('reports')->raw('SUM(actual_weight) as actual_weight'), DB::connection('reports')->raw('SUM(chargeable_weight) as chargeable_weight'))->first();

                        $revenue_wo_gst = $shipments_data->revenue_wo_gst;
                        $actual_weight = $shipments_data->actual_weight;
                        $chargeable_weight = $shipments_data->chargeable_weight;
                        $cod_collection = $shipments_data->cod_collection;
                    }

                    $data[$mode->id]['serial'] = $serial;
                    $data[$mode->id]['mode'] = $mode->mode;
                    $data[$mode->id]['booked'] = $booked;
                    $data[$mode->id]['received'] = $received;
                    $data[$mode->id]['revenue_wo_gst'] = $revenue_wo_gst;
                    $data[$mode->id]['avg_parcel_rev'] = ($received != 0) ? $revenue_wo_gst / $received : 0;
                    $data[$mode->id]['actual_weight'] = $actual_weight;
                    $data[$mode->id]['avg_actual_weight'] = ($received != 0) ? $actual_weight / $received : 0;
                    $data[$mode->id]['avg_rev_actual_weight'] = ($actual_weight != 0) ? $revenue_wo_gst / $actual_weight : 0;
                    $data[$mode->id]['chargeable_weight'] = $chargeable_weight;
                    $data[$mode->id]['avg_chargeable_weight'] = ($received != 0) ? $chargeable_weight / $received : 0;
                    $data[$mode->id]['avg_rev_chargeable_weight'] = ($chargeable_weight != 0) ? $revenue_wo_gst / $chargeable_weight : 0;
                    $data[$mode->id]['collection_amount'] = $cod_collection;
                    $data[$mode->id]['avg_amount_collection'] = ($received != 0) ? $cod_collection / $received : 0;
                    $data[$mode->id]['revenue_amount_collection'] = ($received != 0) ? $revenue_wo_gst / $cod_collection : 0;
                    $data[$mode->id]['revenue_amount_collection'] = $data[$mode->id]['revenue_amount_collection'] * 100;
                    $serial++;
                }
            }
        }
        return $data;
    }

    public function last_mile_app_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 227);
        $riders = Rider::where('status', 1)->get();
        $riders_cat = OperationRidersCategory::all();
        $zones = Zone::where('status', 1)->where('business_category_id', 1)->get();
        $hubs = City::where('status', 1)->where('hub', 1)->where('business_category_id', 1)->get();
        return view('admin.reports.last_mile_app')->with(['riders' => $riders, 'hubs' => $hubs, 'zones' => $zones, 'riders_cat' => $riders_cat]);
    }

    public function last_mile_app_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 228);
        }
        $date = Carbon::createFromDate('2021', '02', '19')->toDateString();
        $deliveries = DB::connection('reports')->table('delivery_notes')
            ->join('cities as c', 'delivery_notes.hub_id', '=', 'c.id')
            ->join('zones as z', 'z.id', '=', 'c.zone_id')
            ->join('riders as r', 'delivery_notes.rider_id', '=', 'r.id')
            ->join('operation_riders_categories as rd', 'r.operation_rider_id', '=', 'rd.id')
            ->select('delivery_notes.id as delivery_note_id', 'z.name as zone', 'delivery_notes.created_at as created_at', 'rd.name as rider_cat', 'r.name as rider', 'delivery_notes.shipments_count as total_shipments', 'c.name as city', DB::raw('(SELECT COUNT(shipment_id) as id FROM `delivery_note_shipments` AS `adns` where `adns`.`delivery_note_id` = `delivery_notes`.`id` AND `adns`.`update_type` = 1) AS `shipments_rider_updated`'), DB::raw('(SELECT COUNT(shipment_id) as id FROM `delivery_note_shipments` AS `dns` where `dns`.`delivery_note_id` = `delivery_notes`.`id` AND `dns`.`update_type` = 0 AND `dns`.`status` > 0) AS `shipments_dbf_updated`'))
            ->whereDate('delivery_notes.created_at', '>', $date);


        $datatable = Datatables::of($deliveries)
            ->addColumn('delivery_note', function ($deliveries) {
                return '<button class="btn btn-sm btn-outline-info align-middle print"><i class="la la-lg la-print align-middle"></i> <span class="align-middle">' . str_pad($deliveries->delivery_note_id, 6, '0', STR_PAD_LEFT) . '</span></button>';
            })
            ->addColumn('delivery_note_id_padded', function ($deliveries) {
                return str_pad($deliveries->delivery_note_id, 6, '0', STR_PAD_LEFT);
            })
            ->editColumn('total_shipments_link', function ($deliveries) {
                if ($deliveries->total_shipments != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $deliveries->total_shipments . '</button>';
                } else {
                    return 0;
                }
            })
            ->editColumn('shipments_rider_updated', function ($deliveries) {
                if ($deliveries->shipments_rider_updated != null) {
                    return $deliveries->shipments_rider_updated;
                } else {
                    return 0;
                }
            })
            ->addColumn('update_via_app', function ($deliveries) {
                if ($deliveries->shipments_rider_updated != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $deliveries->shipments_rider_updated . '</button>';
                } else {
                    return 0;
                }
            })
            ->addColumn('update_via_dbf', function ($deliveries) {
                $count = $deliveries->shipments_dbf_updated;
                if ($count != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $count . '</button>';
                } else {
                    return 0;
                }
            });

        if ($search_rider = $request->get('search_rider')) {
            $datatable->where('r.id', $search_rider);
        }
        if ($search_rider_cat = $request->get('search_rider_cat')) {
            $datatable->where('r.operation_rider_id', $search_rider_cat);
        }
        if ($search_zone = $request->get('search_zone')) {
            $datatable->where('c.zone_id', $search_zone);
        }
        if ($search_hub = $request->get('search_hub')) {
            $datatable->where('c.id', $search_hub);
        }
        if ($dn_no = $request->get('search_dn_no')) {
            $datatable->where('delivery_notes.id', '=', $dn_no);
        }
        if ($tracking = $request->get('search_tracking')) {
            $datatable->join('delivery_note_shipments as rns', 'rns.delivery_note_id', '=', 'delivery_notes.id')
                ->join('shipments as s', 'rns.shipment_id', '=', 's.id')
                ->where('s.tracking_number', '=', $tracking);
        }
        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $datatable->whereBetween('delivery_notes.created_at', [$from, $to]);
        }
        if ($request->get('search_update_date_from') && $request->get('search_update_date_to')) {
            $ufrom = $request->get('search_update_date_from');
            $uto = $request->get('search_update_date_to');
            $datatable->whereBetween('delivery_notes.status_updated_at', [$ufrom, $uto]);
        }
        return $datatable->make(true);
    }

    public function last_mile_app_shipments_list(Request $request)
    {

        $delivery_note_id = $request->delivery_note_id;

        $shipments = DeliveryNoteShipment::join('shipments as s', 's.id', '=', 'delivery_note_shipments.shipment_id')
            ->join('rider_deliveries', function ($join) {
                $join->on('delivery_note_shipments.shipment_id', '=', 'rider_deliveries.shipment_id')
                    ->where('rider_deliveries.id', '=',
                        DB::raw('(select max(id) from rider_deliveries as rrd where rrd.shipment_id = delivery_note_shipments.shipment_id AND rrd.delivery_note_id = delivery_note_shipments.delivery_note_id)'));
            })
            ->leftjoin('shipments_journey', function ($join) {
                $join->on('shipments_journey.shipment_id', '=', 'delivery_note_shipments.shipment_id')
                    ->where('shipments_journey.id', '=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = delivery_note_shipments.shipment_id and reference_1_id = delivery_note_shipments.delivery_note_id and shipments_journey.shipper_status_id != 5 and rider_id is not null)'));
            })
            ->leftjoin('shipment_status as ss', 'ss.id', '=', 'shipments_journey.shipper_status_id')
            ->leftJoin('shipment_status_reason as ssr', 'ssr.id', '=', 'shipments_journey.status_reason_id')
            ->select('s.id as shipment_id', 's.tracking_number', 'shipments_journey.shipper_status_id', 'ss.name as shipment_status', 'ssr.name as shipment_reason', 'shipments_journey.created_at as update_date_time', 'shipments_journey.received_or_refused_by', 'rider_deliveries.picture_path', 'rider_deliveries.cnic_image as cnic_image', 'rider_deliveries.ccd_image as ccd_image', 'rider_deliveries.house_image as house_image', 'rider_deliveries.delivered_status', 'rider_deliveries.audio_path', 'rider_deliveries.cnic as cnic', 'rider_deliveries.relation as relation')
            ->where('delivery_note_shipments.update_type', 1)
            ->where('delivery_note_shipments.delivery_note_id', $delivery_note_id);


        $datatables = Datatables::of($shipments)
            ->addColumn('tracking_number_link', function ($shipments) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->addColumn('status', function ($shipments) {
                if ($shipments->delivered_status == 1) {
                    return 'Delivered';
                } else {
                    return 'Undelivered';
                }
            })
            ->addColumn('pod', function ($shipments) {


                $image = '';
                if ($shipments->picture_path != null) {
                    $exists = Storage::disk('public')->exists($shipments->picture_path);
                    if ($exists) {
//                        $image .= '<div class="text-center"><button type="button" class="btn btn-primary btn-sm picture" data-link="' . asset(Storage::url($shipments->picture_path)) . '"><i class="la la-image"></i> View</button></div>';
                        $image = '<a class="btn btn-sm btn-outline-info align-middle" href="' . asset(Storage::url($shipments->picture_path)) . '" target="_blank"><i class="la la-lg la-image align-middle"></i> <span class="align-middle">View</span></a>';

                    } else {
                        $img = Storage::disk('s3')->temporaryUrl($shipments->picture_path, now()->addMinutes(5));
                        $image = '<a class="btn btn-sm btn-outline-info align-middle" href="' . $img . '" target="_blank"><i class="la la-lg la-image align-middle"></i> <span class="align-middle">View</span></a>';
                    }

                    return $image;
                } else {
                    return '-';
                }
            })
            ->addColumn('cnic_image', function ($shipments) {
                $image = '';
                if ($shipments->cnic_image != null) {
                    $exists = Storage::disk('public')->exists($shipments->cnic_image);
                    if ($exists) {
//                        $image .= '<div class="text-center"><button type="button" class="btn btn-primary btn-sm picture" data-link="' . asset(Storage::url($shipments->cnic_image)) . '"><i class="la la-image"></i> View</button></div>';
                        $image = '<a class="btn btn-sm btn-outline-info align-middle" href="' . asset(Storage::url($shipments->cnic_image)) . '" target="_blank"><i class="la la-lg la-image align-middle"></i> <span class="align-middle">View</span></a>';

                    } else {
                        $img = Storage::disk('s3')->temporaryUrl($shipments->cnic_image, now()->addMinutes(5));
                        $image = '<a class="btn btn-sm btn-outline-info align-middle" href="' . $img . '" target="_blank"><i class="la la-lg la-image align-middle"></i> <span class="align-middle">View</span></a>';
                    }

                    return $image;
                } else {
                    return '-';
                }

            })
            ->addColumn('house_image', function ($shipments) {
                $image = '';
                if ($shipments->house_image != null) {
                    $exists = Storage::disk('public')->exists($shipments->house_image);
                    if ($exists) {
//                        $image .= '<div class="text-center"><button type="button" class="btn btn-primary btn-sm picture" data-link="' . asset(Storage::url($shipments->house_image)) . '"><i class="la la-image"></i> View</button></div>';
                        $image = '<a class="btn btn-sm btn-outline-info align-middle" href="' . asset(Storage::url($shipments->house_image)) . '" target="_blank"><i class="la la-lg la-image align-middle"></i> <span class="align-middle">View</span></a>';

                    } else {
                        $img = Storage::disk('s3')->temporaryUrl($shipments->house_image, now()->addMinutes(5));
                        $image = '<a class="btn btn-sm btn-outline-info align-middle" href="' . $img . '" target="_blank"><i class="la la-lg la-image align-middle"></i> <span class="align-middle">View</span></a>';
                    }

                    return $image;
                } else {
                    return '-';
                }

            })
            ->addColumn('ccd_image', function ($shipments) {
                $image = '';
                if ($shipments->ccd_image != null) {
                    $exists = Storage::disk('public')->exists($shipments->ccd_image);
                    if ($exists) {
//                        $image .= '<div class="text-center"><button type="button" class="btn btn-primary btn-sm picture" data-link="' . asset(Storage::url($shipments->ccd_image)) . '"><i class="la la-image"></i> View</button></div>';
                        $image = '<a class="btn btn-sm btn-outline-info align-middle" href="' . asset(Storage::url($shipments->ccd_image)) . '" target="_blank"><i class="la la-lg la-image align-middle"></i> <span class="align-middle">View</span></a>';

                    } else {
                        $img = Storage::disk('s3')->temporaryUrl($shipments->ccd_image, now()->addMinutes(5));
                        $image = '<a class="btn btn-sm btn-outline-info align-middle" href="' . $img . '" target="_blank"><i class="la la-lg la-image align-middle"></i> <span class="align-middle">View</span></a>';
                    }

                    return $image;
                } else {
                    return '-';
                }

            })
            ->editColumn('audio_path', function ($shipments) {
                $audio = '';
                if ($shipments->audio_path != null) {
                    $exists = Storage::disk('public')->exists($shipments->audio_path);
                    if ($exists) {
//                        $audio .= '<div class="text-center"><button type="button" class="btn btn-primary btn-sm audio" data-link="' . asset(Storage::url($shipments->audio_path)) . '"><i class="la la-lg la-file-sound-o align-middle"></i> Listen</button></div>';
                        $audio = '<a class="btn btn-sm btn-outline-info align-middle" href="' .  asset(Storage::url($shipments->audio_path))  . '" target="_blank"><i class="la la-lg la-file-sound-o align-middle"></i> <span class="align-middle"> Listen</span></a>';

                    } else {
                        $sound = Storage::disk('s3')->temporaryUrl($shipments->audio_path, now()->addMinutes(5));
                        $audio = '<a class="btn btn-sm btn-outline-info align-middle" href="' . $sound . '" target="_blank"><i class="la la-lg la-file-sound-o align-middle"></i> <span class="align-middle"> Listen</span></a>';
                    }
                    return $audio;
                } else {
                    return '-';
                }
            })

            ->editColumn('audio_path', function ($shipments) {
                $audio = '';
                if ($shipments->audio_path != null) {
                    $exists = Storage::disk('public')->exists($shipments->audio_path);
                    if ($exists) {
                        $audio .= '<div class="text-center"><button type="button" class="btn btn-primary btn-sm audio" data-link="' . asset(Storage::url($shipments->audio_path)) . '"><i class="la la-lg la-file-sound-o align-middle"></i> Listen</button></div>';
                    } else {
                        $sound = Storage::disk('s3')->temporaryUrl($shipments->audio_path, now()->addMinutes(5));
                        $audio = '<a class="btn btn-sm btn-outline-info align-middle" href="' . $sound . '" target="_blank"><i class="la la-lg la-file-sound-o align-middle"></i> <span class="align-middle"> Listen</span></a>';
                    }
                    return $audio;
                } else {
                    return '-';
                }
            });
        return $datatables->make(true);

    }

    public function last_mile_dbf_shipments_list(Request $request)
    {
        $delivery_note_id = $request->delivery_note_id;

        $shipments = DeliveryNoteShipment::join('shipments as s', 's.id', '=', 'delivery_note_shipments.shipment_id')
            ->leftjoin('shipments_journey', function ($join) {
                $join->on('shipments_journey.shipment_id', '=', 'delivery_note_shipments.shipment_id')
                    ->where('shipments_journey.id', '=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = delivery_note_shipments.shipment_id and reference_1_id = delivery_note_shipments.delivery_note_id and shipments_journey.shipper_status_id != 5 and rider_id is null)'));
            })
            ->leftjoin('shipment_status as ss', 'ss.id', '=', 'shipments_journey.shipper_status_id')
            ->leftJoin('shipment_status_reason as ssr', 'ssr.id', '=', 'shipments_journey.status_reason_id')
            ->select('s.id as shipment_id', 's.tracking_number', 'shipments_journey.shipper_status_id', 'ss.name as shipment_status', 'ssr.name as shipment_reason', 'shipments_journey.created_at as update_date_time', 'shipments_journey.received_or_refused_by','shipments_journey.cnic as cnic','shipments_journey.relation as relation')
            ->where('delivery_note_shipments.update_type', 0)
            ->where('delivery_note_shipments.status', '>', 0)
            ->where('delivery_note_shipments.delivery_note_id', $delivery_note_id);
        $datatables = Datatables::of($shipments)
            ->addColumn('tracking_number_link', function ($shipments) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->addColumn('status', function ($shipments) {
                if (in_array($shipments->shipper_status_id, [14, 30, 36, 37])) {
                    return 'Delivered';
                } else {
                    return 'Undelivered';
                }
            })
            ->addColumn('consignee_cnic', function ($shipments) {
                if ($shipments->cnic) {
                    return $shipments->cnic;
                } else {
                    return '-';
                }
            })
            ->addColumn('consignee_relation', function ($shipments) {
                if ($shipments->relation) {
                    return $shipments->relation;
                } else {
                    return '-';
                }
            });
        return $datatables->make(true);

    }

    public function weight_qc_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 141);
        $shipping_modes = ShippingMode::all();
        $users = User::where('status', 3)->get(['id', 'name']);
        $hubs = City::where('status', 1)->where('hub', 1)->get(['id', 'name']);
        $zones = Zone::where('status', 1)->get(['id', 'name']);
        return view('admin.reports.weight_qc')->with(['shipping_modes' => $shipping_modes, 'users' => $users, 'hubs' => $hubs, 'zones' => $zones]);
    }

    public function weight_qc_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 142);
        }
        $shipments = DB::connection('reports')->table('shipments')->join('users as u', 'shipments.user_id', '=', 'u.id')
            ->join('shipping_modes as sm', 'shipments.shipping_mode_id', '=', 'sm.id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->leftJoin ('shipments_journey as bkg_date', function ($join) {
                $join->on('bkg_date.shipment_id', '=', 'shipments.id')
                    ->where('bkg_date.id', '=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 1)'));
            })
            ->leftJoin('shipments_journey as arv_date', function ($join) {
                $join->on('arv_date.shipment_id', '=', 'shipments.id')
                    ->where('arv_date.id', '=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)'));
            })
            ->select(['shipments.id as shId', 'shipments.tracking_number', 'u.name as shipper', 'oc.name as origin', 'dc.name as destination', 'bkg_date.created_at as booking_date', 'arv_date.created_at as arrival_date', 'sm.mode as shipping_mode', 'shipments.estimated_weight', 'shipments.actual_weight', 'shipments.length', 'shipments.breadth', 'shipments.height'])
            ->whereNotNull('shipments.actual_weight');

        if (session('role_id') != 1) {
            $shipments = $shipments->where(function ($query) {
                $query->where(function ($sub_query) {
                    $sub_query->whereIn('dc.hub_id', session('hubs'));
                })
                    ->orWhere(function ($sub_query) {
                        $sub_query->whereIn('oc.hub_id', session('hubs'));
                    });
            });
        }

        $datatable = Datatables::of($shipments)
            ->editColumn('tracking_number_link', function ($shipment) {
                $route = route('admin.tracking.index');
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
        if ($search_shipping_mode = $request->get('search_shipping_mode')) {
            $datatable->where('sm.id', $search_shipping_mode);
        }
        if ($tracking_numbers = $request->get('tracking_numbers')) {
            $datatable->whereIn('shipments.tracking_number', explode(',', $tracking_numbers));
        }
        if ($user = $request->get('search_user')) {
            $datatable->where('u.id', $user);
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

    public function in_transit_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 334);
        $bag_statuses = BagStatus::select('id', 'name')->whereNotIn('id', [1, 4, 9])->get();
        $modes = ShippingMode::select('id', 'mode')->get();
        return view('admin.reports.in_transit_report_bag_wise')->with(['bag_statuses' => $bag_statuses, 'modes' => $modes]);
    }

    public function in_transit_list(Request $request)
    {

        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 335);
        }
        $bags = DB::connection('reports')->table('bags')
            ->join('master_cargo_bags as mcb', function ($join) {
                $join->on('mcb.bag_id', '=', 'bags.id')
                    ->where('mcb.created_at', '=', DB::raw('(select max(created_at) from master_cargo_bags where master_cargo_bags.bag_id= bags.id)'));
            })
            ->join('master_cargoes as mc', 'mc.id', '=', 'mcb.master_cargo_id')
            ->join('shipping_modes as sm', 'bags.shipping_mode_id', '=', 'sm.id')
            ->leftjoin('admins as a', 'a.id', '=', 'mc.received_by')
            ->join('admins as ad', 'ad.id', '=', 'mc.created_by')
            ->join('cities as oc', 'bags.origin_hub_id', '=', 'oc.id')
            ->join('cities as dc', 'bags.destination_hub_id', '=', 'dc.id')
            ->join('bag_statuses as bs', 'bs.id', '=', 'bags.status_id')
            ->select(['bags.seal_number as bag_no', 'bags.type', 'oc.name as origin', 'dc.name as destination', 'sm.mode as shipping_mode', 'bags.shipments', 'bags.short_received', 'bags.shipments_weight', 'ad.name as transitted_by', 'a.name as received_by', 'mc.created_at as transitted_date', 'mc.received_at', 'bs.name as status', 'bs.id as status_id', 'bags.shipments as total_shipments', 'bags.short_received as short_received_shipments', 'mc.id as master_cargo_id'])->whereNotIn('bags.status_id', [1, 4, 9]);

        $datatable = Datatables::of($bags)
            ->editColumn('type', function ($bags) {
                if ($bags->type == 1) {
                    return 'Normal';
                } else {
                    return 'Return';
                }
            })
            ->addColumn('id_padded_link', function ($bag) {
                return '<button class="btn btn-sm btn-outline-info align-middle print"><i class="la la-lg la-print align-middle"></i> <span class="align-middle">' . str_pad($bag->master_cargo_id, 6, '0', STR_PAD_LEFT) . '</span></button>';
            })
            ->addColumn('aging', function ($bags) {
                if ($bags->received_at != null) {
                    Carbon::setWeekendDays([
                        Carbon::SUNDAY,
                    ]);
                    $received_date = Carbon::parse($bags->received_at);
                    $transitted_date = Carbon::parse($bags->transitted_date);
                    $days = $transitted_date->diffInDays($received_date);
                    if ($days > 0) {
                        return $days . ' days';
                    } else {
                        return '-';
                    }
                } else {
                    return '-';
                }
            })
            ->editColumn('shipments', function ($bags) {
                if ($bags->shipments != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $bags->shipments . '</button>';
                } else {
                    return 0;
                }
            })
            ->editColumn('short_received', function ($bags) {
                if ($bags->short_received != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $bags->short_received . '</button>';
                } else {
                    return 0;
                }
            })
            ->editColumn('status_id', function ($bags) {
                return $bags->status;
            })
            ->filterColumn('status_id', function ($query, $keyword) {
                if ($keyword != '') {
                    $query->where('bags.status_id', $keyword);
                } else {
                    $query->whereRaw('false');
                }
            });
        return $datatable->make(true);
    }

    public function in_transit_shipments(Request $request)
    {
        $seal_number = $request->input('bag_id');
        $bag = Bag::where('seal_number', $seal_number)->first();
        $bag_shipments = $bag->shipment;
        $shipments = array();
        if ($bag_shipments->count() != 0) {
            foreach ($bag_shipments as $bag_shipment) {
                $shipment = Shipment::find($bag_shipment->shipment_id);
                $shipments[] = $shipment->tracking_number;
            }
            return ['status' => 0, 'success' => 'Shipments Founds', 'shipments' => $shipments];
        } else {
            return ['status' => 0, 'success' => 'No Shipments Found', 'shipments' => FALSE];
        }

    }

    public function short_received_shipments(Request $request)
    {
        $seal_number = $request->input('bag_id');
        $bag = Bag::where('seal_number', $seal_number)->first();
        $bag_shipments = $bag->shipment->where('status', 0);
        $shipments = array();
        if ($bag_shipments->count() != 0) {
            foreach ($bag_shipments as $bag_shipment) {
                $shipment = Shipment::find($bag_shipment->shipment_id);
                $shipments[] = $shipment->tracking_number;
            }
            return ['status' => 0, 'success' => 'Shipments Founds', 'shipments' => $shipments];
        } else {
            return ['status' => 0, 'success' => 'No Shipments Found', 'shipments' => FALSE];
        }

    }

    public function master_cargo_short_received_shipments_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 336);
        return view('admin.reports.master_cargo_short_received_shipments_reports');
    }

    public function master_cargo_short_received_shipments_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 337);
        }
        $cargo_consignments_short_received_shipments = DB::connection('reports')->table('bags')->leftjoin('bag_shipments as bs', 'bs.bag_id', '=', 'bags.id')
            ->join('master_cargo_bags as mcb', function ($join) {
                $join->on('mcb.bag_id', '=', 'bags.id')
                    ->where('mcb.created_at', '=', DB::raw('(select max(created_at) from master_cargo_bags where master_cargo_bags.bag_id= bags.id)'));
            })
            ->join('master_cargoes as mc', 'mc.id', '=', 'mcb.master_cargo_id')
            ->leftjoin('cities as oc', 'oc.id', '=', 'bags.origin_hub_id')
            ->leftjoin('cities as dc', 'dc.id', '=', 'bags.destination_hub_id')
            ->leftjoin('shipping_modes as sm', 'sm.id', '=', 'bags.shipping_mode_id')
            ->leftjoin('shipments as s', 's.id', '=', 'bs.shipment_id')
            ->select('s.tracking_number as tracking_number', 'bags.id as bag', 'oc.name as origin', 'dc.name as destination', 'sm.mode as shipping_mode', 'bags.type as cargo_type', 'mc.id as cargo', 'mc.created_at as transited_at', 'bags.seal_number')
            ->where('bags.status_id', 7)
            ->whereIn('s.shipper_status_id', [3, 21])->get();

        $datatables = Datatables::of($cargo_consignments_short_received_shipments)
            ->editColumn('tracking_number_link', function ($shipments) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->addColumn('id_padded_link', function ($master_cargo) {
                return '<button class="btn btn-sm btn-outline-info align-middle print"><i class="la la-lg la-print align-middle"></i> <span class="align-middle">' . str_pad($master_cargo->cargo, 6, '0', STR_PAD_LEFT) . '</span></button>';
            })
            ->editColumn('cargo_type', function ($shipments) {
                if ($shipments->cargo_type == 1) {
                    return 'Normal';
                } else {
                    return 'Return';
                }
            });
        return $datatables->make(true);
    }

    public function manifest_short_received_shipments_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 406);
        return view('admin.reports.manifest_short_received_shipments_reports');
    }

    public function manifest_short_received_shipments_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 407);
        }
        $short_received_shipments = DB::connection('reports')->table('cargo_manifest_bags')->leftjoin('cargo_manifest_bag_shipments as cmbs', 'cmbs.cargo_manifest_bag_id', '=', 'cargo_manifest_bags.id')
            ->join('manifest_bags as mb', function ($join) {
                $join->on('mb.cargo_manifest_bag_id', '=', 'cargo_manifest_bags.id')
                    ->where('mb.created_at', '=', DB::raw('(select max(created_at) from manifest_bags where manifest_bags.cargo_manifest_bag_id = cargo_manifest_bags.id)'));
            })
            ->join('cargo_manifests as cm', 'cm.id', '=', 'mb.cargo_manifest_id')
            ->leftjoin('cities as oc', 'oc.id', '=', 'cargo_manifest_bags.origin_hub_id')
            ->leftjoin('cities as dc', 'dc.id', '=', 'cargo_manifest_bags.destination_hub_id')
            ->leftjoin('shipping_modes as sm', 'sm.id', '=', 'cm.shipping_mode_id')
            ->leftjoin('shipments as s', 's.id', '=', 'cmbs.shipment_id')
            ->select('s.tracking_number as tracking_number', 'cargo_manifest_bags.id as bag', 'oc.name as origin', 'dc.name as destination', 'sm.mode as shipping_mode', 'cargo_manifest_bags.type as bag_type', 'cm.id as manifest_id', 'cm.created_at as transited_at', 'cargo_manifest_bags.seal_number')
            ->where('cargo_manifest_bags.status_id', 9)
            ->whereIn('s.shipper_status_id', [3, 21])->get();

        $datatables = Datatables::of($short_received_shipments)
            ->editColumn('tracking_number_link', function ($shipments) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->addColumn('id_padded_link', function ($master_cargo) {
                return '<button class="btn btn-sm btn-outline-info align-middle print"><i class="la la-lg la-print align-middle"></i> <span class="align-middle">' . str_pad($master_cargo->manifest_id, 6, '0', STR_PAD_LEFT) . '</span></button>';
            })
            ->editColumn('bag_type', function ($shipments) {
                if ($shipments->bag_type == 1) {
                    return 'Normal';
                } else {
                    return 'Return';
                }
            });
        return $datatables->make(true);
    }

    public function shipper_insurance_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 255);
        $shipper_name = User::select('id', 'name')->get();
        $today = Carbon::now()->endOfDay();
        $threedays = Carbon::now()->subDays(3)->startOfDay();
        return view('admin.reports.shipper_insurance')->with(['shipper_name' => $shipper_name, 'today' => $today, 'threedays' => $threedays]);
    }

    public function shipper_insurance_list(Request $request)
    {

        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 256);
        }
        $shipments = Shipment::join('users as u', 'shipments.user_id', '=', 'u.id')
            ->join('shipment_items as si', 'si.shipment_id', '=', 'shipments.id')
            ->select('shipments.id as shId', 'shipments.tracking_number as tracking_number_link', 'shipments.tracking_number as tracking_number', 'u.name as shipper', 'shipments.insurance_charges', 'shipments.created_at', 'si.insurance', 'si.price as price', DB::raw('sum(si.price) as total_insurance'))
            ->groupBy('tracking_number');

        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $shipments = $shipments->whereBetween('shipments.created_at', [$from, $to]);
        }

        $datatables = Datatables::of($shipments)
            ->editColumn('tracking_number_link', function ($shipments) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })->editColumn('insurance', function ($shipments) {
                if ($shipments->insurance == 0) {
                    return 'No';
                } else {
                    return 'Yes';
                }
            })
            ->addColumn('charges', function ($shipments) {
                return '<div class="text-center">
                                <button type="button" class="btn btn-primary btn-sm"><a class="white" ><i class="la la-dollar align-middle"></i></a></button>
                        </div>';
            });

        if ($shipper_id = $request->get('shipper_name')) {
            $receiving_sheet = $shipments->where('shipments.user_id', '=', $shipper_id);
        }


        /*if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $datatables->whereBetween('shipments.created_at', [$from,$to]);
        }*/

        if ($tracking_numbers = $request->get('tracking_numbers')) {
            $shipments->whereIn('shipments.tracking_number', explode(',', $tracking_numbers));
        }
        return $datatables->make(true);
    }

    public function shipper_insurance_charges(Request $request)
    {
        $tracking_number = $request->tracking_number;

        if ($tracking_number) {
            $shipment = Shipment::where('tracking_number', $tracking_number)->first();
            $user = User::find($shipment->user_id);

            if ($user->account_type->id == 1) {
                $insurance_charges = InsuranceCharge::where('user_id', $user->id);

            } else if ($user->account_type->id == 2) {
                $insurance_charges = CorporateInsuranceCharge::where('user_id', $user->id);
            } else {
                $insurance_charges = CorporateDefaultInsuranceCharge::where('user_id', $user->id);
            }
            if ($insurance_charges->exists()) {
                $insurance_charges = $insurance_charges->get();
                return response()->json(['status' => 1, 'insurance_charges' => $insurance_charges]);
            } else {
                return response()->json(['status' => 0, 'error' => 'No Charges Found!']);
            }

        }
    }

    public function operation_service_level_index()
    {

        ActivityTrailController::createActivityTrailLog(Auth::id(), 442);
        $shippers = DB::connection('reports')->table('users')->whereIn('status', [3, 4])->select('id', 'name')->get();
        $cities = DB::connection('reports')->table('cities')->select('id', 'name')->get();
        $hubs = DB::connection('reports')->table('cities')->where('hub', 1)->select('id', 'name')->get();
        $shippimg_modes = DB::connection('reports')->table('shipping_modes')->get();
        $statuses = DB::connection('reports')->table('shipment_status')->get(['id', 'name']);
        $zones = Zone::where('status', 1)->where('business_category_id', 1)->get();

        return view('admin.reports.operation_service_report')->with(['shippers' => $shippers, 'cities' => $cities, 'hubs' => $hubs, 'shippimg_modes' => $shippimg_modes, 'statuses' => $statuses, 'zones' => $zones]);

    }

    public function operation_service_level_list(Request $request)
    {

        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 443);
        }

        $shipments = DB::connection('reports')->table('shipments')->join('users as u', 'shipments.user_id', '=', 'u.id')
            ->join('shipping_modes as sm', 'shipments.shipping_mode_id', '=', 'sm.id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->join('cities as h', 'dc.hub_id', '=', 'h.id')
            ->join('zones as z', 'z.id', '=', 'h.zone_id')
            ->join('booking_types as bt', 'bt.id', '=', 'shipments.booking_type_id')
            ->join('shipment_status as ss', 'ss.id', '=', 'shipments.shipper_status_id')
            ->leftJoin('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where('sj.id', '=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)'));
            })
            ->leftJoin('shipments_journey as journey', function ($join) {
                $join->on('journey.shipment_id', '=', 'shipments.id')
                    ->where('journey.id', '=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id)'));
            })
            ->leftjoin('shipment_items as si', function ($join) {
                $join->on('si.shipment_id', '=', 'shipments.id')
                    ->where('si.type', '=', 0);
            })
            ->leftjoin('products as p', 'p.id', '=', 'si.product_type_id')
            ->select(['p.product_name as product_type', 'si.description as description', 'shipments.id as shId', 'shipments.tracking_number', 'shipments.tracking_number as tracking_number_link', 'u.name as shipper', 'ss.name as history_status', 'bt.booking_type as service_type', 'sj.created_at as arrival', 'oc.name as origin', 'dc.name as destination', 'h.name as hub', 'shipments.amount', 'journey.created_at as last_status_date', 'shipments.consignee_name as name', 'shipments.booking_type_id', 'shipments.created_at', 'usi.poc', 'u.id as account_no', 'sm.mode as shipping_mode']);

        $shipments = $shipments->whereNotIn('shipments.shipper_status_id', [14, 16, 17, 25, 31, 36, 38, 39, 40, 41, 43, 47, 51]);

        if (session('role_id') != 1) {
            $shipments = $shipments->where(function ($query) {
                $query->where(function ($sub_query) {
                    $sub_query->whereIn('dc.hub_id', session('hubs'));
                })
                    ->orWhere(function ($sub_query) {
                        $sub_query->whereIn('oc.hub_id', session('hubs'));
                    });
            });
        }
        $datatable = Datatables::of($shipments)
            ->editColumn('tracking_number_link', function ($shipments) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->editColumn('amount', function ($shipment) {
                return number_format($shipment->amount);
            })
            ->editColumn('account_no', function ($shipments) {
                return str_pad($shipments->account_no, 6, '0', STR_PAD_LEFT);
            })
            ->editColumn('shipper', function ($shipment) {
                if ($shipment->booking_type_id == 4) {
                    return $shipment->shipper . ' (' . $shipment->poc . ')';
                } else {
                    return $shipment->shipper;
                }
            })
            ->filterColumn('u.name', function ($query, $keyword) {
                $query->where(function ($sub_query) use ($keyword) {
                    $sub_query->where('shipments.booking_type_id', '!=', 4)
                        ->where('u.name', 'like', '%' . $keyword . '%');
                })
                    ->orWhere(function ($sub_query) use ($keyword) {
                        $sub_query->where('shipments.booking_type_id', '=', 4)
                            ->where('usi.poc', 'like', '%' . $keyword . '%');
                    });
            })
            ->addColumn('aging_booking', function ($shipments) {

                $days = Carbon::now()->diffInDays($shipments->created_at);
                if ($days == 0) {
                    return "-";
                } else {
                    return $days;
                }
            })
            ->addColumn('aging', function ($shipments) {

                $days = Carbon::now()->diffInDays($shipments->arrival);
                if ($days == 0) {
                    return "-";
                } else {
                    return $days;
                }
            })
            ->addColumn('aging_last_status', function ($shipments) {

                $days = Carbon::now()->diffInDays($shipments->last_status_date);
                if ($days == 0) {
                    return "-";
                } else {
                    return $days;
                }
            });

        if ($search_shipper = $request->get('search_shipper')) {
            $shipments = $shipments->whereIn('shipments.user_id', $search_shipper);
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
        if ($shipping_mode = $request->get('search_shipping_mode')) {
            $datatable->where('sm.id', '=', $shipping_mode);
        }
        if ($request->get('arrival_date_from') && $request->get('arrival_date_to')) {
            $from = $request->get('arrival_date_from');
            $to = $request->get('arrival_date_to');
            $datatable->whereBetween('sj.created_at', [$from, $to]);
        }
        if ($request->get('booking_date_from') && $request->get('booking_date_to')) {
            $from = $request->get('booking_date_from');
            $to = $request->get('booking_date_to');
            $datatable->whereBetween('shipments.created_at', [$from, $to]);
        }
        if ($status = $request->get('search_status')) {
            $datatable->where('shipments.shipper_status_id', '=', $status);
        }
        if ($zone = $request->get('search_zone')) {
            $datatable->where('z.id', '=', $zone);
        }
        return $datatable->make(true);
    }


    public function work_code_master_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 267);
        $shippers = DB::connection('reports')->table('users')->whereIn('status', [3, 4])->select('id', 'name')->get();
        $riders = DB::connection('reports')->table('riders')->where('status', 1)->select('id', 'name')->get();
        $admins = DB::connection('reports')->table('admins')->where('status', 1)->select('id', 'name')->get();
        $statuses = DB::connection('reports')->table('shipment_status')->where('status', 1)->select('id', 'name')->get();

        return view('admin.reports.work_code_master')->with(['shippers' => $shippers, 'statuses' => $statuses, 'riders' => $riders, 'admins' => $admins]);

    }

    public function work_code_master_list(Request $request)
    {

        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 268);
        }
        $shipments = DB::connection('reports')->table('shipments_journey')
            ->leftjoin('admins as ad', 'shipments_journey.admin_id', '=', 'ad.id')
            ->leftjoin('admin_roles as adr', 'ad.role_id', '=', 'adr.id')
            ->leftjoin('admin_departments as dpt', 'adr.department_id', '=', 'dpt.id')
            ->join('shipments as sh', 'shipments_journey.shipment_id', '=', 'sh.id')
            ->join('users as su', 'sh.user_id', '=', 'su.id')
            ->join('shipment_status as ss', 'ss.id', '=', 'shipments_journey.shipper_status_id')
            ->leftjoin('users as u', 'shipments_journey.user_id', '=', 'u.id')
            ->leftjoin('delivery_notes as dn', 'shipments_journey.reference_1_id', '=', 'dn.id')
            ->leftjoin('return_notes as rn', 'shipments_journey.reference_1_id', '=', 'rn.id')
            ->leftjoin('riders as r', 'shipments_journey.rider_id', '=', 'r.id')
            ->select(['shipments_journey.reference_1_id as ref_id','sh.id as shipment_id','sh.tracking_number','sh.tracking_number as tracking_number_link','r.id','r.name as rider_status_marked_by','u.name as shipper_status_marked_by','su.name as shipper','sh.user_id','ss.name as status_marked','shipments_journey.created_at as status_marking_date','ad.name as status_marked_by','ad.id as admin_id','shipments_journey.id as shId', 'ss.id as status_id', 'shipments_journey.user_id', 'shipments_journey.user_id as ssjj_user_id', 'shipments_journey.admin_id', 'shipments_journey.rider_id','dpt.name as status_marked_by_department','shipments_journey.status_reason_id as reason']);
            
        $datatable = Datatables::of($shipments)
            ->editColumn('tracking_number_link', function ($shipments) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->editColumn('status_marked_by', function ($shipments) {
                if ($shipments->admin_id == null) {
                    if ($shipments->ssjj_user_id == null) {
                        return $shipments->rider_status_marked_by;
                    } else {
                        return $shipments->shipper_status_marked_by;
                    }
                } else {
                    return $shipments->status_marked_by;
                }
            })->filterColumn('ss.id', function ($query, $keyword) {
                $query->where('ss.id', '=', $keyword);

            })
            ->editColumn('rider_status_marked_by', function ($shipments) {
                if ($shipments->status_id == 5) {
                    $delivery_note = DeliveryNote::find($shipments->ref_id);
                    return $delivery_note->rider->name;
                } elseif ($shipments->status_id == 2) {
                    return $shipments->rider_status_marked_by;
                } elseif ($shipments->status_id == 23) {
                    // $return_note_shipment = ReturnNoteShipment::where('shipment_id',$shipments->shipment_id)->get()->first();
                    $return_note = ReturnNote::find($shipments->ref_id);
                    return $return_note->rider->name;
                } else {
                    return '';
                }

            })
            ->editColumn('reason', function($shipments)
            {
                $reason = $shipments->reason;
                if($reason)
                {
                    $reason = ShipmentStatusReason::where('id',$reason);
                    if($reason->exists())
                    {
                        $reason = $reason->first();
                        $reason_name = $reason->name;
                        return isset($reason_name) ? $reason_name : '-';
                    }
                }
            });

        if ($tracking_number = $request->get('tracking_number')) {
            $datatable->whereIn('sh.tracking_number', explode(',', $tracking_number));
        }

        if ($request->get('search_from') && $request->get('search_to')) {
            $from = $request->get('search_from');
            $to = $request->get('search_to');
            $datatable->whereBetween('shipments_journey.created_at', [$from, $to]);
        }
        if ($shipper_id = $request->get('search_shipper')) {
            $datatable->where('sh.user_id', $shipper_id);
        }

        if ($status_marked = $request->get('status_marked')) {
            $datatable->whereIn('ss.id', $status_marked);
        }
        if ($rider_id = $request->get('search_rider')) {
            $datatable->where('shipments_journey.rider_id', $rider_id);
        }
        if ($admins_id = $request->get('search_admin')) {
            $datatable->where('shipments_journey.admin_id', $admins_id);
        }
        if ($search_last_rider = $request->get('search_last_rider')) {
            $datatable->where(function ($query) use ($search_last_rider) {
                $query->where([
                    ['dn.rider_id', '=', $search_last_rider],
                    ['shipments_journey.shipper_status_id', '=', '5']
                ])->orWhere([
                    ['rn.rider_id', '=', $search_last_rider],
                    ['shipments_journey.shipper_status_id', '=', '23']
                ])->orWhere([
                    ['r.id', '=', $search_last_rider],
                    ['shipments_journey.shipper_status_id', '=', '2']
                ]);

            });


            // $datatable->where([
            //     ['dn.rider_id', '=', $search_last_rider],
            //     ['shipments_journey.shipper_status_id', '=', '5']
            // ])->orWhere([
            //     ['rn.rider_id', '=', $search_last_rider],
            //     ['shipments_journey.shipper_status_id', '=', '23']
            // ])->orWhere([
            //     ['r.id', '=', $search_last_rider],
            //     ['shipments_journey.shipper_status_id', '=', '2']
            // ]);

        }


        return $datatable->make(true);
    }

    public function reverse_pickup_index()
    {

        ActivityTrailController::createActivityTrailLog(Auth::id(), 470);
        return view('admin.reports.reverse_pickup');
    }

    public function reverse_pickup_list(Request $request)
    {
        $connection = 'reports';
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 471);
        }
        $shipments = DB::connection('reports')->table('shipments')->join('users as u', 'u.id', '=', 'shipments.user_id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->join('booking_types as bt', 'bt.id', '=', 'shipments.booking_type_id')
            ->leftJoin('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where('sj.id', '=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)'));
            })
            ->select(['shipments.tracking_number as tracking_number_link', 'shipments.order_id as order_id', 'shipments.tracking_number', 'u.name as shipper', 'u.address as address', 'u.phone as contact', 'oc.name as origin', 'dc.name as destination', DB::raw('(select count(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 5) as total_attempt'), 'sj.created_at as arrival_date', 'shipments.pickup_date as pickup_date', 'shipments.consignee_name as consignee_name', 'shipments.consignee_phone_number_1 as consignee_contact', 'shipments.consignee_address as consignee_address'])
            ->where('shipments.booking_type_id', '=', 5);

        $datatable = Datatables::of($shipments)
            ->editColumn('tracking_number_link', function ($shipments) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            });

        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $datatable->whereBetween('sj.created_at', [$from, $to]);
        }
        return $datatable->make(true);
    }


    public function sales_incentive_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 461);
        $filter_dates = DB::connection('reports')->table('sales_incentive_filter_dates')
            ->select('id', 'date')
            ->orderBy('id', 'desc')
            ->get();
        $admins = DB::connection('reports')->table('admins')->whereExists(function ($query) {
            $query->from('admin_roles')
                ->where('admins.role_id', '=', DB::raw('`admin_roles`.`id`'))
                ->where('department_id', '=', 7);
        })->select('id', 'name')->get();
        $cities = City::where('business_category_id', 1)->where('hub', 1)->select('id', 'name')->get();
        return view('admin.reports.sales_incentive')->with(['filter_dates' => $filter_dates, 'admins' => $admins, 'cities' => $cities]);
    }

    public function sales_incentive_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 467);
        }
        $incentive = DB::connection('reports')->table('sales_incentives')
            ->join('sales_territories as st', 'st.id', '=', 'sales_incentives.territory_id')
            ->join('cities as c', 'c.id', '=', 'st.cityid')
            ->join('sales_designations as sd', 'sd.id', '=', 'sales_incentives.designation_id')
            ->join('admins as a', 'a.id', '=', 'sales_incentives.admin_id')
            ->join('sales_incentive_filter_dates as sifd', 'sifd.id', '=', 'sales_incentives.filter_date_id')
            ->select(['sales_incentives.id', 'st.code as territory_code', 'sd.code as designation_code', 'a.name as admin', 'sales_incentives.shipper_count', 'sales_incentives.shipment_count', 'sales_incentives.revenue', 'sales_incentives.commission', 'c.name as origin_city'])
            ->where('sifd.id', $request->filter_date);

        $datatable = Datatables::of($incentive)
            ->addColumn('sales_code', function ($data) {
                return $data->designation_code . '-' . $data->territory_code;
            });
        if ($admin_id = $request->get('admin_id')) {
            $datatable->where('a.id', $admin_id);
        }
        if ($city_id = $request->get('city_id')) {
            $datatable->where('c.id', $city_id);
        }
        return $datatable->make(true);
    }

    public function consolidated_sales_incentive_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 462);
        $filter_dates = DB::connection('reports')->table('sales_incentive_filter_dates')
            ->select('id', 'date')
            ->orderBy('id', 'desc')
            ->get();
        $admins = DB::connection('reports')->table('admins')->whereExists(function ($query) {
            $query->from('admin_roles')
                ->where('admins.role_id', '=', DB::raw('`admin_roles`.`id`'))
                ->where('department_id', '=', 7);
        })->select('id', 'name')->get();
        return view('admin.reports.consolidated_sales_incentive')->with(['filter_dates' => $filter_dates, 'admins' => $admins]);
    }

    public function consolidated_sales_incentive_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 468);
        }
        $incentive = DB::connection('reports')->table('sales_consolidated_incentives')
            ->join('admins as a', 'a.id', '=', 'sales_consolidated_incentives.admin_id')
            ->join('sales_incentive_filter_dates as sifd', 'sifd.id', '=', 'sales_consolidated_incentives.filter_date_id')
            ->select(['sales_consolidated_incentives.id', 'a.name as admin', 'a.trax_id as trax_id', 'sales_consolidated_incentives.shipper_count', 'sales_consolidated_incentives.shipment_count', 'sales_consolidated_incentives.revenue', 'sales_consolidated_incentives.commission'])
            ->where('sifd.id', $request->filter_date);

        $datatable = Datatables::of($incentive);

        if ($admin_id = $request->get('admin_id')) {
            $datatable->where('a.id', $admin_id);
        }
        return $datatable->make(true);
    }

    public function dws_report_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 476);

        return view('admin.reports.dws_report');

    }

    public function dws_report_list(Request $request)
    {

        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 477);
        }
        $shipments = DB::connection('reports')->table('shipments')->join('shipment_details as sd', 'shipments.id', '=', 'sd.shipment_id')
            ->leftJoin('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where('sj.id', '=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)'));
            })
            ->select(['shipments.tracking_number', 'shipments.tracking_number as tracking_number_link', 'sd.dense_weight as dense_weight', 'sd.dimension_l as length', 'sd.dimension_w as width', 'sd.dimension_h as height', 'sd.dws_status as weight_type', 'sj.created_at as date', 'sd.dws_image as dws_image'])
            ->where('sd.dws_status', '<>', Null);

        $datatable = Datatables::of($shipments)
            ->editColumn('tracking_number_link', function ($shipments) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })->editColumn('weight_type', function ($shipments) {
                if ($shipments->weight_type == 1) {
                    return "High";
                } else {
                    return "Low";

                }
            })->addColumn('dws_image', function ($shipments) {
                if ($shipments->dws_image != null) {
                    $image = '';
                    $exists = Storage::disk('public')->exists($shipments->dws_image);
                    if ($exists) {
                        $image .= '<div class="text-center"><button type="button" class="btn btn-primary btn-sm picture" data-link="' . asset(Storage::url($shipments->dws_image)) . '"><i class="la la-image"></i> View</button></div>';
                    } else {
                        $img = Storage::disk('s3')->temporaryUrl($shipments->dws_image, now()->addMinutes(5));
                        $image = '<a class="btn btn-sm btn-outline-info align-middle" href="' . $img . '" target="_blank"><i class="la la-lg la-image align-middle"></i> <span class="align-middle">View</span></a>';
                    }

                    return $image;
                } else {
                    return "-";

                }
            });

        if ($request->get('search_from') && $request->get('search_to')) {
            $from = $request->get('search_from');
            $to = $request->get('search_to');
            $datatable->whereBetween('sj.created_at', [$from, $to]);
        }
        if ($tracking_number = $request->get('tracking_number')) {
            $datatable->whereIn('shipments.tracking_number', explode(',', $tracking_number));
        }
        return $datatable->make(true);
    }

    public function osa_charges_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 481);
        $filter_dates = DB::connection('reports')->table('sales_incentive_filter_dates')
            ->select('id', 'date')
            ->orderBy('id', 'desc')
            ->get();
        $admins = DB::connection('reports')->table('admins')->whereExists(function ($query) {
            $query->from('admin_roles')
                ->where('admins.role_id', '=', DB::raw('`admin_roles`.`id`'))
                ->where('department_id', '=', 7);
        })->select('id', 'name')->get();
        $cities = City::where('business_category_id', 1)->where('hub', 1)->select('id', 'name')->get();
        return view('admin.reports.osa_charges')->with(['filter_dates' => $filter_dates, 'admins' => $admins, 'cities' => $cities]);
    }

    public function osa_charges_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 482);
        }
        $shipments = Shipment::join('osa_charges_logs as nc', 'shipments.id', '=', 'nc.shipment_id')
            ->leftjoin('admins as a', 'a.id', '=', 'nc.updated_by')
            ->select('shipments.tracking_number as tracking_number', 'shipments.tracking_number as tracking', 'a.name as updated_by', 'nc.osa_charges as osa_charges', 'nc.updated_at as updated_at');

        $datatable = Datatables::of($shipments)
            ->editColumn('tracking_number', function ($shipments) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            });

        if ($request->get('search_from') && $request->get('search_to')) {
            $from = $request->get('search_from');
            $to = $request->get('search_to');
            $shipments = $shipments->whereBetween('nc.updated_at', [$from, $to]);
        }
        return $datatable->make(true);
    }

    public function return_revert_log()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 484);

        return view('admin.reports.return_revert_log');

    }

    public function return_revert_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 485);
        }
        $shipments = Shipment::join('return_revert_logs as rr', 'shipments.id', '=', 'rr.shipment_id')
            ->leftjoin('admins as a', 'a.id', '=', 'rr.updated_by')
            ->select('shipments.tracking_number as tracking_number', 'shipments.tracking_number as tracking', 'a.name as updated_by', 'rr.return_note as return_note', 'rr.updated_at as updated_at', 'rr.shipper as shipper');

        $datatable = Datatables::of($shipments)
            ->editColumn('tracking_number', function ($shipments) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->editColumn('return_note', function ($shipments) {
                return str_pad($shipments->return_note, 6, '0', STR_PAD_LEFT);
            });

        if ($request->get('search_from') && $request->get('search_to')) {
            $from = $request->get('search_from');
            $to = $request->get('search_to');
            $shipments = $shipments->whereBetween('rr.updated_at', [$from, $to]);
        }
        return $datatable->make(true);

    }


    public function pickup_history_cn_wise_index(Request $request)
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 506);
        $cities = DB::connection('reports')->table('cities')->get(['id', 'name']);
        $riders = DB::connection('reports')->table('riders')->get(['id', 'name']);
        $hubs = DB::connection('reports')->table('cities')->where('hub', '=', 1)->select(['id', 'name'])->get();
        return view('admin.reports.pickup_history_cn_wise')->with(['cities' => $cities, 'hubs' => $hubs, 'riders' => $riders]);
    }

    public function pickup_history_cn_wise_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 507);
        }
        $shipments = DB::connection('reports')->table('shipments')
            ->join('users as u', 'u.id', '=', 'shipments.user_id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities as h', 'oc.hub_id', '=', 'h.id')
            ->join('v2_pickup_request_shipments as vps', 'vps.shipment_id', '=', 'shipments.id')
            ->join('v2_pickup_requests as vpr', 'vps.pickup_request_id', '=', 'vpr.id')
            ->leftjoin('riders as cr', 'cr.id', '=', 'vpr.current_rider_id')
            ->leftJoin('v2_pickup_note_requests as vpn', function ($join) {
                $join->on('vpn.pickup_request_id', '=', 'vpr.id')
                    ->where('vpn.id', '=',
                        DB::connection('reports')->raw('(select max(id) from v2_pickup_note_requests where v2_pickup_note_requests.pickup_request_id = vpr.id)'));
            })
            ->leftJoin('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where('sj.id', '=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)'));
            })
            ->leftJoin('v2_rider_pickups as vrp', function ($join) {
                $join->on('vrp.pickup_request_id', '=', 'vpr.id')
                    ->where('vrp.id', '=',
                        DB::raw('(select max(id) from v2_rider_pickups where v2_rider_pickups.pickup_request_id = vpr.id)'));
            })
            ->select('shipments.tracking_number as tracking_number', 'shipments.created_at as booking_date', 'shipments.tracking_number as tracking_number_link', 'usi.pickup_address as pickup_address', 'oc.name as origin', 'h.name as hub', 'vpn.pickup_note_id as pickup_note_id', 'vrp.created_at as pickup_date', 'sj.created_at as arrival_date', 'cr.name as rider', 'u.name as shipper');
        if (session('role_id') != 1) {
            $shipments = $shipments->whereIn('oc.hub_id', session('hubs'));
        }
        $pickup_history = Datatables::of($shipments)
            ->editColumn('tracking_number_link', function ($shipments) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->editColumn('pickup_note_no_print', function ($shipments) {
                if ($shipments->pickup_note_id != null) {
                    return '<button class="btn btn-sm btn-outline-info align-middle print" rel="' . $shipments->pickup_note_id . '"><i class="la la-lg la-print align-middle"></i> <span class="align-middle">' . str_pad($shipments->pickup_note_id, 6, '0', STR_PAD_LEFT) . '</span></button>';
                }
                return '';
            })
            ->editColumn('pickup_note_no', function ($shipments) {
                if ($shipments->pickup_note_id != null) {
                    return str_pad($shipments->pickup_note_id, 6, '0', STR_PAD_LEFT);
                }
                return '';
            })
            ->addColumn('arrival_status_badge', function ($shipments) {
                if ($shipments->arrival_date) {
                    return '<span class="badge bg-success">Arrival Done</span>';
                } else {
                    return '<span class="badge bg-danger">Arrival Not Done</span>';
                }
            })
            ->addColumn('arrival_status', function ($shipments) {
                if ($shipments->arrival_date) {
                    return 'Arrival Done';
                } else {
                    return 'Arrival Not Done';
                }
            });
        if ($rider = $request->get('search_rider')) {
            $shipments->where('cr.id', '=', $rider);
        }
        if ($origin = $request->get('search_origin')) {
            $shipments->where('oc.id', '=', $origin);
        }
        if ($hub = $request->get('search_hub')) {
            $shipments->where('h.id', '=', $hub);
        }
        if ($request->get('search_from') && $request->get('search_to')) {
            $from = $request->get('search_from');
            $to = $request->get('search_to');
            $shipments->whereBetween('shipments.created_at', [$from, $to]);
        }
        return $pickup_history->make(true);
    }

    public function crm_count_index()
    {
        // dd(Carbon::now()->subDays());
        // dd(date('D'));


        // $crm_count_report = CRMCount::all()->groupBy(function($date) {
        //     return Carbon::parse($date->date)->format('W');
        // });
        // foreach ($crm_count_report as $key => $value) {
        //     dump($key);
        //     // dump('count');
        //     // dump($value->count());
        //     foreach($value as $item){
        //         dump($item);
        //     }
        // }
        // dd($crm_count_report);
        // $crm_count_data = array();
        // $crm_count_records = CRMCount::all()->groupBy(function($date) {
        //     return Carbon::parse($date->date)->format('W');
        // });
        // foreach ($crm_count_records as $key => $value) {

        //     $crm_count_data[$key]['count_days'] = $value->count();
        //     $avg_closed = 0;
        //     $avg_remaining = 0;
        //     foreach($value as $item){
        //         $avg_closed += number_format((($item->closed / (($item->pending + $item->new_launched) - $item->closed)) * 100), 2);
        //         $avg_remaining += number_format(((($item->pending + $item->new_launched) / (($item->pending + $item->new_launched) - $item->closed)) * 100), 2);
        //         $crm_count_data[$key]['data'][$item->id]['id'] = $item->id;
        //         $crm_count_data[$key]['data'][$item->id]['pending'] = $item->pending;
        //         $crm_count_data[$key]['data'][$item->id]['new_launched'] = $item->new_launched;
        //         $crm_count_data[$key]['data'][$item->id]['closed'] = $item->closed;
        //         $crm_count_data[$key]['data'][$item->id]['date'] = $item->date;
        //         $crm_count_data[$key]['data'][$item->id]['remaining'] = ($item->pending + $item->new_launched);
        //         $crm_count_data[$key]['data'][$item->id]['total'] = (($item->pending + $item->new_launched) - $item->closed);
        //         $crm_count_data[$key]['data'][$item->id]['closure_percent'] = number_format((($item->closed / (($item->pending + $item->new_launched) - $item->closed)) * 100), 2);
        //         $crm_count_data[$key]['data'][$item->id]['remaining_percent'] = number_format(((($item->pending + $item->new_launched) / (($item->pending + $item->new_launched) - $item->closed)) * 100), 2);
        //     }
        //     $crm_count_data[$key]['weekly_close'] = $avg_closed/$value->count();
        //     $crm_count_data[$key]['weekly_remaining'] = $avg_remaining/$value->count();
        // }
        // dd($crm_count_data);
        ActivityTrailController::createActivityTrailLog(Auth::id(), 499);

        return view('admin.reports.crm_count');

    }

    public function crm_count_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 500);
        }
        // $crm_count_report = CRMCount::select('pending','new_launched','closed','date');

        // $datatable = Datatables::of($crm_count_report)
        // ->addColumn('remaining', function ($crm_count_report) {

        //     return ($crm_count_report->pending + $crm_count_report->new_launched);

        // })
        // ->addColumn('total', function ($crm_count_report) {

        //     return (($crm_count_report->pending + $crm_count_report->new_launched) - $crm_count_report->closed);

        // })
        // ->addColumn('closure_percent', function ($crm_count_report) {

        //     return number_format((($crm_count_report->closed / (($crm_count_report->pending + $crm_count_report->new_launched) - $crm_count_report->closed)) * 100), 2);

        // })
        // ->addColumn('remaining_percent', function ($crm_count_report) {

        //     return number_format(((($crm_count_report->pending + $crm_count_report->new_launched) / (($crm_count_report->pending + $crm_count_report->new_launched) - $crm_count_report->closed)) * 100), 2);

        // });


        // return $datatable->make(true);

        $from = $request->search_date_from;
        $to = $request->search_date_to;

        // $mode = $request->search_shipping_mode;
        $crm_count_data = array();

        if ($request->search_date_from && $request->search_date_to) {
            $crm_count_records = CRMCount::whereBetween('date', [$from, $to])->get();
            // dd($crm_count_records->get());
        } else {
            $crm_count_records = CRMCount::all();
        }

        $crm_count_records = $crm_count_records->groupBy(function ($date) {
            return Carbon::parse($date->date)->format('W');
        });
        
        // if ($request->search_date_from && $request->search_date_to) {
        //     $crm_count_records = CRMCount::whereBetween('date', [$from,$to])->groupBy(function($date) {
        //         return Carbon::parse($date->date)->format('W');
        //     });
        //     dd($crm_count_records->get());
        // }else{
        //     $crm_count_records = CRMCount::all()->groupBy(function($date) {
        //         return Carbon::parse($date->date)->format('W');
        //     });
        // }

        foreach ($crm_count_records as $key => $value) {
            $remaining_count_val = 0;
            if(count($value) > 0){
                $crm_count_data[$key]['count_days'] = count($value);
                $avg_closed = 0;
                $avg_remaining = 0;
                foreach ($value as $item) {
                    if((($item->pending + $item->new_launched) - $item->closed) < 0){
                        $remaining_count_val = 0;

                    }else{
                        $remaining_count_val = (($item->pending + $item->new_launched) - $item->closed);

                    }
                    if ($item->pending + $item->new_launched == 0) {
                        $avg_closed += 0;
                        $avg_remaining += 0;
                        $crm_count_data[$key]['data'][$item->id]['closure_percent'] = 0;
                        $crm_count_data[$key]['data'][$item->id]['remaining_percent'] = 0;
                    } else {
                        $avg_closed += ($item->closed / ($item->pending + $item->new_launched)) * 100;
                        $avg_remaining += ($remaining_count_val / ($item->pending + $item->new_launched)) * 100;
                        $crm_count_data[$key]['data'][$item->id]['closure_percent'] = number_format((($item->closed / ($item->pending + $item->new_launched)) * 100), 2);
                        $crm_count_data[$key]['data'][$item->id]['remaining_percent'] = number_format((($remaining_count_val / ($item->pending + $item->new_launched)) * 100), 2);
                    }
                    $crm_count_data[$key]['data'][$item->id]['id'] = $item->id;
                    $crm_count_data[$key]['data'][$item->id]['pending'] = $item->pending;
                    $crm_count_data[$key]['data'][$item->id]['new_launched'] = $item->new_launched;
                    $crm_count_data[$key]['data'][$item->id]['closed'] = $item->closed;
                    $crm_count_data[$key]['data'][$item->id]['date'] = $item->date;
                    $crm_count_data[$key]['data'][$item->id]['remaining'] = $remaining_count_val;
                    $crm_count_data[$key]['data'][$item->id]['total'] = ($item->pending + $item->new_launched);
                    $inner_pending = $item->pending;
                }
                $crm_count_data[$key]['weekly_close'] = number_format($avg_closed / $value->count(), 2);
                $crm_count_data[$key]['weekly_remaining'] = number_format($avg_remaining / $value->count(), 2);
            }
        }
        return $crm_count_data;
    }

    public function debriefing_agent_report()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 504);

        return view('admin.reports.debriefing_agent_report');

    }

    public function debriefing_agent_report_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 505);
        }

        $data = AgentDay::leftjoin('admins as agent', 'agent.id', 'agent_days.agent_id')
            ->select(['agent.id as agent_id', 'agent.name as agent_name', 'agent_days.date as date', 'agent_days.id as day_id', 'agent_days.auto_close as auto_close', 'agent_days.status as status']);

        $datatables = Datatables::of($data)
            ->addColumn('assigned_calls_excel', function ($calls) {

                $next_time = Carbon::createFromFormat('Y-m-d', $calls->date)->endOfDay()->toDateTimeString();
                $prev_time = Carbon::createFromFormat('Y-m-d', $calls->date)->startOfDay()->toDateTimeString();

                return AgentCallMonitoring::where('agent_id', $calls->agent_id)
                    ->where('created_at', '>=', $prev_time)
                    ->where('created_at', '<=', $next_time)
                    ->count();
            })
            ->addColumn('completed_calls_excel', function ($calls) {
                $next_time = Carbon::createFromFormat('Y-m-d', $calls->date)->endOfDay()->toDateTimeString();
                $prev_time = Carbon::createFromFormat('Y-m-d', $calls->date)->startOfDay()->toDateTimeString();

                return AgentCallMonitoring::where([['agent_id', $calls->agent_id], ['completed', 1]])
                    ->where('created_at', '>=', $prev_time)
                    ->where('created_at', '<=', $next_time)
                    ->count();
            })
            ->addColumn('assigned_calls', function ($calls) {
                $next_time = Carbon::createFromFormat('Y-m-d', $calls->date)->endOfDay()->toDateTimeString();
                $prev_time = Carbon::createFromFormat('Y-m-d', $calls->date)->startOfDay()->toDateTimeString();

                $count = AgentCallMonitoring::where('agent_id', $calls->agent_id)
                    ->whereBetween('created_at', [$prev_time, $next_time])
                    ->count();

                if ($count != 0) {
                    $count_cell = '<div><button class="btn btn-sm btn-outline-info align-middle mb-1">' . $count . '</button></div><h4 class="warning">100%</h4>';

                    return $count_cell;
                } else {
                    return 0;
                }
            })
            ->addColumn('completed_calls', function ($calls) {
                $next_time = Carbon::createFromFormat('Y-m-d', $calls->date)->endOfDay()->toDateTimeString();
                $prev_time = Carbon::createFromFormat('Y-m-d', $calls->date)->startOfDay()->toDateTimeString();

                $total_count = AgentCallMonitoring::where('agent_id', $calls->agent_id)
                    ->whereBetween('created_at', [$prev_time, $next_time])
                    ->count();
                $count = AgentCallMonitoring::where([['agent_id', $calls->agent_id], ['completed', 1]])
                    ->whereBetween('created_at', [$prev_time, $next_time])
                    ->count();
                if ($count != 0) {
                    $count_cell = '<div><button class="btn btn-sm btn-outline-info align-middle mb-1">' . $count . '</button></div><h4 class="success">' . round(($count / $total_count) * 100, 2) . '%</h4>';
                    return $count_cell;
                } else {
                    return 0;
                }
            })
            ->addColumn('live_hours', function ($calls) {
                $start = AgentDayLog::where('agent_day_id', $calls->day_id)->where('status', 1)->orderBy('id', 'asc')->first()->start;
                $end = AgentDayLog::where('agent_day_id', $calls->day_id)->where('status', 1)->orderBy('id', 'desc')->first()->end;
                $closed = "";
                if ($calls->status == 3) {
                    $closed = ($calls->auto_close == 1) ? " (Auto Closed)" : " (Self Closed)";
                }
                if ($end == null) {
                    return Carbon::createFromFormat('H:i:s', $start)->format("h:i A") . " - *" . $closed;
                } else {
                    return Carbon::createFromFormat('H:i:s', $start)->format("h:i A") . " - " . Carbon::createFromFormat('H:i:s', $end)->format("h:i A") . $closed;
                }


            })
            ->addColumn('break_hours', function ($calls) {
                $logs = AgentDayLog::where('agent_day_id', $calls->day_id)->where('status', 2)->get();
                $break = 0;
                foreach ($logs as $log) {
                    if ($log->end != null) {
                        $start = Carbon::parse($log->start);
                        $end = Carbon::parse($log->end);
                        $difference = $start->diffInSeconds($end);
                    } else {
                        $difference = 0;
                    }
                    $break += $difference;
                }

                return round($break / 60, 0) . ' Minute(s)';
            });

        return $datatables->make(true);
    }

    public function crm_special_approval_index(){
        ActivityTrailController::createActivityTrailLog(Auth::id(),513);
        return view('admin.reports.crm_special_approval');
    }

    public function crm_special_approval_list(Request $request){
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),514);
        }
        $crm = DB::connection('reports')->table('crm_requests')->leftjoin('shipments as s','s.id','=','crm_requests.shipment_id')
            ->leftjoin('special_approval_requests as sar', 'sar.crm_request_id' , '=', 'crm_requests.id')
            ->leftjoin('admins as sarapproveby' ,'sarapproveby.id', '=' , 'sar.admin_id')
            ->leftjoin('admins as sarrequestedby' ,'sarrequestedby.id', '=' , 'sar.requested_by')
            ->leftjoin('admin_roles as ar' ,'ar.id', '=' , 'sarapproveby.role_id')
            ->leftjoin('admin_departments as ad' ,'ad.id', '=' , 'ar.department_id')
            ->leftjoin('adjustment_logs as adjustment', function ($join) {
                $join->on('adjustment.shipment_id', '=', 'crm_requests.shipment_id')
                    ->where('adjustment.created_at','=',DB::raw('(select max(created_at) from adjustment_logs where adjustment_logs.shipment_id = crm_requests.shipment_id and adjustment_logs.adjustment_type_id IN (4,6,7,8,9,10,11) )'));
            })
            ->select('crm_requests.id as request_number', 's.tracking_number as tracking_number','s.amount as cod_amount','adjustment.adjustment_amount as adjusted_amount', 'sarrequestedby.name as requested_by', 'sar.created_at as requested_date','sarapproveby.name as approved_by','ar.name as designation','ad.name as department','sar.approved_date as approved_at','sar.adjusted_percentage as adjusted_percentage','sar.status as status')
            ->where('sar.status',1);

        $datatable = Datatables::of($crm)
            ->editColumn('request_number', function ($crm_request) {
                return str_pad($crm_request->request_number, 6, '0', STR_PAD_LEFT);
            })
            ->addColumn('id_padded_link', function ($crm_request) {
                return '<u><a href=' . route('admin.crm.request.details', ['id' => $crm_request->request_number]) . ' target="_blank">' . str_pad($crm_request->request_number, 6, '0', STR_PAD_LEFT). '</a></u>';
            })
            ->addColumn('tracking_number_link', function ($crm_request) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$crm_request->tracking_number' class='tracking' target='_blank'>$crm_request->tracking_number</a></u>";
            })
            ->editColumn('approved_by', function ($crm_request) {
                if($crm_request->adjusted_percentage == null){
                    return '';
                }else{
                    return $crm_request->approved_by;
                }
            });
        if($tracking = $request->get('search_tracking_no')){
            $tracking_numbers = explode(',', $tracking);
            $datatable->whereIn('s.tracking_number', $tracking_numbers);
        }
        if($rnumber = $request->get('search_request_number')){
            $rnumber = explode(',',$rnumber);
            $datatable->whereIn('crm_requests.id', $rnumber);
        }
        if ($request->get('search_from') && $request->get('search_to')) {
            $from = $request->get('search_from');
            $to = $request->get('search_to');
            $datatable->whereBetween('sar.created_at', [$from,$to]);
        }

        return $datatable->make(true);
    }

    public function rider_unresponsive_report_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(),523);
        return view('admin.reports.rider_unresponsive_report');
    }

    public function rider_unresponsive_report_list(Request $request){
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),524);
        }
        $data = DB::connection('reports')->table('rider_unresponsive_statuses')
            ->leftjoin('riders as r','r.id','=','rider_unresponsive_statuses.rider_id')
            ->leftjoin('admins as a','a.id','=','rider_unresponsive_statuses.admin_id')
            ->leftjoin('cities as c', 'c.id', '=', 'r.city_id')
            ->select('r.name as rider','a.name as admin','rider_unresponsive_statuses.status as status','rider_unresponsive_statuses.created_at','rider_unresponsive_statuses.note_id','c.name as city_name');

        $datatable = Datatables::of($data)
            ->addColumn('display_status', function ($data) {
                if($data->status == 1)
                {
                    return "Unresponsive";
                }
                else{
                    return "Powered Off";
                }
            })
            ->addColumn('display_note_id', function ($data) {
                if ($data->note_id == null) {
                    return "-";
                } else {
                    return str_pad($data->note_id, 6, '0', STR_PAD_LEFT);
                }
            });
        if($status = $request->get('search_status')){
            $datatable->where('rider_unresponsive_statuses.status', $status);
        }
        if ($request->get('search_from') && $request->get('search_to')) {
            $from = $request->get('search_from');
            $to = $request->get('search_to');
            $datatable->whereBetween('rider_unresponsive_statuses.created_at', [$from,$to]);
        }

        return $datatable->make(true);
    }

public function employee_confirmation_index(){
    ActivityTrailController::createActivityTrailLog(Auth::id(),576);
    return view('admin.reports.employee_confirmation');
}

public function employee_confirmation_list(Request $request){
    if($request->get('excel') && $request->get('excel') == true)
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(),577);
    }
    $employee_confirmation = DB::connection('reports')->table('employee_confirmations')
    ->join('employees as a', 'a.id', 'employee_confirmations.employee_id')
    ->leftjoin('employee_designations as ed', 'ed.id', 'a.designation_id')
    ->leftjoin('admin_departments as ad', 'ad.id', 'a.department_id')
    ->join('employee_confirmation_statuses as sn', 'sn.id', 'employee_confirmations.status')
    ->leftjoin('zones as ez', 'ez.id', '=', 'a.zone_id')
    ->select('a.name as name', 'a.trax_id as trax_id', 'ed.name as designation', 'ad.name as department', 'ad.id as department_id', 'sn.name as status', 'sn.id as status_id', 'employee_confirmations.employee_id as employee_id', 'employee_confirmations.id as id', 'a.joining_date as joining_date', 'employee_confirmations.probation_end_date as probation_end_date', 'employee_confirmations.approve_reason as approve_reason', 'employee_confirmations.reject_reason as reject_reason', 'employee_confirmations.approve_by_lm_at as approve_by_lm_at', 'employee_confirmations.approve_by_hod_at as approve_by_hod_at', 'employee_confirmations.approve_by_hr_at as approve_by_hr_at','a.confirmation_status as confirmation_status','ad.department_head_id as department_head','a.city_id','employee_confirmations.created_at','ez.name as zone');

    if ($request->get('search_from') && $request->get('search_to')) {
        $from = $request->get('search_from');
        $to = $request->get('search_to');
        $employee_confirmation->whereBetween('employee_confirmations.created_at', [$from, $to]);
    }

    $datatable = Datatables::of($employee_confirmation)
    ->addColumn('employee_hub', function ($user) {
        return City::where('id', $user->city_id)->first()->hub_city->name ?? "";
    });


    return $datatable->make(true);
    }
    public function crm_agent_wise_report_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(),574);

        // $agents = CrmAgent::join('admins as ad', 'ad.id', '=', 'crm_agents.admin_id')
        //     ->select('crm_agents.id as id', 'ad.name as name')
        //     ->get();

        $agents = AdminRole::leftjoin('admins as a', 'a.role_id', '=', 'admin_roles.id')
            ->where('admin_roles.department_id',3)->get();

        return view('admin.reports.crm_agent_wise_report.crm_agent_wise_report')->with(['agents' => $agents]);
    }

    public  function crm_agent_wise_report_list(Request $request)
    {
         if ($request->get('excel') && $request->get('excel') == true) {
             ActivityTrailController::createActivityTrailLog(Auth::id(), 575);
         }

        $from = $request->search_date_from;
        $agent_id = $request->agent_id;

        $filter_data =  CrmRequestAgentHistory::join('admins as ad','ad.id','crm_request_agent_histories.agent_id')
        ->join('crm_requests as crm_req','crm_req.id', 'crm_request_agent_histories.crm_request_id')
        ->join('crm_request_status_histories as crmsh','crmsh.crm_request_id', 'crm_request_agent_histories.crm_request_id')
        ->select(
            'ad.id',
            'ad.name',
            DB::raw('sum(case when crmsh.status_id in (2) then 1 else 0 end) AS pending'),
            DB::raw('sum(case when crmsh.status_id in (1,5) then 1 else 0 end) AS new_assign'),
            DB::raw('sum(case when crmsh.status_id in (1,2,5) then 1 else 0 end) AS total'),
            DB::raw('sum(case when crmsh.status_id in (3,4) then 1 else 0 end) AS closed')
            )
        ->groupBy('ad.name')
        ->where(function ($query) use($from){
            if($from != null || $from != '' )
            {
                $query->where('crmsh.created_at','like', $from.'%');
            }
        })
        ->where(function ($query) use($agent_id){
            if($agent_id != null || $agent_id != '')
            {
                $query->where('ad.id', $agent_id);
            }
        })
        ->orderBy('ad.name')
        ->get();
        return $filter_data;
        
    }
}

