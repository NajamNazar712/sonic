<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Admin\MonthClosing;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Admins\ActivityTrailController;

class AdminMonthClosingReportsController extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function month_closing_individual_index(){
        ActivityTrailController::createActivityTrailLog(Auth::id(),223);
        return view('admin.reports.month_closing.individual');
    }

    public function month_closing_individual_list(Request $request){
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),224);
        }
        $month_closing = MonthClosing::join('shipments', 'shipments.id', '=', 'month_closings.shipment_id')
            ->join('users as u', 'shipments.user_id', '=', 'u.id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->join('cities as h' ,'dc.hub_id', '=' , 'h.id')
            ->leftJoin('shipments_journey', function ($join) {
                $join->on('shipments_journey.shipment_id', '=', 'shipments.id')
                    ->where('shipments_journey.id','=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = month_closings.shipment_id)'));
            })
            ->leftJoin('shipment_status as ss', 'ss.id', '=', 'shipments_journey.shipper_status_id')
            ->leftjoin('month_closing_statuses as mcs', 'mcs.id', '=', 'month_closings.status_id')
            ->leftjoin('month_closing_types as mct', 'mct.id', 'month_closings.closing_type_id')
            ->leftJoin('crm_requests as cr', function ($join) {
                $join->on('cr.shipment_id', '=', 'month_closings.shipment_id')
                    ->where('cr.id','=',
                        DB::raw('(select max(id) from crm_requests where crm_requests.shipment_id = month_closings.shipment_id)'));
            })
            ->leftjoin('crm_request_case_nature_types as crn','crn.id','=','cr.case_nature_type_id')
            ->join('month_closing_responsibles as mcr', 'mcr.month_closing_id', '=', 'month_closings.id')
            ->leftJoin('admins as rp', function ($join) {
                $join->on('rp.id', '=', 'mcr.responsible_person_id')
                    ->where('mcr.admin','=',
                        DB::raw(1));
            })
            ->leftJoin('riders as r', function ($join) {
                $join->on('r.id', '=', 'mcr.responsible_person_id')
                    ->where('mcr.admin','=',
                        DB::raw(0));
            })
//            ->leftjoin('admins as rp', 'rp.id', '=', 'mcr.responsible_person_id')
//            ->leftjoin('riders as r', 'r.id', '=', 'mcr.responsible_person_id')
            ->select('shipments.id as shipment_id','shipments.tracking_number as tracking_number_link','shipments.tracking_number','oc.name as origin','dc.name as destination','h.name as hub','shipments.consignee_name','shipments.consignee_phone_number_1 as consignee_phone','shipments.amount as cod_amount','u.name as shipper', 'month_closings.id as month_closing_id','month_closings.remarks','mcs.name as closing_status', 'month_closings.status_id as month_closing_status_id', 'cr.id as claim_id', 'cr.id as claim_id_link', 'crn.type as claim_type','ss.name as current_status','mct.name as closing_type','shipments.consignee_address', 'mcr.admin as admin_check','rp.name as responsible_person','r.name as rider_responsible_person')
            ->whereIn('month_closings.status_id', [2,3]);

        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $month_closing->whereBetween('month_closings.closing_updated_at', [$from,$to]);
        }

        $datatable = Datatables::of($month_closing)
            ->editColumn('tracking_number_link',function ($shipments){
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->editColumn('claim_id_link', function ($shipments) {
                if($shipments->claim_id != null){
                    return '<u><a href=' . route('admin.crm.request.details', ['id' => $shipments->claim_id]) . '  target="_blank">' . str_pad($shipments->claim_id, 6, '0', STR_PAD_LEFT). '</a></u>';
                }
                else{
                    return '-';
                }

            })
            ->addColumn('category', function($shipments){
                if($shipments->admin_check == 1){
                    return 'Admin';
                }
                else{
                    return 'Rider';
                }
            })
            ->editColumn('responsible_person', function ($shipments) {
                if($shipments->admin_check == 1){
                    return $shipments->responsible_person;
                }
                else{
                    return $shipments->rider_responsible_person;
                }
            })
            ->addColumn('shipment_remarks',function ($shipments){
                $remark = '<input class="form-control form-control-sm" value="'.$shipments->remarks.'" />';
                return $remark;
            });
        return $datatable->make(true);
    }

    public function month_closing_pivot_index(){
        ActivityTrailController::createActivityTrailLog(Auth::id(),225);
        return view('admin.reports.month_closing.pivot');
    }

    public function month_closing_pivot_list(Request $request){
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),226);
        }
        $month_closing = MonthClosing::join('shipments', 'shipments.id', '=', 'month_closings.shipment_id')
            ->join('users as u', 'shipments.user_id', '=', 'u.id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->join('cities as h' ,'dc.hub_id', '=' , 'h.id')
            ->leftJoin('shipments_journey', function ($join) {
                $join->on('shipments_journey.shipment_id', '=', 'shipments.id')
                    ->where('shipments_journey.id','=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = month_closings.shipment_id)'));
            })
            ->leftJoin('shipment_status as ss', 'ss.id', '=', 'shipments_journey.shipper_status_id')
            ->leftjoin('month_closing_statuses as mcs', 'mcs.id', '=', 'month_closings.status_id')
            ->leftjoin('month_closing_types as mct', 'mct.id', 'month_closings.closing_type_id')
            ->leftJoin('crm_requests as cr', function ($join) {
                $join->on('cr.shipment_id', '=', 'month_closings.shipment_id')
                    ->where('cr.id','=',
                        DB::raw('(select max(id) from crm_requests where crm_requests.shipment_id = month_closings.shipment_id)'));
            })
            ->leftjoin('crm_request_case_nature_types as crn','crn.id','=','cr.case_nature_type_id')
            ->join('month_closing_responsibles as mcr', 'mcr.month_closing_id', '=', 'month_closings.id')
            ->leftJoin('admins as rp', function ($join) {
                $join->on('rp.id', '=', 'mcr.responsible_person_id')
                    ->where('mcr.admin','=',
                        DB::raw(1));
            })
            ->leftJoin('riders as r', function ($join) {
                $join->on('r.id', '=', 'mcr.responsible_person_id')
                    ->where('mcr.admin','=',
                        DB::raw(0));
            })
            ->select('shipments.id as shipment_id','shipments.tracking_number as tracking_number_link','shipments.tracking_number','oc.name as origin','dc.name as destination','h.name as hub','shipments.consignee_name','shipments.consignee_phone_number_1 as consignee_phone','shipments.amount as cod_amount','u.name as shipper', 'month_closings.id as month_closing_id','month_closings.remarks','mcs.name as closing_status', 'month_closings.status_id as month_closing_status_id', 'cr.id as claim_id', 'cr.id as claim_id_link', 'crn.type as claim_type','ss.name as current_status','mct.name as closing_type','shipments.consignee_address', 'mcr.admin as admin_check','rp.name as responsible_person','r.name as rider_responsible_person', 'rp.id as responsible_person_id', 'r.id as rider_responsible_person_id', DB::raw('(SELECT COUNT(mr.id) FROM month_closing_responsibles AS mr WHERE mr.responsible_person_id = mcr.responsible_person_id) AS shipment_count'))
            ->whereIn('month_closings.status_id', [2,3])
            ->groupBy('mcr.responsible_person_id');

        $datatable = Datatables::of($month_closing)
            ->editColumn('tracking_number_link',function ($shipments){
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->editColumn('claim_id_link', function ($shipments) {
                if($shipments->claim_id != null){
                    return '<u><a href=' . route('admin.crm.request.details', ['id' => $shipments->claim_id]) . '  target="_blank">' . str_pad($shipments->claim_id, 6, '0', STR_PAD_LEFT). '</a></u>';
                }
                else{
                    return '-';
                }

            })
            ->addColumn('category', function($shipments){
                if($shipments->admin_check == 1){
                    return 'Admin';
                }
                else{
                    return 'Rider';
                }
            })
            ->editColumn('responsible_person', function ($shipments) {
                if($shipments->admin_check == 1){
                    return $shipments->responsible_person;
                }
                else{
                    return $shipments->rider_responsible_person;
                }
            })
            ->addColumn('shipment_count_btn', function ($shipments) {
                if($shipments->shipment_count > 0){
                    return '<button class="btn btn-sm btn-outline-info align-middle shipment_count_popup"><span class="align-middle">' . $shipments->shipment_count . '</span></button>';
                }
                else{
                    return '-';
                }

            })
            ->addColumn('shipment_remarks',function ($shipments){
                $remark = '<input class="form-control form-control-sm" value="'.$shipments->remarks.'" />';
                return $remark;
            });
        return $datatable->make(true);
    }
}
