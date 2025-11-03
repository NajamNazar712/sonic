<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Admins\AdminCRMController;
use App\Http\Controllers\CRM\CRMCommentController;
use App\Http\Controllers\CRM\CRMController;
use App\Http\Models\Admin\ChangeShipmentAmountLog;

use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\CRM\CrmComments;
use App\Http\Models\CRM\CrmRequest;
use App\Http\Models\CRM\CrmRequestCaseNature;
use App\Http\Models\CRM\CrmRequestCaseNatureType;
use App\Http\Models\CRM\CrmRequestChannel;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentsJourney;
use App\Http\Requests\AddCrmRequest;
use App\Http\Requests\ValidateShipmentIdRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use function foo\func;

class ShipperCrmApiController extends Controller
{
    public function add_crm_request(AddCrmRequest $request) {


        $nature_id = $request->case_nature_id;
        $complaint_id = $request->complaint_id;
        $app_type = $request->app_type; // if app_type=1 shipper else app_type=2 retail
        $description = $request->description;
        $user_id = $request->shipper_id;
        $launched_by_id =  $request->shipper_id;
//        if($app_type == 2) {
//            $setting = GlobalSettings::where('type', 'retail_store')->first();
//            $user_id = $setting->setting_value;
//        }
        $shipment_ids = explode(',', $request->input('shipment_ids'));
        $launched_by = $app_type == 1 ? 1 : 5; //if app_type=1 shipper-1 else app_type=2 retail-3
        $present_shipments = array();
        $not_found_shipments = array();

        $channel_id=1;
        if($app_type == 2) {
            $channel_id = $request->input('channel_id',2);
        }
        if(empty($channel_id)){
            $channel_id = 2;
        }

        if (!empty($shipment_ids)) {
            foreach ($shipment_ids as $shipment_id) {

                if ($app_type == 2) {
                    $user_id = GlobalSettings::where('type', 'retail_store')->value('setting_value') ?? 1126;
                    $launched_by_id = $request->retail_shipper_id;
                    $shipment = Shipment::whereHas('retail', function ($query) use ($user_id,$request,$launched_by_id) {
                        $query->where('shipper_account_no', $launched_by_id);
                    })->where('id',$shipment_id)->first();
                }else {
                    $shipment = Shipment::where('id',$shipment_id)->where('user_id',$user_id)->first();
                }
                if ($shipment) {

                    if ($complaint_id == 12 && in_array($shipment->shipper_status_id, [14, 18, 30, 36, 37, 20, 21, 22, 23, 24, 25, 26, 32, 44, 47, 48, 57, 60, 51])) // for cod change automation
                    {
                        return response()->json(['status' => 1,'message'=>'Request cannot be catered at this status of the shipment.']);
                    }

                    $is_shipment = CrmRequest::where('shipment_id', $shipment_id)->where('case_nature_id', $nature_id)->first();
                    $already_lodged = false;

                    if ($is_shipment) {
                        $already_lodged = true;
                        if ($is_shipment->case_nature_id == $nature_id) {
                            $present_shipments[] = $shipment->tracking_number;
                            return response()->json(['status' => 1,'message'=>'Request/Complaint already lodged.']);
                        }
                    }
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

                        if ($complaint_id == 21 && $app_type == 1) {
                            $product_packaging_picture = $request->file('product_packaging_picture');
                            $actual_product_picture = $request->file('actual_product_picture');
                            $damage_product_picture = $request->file('damage_product_picture');
                            $damage_claim_product_cost = $request->damage_claim_product_cost;

                        } elseif ($complaint_id == 22 && $app_type == 1) {
                            $product_packaging_picture_content_short =  $request->file('product_packaging_picture');
                            $actual_product_picture_content_short  =  $request->file('actual_product_picture');
                            $missing_product_picture = $request->file('missing_product_picture');
                            $claim_content_product_cost = $request->claim_content_product_cost;
                        }

                        $crm_request_padded_id = CRMController::add($nature_id, $complaint_id, $channel_id, 1, $launched_by_id, $launched_by, $shipment_id, $user_id, NULL, $description, $product_cost,$product_picture, $invoice_picture,$damage_product_picture, $product_packaging_picture,$actual_product_picture,$damage_claim_product_cost,$missing_product_picture,$product_packaging_picture_content_short, $actual_product_picture_content_short, $claim_content_product_cost);
                    }
                    else {
                        if ($shipment->shipper_status_id == 20 || $shipment->shipper_status_id == 1) {
                            if (in_array($complaint_id, [11, 13])) {
                                $present_shipments[] = $shipment->tracking_number;
                                return response()->json(['status' => 1,'message'=>'Request cannot be catered at this status of the shipment.']);
                            }
                        }

                        if ($complaint_id == 12 && $app_type == 1) {
                            if ($shipment->shipper_status_id == 5) {
                                $description = $description . " (change old amouunt $shipment->amount to new amount $request->cod_new_amount )";
                                $crm_request_padded_id = CRMController::add($nature_id, $complaint_id, $channel_id, 1, $launched_by_id, $launched_by, $shipment_id, $user_id, NULL, $description);

                            } else {
                                $crm_request_padded_id =  CRMController::add($nature_id, $complaint_id, $channel_id, 1, $launched_by_id, $launched_by, $shipment_id, $user_id, NULL, $description, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1);
                            }
                        } else {
                            $crm_request_padded_id = CRMController::add($nature_id, $complaint_id, $channel_id, 1, $launched_by_id, $launched_by, $shipment_id,$user_id, NULL, $description);
                        }
                    }
                    if ($nature_id == 2 && $app_type == 1) {
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
                else {
                    $not_found_shipments[]=$shipment_id;
                    return response()->json(['status' => 1,'message'=>'Shipment not found!']);

                }
            }

            if ($nature_id == 1 && isset($crm_request_padded_id) && $app_type == 1) {
                $case_nature_complainant =  $request->case_nature_complainant;
                $complainant_phone =  $request->complainant_phone;
                AdminCRMController::updateComplaintPhone($crm_request_padded_id,$case_nature_complainant, $complainant_phone);
            }



            return response()->json([
                'status' => 0,
                'message' => 'CRM requests have been added successfully.',
                'already_existed_shipments' => $present_shipments,
                'not_found_shipments' => $not_found_shipments,
                'crm_request_id' => $crm_request_padded_id
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
        $case_nature =  CrmRequestCaseNature::where('id','!=',3)->get();

        $complaints = CrmRequestCaseNatureType::where('status_id',1)->where('nature_id',1)->get();
        $service_requests = CrmRequestCaseNatureType::where('status_id',1)->where('nature_id',2)->get();
        $claims = CrmRequestCaseNatureType::where('status_id',1)->where('nature_id',4)->get();
        $complainants = [
            ['id' => 1, 'complainant_type' => 'Consignee'],
            ['id' => 2, 'complainant_type' => 'Shipper'],
        ];
        $channels = $request->app_type == 2
            ? CrmRequestChannel::where('id', '!=', 1)->get()
            : CrmRequestChannel::all();


        return response()->json(['status' => 0, 'case_nature' => $case_nature, 'channels' => $channels, 'complaints' => $complaints, 'service_requests' => $service_requests, 'claims' => $claims, 'complainants' => $complainants]);

    }

    public function crm_request_list(Request $request)
    {
        $tracking_number = $request->input('tracking_number');
        $app_type = $request->app_type;

        if ($request->filled('status') && $request->status != 1) {
            if ($request->status == 4) {
                $request_status = [4, 7];
            } else {
                return response()->json([
                    'status' => 1,
                    'message' => 'CRM Complaints following status not found!'
                ]);
            }
        } else {
            $request_status = [1, 2, 3, 5, 6];
        }

        $count = 0;
        if($app_type == 2) {

//            $launched_count = CrmRequest::leftJoin('retail_shipments as rs', 'rs.shipment_id', '=', 'crm_requests.shipment_id')
//            ->leftJoin('retail_shipper_infos as rsi', 'rsi.id', '=', 'rs.shipper_account_no')
//            ->whereIn('status_id',[1, 2, 3, 5, 6])
//            ->where('rs.shipper_account_no', $request->retail_shipper_id)
//            ->count();
//
////            $in_process = CrmRequest::leftJoin('retail_shipments as rs', 'rs.shipment_id', '=', 'crm_requests.shipment_id')
////            ->leftJoin('retail_shipper_infos as rsi', 'rsi.id', '=', 'rs.shipper_account_no')
////            ->where('status_id',2)
////            ->where('rs.shipper_account_no', $request->retail_shipper_id)
////            ->count();
////
//            $closed_count = CrmRequest::leftJoin('retail_shipments as rs', 'rs.shipment_id', '=', 'crm_requests.shipment_id')
//            ->leftJoin('retail_shipper_infos as rsi', 'rsi.id', '=', 'rs.shipper_account_no')
//            ->whereIn('status_id',[4, 7])
//            ->where('rs.shipper_account_no', $request->retail_shipper_id)
//            ->count();
            $baseQuery = CrmRequest::leftJoin('retail_shipments as rs', 'rs.shipment_id', '=', 'crm_requests.shipment_id')
                ->leftJoin('retail_shipper_infos as rsi', 'rsi.id', '=', 'rs.shipper_account_no')
                ->where('rs.shipper_account_no', $request->retail_shipper_id);

            // Launched count
            $launched_count = (clone $baseQuery)
                ->whereIn('status_id', [1, 2, 3, 5, 6])
                ->count();

            // Closed count
            $closed_count = (clone $baseQuery)
                ->whereIn('status_id', [4, 7])
                ->count();

        } else {
//            $launched_count = CrmRequest::whereIn('status_id',[1, 2, 3, 5, 6])
//            ->where('shipper_id', $request->shipper_id)
//            ->count();
//
////            $in_process = CrmRequest::where('status_id',2)
////                ->where('shipper_id', $request->shipper_id)
////                ->count();
////
//            $closed_count = CrmRequest::whereIn('status_id',[4, 7])
//                ->where('shipper_id', $request->shipper_id)
//                ->count();
            $baseQuery = CrmRequest::where('shipper_id', $request->shipper_id);

            // Launched count
            $launched_count = (clone $baseQuery)
                ->whereIn('status_id', [1, 2, 3, 5, 6])
                ->count();

            // Closed count
            $closed_count = (clone $baseQuery)
                ->whereIn('status_id', [4, 7])
                ->count();
        }

//        $request_count = [
//            'launched' => $launched,
//            'in_process' => $in_process,
//            'closed' => $closed
//        ];
        
        $selects = [
            'crm_requests.id as id',
            's.tracking_number as tracking_number', 's.id as shipment_id',
            'crcn.name as case_nature',
            'crcnt.type as case_nature_type',
            'crc.channel as channel',
            'crs.name as request_status',
            'ad.name as agent',
            'a.name as name',
            'crm_requests.launched_by as launched_added_by',
            'crm_requests.created_at as created_at',
            'crm_requests.description as description',
            'crm_requests.status_id',
            'ss.name as shipment_status',
            'crmst.created_at as closed_at',
            'crm_requests.launched_by_id'
        ];

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
            });

            // Conditional joins and filters
            if ($app_type == 2) {
                $selects[] = 'rsi.shipper_name';
                $crm_requests = $crm_requests
                    ->leftJoin('retail_shipments as rs', 'rs.shipment_id', '=', 'crm_requests.shipment_id')
                    ->leftJoin('retail_shipper_infos as rsi', 'rsi.id', '=', 'rs.shipper_account_no')
                    ->where('rs.shipper_account_no', $request->retail_shipper_id);
            } else {
                $selects[]='u.name as shipper_name';
                $crm_requests = $crm_requests
                    ->leftJoin('users as u', 'u.id', '=', 'crm_requests.shipper_id')
                    ->where('crm_requests.shipper_id', $request->shipper_id);
            }

        // Final query
        $crm_requests = $crm_requests->select($selects)
            ->whereIn('crm_requests.status_id',$request_status)
            ->whereBetween('crm_requests.created_at', [Carbon::now()->subMonths(12)->startOfMonth(), Carbon::now()->endOfDay()]);

            if($tracking_number) {
                $crm_requests = $crm_requests->where('s.tracking_number', $tracking_number)->get();
            } else {

                $crm_requests = $crm_requests->orderBy('crm_requests.id', 'desc')
                    ->cursorPaginate(20);
            }

        if($crm_requests->isNotEmpty()) {
            return response()->json(['status' => 0 , 'message' => 'Success' ,'crm_requests'=>$crm_requests, 'launched_count' => $launched_count,'closed_count' => $closed_count]);
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

    public function get_receving_sheet(ValidateShipmentIdRequest $request)
    {

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
    public function crm_comment_add(Request $request)
    {
        
        $validate = Validator::make($request->all(), [
            'messages' => ['required', 'array', 'min:1'],
            'messages.*.crm_request_id' => ['required', 'integer', 'digits_between:1,10', 'exists:crm_requests,id'],
            'messages.*.comment' => ['required', 'between:0,190'],
        ]);
       
        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $rider_id =  $request->shipper_id;

            foreach ($request->messages as $message) {
                $commented_at = Carbon::createFromTimestampMs($message['commented_at'])->toDateTimeString();
                $comment = new CrmComments();
                $comment->crm_request_id = $message['crm_request_id'];
                $comment->comment_by_id = $rider_id;
                $comment->comment_by = 1;
                $comment->comment_type = 1;
                $comment->comment = $message['comment'];
                $comment->created_at = date('Y-m-d h:i:s');
                $comment->updated_at = date('Y-m-d h:i:s');
                $comment->save();
            }

            return response()->json(['status' => 0, 'message' => 'Comment(s) Added Successfully']);
        }
    }

    public function request_details(Request $request, $id)
    {
        $crm_request = CrmRequest::find($id);
        $details = [];
        if ($crm_request) {
           
          $comments = CrmComments::where('crm_request_id', $crm_request->id)
          ->orderBy('created_at','desc')
          ->get();
          foreach($comments as $comment){
                $details[] = [
                    'crm_message' =>  strip_tags($comment->comment),
                    'date_time' =>  Carbon::parse($comment->created_at)->format('Y-m-d h:i:s'),
                ];
          }
            return response()->json(['status' => 0, 'message' => 'Success', 'crm_request' => $details]);
        } else {
            return redirect()->back()->with('danger', 'CRM Request Not found!');
        }
    }
}
