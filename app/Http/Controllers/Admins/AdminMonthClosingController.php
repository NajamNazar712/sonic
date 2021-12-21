<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\CargoManifestBagJourneyController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\CargoManifest\CargoManifest;
use App\Http\Models\Admin\CargoManifest\CargoManifestBag;
use App\Http\Models\Admin\CargoManifest\CargoManifestBagShipments;
use App\Http\Models\Admin\CargoManifest\ManifestBag;
use App\Http\Models\Admin\DeliveryNote;
use App\Http\Models\Admin\DeliveryNoteShipment;
use App\Http\Models\Admin\MasterCargo\Bag;
use App\Http\Models\Admin\MasterCargo\BagShipment;
use App\Http\Models\Admin\MonthClosing;
use App\Http\Models\Admin\MonthClosingResponsible;
use App\Http\Models\Admin\MonthClosingStatus;
use App\Http\Models\Admin\MonthClosingType;
use App\Http\Models\Admin\ReturnNote;
use App\Http\Models\Admin\ReturnNoteShipment;
use App\Http\Models\CargoConsignment;
use App\Http\Models\CargoConsignmentShipment;
use App\Http\Models\Rider;
use App\Http\Models\ShipmentsJourney;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\ShipmentStatus;
use App\Http\Models\ShippingMode;
use App\Http\Models\BookingType;
use App\Http\Models\Shipment;

use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admins\ActivityTrailController;

class AdminMonthClosingController extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function return_confirm_shipment(Request $request){
        $shipment_ids = $request->shipment_ids;
        $not_updated_shipments = array();
        $shipment_remarks = $request->remark;
        $untouched = false;
            if(!empty($shipment_ids)){

                foreach ($shipment_ids as $shipment){
                    $parcel = Shipment::find($shipment);
                    if($parcel){
                        $month_closing = MonthClosing::where('shipment_id', $parcel->id)->where('status_id', 3);
                        if($month_closing->exists()){
                            $month_closing = $month_closing->first();
                            MonthClosingResponsible::where('month_closing_id', $month_closing->id)->delete();
                            $month_closing->delete();
                            if (!$parcel->packaging_material_request) {
                                Shipment::where('id',$shipment)->update(['shipper_status_id'=>20,'consignee_status_id'=>20]);

                                NotificationsController::send(15, 0, $shipment);
                                NotificationsController::send(16, 0, $shipment);

                                ShipmentChargesController::return($shipment);

                                AdminFinanceController::add_payment($shipment, 1);
                                ShipmentsJourneyController::add($shipment, 20, 20, NULL, $shipment_remarks[$shipment], NULL, Auth::id());
                            }
                            else {
                                Shipment::where('id',$shipment)->update(['shipper_status_id'=>17,'consignee_status_id'=>17]);
                                ShipmentsJourneyController::add($shipment, 17, 17, NULL, $shipment_remarks[$shipment], NULL, Auth::id());
    
                                NotificationsController::send(15, 0, $shipment);
                                NotificationsController::send(16, 0, $shipment);
                            }
                        }else{
                            $not_updated_shipments[] = $shipment;

                        }
                    }else{
                        $not_updated_shipments[] = $shipment;
                    }
                }
            }

            if(count($not_updated_shipments) > 0){
                $untouched = true;
            }

            return response()->json(['status'=>1,'success'=>"Shipment successfully updated as ( Return Confirm )", 'untouched_shipments' => $not_updated_shipments, 'untouched' => $untouched]);
    }

     public function return_reattempt_shipment(Request $request){
         $shipment_ids = $request->shipment_ids;
         $shipment_remarks = $request->remark;
         $not_updated_shipments = array();
         $untouched = false;
            if(!empty($shipment_ids)){
                foreach ($shipment_ids as $shipment){
                    $parcel = Shipment::find($shipment);
                    if($parcel){
                        $month_closing = MonthClosing::where('shipment_id', $parcel->id)->where('status_id', 3);
                        if($month_closing->exists()){
                            $month_closing = $month_closing->first();
                            MonthClosingResponsible::where('month_closing_id', $month_closing->id)->delete();
                            $month_closing->delete();

                            Shipment::where('id',$shipment)->update(['shipper_status_id'=>13,'consignee_status_id'=>13]);
                            ShipmentsJourneyController::add($shipment, 13, 13, NULL, $shipment_remarks[$shipment], NULL, Auth::id());
                            NotificationsController::send(15, 0, $shipment);
                            NotificationsController::send(16, 0, $shipment);
                        }
                        else{
                            $not_updated_shipments[] = $shipment;
                        }
                    }
                    else{
                        $not_updated_shipments[] = $shipment;
                    }
                }
            }
         if(count($not_updated_shipments) > 0){
             $untouched = true;
         }

             return response()->json(['status'=>1,'success'=>"Shipment successfully updated as ( Re-Attempt )", 'untouched_shipments' => $not_updated_shipments, 'untouched' => $untouched]);

     }

    public function pending_index(){
        ActivityTrailController::createActivityTrailLog(Auth::id(),45);
        $closing_types = MonthClosingType::all();
        $admins = Admin::where('status', 1)->where('role_id', '!=', 1)->with(['role.department'])->get();
        $riders = Rider::where('status',1)->get();
        return view('admin.month_closing.pending')->with(['admins' => $admins, 'riders' => $riders, 'closing_types' => $closing_types]);
    }

    public function pending_list(Request $request){
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),105);
        }
//        $status_not_allowed = [1, 5, 6, 14, 17, 25, 31, 38, 51, 53];
        $date = Carbon::now()->startOfMonth()->subMonth()->addDays(20)->toDateString();

        $month_closing_status = [3, 5, 13, 18, 20, 21, 22, 23, 24, 26, 27, 28, 29, 30, 32, 33, 34, 35, 37, 44, 45, 46, 47, 48, 60];
        $shipments = Shipment::join('users as u', 'shipments.user_id', '=', 'u.id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->join('cities as h' ,'dc.hub_id', '=' , 'h.id')
            ->leftJoin('shipments_journey', function ($join) {
                $join->on('shipments_journey.shipment_id', '=', 'shipments.id')
                    ->where('shipments_journey.id','=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id)'));
            })
            ->leftjoin('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where('sj.id', '=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)'));
            })
            ->leftJoin('shipment_status as ss', 'ss.id', '=', 'shipments_journey.shipper_status_id')
            ->leftJoin('month_closings as mc', function ($join){
                $join->on('mc.shipment_id', '=', 'shipments.id')
                    ->where('mc.id', '=',
                        DB::raw('(select max(id) from month_closings where month_closings.shipment_id = shipments.id)'));
            })
//            ->leftjoin('month_closings as mc', 'mc.shipment_id', '=', 'shipments.id')
            ->leftjoin('month_closing_statuses as mcs', 'mcs.id', '=', 'mc.status_id')
            ->leftjoin('month_closing_types as mct', 'mct.id', 'mc.closing_type_id')
            ->leftJoin('crm_requests as cr', function ($join) {
                $join->on('cr.shipment_id', '=', 'shipments.id')
                    ->where('cr.id','=',
                        DB::raw('(select max(id) from crm_requests where crm_requests.shipment_id = shipments.id)'));
            })
            ->leftjoin('crm_request_case_nature_types as crn','crn.id','=','cr.case_nature_type_id')
            ->select('shipments.id as shipment_id','shipments.tracking_number as tracking_number_link','shipments.tracking_number','oc.name as origin','dc.name as destination','h.name as hub','shipments.consignee_name','shipments.consignee_phone_number_1 as consignee_phone','shipments.amount as cod_amount','u.name as shipper', 'mc.id as month_closing_id','mc.remarks','mcs.name as closing_status', 'mc.status_id as month_closing_status_id', 'cr.id as claim_id', 'cr.id as claim_id_link', 'crn.type as claim_type','ss.name as current_status','mct.name as closing_type','shipments.consignee_address', 'shipments.shipper_status_id')
            ->whereIn('shipments.shipper_status_id', $month_closing_status)
            ->where(function($query) {
                $query->whereNull('mc.status_id')
                    ->orWhereNotIn('mc.status_id', [2, 3]);
            })
            ->groupBy('shipments.id');

        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $shipments = $shipments->where(function ($query) use ($from, $to) {
                $query->where(function ($sub_query) use ($from,$to) {
                    $sub_query->whereBetween('sj.created_at', [$from,$to]);
                })
                    ->orWhere(function ($sub_query) use ($from,$to) {
                        $sub_query->whereBetween('shipments.created_at', [$from,$to]);
                    });
            });
        }
        else{
            $shipments = $shipments->where(function ($query) use ($date) {
                $query->where(function ($sub_query) use ($date) {
                    $sub_query->where('sj.created_at', '<', $date);
                })
                    ->orWhere(function ($sub_query) use ($date) {
                        $sub_query->where('shipments.created_at', '<', $date);
                    });
            });
        }
        if ($tracking_numbers = $request->get('tracking_numbers')) {
            $shipments->whereIn('shipments.tracking_number', explode(',', $tracking_numbers));
        }
        if (session('role_id') != 1) {
            $shipments->whereIn('dc.hub_id', session('hubs'));
        }

        $datatable = Datatables::of($shipments)
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
            ->addColumn('shipment_remarks',function ($shipments){
                $remark = '<input class="form-control form-control-sm" value="'.$shipments->remarks.'" />';
                return $remark;
            })
            ->addColumn('action', function($shipments) {
                $assign_responsible = '<button type="button" class="dropdown-item assign_responsible"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-user-plus"></i></div><div class="col-9 offset-1">Assign Responsible</div></button>';
                $edit_assign_responsible = '<button type="button" class="dropdown-item edit_responsible"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-user-plus"></i></div><div class="col-9 offset-1">Edit Responsible</div></button>';
                if (session('role_id') == 1 || in_array(412, session('permissions'))) {
                    $dropdown = '
                    <div class="btn-group">
                      <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                      <div class="dropdown-menu dropdown-menu-sm">';
                    if($shipments->month_closing_id == null){
                        $dropdown .= $assign_responsible;
                    }
                    else{
                        $dropdown = '';
                    }
                }
                else{
                    $dropdown = '';
                }


                return $dropdown;

            });
        return $datatable->make(true);

    }
    public function assign_responsible_submit(Request $request){
        if($request->has('responsible_persons')){
            $responsible_persons = $request->responsible_persons;
        }
        else{
            $responsible_persons = $request->rider_responsible_persons;
        }
        $shipment_ids = explode(',', $request->shipment_ids);
        foreach ($shipment_ids as $shipment_id) {
            $month_closing = MonthClosing::where('shipment_id', $shipment_id);
            if(!$month_closing->exists()){
                if(count($responsible_persons) > 0){
                    $month_closing = new MonthClosing();
                    $month_closing->shipment_id = $shipment_id;
                    $month_closing->status_id = 1;
                    $month_closing->added_by = Auth::id();
                    $month_closing->remarks = $request->remarks;
                    $month_closing->save();
                    $month_closing_id = $month_closing->id;
                    $individual_flag = FALSE;
                    if($request->has('deduct_switch')){
                        $individual_flag = TRUE;
                    }
                    $user_flag = FALSE;
                    if($request->has('user_switch')){
                        $user_flag = TRUE;
                    }

                    foreach ($responsible_persons as $person_id){
                        $month_closing_responsible = new MonthClosingResponsible();
                        $month_closing_responsible->month_closing_id = $month_closing_id;
                        if($user_flag){
                            $month_closing_responsible->admin = 0;
                        }else{
                            $month_closing_responsible->admin = 1;
                        }
                        $month_closing_responsible->responsible_person_id = $person_id;
                        $month_closing_responsible->added_by = Auth::id();
                        if($individual_flag){
                            $month_closing_responsible->amount = $request->deduct_amount_individual[$person_id];
                        }else{
                            $month_closing_responsible->amount = $request->deduct_amount;
                        }
                        $month_closing_responsible->save();
                    }
                }
            }
        }
        return redirect()->back()->with('success', 'Responsible Person(s) updated successfully!');
    }

    public function closing_status_submit(Request $request){

        $shipment_ids = explode(',', $request->shipment_ids);
        $closing_type = $request->closing_type_id;

        if(count($shipment_ids) > 0){
            MonthClosing::whereIn('shipment_id', $shipment_ids)->where('status_id', 1)->whereNull('closing_type_id')->update(['closing_type_id' => $closing_type,'updated_by' => Auth::id()]);
            return redirect()->back()->with('success', 'Closing Type updated successfully!');
        }
        return redirect()->back()->with('error' , 'No shipments selected!');
    }

    public function month_closing_resolved(Request $request){
        $shipment_ids = $request->shipment_ids;

        if(count($shipment_ids) > 0){
            MonthClosing::whereIn('shipment_id', $shipment_ids)->where('status_id', 1)->whereNotNull('closing_type_id')->update(['status_id' => 2,'closing_updated_at' => Carbon::now(), 'updated_by' => Auth::id()]);
            return response()->json(['status' => 0, 'success' => 'Closing Status Resolved updated successfully!']);
        }
        return response()->json(['status' => 1, 'error' => 'No shipment(s) selected!']);

    }

    public function resolved_index(Request $request){
        ActivityTrailController::createActivityTrailLog(Auth::id(),46);
        return view('admin.month_closing.resolved');
    }
    public function resolved_list(Request $request){
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),106);
        }
        $month_closing = MonthClosing::leftjoin('shipments', 'shipments.id', '=', 'month_closings.shipment_id')
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
            ->select('shipments.id as shipment_id','shipments.tracking_number as tracking_number_link','shipments.tracking_number','oc.name as origin','dc.name as destination','h.name as hub','shipments.consignee_name','shipments.consignee_phone_number_1 as consignee_phone','shipments.amount as cod_amount','u.name as shipper', 'month_closings.id as month_closing_id','month_closings.remarks','mcs.name as closing_status', 'month_closings.status_id as month_closing_status_id', 'cr.id as claim_id', 'cr.id as claim_id_link', 'crn.type as claim_type','ss.name as current_status','mct.name as closing_type','shipments.consignee_address','month_closings.closing_updated_at')
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
            ->addColumn('shipment_remarks',function ($shipments){
                $remark = '<input class="form-control form-control-sm" value="'.$shipments->remarks.'" />';
                return $remark;
            });
        return $datatable->make(true);

    }
    public function month_closing_closed(Request $request){
        $shipment_ids = $request->shipment_ids;
        $shipment_remarks = $request->remark;
        $date = Carbon::now();
        if(count($shipment_ids) > 0){
            $status_not_allowed = array(1, 5, 6, 14, 17, 25, 31, 38, 51, 53);
            $intransit_status_array = array(3, 21, 26, 32);
            $return_revert_statuses = array(20, 21, 22, 23, 24, 44, 47, 48);
            $return_note_statuses = array(23, 24, 28, 29, 34, 35, 44, 45,46, 47, 48, 60);
            $replacement_try_and_buy_statuses = array(26,27,28,29,30,32,33,34,35,36,37,45,46);
            $errors = array();
            $success = array();
            foreach ($shipment_ids as $shipment_id){
                $shipment_details = Shipment::find($shipment_id);
                if($shipment_details){

                    $journey=  ShipmentsJourney::where('shipment_id',$shipment_details->id)->latest('id')->first();
                    if($journey)
                    {
                        $verification = $journey->verification;
                        if($verification == 0)
                        {
                            continue;
                        }

                    }

                    if($shipment_details->shipper_status_id != 51){
                        $month_closing = MonthClosing::where('shipment_id', $shipment_id)->where('status_id', 2);
                        if($month_closing->exists()){
                            $month_closing = $month_closing->first();
                            $remarks = $shipment_remarks[$shipment_id];
                            if(!in_array($shipment_details->shipper_status_id, $status_not_allowed)){
                                if(in_array($shipment_details->shipper_status_id, [7, 8, 9, 10, 11, 12, 15, 18, 20, 30])) {
                                    $delivery_note_shipment = DeliveryNoteShipment::where('shipment_id', $shipment_details->id);
                                    if ($delivery_note_shipment->exists()) {
                                        $delivery_note_shipment = $delivery_note_shipment->max('delivery_note_id');
                                        $delivery = DeliveryNote::where('id' , $delivery_note_shipment)->where('status', 0)->exists();
                                        if($delivery){
                                            $errors[$shipment_details->tracking_number] = 'Shipment is in an Unverified Delivery Note';
                                        }
                                    }
                                }
                                if(in_array($shipment_details->shipper_status_id, $return_revert_statuses)){
                                    if($shipment_details->shipment_type == 1){
                                        AdminFinanceController::return_confirmed_revert($shipment_details->id, 13);
                                    }
                                }

                                if(in_array($shipment_details->shipper_status_id, $replacement_try_and_buy_statuses)){
                                    AdminFinanceController::replacement_or_try_and_buy_adjust_in_payment($shipment_details->id);
                                }
//                                if (in_array($shipment_details->shipper_status_id, $intransit_status_array )) {
//                                    $bag_shipment = BagShipment::where('shipment_id', $shipment_details->id);
//                                    if ($bag_shipment->exists()) {
//                                        $bag_shipment = $bag_shipment->max('bag_id');
//
//                                        $bag = Bag::find($bag_shipment);
//                                        $bag->shipment()->where('shipment_id', $shipment_details->id)->delete();
//                                        if (in_array($bag->status_id, [2, 3, 5, 6])) {
//                                            $shipments_count = $bag->shipments;
//                                            $shipment_weight = $bag->shipment_weight;
//                                            $shipments_count = $shipments_count - 1;
//                                            $bag->shipments = $shipments_count;
//                                            $bag->shipments_weight = $shipment_weight - $shipment_details->actual_weight;
////                                            if ($shipments_count == 0) {
////                                                $bag->status_id = 5;
////                                            }
//                                            $bag->save();
////                                            $shipment_details->shipper_status_id = 51;
////                                            $shipment_details->consignee_status_id = 51;
////                                            $shipment_details->save();
////                                            ShipmentsJourneyController::add($shipment_details->id, 51, 51, NULL, $remarks, NULL, Auth::id());
//                                            $success[$shipment_details->tracking_number] = 'Shipment is successfully added to Month Closing!';
//
////                                    return response()->json(['status' => 1, 'success' => 'Shipment is successfully added to Month Closing!']);
//                                        }
////                                        else if ($cargo->status_id == 4) {
////                                            $shipments_count = $cargo->shipments;
////                                            $shipments_received_count = $cargo->received_shipments;
////                                            $shipment_weight = $cargo->shipment_weight;
////                                            $shipments_count = $shipments_count - 1;
////                                            $cargo->shipments = $shipments_count;
////                                            $cargo->shipments_weight = $shipment_weight - $shipment_details->actual_weight;
////                                            if ($shipments_count == 0) {
////                                                $cargo->status_id = 5;
////                                            } else if ($shipments_count == $shipments_received_count) {
////                                                $cargo->status_id = 3;
////                                            }
////                                            $cargo->save();
//////                                            $shipment_details->shipper_status_id = 51;
//////                                            $shipment_details->consignee_status_id = 51;
//////                                            $shipment_details->save();
//////                                            ShipmentsJourneyController::add($shipment_details->id, 51, 51, NULL, $remarks, NULL, Auth::id());
////                                            $success[$shipment_details->tracking_number] = 'Shipment is successfully added to Month Closing!';
////
////                                        }
//                                    }
//
//                                }

                                if (in_array($shipment_details->shipper_status_id, $intransit_status_array )) {
                                    $bag_shipment = CargoManifestBagShipments::where('shipment_id', $shipment_details->id);
                                    if ($bag_shipment->exists()) {
                                        $bag_shipment = $bag_shipment->max('cargo_manifest_bag_id');

                                        $bag = CargoManifestBag::find($bag_shipment);
                                        $bag->shipment()->where('shipment_id', $shipment_details->id)->delete();

                                            $shipments_count = $bag->shipments;
                                            $shipment_weight = $bag->shipment_weight;
                                            $shipments_count = $shipments_count - 1;
                                            $bag->shipments = $shipments_count;
                                            $bag->shipments_weight = $shipment_weight - $shipment_details->actual_weight;
                                            if ($shipments_count == 0) {
                                                $bag->status_id = 10;
                                                CargoManifestBagJourneyController::add($bag->id,$bag->seal_number,10,Auth::id(),NULL,NULL);
                                            }
                                            else if($bag->shipments_count != 0 && $bag->short_received_shipments > 0 && in_array($bag->status_id, [7,8,9,10])) {
                                                $bag->status_id = 7;
                                                CargoManifestBagJourneyController::add($bag->id, $bag->seal_number, 7, Auth::id(), NULL, NULL);
                                            }
                                            else {
                                                $bag->status_id = 8;
                                                CargoManifestBagJourneyController::add($bag->id, $bag->seal_number, 8, Auth::id(), NULL, NULL);
                                            }
                                            $bag->save();

                                            $cargo_bag = ManifestBag::where('cargo_manifest_bag_id',$bag->id)->first();
                                            if($cargo_bag){
                                                $cargo = CargoManifest::find($cargo_bag->cargo_manifest_id);
                                                if($cargo){
                                                    $total_bags = $cargo->bags;
                                                    $cargo_total_shipments = $cargo->shipments;
                                                    if($bag->shipments_count == 0){
                                                        $cargo->bags = $total_bags - 1;
                                                    }
                                                    $cargo->shipments = $cargo_total_shipments - 1;
                                                    $cargo_weight = $cargo->bags_weight;
                                                    $cargo->actual_weight = $cargo_weight - $shipment_details->actual_weight;
                                                    $cargo->save();
                                                }
                                            }
                                            $success[$shipment_details->tracking_number] = 'Shipment is successfully added to Month Closing!';

                                    }
                                }

                                if(in_array($shipment_details->shipper_status_id,$return_note_statuses)){
                                    $return_note_shipments_details = ReturnNoteShipment::where('shipment_id', $shipment_details->id);
                                    if($return_note_shipments_details->exists()){
                                        $return_note_shipments_details = $return_note_shipments_details->get();
                                        foreach ($return_note_shipments_details as $return_note_shipments){
                                            $return_note = ReturnNote::find($return_note_shipments->return_note_id);
                                            if($return_note){
                                                if($return_note->status == 0){
                                                    ReturnNoteShipment::where('return_note_id', $return_note->id)->where('shipment_id', $return_note_shipments->shipment_id)->delete();
                                                    $return_note->shipments_count = $return_note->shipments_count - 1;
                                                    if(ReturnNoteShipment::where('return_note_id', $return_note_shipments->return_note_id)->where('status', 0)->count() == 0){
                                                        $return_note->status = 1;
                                                    }
                                                    $return_note->save();
                                                }
                                            }

                                        }

                                    }
                                }


                                $shipment_details->shipper_status_id = 51;
                                $shipment_details->consignee_status_id = 51;
                                $shipment_details->save();
                                ShipmentsJourneyController::add($shipment_details->id, 51, 51, NULL, $remarks, NULL, Auth::id());

                                $month_closing->status_id = 3;
                                $month_closing->closing_date = $date;
                                $month_closing->closing_updated_at = $date;
                                $month_closing->updated_by = Auth::id();
                                $month_closing->remarks = $remarks;
                                $month_closing->save();
                                $success[$shipment_details->tracking_number] = 'Shipment is successfully added to Month Closing!';

                            }
                            else{
                                $errors[$shipment_details->tracking_number] = 'Shipment can not added to Month Closing!';

                            }
                        }else{
                            $errors[$shipment_details->tracking_number] = 'Shipment not on Month Closing Resolved!';
                        }
                    }
                    else{
                        $errors[$shipment_details->tracking_number] = 'Shipment is already added as Month Closing!';
                    }

                }
            }
            $status = 0;
            if(!empty($errors) && empty($success)) {
                $status = 1;
            }
            if(!empty($success)  && empty($errors)){
                $status = 2;
            }
            if(!empty($errors) && !empty($success)){
                $status = 3;
            }
            return response()->json(['status' => $status, 'success' => $success, 'errors' => $errors]);
        }
        return response()->json(['status' => 1, 'error' => 'No shipment(s) selected!']);
    }


    public function return_confirm_shipment_status(){

    }

}
