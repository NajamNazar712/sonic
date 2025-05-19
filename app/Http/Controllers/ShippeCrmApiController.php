<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Admins\AdminCRMController;
use App\Http\Controllers\CRM\CRMController;
use App\Http\Models\CRM\CrmRequest;
use App\Http\Models\CRM\CrmRequestCaseNature;
use App\Http\Models\CRM\CrmRequestCaseNatureType;
use App\Http\Models\CRM\CrmRequestChannel;
use App\Http\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use function foo\func;

class ShippeCrmApiController extends Controller
{

    public function add_crm_request(Request $request)
    {

        $rules = [
            'tracking_no' => ['required'],
            'case_nature_id' => ['required'],
            'case_nature_type_id' => ['required'],
            'channel_id' => ['required'],
            'description' => ['required'],
            'case_nature_complainant' => ['required_if:case_nature_id,1'],
            'complainant_phone' => ['required_if:case_nature_id,1'],
        ];

        $validate = Validator::make($request->all(), $rules);


        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            if($request->has('alternate_phone')){
                if($request->alternate_phone){
                    $alternate_phone = $request->alternate_phone;
                }else{
                    $alternate_phone = null;
                }
            }
            $shipment = Shipment::where('tracking_number', $request->tracking_no);
            $shipment_info = array();
            if ($shipment->exists()) {
                $shipment = $shipment->first();

                $admin_id = $request->admin_id;
                $shipper_id = $shipment->user_id;
                $shipment_id = $shipment->id;
                $nature_id = $request->case_nature_id;
                $case_nature_type_id = $request->case_nature_type_id; // optional
                $description = $request->description;


                $channel_id = $request->channel_id;
                $launched_by = 0;
                if (!$request->case_nature_id) {
                    return response()->json(['status' => 1, 'message' => 'Case nature not selected!']);
                }

                if (!empty($shipment_id)) {
                    $shipment = Shipment::find($shipment_id);
                    if ($shipment) {
                        $is_shipment = CrmRequest::where('shipment_id', $shipment_id)->where('case_nature_id', $nature_id)->first();
                        if ($is_shipment) {
                            if ($is_shipment->case_nature_id != $nature_id) {
                                if ($request->hasFile('product_picture') && $request->hasFile('invoice_picture')) {
                                    CRMController::add($nature_id, $case_nature_type_id, $channel_id, 1, $admin_id, $launched_by, $shipment_id, $shipper_id, NULL, NULL, $request->product_cost, $request->file('product_picture'), $request->file('invoice_picture'));
                                } else {
                                    if ($shipment->shipper_status_id == 20 || $shipment->shipper_status_id == 1) {
                                        if (in_array($case_nature_type_id, [11, 13])) {
                                            $tracking_no = $shipment->tracking_number;
                                            return response()->json(['status' => 1, 'message' => 'Request for Change cannot be opened for the following Shipment at the Current Status! ' . $tracking_no]);
                                        } else {
                                            CRMController::add($nature_id, $case_nature_type_id, $channel_id, 1, $admin_id, $launched_by, $shipment_id, $shipper_id, NULL, $description);
                                        }
                                    } else {
                                        CRMController::add($nature_id, $case_nature_type_id, $channel_id, 1, $admin_id, $launched_by, $shipment_id, $shipper_id, NULL, $description);
                                    }
                                }
                            } else {
                                $tracking_no = $shipment->tracking_number;
                                return response()->json(['status' => 1, 'message' => 'Request/Complaint already lodged for the following Shipment! ' . $tracking_no]);
                            }
                        } else {

                            if ($request->hasFile('product_picture') && $request->hasFile('invoice_picture')) {
                                if ($nature_id == 4) {
                                    if ($case_nature_type_id == 21 || $case_nature_type_id == 22) {
                                        CRMController::add($nature_id, $case_nature_type_id, $channel_id, 1, $admin_id, $launched_by, $shipment_id, $shipper_id, NULL, $description, $request->product_cost, $request->file('product_picture'), $request->file('invoice_picture'), $request->file('damage_product_picture'), $request->file('product_packaging_picture'), $request->file('actual_product_picture'), $request->damage_claim_product_cost, $request->file('missing_product_picture'), $request->file('product_packaging_picture_content_short'), $request->file('actual_product_picture_content_short'), $request->claim_content_product_cost);
                                    } else {
                                        CRMController::add($nature_id, $case_nature_type_id, $channel_id, 1, $admin_id, $launched_by, $shipment_id, $shipper_id, NULL, $description, $request->product_cost, $request->file('product_picture'), $request->file('invoice_picture'), null, null, null, null, null, null, null, null);
                                    }
                                } else {
                                    CRMController::add($nature_id, $case_nature_type_id, $channel_id, 1, $admin_id, $launched_by, $shipment_id, $shipper_id, NULL, NULL, $request->product_cost, $request->file('product_picture'), $request->file('invoice_picture'));
                                }
                            } else {
                                if ($shipment->shipper_status_id == 20 || $shipment->shipper_status_id == 1) {
                                    if (in_array($case_nature_type_id, [11, 13])) {
                                        $tracking_no = $shipment->tracking_number;
                                        return response()->json(['status' => 1, 'message' => 'Request for Change cannot be opened for the following Shipment at the Current Status! ' . $tracking_no]);
                                    } else {
                                        CRMController::add($nature_id, $case_nature_type_id, $channel_id, 1, $admin_id, $launched_by, $shipment_id, $shipper_id, NULL, $description);
                                    }
                                } else {
                                    CRMController::add($nature_id, $case_nature_type_id, $channel_id, 1, $admin_id, $launched_by, $shipment_id, $shipper_id, NULL, $description);
                                }
                            }
                        }
                        if($nature_id == 2){
                            if($case_nature_type_id == 13){
                                $shipment->consignee_phone_number_2 = $alternate_phone;
                                $shipment->save();
                            }
                        }
                    }
                    if($nature_id == 1 && !empty($request->case_nature_complainant)  && !empty($request->complainant_phone)){
                        AdminCRMController::updateComplaintPhone(CrmRequest::max('id'), $request->case_nature_complainant, $request->complainant_phone);
                    }
                    return response()->json(['status' => 0, 'message' => 'Request(s) successfully added']);
                }
                return response()->json(['status' => 1, 'message' => 'Shipment not provided']);

            } else {
                return response()->json(['status' => 1, 'message' => "Invalid Tracking No."]);
            }

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
