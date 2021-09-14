<?php

namespace App\Http\Controllers;

use App\Http\Controllers\CRM\CRMController;
use App\Http\Models\CRM\CrmRequest;
use App\Http\Models\CRM\CrmRequestCaseNature;
use App\Http\Models\CRM\CrmRequestCaseNatureType;
use App\Http\Models\CRM\CrmRequestChannel;
use App\Http\Models\DonePayment;
use App\Http\Models\DonePaymentShipment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Models\Shipment;
use App\Http\Models\ShipmentJourney;

use Auth;

use Yajra\Datatables\Datatables;
use Carbon\Carbon;

class TrackingController extends Controller
{
    public function index() {
        $case_nature = CrmRequestCaseNature::get();
        $case_nature_type_complaints = CrmRequestCaseNatureType::where('nature_id', '=', 1)->where('status_id',1)->get();
        $case_nature_channels = CrmRequestChannel::where('id', '!=', 1)->get();
        return view('tracking')->with(['case_nature' => $case_nature, 'case_nature_complaints' => $case_nature_type_complaints, 'case_nature_channels' => $case_nature_channels]);
    }

    public function track(Request $request) {
    	$tracking_numbers = explode(',', $request->tracking_numbers);

    	$tracking = array();

    	foreach ($tracking_numbers as $tracking_number) {
    		$shipment = Shipment::where('tracking_number', $tracking_number);

    		if ($shipment->exists()) {
                $shipment = $shipment->first();

                if ($shipment->user->blacklist == 0) {
        			$details = array();

                    $details['tracking_number'] = $tracking_number;

        			$details['shipper']['name'] = $shipment->user->name;

                    $details['pickup']['origin'] = $shipment->pickup_address->city->name;

        			$details['consignee']['name'] = $shipment->consignee_name;
        			$details['consignee']['destination'] = $shipment->consignee_city->name;

        			foreach ($shipment->shipment_journey as $journey) {
                        if ($journey->consignee_status_id) {
                            if ($journey->verification) {
                                $journey_details = array();

                                $journey_details['date_time'] = $journey->created_at->toDateTimeString();
                                $journey_details['status'] = $journey->shipment_status_consignee->name;

                                $details['tracking_history'][] = $journey_details;
                            }

                        }
        			}

        			$tracking['shipments'][$shipment->id] = $details;
                }
                else {
                    $tracking['invalid'][] = $tracking_number;
                }
    		}
    		else {
    			$tracking['invalid'][] = $tracking_number;
    		}
    	}

    	return $tracking;
    }


    public function add_request(Request $request){
        $case_nature = 1;
        $case_nature_type = $request->complaint_id;
        $request_channel = 2;
        $discription = $request->description;
        $reopencount = 0;
        $shipment_id = $request->shipment_id;
        $launched_by = 4;
        $name = $request->complaint_name;
        $phoneno = $request->complaint_phone;
        $data = new CrmRequest();
        $data->case_nature_id = $case_nature;
        $data->case_nature_type_id =$case_nature_type ;
        $data->description = 'Consignee :('.$name.') | Phone Number : ('.$phoneno.') | Complain : '. $discription;
        $data->channel_id =$request_channel;
        $data->status_id = 1;
        $data->launched_by = $launched_by;
        $data->shipment_id = $shipment_id;

        $data->save();
        $id = str_pad($data->id, 6, 0, STR_PAD_LEFT);
        return ['status' => 1, 'success' => 'Request ('. $id .') successfully added'];

////        $nature_id = $request->case_nature_id;
////        $complaint_id = $request->complaint_id;
////        $channel_id = $request->channel_id;
////        $receiving_sheet_id = $request->receiving_sheet_id;
//////        if($complaint_id == 23 && $receiving_sheet_id != null){
////        if($complaint_id == 23){
////            $description_text = $request->description ;
//////            $description = '<strong>' .'Receiving Sheet No: ' .$receiving_sheet_id. '</strong>'. PHP_EOL. $description_text;
////            $description = $description_text;
////        }
////        else{
////            $description = $request->description;
////        }
////        $flag = false;
////        $cannot_change = false;
////        $present_shipments = array();
////        if ($request->has('payment_request')) {
////            if($request->payment_request == 1){
////                $payment_id = $request->payment_id;
////                $payment_id_padded = str_pad($request->payment_id, 6, 0, STR_PAD_LEFT);
////                if(!empty($payment_id)){
////                    $payment = DonePayment::find($payment_id);
////                    $payment_shipment = DonePaymentShipment::where('done_payment_id', $payment->id)->first();
////                    $shipment = Shipment::where('id', $payment_shipment->shipment_id)->first();
////                    $is_shipment = CrmRequest::where('shipment_id',$shipment->id)->where('case_nature_id', $nature_id);
////                    if($is_shipment->exists()){
////                        return ['status' => 0, 'error' => 'Request/Complaint already lodged for the Payment ID: ' . $payment_id_padded];
////                    }
////                    else{
////                        $crm_request_padded_id = CRMController::add($nature_id, $complaint_id, $channel_id, 1, \Illuminate\Support\Facades\Auth::id(), 0, $shipment->id, $shipment->user_id, NULL ,$description);
////                        if($request->has('key_account')){
////                            $this->key_account_crm_summary_shipments($shipment->id, $crm_request_padded_id, Auth::id(), $channel_id, $complaint_id);
////                        }
////                    }
////                    $crm_request_padded_id = str_pad($crm_request_padded_id, 6, 0, STR_PAD_LEFT);
////
////                    return ['status' => 1, 'success' => 'Request ('. $crm_request_padded_id .') successfully added'];
////                }else{
////                    return ['status' => 0, 'error' => 'No Payment selected!'];
////                }
////            }
////        }
////        else{
////            if ($request->has('shipment_ids')) {
////                if ($nature_id == 4) {
////                    $shipment_ids = explode(',', $request->input('shipment_ids'));
////                }
////                else{
////                    $shipment_ids = $request->shipment_ids;
////                }
////                if(!empty($shipment_ids)){
////                    foreach ($shipment_ids as $shipment_id) {
////                        $shipment = Shipment::find($shipment_id);
////                        if($shipment){
////
////                            $is_shipment = CrmRequest::where('shipment_id',$shipment_id)->where('case_nature_id',$nature_id)->first();
////                            if($is_shipment){
////                                $complain = $is_shipment->id;
////                                if($is_shipment->case_nature_id != $nature_id){
////                                    if ($nature_id == 4) {
////                                        if($complaint_id == 26){
////                                            $crm_request_padded_id = CRMController::add($nature_id, $complaint_id, $channel_id, 1, Auth::id(), 0, $shipment_id, $shipment->user_id, NULL , $description);
////                                        }
////                                        else{
////                                            $crm_request_padded_id = CRMController::add($nature_id, $complaint_id, $channel_id, 1, Auth::id(), 0, $shipment_id, $shipment->user_id, NULL , $description, $request->product_cost,  $request->file('product_picture'), $request->file('invoice_picture'));
////                                        }
////                                        if($request->has('key_account')){
////                                            $this->key_account_crm_summary_shipments($shipment->id, $crm_request_padded_id, Auth::id(), $channel_id, $complaint_id);
////                                        }
////                                    }
////                                    else{
////                                        if($shipment->shipper_status_id == 20 || $shipment->shipper_status_id == 1){
////                                            if(in_array($complaint_id, [11, 12, 13])){
////                                                $present_shipments[] = $shipment->tracking_number;
////                                                $flag = true;
////                                                $cannot_change = true;
////                                            }
////                                            else{
////                                                $crm_request_padded_id = CRMController::add($nature_id, $complaint_id, $channel_id, 1, Auth::id(), 0, $shipment_id, $shipment->user_id, NULL ,$description);
////                                                if($request->has('key_account')){
////                                                    $this->key_account_crm_summary_shipments($shipment->id, $crm_request_padded_id, Auth::id(), $channel_id, $complaint_id);
////                                                }
////                                            }
////                                        }
////                                        else{
////                                            $crm_request_padded_id = CRMController::add($nature_id, $complaint_id, $channel_id, 1, Auth::id(), 0, $shipment_id, $shipment->user_id, NULL ,$description);
////                                            if($request->has('key_account')){
////                                                $this->key_account_crm_summary_shipments($shipment->id, $crm_request_padded_id, Auth::id(), $channel_id, $complaint_id);
////                                            }
////                                        }
////                                    }
////                                }else{
////                                    $present_shipments[] = $shipment->tracking_number;
////                                    $present_shipments[] = 'Complaint ID: '. $complain;
////                                    $flag = true;
////                                }
////                            }
////                            else{
////                                if ($nature_id == 4) {
////                                    if ($complaint_id == 21 || $complaint_id == 22) {
////                                        $crm_request_padded_id = CRMController::add($nature_id, $complaint_id, $channel_id, 1, Auth::id(), 0, $shipment_id, $shipment->user_id, NULL, $description, $request->product_cost, $request->file('product_picture'), $request->file('invoice_picture'), $request->file('damage_product_picture'), $request->file('product_packaging_picture'), $request->file('actual_product_picture'), $request->damage_claim_product_cost, $request->file('missing_product_picture'), $request->file('product_packaging_picture_content_short'), $request->file('actual_product_picture_content_short'), $request->claim_content_product_cost);
////                                    }
////                                    else {
////                                        $crm_request_padded_id = CRMController::add($nature_id, $complaint_id, $channel_id, 1, Auth::id(), 0, $shipment_id, $shipment->user_id, NULL , $description, $request->product_cost,  $request->file('product_picture'), $request->file('invoice_picture'));
////                                    }
////                                    if($request->has('key_account')){
////                                        $this->key_account_crm_summary_shipments($shipment->id, $crm_request_padded_id, Auth::id(), $channel_id, $complaint_id);
////                                    }
////                                }
////                                else{
////                                    if($shipment->shipper_status_id == 20 || $shipment->shipper_status_id == 1){
////                                        if(in_array($complaint_id, [11, 12, 13])){
////                                            $present_shipments[] = $shipment->tracking_number;
////                                            $flag = true;
////                                            $cannot_change = true;
////                                        }
////                                        else{
////                                            $crm_request_padded_id = CRMController::add($nature_id, $complaint_id, $channel_id, 1, Auth::id(), 0, $shipment_id, $shipment->user_id, NULL ,$description);
////                                            if($request->has('key_account')){
////                                                $this->key_account_crm_summary_shipments($shipment->id, $crm_request_padded_id, Auth::id(), $channel_id, $complaint_id);
////                                            }
////                                        }
////                                    }
////                                    else{
////                                        $crm_request_padded_id = CRMController::add($nature_id, $complaint_id, $channel_id, 1, Auth::id(), 0, $shipment_id, $shipment->user_id, NULL ,$description);
////                                        if($request->has('key_account')){
////                                            $this->key_account_crm_summary_shipments($shipment->id, $crm_request_padded_id, Auth::id(), $channel_id, $complaint_id);
////                                        }
////                                    }
////                                }
////                            }
////                        }
////                    }
////                    return ['status' => 1, 'success' => 'Request(s) successfully added', 'flag' => $flag, 'already_existed_shipments' => $present_shipments, 'cannot_change' => $cannot_change];
////                }else{
////                    return ['status' => 0, 'error' => 'No shipments selected!'];
////                }
////            }
////            else{
////                $shipment_id = $request->shipment_id;
////                if(!empty($shipment_id)){
////                    $shipment = Shipment::find($shipment_id);
////                    if($shipment){
////                        $is_shipment = CrmRequest::where('shipment_id',$shipment_id)->first();
////                        $is_shipment = CrmRequest::where('shipment_id',$shipment_id)->where('case_nature_id',$nature_id)->first();
////                        if($is_shipment){
////                            $complain = $is_shipment->id;
////                            if($is_shipment->case_nature_id != $nature_id){
////                                if($shipment->shipper_status_id == 20 || $shipment->shipper_status_id == 1){
////                                    if(in_array($complaint_id, [11, 12, 13])){
////                                        $present_shipments[] = $shipment->tracking_number;
////                                        $flag = true;
////                                        $cannot_change = true;
////                                    }
////                                    else{
////                                        $crm_request_padded_id = CRMController::add($nature_id, $complaint_id, $channel_id, 1, Auth::id(), 0, $shipment_id, $shipment->user_id, NULL ,$description);
////                                        if($request->has('key_account')){
////                                            $this->key_account_crm_summary_shipments($shipment->id, $crm_request_padded_id, Auth::id(), $channel_id, $complaint_id);
////                                        }
////                                        $crm_request_padded_id = str_pad($crm_request_padded_id, 6, 0, STR_PAD_LEFT);
////                                        return ['status' => 1, 'success' => 'Request ('. $crm_request_padded_id .') successfully added', 'flag' => $flag, 'already_existed_shipments' => $present_shipments];
////                                    }
////                                }
////                                else{
////                                    $crm_request_padded_id = CRMController::add($nature_id, $complaint_id, $channel_id, 1, Auth::id(), 0, $shipment_id, $shipment->user_id, NULL ,$description);
////                                    if($request->has('key_account')){
////                                        $this->key_account_crm_summary_shipments($shipment->id, $crm_request_padded_id, Auth::id(), $channel_id, $complaint_id);
////                                    }
////                                    $crm_request_padded_id = str_pad($crm_request_padded_id, 6, 0, STR_PAD_LEFT);
////                                    return ['status' => 1, 'success' => 'Request ('. $crm_request_padded_id .') successfully added', 'flag' => $flag, 'already_existed_shipments' => $present_shipments];
////                                }
////                            }else{
////                                $present_shipments[] = $shipment->tracking_number;
////                                $present_shipments[] = 'Complaint ID: '. $complain;
////                                $flag = true;
////                            }
////                        }else{
////                            if($shipment->shipper_status_id == 20 || $shipment->shipper_status_id == 1){
////                                if(in_array($complaint_id, [11, 12, 13])){
////                                    $present_shipments[] = $shipment->tracking_number;
////                                    $flag = true;
////                                    $cannot_change = true;
////                                }
////                                else{
////                                    $crm_request_padded_id = CRMController::add($nature_id, $complaint_id, $channel_id, 1, Auth::id(), 0, $shipment_id, $shipment->user_id, NULL ,$description);
////                                    if($request->has('key_account')){
////                                        $this->key_account_crm_summary_shipments($shipment->id, $crm_request_padded_id, Auth::id(), $channel_id, $complaint_id);
////                                    }
////                                    $crm_request_padded_id = str_pad($crm_request_padded_id, 6, 0, STR_PAD_LEFT);
////                                    return ['status' => 1, 'success' => 'Request ('. $crm_request_padded_id .') successfully added', 'flag' => $flag, 'already_existed_shipments' => $present_shipments];
////                                }
////                            }
////                            else{
////                                $crm_request_padded_id = CRMController::add($nature_id, $complaint_id, $channel_id, 1, Auth::id(), 0, $shipment_id, $shipment->user_id, NULL ,$description);
////                                if($request->has('key_account')){
////                                    $this->key_account_crm_summary_shipments($shipment->id, $crm_request_padded_id, Auth::id(), $channel_id, $complaint_id);
////                                }
////                                $crm_request_padded_id = str_pad($crm_request_padded_id, 6, 0, STR_PAD_LEFT);
////                                return ['status' => 1, 'success' => 'Request ('. $crm_request_padded_id .') successfully added', 'flag' => $flag, 'already_existed_shipments' => $present_shipments];
////                            }
////                        }
////                    }
////                    return ['status' => 1, 'success' => 'Request(s) successfully added', 'flag' => $flag, 'already_existed_shipments' => $present_shipments, 'cannot_change' => $cannot_change];
////                }else{
////                    return ['status' => 0, 'error' => $request->shipment_id];
////                }
//            }
//        }
        return ['status' => 0, 'error' => $request->shipment_id];
    }
}