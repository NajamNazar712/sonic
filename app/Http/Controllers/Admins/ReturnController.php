<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Admins\ShipmentChargesController;
use App\Http\Controllers\Admins\AdminFinanceController;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Controllers\NotificationsController;
use App\Http\Models\Admin\DeliveryNoteShipment;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Admin\ReturnNote;
use App\Http\Models\Admin\ReturnNoteShipment;
use App\Http\Models\Admin\ReturnReattemptRatio;
use App\Http\Models\BookingType;
use App\Http\Models\City;
use App\Http\Models\CRM\CrmRequest;
use App\Http\Models\PackagingMaterialRequest;
use App\Http\Models\PackagingMaterialRequestDetail;
use App\Http\Models\PackagingMaterialRequestHistory;
use App\Http\Models\PendingPayment;
use App\Http\Models\PendingPaymentShipment;
use App\Http\Models\Rider;
use App\Http\Models\Route;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentsJourney;
use App\Http\Models\ShipmentStatus;
use App\Http\Models\ShippingMode;
use App\Http\Models\Warehouse\WarehouseFulfilmentHubs;
use App\http\Models\WarehouseStock;
use Carbon\Carbon;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Yajra\Datatables\Datatables;
class ReturnController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }
    public function return_view(){
        $shipment_status = ShipmentStatus::select('id','name')->get();
        $shipping_mode = ShippingMode::all();
        $service_type = BookingType::all();
        return view('admin.return.index')->with(['shipment_status'=>$shipment_status,'shipping_mode'=>$shipping_mode,'service_type'=>$service_type]);
    }
    public function return_marked_list(Request $request){ //status 12 shipments
        $shipments = Shipment::join('users as u', 'shipments.user_id', '=', 'u.id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->join('cities as h' ,'dc.hub_id', '=' , 'h.id')
            ->join('shipping_modes as sm','sm.id','=','shipments.shipping_mode_id')
            ->join('booking_types as bt','bt.id','=','shipments.booking_type_id')
            ->join('shipment_status as ss','ss.id','=','shipments.shipper_status_id')
            ->leftJoin('shipments_journey', function ($join) {
                $join->on('shipments_journey.shipment_id', '=', 'shipments.id')
                    ->where('shipments_journey.created_at','=',
                        DB::raw('(select max(created_at) from shipments_journey where shipments_journey.shipment_id = shipments.id)'));
            })
            ->leftJoin('shipments_journey as admin_journey', function ($join) {
                $join->on('admin_journey.shipment_id', '=', 'shipments.id')
                    ->where('admin_journey.created_at','=',
                        DB::raw('(select max(created_at) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id != 52)'));
            })
            ->leftJoin('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where('sj.created_at','=',
                        DB::raw('(select max(created_at) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)'));
            })
            ->leftJoin('shipments_journey as sret', function ($join) {
                $join->on('sret.shipment_id', '=', 'shipments.id')
                    ->where('sret.shipper_status_id','=',13)
                    ->where('sret.verification','=',1);
//                    ->where('sret.id','=',
//                        DB::raw('(select id from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 13)'));
            })
            ->leftJoin('shipment_status_reason as ssr','ssr.id','=','shipments_journey.status_reason_id')
            ->leftjoin('crm_requests as crm', function ($join) {
                $join->on('crm.shipment_id', '=', 'shipments.id')
                    ->whereIn('crm.status_id', [2, 3, 5])
                    ->where('crm.case_nature_id', 1);
            })
            ->select('shipments.id as shId','shipments.tracking_number','shipments.tracking_number as tracking','u.name as shipper','u.phone as shipper_phone1','u.phone2 as shipper_phone2','oc.name as origin','dc.name as destination','shipments.order_id','h.name as hub','shipments.consignee_name','shipments.consignee_phone_number_1','shipments.consignee_phone_number_2','shipments.consignee_address','shipments.amount','sm.mode','bt.booking_type as service_type','ss.name as status','ssr.id as reason_id','ssr.name as reason','admin_journey.remarks as remarks','shipments_journey.created_at as status_date','shipments_journey.created_at as last_status_date','sj.created_at as arrival', 'shipments.booking_type_id', 'usi.poc', DB::raw('count(sret.shipment_id) as reattempts'), 'shipments_journey.remarks as shipper_remarks','shipments.shipper_status_id as current_status_id','crm.id as complaint','shipments.nsa_osa_estimated_charges')
            ->whereIn('shipments.shipper_status_id', [12,52])
            ->groupBy('shipments.id');

        if (session('role_id') != 1) {
            $shipments = $shipments->whereIn('dc.hub_id', session('hubs'));
        }

        $datatable = Datatables::of($shipments)
            ->setRowAttr([
                'class' => function ($shipments) {
                    if ($shipments->complaint != null) {
                        return 'complaint_row';
                    }
                    if($shipments->reason_id == 12){
                        return 'nsa_osa_reason';
                    }
                    if ($shipments->current_status_id == 52) {
                        return 'goldClass';
                    }
                },
            ])
            ->editColumn('tracking_number',function ($shipments){
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->editColumn('amount', function($shipment){
                return number_format($shipment->amount);
            })
            ->editColumn('shipper_phone',function ($shipper){
                return "$shipper->shipper_phone1 | $shipper->shipper_phone2";
            })
            ->editColumn('shipper', function ($shipment) {
                if ($shipment->booking_type_id == 4) {
                    return $shipment->shipper .' (' . $shipment->poc . ')';
                }
                else {
                    return $shipment->shipper;
                }
            })
            ->editColumn('shipper_remarks', function ($shipment) {
                if ($shipment->current_status_id == 52) {
                    return $shipment->shipper_remarks;
                }
                else {
                    return '';
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
            ->orderColumn('u.name', 'u.name $1, usi.poc $1')
            ->editColumn('consignee_phone',function ($shipper){
                $consignee_phone = '';
                $consignee_phone .= $shipper->consignee_phone_number_1;
                if($shipper->consignee_phone_number_2 != null){
                    $consignee_phone .= "| ".$shipper->consignee_phone_number_2;
                }
                return $consignee_phone;
            })
            ->filterColumn('shipper_phone',function ($query,$keyword){
                $keyword = strtolower($keyword);
                if ($keyword != '') {
                    $query->where('u.phone', 'like', '%'.$keyword.'%')->orWhere('u.phone2', 'like', '%'.$keyword.'%');
                }

                else {
                    $query->whereRaw('false');
                }
            })
            ->filterColumn('consignee_phone',function ($query,$keyword){
                $keyword = strtolower($keyword);
                if ($keyword != '') {
                    $query->where('shipments.consignee_phone_number_1', 'like', '%'.$keyword.'%')->orWhere('shipments.consignee_phone_number_2', 'like', '%'.$keyword.'%');
                }

                else {
                    $query->whereRaw('false');
                }
            })
            ->orderColumn('shipper_phone', 'u.phone $1, u.phone2 $1')
            ->orderColumn('consignee_phone', 'shipments.consignee_phone_number_1 $1, shipments.consignee_phone_number_2 $1')
            ->addColumn('shipment_remarks',function ($shipments){
                $remark = '<input class="form-control form-control-sm" value="'.$shipments->remarks.'" />';
                return $remark;
            })
            ->editColumn('status_date',function ($shipments){
                if($shipments->status_date) {
                    if (2 - ((new \Carbon\Carbon($shipments->status_date, 'UTC'))->diffInDays()) < 0) {
                        return "<span class='danger font-weight-bold'>" . $shipments->status_date . "</span>";
                    } else {
                        return $shipments->status_date;
                    }
                }else{
                    return " - ";
                }
            })
            ->editColumn('arrival',function($shipments){
                if($shipments->arrival){
                    return $shipments->arrival;
                }else{
                    return " - ";
                }
            })
            ->filterColumn('status',function ($query,$keyword){

                if ($keyword != '') {
                    $query->where('ss.id',$keyword);
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->addColumn("action", function ($result) {
                $confirm_button = '<a href="javascript:void(0);" class="dropdown-item returnMarkStatus" data-action="confirm"><i class="ft-plus-circle primary"></i> Confirm</a>';
                $re_attempt_button = '<a href="javascript:void(0);" class="dropdown-item returnMarkStatus" data-action="reattempt"><i class="ft-plus-circle primary"></i> Re-Attempt</a>';
                $self_collection_button = '<a href="javascript:void(0);" class="dropdown-item selfCollection" data-action="selfCollection"><i class="ft-plus-circle primary"></i> Mark for Self Collection</a>';
                $edit_estimate_charges = '<a href="javascript:void(0);" class="dropdown-item editEstimateCharges" data-action="editEstimateCharges"><i class="ft-plus-circle primary"></i> Edit Estimate Charges</a>';

                if (session('role_id') == 1 || count(array_intersect([45, 46], session('permissions'))) !== 0) {
                    $dropdown = "
                        <div class='btn-group'>
                           <button type='button' class='btn btn-sm btn-success dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>Actions</button>
                            <div class='dropdown-menu dropdown-menu-sm'>";

                    if (session('role_id') == 1 || in_array(45, session('permissions'))) {
                        $dropdown .= $confirm_button;
                    }

                    if (session('role_id') == 1 || in_array(46, session('permissions'))) {
                        $dropdown .= $re_attempt_button;
                    }

                    if (session('role_id') == 1 || in_array(211, session('permissions'))) {
                        if($result->reason_id == 12 || $result->current_status_id == 52){
                            $dropdown .= $self_collection_button;
                        }
                    }

                    if (session('role_id') == 1 || in_array(212, session('permissions'))) {
                        if($result->reason_id == 12 || $result->current_status_id == 52){
                            $dropdown .= $edit_estimate_charges;
                        }
                    }

                    $dropdown .= "
                            </div>
                        </div>
                    ";

                    return $dropdown;
                }
                else {
                    return '';
                }
            });
        if ($tracking_numbers = $request->get('tracking_numbers')) {
            $datatable->whereIn('shipments.tracking_number', explode(',', $tracking_numbers));
        }
        return $datatable->make(true);
    }
    public function return_confirm_status(Request $request){ //update to status 20 for confirm and 13 for re-attempt


        $shipment_ids = $request->shipment_ids;

        if($request->action == 'confirm'){
            foreach ($shipment_ids as $shipment){
                $parcel = Shipment::find($shipment);
                $remark_inp = "remark.$shipment";
                if($parcel->shipper_status_id != 20 && $parcel->shipper_status_id != 54 && $parcel->shipper_status_id != 55){
//                    if (!$parcel->packaging_material_request) {

                        $remarks = ($request->has($remark_inp) && $request->remark[$parcel->id] != null)? $request->remark[$parcel->id] : null;
                        $shipment_history = ShipmentsJourney::where('shipment_id',$shipment)->latest()->first();
                        Shipment::where('id',$shipment)->update(['shipper_status_id'=>20,'consignee_status_id'=>20]);
                        ShipmentsJourneyController::add($shipment, 20, 20, $shipment_history->status_reason_id, $remarks, NULL, Auth::id());

                        NotificationsController::send(15, 0, $shipment);
                        NotificationsController::send(16, 0, $shipment);

                        if ($parcel->booking_type_id != 4) {
                            ShipmentChargesController::return($shipment);

                            AdminFinanceController::add_payment($shipment, 1);
                        }
                        else {
                            ShipmentChargesController::walk_in_return($shipment);

                            $parcel->walk_in_status = 2;

                            $parcel->save();

                            AdminFinanceController::done_payment($shipment, 1);
                        }
//                    }
//                    else {
//                        $remarks = ($request->remark[$parcel->id] != null)? $request->remark[$parcel->id] : null;
//                        $shipment_history = ShipmentsJourney::where('shipment_id',$shipment)->latest()->first();
//                        Shipment::where('id',$shipment)->update(['shipper_status_id'=>17,'consignee_status_id'=>17]);
//                        ShipmentsJourneyController::add($shipment, 17, 17, $shipment_history->status_reason_id, $remarks, NULL, Auth::id());
//
//                        NotificationsController::send(15, 0, $shipment);
//                        NotificationsController::send(16, 0, $shipment);
//                    }
                }

            }
            return ['status'=>1,'success'=>"Shipment successfully updated as ( Return Confirm )"];
        }
    }
    public function return_reattempt_status(Request $request){ //update to status 20 for confirm and 13 for re-attempt

        $shipment_ids = $request->shipment_ids;

        if($request->action == 'reattempt'){
            foreach ($shipment_ids as $shipment){
                $parcel = Shipment::find($shipment);
                if($parcel->shipper_status_id != 13){
                    $remark_inp = "remark.$shipment";
                    $remarks = ($request->has($remark_inp) && $request->remark[$parcel->id] != null)? $request->remark[$parcel->id] : null;
                    Shipment::where('id',$shipment)->update(['shipper_status_id'=>13,'consignee_status_id'=>13]);
                    $journey = ShipmentsJourney::where('shipment_id', $shipment)->where('shipper_status_id', 12)->latest('id')->first();

                    ShipmentsJourneyController::add($shipment, 13, 13, NULL, $remarks, NULL, Auth::id());

                    NotificationsController::send(15, 0, $shipment);
                    NotificationsController::send(16, 0, $shipment);

                    if ($journey) {
                        if ($parcel->shipper_status_id == 12 && ($journey->status_reason_id == 12)) {
                            $parcel->nsa_osa_status = 1;

                            $parcel->save();

                            ShipmentChargesController::nsa_osa_charges($shipment);

                            NotificationsController::send(33, $shipment);
                        }
                        else if ($parcel->shipper_status_id == 52) {
                            $journey = ShipmentsJourney::where('shipment_id', $shipment)->where('shipper_status_id', 12)->latest('id')->first();

                            if ($journey && ($journey->status_reason_id == 12)) {
                                $parcel->nsa_osa_status = 1;

                                $parcel->save();

                                ShipmentChargesController::nsa_osa_charges($shipment);
                            }
                        }
                    }
                }

            }
            return ['status'=>1,'success'=>"Shipment successfully updated as ( Re-Attempt )"];

        }
    }
    public function return_marked_single_status(Request $request){

        $remark = $request->remark;
        if($request->action == 'confirm'){
            $parcel = Shipment::find($request->shipment_id);
            if($parcel->shipper_status_id != 20 && $parcel->shipper_status_id != 54 && $parcel->shipper_status_id != 55){
//                if (!$parcel->packaging_material_request) {
                    Shipment::where('id',$request->shipment_id)->update(['shipper_status_id'=>20,'consignee_status_id'=>20]);
                    $shipment_history = ShipmentsJourney::where('shipment_id',$request->shipment_id)->latest()->first();
                    ShipmentsJourneyController::add($request->shipment_id, 20, 20, $shipment_history->status_reason_id, $remark, NULL, Auth::id());


                    NotificationsController::send(15, 0, $request->shipment_id);
                    NotificationsController::send(16, 0, $request->shipment_id);

                    if ($parcel->booking_type_id != 4) {
                        ShipmentChargesController::return($request->shipment_id);

                        AdminFinanceController::add_payment($request->shipment_id, 1);
                    }
                    else {
                        ShipmentChargesController::walk_in_return($request->shipment_id);

                        $parcel->walk_in_status = 2;

                        $parcel->save();

                        AdminFinanceController::done_payment($request->shipment_id, 1);
                    }
//                }
//                else {
//                    Shipment::where('id',$request->shipment_id)->update(['shipper_status_id'=>17,'consignee_status_id'=>17]);
//                    $shipment_history = ShipmentsJourney::where('shipment_id',$request->shipment_id)->latest()->first();
//                    ShipmentsJourneyController::add($request->shipment_id, 17, 17, $shipment_history->status_reason_id, $remark, NULL, Auth::id());
//
//
//                    NotificationsController::send(15, 0, $request->shipment_id);
//                    NotificationsController::send(16, 0, $request->shipment_id);
//                }
                return ['status'=>1,'success'=>"Shipment successfully marked as Shipment - Return Confirm"];
            }
            return ['status'=>0,'error'=>"Shipment is already updated, Please refresh your page!"];


        }elseif($request->action == 'reattempt'){
            $parcel = Shipment::find($request->shipment_id);
            if($parcel->shipper_status_id != 13){
                Shipment::where('id',$request->shipment_id)->update(['shipper_status_id'=>13,'consignee_status_id'=>13]);
                $journey = ShipmentsJourney::where('shipment_id', $request->shipment_id)->where('shipper_status_id', 12)->latest('id')->first();

                ShipmentsJourneyController::add($request->shipment_id, 13, 13, NULL, $remark, NULL, Auth::id());

                NotificationsController::send(15, 0, $request->shipment_id);
                NotificationsController::send(16, 0, $request->shipment_id);

                if ($journey) {
                    if ($parcel->shipper_status_id == 12 && ($journey->status_reason_id == 12)) {
                        $parcel->nsa_osa_status = 1;

                        $parcel->save();

                        ShipmentChargesController::nsa_osa_charges($request->shipment_id);

                        NotificationsController::send(33, $request->shipment_id);
                    }
                    else if ($parcel->shipper_status_id == 52) {
                        $journey = ShipmentsJourney::where('shipment_id', $request->shipment_id)->where('shipper_status_id', 12)->latest('id')->first();

                        if ($journey && ($journey->status_reason_id == 12)) {
                            $parcel->nsa_osa_status = 1;

                            $parcel->save();

                            ShipmentChargesController::nsa_osa_charges($request->shipment_id);
                        }
                    }
                }

                return ['status'=>1,'success'=>"Shipment successfully marked as Shipment - Re-Attempt"];
            }
            return ['status'=>0,'error'=>"Shipment is already updated, Please refresh your page!"];
        }
        return ['status'=>0,'error'=>"Shipment is already updated, Please refresh your page!"];

    }

    public function change_status_to_self_collection(Request $request){
        $shipmentId = $request->shipment_id;
        $remark = $request->remark;
        if($shipmentId){
            if(Shipment::where('id', $shipmentId)->where('shipper_status_id','!=', 15)->exists()){
                Shipment::where('id',$request->shipment_id)->update(['shipper_status_id'=>15,'consignee_status_id'=>15]);
                ShipmentsJourneyController::add($request->shipment_id, 15, 15, NULL, $remark, NULL, Auth::id());

                return ['status'=>0, 'success'=>"Shipment status successfully updated to Shipment - On Hold for Self Collection"];
            }else{
                return response()->json(['status' => 1, 'error' => 'Shipment already updated to Shipment - On Hold for Self Collection!']);
            }

        }else{
            return response()->json(['status' => 1, 'error' => 'Shipment ID Not selected!']);
        }
    }

    public function update_estimated_charges(Request $request){
        $shipment_id = $request->shipment_id;
        $charges = $request->charges;
        if($shipment_id){
            if($charges != null){
                $shipment = Shipment::find($shipment_id);
                $shipment->nsa_osa_estimated_charges = $charges;
                $shipment->save();

                return response()->json(['status' => 0, 'success' => 'Charges Updated!']);
            }else{
                return response()->json(['status' => 1, 'error' => 'Charges not entered!']);
            }

        }else{
            return response()->json(['status' => 1, 'error' => 'Shipment Not found!']);
        }
    }

    public function excel_store(Request $request){
        $names = [
            'tracking_number' => 'Tracking Number',
            'shipper_status_id' => 'Status (0 - Confirm / 1 - Re-Attempt)',
            'remarks' => 'Remarks'
        ];
        $messages = [
            'required' => ':attribute is Required.',
            'integer' => ':attribute must be an Integer.',
            'digits_between' => ':attribute must be between :min and :max Digits.',
            'unique' => ':attribute is already Present.'
        ];
        $rules = [
            'tracking_number' => ['required', 'integer'],
            'shipper_status_id' => ['required', 'integer', 'digits_between:0,1'],
            'remarks' => ['nullable', 'between:0,190']
        ];
        $fields = [0 => 'tracking_number', 1 => 'shipper_status_id', 2 => 'remarks'];
        $file = $request->file('shipments');

        $spreadsheet = IOFactory::createReaderForFile($file);
        $spreadsheet->setReadDataOnly(true);
        $spreadsheet = $spreadsheet->load($file)->getActiveSheet()->toArray();
        $header = ['Tracking Number', 'Status (0 - Confirm / 1 - Re-Attempt)', 'Remarks'];
        if ($spreadsheet[0] == $header) {
            unset($spreadsheet[0]);

            if (!empty($spreadsheet)) {
                $rows = array();
                foreach ($spreadsheet as $spreadsheet_row) {
                    $row = array();

                    foreach ($spreadsheet_row as $key => $value) {
                        $row[$fields[$key]] = $value;
                    }

                    $rows[] = $row;
                }

                unset($spreadsheet);
                $errors = array();
                $tracking_ids = array();
                $tracking_id_row = array();
                foreach ($rows as $key => $row) {
                    $row_id = $key + 2;

                    $validate = Validator::make($row, $rules, $messages);

                    $validate->setAttributeNames($names);

                    if ($validate->fails()) {
                        $errors['Row #' . $row_id] = $validate->errors()->all();
                    }
                    if (empty($errors['Row #' . $row_id])) {
                        if (!empty(trim($row['tracking_number']))) {
                            if (empty($tracking_ids)) {
                                $tracking_ids[] = $row['tracking_number'];
                                $tracking_id_row[$row['tracking_number']] = $row_id;
                            }
                            else {
                                if (in_array($row['tracking_number'], $tracking_ids)) {
                                    $errors['Row #' . $row_id][] = 'Same Tracking Number as of Row #' . $tracking_id_row[$row['tracking_number']];
                                }
                                else {
                                    $tracking_ids[] = $row['tracking_number'];
                                    $tracking_id_row[$row['tracking_number']] = $row_id;
                                }
                            }
                        }
                        if (!Shipment::where('tracking_number', $row['tracking_number'])->where('shipper_status_id', 12)->exists()) {
                            $errors['Row #' . $row_id][] = 'Shipment is not ready for confirmation pending #' . $row['tracking_number'];
                        }
                    }


                }
                if(empty($errors)){
                    $tracking_numbers = array();
                    foreach ($rows as $key => $row) {
                        $row_id = $key + 2;
                        $tracking = trim($row['tracking_number']);
                        $status = trim($row['shipper_status_id']);
                        $remarks = trim($row['remarks']);

                        if (!empty($row['remarks'])) {
                            $remarks = trim($row['remarks']);
                        }
                        else {
                            $remarks = NULL;
                        }
                        $shipment_details = Shipment::where('tracking_number',$tracking)->first();
                        $shipment_history = ShipmentsJourney::where('shipment_id',$shipment_details->id)->latest()->first();
                        if($status == 0){
                            $shipment_details->shipper_status_id = 20; //Confirmation Pending
                            ShipmentsJourneyController::add($shipment_details->id, 20, 20, $shipment_history->status_reason_id, $remarks, NULL, Auth::id());
                            NotificationsController::send(15, 0, $shipment_details->id);
                            NotificationsController::send(16, 0, $shipment_details->id);

//                            ShipmentChargesController::return($shipment_details->id);
//
//                            AdminFinanceController::add_payment($shipment_details->id, 1);
                        }
                        if($status == 1){
                            $shipment_details->shipper_status_id = 13; //Re-Attempt
                            ShipmentsJourneyController::add($shipment_details->id, 13, 13, $shipment_history->status_reason_id, $remarks, NULL, Auth::id());
//                            NotificationsController::send(15, 0, $shipment_details->id);
//                            NotificationsController::send(16, 0, $shipment_details->id);
                        }
                        $shipment_details->save();
                        $tracking_numbers['Row #' . $row_id] = $tracking;

                    }
                    $tracking_numbers = implode(' | ', array_map(function ($row, $tracking_number) {
                        return $row . ': ' . $tracking_number;
                    }, array_keys($tracking_numbers), $tracking_numbers));

                    return redirect()->back()->with(['success' => 'Total ' . count($rows) . ' Shipment(s) Updated with Tracking Number(s):' . PHP_EOL . $tracking_numbers]);
                }
                else{
                    $errors = array_map(function ($row, $errors) {
                        return $row . ':' . PHP_EOL . implode(' | ', $errors);
                    }, array_keys($errors), $errors);

                    return redirect()->back()->withErrors($errors);
                }

            }
            else {
                return redirect()->back()->with('error', 'No Shipments in File');
            }
        }
        else {
            return redirect()->back()->with('error', 'Invalid Columns, Kindly follow the Template provided');
        }
    }

    public function return_confirmed_view(){
        $shipment_status = ShipmentStatus::select('id','name')->get();
        $shipping_mode = ShippingMode::all();
        $service_type = BookingType::all();
        return view('admin.return.confirmed')->with(['shipment_status'=>$shipment_status,'shipping_mode'=>$shipping_mode,'service_type'=>$service_type]);
    }
    public function return_confirmed_list(Request $request){
        $status_return = array(20,22,24,27,29,30,33,35,37,44,45,46,47,48);
        $shipments = Shipment::join('users as u', 'shipments.user_id', '=', 'u.id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->join('cities as h' ,'dc.hub_id', '=' , 'h.id')
            ->join('shipping_modes as sm','sm.id','=','shipments.shipping_mode_id')
            ->join('booking_types as bt','bt.id','=','shipments.booking_type_id')
            ->join('shipment_status as ss','ss.id','=','shipments.shipper_status_id')
            ->leftJoin('shipments_journey', function ($join) {
                $join->on('shipments_journey.shipment_id', '=', 'shipments.id')
                    ->where('shipments_journey.id','=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id order by shipments_journey.created_at desc limit 1)'));
            })
            ->leftJoin('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where('sj.id','=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)'));
            })
            ->leftjoin('crm_requests as crm', function ($join) {
                $join->on('crm.shipment_id', '=', 'shipments.id')
                    ->whereIn('crm.status_id', [2, 3, 5])
                    ->where('crm.case_nature_id', 1);
            })
            ->leftJoin('shipment_status_reason as ssr','ssr.id','=','shipments_journey.status_reason_id')
            ->select('shipments.id as shipment_id','shipments.id as shId', 'shipments.shipper_status_id', 'shipments.tracking_number as tracking_number', 'shipments.tracking_number as tracking','u.name as shipper', 'oc.hub_id as origin_hub_id', 'oc.name as origin', 'dc.hub_id as destination_hub_id', 'dc.name as destination','shipments.order_id','h.name as hub','shipments.consignee_name','shipments.consignee_phone_number_1 as phone','shipments.consignee_address','shipments.amount','sm.mode','bt.booking_type as service_type','ss.name as status','ssr.name as reason','shipments_journey.remarks as remarks','shipments_journey.created_at as status_date','shipments_journey.created_at as last_status_date','sj.created_at as arrival', 'shipments.booking_type_id', 'usi.poc','crm.id as complaint')
            ->whereIn('shipments.shipper_status_id',$status_return);

        if (session('role_id') != 1) {
            $shipments = $shipments->where(function($query) {
                $query->where(function ($sub_query){
                    $sub_query->whereIn('dc.hub_id', session('hubs'));
                    })
                    ->orWhere(function ($sub_query){
                    $sub_query->whereIn('oc.hub_id', session('hubs'));
                    });
            });
        }

        $datatables = Datatables::of($shipments)
            ->setRowAttr([
                'class' => function ($shipments) {
                    if ($shipments->complaint != null) {
                        return 'complaint_row';
                    }
                },
            ])
            ->addColumn('return_pending_for', function ($shipment) {
                if (in_array($shipment->shipper_status_id, [22, 24, 27, 29, 33, 35, 44, 45, 46,47,48])) {
                    return 'Shipper';
                }
                else {
                    if ($shipment->origin_hub_id == $shipment->destination_hub_id) {
                        return 'Shipper';
                    }
                    else {
                        return 'Cargo';
                    }
                }
            })
            ->editColumn('amount', function($shipment){
                return number_format($shipment->amount);
            })
            ->editColumn('tracking_number',function ($shipments){
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->editColumn('shipper', function ($shipment) {
                if ($shipment->booking_type_id == 4) {
                    return $shipment->shipper .' (' . $shipment->poc . ')';
                }
                else {
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
            ->orderColumn('u.name', 'u.name $1, usi.poc $1')
            ->editColumn('status_date',function ($shipments){
                if($shipments->status_date) {
                    if (2 - ((new \Carbon\Carbon($shipments->status_date, 'UTC'))->diffInDays()) < 0) {
                        return "<span class='danger font-weight-bold'>" . $shipments->status_date . "</span>";
                    } else {
                        return $shipments->status_date;
                    }
                }else{
                    return " - ";
                }
            })
            ->editColumn('arrival',function($shipments){
                if($shipments->arrival){
                    return $shipments->arrival;
                }else{
                    return " - ";
                }
            })
            ->addColumn('shipment_remarks',function ($shipments){
                $remark = '<input class="form-control form-control-sm" placeholder="Remarks here.." value="'.$shipments->remarks.'" />';
                return $remark;
            })
            ->filterColumn('status',function ($query,$keyword){

                if ($keyword != '') {
                    $query->where('ss.id',$keyword);
                }
                else {
                    $query->whereRaw('false');
                }
            })            ->filterColumn('return_pending_for', function ($query, $keyword) {
                $keyword = strtolower($keyword);

                if (strpos('shipper', $keyword) !== FALSE) {
                    $query->whereIn('shipments.shipper_status_id', [22, 24, 27, 29, 33, 35, 44, 45, 46,47,48])
                        ->orWhereRaw('`oc`.`hub_id` = `dc`.`hub_id`');
                }
                else if (strpos('cargo', $keyword) !== FALSE) {
                    $query->whereIn('shipments.shipper_status_id', [20, 30, 37])
                        ->whereRaw('`oc`.`hub_id` != `dc`.`hub_id`');
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->addColumn('action', function($shipment) {
                if (($shipment->shipper_status_id == 20) && (session('role_id') == 1 || in_array(109, session('permissions')))) { //Change ID
                    $revert_button = '<button type="button" class="dropdown-item revert"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Revert</div></button>';

                    $dropdown = '
                      <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                        <div class="dropdown-menu dropdown-menu-sm">
                    ';

                    $dropdown .= $revert_button;

                    $dropdown .= '
                        </div>
                      </div>
                    ';

                    return $dropdown;
                }
                else {
                    return '';
                }
            });

        return $datatables->make(true);
    }
    public function return_create_index(){
        $riders = Rider::where('status', 1);

        if (session('role_id') != 1) {
            $riders = $riders->whereHas('city', function ($query) {
                $query->whereIn('hub_id', session('hubs'));
            });
        }

        $riders = $riders->get();

        $routes = Route::where('status', 1);

        if (session('role_id') != 1) {
            $routes = $routes->whereHas('city', function ($query) {
                $query->whereIn('hub_id', session('hubs'));
            });
        }

        $routes = $routes->get();

        return view('admin.return.create')->with(['riders'=>$riders,'routes'=>$routes]);
    }
    public function get_shipment_details(Request $request){
        if($request->tracking != ''){
//            $shipment_not_arrived = array(20,24,27,29,33,35,42,44,45,46);
//            $shipment_arrived = array(22,24,27,29,30,33,35,44,45,46);
            $allowed_statuses = array(20,22,24,27,29,30,33,35,37,42,44,45,46,47,48);
            $return_note_statuses = array(20, 24, 27, 29, 30, 33, 35, 37, 42, 44, 45, 46, 47, 48);
            $shipment = Shipment::where('tracking_number', $request->tracking)->whereIn('shipper_status_id',$allowed_statuses);
            $status = '';
            if($shipment->exists()) {
                $shipment = $shipment->first();
                $destination_id = $shipment->pickup_address->city_id;
                $destination_id = City::where('id', $destination_id)->select('hub_id')->first();
                $destination_id = $destination_id->hub_id;//first it was origin now for return its destination
                if(session('role_id') == 1 || in_array($destination_id, session('hubs'))){
                    $origin = $shipment->consignee_city->hub_id;//let's suppose consignee city is origin now
                    if(!$request->has('hub_id')){
                        if ($destination_id == $origin && (in_array($shipment->shipper_status_id, $return_note_statuses))) {
                            $destination_city_id = $shipment->pickup_address->city_id;
                            $destination_city = City::find($destination_city_id);
                            if ($destination_city->id == $destination_city->hub_id) {
                                $destination = $destination_city->name;
                                $hub = $destination_city->id;
                            } else {
                                $hubid = $destination_city->hub_id;
                                $destinationHub = City::find($hubid);
                                $destination = $destinationHub->name;
                                $hub = $destinationHub->id;
                            }
                            $service = $shipment->booking_type->booking_type;
                            $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id);
                            if ($shipment_journey->exists()) {
                                $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->select('shipper_status_id', 'remarks')->latest()->first();
//                            $remarks = ($shipment_journey->remarks != '')? $shipment_journey->remarks:' - ';
                                $status_id = ($shipment_journey->shipper_status_id) ? $shipment_journey->shipper_status_id : '';
                                if ($status_id != '') {
                                    $status_name = ShipmentStatus::where('id', $status_id)->select('name')->first();
                                    $status = $status_name->name;
                                } else {
                                    $status = ' - ';
                                }
                            }
                            if($shipment->booking_type_id != 4) {
                                $settings = GlobalSettings::where('type', 'return_note_restriction_bypass');

                                if ($settings->exists()) {
                                    $settings = $settings->first();
                                    $role_ids = array_map('intval', explode(',', $settings->text));
                                }
                                else {
                                    $role_ids = array();
                                }

                                if (!in_array(session('role_id'), $role_ids)) {
                                    $shipper_payable = 0;
                                    $pending_payment = PendingPayment::where('user_id', $shipment->user_id);
                                    if ($pending_payment->exists()) {
                                        $pending_payment = $pending_payment->first();

                                        $pending_payment_shipments = PendingPaymentShipment::where('pending_payment_id', $pending_payment->id);
                                        if ($pending_payment_shipments->exists()) {
                                            $pending_payment_shipments = $pending_payment_shipments->get();
                                            foreach ($pending_payment_shipments as $pending_payment_shipment) {
                                                $shipper_payable += $pending_payment_shipment->payable;
                                            }
                                        }
                                    }
                                    if ($shipper_payable < 0) {
                                        return response()->json(['status' => 1, 'error' => 'Shipper with Negative Balance, Contact Sales Team!']);
                                    }
                                }
                            }
                            $class = null;
                            if(CrmRequest::where('shipment_id',$shipment->id)->where('case_nature_id',1)->whereIn('status_id',[2, 3, 5])->exists()){
                                $class = 'complaint_row';
                            }

                            return response()->json(['status' => 0, 'shId' => $shipment->id, 'tracking_number' => $shipment->tracking_number, 'destination' => $destination, 'hub' => $hub, 'consignee_name' => $shipment->consignee_name, 'phone' => $shipment->consignee_phone_number_1, 'address' => $shipment->consignee_address, 'amount' => number_format($shipment->amount), 'service_type' => $service, 'shipment_status' => $status, 'class' => $class]);

                        } else
                            if ($destination_id != $origin && ($shipment->shipper_status_id == 22 || $shipment->shipper_status_id == 24 || $shipment->shipper_status_id == 27 || $shipment->shipper_status_id == 29 || $shipment->shipper_status_id == 33 || $shipment->shipper_status_id == 35 || $shipment->shipper_status_id == 37 || $shipment->shipper_status_id == 42 || $shipment->shipper_status_id == 44 || $shipment->shipper_status_id == 45 || $shipment->shipper_status_id == 46 || $shipment->shipper_status_id == 47 || $shipment->shipper_status_id == 48)) {
                                $destination_city_id = $shipment->pickup_address->city_id;
                                $destination_city = City::find($destination_city_id);
                                if ($destination_city->id == $destination_city->hub_id) {
                                    $destination = $destination_city->name;
                                    $hub = $destination_city->id;
                                } else {
                                    $hubid = $destination_city->hub_id;
                                    $destinationHub = City::find($hubid);
                                    $destination = $destinationHub->name;
                                    $hub = $destinationHub->id;
                                }

                                $service = $shipment->booking_type->booking_type;
                                $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id);
                                if ($shipment_journey->exists()) {
                                    $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->select('shipper_status_id', 'remarks')->latest()->first();
//                            $remarks = ($shipment_journey->remarks != '')? $shipment_journey->remarks:' - ';
                                    $status_id = ($shipment_journey->shipper_status_id) ? $shipment_journey->shipper_status_id : '';
                                    if ($status_id != '') {
                                        $status_name = ShipmentStatus::where('id', $status_id)->select('name')->first();
                                        $status = $status_name->name;
                                    } else {
                                        $status = ' - ';
                                    }
                                }
                                if($shipment->booking_type_id != 4) {
                                    $settings = GlobalSettings::where('type', 'return_note_restriction_bypass');

                                    if ($settings->exists()) {
                                        $settings = $settings->first();
                                        $role_ids = array_map('intval', explode(',', $settings->text));
                                        if (!in_array(session('role_id'), $role_ids)) {
                                            $shipper_payable = 0;
                                            $pending_payment = PendingPayment::where('user_id', $shipment->user_id);
                                            if ($pending_payment->exists()) {
                                                $pending_payment = $pending_payment->first();

                                                $pending_payment_shipments = PendingPaymentShipment::where('pending_payment_id', $pending_payment->id);
                                                if ($pending_payment_shipments->exists()) {
                                                    $pending_payment_shipments = $pending_payment_shipments->get();
                                                    foreach ($pending_payment_shipments as $pending_payment_shipment) {
                                                        $shipper_payable += $pending_payment_shipment->payable;
                                                    }
                                                }
                                            }
                                            if ($shipper_payable < 0) {
                                                return response()->json(['status' => 1, 'error' => 'Shipper with Negative Balance, Contact Sales Team!']);
                                            }
                                        }
                                    }
                                }
                                $class = null;
                                if(CrmRequest::where('shipment_id',$shipment->id)->where('case_nature_id',1)->whereIn('status_id',[2, 3, 5])->exists()){
                                    $class = 'complaint_row';
                                }
                                return response()->json(['status' => 0, 'shId' => $shipment->id, 'tracking_number' => $shipment->tracking_number, 'destination' => $destination, 'hub' => $hub, 'consignee_name' => $shipment->consignee_name, 'phone' => $shipment->consignee_phone_number_1, 'address' => $shipment->consignee_address, 'amount' => number_format($shipment->amount), 'service_type' => $service, 'shipment_status' => $status, 'class' => $class]);

                            } else {
                                return ['status' => 1, 'error' => 'Return Shipment not arrived at origin center yet.'];

                            }
                    }else
                        if($request->has('hub_id') && ($destination_id == $request->hub_id)){
                            if ($destination_id == $origin && ($shipment->shipper_status_id == 20 || $shipment->shipper_status_id == 24 || $shipment->shipper_status_id == 27 || $shipment->shipper_status_id == 29 || $shipment->shipper_status_id == 30 || $shipment->shipper_status_id == 33 || $shipment->shipper_status_id == 35 || $shipment->shipper_status_id == 37 || $shipment->shipper_status_id == 42 || $shipment->shipper_status_id == 44 || $shipment->shipper_status_id == 45 || $shipment->shipper_status_id == 46 || $shipment->shipper_status_id == 47 || $shipment->shipper_status_id == 48)) {
                                $destination_city_id = $shipment->pickup_address->city_id;
                                $destination_city = City::find($destination_city_id);
                                if ($destination_city->id == $destination_city->hub_id) {
                                    $destination = $destination_city->name;
                                    $hub = $destination_city->id;
                                } else {
                                    $hubid = $destination_city->hub_id;
                                    $destinationHub = City::find($hubid);
                                    $destination = $destinationHub->name;
                                    $hub = $destinationHub->id;
                                }
                                $service = $shipment->booking_type->booking_type;
                                $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id);
                                if ($shipment_journey->exists()) {
                                    $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->select('shipper_status_id', 'remarks')->latest()->first();
//                            $remarks = ($shipment_journey->remarks != '')? $shipment_journey->remarks:' - ';
                                    $status_id = ($shipment_journey->shipper_status_id) ? $shipment_journey->shipper_status_id : '';
                                    if ($status_id != '') {
                                        $status_name = ShipmentStatus::where('id', $status_id)->select('name')->first();
                                        $status = $status_name->name;
                                    } else {
                                        $status = ' - ';
                                    }
                                }
                                if($shipment->booking_type_id != 4) {
                                    $settings = GlobalSettings::where('type', 'return_note_restriction_bypass');

                                    if ($settings->exists()) {
                                        $settings = $settings->first();
                                        $role_ids = array_map('intval', explode(',', $settings->text));
                                        if (!in_array(session('role_id'), $role_ids)) {
                                            $shipper_payable = 0;
                                            $pending_payment = PendingPayment::where('user_id', $shipment->user_id);
                                            if ($pending_payment->exists()) {
                                                $pending_payment = $pending_payment->first();

                                                $pending_payment_shipments = PendingPaymentShipment::where('pending_payment_id', $pending_payment->id);
                                                if ($pending_payment_shipments->exists()) {
                                                    $pending_payment_shipments = $pending_payment_shipments->get();
                                                    foreach ($pending_payment_shipments as $pending_payment_shipment) {
                                                        $shipper_payable += $pending_payment_shipment->payable;
                                                    }
                                                }
                                            }
                                            if ($shipper_payable < 0) {
                                                return response()->json(['status' => 1, 'error' => 'Shipper with Negative Balance, Contact Sales Team!']);
                                            }
                                        }
                                    }
                                }
                                $class = null;
                                if(CrmRequest::where('shipment_id',$shipment->id)->where('case_nature_id',1)->whereIn('status_id',[2, 3, 5])->exists()){
                                    $class = 'complaint_row';
                                }
                                return response()->json(['status' => 0, 'shId' => $shipment->id, 'tracking_number' => $shipment->tracking_number, 'destination' => $destination, 'hub' => $hub, 'consignee_name' => $shipment->consignee_name, 'phone' => $shipment->consignee_phone_number_1, 'address' => $shipment->consignee_address, 'amount' => number_format($shipment->amount), 'service_type' => $service, 'shipment_status' => $status, 'class' => $class]);

                            } else
                                if ($destination_id != $origin && ($shipment->shipper_status_id == 22 || $shipment->shipper_status_id == 24 || $shipment->shipper_status_id == 27 || $shipment->shipper_status_id == 29 || $shipment->shipper_status_id == 33 || $shipment->shipper_status_id == 35 || $shipment->shipper_status_id == 42 || $shipment->shipper_status_id == 44 || $shipment->shipper_status_id == 45 || $shipment->shipper_status_id == 46 || $shipment->shipper_status_id == 47 || $shipment->shipper_status_id == 48)) {
                                    $destination_city_id = $shipment->pickup_address->city_id;
                                    $destination_city = City::find($destination_city_id);
                                    if ($destination_city->id == $destination_city->hub_id) {
                                        $destination = $destination_city->name;
                                        $hub = $destination_city->id;
                                    } else {
                                        $hubid = $destination_city->hub_id;
                                        $destinationHub = City::find($hubid);
                                        $destination = $destinationHub->name;
                                        $hub = $destinationHub->id;
                                    }

                                    $service = $shipment->booking_type->booking_type;
                                    $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id);
                                    if ($shipment_journey->exists()) {
                                        $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->select('shipper_status_id', 'remarks')->latest()->first();
//                            $remarks = ($shipment_journey->remarks != '')? $shipment_journey->remarks:' - ';
                                        $status_id = ($shipment_journey->shipper_status_id) ? $shipment_journey->shipper_status_id : '';
                                        if ($status_id != '') {
                                            $status_name = ShipmentStatus::where('id', $status_id)->select('name')->first();
                                            $status = $status_name->name;
                                        } else {
                                            $status = ' - ';
                                        }
                                    }
                                    if($shipment->booking_type_id != 4) {
                                        $settings = GlobalSettings::where('type', 'return_note_restriction_bypass');

                                        if ($settings->exists()) {
                                            $settings = $settings->first();
                                            $role_ids = array_map('intval', explode(',', $settings->text));
                                            if (!in_array(session('role_id'), $role_ids)) {
                                                $shipper_payable = 0;
                                                $pending_payment = PendingPayment::where('user_id', $shipment->user_id);
                                                if ($pending_payment->exists()) {
                                                    $pending_payment = $pending_payment->first();

                                                    $pending_payment_shipments = PendingPaymentShipment::where('pending_payment_id', $pending_payment->id);
                                                    if ($pending_payment_shipments->exists()) {
                                                        $pending_payment_shipments = $pending_payment_shipments->get();
                                                        foreach ($pending_payment_shipments as $pending_payment_shipment) {
                                                            $shipper_payable += $pending_payment_shipment->payable;
                                                        }
                                                    }
                                                }
                                                if ($shipper_payable < 0) {
                                                    return response()->json(['status' => 1, 'error' => 'Shipper with Negative Balance, Contact Sales Team!']);
                                                }
                                            }
                                        }
                                    }
                                    $class = null;
                                    if(CrmRequest::where('shipment_id',$shipment->id)->where('case_nature_id',1)->whereIn('status_id',[2, 3, 5])->exists()){
                                        $class = 'complaint_row';
                                    }
                                    return response()->json(['status' => 0, 'shId' => $shipment->id, 'tracking_number' => $shipment->tracking_number, 'destination' => $destination, 'hub' => $hub, 'consignee_name' => $shipment->consignee_name, 'phone' => $shipment->consignee_phone_number_1, 'address' => $shipment->consignee_address, 'amount' => ($shipment->amount), 'service_type' => $service, 'shipment_status' => $status, 'class' => $class]);

                                } else {
                                    return ['status' => 1, 'error' => 'Return Shipment not arrived at origin center yet.'];

                                }
                        }else{
                            return ['status' => 1, 'error' => 'Different hub, scan shipments of same hub!.'];
                        }
                } else {
                    return ['status' => 1, 'error' => 'This Shipment doesn\'t belongs to your assigned hubs!'];
                }
            }else{
                return ['status' => 1, 'error' => 'No Shipment with given Tracking Number is present, Check tracking!'];
            }


        }
    }
    public function return_create_note(Request $request)
    {
        $trackings = explode(',', $request->shipment_ids);

        $rider = $request->rider_id;
        $route = $request->route_id;
        $hub_id = $request->hub_id;
        $admin = Auth::id();
        //$errors_not_arrived = array();
        $return_statuses = array(20,22,24,27,29,30,33,35,37,42,44,45,46,47,48);
        $valid_shipments = array();
        $shipments_count = 0;
        if(!empty($trackings)) {
            foreach ($trackings as $shipment_id) {
                $shipment_details = Shipment::find($shipment_id);
                if ($shipment_details) {
                    if (in_array($shipment_details->shipper_status_id, $return_statuses)) {
                        $valid_shipments[] = $shipment_id;
                        $shipments_count++;
                    }
                }
            }
            if ($shipments_count != 0) {

                $note = ReturnNote::create(['hub_id' => $hub_id, 'rider_id' => $rider, 'route_id' => $route, 'shipments_count' => $shipments_count, 'admin_id' => $admin]);
                if ($note) {
                    foreach ($valid_shipments as $tracking) {
                        $shipment = Shipment::where('id', $tracking);

                        $shipment = $shipment->first();
                        if(in_array($shipment->booking_type_id, [1,4,5])){
                            ReturnNoteShipment::create(['return_note_id' => $note->id, 'shipment_id' => $tracking]);
                            $shipment->shipper_status_id = 23;
                            $shipment->consignee_status_id = 23;
                            $shipment->save();
                            ShipmentsJourneyController::add($shipment->id, 23, 23, NULL, NULL, NULL, Auth::id(), $note->id, $rider);
                        }else
                        if ($shipment->booking_type_id == 2) {//attempt failed and arrived at origin center

                            ReturnNoteShipment::create(['return_note_id' => $note->id, 'shipment_id' => $tracking]);
                            $shipment->shipper_status_id = 28;
                            $shipment->consignee_status_id = 28;
                            $shipment->save();
                            ShipmentsJourneyController::add($shipment->id, 28, 28, NULL, NULL, NULL, Auth::id(), $note->id, $rider);


                        } else if ($shipment->booking_type_id == 3) {//attempt failed and arrived at origin center

                            ReturnNoteShipment::create(['return_note_id' => $note->id, 'shipment_id' => $tracking]);
                            $shipment->shipper_status_id = 34;
                            $shipment->consignee_status_id = 34;
                            $shipment->save();
                            ShipmentsJourneyController::add($shipment->id, 34, 34, NULL, NULL, NULL, Auth::id(), $note->id, $rider);


                        }else{
                            ReturnNoteShipment::create(['return_note_id' => $note->id, 'shipment_id' => $tracking]);
                            $shipment->shipper_status_id = 23;
                            $shipment->consignee_status_id = 23;
                            $shipment->save();
                            ShipmentsJourneyController::add($shipment->id, 23, 23, NULL, NULL, NULL, Auth::id(), $note->id, $rider);
                        }


                    }
                }
            }
            return redirect()->back()->with(['success' => "Return note has been created with Return Note Number:" . $note->id,'print'=>$note->id]);
        }else{

            return ['error'=>"No shipments scanned"];
        }
    }
    public function return_receive_deliveries_view(){
        return view('admin.return.receive');
    }
    public function return_receive_deliveries_list(Request $request){
        $deliveries = ReturnNote::
        join('cities AS oc', 'return_notes.hub_id', '=', 'oc.id')
            ->join('riders', 'return_notes.rider_id', '=', 'riders.id')
            ->join('admins','admins.id','=','return_notes.admin_id')
            ->select(['return_notes.id as return_note','return_notes.id as return_note_id','oc.name as hub','riders.name as rider','admins.name as assignee','return_notes.created_at','return_notes.shipments_count','return_notes.shipments_count as shipments_count_link'])
            ->where('return_notes.status',0);

        if (session('role_id') != 1) {
            $deliveries = $deliveries->whereIn('oc.hub_id', session('hubs'));
        }

        $datatables = Datatables::of($deliveries)
            ->editColumn('return_note', function ($deliveries) {
                return "<a href='javascript:void(0);' class='printreturnnote'><u>" . str_pad($deliveries->return_note, 6, '0', STR_PAD_LEFT) . "</u></a>";
            })
            ->addColumn('return_note_id_padded', function ($deliveries) {
                return str_pad($deliveries->return_note_id, 6, '0', STR_PAD_LEFT);
            })
            ->editColumn('shipments_count_link', function($deliveries) {
                if ($deliveries->shipments_count != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $deliveries->shipments_count . '</button>';
                }
                else {
                    return 0;
                }
            })
            ->filterColumn('return_notes.id', function ($query, $keyword) {
                return $query->where('return_notes.id', '=', $keyword);
            })
            ->addColumn("action", function ($result) {
                $statusUpdate = route('admin.return.receive.status',['id'=>$result->return_note]);
                $route = route('admin.return.receive.update',['id'=>$result->return_note]);

                $receive_button = '<a href="' . $statusUpdate . '" class="dropdown-item"><i class="ft-plus-circle primary"></i> Receive</a>';
                $shift_shipment_button = '<a href="' . $route . '" class="dropdown-item returnnoteupdate"><i class="ft-plus-circle primary"></i> Edit Shipment</a>';

                if (session('role_id') == 1 || count(array_intersect([50, 51], session('permissions'))) !== 0) {
                    $dropdown = "
                    <div class='btn-group'>
                           <button type='button' class='btn btn-sm btn-success dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>Actions</button>
                            <div class='dropdown-menu dropdown-menu-sm'>";

                    if (session('role_id') == 1 || in_array(50, session('permissions'))) {
                        $dropdown .= $receive_button;
                    }

                    if (($result->created_at->diffInMinutes(Carbon::now()) <= 60) && (session('role_id') == 1 || in_array(51, session('permissions')))) {
                        $dropdown .= $shift_shipment_button;
                    }

                    $dropdown .= "
                        </div>
                    </div>
                ";

                    return $dropdown;
                }
                else {
                    return '';
                }
            });

        if ($tracking_number = $request->get('search_tracking')) {
            $datatables->join('return_note_shipments as rns', 'return_notes.id', '=', 'rns.return_note_id')
                ->join('shipments as s', 'rns.shipment_id', '=', 's.id')
                ->where('s.tracking_number', '=', $tracking_number);
        }
        if ($return_note_number = $request->get('return_note_number')) {
            $datatables->where('return_notes.id', '=', $return_note_number);
        }

        return $datatables->make(true);

    }

    public function return_receive_update(Request $request,$id){
        $service_type = BookingType::all();
        return view('admin.return.receive_update')->with(['return_note_id'=>$id,'service_type'=>$service_type]);
    }
    public function return_receive_update_list(Request $request,$id){
        $deliveries = ReturnNote::join('return_note_shipments as dns','dns.return_note_id','=','return_notes.id')
            ->join('shipments','shipments.id','=','dns.shipment_id')
            ->join('cities AS oc', 'shipments.consignee_city_id', '=', 'oc.id')
            ->join('booking_types as bt','bt.id','=','shipments.booking_type_id')
            ->select(['return_notes.id as return_note','shipments.tracking_number','shipments.id as shId','oc.name as destination','shipments.consignee_name','shipments.consignee_phone_number_1 as phone','shipments.consignee_address as address','bt.booking_type as service_type'])
            ->where('return_notes.id',$id);

        if (session('role_id') != 1) {
            $deliveries = $deliveries->whereIn('return_notes.hub_id', session('hubs'));
        }

        return Datatables::of($deliveries)
            ->addColumn("action", function ($deliveries) {
                return "<a href='javascript:void(0);' class='returnnoterow'>Remove</a>";

            })
            ->filterColumn('service_type',function ($query,$keyword){

                if ($keyword != '') {
                    $query->where('bt.id',$keyword);
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->make(true);
    }
    public function return_receive_update_remove(Request $request){
//        return $request;
        $shipment = ReturnNoteShipment::where(['return_note_id'=>$request->return_note_id,'shipment_id'=>$request->shipment_id]);
        if($shipment->exists()){
            $shipment = $shipment->first();
            $return_note = $shipment->return_note_id;
            $return = ReturnNote::where('id',$return_note);
            if($return->exists()){
                $parcel = Shipment::where('id',$request->shipment_id);
//                return $parcel->get();
                $parcel = $parcel->first();
                $shipper_status = '';
                if($parcel->shipper_status_id == 23){
                    $shipper_status = 44;
                }else if($parcel->shipper_status_id == 28){
                    $shipper_status = 45;
                }else if($parcel->shipper_status_id == 34){
                    $shipper_status = 46;
                }
                ReturnNoteShipment::where(['return_note_id'=>$return_note,'shipment_id'=>$request->shipment_id])->delete();
                $return = $return->first();
                $count = $return->shipments_count;
                $count = $count-1;
                if($count == 0){
                    ReturnNote::where('id',$return_note)->update(['shipments_count'=>$count,'status'=>2]);
                }else{

                    ReturnNote::where('id',$return_note)->update(['shipments_count'=>$count]);
                }
                Shipment::where('id',$request->shipment_id)->update(['shipper_status_id'=>$shipper_status]);
                ShipmentsJourneyController::add($parcel->id, $shipper_status, NULL, NULL, NULL, NULL, Auth::id(),$request->return_note_id);

                return ['status' => 0, 'success' => 'Return Shipment is successfully removed'];
            }else{
                return ['status' => 1, 'error' => 'Something went wrong'];
            }
        }else{
            return ['status' => 1, 'error' => 'Something went wrong'];
        }
    }
    public function return_receive_status(Request $request,$id){
        $return = ReturnNote::where('id',$id);
        if($return->exists()){
            $return = $return->first();
            $shipment_status = ShipmentStatus::whereIn('id', [24,25, 47, 48])->get();
            $shipment_count = ReturnNoteShipment::where(['return_note_id'=>$id,'status'=>0])->count();
            $return_image = true;
            if($shipment_count == 0){
                $return_image = false;
            }
            return view('admin.return.receive_status')->with(['return_note_id'=>$id,'shipments_count'=>$return->shipments_count, 'return_note_status' => $return->status, 'shipment_statuses' => $shipment_status, 'return_note_image_status' => $return->image, 'return_image' => $return_image]);
        }else{
            return redirect()->route('admin.return.receive.index')->with(['error' => 'Return Note not found']);
        }
    }
    public function return_receive_status_list(Request $request){
        $deliveries = ReturnNote::join('return_note_shipments as dns','dns.return_note_id','=','return_notes.id')
            ->join('shipments','shipments.id','=','dns.shipment_id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('users','shipments.user_id','=','users.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('booking_types as bt','bt.id','=','shipments.booking_type_id')
            ->join('shipment_status as ss','ss.id','=','shipments.shipper_status_id')
            ->leftjoin('crm_requests as crm', function ($join) {
                $join->on('crm.shipment_id', '=', 'shipments.id')
                    ->whereIn('crm.status_id', [2, 3, 5])
                    ->where('crm.case_nature_id', 1);
            })
            ->leftjoin('shipments_journey as rrb', function ($join) {
                $join->on('rrb.shipment_id', '=', 'shipments.id')
                    ->whereIn('shipments.shipper_status_id', [25, 31, 38])
                    ->where('rrb.id', '=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id)'));
            })
            ->select(['return_notes.id as return_note','shipments.tracking_number','shipments.id as shId','oc.name as destination','usi.pickup_address as address','users.name as shipper','bt.booking_type as service_type','shipments.booking_type_id','shipments.shipper_status_id','ss.name as current_status_name', 'usi.poc', 'shipments.charges_mode_id', 'shipments.amount', 'shipments.return_charges','crm.id as complaint', 'rrb.received_or_refused_by', 'rrb.remarks as remarks'])
            ->where('return_notes.id',$request->id);

        if (session('role_id') != 1) {
            $deliveries = $deliveries->whereIn('return_notes.hub_id', session('hubs'));
        }

        return Datatables::of($deliveries)
            ->setRowAttr([
                'class' => function ($deliveries) {
                    if($deliveries->complaint != null){
                        return 'complaint_row';
                    } else {
                        return '';
                    }
                }
            ])
            ->addColumn('shipment_id_padded', function ($deliveries) {
                return str_pad($deliveries->shId, 6, '0', STR_PAD_LEFT);
            })
            ->editColumn('shipper', function ($shipment) {
                if ($shipment->booking_type_id == 4) {
                    return $shipment->shipper .' (' . $shipment->poc . ')';
                }
                else {
                    return $shipment->shipper;
                }
            })
            ->editColumn('amount', function($shipment){
                return number_format($shipment->amount);
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
            ->addColumn('status', function ($deliveries) {
                $delivered_array = array(25,31,38);
                if(in_array($deliveries->shipper_status_id,$delivered_array)){
                    return $deliveries->current_status_name;
                }else{
                    if(in_array($deliveries->booking_type_id,[1,4,5])){
                        $where = array(24,47,48);
                    }else if($deliveries->booking_type_id == 2){
                        $where = array(29,47,48);
                    }else if($deliveries->booking_type_id == 3){
                        $where = array(35,47,48);
                    }
                    $statuses = ShipmentStatus::whereIn('id',$where)->get();
                    $drops = '';
                    foreach ($statuses as $status){
                        $drops .= '<option value="'.$status->id.'">'.$status->name.'</option>';
                    }
                    $select = '<select class="form-control form-control-sm select2 statusDrop" name="status_drop['.$deliveries->shId.']" ><option></option>'.$drops.'</select>';
                    return $select;
                }

            })
            ->addColumn('reason', function ($deliveries) {
                $delivered_array = array(25,31,38);
                if(in_array($deliveries->shipper_status_id,$delivered_array)) {
                    return '';
                }else{
                    $reason = '<select class="form-control form-control-sm select2 reasonDrop" name="reason_drop['.$deliveries->shId.']" ><option></option></select>';
                    return $reason;
                }

            })
            ->addColumn('remarks', function ($deliveries) {
                $delivered_array = array(25,31,38);
                if(in_array($deliveries->shipper_status_id,$delivered_array)) {
                    return $deliveries->remarks;
                }else{
                    $reason = '<input class="form-control form-control-sm" name="remarks['.$deliveries->shId.']" placeholder="Enter Remarks">';
                    return $reason;
                }

            })
            ->addColumn('received_or_refused_by', function ($deliveries) {
                    $delivered_array = array(25,31,38);
                    if(!in_array($deliveries->shipper_status_id,$delivered_array)) {
                        $received_or_refused_by = '<input class="form-control form-control-sm" name="received_or_refused_by['.$deliveries->shId.']" placeholder="Enter Name">';
                        return $received_or_refused_by;
                    }else{
                        return $deliveries->received_or_refused_by;
                    }


            })
            ->addColumn('charges', function ($shipment) {
                if ($shipment->booking_type_id == 4) {
                    if ($shipment->charges_mode_id == 1) {
                        return number_format($shipment->return_charges);
                    }
                    else {
                        return number_format($shipment->amount);
                    }
                }
                else {
                    return '';
                }
            })
            ->addColumn('action',function($deliveries){
                $delivered_array = array(25,31,38);
                if(in_array($deliveries->shipper_status_id,$delivered_array)) {
                    return '';
                }else{
                    return " <span class='dropdown'>
                                            <button type='button' class='btn btn-success dropdown-toggle' data-toggle='dropdown'
                                                    aria-haspopup='true' aria-expanded='false'><i class='ft-settings'></i></button>
                                            <div class='dropdown-menu open-left arrow'>
                                              <a href='javascript:void(0);' class='dropdown-item clear'><i class='ft-rotate-cw primary'></i> Clear</a>                                         
                                            </div></span>";
                }

            })
            ->make(true);
    }
    public function receive_return_reason(Request $request){

        $status_id = $request->status;
        $statuses = ShipmentStatus::find($status_id)->reasons()->select('id','name')->orderBy('name')->get();

        if(!$statuses->isEmpty()){
            return response()->json(['status'=>0,'reasons'=>$statuses]);
        }else{
            return ['status'=>1,'error'=>'No reasons are defined for this status!'];
        }

    }
    public function receive_return_status_submit(Request $request){
        $shipments = explode(',',$request->shipment_ids);
        $return_note_id = $request->return_note_id;
        $array_returned = array(25,31,38);
        $array_returned_status = array(24,29,35,47,48);
        if($return_note_id != '') {
            foreach ($shipments as $shipment) {
                $reasonId = "reason_drop.$shipment";
                $parcel = Shipment::where('id', $shipment)->first();
                if (!in_array($parcel->shipper_status_id, $array_returned)) {
                    if (in_array($request->status_drop[$shipment],$array_returned_status)) {
                        if($request->status_drop[$shipment] != $parcel->shipper_status_id){
                            ShipmentsJourneyController::add($shipment, $request->status_drop[$shipment], NULL, ($request->has($reasonId) ? $request->reason_drop[$shipment] : null), $request->remarks[$shipment], NULL, Auth::id(), $return_note_id);

                            Shipment::where('id', $shipment)->update(['shipper_status_id' => $request->status_drop[$shipment]]);
                            ReturnNoteShipment::where(['return_note_id' => $return_note_id, 'shipment_id' => $shipment])->update(['status' => 1]);

                        }
                    }
//                else {
//                    ShipmentsJourneyController::add($shipment, $request->status_drop[$shipment], $request->status_drop[$shipment], ($request->has($reasonId) ? $request->reason_drop[$shipment] : null), $request->remarks[$shipment], NULL, Auth::id(), $return_note_id);
//
//                    Shipment::where('id', $shipment)->update(['shipper_status_id' => $request->status_drop[$shipment], 'consignee_status_id' => $request->status_drop[$shipment]]);
//                }

                }

            }
            $shipment_status = ReturnNoteShipment::where(['return_note_id'=>$return_note_id,'status'=>0])->count();
            if($shipment_status == 0){
                ReturnNote::where('id',$return_note_id)->update(['status' => 1 ,'updated_by' => Auth::id()]);
            }

            NotificationsController::send(15, $return_note_id);
            NotificationsController::send(16, $return_note_id);

            return redirect()->back()->with(['success'=>'Return Note Status Has Been Updated']);
        }
    }
    public function return_status_delivered(Request $request){
        if(!empty($request->shipment_ids)){
            foreach ($request->shipment_ids as $shipment){
                $parcel = Shipment::where('id',$shipment)->first();
                if($parcel->booking_type_id == 1 || $parcel->booking_type_id == 4 || $parcel->booking_type_id == 5){
                    ShipmentsJourneyController::add($shipment, 25, 25, NULL, ($request->has('remarks')? $request->remarks[$shipment]:null), NULL, Auth::id(),$request->return_note_id,NULL,1,($request->has('received_or_refused_by')? $request->received_or_refused_by[$shipment]:null));

                    Shipment::where('id',$shipment)->update(['shipper_status_id'=>25,'consignee_status_id'=>25]);
                    ReturnNoteShipment::where(['return_note_id'=>$request->return_note_id,'shipment_id'=>$shipment])->update(['status'=>1]);


                        $packaging_material_shipment = PackagingMaterialRequest::where('tracking_number', $parcel->tracking_number)->first();
                        if($packaging_material_shipment != null){
                            $request_id = $packaging_material_shipment->id;

                            $packaging_material_request = PackagingMaterialRequest::where('id',$request_id)->with('city')->first();

                            if($packaging_material_shipment->status_id == 3) {
                                $packaging_material_request_details = PackagingMaterialRequestDetail::where('packaging_material_request_id', $request_id)->get();

                                $hub_id = $packaging_material_request->city->hub_id;

                                $fulfilment_hub = WarehouseFulfilmentHubs::where('hub_id', $hub_id)->first();

                                $warehouse_id = $fulfilment_hub->warehouse_id;

                                foreach ($packaging_material_request_details as $detail_add) {
                                    $type_id = $detail_add->type_id;
                                    $type_size_id = $detail_add->type_size_id;
                                    $stock = WarehouseStock::where(['warehouse_id' => $warehouse_id, 'type_id' => $type_id, 'type_size_id' => $type_size_id]);

                                    $stock = $stock->first();
                                    $stock->stock = $stock['stock'] + $detail_add->quantity;
                                    $stock->save();
                                }
                            }
                            $packaging_material_request->status_id = 5;
                            $packaging_material_request->save();


                            $packaging_request_history = new PackagingMaterialRequestHistory();
                            $packaging_request_history->packaging_material_request_id = $request_id;
                            $packaging_request_history->status = 5;
                            $packaging_request_history->updated_by = Auth::id();
                            $packaging_request_history->save();
                        }

                }else if($parcel->booking_type_id == 2){
                    ShipmentsJourneyController::add($shipment, 31, 31, NULL, ($request->has('remarks')? $request->remarks[$shipment]:null), NULL, Auth::id(),$request->return_note_id,NULL,1,($request->has('received_or_refused_by')? $request->received_or_refused_by[$shipment]:null));

                    Shipment::where('id',$shipment)->update(['shipper_status_id'=>31,'consignee_status_id'=>31]);
                    ReturnNoteShipment::where(['return_note_id'=>$request->return_note_id,'shipment_id'=>$shipment])->update(['status'=>1]);

                }else if($parcel->booking_type_id == 3){
                    ShipmentsJourneyController::add($shipment, 38, 38, NULL, NULL, NULL, Auth::id(),$request->return_note_id,NULL,1,($request->has('received_or_refused_by')? $request->received_or_refused_by[$shipment]:null));

                    Shipment::where('id',$shipment)->update(['shipper_status_id'=>38,'consignee_status_id'=>38]);
                    ReturnNoteShipment::where(['return_note_id'=>$request->return_note_id,'shipment_id'=>$shipment])->update(['status'=>1]);

                }else{
                    ShipmentsJourneyController::add($shipment, 25, 25, NULL, ($request->has('remarks')? $request->remarks[$shipment]:null), NULL, Auth::id(),$request->return_note_id,NULL,1,($request->has('received_or_refused_by')? $request->received_or_refused_by[$shipment]:null));

                    Shipment::where('id',$shipment)->update(['shipper_status_id'=>25,'consignee_status_id'=>25]);
                    ReturnNoteShipment::where(['return_note_id'=>$request->return_note_id,'shipment_id'=>$shipment])->update(['status'=>1]);
                }
            }
            $shipment_status = ReturnNoteShipment::where(['return_note_id'=>$request->return_note_id,'status'=>0])->count();
            if($shipment_status == 0){
                ReturnNote::where('id',$request->return_note_id)->update(['status' => 1 ,'updated_by'=>Auth::id()]);
            }

            NotificationsController::send(15, $request->return_note_id);
            NotificationsController::send(16, $request->return_note_id);

            return ['status'=>0,'success'=>'Return note shipments status are updated to : Delivered to Shipper'];
        }else{
            return ['status'=>1,'error'=>'No shipments selected'];

        }
    }

    public function receive_return_status_submit_all(Request $request){

        if(!empty($request->shipment_ids)){
            $shipment_ids = $request->shipment_ids;
            $shipment_status = $request->shipment_status;
            if($shipment_status == 25){
                foreach ($shipment_ids as $shipment_id) {
                    $parcel = Shipment::where('id',$shipment_id)->first();
                    if($parcel->booking_type_id == 2){
                        ShipmentsJourneyController::add($shipment_id, 31, 31, NULL, ($request->has('remarks')? $request->remarks[$shipment_id]:null), NULL, Auth::id(),$request->return_note_id,NULL,1,($request->has('received_or_refused_by')? $request->received_or_refused_by[$shipment_id]:null));

                        Shipment::where('id',$shipment_id)->update(['shipper_status_id'=>31,'consignee_status_id'=>31]);

                    }else if($parcel->booking_type_id == 3){
                        ShipmentsJourneyController::add($shipment_id, 38, 38, NULL, NULL, NULL, Auth::id(),$request->return_note_id,NULL,1,($request->has('received_or_refused_by')? $request->received_or_refused_by[$shipment_id]:null));

                        Shipment::where('id',$shipment_id)->update(['shipper_status_id'=>38,'consignee_status_id'=>38]);

                    }else{
                        ShipmentsJourneyController::add($shipment_id, 25, 25, NULL, ($request->has('remarks')? $request->remarks[$shipment_id]:null), NULL, Auth::id(),$request->return_note_id,NULL,1,($request->has('received_or_refused_by')? $request->received_or_refused_by[$shipment_id]:null));

                        Shipment::where('id',$shipment_id)->update(['shipper_status_id'=>25,'consignee_status_id'=>25]);
                    }
                    ReturnNoteShipment::where(['return_note_id'=>$request->return_note_id,'shipment_id'=>$shipment_id])->update(['status'=>1]);
                }
                $shipment_status_count = ReturnNoteShipment::where(['return_note_id'=>$request->return_note_id,'status'=>0])->count();
                if($shipment_status_count == 0){
                    ReturnNote::where('id',$request->return_note_id)->update(['updated_by'=>Auth::id(),'status'=>1]);
                }

                NotificationsController::send(15, $request->return_note_id);
                NotificationsController::send(16, $request->return_note_id);
                return response()->json(['status'=> 0, 'success' => 'Return note shipments status are updated to : Delivered to Shipper']);
            }
            else{
                $remarks = $request->remarks;
                foreach ($shipment_ids as $shipment_id) {
                    ShipmentsJourneyController::add($shipment_id, $shipment_status, NULL, NULL, $remarks, NULL, Auth::id(), $request->return_note_id);

                    Shipment::where('id', $shipment_id)->update(['shipper_status_id' => $shipment_status]);
                    ReturnNoteShipment::where(['return_note_id' => $request->return_note_id, 'shipment_id' => $shipment_id])->update(['status' => 1]);

                }
                $shipment_status = ReturnNoteShipment::where(['return_note_id'=>$request->return_note_id,'status'=>0])->count();
                if($shipment_status == 0){
                    ReturnNote::where('id',$request->return_note_id)->update(['status' => 1 ,'updated_by'=>Auth::id()]);
                }

                NotificationsController::send(15, $request->return_note_id);
                NotificationsController::send(16, $request->return_note_id);
                return response()->json(['status'=> 0, 'success' => 'Return Note Status Has Been Updated']);
            }
        }
    }
    public function rrd_print(Request $request){

        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();

        $html = '
                <!doctype html>
                <html lang="en">
                  <head>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

                    <link rel="stylesheet" type="text/css" href="' . asset('app-assets/css/bootstrap.min.css') . '">

                    <title>Return Note</title>

                    <style>
                      @page {
                        size: A4 portrait;
                      }

                      * {
                        -webkit-print-color-adjust: exact !important;
                        color-adjust: exact !important;
                      }

                      body {
                        background: none !important;
                        color: #09262e !important;
                        font-size: 0.9rem !important;
                      }

                      hr {
                        border-top: 1px dashed #000000;
                      }

                      table.table-bordered {
                        page-break-inside: avoid;
                      }

                      table.table-bordered tbody tr td {
                        border: 1px solid #09262e !important;
                      }

                      .color.primary {
                        background: #c8c8c8 !important;
                      }

                      .color.secondary {
                        background: #ebebeb !important;
                      }

                      .border {
                        border: 1px solid #09262e !important;
                      }
                      td.complaint {
                            background: #09262e !important;
                            color: #ffffff;
                       }
                    </style>
                  </head>
                  <body>
                    <div>
      ';
        $return_note = ReturnNote::where('id',$request->id);
        if($return_note->exists()) {
            $total_shipments = 0;
            $shipment_ids = ReturnNoteShipment::where('return_note_id',$request->id)->select('shipment_id')->get();
            $filtered_shipments = Shipment::whereIn('id',$shipment_ids)->orderBy('id')->get();
            $shipment_details = '
                      <table class="table table-sm table-bordered border">
                        <tbody>
                          <tr>
                            <td class="color primary"><strong>S. No.</strong></td>
                            <td class="color primary"><strong>Tracking No.</strong></td>
                            <td class="color primary"><strong>Client Name & Phone No(s).</strong></td>
                            <td class="color primary"><strong>Contact Person</strong></td>
                            <td class="color primary"><strong>Contact Person Phone</strong></td>
                            <td class="color primary"><strong>Client Address</strong></td>
                            <td class="color primary"><strong>No. of Items</strong></td>
                            <td class="color primary"><strong>Collection Charges</strong></td>
                            <td class="color primary" style="width:200px;"><strong>Sign</strong></td>
                          </tr>
        ';


            foreach ($filtered_shipments as $shipment) {
                $total_shipments++;
                $class = null;
                if(CrmRequest::where('shipment_id',$shipment->id)->where('case_nature_id',1)->whereIn('status_id',[2, 3, 5])->exists()){
                    $class = 'complaint';
                }
                $shipment_details_row_start = '
                          <tr>
                            <td>' . $total_shipments . '</td>
                            <td class="'. $class .'">' . $shipment->tracking_number . '</td>
                            <td>' . $shipment->user->name . ' | ' . $shipment->user->phone . (($shipment->user->phone2) ? (' / ' . $shipment->user->phone2) : '') . '</td>
                            <td>' . $shipment->pickup_address->poc . '</td>
                            <td>' . $shipment->pickup_address->phone . '</td>
                            <td>' . $shipment->pickup_address->pickup_address . '</td>
                            <td>' . $shipment->items->sum('quantity') . '</td>
                ';

                if ($shipment->booking_type_id != 4) {
                    $shipment_details_row_start .= '
                            <td></td>
                    ';
                }
                else {
                    if ($shipment->charges_mode_id == 1) {
                        $shipment_details_row_start .= '
                            <td>' . $shipment->return_charges . '</td>
                        ';
                    }
                    else {
                        $shipment_details_row_start .= '
                            <td>' . number_format($shipment->amount) . '</td>
                        ';
                    }
                }

                $shipment_details_row_start .= '
                            <td></td>

                          </tr>
            ';

                $shipment_details .= $shipment_details_row_start;
            }
            $shipment_details .= '
                        </tbody>
                      </table>
        ';
            $return_note_details = ReturnNote::where('id',$request->id)->first();
            $rider = Rider::where('id',$return_note_details->rider_id)->first();
            $city_name = $return_note_details->hub->name;
            $rider_name = $rider->name;
            $category = $rider->rider_category->name;
            $route_name = $return_note_details->route->code .' ('.$return_note_details->route->start.' to '.$return_note_details->route->end.')';
            $main_details = '
                      <table class="table table-sm table-bordered border">
                        <tbody>
                          <tr>
                            <td class="text-center align-middle"><img src="' . asset('img/trax_logo.png') . '" width="150" class="d-block mx-auto"></td>
                            <td class="text-center align-middle color primary"><strong>Return Note</strong></td>
                            <td class="text-center align-middle color secondary">Created at ' . $return_note_details->created_at . '</br> by ' . ucfirst($return_note_details->admin->name) . '</td>
                            <td class="text-center align-middle color secondary">Printed at ' . Carbon::now() . '</br> by ' . ucfirst(Auth::user()->name) . '</td>
                          </tr>
                         
                          <tr>
                            <td class="color secondary"><strong>Rider Name</strong></td>
                            <td>' . $rider_name . '</td>
                            <td colspan="2" rowspan="7" class="pl-1 pr-1 text-center align-middle">
                              <img src="data:image/png;base64,' . base64_encode($generator->getBarcode(str_pad($request->id, 6, '0', STR_PAD_LEFT), $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                              <span><strong>' . str_pad($request->id, 6, '0', STR_PAD_LEFT) . '</strong></span>
                            </td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Category</strong></td>
                            <td>' . $category . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Route</strong></td>
                            <td>' . $route_name . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>City</strong></td>
                            <td>' . $city_name  . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Total Shipments</strong></td>
                            <td>' . $total_shipments . '</td>
                          </tr>
                        
                        </tbody>
                      </table>
        ';
            $html .= $main_details;
            $html .= $shipment_details;

        }

//        return $shipments;


        $html .= '
                    </div>

                    <script>
                      window.onload = function() {
                        window.print();
                      }
                    </script>
                  </body>
                </html>
      ';

        return $html;


    }

    public function return_confirmed_revert(Request $request) {
        $shipment = Shipment::find($request->id);

        if ($shipment->shipper_status_id == 20) {
            $shipment->shipper_status_id = 13;
            $shipment->consignee_status_id = 13;

            $shipment->save();

            $journey = ShipmentsJourney::where('shipment_id',$shipment->id)->where('shipper_status_id', 20)->latest()->first();
            if($journey){
                $return_reattempt = new ReturnReattemptRatio();
                $return_reattempt->shipment_id = $shipment->id;
                $return_reattempt->return_confirm_date = $journey->created_at;
                $return_reattempt->save();
            }


            ShipmentsJourneyController::add($request->id, 13, 13, NULL, $request->remarks, NULL, Auth::id());

            AdminFinanceController::return_confirmed_revert($request->id);

            return ['status' => 0, 'success' => 'Shipment has been Reverted'];
        }
        else {
            return ['status' => 1, 'error' => 'Shipment has already been Reverted'];
        }
    }
    public function receive_return_shipments(Request $request){
        $return_note_id = $request->input('return_note_id');
        $return_note_details = ReturnNote::find($return_note_id);
        $return_note_shipments = $return_note_details->return_note_shipments;
        $shipments = array();
        if($return_note_shipments->count() != 0){
            foreach ($return_note_shipments as $return_note_shipment){
                $shipment = Shipment::find($return_note_shipment->shipment_id);
                $shipments[] = $shipment->tracking_number;
            }
            return ['status' => 0, 'success' => 'Return Note Shipments', 'shipments' => $shipments];
        }else{
            return ['status' => 0, 'success' => 'No Return Note Shipments', 'shipments' => FALSE];
        }
    }
    public function history_index(){
        return view('admin.return.history');
    }
    public function history_list(Request $request){
        $deliveries = ReturnNote::
        join('cities AS oc', 'return_notes.hub_id', '=', 'oc.id')
            ->join('riders', 'return_notes.rider_id', '=', 'riders.id')
            ->join('admins','admins.id','=','return_notes.admin_id')
            ->join('admins as sb','sb.id','=','return_notes.updated_by')
            ->select(['return_notes.id as return_note','return_notes.id as return_note_id','oc.name as hub','riders.name as rider','admins.name as assigned_by','return_notes.created_at','return_notes.shipments_count','return_notes.shipments_count as shipments_count_link','return_notes.status','sb.name as submitted_by','return_notes.updated_at','return_notes.updated_at as submitted_at','return_notes.image']);

        if (session('role_id') != 1) {
            $deliveries = $deliveries->whereIn('oc.hub_id', session('hubs'));
        }

        $datatables = Datatables::of($deliveries)
            ->editColumn('return_note', function ($deliveries) {
                return "<a href='javascript:void(0);' class='printreturnnote'><u>" . str_pad($deliveries->return_note, 6, '0', STR_PAD_LEFT) . "</u></a>";
            })
            ->addColumn('return_note_id_padded', function ($deliveries) {
                return str_pad($deliveries->return_note_id, 6, '0', STR_PAD_LEFT);
            })
            ->editColumn('image', function ($deliveries) {
                $now = Carbon::now();
                if ($deliveries->image != null && ($now->diffInDays($deliveries->updated_at) < 30)) {
                    $img = asset('uploads/return_notes/' . $deliveries->image);
                    return "<a href='{$img}' class='btn btn-block btn-outline-info mr-1' target='_blank'><i class='la la-image'></i></a>";

                } else {
                    return "-";
                }

            })
            ->editColumn('shipments_count_link', function($deliveries) {
                if ($deliveries->shipments_count != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $deliveries->shipments_count . '</button>';
                }
                else {
                    return 0;
                }
            })
            ->filterColumn('return_notes.id', function ($query, $keyword) {
                return $query->where('return_notes.id', '=', $keyword);
            })

            ->addColumn('main_status', function($deliveries) {
                if($deliveries->status == 0){
                    return 'Pending for Update';
                }else if($deliveries->status == 1){
                    return 'Verified';
                }else if($deliveries->status == 2){
                    return 'Canceled';
                }
            })
            ->filterColumn('main_status',function ($query,$keyword){
                if($keyword == 0){
                    $query->where('return_notes.status',0);
                }else if($keyword == 1){
                    $query->where('return_notes.status',1);
                }else if($keyword == 2){
                    $query->where('return_notes.status',2);
                }
            });
        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $datatables->whereBetween('return_notes.created_at', [$from,$to]);
        }

        return $datatables->make(true);
    }
    public function history_shipments(Request $request){
        $return_note_id = $request->input('return_note_id');
        $return_note_details = ReturnNote::find($return_note_id);
        $return_note_shipments = $return_note_details->return_note_shipments;
        $shipments = array();
        if($return_note_shipments->count() != 0){
            foreach ($return_note_shipments as $return_note_shipment){
                $shipment = Shipment::find($return_note_shipment->shipment_id);
                $shipments[] = $shipment->tracking_number;
            }
            return ['status' => 0, 'success' => 'Return Note Shipments', 'shipments' => $shipments];
        }else{
            return ['status' => 0, 'success' => 'No Return Note Shipments', 'shipments' => FALSE];
        }
    }

    public function receive_return_note_image_upload(Request $request){

        $return_note_id = $request->image_return_note_id;
        $return_note_image = $request->return_note_image;

        $messages = [
            'return_note_image.required' => 'No Image file selected!.',
            'return_note_image.mimes' => 'Image file not supported!.',
            'return_note_image.size' => 'Image file size exceded!.',
        ];
        $validation = [
            'return_note_image' => 'required | mimes:jpeg,png,jpg | max:2048',
        ];
        $validate = Validator::make($request->all(), $validation, $messages);

        if ($validate->fails()) {
            return response()->json(['status' => 0, 'error' => $validate->errors()]);
        }

        if($return_note_id){

            $image = $request->file('return_note_image');
            $imageName = $image->getClientOriginalName();

            //$imageName = explode('.', $imageName);
            $extension = $image->getClientOriginalExtension();
            $random = rand(1000, 100000);
            $now = Carbon::now();
            $time = $now->year . '_' . $now->month;
            $generated_image_name = $time . $random . Auth::id() . '.' . $extension;
            $image->move(public_path('uploads/return_notes'), $generated_image_name);

            $imageUpload = ReturnNote::find($return_note_id);
            $imageUpload->image = $generated_image_name;
            $imageUpload->updated_by = Auth::id();
//            $imageUpload->status = 1;
            $imageUpload->save();


            return redirect()->back()->with(['status' => 1, 'success' => 'Return Note updated successfully']);
        }
        else{
            return redirect()->back()->with(['status' => 0, 'error' => 'Return Note Not found!']);
        }
    }

    static public function archive_directory(){
        $files = File::glob(public_path() . '/uploads/return_notes/*.*');
        $now = Carbon::now();
        foreach ($files as $file) {
            if (is_file($file)) {
               $created = date("F d Y H:i:s.",filemtime($file));
                $file_name = pathinfo($file);
               if($now->diffInDays($created) > 30){
                   Storage::disk('s3')->put( 'return_note_images/'.$file_name['basename'], file_get_contents($file));
                   File::delete($file);
               }
            }
        }
    }
}
