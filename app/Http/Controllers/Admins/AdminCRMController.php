<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\CRM\CRMCommentController;
use App\Http\Controllers\CRM\CRMController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\ShipmentsAirWaybillJourneyController;
use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\AdminDepartment;
use App\Http\Models\Admin\AdminHub;
use App\Http\Models\Admin\AdminRole;
use App\Http\Models\Admin\ChangeShipmentAmountLog;
use App\Http\Models\Admin\KeyAccountDailyShipmentCrm;
use App\Http\Models\Admin\KeyAccountDailySummaryCrm;
use App\Http\Models\Admin\KeyAccountPendingCrm;
use App\Http\Models\Admin\KeyAccountPendingSummaryCrm;
use App\Http\Models\Admin\Retail\RetailUser;
use App\Http\Models\Admin\RevertStatusRequest;
use App\Http\Models\Admin\SalePersonTag;
use App\Http\Models\City;
use App\Http\Models\CityDelivery;
use App\Http\Models\CRM\CrmComments;
use App\Http\Models\CRM\CrmConsigneeInfoPrint;
use App\Http\Models\CRM\CrmPaymentShipment;
use App\Http\Models\CRM\CrmRequest;
use App\Http\Models\CRM\CrmRequestAgentHistory;
use App\Http\Models\CRM\CrmRequestCaseNatureAndTypeHistory;
use App\Http\Models\CRM\CrmRequestEscalationTagging;
use App\Http\Models\CRM\CrmRequestImage;
use App\Http\Models\CRM\CrmRequestRating;
use App\Http\Models\CRM\CrmRequestStatus;
use App\Http\Models\CRM\CrmRequestStatusHistory;
use App\Http\Models\CRM\CrmRequestTagging;
use App\Http\Models\CRM\CrmRequestTaggingHistory;
use App\Http\Models\CRM\CrmRequestTaggingTypes;
use App\Http\Models\CRM\CrmSettings;
use App\Http\Models\CRM\CrmTatHolidays;
use App\Http\Models\CRM\DelayInDeliveryShipment;
use App\Http\Models\CRM\Escalation\CrmEscalationLevel;
use App\Http\Models\CRM\Escalation\CrmEscalationTagging;
use App\Http\Models\CRM\Escalation\CrmEscalationTaggingLevel;
use App\Http\Models\CRM\Escalation\CrmRequestEscalationLog;
use App\Http\Models\CRM\Escalation\CrmRequestEscalationStatus;
use App\Http\Models\DonePayment;
use App\Http\Models\DonePaymentShipment;
use App\Http\Models\InterceptReBookRequestHistory;
use App\Http\Models\SaleTierTag;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentInvoice;
use App\Http\Models\ShipmentItem;
use App\Http\Models\ShipmentsJourney;
use App\Http\Models\ShipmentStatus;
use App\Http\Models\Shipper\ShipperAirWaybillSettings;
use App\Http\Models\Shipper\SubstituteUser;
use App\Http\Models\Shipper\User;
use App\Http\Models\Zone;
use App\SpecialApprovalRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Models\CRM\CrmRequestCaseNature;
use App\Http\Models\CRM\CrmRequestCaseNatureType;
use App\Http\Models\CRM\CrmRequestChannel;
use App\Http\Models\Admin\AdminRoleModulePermission;
use App\Http\Models\Admin\Module;
use App\Http\Models\Admin\ModulePermission;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use phpDocumentor\Reflection\Types\Null_;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Admins\ActivityTrailController;
use App\Http\Models\Admin\CrmAutoTagUser;
use App\Http\Models\Admin\Retail\RetailFranchise;
use App\Http\Models\Admin\Retail\RetailTraxCenter;
use App\Http\Models\ShipmentDetail;

class AdminCRMController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function add_request(Request $request){
        $nature_id = $request->case_nature_id;
        $complaint_id = $request->complaint_id;
        $channel_id = $request->channel_id;
        $receiving_sheet_id = $request->receiving_sheet_id;
//        if($complaint_id == 23 && $receiving_sheet_id != null){
        if($complaint_id == 23){
            $description_text = $request->description ;
//            $description = '<strong>' .'Receiving Sheet No: ' .$receiving_sheet_id. '</strong>'. PHP_EOL. $description_text;
            $description = $description_text;
        }
        else{
            $description = $request->description;
        }
        $flag = false;
        $cannot_change = false;
        $present_shipments = array();
        if ($request->has('payment_request')) {
            if($request->payment_request == 1){
                $payment_id = $request->payment_id;
                $payment_id_padded = str_pad($request->payment_id, 6, 0, STR_PAD_LEFT);
                if(!empty($payment_id)){
                    $payment = DonePayment::find($payment_id);
                    $payment_shipment = DonePaymentShipment::where('done_payment_id', $payment->id)->first();
                    $shipment = Shipment::where('id', $payment_shipment->shipment_id)->first();
                    $is_shipment = CrmRequest::where('shipment_id',$shipment->id)->where('case_nature_id', $nature_id);
                    if($is_shipment->exists()){
                        return ['status' => 0, 'error' => 'Request/Complaint already lodged for the Payment ID: ' . $payment_id_padded];
                    }
                    else{
                        $crm_request_padded_id = CRMController::add($nature_id, $complaint_id, $channel_id, 1, Auth::id(), 0, $shipment->id, $shipment->user_id, NULL ,$description);
                        if($request->has('key_account')){
                            $this->key_account_crm_summary_shipments($shipment->id, $crm_request_padded_id, Auth::id(), $channel_id, $complaint_id);
                        }
                    }
                    $crm_request_padded_id = str_pad($crm_request_padded_id, 6, 0, STR_PAD_LEFT);

                    return ['status' => 1, 'success' => 'Request ('. $crm_request_padded_id .') successfully added'];
                }else{
                    return ['status' => 0, 'error' => 'No Payment selected!'];
                }
            }
        }
        else{
            if ($request->has('shipment_ids')) {
                if ($nature_id == 4) {
                    $shipment_ids = explode(',', $request->input('shipment_ids'));
                }
                else{
                    $shipment_ids = $request->shipment_ids;
                }
                if(!empty($shipment_ids)){
                    foreach ($shipment_ids as $shipment_id) {
                        $shipment = Shipment::find($shipment_id);
                        if($shipment){

                            $is_shipment = CrmRequest::where('shipment_id',$shipment_id)->where('case_nature_id',$nature_id)->first();
                            if($is_shipment){
                                $complain = $is_shipment->id;
                                if($is_shipment->case_nature_id != $nature_id){
                                    if ($nature_id == 4) {
                                        if($complaint_id == 26){
                                            $crm_request_padded_id = CRMController::add($nature_id, $complaint_id, $channel_id, 1, Auth::id(), 0, $shipment_id, $shipment->user_id, NULL , $description);
                                        }
                                        else{
                                            $crm_request_padded_id = CRMController::add($nature_id, $complaint_id, $channel_id, 1, Auth::id(), 0, $shipment_id, $shipment->user_id, NULL , $description, $request->product_cost,  $request->file('product_picture'), $request->file('invoice_picture'));
                                        }
                                        if($request->has('key_account')){
                                            $this->key_account_crm_summary_shipments($shipment->id, $crm_request_padded_id, Auth::id(), $channel_id, $complaint_id);
                                        }
                                    }
                                    else{
                                        if($shipment->shipper_status_id == 20 || $shipment->shipper_status_id == 1){
                                            if(in_array($complaint_id, [11, 12, 13])){
                                                $present_shipments[] = $shipment->tracking_number;
                                                $flag = true;
                                                $cannot_change = true;
                                            }
                                            else{
                                                $crm_request_padded_id = CRMController::add($nature_id, $complaint_id, $channel_id, 1, Auth::id(), 0, $shipment_id, $shipment->user_id, NULL ,$description);
                                                if($request->has('key_account')){
                                                    $this->key_account_crm_summary_shipments($shipment->id, $crm_request_padded_id, Auth::id(), $channel_id, $complaint_id);
                                                }
                                            }
                                        }
                                        else{
                                            $crm_request_padded_id = CRMController::add($nature_id, $complaint_id, $channel_id, 1, Auth::id(), 0, $shipment_id, $shipment->user_id, NULL ,$description);
                                            if($request->has('key_account')){
                                                $this->key_account_crm_summary_shipments($shipment->id, $crm_request_padded_id, Auth::id(), $channel_id, $complaint_id);
                                            }
                                        }
                                    }
                                }else{
                                    $present_shipments[] = $shipment->tracking_number;
                                    $present_shipments[] = 'Complaint ID: '. $complain;
                                    $flag = true;
                                }
                            }
                            else{
                                if ($nature_id == 4) {
                                    if ($complaint_id == 21 || $complaint_id == 22) {
                                        $crm_request_padded_id = CRMController::add($nature_id, $complaint_id, $channel_id, 1, Auth::id(), 0, $shipment_id, $shipment->user_id, NULL, $description, $request->product_cost, $request->file('product_picture'), $request->file('invoice_picture'), $request->file('damage_product_picture'), $request->file('product_packaging_picture'), $request->file('actual_product_picture'), $request->damage_claim_product_cost, $request->file('missing_product_picture'), $request->file('product_packaging_picture_content_short'), $request->file('actual_product_picture_content_short'), $request->claim_content_product_cost);
                                    }
                                    else {
                                    $crm_request_padded_id = CRMController::add($nature_id, $complaint_id, $channel_id, 1, Auth::id(), 0, $shipment_id, $shipment->user_id, NULL , $description, $request->product_cost,  $request->file('product_picture'), $request->file('invoice_picture'));
                                    }
                                    if($request->has('key_account')){
                                        $this->key_account_crm_summary_shipments($shipment->id, $crm_request_padded_id, Auth::id(), $channel_id, $complaint_id);
                                    }
                                }
                                else{
                                    if($shipment->shipper_status_id == 20 || $shipment->shipper_status_id == 1){
                                        if(in_array($complaint_id, [11, 12, 13])){
                                            $present_shipments[] = $shipment->tracking_number;
                                            $flag = true;
                                            $cannot_change = true;
                                        }
                                        else{
                                            $crm_request_padded_id = CRMController::add($nature_id, $complaint_id, $channel_id, 1, Auth::id(), 0, $shipment_id, $shipment->user_id, NULL ,$description);
                                            if($request->has('key_account')){
                                                $this->key_account_crm_summary_shipments($shipment->id, $crm_request_padded_id, Auth::id(), $channel_id, $complaint_id);
                                            }
                                        }
                                    }
                                    else{
                                        $crm_request_padded_id = CRMController::add($nature_id, $complaint_id, $channel_id, 1, Auth::id(), 0, $shipment_id, $shipment->user_id, NULL ,$description);
                                        if($request->has('key_account')){
                                            $this->key_account_crm_summary_shipments($shipment->id, $crm_request_padded_id, Auth::id(), $channel_id, $complaint_id);
                                        }
                                    }
                                }
                            }
                        }
                    }
                    return ['status' => 1, 'success' => 'Request(s) successfully added', 'flag' => $flag, 'already_existed_shipments' => $present_shipments, 'cannot_change' => $cannot_change];
                }else{
                    return ['status' => 0, 'error' => 'No shipments selected!'];
                }
            }
            else{
                $shipment_id = $request->shipment_id;
                if(!empty($shipment_id)){
                    $shipment = Shipment::find($shipment_id);
                    if($shipment){
                        $is_shipment = CrmRequest::where('shipment_id',$shipment_id)->first();
                        $is_shipment = CrmRequest::where('shipment_id',$shipment_id)->where('case_nature_id',$nature_id)->first();
                        if($is_shipment){
                            $complain = $is_shipment->id;
                            if($is_shipment->case_nature_id != $nature_id){
                                if($shipment->shipper_status_id == 20 || $shipment->shipper_status_id == 1){
                                    if(in_array($complaint_id, [11, 12, 13])){
                                        $present_shipments[] = $shipment->tracking_number;
                                        $flag = true;
                                        $cannot_change = true;
                                    }
                                    else{
                                        $crm_request_padded_id = CRMController::add($nature_id, $complaint_id, $channel_id, 1, Auth::id(), 0, $shipment_id, $shipment->user_id, NULL ,$description);
                                        if($request->has('key_account')){
                                            $this->key_account_crm_summary_shipments($shipment->id, $crm_request_padded_id, Auth::id(), $channel_id, $complaint_id);
                                        }
                                        $crm_request_padded_id = str_pad($crm_request_padded_id, 6, 0, STR_PAD_LEFT);
                                        return ['status' => 1, 'success' => 'Request ('. $crm_request_padded_id .') successfully added', 'flag' => $flag, 'already_existed_shipments' => $present_shipments];
                                    }
                                }
                                else{
                                    $crm_request_padded_id = CRMController::add($nature_id, $complaint_id, $channel_id, 1, Auth::id(), 0, $shipment_id, $shipment->user_id, NULL ,$description);
                                    if($request->has('key_account')){
                                        $this->key_account_crm_summary_shipments($shipment->id, $crm_request_padded_id, Auth::id(), $channel_id, $complaint_id);
                                    }
                                    $crm_request_padded_id = str_pad($crm_request_padded_id, 6, 0, STR_PAD_LEFT);
                                    return ['status' => 1, 'success' => 'Request ('. $crm_request_padded_id .') successfully added', 'flag' => $flag, 'already_existed_shipments' => $present_shipments];
                                }
                            }else{
                                $present_shipments[] = $shipment->tracking_number;
                                $present_shipments[] = 'Complaint ID: '. $complain;
                                $flag = true;
                            }
                        }else{
                            if($shipment->shipper_status_id == 20 || $shipment->shipper_status_id == 1){
                                if(in_array($complaint_id, [11, 12, 13])){
                                    $present_shipments[] = $shipment->tracking_number;
                                    $flag = true;
                                    $cannot_change = true;
                                }
                                else{
                                    $crm_request_padded_id = CRMController::add($nature_id, $complaint_id, $channel_id, 1, Auth::id(), 0, $shipment_id, $shipment->user_id, NULL ,$description);
                                    if($request->has('key_account')){
                                        $this->key_account_crm_summary_shipments($shipment->id, $crm_request_padded_id, Auth::id(), $channel_id, $complaint_id);
                                    }
                                    $crm_request_padded_id = str_pad($crm_request_padded_id, 6, 0, STR_PAD_LEFT);
                                    return ['status' => 1, 'success' => 'Request ('. $crm_request_padded_id .') successfully added', 'flag' => $flag, 'already_existed_shipments' => $present_shipments];
                                }
                            }
                            else{
                                $crm_request_padded_id = CRMController::add($nature_id, $complaint_id, $channel_id, 1, Auth::id(), 0, $shipment_id, $shipment->user_id, NULL ,$description);
                                if($request->has('key_account')){
                                    $this->key_account_crm_summary_shipments($shipment->id, $crm_request_padded_id, Auth::id(), $channel_id, $complaint_id);
                                }
                                $crm_request_padded_id = str_pad($crm_request_padded_id, 6, 0, STR_PAD_LEFT);
                                return ['status' => 1, 'success' => 'Request ('. $crm_request_padded_id .') successfully added', 'flag' => $flag, 'already_existed_shipments' => $present_shipments];
                            }
                        }
                    }
                    return ['status' => 1, 'success' => 'Request(s) successfully added', 'flag' => $flag, 'already_existed_shipments' => $present_shipments, 'cannot_change' => $cannot_change];
                }else{
                    return ['status' => 0, 'error' => $request->shipment_id];
                }
            }
        }

    }

//    public function update_request(Request $request){
//        $request_id = $request->request_id;
//        $nature_id = $request->case_nature_id;
//        $complaint_id = $request->complaint_id;
//        $channel_id = $request->channel_id;
//        if($request_id != null){
//            $crm_request = CrmRequest::find($request_id);
//            $crm_request->case_nature_id = $nature_id;
//            $crm_request->case_nature_type_id = $complaint_id;
//            $crm_request->channel_id = $channel_id;
//            $crm_request->save();
//            return ['status' => 1, 'success' => 'Request successfully updated!'];
//        }
//        return ['status' => 0, 'error' => 'Request not found!'];
//
//    }
    public function add_feedback(Request $request){
        $nature_id = 3;
        $channel_id = $request->channel_id;
        $description = $request->description;
        $shipment_ids = $request->shipment_ids;
        $flag = false;
        $present_shipments = null;
        if($shipment_ids != null) {
            foreach ($shipment_ids as $shipment_id) {
                if (session('user_type') == 2) {
                    $launched_by = 2;
                }
                if ($channel_id == null) {
                    return ['status' => 0, 'error' => 'Channel Not selected!'];
                }
                if ($description == null) {
                    return ['status' => 0, 'error' => 'Description Not Entered!'];
                }
                $shipment = Shipment::find($shipment_id);
                if($shipment) {
                    $is_shipment = CrmRequest::where('shipment_id', $shipment_id)->where('case_nature_id', $nature_id)->first();
                    if (!$is_shipment) {
                        CRMController::add($nature_id, NULL, $channel_id, 1, Auth::id(), 0, $shipment_id, session('user_id'), NULL, $description);
                    } else {
                        $present_shipments[] = $shipment->tracking_number;
                        $flag = true;
                    }
                }else{
                    CRMController::add($nature_id, null, $channel_id, 1, Auth::id(), 0, $shipment_id, session('user_id'), NULL, $description);
                }
            }
        }
        else {
            if ($channel_id == null) {
                return ['status' => 0, 'error' => 'Channel Not selected!'];
            }
            if ($description == null) {
                return ['status' => 0, 'error' => 'Description Not Entered!'];
            }

            CRMController::add($nature_id, NULL, $channel_id, 1, Auth::id(), 0, NULL, NULL, NULL, $description);
        }
        return ['status' => 1, 'success' => 'Feedback successfully added', 'flag' => $flag, 'already_existed_shipments' => $present_shipments];

    }

    public function request_details(Request $request,$id){
        $crm_request = CrmRequest::find($id);
        if($crm_request){
            $shipment_status = null;
            $shipment_status_date = null;
            $shipper = null;
            $arrival_date = '';
            $insurance = 'No';
            if($crm_request->shipment_id != null) {
                $shipment_status = Shipment::find($crm_request->shipment_id);
                $shipment_status = $shipment_status->status_shipper->name;
                $journey = ShipmentsJourney::where('shipment_id', $crm_request->shipment_id)->where('shipper_status_id', '=', 2);
                if($journey->exists()){
                    $journey = $journey->latest()->first();
                    $arrival_date = $journey->created_at;
                }
                $shipment_status_journey = ShipmentsJourney::where('shipment_id', $crm_request->shipment_id)->latest('id')->first();
                $shipment_status_date = $shipment_status_journey->created_at;




                $product_insurance = ShipmentItem::where('shipment_id',$crm_request->shipment_id);
                if($product_insurance->exists()){
                    $product = $product_insurance->first();
                    $insurance_check = $product->insurance;
                    if($insurance_check == 1){
                        $insurance = 'Yes';
                    }
                    else{
                        $insurance = 'No';
                    }
                }
                else{
                    $insurance = 'No';
                }

            }

            if($crm_request->shipper_id != null){
                $shipper = User::find($crm_request->shipper_id);
                $shipper = $shipper->name;
            }
            $admins = AdminRole::leftjoin('admins as a', 'a.role_id', '=', 'admin_roles.id' )
                ->select('a.id as id', 'a.name as name')
                ->whereNotIn('admin_roles.department_id', [1,3])->get();
            $types = CrmRequestTaggingTypes::get();
            $departments = AdminDepartment::whereNotIn('id', [1,3])->get();
            $hubs = City::where('hub', 1)->get();
            $tagged = CrmRequestTagging::where('crm_request_id', $crm_request['id'])->whereIn('crm_request_tagging_type_id', [1,2])->first();
            $tagged_name = '';
            $tag_check = '';
            $tag_permission = '';
            if($tagged){
                if($tagged['crm_request_tagging_type_id'] == 1){
                    $tag_check = Auth::user()->role_id;
                    $tag = AdminRole::where('id', $tag_check)->first();
                    $tag_permission = $tag->department_id;
                    $tagged_name = AdminDepartment::find($tagged['tagged_id'])->name;
                }
                else if($tagged['crm_request_tagging_type_id'] == 2){
                    $tagged_name = Admin::find($tagged['tagged_id'])->name;
                }
            }
            
            $tagged_kae = CrmRequestTagging::where('crm_request_id', $crm_request['id'])->where('crm_request_tagging_type_id', 4)->get()->first();
            if($tagged_kae){
                $tagged_kae_name = Admin::find($tagged_kae->tagged_id)->name;
            }else{
                $tagged_kae_name = '';
            }
            $tagged_operation = CrmRequestTagging::where('crm_request_id', $crm_request['id'])->where('crm_request_tagging_type_id', 5)->get()->first();
            if($tagged_operation){
                $tagged_operation_name = Admin::find($tagged_operation->tagged_id)->name;
            }else{
                $tagged_operation_name = '';
            }
            $escalation_tagged = CrmRequestEscalationTagging::where('crm_request_id', $crm_request['id'])
                ->where('role_id', session('role_id'))
                ->where(function ($sub_sub_query) {
                    $sub_sub_query->whereNull('hub_id')
                        ->orWhereNotNull('hub_id')
                        ->whereIn('hub_id', session('hubs'));
                });
            if($escalation_tagged->exists()){
                $escalation_tagged_check = true;
            }
            else{
                $escalation_tagged_check = false;
            }
            $agent = Admin::where('id', $crm_request['agent_id'])->first();
            $agent_name = '';
            if($agent){
                $agent_name = $agent['name'];
            }

            $crm_comments = array();
            $last_comment = null;
            $crm_comments = CrmComments::where('crm_request_id', $id);
            if($crm_comments->exists()){
                $crm_comments = $crm_comments->orderBy('created_at','asc')->get();
                $last_comment = CrmComments::where('crm_request_id', $id)->latest()->first();
                $last_comment = $last_comment->id;
            }

            $launched_by  = '';
            if($crm_request->launched_by == 0){
                $launched_by = $launched_by = $crm_request->launched_by_admin->name."(Admin)";
            }else if($crm_request->launched_by == 1){
                $launched_by = User::find($crm_request->launched_by_id)->name;
            }else if($crm_request->launched_by == 2){
                $launched_by = SubstituteUser::find($crm_request->launched_by_id)->name;
            }else if($crm_request->launched_by == 3){
                $launched_by = RetailUser::find($crm_request->launched_by_id)->name;
            }
            $crm_tagging = array();
            $crm_tagging_details = CrmRequestTagging::where('crm_request_id', $id)->first();
            if($crm_tagging_details){
                $crm_tagging = $crm_tagging_details;
            }
            $crm_agent_history = CrmRequestAgentHistory::where('crm_request_id', $id)->get();
            $crm_request_ids = array();
            if($crm_request->shipment_id){
                $all_crm_request_ids_for_shipment = CrmRequest::where('shipment_id',$crm_request->shipment_id)->pluck('id')->toArray();
                $crm_request_ids = $all_crm_request_ids_for_shipment;
            }else{
                $crm_request_ids = [$id];
            }
            $crm_status_history = CrmRequestStatusHistory::whereIn('crm_request_id', $crm_request_ids)->get();
            $crm_tagging_history = CrmRequestTaggingHistory::where('crm_request_id', $id)->get();
            $case_nature = CrmRequestCaseNature::where('id', '!=', 3)->get();
            $case_nature_type_complaints = CrmRequestCaseNatureType::where('nature_id', '=', 1)->where('status_id',1)->get();
            $case_nature_type_service_requests = CrmRequestCaseNatureType::where('nature_id', '=', 2)->where('status_id',1)->get();
            $case_nature_type_claims = CrmRequestCaseNatureType::where('nature_id', '=', 4)->where('status_id',1)->get();
            $crm_histories = CrmRequestCaseNatureAndTypeHistory::join('crm_request_case_nature as a','a.id','=','crm_request_case_nature_and_type_histories.case_nature_id')
            ->join('crm_request_case_nature_types as b','b.id','=','crm_request_case_nature_and_type_histories.case_nature_type_id')
            ->where('crm_request_case_nature_and_type_histories.crm_request_id',$id)
            ->select('a.name as casenature','b.type as casenaturetype')->get();

            $sale_person = SalePersonTag::where('user_id', $crm_request->shipper_id)->where('status', 0)->first();

            $crm_escalation_tagging_history = CrmRequestEscalationTagging::where('crm_request_id', $id)->get();

            $escalation_status_flag = true;
            $escalation_log_flag = false;
            $escalation_tagging_id = NULL;
            $crm_escalation_levels = NULL;
            $crm_request_escalation_status = CrmRequestEscalationStatus::where('crm_request_id', $crm_request->id);
            if($crm_request_escalation_status->exists()){
                $crm_request_escalation_status = $crm_request_escalation_status->first();
                if($crm_request_escalation_status->status == 0){
                    $escalation_status_flag = false;
                }
            }
            $crm_request_escalation_log = CrmRequestEscalationLog::where('crm_request_id', $crm_request->id);
            if($crm_request_escalation_log->exists()){
                $crm_request_escalation_log = $crm_request_escalation_log->latest()->first();
                $previous_levels = CrmRequestEscalationLog::where('crm_request_id', $crm_request->id)->where('escalation_tagging_id', $crm_request_escalation_log->escalation_tagging_id)->pluck('level_id')->toArray();
                $crm_request_escalation_tagging = CrmEscalationTaggingLevel::where('escalation_tagging_id', $crm_request_escalation_log->escalation_tagging_id)->whereNotIn('level_id', $previous_levels)->pluck('level_id')->toArray();
                    $crm_escalation_levels = CrmEscalationLevel::whereIn('id', $crm_request_escalation_tagging)->get();
                    if(count($crm_escalation_levels) > 0){
                        $escalation_log_flag = true;
                        $escalation_tagging_id = $crm_request_escalation_log->escalation_tagging_id;
                    }
            }

            $crm_images_count = $crm_request->images->count();

            $approvers = array();
            $special_request = SpecialApprovalRequest::join('admins as a','a.id','=','special_approval_requests.admin_id')
               ->where('special_approval_requests.crm_request_id',$id)->where('special_approval_requests.status',1)->select('a.name as admin')->get();
            foreach($special_request as $admin_request){
                $approvers[] = $admin_request->admin;
            }

            $ratings = CrmRequestRating::all();

            return view('admin.crm.request_details')->with(['tagged_kae_name' => $tagged_kae_name, 'tagged_operation_name' => $tagged_operation_name, 'crm_histories' => $crm_histories,'crm_historiescount' => $crm_histories->count(), 'crm_details' => $crm_request, 'launched_by' => $launched_by, 'comments' => $crm_comments, 'last_comment_id' => $last_comment, 'admins' => $admins, 'types' => $types, 'departments' => $departments, 'tagged_name' => $tagged_name,'crm_tagging' => $crm_tagging, 'crm_agent_history' => $crm_agent_history, 'crm_status_history' => $crm_status_history, 'crm_tagging_history' => $crm_tagging_history, 'agent' => $agent_name, 'tag_check' => $tagged, 'tag_permission' => $tag_permission, 'shipment_status' => $shipment_status, 'shipper' => $shipper,'case_nature' => $case_nature, 'case_nature_complaints' => $case_nature_type_complaints, 'case_nature_service_requests' => $case_nature_type_service_requests, 'arrival_date' => $arrival_date, 'shipment_status_date' => $shipment_status_date, 'sale_person' => $sale_person, 'case_nature_type_claims' => $case_nature_type_claims, 'hubs' => $hubs, 'escalation_tagged_check' => $escalation_tagged_check, 'crm_escalation_tagging_history' => $crm_escalation_tagging_history, 'escalation_status_flag' => $escalation_status_flag, 'escalation_log_flag' => $escalation_log_flag, 'escalation_tagging_id' => $escalation_tagging_id, 'crm_escalation_levels' => $crm_escalation_levels, 'crm_images_count' => $crm_images_count,'insurance' => $insurance,'approvers' => $approvers, 'ratings' => $ratings]);
        }else{
            return redirect()->back()->with('danger', 'CRM Request Not found!');
        }
    }

    public function add_comment(Request $request){

        $comment = $request->comment;
        $request_id = $request->request_id;
        $comment_by = 0;
        $comment_type = 0;
        if($request->internal_switch == 1){
            $comment_type = 1;
        }else
        if($request->internal_switch == 2){
            $comment_type = 2;
        }

        if($comment == null){
            return ['status' => 0, 'error' => 'Comment Not selected!'];
        }
        if(!$request_id){
            return ['status' => 0, 'error' => 'Request ID Not selected!'];
        }
        $shipper_email = '';
        if($request->email_check == 'true'){
            $shipper_email = 1;
        }
        else{
            $shipper_email = 0;
        }

        CRMCommentController::add($request_id, Auth::id(),$comment_by,$comment_type, $comment,$shipper_email);
        $last_comment = CrmComments::where('crm_request_id', $request_id)->where('comment_by',0)->latest()->first();
        return ['status' => 1, 'success' => 'Comment successfully added', 'last_comment_id' => $last_comment->id];
    }

    public function get_latest_comment(Request $request){
        $comment_id = $request->comment_id;
        $request_id = $request->request_id;
        if(($comment_id != null) && ($request_id != null)){
            $name = '';
            $comment_details = CrmComments::where('crm_request_id', $request_id)->latest('id')->first();
            if($comment_details){
                if($comment_details->id > $comment_id){
                    if($comment_details->comment_by == 0){
                        $name = $comment_details->admin->name;
                    }else if($comment_details->comment_by == 1) {
                        $name = $comment_details->shipper->name;
                    }else if($comment_details->comment_by == 2 && $comment_details->comment_type == 2){
                        $name = $comment_details->rider->name;
                    }else{
                        $name = $comment_details->substitute_user->name;
                    }
                    return ['status' => 1, 'comment' => $comment_details, 'name'=> $name];
                }
            }

        }
    }

    public function launched_re_open_index(){
        ActivityTrailController::createActivityTrailLog(Auth::id(),42);
        $case_nature = CrmRequestCaseNature::select('id', 'name')->get();
        $case_nature_type = CrmRequestCaseNatureType::select('id', 'type')->get();
        $channels = CrmRequestChannel::select('id', 'channel')->get();
        $status = CrmRequestStatus::whereIn('id', [1,5])->select('id', 'name')->get();
        $shipment_status = ShipmentStatus::select('id', 'name')->get();
        $agents = AdminRole::leftjoin('admins as a', 'a.role_id', '=', 'admin_roles.id')
            ->where('admin_roles.department_id',3)->get();
        $case_nature_type_complaints = CrmRequestCaseNatureType::where('nature_id', '=', 1)->where('status_id',1)->get();
        $case_nature_type_service_requests = CrmRequestCaseNatureType::where('nature_id', '=', 2)->where('status_id',1)->get();
        $zones = Zone::where('status', 1)->get();
        return view('admin.crm.launched_re_open')->with(['case_nature' => $case_nature, 'case_nature_type' => $case_nature_type, 'channels' => $channels, 'status' => $status, 'agents' => $agents,'case_nature_complaints' => $case_nature_type_complaints, 'case_nature_service_requests' => $case_nature_type_service_requests, 'shipment_status' => $shipment_status, 'zones' => $zones]);
    }

    public function launched_re_open_list(Request $request){
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),102);
        }
        $launched_request = CrmRequest::leftjoin('crm_request_case_nature as crcn', 'crcn.id', '=', 'crm_requests.case_nature_id')
            ->leftjoin('crm_request_case_nature_types as crcnt', 'crcnt.id', '=', 'crm_requests.case_nature_type_id')
            ->leftjoin('crm_request_channels as crc', 'crc.id', '=', 'crm_requests.channel_id')
            ->leftjoin('crm_request_statuses as crs', 'crs.id', '=', 'crm_requests.status_id')
            ->leftjoin('admins as ad', 'ad.id', '=', 'crm_requests.agent_id')
            ->leftjoin('admins as a', function ($join) {
                $join->on('a.id', '=', 'crm_requests.launched_by_id')
                    ->where('crm_requests.launched_by', '=', DB::raw(0));
            })
            ->leftjoin('users as u', function ($join) {
                $join->on('u.id', '=', 'crm_requests.launched_by_id')
                    ->where('crm_requests.launched_by', '=', DB::raw(1));
            })
            ->leftjoin('substitute_users as su', function ($join) {
                $join->on('su.id', '=', 'crm_requests.launched_by_id')
                    ->where('crm_requests.launched_by', '=', DB::raw(2));
            })
            ->leftjoin('retail_users as ru', function ($join) {
                $join->on('ru.id', '=', 'crm_requests.launched_by_id')
                    ->where('crm_requests.launched_by', '=', DB::raw(3));
            })
            ->leftjoin('consignee_users as cu', function ($join) {
                $join->on('cu.id', '=', 'crm_requests.launched_by_id')
                    ->where('crm_requests.launched_by', '=', DB::raw(4));
            })
            ->leftjoin('shipments as s', 's.id', '=', 'crm_requests.shipment_id')
            ->leftjoin('user_shipping_infos AS usi', 'usi.id', '=', 's.pickup_address_id')
            ->leftjoin('users as user', 'user.id', '=', 'crm_requests.shipper_id')
            ->leftjoin('cities as oc', 'oc.id', '=', 'usi.city_id')
            ->leftjoin('cities as dc', 'dc.id', '=', 's.consignee_city_id')
            ->leftjoin('cities as dh', 'dh.id', '=', 'dc.hub_id')
            ->leftjoin('zones as z', 'z.id', '=', 'dc.zone_id')
            ->leftjoin('shipment_status as ss', 'ss.id', '=', 's.shipper_status_id')
            ->leftjoin('crm_request_agent_histories as res', function ($join) {
                $join->on('res.crm_request_id', '=', 'crm_requests.id')
                    ->where('res.id','=',
                        DB::raw('(select max(id) from crm_request_agent_histories where crm_request_agent_histories.crm_request_id = crm_requests.id and crm_request_agent_histories.agent_id = crm_requests.agent_id)'));
            })
            ->leftjoin('admins as resby', 'resby.id', '=', 'res.assigned_by')
            ->leftjoin('crm_comments as ccs', function($join){
                $join->on('ccs.crm_request_id', '=', 'crm_requests.id')
                    ->where('ccs.id', '=', DB::raw('(select max(id) from crm_comments where crm_comments.crm_request_id = crm_requests.id)'));
            })
            ->leftjoin('sale_person_tags as spt', function($join){
                $join->on('spt.user_id', '=', 'crm_requests.shipper_id')
                    ->where('spt.id', '=', DB::raw('(select max(id) from sale_person_tags where sale_person_tags.user_id = crm_requests.shipper_id and sale_person_tags.status = 0)'));
            })
            ->leftjoin('admins as accs', 'accs.id', '=', 'ccs.comment_by_id')
            ->leftjoin('users as uccs', 'uccs.id', '=', 'ccs.comment_by_id')
			->leftjoin('crm_request_status_histories as crsh', function($join){
                $join->on('crsh.crm_request_id', '=', 'crm_requests.id')
                    ->where('crsh.created_at', '=', DB::raw('(select max(created_at) from crm_request_status_histories where crm_request_status_histories.crm_request_id = crm_requests.id and crm_request_status_histories.status_id = 5)'));
            })
			->select('crm_requests.id as id', 's.tracking_number as tracking_number','crcn.id as nature_id', 'crcn.name as case_nature', 'crcnt.type as case_nature_type', 'crc.channel as channel', 'crs.name as status', 'ad.name as agent', 'a.name as name', 'u.name as shipper', 'su.name as sub_shipper', 'ru.name as retail_user', 'cu.name as consignee_user', 'crm_requests.launched_by as launched_added_by', 'crm_requests.created_at as created_at', 'crm_requests.description as description','crm_requests.description as descr', 'ss.name as shipment_status', 'user.name as shipper_name', 'oc.name as origin', 'dc.name as destination', 'res.created_at as agent_assigned_date', 'ccs.comment as last_comment', 'ccs.created_at as last_comment_date', 'ccs.comment_by as last_comment_by', 'accs.name as last_comment_admin', 'uccs.name as last_comment_shipper', 'crm_requests.launched_by_id', 'dh.name as hub', 'z.name as zone', 'resby.name as agent_assigned_by', 'crm_requests.address as address', 'crm_requests.address_latitude as address_latitude','crm_requests.address_longitude as address_longitude' ,'crsh.created_at as reopen_date')
            ->whereIn('crm_requests.status_id', [1, 5])
            ->groupBy('crm_requests.id');

        if (!in_array(session('role_id'), [1, 6]) && !in_array(179, session('permissions')) && !in_array(201, session('permissions'))) {
            $launched_request = $launched_request
                ->where(function ($sub_query) {
                    $sub_query->where('crm_requests.agent_id', Auth::id())
                    ->orWhere(function ($sub_query) {
                        $sub_query->where('crm_requests.launched_by', 0)
                            ->where('crm_requests.launched_by_id', Auth::id());
                    });
                });
        }
        else if (session('department_id') == 7){
            if(!in_array(session('id'), session('sale_users_bypass'))){
                $launched_request = $launched_request->where('spt.admin_id', Auth::id());
            }
        }

        $datatables = Datatables::of($launched_request)
            ->setRowAttr([
                'nature' => function ($requests) {
                    $nature = '';
                    if($requests->nature_id == 1){
                        $nature = 1;
                    }else if($requests->nature_id == 2){
                        $nature = 2;
                    }else if($requests->nature_id == 3){
                        $nature = 3;
                    }
                    return $nature;
                }
            ])
            ->addColumn('id_padded', function ($requests) {
                return str_pad($requests->id, 6, '0', STR_PAD_LEFT);
            })
            ->addColumn('id_padded_link', function ($requests) {
                return '<u><a href=' . route('admin.crm.request.details', ['id' => $requests->id]) . '  target="_blank">' . str_pad($requests->id, 6, '0', STR_PAD_LEFT). '</a></u>';
            })
            ->addColumn('tracking_number_hyperlink', function ($requests) {
                return '<u><a href=' . route('admin.tracking.index') . '?tracking_number=' . $requests->tracking_number . ' class="tracking" target="_blank">' . $requests->tracking_number . '</a></u>';
            })
            ->editColumn('descr',function($request){
                return strip_tags($request->description);
            })
            ->editColumn('agent', function ($requests){
                if($requests->agent == null){
                    return '-';
                }
                else{
                    return $requests->agent;
                }
            })
            ->filterColumn('shipment_status',function ($query,$keyword){

                if ($keyword != '') {
                    $query->where('ss.id',$keyword);
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->addColumn('launched_by_name', function ($requests){
                $name = '';
                if($requests->launched_added_by == 0){
                    $name = $requests->name;
                }
                else if($requests->launched_added_by == 1){
                    $name = $requests->shipper;
                }else if($requests->launched_added_by == 2){
                    $name = $requests->sub_shipper;
                }else if($requests->launched_added_by == 3){
                    $name = $requests->retail_user;
                }else if($requests->launched_added_by == 4){
                    $name = $requests->consignee_user;
                }

                return $name;
            })
            ->filterColumn('launched_by_name', function($query, $keyword) {
                $keyword = strtolower($keyword);

                if ($keyword != '') {
                    $query->where(function ($sub_query) use ($keyword) {
                        $sub_query->where('crm_requests.launched_by', '=', 0)
                            ->where('a.name', 'like', '%' . $keyword . '%');
                    })
                    ->orWhere(function ($sub_query) use ($keyword) {
                        $sub_query->where('crm_requests.launched_by', '=', 1)
                            ->where('u.name', 'like', '%' . $keyword . '%');
                    })
                    ->orWhere(function ($sub_query) use ($keyword) {
                        $sub_query->where('crm_requests.launched_by', '=', 2)
                            ->where('su.name', 'like', '%' . $keyword . '%');
                    })
                    ->orWhere(function ($sub_query) use ($keyword) {
                        $sub_query->where('crm_requests.launched_by', '=', 3)
                            ->where('cu.name', 'like', '%' . $keyword . '%');
                    });
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->orderColumn('launched_by_name', DB::raw('IF (crm_requests.launched_by = 0, a.name, IF (crm_requests.launched_by = 1, u.name, IF (crm_requests.launched_by = 2, su.name, "")))') . ' $1')
            ->addColumn('current_tat', function ($requests){
                if($requests->created_at){
                    Carbon::setWeekendDays([
                        Carbon::SUNDAY,
                    ]);

                    $re_open_count = CrmRequestStatusHistory::where('crm_request_id', $requests->id)->where('status_id' ,5)->latest('id');
                    if($re_open_count->exists()){
                        $re_open_count = $re_open_count->first();

                        $launched = Carbon::parse($re_open_count->created_at);
                        $last_closed = CrmRequestStatusHistory::where('crm_request_id', $requests->id)->where('status_id' ,4)->where('created_at', '>=', $re_open_count->created_at)->first();
                        if($last_closed){
                            $current = $last_closed->created_at;
                        }
                        else{
                            $current = Carbon::now();
                        }
                        $time_format = 'H:i';
                        $time_from = CrmSettings::where('name','TAT Cut-Off Time From')->first();
                        $time_to = CrmSettings::where('name','TAT Cut-Off Time To')->first();
                        $to_formatted = date($time_format, strtotime($time_to->setting_value));
                        $cut_off_check = $requests->created_at->format($time_format);
                        $additional_tat = $current->diffInWeekdays($launched);
                        $current_tat = $additional_tat;
                        $launched_check = $launched->toDateString();
                        $current_check = $current->toDateString();
                        if($launched_check <= $current_check){
                            if($to_formatted < $cut_off_check){
                                $after_cut_off = $current_tat - 1;
                                $current_tat = $after_cut_off;
                            }
                        }
                        $holidays = CrmTatHolidays::whereBetween('holiday', [$launched, $current])->get();
                        foreach($holidays as $holiday){
                            $holiday_formatted = date('Y-m-d H:i:s', strtotime($holiday->holiday));
                            $holiday_formatted_check = date('Y-m-d', strtotime($holiday->holiday));
                            $launched_formatted_check = date('Y-m-d', strtotime($launched));
                            if($launched < $holiday_formatted || $current > $holiday_formatted){
                                if($holiday_formatted_check == $launched_formatted_check){
                                    if($to_formatted < $cut_off_check){
                                        $after_cut_off = $current_tat + 1;
                                        $current_tat = $after_cut_off;
                                    }
                                }
                                $after_holidays = $current_tat - 1;
                                $current_tat = $after_holidays;
                            }
                        }
                    }
                    else {
                        $launched = Carbon::parse($requests->created_at);
                        $first_closed = CrmRequestStatusHistory::where('crm_request_id', $requests->id)->where('status_id' ,4)->first();
                        if($first_closed){
                            $current = $first_closed->created_at;
                        }
                        else{
                            $current = Carbon::now();
                        }
                        $time_format = 'H:i';
                        $time_from = CrmSettings::where('name','TAT Cut-Off Time From')->first();
                        $time_to = CrmSettings::where('name','TAT Cut-Off Time To')->first();
                        $to_formatted = date($time_format, strtotime($time_to->setting_value));
                        $cut_off_check = $requests->created_at->format($time_format);
                        $current_tat = $current->diffInWeekdays($launched);
                        $launched_check = $launched->toDateString();
                        $current_check = $current->toDateString();
                        if($launched_check <= $current_check){
                            if($to_formatted < $cut_off_check){
                                $after_cut_off = $current_tat - 1;
                                $current_tat = $after_cut_off;
                            }
                        }
                        $holidays = CrmTatHolidays::whereBetween('holiday', [$launched, $current])->get();
                        foreach($holidays as $holiday){
                            $holiday_formatted = date('Y-m-d H:i:s', strtotime($holiday->holiday));
                            $holiday_formatted_check = date('Y-m-d', strtotime($holiday->holiday));
                            $launched_formatted_check = date('Y-m-d', strtotime($launched));
                            if($launched < $holiday_formatted || $current > $holiday_formatted){
                                if($holiday_formatted_check == $launched_formatted_check){
                                    if($to_formatted < $cut_off_check){
                                        $after_cut_off = $current_tat + 1;
                                        $current_tat = $after_cut_off;
                                    }
                                }
                                $after_holidays = $current_tat - 1;
                                $current_tat = $after_holidays;
                            }
                        }
                    }
                    return $current_tat;
                }
                return "-";
            })
            ->filterColumn('case_nature_type',function ($query,$keyword){

                if ($keyword != '') {
                    $query->where('crcnt.id','=',$keyword);
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->editColumn('added_by', function($requests){
                if($requests->launched_added_by == 0) {
                    return 'Admin';
                }
                else if($requests->launched_added_by == 1) {
                    return 'Shipper';
                }
                else if($requests->launched_added_by == 2){
                    return 'Shipper Substitute User';
                }
                else if($requests->launched_added_by == 3){
                    return 'Retail';
                }
                else if($requests->launched_added_by == 4)
                {
                    return 'Consignee';
                }
            })
            ->editColumn('last_comment_name', function($requests){
                if($requests->last_comment_by == 0){
                    return $requests->last_comment_admin;
                }
                else if($requests->last_comment_by == 1){
                    return $requests->last_comment_shipper;
                }
                else{
                    return '-';
                }
            })
            ->editColumn('last_comment_date', function($requests){
                if($requests->last_comment_date != null){
                    return $requests->last_comment_date;
                }
                else{
                    return '-';
                }
            })
            ->filterColumn('last_comment_name', function($query, $keyword) {
                $keyword = strtolower($keyword);

                if ($keyword != '') {
                    $query->where(function ($sub_query) use ($keyword) {
                        $sub_query->where('ccs.comment_by', '=', 0)
                            ->where('accs.name', 'like', '%' . $keyword . '%');
                    })
                        ->orWhere(function ($sub_query) use ($keyword) {
                            $sub_query->where('ccs.comment_by', '=', 1)
                                ->where('uccs.name', 'like', '%' . $keyword . '%');
                        });
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->orderColumn('last_comment_name', DB::raw('IF (ccs.comment_by = 0, accs.name, IF (ccs.comment_by = 1, uccs.name, ""))') . ' $1')

            ->editColumn('last_comment', function($requests){
                if($requests->last_comment != null){
                    return $requests->last_comment;
                }
                else{
                    return '-';
                }
            })
            ->addColumn('action', function($requests) {
                $route = route('admin.crm.request.details', ['id' => $requests->id]);
                    $dropdown = '
                  <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">';
                    $dropdown .= '<button onclick="window.open(\'' . $route . '\', \'_tab\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Details</div></button>';
//                    if($requests->nature_id == 1 || $requests->nature_id == 2){
//                        $dropdown .= '<button type="button" class="dropdown-item update_request"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Update</div></button>';
//                    }


                    $dropdown .= '</div>
                  </div>
                ';

                    return $dropdown;
            });
        if ($tracking_numbers = $request->get('tracking_numbers')) {
            $datatables->whereIn('s.tracking_number', explode(',', $tracking_numbers));
        }

        return $datatables->make(true);
    }
    public function in_process_index(){
        ActivityTrailController::createActivityTrailLog(Auth::id(),312);
        $case_nature = CrmRequestCaseNature::select('id', 'name')->get();
        $case_nature_type = CrmRequestCaseNatureType::select('id', 'type')->get();
        $shipment_status = ShipmentStatus::select('id', 'name')->get();
        $channels = CrmRequestChannel::select('id', 'channel')->get();
        $agents = AdminRole::leftjoin('admins as a', 'a.role_id', '=', 'admin_roles.id')
            ->where('admin_roles.department_id',3)->get();
        $admins = AdminRole::leftjoin('admins as a', 'a.role_id', '=', 'admin_roles.id' )
            ->select('a.id as id', 'a.name as name')
            ->where('a.status', 1)
            ->whereNotIn('admin_roles.department_id', [1,3])->get();
        $types = CrmRequestTaggingTypes::get();
        $departments = AdminDepartment::whereNotIn('id', [1,3])->get();
        $hubs = City::where('hub', 1)->get();
        $zones = Zone::where('status', 1)->get();
                return view('admin.crm.in_process')->with(['case_nature' => $case_nature, 'case_nature_type' => $case_nature_type, 'channels' => $channels, 'agents' => $agents, 'shipment_status' => $shipment_status, 'types' => $types, 'admins' => $admins, 'departments' => $departments, 'hubs' => $hubs, 'zones' => $zones]);
    }

    public function in_process_list(Request $request){
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),313);
        }
        $in_process_request = CrmRequest::leftjoin('crm_request_case_nature as crcn', 'crcn.id', '=', 'crm_requests.case_nature_id')
            ->leftjoin('crm_request_case_nature_types as crcnt', 'crcnt.id', '=', 'crm_requests.case_nature_type_id')
            ->leftjoin('crm_request_channels as crc', 'crc.id', '=', 'crm_requests.channel_id')
            ->leftjoin('admins as ad', 'ad.id', '=', 'crm_requests.agent_id')
            ->leftjoin('admins as a', function ($join) {
                $join->on('a.id', '=', 'crm_requests.launched_by_id')
                    ->where('crm_requests.launched_by', '=', DB::raw(0));
            })
            ->leftjoin('users as u', function ($join) {
                $join->on('u.id', '=', 'crm_requests.launched_by_id')
                    ->where('crm_requests.launched_by', '=', DB::raw(1));
            })
            ->leftjoin('substitute_users as su', function ($join) {
                $join->on('su.id', '=', 'crm_requests.launched_by_id')
                    ->where('crm_requests.launched_by', '=', DB::raw(2));
            })
            ->leftjoin('retail_users as ru', function ($join) {
                $join->on('ru.id', '=', 'crm_requests.launched_by_id')
                    ->where('crm_requests.launched_by', '=', DB::raw(3));
            })
            ->leftjoin('consignee_users as cu', function ($join) {
                $join->on('cu.id', '=', 'crm_requests.launched_by_id')
                    ->where('crm_requests.launched_by', '=', DB::raw(4));
            })
            ->leftjoin('shipments as s', 's.id', '=', 'crm_requests.shipment_id')
            ->leftjoin('shipments_journey as sj', function ($join){
                $join->on('sj.shipment_id', '=', 's.id')
                ->where('sj.shipper_status_id',2);
            })
            ->leftjoin('shipment_status as ss', 'ss.id', '=', 's.shipper_status_id')
            ->leftjoin('users as user', 'user.id', '=', 'crm_requests.shipper_id')
            ->leftjoin('user_shipping_infos AS usi', 's.pickup_address_id', '=', 'usi.id')
            ->leftjoin('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->leftjoin('cities AS dc', 's.consignee_city_id', '=', 'dc.id')
            ->leftjoin('cities AS dh', 'dc.hub_id', '=', 'dh.id')
            ->leftjoin('zones as z', 'z.id', '=', 'dc.zone_id')
            ->leftjoin('crm_request_taggings as crt', 'crt.crm_request_id', '=', 'crm_requests.id')
            ->leftjoin('crm_request_tagging_histories as crth', function ($join) {
                $join->on('crth.crm_request_id', '=', 'crm_requests.id')
                    ->where('crth.id','=',
                        DB::raw('(select max(id) from crm_request_tagging_histories where crm_request_tagging_histories.crm_request_id = crm_requests.id)'));
            })
            ->leftjoin('admin_departments as adp', 'adp.id', '=', 'crt.tagged_id')
            ->leftjoin('admins as at', 'at.id', '=', 'crt.tagged_id')
            ->leftjoin('sale_person_tags as spt', function($join) {
                $join->on('spt.user_id', '=', 's.user_id')
                    ->where('spt.status', '=', 0);
            })
            ->leftjoin('crm_request_status_histories as res', function ($join) {
                $join->on('res.crm_request_id', '=', 'crm_requests.id')
                    ->where('res.id','=',
                        DB::raw('(select max(id) from crm_request_status_histories where crm_request_status_histories.crm_request_id = crm_requests.id and crm_request_status_histories.status_id = 2)'));
            })
            ->leftjoin('crm_request_agent_histories as resa', function ($join) {
                $join->on('resa.crm_request_id', '=', 'crm_requests.id')
                    ->where('resa.id','=',
                        DB::raw('(select max(id) from crm_request_agent_histories where crm_request_agent_histories.crm_request_id = crm_requests.id and crm_request_agent_histories.agent_id = crm_requests.agent_id)'));
            })
            ->leftjoin('admins as resby', 'resby.id', '=', 'resa.assigned_by')
            ->leftjoin('crm_comments as ccs', function($join){
                $join->on('ccs.crm_request_id', '=', 'crm_requests.id')
                    ->where('ccs.id', '=', DB::raw('(select max(id) from crm_comments where crm_comments.crm_request_id = crm_requests.id)'));
            })
            ->leftjoin('admins as accs', 'accs.id', '=', 'ccs.comment_by_id')
            ->leftjoin('users as uccs', 'uccs.id', '=', 'ccs.comment_by_id')
            ->leftjoin('crm_request_escalation_taggings as cret', 'cret.crm_request_id', '=', 'crm_requests.id')
			->leftjoin('crm_request_status_histories as crsh', function($join){
                $join->on('crsh.crm_request_id', '=', 'crm_requests.id')
                    ->where('crsh.created_at', '=', DB::raw('(select max(created_at) from crm_request_status_histories where crm_request_status_histories.crm_request_id = crm_requests.id and crm_request_status_histories.status_id = 5)'));
            })
			->select('sj.created_at as arrival','crm_requests.id as id', 's.tracking_number as tracking_number', 'crcn.name as case_nature', 'crcnt.type as case_nature_type', 'crc.channel as channel', 'ad.name as agent', 'a.name as name', 'u.name as shipper', 'su.name as sub_shipper', 'cu.name as consignee_users', 'ru.name as retail_users', 'crm_requests.launched_by as launched_added_by', 'crm_requests.created_at as created_at', 'crm_requests.description as description','crm_requests.description as descr','at.name as tagged_admin', 'adp.name as tagged_department', 'crt.crm_request_tagging_type_id as crm_request_tagging_type_id', 'ss.name as status', 'user.name as shipper_name', 'oc.name as origin', 'dc.name as destination', 'dh.name as hub', 'crt.crm_request_tagging_type_id as tagged_type', 'res.created_at as valid_date', 'ccs.comment as last_comment', 'ccs.created_at as last_comment_date', 'ccs.comment_by as last_comment_by', 'accs.name as last_comment_admin', 'uccs.name as last_comment_shipper', 'crm_requests.launched_by_id', 'res.created_at as agent_assigned_date', 'resby.name as agent_assigned_by', 'crth.created_at as tagged_date', 'z.name as zone','crsh.created_at as reopen_date','crm_requests.address as address', 'crm_requests.address_latitude as address_latitude','crm_requests.address_longitude as address_longitude','at.id as tagged_admin_id')
            ->where('crm_requests.status_id', 2)
            ->groupBy('crm_requests.id');

        if ((!in_array(session('role_id'), [1, 4, 6])) && (!in_array(179, session('permissions')) && !in_array(201, session('permissions')))) {
            $in_process_request = $in_process_request->where(function ($query) {
                $query->where(function ($sub_query) {
                    $sub_query->where('crm_requests.agent_id', Auth::id())
                        ->orWhere(function ($sub_query) {
                            $sub_query->where('crm_requests.launched_by', 0)
                                ->where('crm_requests.launched_by_id', Auth::id());
                        });
                })
                ->orWhere(function ($sub_query) {
                    $sub_query->where('spt.admin_id', '=', Auth::id());
                })
                ->orWhere(function ($sub_query) {
                    $sub_query->where('crt.crm_request_tagging_type_id', 2)
                        ->where('crt.tagged_id', '=', Auth::id());
                })
                ->orWhere(function ($sub_query) {
                    $sub_query->where('crt.crm_request_tagging_type_id', 1)
                        ->where('adp.id', '=', session('department_id'))
                        ->where(function ($sub_sub_query) {
                            $sub_sub_query->whereIn('oc.hub_id', session('hubs'))
                                ->orWhereIn('dc.hub_id', session('hubs'))
                                ->orWhereIn('crt.hub_id', session('hubs'));
                        });
                })
                ->orWhere(function ($sub_query) {
                    $sub_query->where('cret.role_id', '=', session('role_id'))
                        ->where(function ($sub_sub_query) {
                            $sub_sub_query->whereNull('cret.hub_id')
                                ->orWhereNotNull('cret.hub_id')
                                ->whereIn('cret.hub_id', session('hubs'));
                        });
                })
                ->orWhere(function ($sub_query) {
                    if(in_array(session('role_id'), [8, 9 ,10])){
                        $sub_query->whereIn('oc.hub_id', session('hubs'))
                            ->orWhereIn('dc.hub_id', session('hubs'));
                    }
                });
            });
        }
        else if (in_array(session('role_id'), [67, 43])){
            $in_process_request = $in_process_request->where('at.id', Auth::id());
        }
        else if (session('department_id') == 7){
            if(!in_array(session('id'), session('sale_users_bypass'))){
                $in_process_request = $in_process_request->where('spt.admin_id', Auth::id());
            }
        }
        $datatables = Datatables::of($in_process_request)
            ->addColumn('id_padded', function ($requests) {
                return str_pad($requests->id, 6, '0', STR_PAD_LEFT);
            })
            ->addColumn('id_padded_link', function ($requests) {
                return '<u><a href=' . route('admin.crm.request.details', ['id' => $requests->id]) . ' target="_blank">' . str_pad($requests->id, 6, '0', STR_PAD_LEFT). '</a></u>';
            })
            ->addColumn('tagged', function ($requests) {
                if($requests->tagged_type == 1){
                    return 'Department';
                }
                else if($requests->tagged_type == 2){
                    return 'Admin';
                }
                else{
                    return '-';
                }
            })
            ->addColumn('tracking_number_hyperlink', function ($requests) {
                return '<u><a href=' . route('admin.tracking.index') . '?tracking_number=' . $requests->tracking_number . ' class="tracking" target="_blank">' . $requests->tracking_number . '</a></u>';
            })
            ->editColumn('descr',function($request){
                return strip_tags($request->description);
            })
            ->addColumn('added_by', function($requests){
                if($requests->launched_added_by == 0) {
                    return 'Admin';
                }
                else if($requests->launched_added_by == 1) {
                    return 'Shipper';
                }
                else if($requests->launched_added_by == 2) {
                    return 'Shipper Substitute User';
                }
                else if($requests->launched_added_by == 3){
                    return 'Retail';
                }
                else if($requests->launched_added_by == 4){
                    return 'Consignee';
                }
                return $requests->launched_added_by;
            })
            ->addColumn('current_tat', function ($requests){
                if($requests->created_at){
                    Carbon::setWeekendDays([
                        Carbon::SUNDAY,
                    ]);

                    $re_open_count = CrmRequestStatusHistory::where('crm_request_id', $requests->id)->where('status_id' ,5)->latest('id');
                    if($re_open_count->exists()){
                        $re_open_count = $re_open_count->first();

                        $launched = Carbon::parse($re_open_count->created_at);
                        $last_closed = CrmRequestStatusHistory::where('crm_request_id', $requests->id)->where('status_id' ,4)->where('created_at', '>=', $re_open_count->created_at)->first();
                        if($last_closed){
                            $current = $last_closed->created_at;
                        }
                        else{
                            $current = Carbon::now();
                        }
                        $time_format = 'H:i';
                        $time_from = CrmSettings::where('name','TAT Cut-Off Time From')->first();
                        $time_to = CrmSettings::where('name','TAT Cut-Off Time To')->first();
                        $to_formatted = date($time_format, strtotime($time_to->setting_value));
                        $cut_off_check = $requests->created_at->format($time_format);
                        $additional_tat = $current->diffInWeekdays($launched);
                        $current_tat = $additional_tat;
                        $launched_check = $launched->toDateString();
                        $current_check = $current->toDateString();
                        if($launched_check <= $current_check){
                            if($to_formatted < $cut_off_check){
                                $after_cut_off = $current_tat - 1;
                                $current_tat = $after_cut_off;
                            }
                        }
                        $holidays = CrmTatHolidays::whereBetween('holiday', [$launched, $current])->get();
                        foreach($holidays as $holiday){
                            $holiday_formatted = date('Y-m-d H:i:s', strtotime($holiday->holiday));
                            $holiday_formatted_check = date('Y-m-d', strtotime($holiday->holiday));
                            $launched_formatted_check = date('Y-m-d', strtotime($launched));
                            if($launched < $holiday_formatted || $current > $holiday_formatted){
                                if($holiday_formatted_check == $launched_formatted_check){
                                    if($to_formatted < $cut_off_check){
                                        $after_cut_off = $current_tat + 1;
                                        $current_tat = $after_cut_off;
                                    }
                                }
                                $after_holidays = $current_tat - 1;
                                $current_tat = $after_holidays;
                            }
                        }
                    }
                    else {
                        $launched = Carbon::parse($requests->created_at)->startOfDay();
                        $first_closed = CrmRequestStatusHistory::where('crm_request_id', $requests->id)->where('status_id' ,4)->first();
                        if($first_closed){
                            $current = $first_closed->created_at;
                        }
                        else{
                            $current = Carbon::now();
                        }
                        $time_format = 'H:i';
                        $time_from = CrmSettings::where('name','TAT Cut-Off Time From')->first();
                        $time_to = CrmSettings::where('name','TAT Cut-Off Time To')->first();
                        $to_formatted = date($time_format, strtotime($time_to->setting_value));
                        $cut_off_check = $requests->created_at->format($time_format);
                        $current_tat = $current->diffInWeekdays($launched);

                        $launched_check = $launched->toDateString();
                        $current_check = $current->toDateString();
                        if($launched_check <= $current_check){
                            if($to_formatted < $cut_off_check){
                                $after_cut_off = $current_tat - 1;
                                $current_tat = $after_cut_off;
                            }
                        }
                        $holidays = CrmTatHolidays::whereBetween('holiday', [$launched, $current])->get();
                        foreach($holidays as $holiday){
                            $holiday_formatted = date('Y-m-d H:i:s', strtotime($holiday->holiday));
                            $holiday_formatted_check = date('Y-m-d', strtotime($holiday->holiday));
                            $launched_formatted_check = date('Y-m-d', strtotime($launched));
                            if($launched < $holiday_formatted || $current > $holiday_formatted){
                                if($holiday_formatted_check == $launched_formatted_check){
                                    if($to_formatted < $cut_off_check){
                                        $after_cut_off = $current_tat + 1;
                                        $current_tat = $after_cut_off;
                                    }
                                }
                                $after_holidays = $current_tat - 1;
                                $current_tat = $after_holidays;
                            }
                        }
                    }

                    return $current_tat;
                }
                return "-";
            })
            ->editColumn('tagged_to', function($requests){
                if($requests->crm_request_tagging_type_id == 1) {
                    return $requests->tagged_department;
                }
                else if($requests->crm_request_tagging_type_id == 2) {
                    return $requests->tagged_admin;
                }
                else{
                    return '-';
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
            ->filterColumn('tagged_to',function ($query,$keyword){
                if ($keyword != '') {
                    $query->where(function($sub_query) use ($keyword) {
                        $sub_query->where('crt.crm_request_tagging_type_id', '=', 1)
                            ->where('adp.name', 'like', '%' . $keyword . '%');
                    })
                    ->orWhere(function($sub_query) use ($keyword) {
                        $sub_query->where('crt.crm_request_tagging_type_id', '=', 2)
                            ->where('at.name', 'like', '%' . $keyword . '%');
                    });
                }
            })
            ->orderColumn('tagged_to', DB::raw('IF (crt.crm_request_tagging_type_id = 1, adp.name, IF (crt.crm_request_tagging_type_id = 2, at.name, ""))') . ' $1')
            ->editColumn('agent', function ($requests){
                if($requests->agent == null){
                    return '-';
                }
                else{
                    return $requests->agent;
                }
            })
            ->addColumn('launched_by_name', function ($requests){
                $name = '';
                if($requests->launched_added_by == 0){
                    $name = $requests->name;
                }
                else if($requests->launched_added_by == 1){
                    $name = $requests->shipper;
                }else if($requests->launched_added_by == 2){
                    $name = $requests->sub_shipper;
                }else if($requests->launched_added_by == 3){
                    $name = $requests->retail_user;
                }else if($requests->launched_added_by == 4){
                    $name = $requests->consignee_user;
                }
                return $name;
            })
            ->editColumn('last_comment_date', function($requests){
                if($requests->last_comment_date != null){
                    return $requests->last_comment_date;
                }
                else{
                    return '-';
                }
            })
            ->filterColumn('launched_by_name', function($query, $keyword) {
                $keyword = strtolower($keyword);

                if ($keyword != '') {
                    $query->where(function ($sub_query) use ($keyword) {
                        $sub_query->where('crm_requests.launched_by', '=', 0)
                            ->where('a.name', 'like', '%' . $keyword . '%');
                    })
                        ->orWhere(function ($sub_query) use ($keyword) {
                            $sub_query->where('crm_requests.launched_by', '=', 1)
                                ->where('u.name', 'like', '%' . $keyword . '%');
                        })
                        ->orWhere(function ($sub_query) use ($keyword) {
                            $sub_query->where('crm_requests.launched_by', '=', 2)
                                ->where('su.name', 'like', '%' . $keyword . '%');
                        })
                        ->orWhere(function ($sub_query) use ($keyword) {
                            $sub_query->where('crm_requests.launched_by', '=', 3)
                                ->where('cu.name', 'like', '%' . $keyword . '%');
                        });

                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->orderColumn('launched_by_name', DB::raw('IF (crm_requests.launched_by = 0, a.name, IF (crm_requests.launched_by = 1, u.name, IF (crm_requests.launched_by = 2, su.name, "")))') . ' $1')
            ->filterColumn('case_nature_type',function ($query,$keyword){

                if ($keyword != '') {
                    $query->where('crcnt.id','=',$keyword);
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->editColumn('last_comment_name', function($requests){
                if($requests->last_comment_by == 0){
                    return $requests->last_comment_admin;
                }
                else if($requests->last_comment_by == 1){
                    return $requests->last_comment_shipper;
                }
                else{
                    return '-';
                }
            })
            ->filterColumn('last_comment_name', function($query, $keyword) {
                $keyword = strtolower($keyword);

                if ($keyword != '') {
                    $query->where(function ($sub_query) use ($keyword) {
                        $sub_query->where('ccs.comment_by', '=', 0)
                            ->where('accs.name', 'like', '%' . $keyword . '%');
                    })
                        ->orWhere(function ($sub_query) use ($keyword) {
                            $sub_query->where('ccs.comment_by', '=', 1)
                                ->where('uccs.name', 'like', '%' . $keyword . '%');
                        });
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->orderColumn('last_comment_name', DB::raw('IF (ccs.comment_by = 0, accs.name, IF (ccs.comment_by = 1, uccs.name, ""))') . ' $1')
          /*  ->addColumn('special_request', function ($shipments){
                return '<div class="text-center">
                                <button type="button" class="btn btn-primary btn-sm"><a class="white" ><i class="la la-dollar align-middle"></i></a></button>
                        </div>';
            })*/
            ->editColumn('last_comment', function($requests){
                if($requests->last_comment != null){
                    return $requests->last_comment;
                }
                else{
                    return '-';
                }
            })
            ->addColumn('tagged_to_manual', function($requests){
                $crm_tagging = CrmRequestTagging::where('crm_request_id',$requests->id)->whereIn('crm_request_tagging_type_id', [1,2])->get()->first();
                if($crm_tagging){
                    if($crm_tagging->crm_request_tagging_type_id == 1){
                        
                        $tagged_name = AdminDepartment::find($crm_tagging->tagged_id)->name;
                        return $tagged_name;
                    }elseif($crm_tagging->crm_request_tagging_type_id == 2){
                        $tagged_name = Admin::find($crm_tagging->tagged_id)->name;
                        return $tagged_name;

                    }else{
                        return '-';
                    }
                }else{
                    return '-';
                }
            })
            ->filterColumn('tagged_to_manual',function ($query,$keyword){
                if ($keyword != '') {
                    $query->where(function($sub_query) use ($keyword) {
                        $sub_query->where('crt.crm_request_tagging_type_id', '=', 1)
                            ->where('adp.name', 'like', '%' . $keyword . '%');
                    })
                    ->orWhere(function($sub_query) use ($keyword) {
                        $sub_query->where('crt.crm_request_tagging_type_id', '=', 2)
                            ->where('at.name', 'like', '%' . $keyword . '%');
                    });
                }
            })
            ->orderColumn('tagged_to_manual', DB::raw('IF (crt.crm_request_tagging_type_id = 1, adp.name, IF (crt.crm_request_tagging_type_id = 2, at.name, ""))') . ' $1')
            
            ->addColumn('tagged_to_kae', function($requests){
                $crm_tagging = CrmRequestTagging::where('crm_request_id',$requests->id)->where('crm_request_tagging_type_id',4)->get()->first();
                if($crm_tagging){
                    $admin = Admin::find($crm_tagging->tagged_id);
                    if($admin){
                        return $admin->name;

                    }else{

                        return '-';
                    }
                }else{
                    return '-';
                }

            })
            ->filterColumn('tagged_to_kae',function ($query,$keyword){
                if ($keyword != '') {
                    $query->where(function($sub_query) use ($keyword) {
                        $sub_query->where('crt.crm_request_tagging_type_id', '=', 4)
                            ->where('at.name', 'like', '%' . $keyword . '%');
                    });
                }
            })
            ->orderColumn('tagged_to_kae', DB::raw('IF (crt.crm_request_tagging_type_id = 4, at.name, "")') . ' $1')
            
            ->addColumn('tagged_to_operation', function($requests){
                $crm_tagging = CrmRequestTagging::where('crm_request_id',$requests->id)->where('crm_request_tagging_type_id',5)->get()->first();
                if($crm_tagging){
                    $admin = Admin::find($crm_tagging->tagged_id);
                    if($admin){
                        return $admin->name;

                    }else{

                        return '-';
                    }
                }else{
                    return '-';
                }
            })
            ->filterColumn('tagged_to_operation',function ($query,$keyword){
                if ($keyword != '') {
                    $query->where(function($sub_query) use ($keyword) {
                        $sub_query->where('crt.crm_request_tagging_type_id', '=', 5)
                            ->where('at.name', 'like', '%' . $keyword . '%');
                    });
                }
            })
            ->orderColumn('tagged_to_operation', DB::raw('IF (crt.crm_request_tagging_type_id = 5, at.name, "")') . ' $1')
            
            ->addColumn('action', function($requests) {
                $route = route('admin.crm.request.details', ['id' => $requests->id]);
                    $dropdown = '
                  <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">';
                    $dropdown .= '<button onclick="window.open(\'' . $route . '\', \'_tab\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Details</div></button>';
                    $dropdown .= '</div>
                  </div>
                ';

                    return $dropdown;
            });

        if ($tracking_numbers = $request->get('tracking_numbers')) {
            $datatables->whereIn('s.tracking_number', explode(',', $tracking_numbers));
        }

        return $datatables->make(true);
    }

    public function resolved_index(){
        ActivityTrailController::createActivityTrailLog(Auth::id(),314);
        $case_nature = CrmRequestCaseNature::select('id', 'name')->get();
        $case_nature_type = CrmRequestCaseNatureType::select('id', 'type')->get();
        $channels = CrmRequestChannel::select('id', 'channel')->get();
        $shipment_status = ShipmentStatus::select('id', 'name')->get();
        $agents = AdminRole::leftjoin('admins as a', 'a.role_id', '=', 'admin_roles.id')
            ->where('admin_roles.department_id',3)->get();
        $admins = AdminRole::leftjoin('admins as a', 'a.role_id', '=', 'admin_roles.id' )
            ->select('a.id as id', 'a.name as name')
            ->where('a.status', 1)
            ->whereNotIn('admin_roles.department_id', [1,3])->get();
        $types = CrmRequestTaggingTypes::get();
        $departments = AdminDepartment::whereNotIn('id', [1,3])->get();
        $hubs = City::where('hub', 1)->get();
        //return view('admin.crm.in_process')->with(['case_nature' => $case_nature, 'case_nature_type' => $case_nature_type, 'channels' => $channels, 'agents' => $agents, 'shipment_status' => $shipment_status, 'types' => $types, 'admins' => $admins, 'departments' => $departments, 'hubs' => $hubs, 'zones' => $zones]);

        return view('admin.crm.resolved')->with(['case_nature' => $case_nature, 'case_nature_type' => $case_nature_type, 'channels' => $channels, 'agents' => $agents, 'shipment_status' => $shipment_status,'types' => $types, 'admins' => $admins, 'departments' => $departments, 'hubs' => $hubs]);
    }

    public function resolved_list(Request $request){
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),315);
        }
        $resolved_request = CrmRequest::leftjoin('crm_request_case_nature as crcn', 'crcn.id', '=', 'crm_requests.case_nature_id')
            ->leftjoin('crm_request_case_nature_types as crcnt', 'crcnt.id', '=', 'crm_requests.case_nature_type_id')
            ->leftjoin('crm_request_channels as crc', 'crc.id', '=', 'crm_requests.channel_id')
            ->leftjoin('admins as ad', 'ad.id', '=', 'crm_requests.agent_id')
            ->leftjoin('admins as a', function ($join) {
                $join->on('a.id', '=', 'crm_requests.launched_by_id')
                    ->where('crm_requests.launched_by', '=', DB::raw(0));
            })
            ->leftjoin('users as u', function ($join) {
                $join->on('u.id', '=', 'crm_requests.launched_by_id')
                    ->where('crm_requests.launched_by', '=', DB::raw(1));
            })
            ->leftjoin('substitute_users as su', function ($join) {
                $join->on('su.id', '=', 'crm_requests.launched_by_id')
                    ->where('crm_requests.launched_by', '=', DB::raw(2));
            })
            ->leftjoin('retail_users as ru', function ($join) {
                $join->on('ru.id', '=', 'crm_requests.launched_by_id')
                    ->where('crm_requests.launched_by', '=', DB::raw(3));
            })
            ->leftjoin('consignee_users as cu', function ($join) {
                $join->on('cu.id', '=', 'crm_requests.launched_by_id')
                    ->where('crm_requests.launched_by', '=', DB::raw(4));
            })
            ->leftjoin('shipments as s', 's.id', '=', 'crm_requests.shipment_id')
            ->leftjoin('shipments_journey as sj', function ($join){
                $join->on('sj.shipment_id', '=', 's.id')
                ->where('sj.shipper_status_id',2);
            })
            ->leftjoin('shipment_status as ss', 'ss.id', '=', 's.shipper_status_id')
            ->leftjoin('user_shipping_infos AS usi', 'usi.id', '=', 's.pickup_address_id')
            ->leftjoin('users as user', 'user.id', '=', 'crm_requests.shipper_id')
            ->leftjoin('cities as oc', 'oc.id', '=', 'usi.city_id')
            ->leftjoin('cities as dc', 'dc.id', '=', 's.consignee_city_id')
            ->leftjoin('crm_request_status_histories as inp', function ($join) {
                $join->on('inp.crm_request_id', '=', 'crm_requests.id')
                    ->where('inp.id', '=',
                        DB::raw('(select max(id) from crm_request_status_histories where crm_request_status_histories.crm_request_id = crm_requests.id and crm_request_status_histories.status_id = 2)'));
            })
            ->leftjoin('crm_request_status_histories as res', function ($join) {
                $join->on('res.crm_request_id', '=', 'crm_requests.id')
                    ->where('res.id', '=',
                        DB::raw('(select max(id) from crm_request_status_histories where crm_request_status_histories.crm_request_id = crm_requests.id and crm_request_status_histories.status_id = 3)'));
            })
            ->leftjoin('admins as ra', 'ra.id', '=', 'res.agent_id')
            ->leftjoin('crm_comments as ccs', function($join){
                $join->on('ccs.crm_request_id', '=', 'crm_requests.id')
                    ->where('ccs.id', '=', DB::raw('(select max(id) from crm_comments where crm_comments.crm_request_id = crm_requests.id)'));
            })
            ->leftjoin('admins as accs', 'accs.id', '=', 'ccs.comment_by_id')
            ->leftjoin('users as uccs', 'uccs.id', '=', 'ccs.comment_by_id')
            ->leftjoin('crm_request_taggings as crt', 'crt.crm_request_id', '=', 'crm_requests.id')
            ->leftjoin('crm_request_tagging_histories as crth', function ($join) {
                $join->on('crth.crm_request_id', '=', 'crm_requests.id')
                    ->where('crth.id', '=',
                        DB::raw('(select max(id) from crm_request_tagging_histories where crm_request_tagging_histories.crm_request_id = crm_requests.id)'));
            })
            ->leftjoin('sale_person_tags as spt', function($join){
                $join->on('spt.user_id', '=', 'crm_requests.shipper_id')
                    ->where('spt.id', '=', DB::raw('(select max(id) from sale_person_tags where sale_person_tags.user_id = crm_requests.shipper_id and sale_person_tags.status = 0)'));
            })
            ->leftjoin('admin_departments as adp', 'adp.id', '=', 'crth.tagged_id')
            ->leftjoin('admins as at', 'at.id', '=', 'crth.tagged_id')
            ->leftjoin('crm_request_status_histories as crsh', function($join){
                $join->on('crsh.crm_request_id', '=', 'crm_requests.id')
                    ->where('crsh.created_at', '=', DB::raw('(select max(created_at) from crm_request_status_histories where crm_request_status_histories.crm_request_id = crm_requests.id and crm_request_status_histories.status_id = 5)'));
            })
			->select('at.name as tagged_to','at.name as tagged_admin', 'adp.name as tagged_department','crt.crm_request_tagging_type_id as crm_request_tagging_type_id','crth.created_at as tagged_date','sj.created_at as arrival', 'crm_requests.id as id', 's.tracking_number as tracking_number','s.amount as cod_amount', 'crcn.name as case_nature', 'crcnt.type as case_nature_type', 'crc.channel as channel', 'ad.name as agent', 'a.name as name', 'u.name as shipper', 'su.name as sub_shipper', 'ru.name as retail_user', 'cu.name as consignee_user', 'crm_requests.launched_by as launched_added_by', 'crm_requests.created_at as created_at', 'crm_requests.description as description','inp.created_at as inprocess','res.created_at as resolved_date', 'ss.name as status', 'user.name as shipper_name', 'oc.name as origin', 'dc.name as destination', 'ra.name as resolved_by', 'ccs.comment as last_comment', 'ccs.created_at as last_comment_date', 'ccs.comment_by as last_comment_by', 'accs.name as last_comment_admin', 'uccs.name as last_comment_shipper', 'crm_requests.launched_by_id','crm_requests.description as descr','crm_requests.address as address', 'crm_requests.address_latitude as address_latitude','crm_requests.address_longitude as address_longitude','crsh.created_at as reopen_date', 'at.id as tagged_to_id')
            ->where('crm_requests.status_id', 3)
            ->groupBy('crm_requests.id');

        if (!in_array(session('role_id'), [1, 6]) && !in_array(179, session('permissions')) && !in_array(201, session('permissions'))) {
            $resolved_request = $resolved_request->where(function ($sub_query) {
                $sub_query->where('crm_requests.agent_id', Auth::id())
                    ->orWhere(function ($sub_query) {
                        $sub_query->where('crm_requests.launched_by', 0)
                            ->where('crm_requests.launched_by_id', Auth::id());
                    })
                    ->orWhere(function ($sub_query) {
                        $sub_query->where('crt.crm_request_tagging_type_id', 2)
                            ->where('crt.tagged_id', '=', Auth::id());
                    })
                    ->orWhere(function ($sub_query) {
                        $sub_query->where('crt.crm_request_tagging_type_id', 1)
                            ->where('adp.id', '=', session('department_id'))
                            ->where(function ($sub_sub_query) {
                                $sub_sub_query->whereIn('oc.hub_id', session('hubs'))
                                    ->orWhereIn('dc.hub_id', session('hubs'))
                                    ->orWhereIn('crt.hub_id', session('hubs'));
                            });
                    })
                ->orWhere(function ($parent_sub_query){
                    $parent_sub_query->orWhere(function ($sub_query) {
                            $sub_query->where('crth.crm_request_tagging_type_id', 2)
                                ->where('crth.tagged_id', '=', Auth::id());
                    })
                        ->orWhere(function ($sub_query) {
                            $sub_query->where('crth.crm_request_tagging_type_id', 1)
                                ->where('adp.id', '=', session('department_id'))
                                ->where(function ($sub_sub_query) {
                                    $sub_sub_query->whereIn('oc.hub_id', session('hubs'))
                                        ->orWhereIn('dc.hub_id', session('hubs'));
                                });
                        });
                });
            });
        }
        else if (in_array(session('role_id'), [67, 43])){
            $resolved_request = $resolved_request->where('at.id', Auth::id());
        }
        else if (session('department_id') == 7){
            if(!in_array(session('id'), session('sale_users_bypass'))){
                $resolved_request = $resolved_request->where('spt.admin_id', Auth::id());
            }
        }

        $datatables = Datatables::of($resolved_request)
            ->addColumn('id_padded', function ($requests) {
                return str_pad($requests->id, 6, '0', STR_PAD_LEFT);
            })
            ->addColumn('id_padded_link', function ($requests) {
                return '<u><a href=' . route('admin.crm.request.details', ['id' => $requests->id]) . ' target="_blank">' . str_pad($requests->id, 6, '0', STR_PAD_LEFT). '</a></u>';
            })
            ->editColumn('descr',function($request){
                return strip_tags($request->description);
            })
            ->editColumn('crm_request_tagging_type_id', function ($requests) {
                if($requests->crm_request_tagging_type_id == 1){
                    return 'Department';
                }
                else if($requests->crm_request_tagging_type_id == 2){
                    return 'Admin';
                }
                else{
                    return '-';
                }
            })
            ->addColumn('tracking_number_hyperlink', function ($requests) {
                return '<u><a href=' . route('admin.tracking.index') . '?tracking_number=' . $requests->tracking_number . ' class="tracking" target="_blank">' . $requests->tracking_number . '</a></u>';
            })
            
            ->editColumn('added_by', function($requests){
                if($requests->launched_added_by == 0) {
                    return 'Admin';
                }
                else if($requests->launched_added_by == 1) {
                    return 'Shipper';
                }
                else if($requests->launched_added_by == 2){
                    return 'Shipper Substitute User';
                }
                else if($requests->launched_added_by == 3){
                    return 'Retail';
                }
                else if($requests->launched_added_by == 4){
                    return 'Consignee';
                }
            })
            ->editColumn('in_process_resolved_tat', function ($requests){
                    Carbon::setWeekendDays([
                        Carbon::SUNDAY,
                    ]);
                $in_process_count = CrmRequestStatusHistory::where('crm_request_id', $requests->id)->where('status_id' ,2)->latest('id');
                if($in_process_count->exists()) {
                    $in_process_count = $in_process_count->first();
                    $resolved_tat = 0;
                    $process = Carbon::parse($in_process_count->created_at);
                    $first_resolved = CrmRequestStatusHistory::where('crm_request_id', $requests->id)->where('status_id' ,3)->where('created_at', '>=', $in_process_count->created_at)->first();
                    $resolved = Carbon::parse($first_resolved->created_at);
                    $resolved_tat = $resolved_tat + $resolved->diffInWeekdays($process);
                    $holidays = CrmTatHolidays::whereBetween('holiday', [$process, $resolved])->get();
                    foreach($holidays as $holiday){
                        $holiday_formatted = date('Y-m-d H:i:s', strtotime($holiday->holiday));
                        if($process < $holiday_formatted || $resolved > $holiday_formatted){
                            $after_holidays = $resolved_tat - 1;
                            $resolved_tat = $after_holidays;
                        }
                    }
                }else{
                    $resolved_tat = 0;

                }
                    return $resolved_tat;
            })
            ->addColumn('launched_by_name', function ($requests){
                $name = '';
                if($requests->launched_added_by == 0){
                    $name = $requests->name;
                }
                else if($requests->launched_added_by == 1){
                    $name = $requests->shipper;
                }else if($requests->launched_added_by == 2){
                    $name = $requests->sub_shipper;
                }else if($requests->launched_added_by == 3){
                    $name = $requests->retail_user;
                }else if($requests->launched_added_by == 4){
                    $name = $requests->consignee_user;
                }
                return $name;
            })
            ->editColumn('tagged_to', function($requests){
                if($requests->crm_request_tagging_type_id == 1) {
                    return $requests->tagged_department;
                }
                else if($requests->crm_request_tagging_type_id == 2) {
                    return $requests->tagged_admin;
                }
                else{
                    return '-';
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
            ->filterColumn('launched_by_name', function($query, $keyword) {
                $keyword = strtolower($keyword);

                if ($keyword != '') {
                    $query->where(function ($sub_query) use ($keyword) {
                        $sub_query->where('crm_requests.launched_by', '=', 0)
                            ->where('a.name', 'like', '%' . $keyword . '%');
                    })
                        ->orWhere(function ($sub_query) use ($keyword) {
                            $sub_query->where('crm_requests.launched_by', '=', 1)
                                ->where('u.name', 'like', '%' . $keyword . '%');
                        })
                        ->orWhere(function ($sub_query) use ($keyword) {
                            $sub_query->where('crm_requests.launched_by', '=', 2)
                                ->where('su.name', 'like', '%' . $keyword . '%');
                        })
                        ->orWhere(function ($sub_query) use ($keyword) {
                            $sub_query->where('crm_requests.launched_by', '=', 3)
                                ->where('cu.name', 'like', '%' . $keyword . '%');
                        });
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->filterColumn('tagged_to',function ($query,$keyword){
                if ($keyword != '') {
                    $query->where(function($sub_query) use ($keyword) {
                        $sub_query->where('crt.crm_request_tagging_type_id', '=', 1)
                            ->where('adp.name', 'like', '%' . $keyword . '%');
                    })
                    ->orWhere(function($sub_query) use ($keyword) {
                        $sub_query->where('crt.crm_request_tagging_type_id', '=', 2)
                            ->where('at.name', 'like', '%' . $keyword . '%');
                    });
                }
            })
            ->orderColumn('tagged_to', DB::raw('IF (crt.crm_request_tagging_type_id = 1, adp.name, IF (crt.crm_request_tagging_type_id = 2, at.name, ""))') . ' $1')
            ->orderColumn('launched_by_name', DB::raw('IF (crm_requests.launched_by = 0, a.name, IF (crm_requests.launched_by = 1, u.name, IF (crm_requests.launched_by = 2, su.name, "")))') . ' $1')
            ->editColumn('agent', function ($requests){
                if($requests->agent == null){
                    return '-';
                }
                else{
                    return $requests->agent;
                }
            })->filterColumn('case_nature_type',function ($query,$keyword){

                if ($keyword != '') {
                    $query->where('crcnt.id','=',$keyword);
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->editColumn('last_comment_name', function($requests){
                if($requests->last_comment_by == 0){
                    return $requests->last_comment_admin;
                }
                else if($requests->last_comment_by == 1){
                    return $requests->last_comment_shipper;
                }
                else{
                    return '-';
                }
            })
            ->filterColumn('last_comment_name', function($query, $keyword) {
                $keyword = strtolower($keyword);

                if ($keyword != '') {
                    $query->where(function ($sub_query) use ($keyword) {
                        $sub_query->where('ccs.comment_by', '=', 0)
                            ->where('accs.name', 'like', '%' . $keyword . '%');
                    })
                        ->orWhere(function ($sub_query) use ($keyword) {
                            $sub_query->where('ccs.comment_by', '=', 1)
                                ->where('uccs.name', 'like', '%' . $keyword . '%');
                        });
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->orderColumn('last_comment_name', DB::raw('IF (ccs.comment_by = 0, accs.name, IF (ccs.comment_by = 1, uccs.name, ""))') . ' $1')

            ->editColumn('last_comment', function($requests){
                if($requests->last_comment != null){
                    return $requests->last_comment;
                }
                else{
                    return '-';
                }
            })
            ->editColumn('last_comment_date', function($requests){
                if($requests->last_comment_date != null){
                    return $requests->last_comment_date;
                }
                else{
                    return '-';
                }
            })->addColumn('tagged_to_manual', function($requests){
                $crm_tagging = CrmRequestTagging::where('crm_request_id',$requests->id)->whereIn('crm_request_tagging_type_id', [1,2])->get()->first();
                if($crm_tagging){
                    if($crm_tagging->crm_request_tagging_type_id == 1){
                        
                        $tagged_name = AdminDepartment::find($crm_tagging->tagged_id)->name;
                        return $tagged_name;
                    }elseif($crm_tagging->crm_request_tagging_type_id == 2){
                        $tagged_name = Admin::find($crm_tagging->tagged_id)->name;
                        return $tagged_name;

                    }else{
                        return '-';
                    }
                }else{
                    return '-';
                }
            })
            ->filterColumn('tagged_to_manual',function ($query,$keyword){
                if ($keyword != '') {
                    $query->where(function($sub_query) use ($keyword) {
                        $sub_query->where('crt.crm_request_tagging_type_id', '=', 1)
                            ->where('adp.name', 'like', '%' . $keyword . '%');
                    })
                    ->orWhere(function($sub_query) use ($keyword) {
                        $sub_query->where('crt.crm_request_tagging_type_id', '=', 2)
                            ->where('at.name', 'like', '%' . $keyword . '%');
                    });
                }
            })
            ->orderColumn('tagged_to_manual', DB::raw('IF (crt.crm_request_tagging_type_id = 1, adp.name, IF (crt.crm_request_tagging_type_id = 2, at.name, ""))') . ' $1')
            
            ->addColumn('tagged_to_kae', function($requests){
                $crm_tagging = CrmRequestTagging::where('crm_request_id',$requests->id)->where('crm_request_tagging_type_id',4)->get()->first();
                if($crm_tagging){
                    $admin = Admin::find($crm_tagging->tagged_id);
                    return $admin->name;
                }else{
                    return '-';
                }

            })
            ->filterColumn('tagged_to_kae',function ($query,$keyword){
                if ($keyword != '') {
                    $query->where(function($sub_query) use ($keyword) {
                        $sub_query->where('crt.crm_request_tagging_type_id', '=', 4)
                            ->where('at.name', 'like', '%' . $keyword . '%');
                    });
                }
            })
            ->orderColumn('tagged_to_kae', DB::raw('IF (crt.crm_request_tagging_type_id = 4, at.name, "")') . ' $1')
            
            ->addColumn('tagged_to_operation', function($requests){
                $crm_tagging = CrmRequestTagging::where('crm_request_id',$requests->id)->where('crm_request_tagging_type_id',5)->get()->first();
                if($crm_tagging){
                    $admin = Admin::find($crm_tagging->tagged_id);
                    return $admin->name;
                }else{
                    return '-';
                }
            })
            ->filterColumn('tagged_to_operation',function ($query,$keyword){
                if ($keyword != '') {
                    $query->where(function($sub_query) use ($keyword) {
                        $sub_query->where('crt.crm_request_tagging_type_id', '=', 5)
                            ->where('at.name', 'like', '%' . $keyword . '%');
                    });
                }
            })
            ->orderColumn('tagged_to_operation', DB::raw('IF (crt.crm_request_tagging_type_id = 5, at.name, "")') . ' $1')
            
            ->addColumn('action', function($requests) {
                $route = route('admin.crm.request.details', ['id' => $requests->id]);
                    $dropdown = '
                  <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">';
                    $dropdown .= '<button onclick="window.open(\'' . $route . '\', \'_tab\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Details</div></button>';
                    $dropdown .= '</div>
                  </div>
                ';

                    return $dropdown;
            });

        if ($tracking_numbers = $request->get('tracking_numbers')) {
            $datatables->whereIn('s.tracking_number', explode(',', $tracking_numbers));
        }

        return $datatables->make(true);
    }
    public function closed_index(){
        ActivityTrailController::createActivityTrailLog(Auth::id(),316);
        $case_nature = CrmRequestCaseNature::select('id', 'name')->get();
        $case_nature_type = CrmRequestCaseNatureType::select('id', 'type')->get();
        $shipment_status = ShipmentStatus::select('id', 'name')->get();
        $channels = CrmRequestChannel::select('id', 'channel')->get();
        return view('admin.crm.closed')->with(['case_nature' => $case_nature, 'case_nature_type' => $case_nature_type, 'channels' => $channels, 'shipment_status' => $shipment_status]);
    }

    public function closed_list(Request $request){
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),317);
        }
        $count = CrmRequest::where('crm_requests.status_id', 4)->count();

        $closed_request = CrmRequest::leftjoin('crm_request_case_nature as crcn', 'crcn.id', '=', 'crm_requests.case_nature_id')
            ->leftjoin('crm_request_case_nature_types as crcnt', 'crcnt.id', '=', 'crm_requests.case_nature_type_id')
            ->leftjoin('crm_request_channels as crc', 'crc.id', '=', 'crm_requests.channel_id')
            ->leftjoin('admins as ad', 'ad.id', '=', 'crm_requests.agent_id')
            ->leftjoin('admins as a', function ($join) {
                $join->on('a.id', '=', 'crm_requests.launched_by_id')
                    ->where('crm_requests.launched_by', '=', DB::raw(0));
            })
            ->leftjoin('users as u', function ($join) {
                $join->on('u.id', '=', 'crm_requests.launched_by_id')
                    ->where('crm_requests.launched_by', '=', DB::raw(1));
            })
            ->leftjoin('substitute_users as su', function ($join) {
                $join->on('su.id', '=', 'crm_requests.launched_by_id')
                    ->where('crm_requests.launched_by', '=', DB::raw(2));
            })
            ->leftjoin('retail_users as ru', function ($join) {
                $join->on('ru.id', '=', 'crm_requests.launched_by_id')
                    ->where('crm_requests.launched_by', '=', DB::raw(3));
            })
            ->leftjoin('consignee_users as cu', function ($join) {
                $join->on('cu.id', '=', 'crm_requests.launched_by_id')
                    ->where('crm_requests.launched_by', '=', DB::raw(4));
            })
            ->leftjoin('shipments as s', 's.id', '=', 'crm_requests.shipment_id')
            ->leftjoin('shipment_status as ss', 'ss.id', '=', 's.shipper_status_id')
            ->leftjoin('user_shipping_infos AS usi', 'usi.id', '=', 's.pickup_address_id')
            ->leftjoin('users as user', 'user.id', '=', 'crm_requests.shipper_id')
            ->leftjoin('cities as oc', 'oc.id', '=', 'usi.city_id')
            ->leftjoin('cities as dc', 'dc.id', '=', 's.consignee_city_id')
            ->leftjoin('crm_request_status_histories as inp', function ($join) {
                $join->on('inp.crm_request_id', '=', 'crm_requests.id')
                    ->where('inp.id', '=',
                        DB::raw('(select max(id) from crm_request_status_histories where crm_request_status_histories.crm_request_id = crm_requests.id and crm_request_status_histories.status_id = 1)'));
            })
            ->leftjoin('crm_request_status_histories as res', function ($join) {
                $join->on('res.crm_request_id', '=', 'crm_requests.id')
                    ->where('res.id', '=',
                        DB::raw('(select max(id) from crm_request_status_histories where crm_request_status_histories.crm_request_id = crm_requests.id and crm_request_status_histories.status_id = 4)'));
            })
            ->leftjoin('crm_request_tagging_histories as crth', function ($join) {
                $join->on('crth.crm_request_id', '=', 'crm_requests.id')
                    ->where('crth.id', '=',
                        DB::raw('(select max(id) from crm_request_tagging_histories where crm_request_tagging_histories.crm_request_id = crm_requests.id)'));
            })
            ->leftjoin('sale_person_tags as spt', function($join){
                $join->on('spt.user_id', '=', 'crm_requests.shipper_id')
                    ->where('spt.id', '=', DB::raw('(select max(id) from sale_person_tags where sale_person_tags.user_id = crm_requests.shipper_id and sale_person_tags.status = 0)'));
            })
            ->leftjoin('admin_departments as adp', 'adp.id', '=', 'crth.tagged_id')
            ->leftjoin('admins as at', 'at.id', '=', 'crth.tagged_id')
            ->leftjoin('crm_request_status_histories as crsh', function($join){
                $join->on('crsh.crm_request_id', '=', 'crm_requests.id')
                    ->where('crsh.created_at', '=', DB::raw('(select max(created_at) from crm_request_status_histories where crm_request_status_histories.crm_request_id = crm_requests.id and crm_request_status_histories.status_id = 5)'));
            })
			->select('crm_requests.id as id', 's.tracking_number as tracking_number', 'crcn.name as case_nature', 'crcnt.type as case_nature_type', 'crc.channel as channel', 'ad.name as agent', 'a.name as name', 'u.name as shipper', 'su.name as sub_shipper', 'ru.name as retail_user', 'cu.name as consignee_user', 'crm_requests.launched_by as launched_added_by', 'crm_requests.created_at as created_at', 'crm_requests.description as description','inp.created_at as inprocess','res.created_at as closed_date', 'ss.name as status', 'user.name as shipper_name', 'oc.name as origin', 'dc.name as destination', 'crm_requests.launched_by_id','crm_requests.address as address', 'crm_requests.address_latitude as address_latitude','crm_requests.address_longitude as address_longitude','crsh.created_at as reopen_date')
            ->where('crm_requests.status_id', 4);

        if (!in_array(session('role_id'), [1, 6]) && !in_array(179, session('permissions')) && !in_array(201, session('permissions'))) {
            $closed_request = $closed_request->where(function ($sub_query) {
                $sub_query->where('crm_requests.agent_id', Auth::id())
                    ->orWhere(function ($sub_query) {
                        $sub_query->where('crm_requests.launched_by', 0)
                            ->where('crm_requests.launched_by_id', Auth::id());
                    })
                    ->orWhere(function ($parent_sub_query){
                        $parent_sub_query->orWhere(function ($sub_query) {
                            $sub_query->where('crth.crm_request_tagging_type_id', 2)
                                ->where('crth.tagged_id', '=', Auth::id());
                        })
                            ->orWhere(function ($sub_query) {
                                $sub_query->where('crth.crm_request_tagging_type_id', 1)
                                    ->where('adp.id', '=', session('department_id'))
                                    ->where(function ($sub_sub_query) {
                                        $sub_sub_query->whereIn('oc.hub_id', session('hubs'))
                                            ->orWhereIn('dc.hub_id', session('hubs'));
                                    });
                            });
                    });
            });
        }
        else if (session('department_id') == 7){
            if(!in_array(session('id'), session('sale_users_bypass'))){
                $closed_request = $closed_request->where('spt.admin_id', Auth::id());
            }
        }

        $datatables = Datatables::of($closed_request)
            ->setTotalRecords($count)
            ->addColumn('id_padded', function ($requests) {
                return str_pad($requests->id, 6, '0', STR_PAD_LEFT);
            })
            ->addColumn('id_padded_link', function ($requests) {
                return '<u><a href=' . route('admin.crm.request.details', ['id' => $requests->id]) . ' target="_blank">' . str_pad($requests->id, 6, '0', STR_PAD_LEFT). '</a></u>';
            })
            ->addColumn('tracking_number_hyperlink', function ($requests) {
                return '<u><a href=' . route('admin.tracking.index') . '?tracking_number=' . $requests->tracking_number . ' class="tracking" target="_blank">' . $requests->tracking_number . '</a></u>';
            })
            ->editColumn('added_by', function($requests){
                if($requests->launched_added_by == 0) {
                    return 'Admin';
                }
                else if($requests->launched_added_by == 1) {
                    return 'Shipper';
                }
                else if($requests->launched_added_by == 2){
                    return 'Shipper Substitute User';
                }
                else if($requests->launched_added_by == 3){
                    return 'Retail';
                }
                else if($requests->launched_added_by == 4){
                    return 'Consignee';
                }
            })
            ->editColumn('total_tat', function ($requests){
                    Carbon::setWeekendDays([
                        Carbon::SUNDAY,
                    ]);


                $re_open_count = CrmRequestStatusHistory::where('crm_request_id', $requests->id)->where('status_id' ,5)->latest('id');
                if($re_open_count->exists()) {
                    $re_open_count = $re_open_count->first();

                    $launched = Carbon::parse($requests->created_at);
                    $last_closed = CrmRequestStatusHistory::where('crm_request_id', $requests->id)->where('status_id' ,4)->where('created_at', '>=', $re_open_count->created_at)->first();
                    $closed = $last_closed->created_at;
                    $time_format = 'H:i';
                    $time_from = CrmSettings::where('name','TAT Cut-Off Time From')->first();
                    $time_to = CrmSettings::where('name','TAT Cut-Off Time To')->first();

                    $to_formatted = date($time_format, strtotime($time_to->setting_value));
                    $cut_off_check = $requests->created_at->format($time_format);
                    $current_tat = $closed->diffInWeekdays($launched);

                    $launched_check = $launched->toDateString();
                    $closed_check = $closed->toDateString();
                    if($launched_check <= $closed_check){
                        if($to_formatted < $cut_off_check){
                            $after_cut_off = $current_tat - 1;
                            $current_tat = $after_cut_off;
                        }
                    }
                    $holidays = CrmTatHolidays::whereBetween('holiday', [$launched, $closed])->get();
                    foreach($holidays as $holiday){
                        $holiday_formatted = date('Y-m-d H:i:s', strtotime($holiday->holiday));
                        $holiday_formatted_check = date('Y-m-d', strtotime($holiday->holiday));
                        $launched_formatted_check = date('Y-m-d', strtotime($launched));
                        if($launched < $holiday_formatted || $closed > $holiday_formatted){
                            if($holiday_formatted_check == $launched_formatted_check){
                                if($to_formatted < $cut_off_check){
                                    $after_cut_off = $current_tat + 1;
                                    $current_tat = $after_cut_off;
                                }
                            }
                            $after_holidays = $current_tat - 1;
                            $current_tat = $after_holidays;
                        }
                    }
                }
                else {
                    $launched = Carbon::parse($requests->created_at);
                    $first_closed = CrmRequestStatusHistory::where('crm_request_id', $requests->id)->where('status_id' ,4)->first();
                    if($first_closed){
                        $closed = $first_closed->created_at;
                    }
                    else{
                        $closed = Carbon::parse($requests->closed_date);
                    }
                    $time_format = 'H:i';
                    $time_from = CrmSettings::where('name','TAT Cut-Off Time From')->first();
                    $time_to = CrmSettings::where('name','TAT Cut-Off Time To')->first();
                    $to_formatted = date($time_format, strtotime($time_to->setting_value));
                    $cut_off_check = $requests->created_at->format($time_format);
                    $current_tat = $closed->diffInWeekdays($launched);

                    $launched_check = $launched->toDateString();
                    $closed_check = $closed->toDateString();
                    if($launched_check <= $closed_check){
                        if($to_formatted < $cut_off_check){
                            $after_cut_off = $current_tat - 1;
                            $current_tat = $after_cut_off;
                        }
                    }
                    $holidays = CrmTatHolidays::whereBetween('holiday', [$launched, $closed])->get();
                    foreach($holidays as $holiday){
                        $holiday_formatted = date('Y-m-d H:i:s', strtotime($holiday->holiday));
                        $holiday_formatted_check = date('Y-m-d', strtotime($holiday->holiday));
                        $launched_formatted_check = date('Y-m-d', strtotime($launched));
                        if($launched < $holiday_formatted || $closed > $holiday_formatted){
                            if($holiday_formatted_check == $launched_formatted_check){
                                if($to_formatted < $cut_off_check){
                                    $after_cut_off = $current_tat + 1;
                                    $current_tat = $after_cut_off;
                                }
                            }
                            $after_holidays = $current_tat - 1;
                            $current_tat = $after_holidays;
                        }
                    }
                }
                    return $current_tat;
            })
            ->addColumn('launched_by_name', function ($requests){
                $name = '';
                if($requests->launched_added_by == 0){
                    $name = $requests->name;
                }
                else if($requests->launched_added_by == 1){
                    $name = $requests->shipper;
                }else if($requests->launched_added_by == 2){
                    $name = $requests->sub_shipper;
                }else if($requests->launched_added_by == 3){
                    $name = $requests->retail_user;
                }else if($requests->launched_added_by == 4){
                    $name = $requests->consignee_user;
                }
                return $name;
            })
            ->filterColumn('status',function ($query,$keyword){

                if ($keyword != '') {
                    $query->where('ss.id',$keyword);
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->filterColumn('launched_by_name', function($query, $keyword) {
                $keyword = strtolower($keyword);

                if ($keyword != '') {
                    $query->where(function ($sub_query) use ($keyword) {
                        $sub_query->where('crm_requests.launched_by', '=', 0)
                            ->where('a.name', 'like', '%' . $keyword . '%');
                    })
                        ->orWhere(function ($sub_query) use ($keyword) {
                            $sub_query->where('crm_requests.launched_by', '=', 1)
                                ->where('u.name', 'like', '%' . $keyword . '%');
                        })
                        ->orWhere(function ($sub_query) use ($keyword) {
                            $sub_query->where('crm_requests.launched_by', '=', 2)
                                ->where('su.name', 'like', '%' . $keyword . '%');
                        })
                        ->orWhere(function ($sub_query) use ($keyword) {
                            $sub_query->where('crm_requests.launched_by', '=', 3)
                                ->where('cu.name', 'like', '%' . $keyword . '%');
                        });
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->orderColumn('launched_by_name', DB::raw('IF (crm_requests.launched_by = 0, a.name, IF (crm_requests.launched_by = 1, u.name, IF (crm_requests.launched_by = 2, su.name, "")))') . ' $1')
            ->editColumn('agent', function ($requests){
                if($requests->agent == null){
                    return '-';
                }
                else{
                    return $requests->agent;
                }
            })
            ->filterColumn('case_nature_type',function ($query,$keyword){

                if ($keyword != '') {
                    $query->where('crcnt.id','=',$keyword);
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->addColumn('action', function($requests) {
                $route = route('admin.crm.request.details', ['id' => $requests->id]);
                    $dropdown = '
                  <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">';
                    $dropdown .= '<button onclick="window.open(\'' . $route . '\', \'_tab\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Details</div></button>';
                    $dropdown .= '</div>
                  </div>
                ';

                    return $dropdown;
            });

        if ($tracking_numbers = $request->get('tracking_numbers')) {
            $datatables->whereIn('s.tracking_number', explode(',', $tracking_numbers));
        }

        return $datatables->make(true);
    }

    public function assign(Request $request){
        if(!empty($request->crm_request_ids)){
            foreach ($request->crm_request_ids as $crm_request_id)
            {
                $crm_requests = CrmRequest::find($crm_request_id);
                if ($crm_request_id == $crm_requests->id) {
                    CrmRequestAgentHistory::create([
                        'crm_request_id' => $crm_requests->id,
                        'agent_id' => $request->admin_id,
                        'assigned_by' => Auth::id()
                    ]);
                    $crm_requests->agent_id = $request->admin_id;
                    $crm_requests->save();
                }
            }
            return ['status' => 0, 'success' => 'Request(s) has been Assigned'];
        }
    }

    public function valid(Request $request)
    {
        $crm_request = CrmRequest::where('id', $request->req_id)->first();
        if ($crm_request['agent_id'] != null) {
            if ($request->prev_status == 1 || $request->prev_status == 5) {
                if ($crm_request['status_id'] != 2) {
                    CrmRequest::where('id', $request->req_id)->update([
                        'status_id' => 2,
                    ]);
                    CrmRequestStatusHistory::create([
                        'crm_request_id' => $request->req_id,
                        'status_id' => 6,
                        'agent_id' => Auth::id()
                    ]);
                    NotificationsController::send(41, $request->req_id);
                    CrmRequestStatusHistory::create([
                        'crm_request_id' => $request->req_id,
                        'status_id' => 2,
                        'agent_id' => Auth::id()
                    ]);
                    if($crm_request->case_nature_type_id == 2 && $crm_request->shipment_id != null){
                        self::delay_in_delivery_shipment_add($crm_request->id, $crm_request->shipment_id);
                    }
                    if($request->prev_status == 1){
                        if($crm_request->case_nature_type_id == 1 && $crm_request->shipment_id != null){
                            self::automation_payment_add($crm_request->id, $crm_request->shipment_id);
                        }
                    }
                    if($crm_request->case_nature_id == 4){
                        NotificationsController::send(117, $crm_request->id, 6);
                    }
                    if($crm_request->shipper_id){
                        $sales_tier_tag = SaleTierTag::where('user_id', $crm_request->shipper_id);
                        if($sales_tier_tag->exists()){
                            $sales_tier_tag = $sales_tier_tag->first();
                            $tagged_id = $sales_tier_tag->kam;
                            $kam_admin = Admin::find($tagged_id);
                            if($kam_admin){
                                if($kam_admin->status){
                                    if($tagged_id){
                                        $tagged_crm_request = CrmRequestTagging::where('crm_request_id', $crm_request->id)->where('crm_request_tagging_type_id',4)->first();
                                        if(!empty($tagged_crm_request)){
                                            if($tagged_crm_request['tagged_id'] != $tagged_id) {
                                                CrmRequestTagging::where('crm_request_id', $crm_request->id)->where('crm_request_tagging_type_id',4)->update([
                                                    'crm_request_tagging_type_id' => 4,
                                                    'tagged_id' => $tagged_id
                                                ]);

                                                CrmRequestTaggingHistory::create([
                                                    'crm_request_id' => $crm_request->id,
                                                    'crm_request_tagging_type_id' => 4,
                                                    'tagged_id' => $tagged_id,
                                                    'agent_id' => 306,
                                                    'hub_id' => NULL
                                                ]);
                                                NotificationsController::send(31,$crm_request->id);
                                            }
                                        }
                                        else{
                                            CrmRequestTagging::create([
                                                'crm_request_id' => $crm_request->id,
                                                'crm_request_tagging_type_id' => 4,
                                                'tagged_id' => $tagged_id,
                                                'hub_id' => NULL
                                            ]);

                                            CrmRequestTaggingHistory::create([
                                                'crm_request_id' => $crm_request->id,
                                                'crm_request_tagging_type_id' => 4,
                                                'tagged_id' => $tagged_id,
                                                'agent_id' => 306,
                                                'hub_id' => NULL
                                            ]);
                                            NotificationsController::send(31,$crm_request->id);
                                        }
                                    }
                                }
                            }

                        }
                    }
                        if($crm_request->case_nature_type_id == 3 || $crm_request->case_nature_type_id == 5){
                            $crm_city_id = $crm_request->shipment->pickup_address->city->id;

                        }else{
                            $crm_city_id = $crm_request->shipment->consignee_city_id;
                        }
                        if($crm_request->case_nature_type_id != 1){
                            $crm_auto_tag_user = CrmAutoTagUser::where('city_id',$crm_city_id)->where('status',1);
                            if($crm_auto_tag_user->exists()){

                                $crm_auto_tag_user = $crm_auto_tag_user->get()->first();
                                $tagged_crm_request = CrmRequestTagging::where('crm_request_id', $crm_request->id)->where('crm_request_tagging_type_id',5)->first();
                                if(!empty($tagged_crm_request)){
                                    if($tagged_crm_request['tagged_id'] != $crm_auto_tag_user->admin_id) {
                                        CrmRequestTagging::where('crm_request_id', $crm_request->id)->where('crm_request_tagging_type_id',5)->update([
                                            'crm_request_tagging_type_id' => 5,
                                            'tagged_id' => $crm_auto_tag_user->admin_id
                                        ]);

                                        CrmRequestTaggingHistory::create([
                                            'crm_request_id' => $crm_request->id,
                                            'crm_request_tagging_type_id' => 5,
                                            'tagged_id' => $crm_auto_tag_user->admin_id,
                                            'agent_id' => 306,
                                            'hub_id' => NULL
                                        ]);
                                        NotificationsController::send(31,$crm_request->id);
                                    }
                                }
                                else{
                                    CrmRequestTagging::create([
                                        'crm_request_id' => $crm_request->id,
                                        'crm_request_tagging_type_id' => 5,
                                        'tagged_id' => $crm_auto_tag_user->admin_id,
                                        'hub_id' => NULL
                                    ]);

                                    CrmRequestTaggingHistory::create([
                                        'crm_request_id' => $crm_request->id,
                                        'crm_request_tagging_type_id' => 5,
                                        'tagged_id' => $crm_auto_tag_user->admin_id,
                                        'agent_id' => 306,
                                        'hub_id' => NULL
                                    ]);
                                    NotificationsController::send(31,$crm_request->id);
                                }
                            }
                        }
                        
                    
                    return redirect()->back()->with(['success' => 'Request marked as In-Process']);
                }
                else {
                    return redirect()->back()->with(['error' => 'Request is already marked as In-Process']);
                }
            }
            if ($request->prev_status == 2) {
                if ($crm_request['status_id'] != 3) {
                    CrmRequest::where('id', $request->req_id)->update([
                        'status_id' => 3
                    ]);
                    CrmRequestStatusHistory::create([
                        'crm_request_id' => $request->req_id,
                        'status_id' => 3,
                        'agent_id' => Auth::id()
                    ]);
                    if(DelayInDeliveryShipment::where('crm_request_id', $request->req_id)->exists()){
                        DelayInDeliveryShipment::where('crm_request_id', $request->req_id)->delete();
                    }
                    if(CrmPaymentShipment::where('crm_request_id', $request->req_id)->exists()){
                        CrmPaymentShipment::where('crm_request_id', $request->req_id)->delete();
                    }
                    return redirect()->back()->with(['success' => 'Request marked as Resolved']);
                } else {
                    return redirect()->back()->with(['error' => 'Request is already marked as Resolved']);
                }
            }
            if ($request->prev_status == 3) {
                if ($crm_request['status_id'] != 4) {
                    CrmRequest::where('id', $request->req_id)->update([
                        'status_id' => 4
                    ]);
                    CrmRequestStatusHistory::create([
                        'crm_request_id' => $request->req_id,
                        'status_id' => 4,
                        'agent_id' => Auth::id()
                    ]);

                    if($crm_request->case_nature_id == 4) {
                        NotificationsController::send(117, $crm_request->id, 4);
                    }
                    CrmRequestTagging::where('crm_request_id', $request->id)->delete();
                    return redirect()->back()->with(['success' => 'Request marked as Closed']);
                } else {
                    return redirect()->back()->with(['error' => 'Request is already marked as Closed']);
                }
            }
            if ($request->prev_status == 4) {
                if ($crm_request['status_id'] != 5) {
                    CrmRequest::where('id', $request->req_id)->update([
                        'status_id' => 5
                    ]);
                    CrmRequestStatusHistory::create([
                        'crm_request_id' => $request->req_id,
                        'status_id' => 5,
                        'agent_id' => Auth::id()
                    ]);
                    return redirect()->back()->with(['success' => 'Request marked as Re-Open']);
                } else {
                    return redirect()->back()->with(['error' => 'Request is already marked as Re-Open']);
                }
            }
        }
        else{
            return redirect()->back()->with(['error' => 'Agent is not assigned yet']);
        }
    }

    static public function delay_in_delivery_shipment_add($request_id, $shipment_id){

        $delay_in_delivery = new DelayInDeliveryShipment();
        $delay_in_delivery->crm_request_id = $request_id;
        $delay_in_delivery->shipment_id = $shipment_id;
        $delay_in_delivery->save();

    }

    static public function automation_payment_add($request_id, $shipment_id){

        $payment = new CrmPaymentShipment();
        $payment->crm_request_id = $request_id;
        $payment->shipment_id = $shipment_id;
        $payment->save();

    }

    public function bulk_re_open(Request $request){

        if(count($request->crm_request_ids) > 0){
            foreach ($request->crm_request_ids as $crm_request_id)
            {
                $crm_requests = CrmRequest::find($crm_request_id);
                if ($crm_requests) {
                    $crm_requests->status_id = 5;
                    $crm_requests->save();
                    CrmRequestStatusHistory::create([
                        'crm_request_id' => $crm_requests->id,
                        'status_id' => 5,
                        'agent_id' => Auth::id()
                    ]);
                    $crm_requests->agent_id = $request->admin_id;
                    $crm_requests->save();
                }
            }
            return ['status' => 0, 'success' => 'Request(s) has been Re-Opened'];
        }

    }

    public function invalid(Request $request)
    {
        $crm_request = CrmRequest::where('id', $request->req_id)->first();
        if ($crm_request['agent_id'] != null) {
            if($crm_request['status_id'] == 2){
                if(DelayInDeliveryShipment::where('crm_request_id', $request->req_id)->exists()){
                    DelayInDeliveryShipment::where('crm_request_id', $request->req_id)->delete();
                }
                if(CrmPaymentShipment::where('crm_request_id', $request->req_id)->exists()){
                    CrmPaymentShipment::where('crm_request_id', $request->req_id)->delete();
                }
            }
            if ($crm_request['status_id'] != 4) {
                CrmRequest::where('id', $request->req_id)->update([
                    'status_id' => 4,
                ]);
                if($request->close == 0 || (!$request->has('resolved_close'))){
                    CrmRequestStatusHistory::create([
                        'crm_request_id' => $request->req_id,
                        'status_id' => 7,
                        'agent_id' => Auth::id()
                    ]);
                    if($crm_request->case_nature_id == 4){
                        NotificationsController::send(117, $crm_request->id, 7);
                    }
                }
                else{
                    if($crm_request->case_nature_id == 4){
                        NotificationsController::send(117, $crm_request->id, 4);
                    }
                }
                CrmRequestStatusHistory::create([
                    'crm_request_id' => $request->req_id,
                    'status_id' => 4,
                    'agent_id' => Auth::id()
                ]);
                NotificationsController::send(31,$crm_request->id);
                NotificationsController::send(142,$crm_request->id);
                CrmRequestTagging::where('crm_request_id', $request->id)->delete();
                return redirect()->back()->with(['success' => 'Request marked as Closed']);
            } else {
                return redirect()->back()->with(['error' => 'Request is already marked as Closed']);
            }
        }
        else{
            return redirect()->back()->with(['error' => 'Agent is not assigned yet']);
        }
    }

    public function close(Request $request){
        $crm_requests = $request->crm_request_ids;
        if($crm_requests){
            foreach ($crm_requests as $crm_request_id){
                CrmRequest::where('id', $crm_request_id)->update([
                    'status_id' => 4
                ]);
                CrmRequestStatusHistory::create([
                    'crm_request_id' => $crm_request_id,
                    'status_id' => 3,
                    'agent_id' => Auth::id()
                ]);
                CrmRequestStatusHistory::create([
                    'crm_request_id' => $crm_request_id,
                    'status_id' => 4,
                    'agent_id' => Auth::id()
                ]);
                $crm_request = CrmRequest::find($crm_request_id);
                if($crm_request->case_nature_id == 4){
                    NotificationsController::send(117, $crm_request->id, 4);
                }

                NotificationsController::send(31,$crm_request_id);
                NotificationsController::send(142,$crm_request_id);
            }
            return ['status' => 0, 'success' => 'Request marked as Closed'];
        }
        return ['status' => 1, 'error' => 'Requests does\'nt exists'];
    }

    public function admin_tag(Request $request){
        $crm_request = CrmRequest::where('id', $request->crm_request_id)->first();
        $tagged_hub = null;
        if($request->tagged_hub != null){
            $tagged_hub = $request->tagged_hub;
        }
        if($request->crm_request_tagging_type_id == 1){
            $name = AdminDepartment::where('id', $request->tagged_id)->first();
        }
        else if($request->crm_request_tagging_type_id == 2){
            $name = Admin::where('id', $request->tagged_id)->first();
        }
        $tagged_crm_request = CrmRequestTagging::where('crm_request_id', $request->crm_request_id)->whereNotIn('crm_request_tagging_type_id', [4,5])->first();
        if(!empty($tagged_crm_request)){
            if($tagged_crm_request['tagged_id'] != $request->tagged_id) {

                CrmRequestTagging::where('crm_request_id', $request->crm_request_id)->whereNotIn('crm_request_tagging_type_id', [4,5])->delete();

                CrmRequestTagging::create([
                    'crm_request_id' => $request->crm_request_id,
                    'crm_request_tagging_type_id' => $request->crm_request_tagging_type_id,
                    'tagged_id' => $request->tagged_id,
                    'hub_id' => $tagged_hub
                ]);

                CrmRequestTaggingHistory::create([
                    'crm_request_id' => $request->crm_request_id,
                    'crm_request_tagging_type_id' => $request->crm_request_tagging_type_id,
                    'tagged_id' => $request->tagged_id,
                    'agent_id' => Auth::id(),
                    'hub_id' => $tagged_hub
                ]);

                if ($request->prev_status == 3) {
                    CrmRequest::where('id', $request->crm_request_id)->update([
                        'status_id' => 2
                    ]);
                    CrmRequestStatusHistory::create([
                        'crm_request_id' => $request->crm_request_id,
                        'status_id' => 2,
                        'agent_id' => Auth::id()
                    ]);
                }
                NotificationsController::send(31,$request->crm_request_id);
            }
            else{
                return ['status' => 1, 'error' => 'Request is already tagged to ' . $name['name']];
            }
        }
        else{
            CrmRequestTagging::create([
                'crm_request_id' => $request->crm_request_id,
                'crm_request_tagging_type_id' => $request->crm_request_tagging_type_id,
                'tagged_id' => $request->tagged_id,
                'hub_id' => $tagged_hub
            ]);

            CrmRequestTaggingHistory::create([
                'crm_request_id' => $request->crm_request_id,
                'crm_request_tagging_type_id' => $request->crm_request_tagging_type_id,
                'tagged_id' => $request->tagged_id,
                'agent_id' => Auth::id(),
                'hub_id' => $tagged_hub
            ]);
            NotificationsController::send(31,$request->crm_request_id);
        }
        return ['status' => 0, 'success' => 'Request successfully tagged to ' . $name['name']];
    }
   // public function request_info(Request $request){
   //     $request_id = $request->request_id;
   //     $request_details = CrmRequest::find($request_id);
   //     if($request_details){
   //         $tracking_number = '';
   //         if($request_details->shipment_id != null){
   //             $tracking_number = Shipment::find($request_details->shipment_id)->tracking_number;
   //         }
   //         return ['status' => 1, 'details' => $request_details, 'tracking_number' => $tracking_number];
   //     }else{
   //         return ['status' => 0, 'error' => 'Request ID not found!'];
   //     }
   // }
    public function bulk_admin_tag(Request $request){
        if(count($request->crm_request_ids) > 0){
            $tagged_hub = null;
            if($request->tagged_hub != null){
                $tagged_hub = $request->tagged_hub;
            }
            foreach($request->crm_request_ids as $crm_request_id){
                $crm_request = CrmRequest::where('id', $crm_request_id)->first();
                if($request->crm_request_tagging_type_id == 1){
                    $name = AdminDepartment::where('id', $request->tagged_id)->first();
                }
                else if($request->crm_request_tagging_type_id == 2){
                    $name = Admin::where('id', $request->tagged_id)->first();
                }
                $tagged_crm_request = CrmRequestTagging::where('crm_request_id', $crm_request_id)->whereNotIn('crm_request_tagging_type_id', [4,5])->first();
                if(!empty($tagged_crm_request)){
                    if($tagged_crm_request['tagged_id'] != $request->tagged_id) {
                        
                        CrmRequestTagging::where('crm_request_id', $crm_request_id)->whereNotIn('crm_request_tagging_type_id', [4,5])->delete();

                        CrmRequestTagging::create([
                            'crm_request_id' => $crm_request_id,
                            'crm_request_tagging_type_id' => $request->crm_request_tagging_type_id,
                            'tagged_id' => $request->tagged_id,
                            'hub_id' => $tagged_hub
                        ]);
                        // CrmRequestTagging::where('crm_request_id', $crm_request_id)->update([
                        //     'crm_request_tagging_type_id' => $request->crm_request_tagging_type_id,
                        //     'tagged_id' => $request->tagged_id,
                        //     'hub_id' => $tagged_hub
                        // ]);

                        CrmRequestTaggingHistory::create([
                            'crm_request_id' => $crm_request_id,
                            'crm_request_tagging_type_id' => $request->crm_request_tagging_type_id,
                            'tagged_id' => $request->tagged_id,
                            'agent_id' => Auth::id(),
                            'hub_id' => $tagged_hub
                        ]);

                        NotificationsController::send(31,$crm_request_id);
                    }
                }
                else{
                    CrmRequestTagging::create([
                        'crm_request_id' => $crm_request_id,
                        'crm_request_tagging_type_id' => $request->crm_request_tagging_type_id,
                        'tagged_id' => $request->tagged_id,
                        'hub_id' => $tagged_hub
                    ]);

                    CrmRequestTaggingHistory::create([
                        'crm_request_id' => $crm_request_id,
                        'crm_request_tagging_type_id' => $request->crm_request_tagging_type_id,
                        'tagged_id' => $request->tagged_id,
                        'agent_id' => Auth::id(),
                        'hub_id' => $tagged_hub
                    ]);
                    NotificationsController::send(31,$crm_request_id);
                }
            }
            
            return ['status' => 0, 'success' => 'Request(s) successfully tagged to ' . $name['name']];
        }
    }

    public function admin_un_tag(Request $request){
        if($request->multiple == 1){
            if(count($request->crm_request_ids) > 0){
                foreach($request->crm_request_ids as $crm_request_id){
                    $check_previous = CrmRequestTagging::where('crm_request_id', $crm_request_id)->whereNotIn('crm_request_tagging_type_id', [4,5])->first();
                    if($check_previous){
                        CrmRequestTagging::where('crm_request_id', $crm_request_id)->whereNotIn('crm_request_tagging_type_id', [4,5])->delete();
                       
                        CrmRequestTagging::create([
                            'crm_request_id' => $crm_request_id,
                            'crm_request_tagging_type_id' => 3,
                            'tagged_id' => null
                        ]);
                        CrmRequestTagging::where('crm_request_id', $crm_request_id)->update([
                        ]);
                        CrmRequestTaggingHistory::create([
                            'crm_request_id' => $crm_request_id,
                            'crm_request_tagging_type_id' => 3,
                            'tagged_id' => null,
                            'agent_id' => Auth::id()
                        ]);
                    }
                }
            }
            return ['status' => 0, 'success' => 'Request(s) successfully un tagged'];
        }
        else{
            $crm_request_id = $request->crm_request_id;
            $check_previous = CrmRequestTagging::where('crm_request_id', $crm_request_id)->whereNotIn('crm_request_tagging_type_id', [4,5])->first();
            if($check_previous){
                CrmRequestTagging::where('crm_request_id', $crm_request_id)->whereNotIn('crm_request_tagging_type_id', [4,5])->delete();
                        CrmRequestTagging::create([
                            'crm_request_id' => $crm_request_id,
                            'crm_request_tagging_type_id' => 3,
                            'tagged_id' => null
                        ]);

               
                CrmRequestTaggingHistory::create([
                    'crm_request_id' => $crm_request_id,
                    'crm_request_tagging_type_id' => 3,
                    'tagged_id' => null,
                    'agent_id' => Auth::id()
                ]);
            }
            return ['status' => 0, 'success' => 'Request(s) successfully un tagged'];
        }
    }

    public function crm_index(){
        $departments = AdminDepartment::where('id', '!=', 1)->get(['id', 'name']);
        return view('admin.crm.index')->with(['departments' => $departments]);
    }

    public function crm_list(){
        $roles = AdminRole::join('admin_departments as ad', 'admin_roles.department_id', '=', 'ad.id')
            ->join('admins as a', 'admin_roles.updated_by', '=', 'a.id')
            ->select('admin_roles.id', 'admin_roles.name', 'ad.name as department', 'admin_roles.created_at', 'admin_roles.updated_at', 'a.name as updated_by')
            
            ->where('admin_roles.department_id', '!=', 1);

        $datatables = Datatables::of($roles)
            ->addColumn('action', function($roles) {
                if (session('role_id') == 1 || session('role_id') == 6 || in_array(188, session('permissions'))) {
                    return '<div class="btn-group">
                          <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                          <div class="dropdown-menu dropdown-menu-sm">
                            <button type="button" class="dropdown-item edit"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>
                          </div>
                        </div>
                ';
                }
                else {
                    return '';
                }
            });

        return $datatables->make(true);

    }

    public function crm_update_index($id){
        $departments = AdminDepartment::where('id', '!=', 1)->get(['id', 'name']);
        $modules = Module::with('permissions')->where('id', '=', 18)->get();
        $role = AdminRole::find($id);
        $permissions = $role->module_permissions->pluck('permission_id')->toArray();

        if ($role->id != 1) {
            return view('admin.crm.update.index')->with(['departments' => $departments, 'modules' => $modules, 'role' => $role, 'permissions' => $permissions]);
        }
        else {
            return redirect()->route('admin.access_denied');
        }
    }

    public function crm_update_store(Request $request, $id) {
        $admin_role = AdminRole::find($id);
        $admin_role->updated_by = Auth::id();

        $admin_role->save();

        if ($request->has('permission_ids')) {
            $current_permission_ids = AdminRoleModulePermission::where('role_id', $id)->pluck('permission_id')->toArray();;

            $crm_module_permission = ModulePermission::where('module_id', '!=', 18)->pluck('id')->toArray();
            $crm_module_main_permission = ModulePermission::where('id', '=', 188)->first();
            $delete_permission_ids = array_diff($current_permission_ids, $request->input('permission_ids'));
            $new_permission_ids = array_diff($request->input('permission_ids'), $current_permission_ids);

            AdminRoleModulePermission::where('role_id', $id)->where('permission_id', '!=', $crm_module_main_permission['id'])->whereNotIn('permission_id', $crm_module_permission)->whereIn('permission_id', $delete_permission_ids)->delete();

            foreach($new_permission_ids as $permission_id) {
                $admin_role_module_permission = new AdminRoleModulePermission();

                $admin_role_module_permission->role_id = $id;
                $admin_role_module_permission->permission_id = $permission_id;

                $admin_role_module_permission->save();
            }
        }
        else {
            AdminRoleModulePermission::where('role_id', $id)->delete();
        }

        return redirect()->route('admin.crm.permissions')->with(['success' => 'CRM Permissions: ' . $request->input('name') . ' has been updated!']);
    }

    public function edit_request(Request $request){
        $crm_request_id = $request->request_id;
        $crm_details = CrmRequest::where('id', $crm_request_id)->first();
        $shipment = Shipment::where('tracking_number', $request->tracking_number)->first();
        if($shipment != null){
            if($crm_details['shipment_id'] == null){
                $shipment_id = $shipment['id'];
            }
            else{
                $shipment_id = $crm_details['shipment_id'];
            }
            $crm_check = CrmRequest::where('shipment_id', $shipment_id)->where('case_nature_id', $request->case_nature_id)->where('case_nature_type_id', $request->complaint_id)->first();
            if($crm_check == null){
                if($request->case_nature_select == 4){
                    if($request->hasFile('product_picture')){
                        $filename = 'claim_product_' . $crm_request_id . '.png';
                        $file = $request->file('product_picture');
                        Storage::disk('public')->putFileAs('crm_claims', $file, $filename);
                        $product_picture = $filename;
                    }
                    else{
                        $product_picture = null;
                    }

                    if($request->hasFile('invoice_picture')){
                        $filename = 'claim_invoice_' . $crm_request_id . '.png';
                        $file = $request->file('invoice_picture');
                        Storage::disk('public')->putFileAs('crm_claims', $file, $filename);
                        $invoice_picture = $filename;
                    }
                    else{
                        $invoice_picture = null;
                    }

                    CrmRequest::where('id', $crm_request_id)->update([
                        'shipment_id' => $shipment_id,
                        'case_nature_id' => $request->case_nature_select,
                        'case_nature_type_id' => $request->case_nature_claim,
                        'product_cost' => $request->claim_product_cost,
                        'product_picture' => $product_picture,
                        'invoice_picture' => $invoice_picture,
                    ]);
                    CrmRequestCaseNatureAndTypeHistory::create([
                        'crm_request_id' => $crm_request_id,
                        'case_nature_id' => $crm_details['case_nature_id'],
                        'case_nature_type_id' => $crm_details['case_nature_type_id'],
                        'description' => null,
                        'edited_by' => Auth::id()
                    ]);
                }
                else{
                    CrmRequest::where('id', $crm_request_id)->update([
                        'shipment_id' => $shipment_id,
                        'case_nature_id' => $request->case_nature_id,
                        'case_nature_type_id' => $request->complaint_id,
                        'description' => $request->description,
                    ]);
                    CrmRequestCaseNatureAndTypeHistory::create([
                        'crm_request_id' => $crm_request_id,
                        'case_nature_id' => $crm_details['case_nature_id'],
                        'case_nature_type_id' => $crm_details['case_nature_type_id'],
                        'description' => $crm_details['description'],
                        'edited_by' => Auth::id()
                    ]);
                }
                return ['status' => 0, 'success' => 'Request Edited Successfully'];
            }
            else{
                if($crm_request_id == 1){
                    return ['status' => 1, 'error' => 'Complaint already lodged for Tracking Number: ' . $request->tracking_number];
                }
                else{
                    return ['status' => 1, 'error' => 'Request already lodged for Tracking Number: ' . $request->tracking_number];
                }
            }
        }
        else{
            return ['status' => 1, 'error' => 'Tracking Number: ' . $request->tracking_number . ' doesn\'t exists'];
        }
    }
    public function bulk_valid_invalid(Request $request){
        foreach ($request->crm_request_ids as $crm_request_id)
        {
            $crm_request = CrmRequest::find($crm_request_id);
            if ($crm_request->agent_id != null) {
                if (session('role_id') == 1 || session('role_id') == 6 || $crm_request->agent_id == Auth::id() || in_array(184, session('permissions')))
                    {
                    if ($crm_request->status_id == 1 || $crm_request->status_id == 5) {
                        if($request->valid == 1){
                            CrmRequest::where('id', $crm_request->id)->update([
                                'status_id' => 2,
                            ]);
                            CrmRequestStatusHistory::create([
                                'crm_request_id' => $crm_request->id,
                                'status_id' => 6,
                                'agent_id' => Auth::id()
                            ]);
                            NotificationsController::send(41, $crm_request->id);
                            CrmRequestStatusHistory::create([
                                'crm_request_id' => $crm_request->id,
                                'status_id' => 2,
                                'agent_id' => Auth::id()
                            ]);
                            if($crm_request->case_nature_type_id == 2 && $crm_request->shipment_id != null){
                                self::delay_in_delivery_shipment_add($crm_request->id, $crm_request->shipment_id);
                            }
                            if($crm_request->status_id == 1){
                                if($crm_request->case_nature_type_id == 1 && $crm_request->shipment_id != null){
                                    self::automation_payment_add($crm_request->id, $crm_request->shipment_id);
                                }
                            }

                            if($crm_request->case_nature_id == 4){
                                NotificationsController::send(117, $crm_request->id, 6);
                            }

                            if($crm_request->case_nature_type_id == 3 || $crm_request->case_nature_type_id == 5){
                                $crm_city_id = $crm_request->shipment->pickup_address->city->id;
    
                            }else{
                                $crm_city_id = $crm_request->shipment->consignee_city_id;
                            }
                            if($crm_request->shipper_id){
                                $sales_tier_tag = SaleTierTag::where('user_id', $crm_request->shipper_id);
                                if($sales_tier_tag->exists()){
                                    $sales_tier_tag = $sales_tier_tag->first();
                                    $tagged_id = $sales_tier_tag->kam;
                                    $kam_admin = Admin::find($tagged_id);
                                    if($kam_admin){
                                        if($kam_admin->status){
                                            if($tagged_id){
                                                $tagged_crm_request = CrmRequestTagging::where('crm_request_id', $crm_request->id)->where('crm_request_tagging_type_id',4)->first();
                                                if(!empty($tagged_crm_request)){
                                                    if($tagged_crm_request['tagged_id'] != $tagged_id) {
                                                        CrmRequestTagging::where('crm_request_id', $crm_request->id)->where('crm_request_tagging_type_id',4)->update([
                                                            'crm_request_tagging_type_id' => 4,
                                                            'tagged_id' => $tagged_id
                                                        ]);
        
                                                        CrmRequestTaggingHistory::create([
                                                            'crm_request_id' => $crm_request->id,
                                                            'crm_request_tagging_type_id' => 4,
                                                            'tagged_id' => $tagged_id,
                                                            'agent_id' => 306,
                                                            'hub_id' => NULL
                                                        ]);
                                                        NotificationsController::send(31,$crm_request->id);
                                                    }
                                                }
                                                else{
                                                    CrmRequestTagging::create([
                                                        'crm_request_id' => $crm_request->id,
                                                        'crm_request_tagging_type_id' => 4,
                                                        'tagged_id' => $tagged_id,
                                                        'hub_id' => NULL
                                                    ]);
        
                                                    CrmRequestTaggingHistory::create([
                                                        'crm_request_id' => $crm_request->id,
                                                        'crm_request_tagging_type_id' => 4,
                                                        'tagged_id' => $tagged_id,
                                                        'agent_id' => 306,
                                                        'hub_id' => NULL
                                                    ]);
                                                    NotificationsController::send(31,$crm_request->id);
                                                }
                                            }
                                        }
                                    }
        
                                }
                            }
    
                            $crm_auto_tag_user = CrmAutoTagUser::where('city_id',$crm_city_id)->where('status',1);
                            if($crm_auto_tag_user->exists()){
    
                                $crm_auto_tag_user = $crm_auto_tag_user->get()->first();
                                $tagged_crm_request = CrmRequestTagging::where('crm_request_id', $crm_request->id)->where('crm_request_tagging_type_id',5)->first();
                                if(!empty($tagged_crm_request)){
                                    if($tagged_crm_request['tagged_id'] != $crm_auto_tag_user->admin_id) {
                                        CrmRequestTagging::where('crm_request_id', $crm_request->id)->where('crm_request_tagging_type_id',5)->update([
                                            'crm_request_tagging_type_id' => 5,
                                            'tagged_id' => $crm_auto_tag_user->admin_id
                                        ]);
    
                                        CrmRequestTaggingHistory::create([
                                            'crm_request_id' => $crm_request->id,
                                            'crm_request_tagging_type_id' => 5,
                                            'tagged_id' => $crm_auto_tag_user->admin_id,
                                            'agent_id' => 306,
                                            'hub_id' => NULL
                                        ]);
                                        NotificationsController::send(31,$crm_request->id);
                                    }
                                }
                                else{
                                    CrmRequestTagging::create([
                                        'crm_request_id' => $crm_request->id,
                                        'crm_request_tagging_type_id' => 5,
                                        'tagged_id' => $crm_auto_tag_user->admin_id,
                                        'hub_id' => NULL
                                    ]);
    
                                    CrmRequestTaggingHistory::create([
                                        'crm_request_id' => $crm_request->id,
                                        'crm_request_tagging_type_id' => 5,
                                        'tagged_id' => $crm_auto_tag_user->admin_id,
                                        'agent_id' => 306,
                                        'hub_id' => NULL
                                    ]);
                                    NotificationsController::send(31,$crm_request->id);
                                }
                            }
                        }
                        elseif ($request->valid == 0){
                            CrmRequest::where('id', $crm_request->id)->update([
                                'status_id' => 4,
                            ]);
                            CrmRequestStatusHistory::create([
                                'crm_request_id' => $crm_request->id,
                                'status_id' => 7,
                                'agent_id' => Auth::id()
                            ]);
                            CrmRequestStatusHistory::create([
                                'crm_request_id' => $crm_request->id,
                                'status_id' => 4,
                                'agent_id' => Auth::id()
                            ]);

                            if($crm_request->case_nature_id == 4){
                                NotificationsController::send(117, $crm_request->id, 7);
                            }

                            CrmRequestTagging::where('crm_request_id', $crm_request->id)->delete();
                        }
                    }
                }
            }
            else{
                return ['status' => 0, 'error' => 'Agent is not Assigned yet'];
            }
        }
        if($request->valid == 1){

            return ['status' => 1, 'success' => 'Request(s) has been marked as Valid'];
        }
        elseif ($request->valid == 0){
            return ['status' => 1, 'success' => 'Request(s) has been marked as In-Valid'];
        }
        else{
            return ['status' => 0, 'error' => 'Something went wrong'];
        }
    }

    public function product_image($id){
        $url = Storage::url('crm_claims/claim_product_' . $id . '.png');

        return view('admin.crm.picture')->with(['url' => $url]);
    }

    public function invoice_image($id){
        $url = Storage::url('crm_claims/claim_invoice_' . $id . '.png');

        return view('admin.crm.picture')->with(['url' => $url]);
    }

    public function damage_product_image($id){
        $url = Storage::url('crm_claims/claim_damage_product_' . $id . '.png');

        return view('admin.crm.picture')->with(['url' => $url]);
    }
    public function product_packaging_image($id){
        $url = Storage::url('crm_claims/claim_product_packaging_' . $id . '.png');

        return view('admin.crm.picture')->with(['url' => $url]);
    }
    public function actual_product_image($id){
        $url = Storage::url('crm_claims/claim_actual_product_' . $id . '.png');

        return view('admin.crm.picture')->with(['url' => $url]);
    }
    public function missing_product_image($id){
        $url = Storage::url('crm_claims/claim_missing_product_' . $id . '.png');

        return view('admin.crm.picture')->with(['url' => $url]);
    }
    public function product_packaging_image_for_content_short($id){
        $url = Storage::url('crm_claims/claim_product_content_short_' . $id . '.png');

        return view('admin.crm.picture')->with(['url' => $url]);
    }
    public function actual_product_image_for_content_short($id){
        $url = Storage::url('crm_claims/claim_actual_content_short_' . $id . '.png');

        return view('admin.crm.picture')->with(['url' => $url]);
    }

    public function edit_comment(Request $request){
        $comment_id = $request->comment_id;
        $comment = CrmComments::find($comment_id);
        $comment->comment_type = 1;
        $comment->comment_updated_by = Auth::id();
        $comment->comment_updated_at = Carbon::now();
        $comment->save();

        $edited_date = Carbon::parse($comment->comment_updated_at)->format('Y-m-d H:i:s');
        return ['status' => 0, 'success' => 'Comment has been marked as Internal', 'updated_at' => $edited_date, 'updated_by' => $comment->updated_by_admin->name];
    }

    public function escalation_status(Request $request){
        $existing_escalation_status = CrmRequestEscalationStatus::where('crm_request_id', $request->crm_request_id);
        if($existing_escalation_status->exists()){
            $existing_escalation_status = $existing_escalation_status->first();
            $existing_escalation_status->status = $request->status;
            $existing_escalation_status->save();
        }
        else{
            $new_escalation_status = new CrmRequestEscalationStatus();
            $new_escalation_status->crm_request_id = $request->crm_request_id;
            $new_escalation_status->status = $request->status;
            $new_escalation_status->save();
        }

        return ['status' => 0, 'success' => 'Request(s) Escalation updated successfully!'];
    }
    public function escalate(Request $request){
        $crm_request_id = $request->crm_request_id;
        $crm_request = CrmRequest::find($crm_request_id);
        $escalation_tagging_id = $request->escalation_tagging_id;
        $escalation_level_id = $request->selected_escalation;
        $crm_escalation_level = CrmEscalationTaggingLevel::where('escalation_tagging_id', $escalation_tagging_id)->where('level_id', $escalation_level_id)->first();
        if($crm_escalation_level){
            $crm_escalation_tag = CrmEscalationTagging::find($escalation_tagging_id);
            if($crm_escalation_tag->hub_status == 1){
                $origin_hub_id = $crm_request->shipment->pickup_address->city->hub_id;
                $matching_hubs[] = $origin_hub_id;
            }
            elseif($crm_escalation_tag->hub_status == 2){
                $destination_hub_id = $crm_request->shipment->consignee_city->hub_id;
                $matching_hubs[] = $destination_hub_id;

            }
            elseif($crm_escalation_tag->hub_status == 3){
                $origin_hub_id = $crm_request->shipment->pickup_address->city->hub_id;
                $matching_hubs[] = $origin_hub_id;
                $destination_hub_id = $crm_request->shipment->consignee_city->hub_id;
                if($origin_hub_id != $destination_hub_id){
                    $matching_hubs[] = $destination_hub_id;
                }
            }
            $hubs = $crm_escalation_tag->hubs;
            $tagging_to = array();
            $to = array();
            $cc = array();
            $bcc = array();
            $escalation_emails = array();
            $escalation_emails['level'] = $crm_escalation_level->level->name .'(' . $crm_escalation_level->level->id . ')';
            $roles = $crm_escalation_level->roles;
            foreach ($roles as $role)
            {
                $admins = AdminRole::leftjoin('admins as a', 'a.role_id', '=', 'admin_roles.id')
                    ->where('admin_roles.id', $role->role_id)
                    ->where('a.status', 1)
                    ->select('a.id as id', 'a.email as email')
                    ->get();
                if(count($hubs) > 0){
                    $total_hubs = array();
                    foreach ($hubs as $hub){
                        if(in_array($hub->hub_id, $matching_hubs)) {
                            $crm_request_multiple_tagging = new CrmRequestEscalationTagging();
                            $crm_request_multiple_tagging->crm_request_id = $crm_request_id;
                            $crm_request_multiple_tagging->role_id = $role->role_id;
                            $crm_request_multiple_tagging->hub_id = $hub->hub_id;
                            $crm_request_multiple_tagging->save();

                            $total_hubs[] = $hub->hub_id;
                        }
                    }

                    foreach ($admins as $admin){
                        $admin_hubs = AdminHub::where('admin_id', $admin->id)->whereIn('hub_id', $total_hubs);
                        if($admin_hubs->exists()){
                            $tagging_to[] = $admin->email;
                        }
                    }
                }
                else{
                    $crm_request_multiple_tagging = new CrmRequestEscalationTagging();
                    $crm_request_multiple_tagging->crm_request_id = $crm_request_id;
                    $crm_request_multiple_tagging->role_id = $role->role_id;
                    $crm_request_multiple_tagging->hub_id = NULL;
                    $crm_request_multiple_tagging->save();


                    foreach ($admins as $admin){
                        $tagging_to[] = $admin->email;
                    }
                }
            }
            $emails = $crm_escalation_level->emails;
            foreach ($emails as $email){
                if($email->status == 1){
                    $to[] = $email->email;
                }
                else if($email->status == 2){
                    $cc[] = $email->email;
                }
                else if($email->status == 3){
                    $bcc[] = $email->email;
                }
            }
            $escalation_emails['to'] = $to;
            $escalation_emails['cc'] = $cc;
            $escalation_emails['bcc'] = $bcc;

            $new_log = new CrmRequestEscalationLog();
            $new_log->crm_request_id = $crm_request_id;
            $new_log->escalation_tagging_id = $escalation_tagging_id;
            $new_log->tagging_level_id = $crm_escalation_level->id;
            $new_log->level_id = $crm_escalation_level->level_id;
            $new_log->save();
            NotificationsController::send(65, $crm_request_id, $tagging_to);
            NotificationsController::send(66, $crm_request_id, $escalation_emails);
        }
        return ['status' => 0, 'success' => 'Request(s) Escalated successfully!'];
    }
    public function consignee_info_index(){
        ActivityTrailController::createActivityTrailLog(Auth::id(),43);
        $case_nature = CrmRequestCaseNature::select('id', 'name')->get();
        $case_nature_type = CrmRequestCaseNatureType::select('id', 'type')->get();
        $shipment_status = ShipmentStatus::select('id', 'name')->get();
        $channels = CrmRequestChannel::select('id', 'channel')->get();
        $agents = AdminRole::leftjoin('admins as a', 'a.role_id', '=', 'admin_roles.id')
            ->where('admin_roles.department_id',3)->get();
        $admins = AdminRole::leftjoin('admins as a', 'a.role_id', '=', 'admin_roles.id' )
            ->select('a.id as id', 'a.name as name')
            ->whereNotIn('admin_roles.department_id', [1,3])->get();
        $types = CrmRequestTaggingTypes::get();
        $departments = AdminDepartment::whereNotIn('id', [1,3])->get();
        $hubs = City::where('hub', 1)->get();
        $zones = Zone::where('status', 1)->get();
        return view('admin.crm.consignee_info')->with(['case_nature' => $case_nature, 'case_nature_type' => $case_nature_type, 'channels' => $channels, 'agents' => $agents, 'shipment_status' => $shipment_status, 'types' => $types, 'admins' => $admins, 'departments' => $departments, 'hubs' => $hubs, 'zones' => $zones]);
    }

    public function consignee_info_list(Request $request){
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),103);
        }
        $consignee_info_request = CrmRequest::leftjoin('crm_request_case_nature as crcn', 'crcn.id', '=', 'crm_requests.case_nature_id')
            ->leftjoin('crm_request_case_nature_types as crcnt', 'crcnt.id', '=', 'crm_requests.case_nature_type_id')
            ->leftjoin('crm_request_channels as crc', 'crc.id', '=', 'crm_requests.channel_id')
            ->leftjoin('admins as ad', 'ad.id', '=', 'crm_requests.agent_id')
            ->leftjoin('admins as a', function ($join) {
                $join->on('a.id', '=', 'crm_requests.launched_by_id')
                    ->where('crm_requests.launched_by', '=', DB::raw(0));
            })
            ->leftjoin('users as u', function ($join) {
                $join->on('u.id', '=', 'crm_requests.launched_by_id')
                    ->where('crm_requests.launched_by', '=', DB::raw(1));
            })
            ->leftjoin('substitute_users as su', function ($join) {
                $join->on('su.id', '=', 'crm_requests.launched_by_id')
                    ->where('crm_requests.launched_by', '=', DB::raw(2));
            })
            ->leftjoin('retail_users as ru', function ($join) {
                $join->on('ru.id', '=', 'crm_requests.launched_by_id')
                    ->where('crm_requests.launched_by', '=', DB::raw(3));
            })
            ->leftjoin('consignee_users as cu', function ($join) {
                $join->on('cu.id', '=', 'crm_requests.launched_by_id')
                    ->where('crm_requests.launched_by', '=', DB::raw(3));
            })
            ->leftjoin('shipments as s', 's.id', '=', 'crm_requests.shipment_id')
            ->leftjoin('shipment_status as ss', 'ss.id', '=', 's.shipper_status_id')
            ->leftjoin('users as user', 'user.id', '=', 'crm_requests.shipper_id')
            ->leftjoin('user_shipping_infos AS usi', 's.pickup_address_id', '=', 'usi.id')
            ->leftjoin('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->leftjoin('cities AS dc', 's.consignee_city_id', '=', 'dc.id')
            ->leftjoin('cities AS dh', 'dc.hub_id', '=', 'dh.id')
            ->leftjoin('zones as z', 'z.id', '=', 'dc.zone_id')
            ->leftjoin('crm_request_taggings as crt', 'crt.crm_request_id', '=', 'crm_requests.id')
            ->leftjoin('crm_request_tagging_histories as crth', function ($join) {
                $join->on('crth.crm_request_id', '=', 'crm_requests.id')
                    ->where('crth.id','=',
                        DB::raw('(select max(id) from crm_request_tagging_histories where crm_request_tagging_histories.crm_request_id = crm_requests.id)'));
            })
            ->leftjoin('admin_departments as adp', 'adp.id', '=', 'crt.tagged_id')
            ->leftjoin('admins as at', 'at.id', '=', 'crt.tagged_id')
            ->leftjoin('sale_person_tags as spt', function($join) {
                $join->on('spt.user_id', '=', 's.user_id')
                    ->where('spt.status', '=', 0);
            })
            ->leftjoin('crm_request_status_histories as res', function ($join) {
                $join->on('res.crm_request_id', '=', 'crm_requests.id')
                    ->where('res.id','=',
                        DB::raw('(select max(id) from crm_request_status_histories where crm_request_status_histories.crm_request_id = crm_requests.id and crm_request_status_histories.status_id = 2)'));
            })
            ->leftjoin('crm_request_agent_histories as resa', function ($join) {
                $join->on('resa.crm_request_id', '=', 'crm_requests.id')
                    ->where('resa.id','=',
                        DB::raw('(select max(id) from crm_request_agent_histories where crm_request_agent_histories.crm_request_id = crm_requests.id and crm_request_agent_histories.agent_id = crm_requests.agent_id)'));
            })
            ->leftjoin('admins as resby', 'resby.id', '=', 'resa.assigned_by')
            ->leftjoin('crm_comments as ccs', function($join){
                $join->on('ccs.crm_request_id', '=', 'crm_requests.id')
                    ->where('ccs.id', '=', DB::raw('(select max(id) from crm_comments where crm_comments.crm_request_id = crm_requests.id)'));
            })
            ->leftjoin('admins as accs', 'accs.id', '=', 'ccs.comment_by_id')
            ->leftjoin('users as uccs', 'uccs.id', '=', 'ccs.comment_by_id')
            ->leftjoin('crm_request_escalation_taggings as cret', 'cret.crm_request_id', '=', 'crm_requests.id')
            ->leftjoin('consolidation_shipments as consolidations', function ($join){
                $join->on('consolidations.shipment_id', '=', 's.id')
                    ->where('consolidations.consolidation_id','=',
                        DB::raw('(select consolidation_id from consolidation_shipments where consolidation_shipments.shipment_id = s.id)'));
            })
            ->leftjoin('crm_consignee_info_prints as ccip', 'ccip.crm_request_id', '=', 'crm_requests.id')
            ->select('crm_requests.id as id', 's.tracking_number as tracking_number', 'crcn.name as case_nature', 'crcnt.type as case_nature_type', 'crc.channel as channel', 'ad.name as agent', 'a.name as name', 'u.name as shipper', 'su.name as sub_shipper', 'ru.name as retail_user', 'cu.name as consignee_user', 'crm_requests.launched_by as launched_added_by', 'crm_requests.created_at as created_at', 'crm_requests.description as description', 'at.name as tagged_admin', 'adp.name as tagged_department', 'crt.crm_request_tagging_type_id as crm_request_tagging_type_id', 'ss.name as status', 'user.name as shipper_name', 'oc.name as origin', 'dc.name as destination', 'dh.name as hub', 'crt.crm_request_tagging_type_id as tagged_type', 'res.created_at as valid_date', 'ccs.comment as last_comment', 'ccs.created_at as last_comment_date', 'ccs.comment_by as last_comment_by', 'accs.name as last_comment_admin', 'uccs.name as last_comment_shipper', 'crm_requests.launched_by_id', 'res.created_at as agent_assigned_date', 'resby.name as agent_assigned_by', 'crth.created_at as tagged_date', 'z.name as zone', 'dc.id as consignee_city_id', 's.shipper_Status_id as current_status_id','consolidations.consolidation_id', 's.intercepted as intercepted','s.shipping_mode_id as shipping_mode_id', 'crcnt.id as case_nature_type_id', 's.id as shipment_id', 'ccip.id as print_id', 's.booking_type_id as booking_type_id','crm_requests.address as address', 'crm_requests.address_latitude as address_latitude','crm_requests.address_longitude as address_longitude')
            ->where('crm_requests.status_id', 2)
            ->whereIn('crcnt.id', [11, 12, 13])
            ->groupBy('crm_requests.id');
        if ((!in_array(session('role_id'), [1, 4, 6])) && (!in_array(179, session('permissions')) && !in_array(201, session('permissions')))) {
            $consignee_info_request = $consignee_info_request->where(function ($query) {
                $query->where(function ($sub_query) {
                    $sub_query->where('crm_requests.agent_id', Auth::id())
                        ->orWhere(function ($sub_query) {
                            $sub_query->where('crm_requests.launched_by', 0)
                                ->where('crm_requests.launched_by_id', Auth::id());
                        });
                })
                    ->orWhere(function ($sub_query) {
                        $sub_query->where('spt.admin_id', '=', Auth::id());
                    })
                    ->orWhere(function ($sub_query) {
                        $sub_query->where('crt.crm_request_tagging_type_id', 2)
                            ->where('crt.tagged_id', '=', Auth::id());
                    })
                    ->orWhere(function ($sub_query) {
                        $sub_query->where('crt.crm_request_tagging_type_id', 1)
                            ->where('adp.id', '=', session('department_id'))
                            ->where(function ($sub_sub_query) {
                                $sub_sub_query->whereIn('oc.hub_id', session('hubs'))
                                    ->orWhereIn('dc.hub_id', session('hubs'))
                                    ->orWhereIn('crt.hub_id', session('hubs'));
                            });
                    })
                    ->orWhere(function ($sub_query) {
                        $sub_query->where('cret.role_id', '=', session('role_id'))
                            ->where(function ($sub_sub_query) {
                                $sub_sub_query->whereNull('cret.hub_id')
                                    ->orWhereNotNull('cret.hub_id')
                                    ->whereIn('cret.hub_id', session('hubs'));
                            });
                    })
                    ->orWhere(function ($sub_query) {
                        if(in_array(session('role_id'), [8, 9 ,10])){
                            $sub_query->whereIn('oc.hub_id', session('hubs'))
                                ->orWhereIn('dc.hub_id', session('hubs'));
                        }
                    });
            });
        }
        else if (session('department_id') == 7){
            if(!in_array(session('id'), session('sale_users_bypass'))){
                $consignee_info_request = $consignee_info_request->where('spt.admin_id', Auth::id());
            }
        }

        $datatables = Datatables::of($consignee_info_request)
            ->addColumn('id_padded', function ($requests) {
                return str_pad($requests->id, 6, '0', STR_PAD_LEFT);
            })
            ->addColumn('id_padded_link', function ($requests) {
                return '<u><a href=' . route('admin.crm.request.details', ['id' => $requests->id]) . ' target="_blank">' . str_pad($requests->id, 6, '0', STR_PAD_LEFT). '</a></u>';
            })
            ->addColumn('tagged', function ($requests) {
                if($requests->tagged_type == 1){
                    return 'Department';
                }
                else if($requests->tagged_type == 2){
                    return 'Admin';
                }
                else{
                    return '-';
                }
            })
            ->addColumn('tracking_number_hyperlink', function ($requests) {
                return '<u><a href=' . route('admin.tracking.index') . '?tracking_number=' . $requests->tracking_number . ' class="tracking" target="_blank">' . $requests->tracking_number . '</a></u>';
            })
            ->editColumn('added_by', function($requests){
                if($requests->launched_added_by == 0) {
                    return 'Admin';
                }
                else if($requests->launched_added_by == 1) {
                    return 'Shipper';
                }
                else if($requests->launched_added_by == 2){
                    return 'Shipper Substitute User';
                }
                else if($requests->launched_added_by == 3){
                    return 'Retail';
                }
                else if($requests->launched_added_by == 4){
                    return 'Consignee';
                }
            })
            ->addColumn('current_tat', function ($requests){
                if($requests->created_at){
                    Carbon::setWeekendDays([
                        Carbon::SUNDAY,
                    ]);
                    $launched = Carbon::parse($requests->created_at)->startOfDay();
                    $first_closed = CrmRequestStatusHistory::where('crm_request_id', $requests->id)->where('status_id' ,4)->first();
                    if($first_closed){
                        $current = $first_closed->created_at;
                    }
                    else{
                        $current = Carbon::now();
                    }
                    $time_format = 'H:i';
                    $time_from = CrmSettings::where('name','TAT Cut-Off Time From')->first();
                    $time_to = CrmSettings::where('name','TAT Cut-Off Time To')->first();
                    $from_formatted = date($time_format, strtotime($time_from->setting_value));
                    $to_formatted = date($time_format, strtotime($time_to->setting_value));
                    $cut_off_check = $requests->created_at->format($time_format);
                    $current_tat = $current->diffInWeekdays($launched);

                    $launched_check = $launched->toDateString();
                    $current_check = $current->toDateString();
                    if($launched_check <= $current_check){
                        if($to_formatted < $cut_off_check){
                            $after_cut_off = $current_tat - 1;
                            $current_tat = $after_cut_off;
                        }
                    }
                    $holidays = CrmTatHolidays::whereBetween('holiday', [$launched, $current])->get();
                    foreach($holidays as $holiday){
                        $holiday_formatted = date('Y-m-d H:i:s', strtotime($holiday->holiday));
                        $holiday_formatted_check = date('Y-m-d', strtotime($holiday->holiday));
                        $launched_formatted_check = date('Y-m-d', strtotime($launched));
                        if($launched < $holiday_formatted || $current > $holiday_formatted){
                            if($holiday_formatted_check == $launched_formatted_check){
                                if($to_formatted < $cut_off_check){
                                    $after_cut_off = $current_tat + 1;
                                    $current_tat = $after_cut_off;
                                }
                            }
                            $after_holidays = $current_tat - 1;
                            $current_tat = $after_holidays;
                        }
                    }


                    $re_open_counts = CrmRequestStatusHistory::where('crm_request_id', $requests->id)->where('status_id' ,5)->get();
                    if($re_open_counts){
                        foreach ($re_open_counts as $re_open_count){
                            $launched = Carbon::parse($re_open_count->created_at);
                            $last_closed = CrmRequestStatusHistory::where('crm_request_id', $requests->id)->where('status_id' ,4)->where('created_at', '>=', $re_open_count->created_at)->first();
                            if($last_closed){
                                $current = $last_closed->created_at;
                            }
                            else{
                                $current = Carbon::now();
                            }
                            $time_format = 'H:i';
                            $time_from = CrmSettings::where('name','TAT Cut-Off Time From')->first();
                            $time_to = CrmSettings::where('name','TAT Cut-Off Time To')->first();
                            $from_formatted = date($time_format, strtotime($time_from->setting_value));
                            $to_formatted = date($time_format, strtotime($time_to->setting_value));
                            $cut_off_check = $requests->created_at->format($time_format);
                            $additional_tat = $current->diffInWeekdays($launched);
                            $current_tat = $current_tat + $additional_tat;
                            $launched_check = $launched->toDateString();
                            $current_check = $current->toDateString();
                            if($launched_check <= $current_check){
                                if($to_formatted < $cut_off_check){
                                    $after_cut_off = $current_tat - 1;
                                    $current_tat = $after_cut_off;
                                }
                            }
                            $holidays = CrmTatHolidays::whereBetween('holiday', [$launched, $current])->get();
                            foreach($holidays as $holiday){
                                $holiday_formatted = date('Y-m-d H:i:s', strtotime($holiday->holiday));
                                $holiday_formatted_check = date('Y-m-d', strtotime($holiday->holiday));
                                $launched_formatted_check = date('Y-m-d', strtotime($launched));
                                if($launched < $holiday_formatted || $current > $holiday_formatted){
                                    if($holiday_formatted_check == $launched_formatted_check){
                                        if($to_formatted < $cut_off_check){
                                            $after_cut_off = $current_tat + 1;
                                            $current_tat = $after_cut_off;
                                        }
                                    }
                                    $after_holidays = $current_tat - 1;
                                    $current_tat = $after_holidays;
                                }
                            }
                        }
                    }

                    return $current_tat;
                }
                return "-";
            })
            ->editColumn('tagged_to', function($requests){
                if($requests->crm_request_tagging_type_id == 1) {
                    return $requests->tagged_department;
                }
                else if($requests->crm_request_tagging_type_id == 2) {
                    return $requests->tagged_admin;
                }
                else{
                    return '-';
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
            ->filterColumn('tagged_to',function ($query,$keyword){
                if ($keyword != '') {
                    $query->where(function($sub_query) use ($keyword) {
                        $sub_query->where('crt.crm_request_tagging_type_id', '=', 1)
                            ->where('adp.name', 'like', '%' . $keyword . '%');
                    })
                        ->orWhere(function($sub_query) use ($keyword) {
                            $sub_query->where('crt.crm_request_tagging_type_id', '=', 2)
                                ->where('at.name', 'like', '%' . $keyword . '%');
                        });
                }
            })
            ->orderColumn('tagged_to', DB::raw('IF (crt.crm_request_tagging_type_id = 1, adp.name, IF (crt.crm_request_tagging_type_id = 2, at.name, ""))') . ' $1')
            ->editColumn('agent', function ($requests){
                if($requests->agent == null){
                    return '-';
                }
                else{
                    return $requests->agent;
                }
            })
            ->addColumn('launched_by_name', function ($requests){
                $name = '';
                if($requests->launched_added_by == 0){
                    $name = $requests->name;
                }
                else if($requests->launched_added_by == 1){
                    $name = $requests->shipper;
                }else if($requests->launched_added_by == 2){
                    $name = $requests->sub_shipper;
                }else if($requests->launched_added_by == 3){
                    $name = $requests->retail_user;
                }else if($requests->launched_added_by == 4){
                    $name = $requests->consignee_user;
                }
                return $name;
            })
            ->editColumn('last_comment_date', function($requests){
                if($requests->last_comment_date != null){
                    return $requests->last_comment_date;
                }
                else{
                    return '-';
                }
            })
            ->filterColumn('launched_by_name', function($query, $keyword) {
                $keyword = strtolower($keyword);

                if ($keyword != '') {
                    $query->where(function ($sub_query) use ($keyword) {
                        $sub_query->where('crm_requests.launched_by', '=', 0)
                            ->where('a.name', 'like', '%' . $keyword . '%');
                    })
                        ->orWhere(function ($sub_query) use ($keyword) {
                            $sub_query->where('crm_requests.launched_by', '=', 1)
                                ->where('u.name', 'like', '%' . $keyword . '%');
                        })
                        ->orWhere(function ($sub_query) use ($keyword) {
                            $sub_query->where('crm_requests.launched_by', '=', 2)
                                ->where('su.name', 'like', '%' . $keyword . '%');
                        });
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->orderColumn('launched_by_name', DB::raw('IF (crm_requests.launched_by = 0, a.name, IF (crm_requests.launched_by = 1, u.name, IF (crm_requests.launched_by = 2, su.name, "")))') . ' $1')
            ->filterColumn('case_nature_type',function ($query,$keyword){

                if ($keyword != '') {
                    $query->where('crcnt.id','=',$keyword);
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->editColumn('last_comment_name', function($requests){
                if($requests->last_comment_by == 0){
                    return $requests->last_comment_admin;
                }
                else if($requests->last_comment_by == 1){
                    return $requests->last_comment_shipper;
                }
                else{
                    return '-';
                }
            })
            ->filterColumn('last_comment_name', function($query, $keyword) {
                $keyword = strtolower($keyword);

                if ($keyword != '') {
                    $query->where(function ($sub_query) use ($keyword) {
                        $sub_query->where('ccs.comment_by', '=', 0)
                            ->where('accs.name', 'like', '%' . $keyword . '%');
                    })
                        ->orWhere(function ($sub_query) use ($keyword) {
                            $sub_query->where('ccs.comment_by', '=', 1)
                                ->where('uccs.name', 'like', '%' . $keyword . '%');
                        });
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->orderColumn('last_comment_name', DB::raw('IF (ccs.comment_by = 0, accs.name, IF (ccs.comment_by = 1, uccs.name, ""))') . ' $1')

            ->editColumn('last_comment', function($requests){
                if($requests->last_comment != null){
                    return $requests->last_comment;
                }
                else{
                    return '-';
                }
            })
            ->addColumn('action', function($requests) {
                $route = route('admin.crm.request.details', ['id' => $requests->id]);
                $open_intercept = CityDelivery::where('city_id', $requests->consignee_city_id)->where('shipping_mode_id',$requests->shipping_mode_id)->exists();
                $dropdown = '
                  <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">';
                $dropdown .= '<button onclick="window.open(\'' . $route . '\', \'_tab\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div> View Details</button>';
                if ((session('role_id') == 1 || in_array(245, session('permissions'))) && !$requests->consolidation_id && $requests->booking_type_id != 4) {
                    if($requests->intercepted == 0 && $requests->case_nature_type_id == 11){
                        if($open_intercept){
                            $dropdown .= '<a href="javascript:void(0);" class="dropdown-item intercept"><i class="ft-plus-circle"></i>  Intercept/Re-Book</a>';
                        }
                    }
                }
                $dropdown .= '<a href="javascript:void(0);" class="dropdown-item print"><i class="ft-plus-circle"></i>  Print</a>';
                if($requests->print_id != null && (session('role_id') == 1 || session('role_id') == 6 || in_array(184, session('permissions')))){
                    $dropdown .= '<a href="javascript:void(0);" class="dropdown-item resolve"><i class="ft-plus-circle"></i>  Resolve</a>';
                }

                $dropdown .= '</div>
                  </div>
                ';

                return $dropdown;
            });

        if ($tracking_numbers = $request->get('tracking_numbers')) {
            $datatables->whereIn('s.tracking_number', explode(',', $tracking_numbers));
        }

        return $datatables->make(true);
    }

    public function print_air_waybill(Request $request){
        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
        $user_id = Auth::id();
        $user_type = 3;
        $body_only = FALSE;
        $type = NULL;
        $user_name = Admin::find($user_id)->name . ' (Admin)';
        $print_details = '
            <div class="small mt-1">Printed By: ' . $user_name . '</div>
        ';

        $html = '';

        if (!$body_only) {
            $html .= '
                <!doctype html>
                <html lang="en">
                  <head>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
            ';

            if ($user_type != 4 && $type != 'pdf') {
                $html .= '
                    <link rel="stylesheet" type="text/css" href="' . asset('app-assets/css/bootstrap.min.css') . '">
                ';
            }
            else {
                $html .= '
                    <style>' . file_get_contents(public_path('app-assets/css/bootstrap.min.css')) . '</style>
                ';
            }

            $html .= '
                    <title>Air Waybill</title>

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
                        width: 12.5% !important;
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

                      .border.twice {
                        border-width: 2px !important;
                      }

                      .border.twice-top {
                        border-top-width: 2px !important;
                      }

                      .border.twice-bottom {
                        border-bottom-width: 2px !important;
                      }

                      .border.twice-left {
                        border-left-width: 2px !important;
                      }

                      .border.twice-right {
                        border-right-width: 2px !important;
                      }

                      td.replacement span {
                        width: 22px;
                      }

                      td.replacement span img {
                        display: block;
                        width: 100%;
                        margin: auto;
                        background: #c8c8c8;
                        border-radius: 25px;
                      }

                      .void {
                        top: 0;
                        bottom: 0;
                        right: 0;
                        left: 0;
                        height: 80px;
                        font-size: 5rem;
                        line-height: 3.5rem;
                        opacity: 0.25;
                      }
                       div.page
                        {
                            page-break-after: always;
                            page-break-inside: avoid;
                        }
                        .piece_number{
                            font-size: 2.5rem;
                        }
                    </style>
                  </head>
                  <body>
                    <div>
            ';

            if ($user_type != 4 && $type != 'pdf') {
                $html .= '
                    <style>
                      @font-face {
                        font-family: "Fajer Noori Nastalique";
                        src: url("' . asset('fonts/urdu/Fajer-Noori-Nastalique.eot') . '");
                        src: url("' . asset('fonts/urdu/Fajer-Noori-Nastalique.eot?#iefix') . '") format("embedded-opentype"),
                        url("' . asset('fonts/urdu/Fajer-Noori-Nastalique.woff') . '") format("woff"),
                        url("' . asset('fonts/urdu/Fajer-Noori-Nastalique.otf') . '") format("opentype"),
                        url("' . asset('fonts/urdu/Fajer-Noori-Nastalique.ttf') . '") format("truetype"),
                        url("' . asset('fonts/urdu/Fajer-Noori-Nastalique.svg#FajerNooriNastalique') . '") format("svg");
                        font-weight: normal;
                        font-style: normal;
                        unicode-range: U+0600-06FF, U+0750-077F, U+FB50-FDFF, U+FE70-FEFF;
                      }

                      .urdu {
                        font-family: "Fajer Noori Nastalique";
                      }
                    </style>
                ';
            }
            else {
                $html .= '
                    <style>
                      @font-face {
                        font-family: "Fajer Noori Nastalique";
                        src: url("data:font/truetype;charset=utf-8;base64,' . base64_encode(file_get_contents(public_path('fonts/urdu/Fajer-Noori-Nastalique.ttf'))) . '") format("truetype");
                        font-weight: normal;
                        font-style: normal;
                        unicode-range: U+0600-06FF, U+0750-077F, U+FB50-FDFF, U+FE70-FEFF;
                      }

                      .urdu {
                        font-family: "Fajer Noori Nastalique";
                        padding-bottom: .75rem !important;
                      }
                    </style>
                ';
            }

            if ($type == 'pdf') {
                $html .= '
                    <style>
                      body {
                        font-size: 0.75rem !important;
                        font-weight: bold !important;
                      }

                      td.replacement span {
                        width: auto !important;
                      }

                      .border.twice {
                        border-width: 1px !important;
                      }

                      .border.twice-top {
                        border-top-width: 1px !important;
                      }

                      .border.twice-bottom {
                        border-bottom-width: 1px !important;
                      }

                      .border.twice-left {
                        border-left-width: 1px !important;
                      }

                      .border.twice-right {
                        border-right-width: 1px !important;
                      }

                      .font-small {
                        font-size: 0.65rem !important;
                      }
                    </style>
                ';
            }
        }

        $shipment_details = '';
        $page_items = 1;

        $cod_change = false;
        $address_change = false;
        $phone_one_change = false;

        $shipment = Shipment::find($request->shipment_id);
        $intercept = false;
        if(InterceptReBookRequestHistory::where('shipment_id', $shipment->id)->exists()){
            $intercept = true;
        }
        if(($intercept == true && ($shipment->intercept_history->old_amount != $shipment->intercept_history->new_amount)) || (ChangeShipmentAmountLog::where('shipment_id', $shipment->id)->exists() && ($shipment->amount_change_log->old_amount != $shipment->amount_change_log->new_amount))){
            $cod_change = true;
        }
        if(($intercept == true && ($shipment->intercept_history->old_consignee_address != $shipment->intercept_history->new_consignee_address))){
            $address_change = true;
        }
        if(($intercept == true && ($shipment->intercept_history->old_consignee_phone_number_1 != $shipment->intercept_history->new_consignee_phone_number_1))){
            $phone_one_change = true;
        }
        ShipmentsAirWaybillJourneyController::add($shipment->id, $user_type, $user_id);

        if ($user_type == 3 || $user_id == $shipment->user_id) {

            if ($shipment->booking_type_id == 3 && $user_type != 3) {
                foreach ($shipment->items as $shipment_item){
                    if($page_items == 0){
                        $table_start = '
                <div class="page position-relative"><table class="table table-sm table-bordered border twice">
                    <tbody>
        ';
                    }
                    else{
                        $table_start = '
                <div class="position-relative"><table class="table table-sm table-bordered border twice">
                    <tbody>
        ';
                    }

                    if ($user_type != 4 && $type != 'pdf') {
                        $table_start .= '
                            <td rowspan="4" class="text-center align-middle border twice-bottom twice-right"><img src="' . asset('img/trax_logo_new.png') . '" width="100" class="d-block mx-auto">' . $print_details . '</td>
                ';
                    } else {
                        if ($type != 'pdf') {
                            $table_start .= '
                            <td rowspan="4" class="text-center align-middle border twice-bottom twice-right"><img src="' . public_path('img/trax_logo_new.png') . '" width="100" class="d-block mx-auto">' . $print_details . '</td>
                    ';
                        } else {
                            $table_start .= '
                            <td rowspan="4" class="text-center align-middle border twice-bottom twice-right"><img src="' . public_path('img/trax_logo_new.png') . '" width="75" class="d-block mx-auto">' . $print_details . '</td>
                    ';
                        }
                    }
                    if ($type != 'pdf') {
                        $table_start .= '
                            <td rowspan="4" colspan="2" class="text-center align-middle pl-1 pr-1 border twice-bottom twice-left twice-right">
                              <img src="data:image/png;base64,' . base64_encode($generator->getBarcode($shipment_item->id, $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                              <span><strong>' . $shipment_item->id . '</strong></span>
                            </td>
                            <tr>
                                <td class="color secondary border twice-top twice-left"><strong>Type</strong></td>
                                <td colspan="2" class="border twice-top">' . $shipment_item->product->product_name . '</td>
                                <td class="color secondary border twice-top"><strong>Quantity</strong></td>
                                <td colspan="1" class="border twice-top">' . $shipment_item->quantity . '</td>
                                <td colspan="2" class="color secondary border twice-top"><b>Tracking Number</b></td>
                            </tr>
                ';
                    } else {
                        $table_start .= '
                            <td rowspan="4" colspan="2" class="text-center align-middle pl-1 pr-1 border twice-bottom twice-left twice-right">
                              <img src="data:image/png;base64,' . base64_encode($generator->getBarcode($shipment_item->id, $generator::TYPE_CODE_128, 1.5, 45)) . '" class="d-block mx-auto">
                              <span><strong>' . $shipment_item->id . '</strong></span>
                            </td>
                            <tr>
                                <td class="color primary border twice-left"><strong>Type</strong></td>
                                <td colspan="2" class="border twice-top">' . $shipment_item->product->product_name . '</td>
                                <td class="color secondary border twice-top"><strong>Quantity</strong></td>
                                <td colspan="1" class="border twice-top">' . $shipment_item->quantity . '</td>
                                <td colspan="2" class="border twice-top">Tracking Number</td>
                            </tr>
                ';
                    }

                    $table_start .= '
                          <tr>
                            <td class="color secondary border twice-bottom"><strong>Description</strong></td>
                            <td colspan="2" class="border twice-bottom">' . $shipment_item->description . '</td>
                            <td class="color secondary border twice-bottom"><strong>Price</strong></td>
                            <td class="border twice-bottom">Rs ' . number_format($shipment_item->price) . '</td>';
                    if ($type != 'pdf') {
                        $table_start .= '
                            <td rowspan="3" colspan="2" class="text-center align-middle pl-1 pr-1 border twice-bottom twice-right">
                              <img src="data:image/png;base64,' . base64_encode($generator->getBarcode($shipment->tracking_number, $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                              <span><strong>' . $shipment->tracking_number . '</strong></span>
                            </td>
                          </tr>
                        </tbody>
                    </table></div>
                ';
                    } else {
                        $table_start .= '
                            <td rowspan="3" colspan="2" class="text-center align-middle pl-1 pr-1 border twice-bottom twice-left twice-right">
                              <img src="data:image/png;base64,' . base64_encode($generator->getBarcode($shipment->tracking_number, $generator::TYPE_CODE_128, 1.5, 45)) . '" class="d-block mx-auto">
                              <span><strong>' . $shipment->tracking_number . '</strong></span>
                            </td>
                          </tr>
                        </tbody>
                    </table></div>
                ';
                    }
                    $shipment_details .= $table_start;
                    $page_items++;
                    if($page_items >= 5){
                        $page_items = 0;
                    }
                }
            } else {
                $page_items = $page_items + 3;
                if($page_items >= 5){
                    $page_items = 0;
                }
                $table_start = '
                  <div class="position-relative">
                    <table class="table table-sm table-bordered border twice">
                        <tbody>
            ';

                if ($user_type != 4 && $type != 'pdf') {
                    $table_start .= '
                            <td rowspan="3" class="text-center align-middle border twice-bottom twice-right"><img src="' . asset('img/trax_logo_new.png') . '" width="100" class="d-block mx-auto">' . $print_details . '</td>
                ';
                } else {
                    if ($type != 'pdf') {
                        $table_start .= '
                            <td rowspan="3" class="text-center align-middle border twice-bottom twice-right"><img src="' . public_path('img/trax_logo_new.png') . '" width="100" class="d-block mx-auto">' . $print_details . '</td>
                    ';
                    } else {
                        $table_start .= '
                            <td rowspan="3" class="text-center align-middle border twice-bottom twice-right"><img src="' . public_path('img/trax_logo_new.png') . '" width="75" class="d-block mx-auto">' . $print_details . '</td>
                    ';
                    }
                }
                if ($type != 'pdf') {
                    $table_start .= '
                            <td rowspan="3" colspan="3" class="text-center align-middle pl-1 pr-1 border twice-bottom twice-left twice-right">
                              <img src="data:image/png;base64,' . base64_encode($generator->getBarcode($shipment->tracking_number, $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                              <span><strong>' . $shipment->tracking_number . '</strong></span>
                            </td>

                            <td class="color primary border twice-left"><strong>Service</strong></td>
                ';
                } else {
                    $table_start .= '
                            <td rowspan="3" colspan="3" class="text-center align-middle pl-1 pr-1 border twice-bottom twice-left twice-right">
                              <img src="data:image/png;base64,' . base64_encode($generator->getBarcode($shipment->tracking_number, $generator::TYPE_CODE_128, 1.5, 45)) . '" class="d-block mx-auto">
                              <span><strong>' . $shipment->tracking_number . '</strong></span>
                            </td>

                            <td class="color primary border twice-left"><strong>Service</strong></td>
                ';
                }

                if ($shipment->booking_type_id == 1 || $shipment->booking_type_id == 4) {
                    $table_start .= '
                            <td><strong>' . $shipment->booking_type->booking_type . '</strong></td>
                ';
                } else if ($shipment->booking_type_id == 2) {
                    if ($type != 'pdf') {
                        $table_start .= '
                            <td class="replacement"><strong class="align-middle">' . $shipment->booking_type->booking_type . '</strong><span class="d-inline-block align-middle float-right"><img src="' . asset('img/replacement.png') . '"></span></td>
                    ';
                    } else {
                        $table_start .= '
                            <td class="replacement"><strong class="align-middle">' . $shipment->booking_type->booking_type . '</strong></td>
                    ';
                    }
                }
//                    else if ($shipment->booking_type_id == 3) {
//                        $table_start .= '
//                                <td><strong>' . $shipment->booking_type->booking_type . ' (' . (($shipment->package_type == 1) ? 'Complete' : 'Partial') . ')' . '</strong></td>
//                    ';
//                    }
                else {
                    $table_start .= '
                            <td><strong>' . $shipment->booking_type->booking_type . '</strong></td>
                ';
                }
                
                if ($type != 'pdf') {
                    $table_start .= '
                            <td class="color primary"><strong>Datetime</strong></td>
                            <td>' . $shipment->created_at->format('Y-m-d H:i:s') . '</td>
                          </tr>
                          <tr>
                            <td class="color primary border twice-left"><strong>Shipping Mode</strong></td>
                            <td><strong>' . $shipment->shipping_mode->mode . '</strong></td>
                ';
               
                    $table_start .= '
                            <td class="color primary"><strong>Order ID</strong></td>
                            <td>' . $shipment->order_id . '</td>
                          </tr>
                          <tr>
                            <td class="color primary border twice-bottom twice-left"><strong>Origin</strong></td>
                            <td class="border twice-bottom"><strong>' . $shipment->pickup_address->city->name . '</strong></td>
                            <td class="color primary border twice-bottom"><strong>Destination</strong></td>
                            <td class="border twice-bottom"><strong>' . $shipment->consignee_city->name .'</strong></td>
                          </tr>
                          <tr>
                            <td colspan="4" class="text-center color primary border twice-top twice-right"><strong>Shipper</strong></td>
                            <td colspan="4" class="text-center color primary border twice-top twice-left"><strong>Consignee</strong></td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Name</strong></td>
                ';
                } else {
                    $table_start .= '
                            <td class="color primary"><strong>Order ID</strong></td>
                            <td>' . $shipment->order_id . '</td>
                          </tr>
                          <tr>
                            <td class="color primary border twice-left"><strong>Shipping</strong></td>
                            <td><strong>' . $shipment->shipping_mode->mode . '</strong></td>
                ';

                    $table_start .= '
                            <td class="color primary"><strong>Date</strong></td>
                            <td>' . $shipment->created_at->format('Y-m-d') . '</td>
                          </tr>
                          <tr>
                            <td class="color primary border twice-bottom twice-left"><strong>Origin</strong></td>
                            <td class="border twice-bottom"><strong>' . $shipment->pickup_address->city->name . '</strong></td>
                            <td class="color primary border twice-bottom"><strong>Destination</strong></td>
                            <td class="border twice-bottom"><strong>' . $shipment->consignee_city->name .'</strong></td>
                          </tr>
                          <tr>
                            <td colspan="4" class="text-center color primary border twice-top twice-right"><strong>Shipper</strong></td>
                            <td colspan="4" class="text-center color primary border twice-top twice-left"><strong>Consignee</strong></td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Name</strong></td>
                ';
                }

                if($shipment->user->brand_name != NULL){
                    $company_name = $shipment->user->brand_name;
                }
                else{
                    $company_name = $shipment->user->name;
                }

                if ($shipment->booking_type_id != 4) {
                    $table_start .= '
                            <td colspan="3" class="border twice-right">' . $company_name . '</td>
                ';
                } else {
                    $table_start .= '
                            <td colspan="3" class="border twice-right">' . $company_name . ' (' . $shipment->pickup_address->poc . ')</td>
                ';
                }

                $table_start .= '
                            <td class="color secondary border twice-left"><strong>Name</strong></td>
                            <td colspan="3">' . $shipment->consignee_name . '</td>
                          </tr>

                          <tr>
            ';

                if ($shipment->information_display == 1) {
                    if ($shipment->booking_type_id != 4) {
                        $table_start .= '
                            <td class="color secondary"><strong>Address</strong></td>
                            <td colspan="3" class="border twice-right">' . $shipment->pickup_address->pickup_address . '</td>
                    ';
                    } else {
                        $table_start .= '
                            <td class="color secondary"><strong>Address</strong></td>
                            <td colspan="3" class="border twice-right">' . $shipment->pickup_address->pickup_address . '</td>
                    ';
                    }
                } else {
                    $table_start .= '
                            <td colspan="4" class="border twice-bottom twice-right"></td>
                ';
                }
                if($address_change == true){
                    $table_start .= '
                            <td class="color secondary border twice-left"><strong>Address</strong></td>
                            <td colspan="3" class=""><b>' . $shipment->consignee_address . ' *</b></td>
                          </tr>
                          <tr>
            ';
                }
                else{
                    $table_start .= '
                            <td class="color secondary border twice-left"><strong>Address</strong></td>
                            <td colspan="3" class="">' . $shipment->consignee_address . '</td>
                          </tr>
                          <tr>
            ';
                }

                if ($type != 'pdf') {
                    if ($shipment->booking_type_id != 4) {
                        $table_start .= '
                        <td class="color secondary border twice-bottom"><strong>Phone Number(s)</strong></td>
                        <td colspan="3" class="border twice-bottom twice-right">' . $shipment->pickup_address->phone . '</td>
                    ';
                    } else {
                        $table_start .= '
                        <td class="color secondary border twice-bottom"><strong>Phone Number(s)</strong></td>
                        <td colspan="3" class="border twice-bottom twice-right">' . $shipment->pickup_address->phone . '</td>
                    ';
                    }
                } else {
                    if ($shipment->booking_type_id != 4) {
                        $table_start .= '
                        <td class="color secondary border twice-bottom"><strong>Phone No(s).</strong></td>
                        <td colspan="3" class="border twice-bottom twice-right">' . $shipment->pickup_address->phone . '</td>
                    ';
                    } else {
                        $table_start .= '
                        <td class="color secondary border twice-bottom"><strong>Phone No(s).</strong></td>
                        <td colspan="3" class="border twice-bottom twice-right">' . $shipment->pickup_address->phone . '</td>
                    ';
                    }
                }
                if($phone_one_change == true){
                    if ($type != 'pdf') {
                        $table_start .= '
                            <td class="color secondary border twice-bottom twice-left"><strong>Phone Number(s)</strong></td>
                            <td colspan="3" class="border twice-bottom"><b>' . $shipment->consignee_phone_number_1 . (($shipment->consignee_phone_number_2) ? (' / ' . $shipment->consignee_phone_number_2) : '') . ' *</b></td>
                          </tr>
                ';
                    } else {
                        $table_start .= '
                            <td class="color secondary border twice-bottom twice-left"><strong>Phone No(s).</strong></td>
                            <td colspan="3" class="border twice-bottom"><b>' . $shipment->consignee_phone_number_1 . (($shipment->consignee_phone_number_2) ? (' / ' . $shipment->consignee_phone_number_2) : '') . ' *</b></td>
                          </tr>
                ';
                    }
                }
                else{
                    if ($type != 'pdf') {
                        $table_start .= '
                            <td class="color secondary border twice-bottom twice-left"><strong>Phone Number(s)</strong></td>
                            <td colspan="3" class="border twice-bottom">' . $shipment->consignee_phone_number_1 . (($shipment->consignee_phone_number_2) ? (' / ' . $shipment->consignee_phone_number_2) : '') . '</td>
                          </tr>
                ';
                    } else {
                        $table_start .= '
                            <td class="color secondary border twice-bottom twice-left"><strong>Phone No(s).</strong></td>
                            <td colspan="3" class="border twice-bottom">' . $shipment->consignee_phone_number_1 . (($shipment->consignee_phone_number_2) ? (' / ' . $shipment->consignee_phone_number_2) : '') . '</td>
                          </tr>
                ';
                    }
                }

                if ($type != 'pdf') {
                    $table_end = '
                          <tr>
                            <td rowspan="3" colspan="2" class="color primary border twice-top twice-bottom twice-right"><strong>Special Instruction(s)</strong></td>
                            <td rowspan="3" colspan="4" class="border twice-top twice-bottom twice-right">' . $shipment->special_instructions . '</td>
                            <td colspan="2" class="border twice-top twice-bottom twice-left" style="height: 20px;"></td>
                          </tr>
                          <tr>
                ';

                    if ($shipment->booking_type_id == 5) {
                        $table_end .= '
                            <td class="border twice-top twice-bottom twice-left" colspan="2" rowspan="2" style="height: 32px;"></td>
                          </tr>
                          <tr>
                    ';
                    } 
                    elseif ($shipment->booking_type_id == 3) {
                        $table_end .= '
                            <td class="border twice-top twice-bottom twice-left" colspan="2" rowspan="2" style="height: 32px;"></td>
                          </tr>
                          <tr>
                    ';
                    }  elseif ($shipment->booking_type_id != 4) {
                        $table_end .= '
                            <td class="color primary border twice-top twice-bottom twice-left"><strong>Payment Mode</strong></td>
                            <td class="border twice-top twice-bottom twice-left"><strong>' . $shipment->payment_mode->mode . '</strong></td>
                    ';
                    } else {
                        $table_end .= '
                            <td class="color primary border twice-top twice-bottom twice-left"><strong>Charges Mode</strong></td>
                            <td class="border twice-top twice-bottom twice-left"><strong>' . $shipment->charges_mode->charges_mode . '</strong></td>
                    ';
                    }
                        if($shipment->shipment_detail()->exists()){
                            if($shipment->shipment_detail->is_open==1){
                        $table_end .= '<tr>
                        <td colspan="2" class="color primary border twice-top twice-bottom twice-left"><strong>Open Box</strong></td>
                        <td colspan="4" class="border twice-top twice-bottom twice-left"><strong> Yes <span><img src="'.asset('img/open_box_icon.png').'" ></span></strong></td>
                        
                        </tr>';
                    }
                }
                } else {
                    $table_end = '
                          <tr>
                            <td rowspan="2" colspan="2" class="color primary border twice-top twice-bottom twice-right"><strong>Special Instruction(s)</strong></td>
                            <td rowspan="2" colspan="4" class="border twice-top twice-bottom twice-right">' . $shipment->special_instructions . '</td>
                            <td colspan="2" class="border twice-top twice-bottom twice-left" style="height: 32px;"></td>
                ';
                }

                if ($shipment->booking_type_id != 5 && $shipment->booking_type_id != 3) {
                    $table_end .= '
                          </tr>
                          <tr>
                            <td class="align-middle color primary border twice-top twice-bottom twice-left"><strong>Collection Amount</strong></td>
                ';

                    if ($shipment->booking_type_id == 4 && $shipment->charges_mode_id == 1) {
                        $table_end .= '
                            <td class="align-middle border twice-top twice-bottom twice-left"><strong>Rs 0</strong></td>
                    ';
                    } else {
                        if($cod_change == true){
                            $table_end .= '
                            <td class="align-middle border twice-top twice-bottom twice-left"><strong>Rs ' . number_format($shipment->amount) . ' *</strong></td>
                    ';
                        }
                        else{
                            $table_end .= '
                            <td class="align-middle border twice-top twice-bottom twice-left"><strong>Rs ' . number_format($shipment->amount) . '</strong></td>
                    ';
                        }
                    }
                }

                if ($user_type != 4 && $type != 'pdf') {
                    $table_end .= '
                          </tr>
                          <tr>
                            <td colspan="8" class="text-center border twice-top urdu h5" dir="rtl"><em>برائے مہربانی رائڈر / کورئیر کو کوئی اضافی پیسہ نہ دیں۔ اگر پارسل / پیکٹ خراب یا خراب حالت میں ہے تو ، براہ کرم اسے وصول نہ کریں۔</em></td>
                          </tr>
                          <tr>
                            <td colspan="8" class="text-center border twice-top urdu h5" dir="rtl"><em>ٹریکس لاجسٹک کا اس پارسل / پیکٹ میں موجود کسی آئٹم یا مواد سے کوئی تعلق نہیں ہے۔ ہم سامان ایک جگہ سے دوسری جگہ بھیجتے ہیں۔ اگر آپ کو اس بارے میں کوئی شکایت ہے تو ، براہ کرم متعلقہ آن لائن اسٹور سے رابطہ کریں۔</em></td>
                          </tr>
                        </tbody>
                    </table>
                ';
                } else {
                    $table_end .= '
                          </tr>
                          <tr>
                            <td colspan="8" class="text-center border twice-top font-small"><em>Kindly do not give any addtional charges to the rider/courier. If shipment is found in torn or damaged condition, please do not receive.</em></td>
                          </tr>
                          <tr>
                            <td colspan="8" class="text-center border twice-top font-small"><em>Trax Logistics has nothing to do with any item or content contained in this parcel/packet. We ship goods from one place to another. If you have a complaint about this, please contact the relevant online store.</em></td>
                          </tr>
                        </tbody>
                    </table>
                ';
                }

                if ($shipment->booking_type_id != 4 && $shipment->charges_mode_id == 2 && $shipment->shipper_status_id == 1) {
                    $table_end .= '
                    <div class="void position-absolute m-auto text-center font-weight-bold">Void Air Waybill after Arrival</div>
                ';
                }

                $table_end .= '
                  </div>
            ';

                if ($type != 'pdf') {
                    $table_end .= '
                  <hr>
                ';
                }

                if ($shipment->booking_type_id == 1 || $shipment->booking_type_id == 4 || $shipment->booking_type_id == 5) {
                    $shipment_details .= $table_start;

                    $item = $shipment->items->first();

                    $shipment_details .= '
                          <tr>
                            <td rowspan="2" class="align-middle color primary border twice-top twice-bottom"><strong>Item</strong></td>
                            <td class="color secondary border twice-top"><strong>Type</strong></td>
                            <td colspan="2" class="border twice-top">' . $item->product->product_name . '</td>
                            <td class="color secondary border twice-top"><strong>Quantity</strong></td>
                            <td>' . $item->quantity . '</td>
                            <td colspan="1" class="color secondary border twice-top"><strong>Piece(s)</strong></td>
                            <td>'. $shipment->pieces .'</td>
                          </tr>
                          <tr>
                            <td class="color secondary border twice-bottom"><strong>Description</strong></td>
                            <td colspan="6" class="border twice-bottom">' . $item->description . '</td>
                          </tr>
                ';

                    $shipment_details .= $table_end;
                } else if ($shipment->booking_type_id == 2) {
                    $shipment_details .= $table_start;

                    $items = $shipment->items;

                    $item = $items[0];

                    $shipment_details .= '
                          <tr>
                            <td rowspan="2" class="align-middle color primary border twice-top twice-bottom"><strong>Delivery Item</strong></td>
                            <td class="color secondary border twice-top"><strong>Type</strong></td>
                            <td colspan="2" class="border twice-top">' . $item->product->product_name . '</td>
                            <td class="color secondary border twice-top"><strong>Quantity</strong></td>
                            <td>' . $item->quantity . '</td>
                            <td colspan="2" class="border twice-top"></td>
                          </tr>
                          <tr>
                            <td class="color secondary border twice-bottom"><strong>Description</strong></td>
                            <td colspan="6" class="border twice-bottom">' . $item->description . '</td>
                          </tr>
                ';

                    $item = $items[1];

                    $shipment_details .= '
                    <tr>
                      <td rowspan="2"  style="color:#ffffff !important; background-color: #000000 !important;border-color:#ffffff !important" class="align-middle  border twice-top twice-bottom"><strong>Replacement Item</strong></td>
                      <td style="color:#ffffff !important; background-color: #000000 !important;border-color:#ffffff !important" class=" border twice-top"><strong>Type</strong></td>
                      <td colspan="2" style="color:#ffffff !important; background-color: #000000 !important;border-color:#ffffff !important" class="border twice-top">' . $item->product->product_name . '</td>
                      <td style="color:#ffffff !important; background-color: #000000 !important;border-color:#ffffff !important" class=" border twice-top"><strong>Quantity</strong></td>
                      <td style="color:#ffffff !important; background-color: #000000 !important;border-color:#ffffff !important">' . $item->quantity . '</td>
                      <td style="color:#ffffff !important; background-color: #000000 !important;border-color:#ffffff !important" colspan="2" class="border twice-top"></td>
                    </tr>
                    <tr>
                      <td style="color:#ffffff !important; background-color: #000000 !important;border-color:#ffffff !important" class=" border twice-bottom"><strong>Description</strong></td>
                      <td colspan="6" style="color:#ffffff !important; background-color: #000000 !important;border-color:#ffffff !important" class="border twice-bottom">' . $item->description . '</td>
                    </tr>
                ';

                    $shipment_details .= $table_end;
                } else if ($shipment->booking_type_id == 3) {
                    $shipment_details .= $table_start;

                    $item_quantity = 0;
                    foreach ($shipment->items as $item) {
                        $item_quantity += $item->quantity;
                    }
                    $shipment_details .= '
                          <tr>
                            <td rowspan="1" class="align-middle color primary border twice-top twice-bottom"><strong>Try & Buy Products</strong></td>
                            <td class="color secondary border twice-top"><strong>Products</strong></td>
                            <td colspan="2" class="border twice-top">' . count($shipment->items) . '</td>
                            <td class="color secondary border twice-top"><strong>Quantity</strong></td>
                            <td>' . $item_quantity . '</td>
                            <td colspan="4" class=""></td>
                          </tr>
                    ';
                    $shipment_details .= ' <tr>
                            <td colspan="2" class="color primary border twice-top twice-bottom twice-right"><strong>Try & Buy Fees</strong></td>
                            <td colspan="6" colspan="4" class="border twice-top twice-bottom twice-right">' . $shipment->try_and_buy_fees . '</td>
                          </tr>';
                    $shipment_details .= $table_end;

                }
                
                
                if($shipment->booking_type_id == 1 && $shipment->pieces > 1){
                    $shipment_pieces = '';

                    foreach ($shipment->shipment_pieces as $piece){
                        $shipment_pieces .= '<table class="table table-sm table-bordered border twice">
                    <tbody><tr>';
                        $shipment_pieces .= '<td rowspan="3" class="text-center align-middle border twice-bottom twice-right"><img src="' . asset('img/trax_logo_new.png') . '" width="100" class="d-block mx-auto">' . $print_details . '</td>';
                        $shipment_pieces .= '<td rowspan="3" class="text-center align-middle pl-1 pr-1 border twice-bottom twice-left twice-right">
                              <img src="data:image/png;base64,' . base64_encode($generator->getBarcode($piece->tracking_number, $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                              <span><strong>' . $piece->tracking_number . '</strong></span>
                            </td>
                            <td rowspan="1" class="color primary border twice-left"><strong>Origin</strong></td>
                            <td rowspan="1" class="border">' . $shipment->pickup_address->city->name . '</td>
                            <td rowspan="1" class="color primary border "><strong>Destination</strong></td>
                            <td rowspan="1" class="border">' . $shipment->consignee_city->name .'</td>
                            
                            <td rowspan="3" class="text-center align-middle pl-1 pr-1 border twice-bottom twice-left twice-right">
                            <img src="data:image/png;base64,' . base64_encode($generator->getBarcode($shipment->tracking_number, $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                            <span><strong>' . $shipment->tracking_number . '</strong></span>
                        </td>
                        <td rowspan="3" class="text-center align-middle pl-1 pr-1 border twice-bottom twice-left twice-right"><span class="piece_number"><strong>' . $piece->numbering. '/' .$shipment->pieces . '</strong></span>
                        </td>
                            </tr>
                            <tr>
                            <td class="color primary border twice-left"><strong>Shipper</strong></td>
                            <td class="border">'. $shipment->user->name .'</td>
                            <td class="color primary border "><strong>Booking Date</strong></td>
                            <td class="border">'. $shipment->created_at .'</td>
</tr>
                          ';
                        $shipment_pieces .= '</tbody></table>';

                    }


                    $shipment_details .= $shipment_pieces;
                }

                if ($shipment->user->logo_status) {
                    if ($shipment->shipment_invoice_status) {
                        $logo = $shipment->user->logo;
                        $invoice_id = '(' . ($shipment->order_id != null) ? $shipment->order_id : '' . ')';
                        $logo_invoice = '<div class="invoice p-1" style="page-break-before: always;">
                    <div class="row"><div class="col-3"><h2>Invoice ' . $invoice_id . '</h2></div></div>
                    <div class="row"><div class="col-6 text-center">
                    <img src="' . Storage::url('shippers_logo/' . $logo) . '" width="100" class="d-block mb-1">
</div><div class="col-6 text-right"><img src="' . asset('img/trax_logo_new.png') . '" width="100" class="d-block mb-1" style="margin: 0 auto;"></div></div>
                    
                    <div class="row align-items-start justify-content-between p-2">
                        <div class="col-12">
                            <div class=""><h5 class="d-inline">Booking Date: </h5> <span>' . $shipment->created_at . '</span></div>
                            <div class="mb-2"><h5 class="d-inline">Shipper Name: </h5> <span>' . $shipment->user->name . '</span></div>
                            
                            <div class=""><h5 class="d-inline">Consignee Name: </h5> <span>' . $shipment->consignee_name . '</span></div>
                            <div class=""><h5 class="d-inline">Consignee Address: </h5> <span>' . $shipment->consignee_address . '</span></div>
                            <div class=""><h5 class="d-inline">Consignee City: </h5> <span>' . $shipment->consignee_city->name . '</span></div>
                            <div class=""><h5 class="d-inline">Consignee Phone Number: </h5> <span>' . $shipment->consignee_phone_number_1 . (($shipment->consignee_phone_number_2) ? (' / ' . $shipment->consignee_phone_number_2) : '') . '</span></div>
                        </div>
                    </div>';
                        $invoice_items = '';
                        $shipment_invoice = ShipmentInvoice::where('shipment_id', $shipment->id)->first();
                        if ($shipment_invoice) {
                            $invoice_items .= '<div class="row align-items-start justify-content-between summary">
                        <div class="col-12">
                            <table class="table table-sm invoice">
                                  <thead><tr><td  colspan="1">S.NO.</td>
                                  <td class="text-center" colspan="6">ITEM DESCRIPTION</td>
                                  <td class="text-center" colspan="2">AMOUNT</td></tr></thead><tbody>';
                            $serial = 1;
                            foreach ($shipment_invoice->items as $item) {
                                $invoice_items .= '<tr>
                                    <td colspan="1">' . $serial . '</td>
                                    <td colspan="6" class="">' . $item->description . '</td>
                                    <td colspan="2" class="text-center color secondary">' . $item->amount . '</td>
                                    </tr>
                                    ';
                                $serial++;
                            }
                            $invoice_items .= '<tr colspan="1"><td></td><td colspan="6" class="text-right">Shipping Charges</td><td class="text-center" colspan="2">' . $shipment_invoice->shipping_charges . '</td></tr>
                                <td colspan="1"></td><td colspan="6" class="text-right">Total COD Amount</td><td class="text-center" colspan="2">' . $shipment_invoice->total_cod . '</td></tbody>
                            </table>
                        </div>
                    </div>';
                        }
                        $logo_invoice .= $invoice_items;
                        $logo_invoice .= '</div>';
                        $shipment_details .= $logo_invoice;
                    }

                }
            }
        }

        $html .= $shipment_details;

        if (!$body_only) {
            $html .= '
                    </div>
            ';

            if ($user_type != 4 && $type != 'pdf') {
                $html .= '
                <script>
                  window.onload = function() {
                    window.print();
                  }
                </script>
                ';
            }

            $html .= '
                  </body>
                </html>
            ';
        }

        $print_info = CrmConsigneeInfoPrint::where('crm_request_id', $request->crm_request_id)->where('shipment_id', $request->shipment_id);
        if(!$print_info->exists()){
            $print_info = new CrmConsigneeInfoPrint();
            $print_info->crm_request_id = $request->crm_request_id;
            $print_info->shipment_id = $request->shipment_id;
            $print_info->save();
        }
        return $html;
    }

    public function consignee_info_resolve(Request $request){
        $crm_request = CrmRequest::find($request->crm_request_id);
        if($crm_request->status_id == 2){
            if ($crm_request->status_id != 3) {
                CrmRequest::where('id', $request->crm_request_id)->update([
                    'status_id' => 3
                ]);
                CrmRequestStatusHistory::create([
                    'crm_request_id' => $request->crm_request_id,
                    'status_id' => 3,
                    'agent_id' => Auth::id()
                ]);
                if(DelayInDeliveryShipment::where('crm_request_id', $request->crm_request_id)->exists()){
                    DelayInDeliveryShipment::where('crm_request_id', $request->crm_request_id)->delete();
                }
                if(CrmPaymentShipment::where('crm_request_id', $request->crm_request_id)->exists()){
                    CrmPaymentShipment::where('crm_request_id', $request->crm_request_id)->delete();
                }
                return response()->json(['status' => 1, 'success' => 'Request marked as Resolved']);
            } else {
                return response()->json(['status' => 0, 'error' => 'Request is already marked as Resolved']);
            }
        }
    }

    public function crm_image_details(Request $request){

        $crm_request_id = $request->crm_request_id;
        if($crm_request_id){
            $crm_request = CrmRequest::find($crm_request_id);
            if($crm_request){
                $details = array();
                $crm_images = CrmRequestImage::where('crm_request_id', $crm_request_id);
                if($crm_images->exists()){
                    $crm_images = $crm_images->get();
                    foreach ($crm_images as $crm_image) {
                        $img_url = asset('uploads/crm_request_images/'.$crm_image->image);
                        $details[] = array('id' => $crm_image->id,'date' => Carbon::parse($crm_image->created_at)->toDateTimeString(),'image'=> $img_url);
                    }
                    return response()->json(['status' => 0, 'images' => $details]);
                }
                return response()->json(['status' => 2]);
            }
            return response()->json(['status' => 1, 'error' => 'CRM Request not found!']);
        }
    }

    public function crm_image_submit(Request $request){
        $crm_request_id = $request->image_crm_request_id;
        $image_ids = explode(',', $request->selected_ids);
        if(count($image_ids) == 0){
            return redirect()->back()->with('error', 'No images selected!');
        }
        $crm_request = CrmRequest::find($crm_request_id);
        if($crm_request){
            $crm_request_images = $crm_request->images->count();
            if($crm_request_images == 2){
                return redirect()->back()->with('error', 'Two images are already uploaded!');
            }

            foreach ($image_ids as $id){
                    $file_name = 'crm_image_'.$id;
                    $image = $request->file($file_name);

                    $extension = $image->getClientOriginalExtension();
                    $random = rand(1000, 100000);
                    $now = Carbon::now();
                    $time = $now->year . '_' . $now->month;
                    $generated_image_name = $time . $random . Auth::id() . '.' . $extension;
                    $image->move(public_path('uploads/crm_request_images'), $generated_image_name);
                    $crm_image = new CrmRequestImage();
                    $crm_image->crm_request_id = $crm_request_id;
                    $crm_image->added_by = Auth::id();
                    $crm_image->image = $generated_image_name;
                    $crm_image->save();
            }

            return redirect()->back()->with(['status' => 1, 'success' => 'CRM Images updated successfully']);

        }
        return redirect()->back()->with(['status' => 0, 'error' => 'CRM Request Not found!']);
    }
    public function crm_image_delete(Request $request){
        $crm_image_id = $request->crm_image_id;
        if($crm_image_id){
            $crm_image = CrmRequestImage::find($crm_image_id);
            if($crm_image){
                $crm_image_name = public_path('uploads/crm_request_images/'.$crm_image->image);
                if (is_file($crm_image_name))
                {
                    unlink($crm_image_name);
                }
                $crm_image->delete();
                return response()->json(['status' => 0, 'success' => 'Image deleted successfully!']);
            }
            return response()->json(['status' => 1, 'error' => 'Image not found!']);
        }
        return response()->json(['status' => 1, 'error' => 'Image ID not selected!']);
    }

    static public function key_account_crm_summary_shipments($shipment_id, $crm_request_id, $admin_id, $channel_id, $case_nature_type_id){
        $daily_summary_crm = KeyAccountDailySummaryCrm::where('admin_id', $admin_id)->where('case_nature_type_id', $case_nature_type_id)->where('channel_id', $channel_id);
        if($daily_summary_crm->exists()){
            $daily_summary_crm = $daily_summary_crm->first();
            $count = $daily_summary_crm->count + 1;
            $daily_summary_crm->count = $count;
            $daily_summary_crm->save();
        }
        else{
            $daily_summary_crm = new KeyAccountDailySummaryCrm();
            $daily_summary_crm->admin_id = $admin_id;
            $daily_summary_crm->case_nature_type_id = $case_nature_type_id;
            $daily_summary_crm->count = 1;
            $daily_summary_crm->channel_id = $channel_id;
            $daily_summary_crm->save();
        }

        $daily_shipment_crm = new KeyAccountDailyShipmentCrm();
        $daily_shipment_crm->shipment_id = $shipment_id;
        $daily_shipment_crm->admin_id = $admin_id;
        $daily_shipment_crm->case_nature_type_id = $case_nature_type_id;
        $daily_shipment_crm->save();

        $pending_summary_crm = KeyAccountPendingSummaryCrm::where('case_nature_type_id', $case_nature_type_id)->where('admin_id', $admin_id);
        if($pending_summary_crm->exists()){
            $pending_summary_crm = $pending_summary_crm->first();
            $count = $pending_summary_crm->count;
            $count = $count + 1;
            $tat = ($pending_summary_crm->tat * $pending_summary_crm->count) / ($count);
            $pending_summary_crm->tat = $tat;
            $pending_summary_crm->count = $count;
            $pending_summary_crm->save();
        }
        else{
            $pending_summary_crm = new KeyAccountPendingSummaryCrm();
            $pending_summary_crm->case_nature_type_id = $case_nature_type_id;
            $pending_summary_crm->admin_id = $admin_id;
            $pending_summary_crm->count = 1;
            $pending_summary_crm->tat = 0;
            $pending_summary_crm->save();
        }
        $pending_crm = new KeyAccountPendingCrm();
        $pending_crm->admin_id = $admin_id;
        $pending_crm->crm_request_id = $crm_request_id;
        $pending_crm->summary_crm_request_id = $pending_summary_crm->id;
        $pending_crm->save();
    }

    public function bulk_comment_for_shipper(Request $request){
        $comment_type= $request->comment_type;
        $comment = $request->comment;
        $crm_request_ids = $request->crm_request_ids;
        if(is_array($crm_request_ids)){
            if(count($crm_request_ids) > 0){
                if($comment != null){
                    foreach ($crm_request_ids as $request_id){
                        $crm_comment = new CrmComments();
                        $crm_comment->crm_request_id = $request_id;
                        $crm_comment->comment_by_id = Auth::id();
                        $crm_comment->comment_by = 0;
                        $crm_comment->comment_type = $comment_type;
                        $crm_comment->comment = $comment ;
                        $crm_comment->save();
                    }
                    return response()->json(['status'=> 1,'success'=>"Comments Added"]);
                }
                else{
                    return response()->json(['status'=> 0,'error'=>"Add Comment First"]);
                }
            }
            else{
                return response()->json(['status'=> 0,'error'=>"Select Request First"]);
            }
        }
        else{
            return response()->json(['status'=> 0,'error'=>"Select Request First"]);
        }

    }

    public function lost_claim(Request $request){

        $shipment = Shipment::find($request->shipment_id);
        if($shipment){
//            if($shipment->shipper_status_id == 1 || $shipment->shipper_status_id == 17){
                if($shipment->receiving_sheet_shipment){
                    $receiving_sheet_id = $shipment->receiving_sheet_shipment->receiving_sheet_id;
                    return response()->json(['status' => 1,'receiving_sheet_id' => $receiving_sheet_id]);
                }
                else{
                    return response()->json(['status' => 0,'error'=>'Receiving Sheet does not exists']);
                }
//            }
//            else{
//                return response()->json(['status' => 0,'error'=>'Only Booked and Cancelled Shipments Allowed']);
//            }
        }
        return response()->json(['status' => 0,'error'=>'No Shipments Found']);
    }

    public function special_request_appvove(Request $request){

     $request_id = $request->request_id;
     $admin_ids = $request->admin;

     if($admin_ids){
         $approval = SpecialApprovalRequest::where('crm_request_id',$request_id)->update(['status' => 0]);
         foreach($admin_ids as $admin){

             NotificationsController::send(131,$request_id,$admin);

             $approval_request =  new SpecialApprovalRequest();
             $approval_request->crm_request_id = $request_id;
             $approval_request->admin_id = $admin;
             $approval_request->status = 1;
             $approval_request->save();

         }
         return redirect()->back()->with(['success'=> "Request Submitted"]);
     }
     else{
         return redirect()->back()->with(['error'=> "Select One Admin At-least"]);
     }

    }

   /* public function special_request_tag(Request $request){
     $request_id = $request->crm_request_id;
     $approval_request = SpecialApprovalRequest::where('crm_request_id',$request_id)->latest();
     if($approval_request->exists()){
       $approval_request = $approval_request->get();
       $admin = array();
       foreach ($approval_request as $sr){
           $admin[] = Admin::find($sr->admin_id)->name;
       }
         return response()->json(['status' => 1,'admin' => $admin]);
     }
     else{
         return response()->json(['status' => 0,'error'=>'No Request Found']);
     }


    }*/
}
