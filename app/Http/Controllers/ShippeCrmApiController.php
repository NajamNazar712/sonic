<?php

namespace App\Http\Controllers;

use App\Http\Controllers\CRM\CRMController;
use App\Http\Models\CRM\CrmRequest;
use App\Http\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use function foo\func;

class ShippeCrmApiController extends Controller
{

    public function add_crm_request(Request $request)
    {
        $baseRules = [
            'case_nature_id' => 'required|integer',
            'shipment_id' => 'required|integer',
        ];

        $additionalRules = [];

        if ($request->case_nature_id == 3) {
            // Feedback case
            $additionalRules['description'] = 'required|string';
        } else {
            // Other Requests
            $additionalRules['complaint_id'] = 'required|integer';

            if ($request->hasFile('product_picture') || $request->hasFile('invoice_picture')) {
                $additionalRules['product_picture'] = 'required|file|mimes:jpg,jpeg,png';
                $additionalRules['invoice_picture'] = 'required|file|mimes:jpg,jpeg,png';
                $additionalRules['product_cost'] = 'required|numeric';

                // Extra checks if it's damage/missing complaint (complaint_id 21 or 22)
                if ($request->case_nature_id == 4 && in_array($request->complaint_id, [21, 22])) {
                    $additionalRules = array_merge($additionalRules, [
                        'damage_product_picture' => 'required|file|mimes:jpg,jpeg,png',
                        'product_packaging_picture' => 'required|file|mimes:jpg,jpeg,png',
                        'actual_product_picture' => 'required|file|mimes:jpg,jpeg,png',
                        'damage_claim_product_cost' => 'required|numeric',
                        'missing_product_picture' => 'required|file|mimes:jpg,jpeg,png',
                        'product_packaging_picture_content_short' => 'required|file|mimes:jpg,jpeg,png',
                        'actual_product_picture_content_short' => 'required|file|mimes:jpg,jpeg,png',
                        'claim_content_product_cost' => 'required|numeric',
                    ]);
                }
            } else {
                // If no files, then at least a description should be present
                $additionalRules['description'] = 'required|string';
            }
        }

        $validator = Validator::make($request->all(), array_merge($baseRules, $additionalRules));

        if ($validator->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validator->errors()]);
        }

        $shipper_id = $request->shipper_id;
        $nature_id = $request->case_nature_id;
        $complaint_id = $request->complaint_id;
        $shipment_id = $request->shipment_id;
        $receiving_sheet_id = $request->receiving_sheet_id;
        $description = $request->description;
        $channel_id = 7;
        $launched_by = 1;
        if (!$request->case_nature_id) {
            return response()->json(['status' => 1, 'message' => 'Case nature not selected!']);
        }
        //feedback
        if ($nature_id == 3) {
            if ($shipment_id != null) {
                if ($description == null) {
                    return response()->json(['status' => 1, 'message' => 'Description Not Entered!']);
                }
                $shipment = Shipment::find($shipment_id);
                if ($shipment) {
                    $is_shipment = CrmRequest::where('shipment_id', $shipment_id)->where('case_nature_id', $nature_id)->first();
                    if (!$is_shipment) {
                        CRMController::add($nature_id, NULL, $channel_id, 1, $shipper_id, $launched_by, $shipment_id, $shipper_id, NULL, $description);
                        return response()->json(['status' => 0, 'message' => 'Feedback successfully added']);
                    } else {
                        $tracking_no = $shipment->tracking_number;
                        return response()->json(['status' => 1, 'message' => 'Feedback already entered for the following Shipment! ' . $tracking_no]);
                    }
                }
                return response()->json(['status' => 1, 'message' => 'Shipment not Found!']);
            }
            return response()->json(['status' => 1, 'message' => 'Shipment not provided']);
        } //Other Requests
        else {
            if (!empty($shipment_id)) {
                $shipment = Shipment::find($shipment_id);
                if ($shipment) {
                    $is_shipment = CrmRequest::where('shipment_id', $shipment_id)->where('case_nature_id', $nature_id)->first();
                    if ($is_shipment) {
                        if ($is_shipment->case_nature_id != $nature_id) {
                            if ($request->hasFile('product_picture') && $request->hasFile('invoice_picture')) {
                                CRMController::add($nature_id, $complaint_id, $channel_id, 1, $shipper_id, $launched_by, $shipment_id, $shipper_id, NULL, NULL, $request->product_cost, $request->file('product_picture'), $request->file('invoice_picture'));
                            } else {
                                if ($shipment->shipper_status_id == 20 || $shipment->shipper_status_id == 1) {
                                    if (in_array($complaint_id, [11, 13])) {
                                        $tracking_no = $shipment->tracking_number;
                                        return response()->json(['status' => 1, 'message' => 'Request for Change cannot be opened for the following Shipment at the Current Status! ' . $tracking_no]);
                                    } else {
                                        CRMController::add($nature_id, $complaint_id, $channel_id, 1, $shipper_id, $launched_by, $shipment_id, $shipper_id, NULL, $description);
                                    }
                                } else {
                                    CRMController::add($nature_id, $complaint_id, $channel_id, 1, $shipper_id, $launched_by, $shipment_id, $shipper_id, NULL, $description);
                                }
                            }
                        } else {
                            $tracking_no = $shipment->tracking_number;
                            return response()->json(['status' => 1, 'message' => 'Request/Complaint already lodged for the following Shipment! ' . $tracking_no]);
                        }
                    } else {

                        if ($request->hasFile('product_picture') && $request->hasFile('invoice_picture')) {
                            if ($nature_id == 4) {
                                if ($complaint_id == 21 || $complaint_id == 22) {
                                    CRMController::add($nature_id, $complaint_id, $channel_id, 1, $shipper_id, $launched_by, $shipment_id, $shipper_id, NULL, $description, $request->product_cost, $request->file('product_picture'), $request->file('invoice_picture'), $request->file('damage_product_picture'), $request->file('product_packaging_picture'), $request->file('actual_product_picture'), $request->damage_claim_product_cost, $request->file('missing_product_picture'), $request->file('product_packaging_picture_content_short'), $request->file('actual_product_picture_content_short'), $request->claim_content_product_cost);
                                } else {
                                    CRMController::add($nature_id, $complaint_id, $channel_id, 1, $shipper_id, $launched_by, $shipment_id, $shipper_id, NULL, $description, $request->product_cost, $request->file('product_picture'), $request->file('invoice_picture'), null, null, null, null, null, null, null, null);
                                }
                            } else {
                                CRMController::add($nature_id, $complaint_id, $channel_id, 1, $shipper_id, $launched_by, $shipment_id, $shipper_id, NULL, NULL, $request->product_cost, $request->file('product_picture'), $request->file('invoice_picture'));
                            }
                        } else {
                            if ($shipment->shipper_status_id == 20 || $shipment->shipper_status_id == 1) {
                                if (in_array($complaint_id, [11, 13])) {
                                    $tracking_no = $shipment->tracking_number;
                                    return response()->json(['status' => 1, 'message' => 'Request for Change cannot be opened for the following Shipment at the Current Status! ' . $tracking_no]);
                                } else {
                                    CRMController::add($nature_id, $complaint_id, $channel_id, 1, $shipper_id, $launched_by, $shipment_id, $shipper_id, NULL, $description);
                                }
                            } else {
                                CRMController::add($nature_id, $complaint_id, $channel_id, 1, $shipper_id, $launched_by, $shipment_id, $shipper_id, NULL, $description);
                            }
                        }
                    }
                }
                return response()->json(['status' => 0, 'message' => 'Request(s) successfully added']);
            }
            return response()->json(['status' => 1, 'message' => 'Shipment not provided']);
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

    public function crm_request_list(Request $request)
    {
        $perPage = request()->get('per_page', 50);

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
            ->paginate($perPage);



        return response()->json(['status' => 0 , 'message' => 'Success' ,'crm_requests'=>$crm_requests]);

    }
    public function single_crm_request(Request $request)
    {

        $validate = Validator::make($request->all(), [
            'tracking_number' => ['required', 'integer', 'digits_between:10,20'],
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

}
