<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Admins\AdminCRMController;
use App\Http\Controllers\CRM\CRMCommentController;
use App\Http\Controllers\CRM\CRMController;
use App\Http\Models\Admin\ChangeShipmentAmountLog;

use App\Http\Models\CRM\CrmRequest;
use App\Http\Models\CRM\CrmRequestCaseNature;
use App\Http\Models\CRM\CrmRequestCaseNatureType;
use App\Http\Models\CRM\CrmRequestChannel;
use App\Http\Models\Shipment;

use App\Http\Requests\AddCrmRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use function foo\func;

class ShipperCrmApiController extends Controller
{

    public function add_crm_request(AddCrmRequest $request) {


        $nature_id = $request->case_nature_id;
        $complaint_id = $request->complaint_id;
        $app_type = $request->app_type;
        $description = $request->description;
        $user_id = $app_type == 2 ? $request->retail_user_id : $request->shipper_id;
        $shipment_ids = explode(',', $request->input('shipment_ids'));
        $launched_by = $app_type == 2 ? 3 : 1; // check shipper or retail shipper
        $present_shipments = array();

        if (!empty($shipment_ids)) {
            foreach ($shipment_ids as $shipment_id) {
                $shipment = Shipment::find($shipment_id);
                if ($shipment) {

                    if ($complaint_id == 12 && in_array($shipment->shipper_status_id, [14, 18, 30, 36, 37, 20, 21, 22, 23, 24, 25, 26, 32, 44, 47, 48, 57, 60, 51])) // for cod change automation
                    {
                        return response()->json(['status' => 1,'error'=>'Request cannot be catered at this status of the shipment.']);
                    }

                    $is_shipment = CrmRequest::where('shipment_id', $shipment_id)->where('case_nature_id', $nature_id)->first();
                    $already_lodged = false;

                    if ($is_shipment) {
                        $already_lodged = true;
                        if ($is_shipment->case_nature_id != $nature_id) {

                            if($nature_id == 4 && $complaint_id!=26) {

                                $product_cost = $request->product_cost;
                                $product_picture = $request->file('product_picture');
                                $invoice_picture = $request->file('invoice_picture');
                                //21 (shipment damage)
                                $damage_product_picture = null;
                                $damage_claim_product_cost = null;
                                $product_packaging_picture = null;
                                $actual_product_picture = null;
                                //22 (content short)
                                $missing_product_picture = null;
                                $claim_content_product_cost = null;
                                $product_packaging_picture_content_short = null;
                                $actual_product_picture_content_short = null;

                                if ($complaint_id == 21) {
                                    $product_packaging_picture = $request->file('product_packaging_picture');
                                    $actual_product_picture = $request->file('actual_product_picture');
                                    $damage_product_picture = $request->file('damage_product_picture');
                                    $damage_claim_product_cost = $request->damage_claim_product_cost;

                                } elseif ($complaint_id == 22) {
                                    $product_packaging_picture_content_short =  $request->file('product_packaging_picture');
                                    $actual_product_picture_content_short  =  $request->file('actual_product_picture');
                                    $missing_product_picture = $request->file('missing_product_picture');
                                    $claim_content_product_cost = $request->claim_content_product_cost;
                                }

                                CRMController::add($nature_id, $complaint_id, 1, 1, $user_id, $launched_by, $shipment_id, $user_id, NULL, $description, $product_cost,$product_picture, $invoice_picture,$damage_product_picture, $product_packaging_picture,$actual_product_picture,$damage_claim_product_cost,$missing_product_picture,$product_packaging_picture_content_short, $actual_product_picture_content_short, $claim_content_product_cost);
                            }
                            else {

                                if ($shipment->shipper_status_id == 20 || $shipment->shipper_status_id == 1) {
                                    if (in_array($complaint_id, [11, 13])) {
                                        $present_shipments[] = $shipment->tracking_number;
                                    }
                                }

                                if ($complaint_id == 12) {
                                    if ($shipment->shipper_status_id == 5) {
                                        $description = $description . " (change old amouunt $shipment->amount to new amount $request->cod_new_amount )";
                                        $crm_request_padded_id = CRMController::add($nature_id, $complaint_id, 1, 1, $user_id, $launched_by, $shipment_id, $user_id, NULL, $description);

                                    } else {
                                        $crm_request_padded_id =  CRMController::add($nature_id, $complaint_id, 1, 1, $user_id, $launched_by, $shipment_id, $user_id, NULL, $description, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1);
                                    }
                                } else {
                                    $crm_request_padded_id = CRMController::add($nature_id, $complaint_id, 1, 1, $user_id, $launched_by, $shipment_id,$user_id, NULL, $description);
                                }
                            }
                        } else {
                            $present_shipments[] = $shipment->tracking_number;
                        }
                    }
                    else {
                        if($nature_id == 4 && $complaint_id!=26) {

                            $product_cost = $request->product_cost;
                            $product_picture = $request->file('product_picture');
                            $invoice_picture = $request->file('invoice_picture');
                            //21 (shipment damage)
                            $damage_product_picture = null;
                            $damage_claim_product_cost = null;
                            $product_packaging_picture = null;
                            $actual_product_picture = null;
                            //22 (content short)
                            $missing_product_picture = null;
                            $claim_content_product_cost = null;
                            $product_packaging_picture_content_short = null;
                            $actual_product_picture_content_short = null;

                            if ($complaint_id == 21) {
                                $product_packaging_picture = $request->file('product_packaging_picture');
                                $actual_product_picture = $request->file('actual_product_picture');
                                $damage_product_picture = $request->file('damage_product_picture');
                                $damage_claim_product_cost = $request->damage_claim_product_cost;

                            } elseif ($complaint_id == 22) {
                                $product_packaging_picture_content_short =  $request->file('product_packaging_picture');
                                $actual_product_picture_content_short  =  $request->file('actual_product_picture');
                                $missing_product_picture = $request->file('missing_product_picture');
                                $claim_content_product_cost = $request->claim_content_product_cost;
                            }

                            CRMController::add($nature_id, $complaint_id, 1, 1, $user_id, $launched_by, $shipment_id, $user_id, NULL, $description, $product_cost,$product_picture, $invoice_picture,$damage_product_picture, $product_packaging_picture,$actual_product_picture,$damage_claim_product_cost,$missing_product_picture,$product_packaging_picture_content_short, $actual_product_picture_content_short, $claim_content_product_cost);
                        }
                        else {
                            if ($shipment->shipper_status_id == 20 || $shipment->shipper_status_id == 1) {
                                if (in_array($complaint_id, [11, 13])) {
                                    $present_shipments[] = $shipment->tracking_number;
                                }
                            }

                            if ($complaint_id == 12) {
                                if ($shipment->shipper_status_id == 5) {
                                    $description = $description . " (change old amouunt $shipment->amount to new amount $request->cod_new_amount )";
                                    $crm_request_padded_id = CRMController::add($nature_id, $complaint_id, 1, 1, $user_id, $launched_by, $shipment_id, $user_id, NULL, $description);

                                } else {
                                    $crm_request_padded_id =  CRMController::add($nature_id, $complaint_id, 1, 1, $user_id, $launched_by, $shipment_id, $user_id, NULL, $description, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1);
                                }
                            } else {
                                $crm_request_padded_id = CRMController::add($nature_id, $complaint_id, 1, 1, $user_id, $launched_by, $shipment_id,$user_id, NULL, $description);
                            }
                        }
                    }

                    if ($nature_id == 2) {
                        if ($complaint_id == 13) {
                            $shipment->consignee_phone_number_2 = $request->alternate_phone;
                            $shipment->save();
                        }
                        else if ($complaint_id == 12 && !$already_lodged) // for cod change automation
                        {
                            if ($request->has('cod_new_amount')) {
                                if ($request->cod_new_amount >= 0 && $shipment->shipper_status_id != 5) {

                                    $crm_request_id = CrmRequest::where('shipment_id', $shipment->id)->pluck('id')->first();
                                    $default_agent_id = 306;
                                    $comment_by = 0;
                                    $comment_type = 0;
                                    $comment = "Dear Customer,
                                                    Request of “COD Change” from (Old amount: $shipment->amount) to (New amount: $request->cod_new_amount) has been updated on system
                                                    
                                                    CRM automated Comment";

                                    CRMCommentController::add($crm_request_id, $default_agent_id, $comment_by, $comment_type, $comment, 1);

                                    $old_amount = $shipment->amount;
                                    $message = "Request of “COD Change” from (Old amount: $shipment->amount) to (New amount: $request->cod_new_amount) has been updated on system";
                                    $shipment->amount = $request->cod_new_amount;
                                    if ($request->is_zero_cod == 1 && $request->cod_parcel_value > 0) {
                                        $comment = "Dear Customer,
                                            Request of “COD Change” from (Old amount: $shipment->amount) to (New amount: $request->cod_new_amount) has been updated on system
                                            Due to change of COD amount 0. parcel value has been updated from ($shipment->parcel_value) to ($request->cod_parcel_value)
                                            
                                            CRM automated Comment";
                                        $shipment->parcel_value = $request->cod_parcel_value;
                                    }
                                    ChangeShipmentAmountLog::create([
                                        'shipment_id' => $shipment->id,
                                        'old_amount' => $old_amount,
                                        'new_amount' => $request->cod_new_amount,
                                        'remarks' => $request->cod_remarks,
                                        'admin_id' => 346 // for global admin
                                    ]);
                                    $shipment->save();
                                }
                            }
                        }
                        else if ($complaint_id == 32) {
                            $shipment->special_instructions = 'Allow to Open Shipment';
                            $shipment->save();
                        }
                    }
                }

            }

            if ($nature_id == 1 && isset($crm_request_padded_id)) {
                $case_nature_complainant =  $request->case_nature_complainant;
                $complainant_phone =  $request->complainant_phone;
                AdminCRMController::updateComplaintPhone($crm_request_padded_id,$case_nature_complainant, $complainant_phone);
            }

            if (!empty($present_shipments)) {
                if (count($shipment_ids) === count($present_shipments)) {
                    return response()->json([
                        'status' => 1,
                        'error' => 'Requests or complaints already exist for all provided shipments.',
                        'already_existed_shipments' => $present_shipments
                    ]);
                }

                return response()->json([
                    'status' => 0,
                    'success' => 'Requests added, but some shipments already have existing requests or complaints.',
                    'already_existed_shipments' => $present_shipments
                ]);
            }

            return response()->json([
                'status' => 0,
                'success' => 'Requests successfully added.',
                'already_existed_shipments' => []
            ]);
        }
    }
    public function crm_request_summary(Request $request)
    {
        $shipper_id = $request->shipper_id;

        $launched = CrmRequest::where('status_id',1)
            ->where('shipper_id', $shipper_id)
            ->count();

        $in_process = CrmRequest::where('status_id',2)
            ->where('shipper_id', $shipper_id)
            ->count();

        $closed = CrmRequest::where('status_id',4)
            ->where('shipper_id', $shipper_id)
            ->count();

        return response()->json(['status' => 0 , 'message' => 'Success' ,'launched'=>$launched,'in_process'=>$in_process,'closed'=>$closed]);

    }

    public function crm_request_resources(Request $request)
    {
        $case_nature = CrmRequestCaseNature::where('id','!=',3)->get();
        $channels = CrmRequestChannel::all();
        $complaints = CrmRequestCaseNatureType::where('status_id',1)->where('nature_id',1)->get();
        $service_requests = CrmRequestCaseNatureType::where('status_id',1)->where('nature_id',2)->get();
        $claims = CrmRequestCaseNatureType::where('status_id',1)->where('nature_id',4)->get();
        $complainants = [
            ['id' => 1, 'complainant_type' => 'Consignee'],
            ['id' => 2, 'complainant_type' => 'Shipper'],
        ];

        return response()->json(['status' => 0, 'case_nature' => $case_nature, 'channels' => $channels, 'complaints' => $complaints, 'service_requests' => $service_requests, 'claims' => $claims, 'complainants' => $complainants]);

    }

    public function crm_request_list(Request $request)
    {

        $shipper_id = $request->shipper_id;

        $crm_requests = CrmRequest::leftjoin('crm_request_case_nature as crcn', 'crcn.id', '=', 'crm_requests.case_nature_id')
            ->leftjoin('crm_request_case_nature_types as crcnt', 'crcnt.id', '=', 'crm_requests.case_nature_type_id')
            ->leftjoin('crm_request_channels as crc', 'crc.id', '=', 'crm_requests.channel_id')
            ->leftjoin('crm_request_statuses as crs', 'crs.id', '=', 'crm_requests.status_id')
            ->leftjoin('admins as ad', 'ad.id', '=', 'crm_requests.agent_id')
            ->leftjoin('admins as a', 'a.id', '=', 'crm_requests.launched_by_id')
            ->leftjoin('shipments as s', 's.id', '=', 'crm_requests.shipment_id')
            ->leftjoin('shipment_status as ss', 'ss.id', '=', 's.shipper_status_id')
            ->leftjoin('crm_closed_reasons as crmcr', 'crmcr.crm_request_id', '=', 'crm_requests.id')
            ->leftjoin('crm_closed_reason_statuses as crmcrs', 'crmcrs.id', '=', 'crmcr.status_id')
            ->leftjoin('crm_request_status_histories as crmst', function ($join) {
                $join->on('crmst.crm_request_id', '=', 'crm_requests.id')
                    ->where('crm_requests.status_id', 4)
                    ->where('crmst.id', '=',
                        DB::raw('(select max(id) from crm_request_status_histories where crm_request_status_histories.crm_request_id = crm_requests.id)'));
            })
            ->leftjoin('users as u', 'u.id', '=', 'crm_requests.shipper_id')
            ->select('crm_requests.id as id', 's.tracking_number as tracking_number','s.id as shipment_id', 'crcn.name as case_nature', 'crcnt.type as case_nature_type', 'crc.channel as channel', 'crs.name as request_status', 'ad.name as agent', 'a.name as name', 'crm_requests.launched_by as launched_added_by', 'crm_requests.created_at as created_at','crm_requests.description','crm_requests.status_id', 'ss.name as shipment_status','crm_requests.description as descr','crmst.created_at as closed_at','crm_requests.launched_by_id','crmcrs.name as at_fault', 'u.name as shipper_name')
            ->where('crm_requests.shipper_id', $shipper_id)
            ->orderBy('crm_requests.id', 'desc')
            ->get();

        if($crm_requests->isNotEmpty()) {
            return response()->json(['status' => 0 , 'message' => 'Success' ,'crm_requests'=>$crm_requests]);
        }
        return response()->json(['status' => 1 , 'message' => 'CRM Complaints not found!']);




    }
    public function single_crm_request(Request $request)
    {

        $validate = Validator::make($request->all(), [
            'tracking_number' => ['required', 'integer'],
        ]);

        if($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        }

        $crm_requests = CrmRequest::leftjoin('crm_request_case_nature as crcn', 'crcn.id', '=', 'crm_requests.case_nature_id')
            ->leftjoin('crm_request_case_nature_types as crcnt', 'crcnt.id', '=', 'crm_requests.case_nature_type_id')
            ->leftjoin('crm_request_channels as crc', 'crc.id', '=', 'crm_requests.channel_id')
            ->leftjoin('crm_request_statuses as crs', 'crs.id', '=', 'crm_requests.status_id')
            ->leftjoin('admins as ad', 'ad.id', '=', 'crm_requests.agent_id')
            ->leftjoin('admins as a', 'a.id', '=', 'crm_requests.launched_by_id')
            ->leftjoin('shipments as s', 's.id', '=', 'crm_requests.shipment_id')
            ->leftjoin('shipment_status as ss', 'ss.id', '=', 's.shipper_status_id')
            ->leftjoin('crm_closed_reasons as crmcr', 'crmcr.crm_request_id', '=', 'crm_requests.id')
            ->leftjoin('crm_closed_reason_statuses as crmcrs', 'crmcrs.id', '=', 'crmcr.status_id')
            ->leftjoin('crm_request_status_histories as crmst', function ($join) {
                $join->on('crmst.crm_request_id', '=', 'crm_requests.id')
                    ->where('crm_requests.status_id', 4)
                    ->where('crmst.id', '=',
                        DB::raw('(select max(id) from crm_request_status_histories where crm_request_status_histories.crm_request_id = crm_requests.id)'));
            })
            ->leftjoin('users as u', 'u.id', '=', 'crm_requests.shipper_id')
            ->select('crm_requests.id as id', 's.tracking_number as tracking_number', 's.id as shipment_id','crcn.name as case_nature', 'crcnt.type as case_nature_type', 'crc.channel as channel', 'crs.name as request_status', 'ad.name as agent', 'a.name as name', 'crm_requests.launched_by as launched_added_by', 'crm_requests.created_at as created_at','crm_requests.description','crm_requests.status_id', 'ss.name as shipment_status','crm_requests.description as descr','crmst.created_at as closed_at','crm_requests.launched_by_id','crmcrs.name as at_fault', 'u.name as shipper_name')
            ->where('crm_requests.shipper_id', $request->shipper_id)
            ->where('s.tracking_number',$request->tracking_number)
            ->orderBy('crm_requests.id', 'desc')
            ->get();

        return response()->json(['status' => 0 , 'message' => 'Success' ,'crm_request'=>$crm_requests]);

    }

    public function get_receving_sheet(Request $request)
    {

        $validate = Validator::make($request->only('shipment_id'), [
            'shipment_id' => ['required', 'integer'],
        ]);

        if($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        }

        $shipment = Shipment::find($request->shipment_id);
        if($shipment){
            if($shipment->receiving_sheet_shipment){
                $receiving_sheet_id = $shipment->receiving_sheet_shipment->receiving_sheet_id;
                return response()->json(['status' => 0,'receiving_sheet_id' => $receiving_sheet_id]);
            }
            else{
                return response()->json(['status' => 1,'error'=>'Receiving Sheet does not exists']);
            }
        }
        return response()->json(['status' => 1,'error'=>'No Shipments Found']);
    }



}
