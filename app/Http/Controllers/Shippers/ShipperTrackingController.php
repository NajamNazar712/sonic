<?php

namespace App\Http\Controllers\Shippers;

use App\Models\ShipmentGeoCode;
use Auth;
use Carbon\Carbon;
use Cassandra\Session;
use App\Http\Models\Rider;
use App\RvAgentCallHistory;
use Illuminate\Http\Request;
use App\Http\Models\Shipment;
use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Datatables;
use App\Http\Models\Shipper\User;
use App\Http\Models\RiderDelivery;
use App\Http\Models\CRM\CrmRequest;
use App\Http\Controllers\Controller;
use App\Http\Models\Admin\ReturnNote;
use App\Http\Models\CargoConsignment;
use App\Http\Models\Admin\StatusRemark;
use Illuminate\Support\Facades\Storage;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\InternationalShipment;
use App\Http\Models\Shipper\SubstituteUser;
use App\Http\Models\SubstituteUserShipment;
use App\Http\Models\CRM\CrmRequestCaseNature;
use App\Http\Models\CRM\CrmRequestCaseNatureType;
use App\Http\Models\Shipper\ReturnSheetShipments;

use App\Http\Models\Admin\DeliveryLocationMapping;
use App\Http\Models\Admin\ShipementReceiveDetails;
use App\Http\Models\ShipmentReplacementParcelImage;
use App\Http\Models\Admin\DeliveryLocationMappingKeyword;
use App\Http\Controllers\ShipmentScanningJourneyController;
use App\Http\Models\Sister_account\MergedSisterAccountMapping;
use DB;
use App\Http\Models\CrmCaseNatureRemark;
use App\Http\Models\InterceptReBookRequestHistory;
use App\Http\Traits\CommonTrait;

class ShipperTrackingController extends Controller
{
    use CommonTrait;
    public function __construct() {
      $this->middleware('auth:web,substitute_users');

      $this->middleware('Permission');
    }

    public function index() {
        $permission = session('permissions');
        $case_nature = CrmRequestCaseNature::where('id','!=',3)->get();
        $row = array();
        if(session('user_type') !== 1){
            foreach($case_nature as $nature) {
                if (in_array(16,$permission) && ($nature->id == 1)) {
                    $row[] = $nature;
                }

                elseif (in_array(17,$permission) && ($nature->id == 2)) {
                    $row[] = $nature;
                }

                elseif (in_array(18,$permission) && ($nature->id == 3 || $nature->id == 4)) {
                    $row[] = $nature;
                }

            }
            $case_nature = $row;
        }
        $case_nature_type_complaints = CrmRequestCaseNatureType::where('nature_id', '=', 1)->where('status_id',1)->where('shipper_visibility', 1)->get();
        $case_nature_type_service_requests = CrmRequestCaseNatureType::where('nature_id', '=', 2)->where('status_id',1)->where('shipper_visibility', 1)->get();
        $case_nature_type_claims = CrmRequestCaseNatureType::where('nature_id', '=', 4)->where('status_id',1)->where('shipper_visibility', 1)->get();
        return view('client.tracking')->with([ 
            'case_nature' => $case_nature, 
            'case_nature_complaints' => $case_nature_type_complaints, 
            'case_nature_service_requests' => $case_nature_type_service_requests, 
            'case_nature_type_claims' => $case_nature_type_claims,
            'case_permission'=>$permission,
        ]);
    }

    public function shipper_visibility(Request $request)
    {
        $nature_id = $request->input('id');
        $shipment_id = $request->input('shipment_id');
        //dd($shipment_id);
        $shipment_status = Shipment::where('id', $shipment_id)->pluck('shipper_status_id')->first();
        $case_nature_types = CrmRequestCaseNatureType::where('nature_id', '=', $nature_id)
            ->where('status_id', 1)
            ->where('shipper_visibility', 1)
            ->get()->filter(function ($case_nature_types) use ($shipment_status) {
                $status = json_decode($case_nature_types->shipment_status, true);
                if (is_null($status)) {
                    return false;
                }
                // Check if the dept_id is in the admin_departments array
                return in_array($shipment_status, $status);
            });
            //dd($case_nature_types);
        return response()->json([
            'case_nature_types' => $case_nature_types,
        ]);
    }
    

    public function case_nature_remarks(Request $request)
    {
        $complaintId = $request->input('complaint_id');
        $case_nature = CrmRequestCaseNatureType::where('id', $complaintId)->first();
        $remarks_visibility = $case_nature->remarks_visibility;
        $case_nature_remarks = CrmCaseNatureRemark::where('case_nature_id', $complaintId)->get();
        return response()->json([
            'data' => $case_nature_remarks,
            'remarks_visibility' => $remarks_visibility
        ]);
    }

    public function case_nature_service_remarks(Request $request)
    {
        $serviceId = $request->input('service_id');
        $case_nature = CrmRequestCaseNatureType::where('id', $serviceId)->first();
        $remarks_visibility = $case_nature->remarks_visibility;
        $case_nature_service_remarks = CrmCaseNatureRemark::where('case_nature_id', $serviceId)->get();
        return response()->json([
            'data' => $case_nature_service_remarks,
            'remarks_visibility' => $remarks_visibility
        ]);
    }

    public function case_nature_claim_remarks(Request $request)
    {
        $claimId = $request->input('claim_id');
        $case_nature = CrmRequestCaseNatureType::where('id', $claimId)->first();
        $remarks_visibility = $case_nature->remarks_visibility;
        $case_nature_claim_remarks = CrmCaseNatureRemark::where('case_nature_id', $claimId)->get();
        return response()->json([
            'data' => $case_nature_claim_remarks,
            'remarks_visibility' => $remarks_visibility
        ]);
    }

    public function track(Request $request) {
    	$tracking_numbers = explode(',', $request->tracking_numbers);

    	$tracking = array();
        $globalSetting = GlobalSettings::where(['setting_value' => 1, 'type' => 'specific_shipper_rider_view'])->first();
        $riderDetailView = $globalSetting ? explode(',', $globalSetting->text) : null;
        foreach ($tracking_numbers as $tracking_number) {
//    		$shipment = Shipment::where('tracking_number', $tracking_number);

            $shipment = Shipment::where('tracking_number', $tracking_number);
    		if ($shipment->exists()) {
                $shipment = $shipment->first();

                $sub_segment_name = '-';
                $sub_segment = DB::table('shipper_segment_logs')
                ->leftJoin('sub_category_segments', 'sub_category_segments.id', 'shipper_segment_logs.sub_segment_id')
                ->where('shipment_id', $shipment->id)
                ->select('sub_category_segments.name')
                ->first();

                if ($sub_segment && $sub_segment->name)
                {
                    $sub_segment_name = $sub_segment->name;
                }

                $sub_shipment = true;
                if(session('user_type') == 2){
                    if(session('restriction') == 1){
                        $sub_check = SubstituteUserShipment::where('substitute_user_id', Auth::id())->where('shipment_id', $shipment->id);
                        if(!$sub_check->exists()){
                            $sub_shipment = false;
                        }
                    }
                }
                if($sub_shipment == true){
                    $track_check = false;
                    if(count(session('sister_users')) > 0){
                        foreach (session('sister_users') as $user_id){
                            if($user_id == $shipment->user_id){
                                $track_check = true;
                            }

                            if (session('user_id') == $shipment->user_id) {
                                $track_check = true;
                            }
                        }
                    }else{
                        if (session('user_id') == $shipment->user_id) {
                            $track_check = true;
                        }
                    }


                    if ($track_check == true) {
                        $details = array();

                        if($shipment->business_category_id == 2){
                            $international_shipment = InternationalShipment::where('shipment_id', $shipment->id)->whereNotNull('international_tracking_number');
                            if($international_shipment->exists()){
                                $international_shipment = $international_shipment->first();
                                $details['international_shipment'] = 1;
                                $details['international_tracking_number'] = $international_shipment->international_tracking_number;
                            }
                            else{
                                $details['international_shipment'] = 0;
                            }
                        }
                        else{
                            $details['international_shipment'] = 0;
                        }
                        $details['tracking_number'] = $tracking_number;
                        if($shipment->pod_image()->exists()){
                            $details['pod_file'] = asset('uploads/pod_images/' . $shipment->pod_image->pod_file);
                          
                        }
                        $received_shipments = ReturnSheetShipments::where('shipment_id',$shipment->id);
                        if($received_shipments->exists()){
                        $details['received_img'] =  '<img src="' . asset('img/shipement_received.png').' ">';
                        }else{
                            $details['received_img'] ="";
                        }

                        $shipper = $shipment->user;

                        $allowed_user_id = GlobalSettings::where('setting_value', 0)->where('type', 'brand_and_vendor_rights')->pluck('text');
                        $details['shipper']['id'] = $shipper->id;
                        $details['shipper']['assigned'] = $allowed_user_id;
                        $details['shipper']['name'] = $shipper->name;
                        $details['shipper']['account_number'] = str_pad($shipper->id, 6, '0', STR_PAD_LEFT);
                        $details['shipper']['city'] = $shipper->city->name;
                        $details['shipper']['phone_number_1'] = $shipper->phone;
                        $details['shipper']['phone_number_2'] = $shipper->phone2;
                        $details['shipper']['email'] = $shipper->email;

                        $pickup = $shipment->pickup_address;

                        $details['pickup']['person_of_contact'] = $pickup->poc;
                        $details['pickup']['vendor'] = $pickup->vendor;
                        $details['pickup']['pickup_brand_name'] = $pickup->pickup_brand_name;
                        $details['pickup']['phone_number'] = $pickup->phone;
                        $details['pickup']['email'] = $pickup->email;
                        $details['pickup']['origin'] = $pickup->city->name;
                        $details['pickup']['address'] = $pickup->pickup_address;

                        $details['consignee']['name'] = $shipment->consignee_name;
                        $details['consignee']['phone_number_1'] = $shipment->consignee_phone_number_1;
                        $details['consignee']['phone_number_2'] = $shipment->consignee_phone_number_2;
                        $details['consignee']['destination'] = $shipment->consignee_city->name;

                        $consignee_address = InterceptReBookRequestHistory::where('shipment_id', $shipment->id)
                        ->select([
                            'new_consignee_address',
                        ])
                        ->first();

                        if ($consignee_address){
                            $details['consignee']['address'] = $consignee_address->new_consignee_address;
                        } else {
                            $details['consignee']['address'] = $shipment->consignee_address;
                        }
                        // $details['consignee']['address'] = $shipment->consignee_address;

                        $details['consignee']['email'] = $shipment->consignee_email;
                        $details['consignee']['crm_status'] = 0;

                        $on_hold_sc = CrmRequest::where('shipment_id',$shipment->id)->where('case_nature_type_id',20);
                        if($on_hold_sc->exists()){
                            $details['consignee']['crm_status'] = 1;
                        }

                        $check = DeliveryLocationMappingKeyword::pluck('keyword')->toArray();

                        $msg_string = null;
                        $str_arr = null;
                        $str_arr = preg_split('/[\s.,-,_,*,?,<,>,!,@,#,$,%,^,&,(,)]+/', $shipment->consignee_address);
                        // $str_arr = preg_split("/[ ,]+/", $shipment->consignee_address);
                        foreach ($check as $nsa) {
                            foreach ($str_arr as $arr_value) {
                                if (strtolower($nsa) == strtolower($arr_value)) {
                                    $con_nsa = $arr_value;
                                    
                                    // if ($msg_string != null) {
                                    //     $msg_string = $msg_string . ', ' . $arr_value;
                                    // } else {
                                        $msg_string = $arr_value;
                                    // }
                                }
                            }
                        }

                        
                        $delivery_area = null;
                        if($msg_string != null){
                            $found = DeliveryLocationMappingKeyword::join('delivery_location_mappings as dlm','delivery_location_mapping_keywords.mapping_id','=','dlm.id')
                                        ->select('dlm.area_name as area_name','dlm.id')
                                        ->where('delivery_location_mapping_keywords.keyword',$msg_string)
                                        ->where('dlm.city_id',$shipment->consignee_city_id);
                            if($found->exists()){
                                $found = $found->first();
                                $delivery_area = $found->area_name;
                            }
                        }
                        $details['consignee']['delivery_area'] = $delivery_area;


                        foreach ($shipment->items as $item) {
                            $item_details = array();

                            $item_details['product_type'] = $item->product->product_name;
                            $item_details['description'] = $item->description;
                            $item_details['quantity'] = $item->quantity;

                            $details['order_information']['items'][] = $item_details;
                        }

                        $details['order_information']['order_id'] = $shipment->order_id;
                        if($shipment->order_date){
                            $details['order_information']['order_date'] = $shipment->order_date->order_date;
                        }
                        else{
                            $details['order_information']['order_date'] = NULL;
                        }

                        $details['order_information']['weight'] = ($shipment->actual_weight) ? floatval($shipment->actual_weight) : floatval($shipment->estimated_weight);
                        $details['order_information']['shipping_mode'] = $shipment->shipping_mode->mode;

                        $details['order_information']['booking_type_id'] = $shipment->booking_type_id;

                        if ($shipment->booking_type_id != 4) {
                            $details['order_information']['amount'] = number_format($shipment->amount);
                        }
                        else {
                            if ($shipment->charges_mode_id == 1) {
                                $details['order_information']['amount'] = 0;
                            }
                            else {
                                $details['order_information']['amount'] = $shipment->amount;
                            }
                        }

                        $details['order_information']['account_type_id'] = $shipment->user->account_type_id;

                        $details['order_information']['charges_mode_id'] = $shipment->charges_mode_id;

                        if ($shipment->charges_mode_id) {
                            $details['order_information']['charges_mode'] = $shipment->charges_mode->charges_mode;
                        }

                        $details['order_information']['instructions'] = $shipment->special_instructions;
                        $details['order_information']['pieces'] = $shipment->pieces;
                        $details['order_information']['business_category'] = $shipment->business_category->name;

                        $details['order_information']['sub_segment'] = $sub_segment_name;
                        $details['order_information']['channel'] = optional(optional($shipment->bookingChannel)->channel)->name ?? '-';

                        foreach ($shipment->shipment_journey as $journey) {
                            if($journey->shipper_status_id != '67'){
                                if ($journey->verification) {
                                    $journey_details = array();

                                    $journey_details['date_time'] = $journey->created_at->toDateTimeString();
                                    if($journey->shipper_status_id == '68'){
                                        $journey_details['status'] = "Shipment - Misrouted";
                                    }else{
                                        $journey_details['status'] = $journey->shipment_status_shipper->name;
                                    }
                                    if(in_array($journey->shipper_status_id, [1])){
                                        if($shipment->booked_by == 1){
                                            $journey_details['status'] .= ' (Main User)';
                                        }
                                        else if($shipment->booked_by == 2){
                                            if($journey->reference_1_id != NULL){
                                                $sub_user = SubstituteUser::find($journey->reference_1_id);
                                                $journey_details['status'] .= ' (' . $sub_user->name . ' - Substitute User)';
                                            }
                                            else{
                                                $journey_details['status'] .= ' (Substitute User)';
                                            }
                                        }
                                    }
                                    if(in_array($journey->shipper_status_id, [52])){
                                        if($journey->reference_1_id != NULL){
                                            $sub_user = SubstituteUser::find($journey->reference_1_id);
                                            if($sub_user){
                                                $journey_details['status'] .= ' (' . $sub_user->name . ' - Substitute User)';
                                            }else{
                                                if($journey->user_id != null){
                                                    $journey_details['status'] .= ' (' . User::find($journey->user_id)->name . ' - Main User)';
                                                }
                                            }
                                        }
                                        else{
                                            if($journey->user_id != null){
                                                $journey_details['status'] .= ' (' . User::find($journey->user_id)->name . ' - Main User)';
                                            }else{
                                                $journey_details['status'] .= ' (Substitute User)';
                                            }
                                        }
                                    }

                                    if($journey->shipper_status_id == 25 && $journey->reference_1_id){
                                        $return_note = ReturnNote::find($journey->reference_1_id);
                                        if($return_note && $return_note->actual_date != null){
                                            $journey_details['status'] .= ' | ' . Carbon::parse($return_note->actual_date)->toDateString();
                                        }
                                    }

                                    if(in_array($journey->shipper_status_id, [1])){
                                        $replacement_image = ShipmentReplacementParcelImage::where('shipment_id',$journey->shipment_id);
                                        if($replacement_image->exists()){
                                            $replacement_image = $replacement_image->first();
                                            $journey_details['status'] .= '  <button class="btn btn-sm btn-outline-info align-middle replacement_booked_image" data-link="' . asset(Storage::url($replacement_image->picture_path)).'" data-id="' . $journey->shipment_id . '"><i class=><i class="la la-lg la-image"></i></button>';
                                        }
                                    }
                                    if(in_array($journey->shipper_status_id, [30])){
                                        $replacement_image2 = RiderDelivery::where('shipment_id',$journey->shipment_id)->where('rider_status_id',14);
                                        if($replacement_image2->exists()){
                                            $replacement_image2 = $replacement_image2->first();
                                            if($replacement_image2->replacement_image != null){

                                                $journey_details['status'] .= '  <button class="btn btn-sm btn-outline-info align-middle replacement_collected_image" data-link="' . asset(Storage::url($replacement_image2->replacement_image)).'" data-id="' . $journey->shipment_id . '"><i class=><i class="la la-lg la-image"></i></button>';
                                            }

                                        }
                                    }
                                    if ($riderDetailView && $journey->reference_2_id && in_array(Auth::id(),$riderDetailView)) {
                                        if (in_array($journey->shipper_status_id, [5, 23, 28, 34])) {
                                            $rider = Rider::find($journey->reference_2_id);
                                            if ($rider) {
                                                $journey_details['status'] .= ' | <button class="btn btn-sm btn-outline-info align-middle rider_information" data-id="' . $rider->id . '">' . $rider->name . '</button>';
                                            }
                                        } else {
                                            $journey_details['status'] .= ' | ' . str_pad($journey->reference_2_id, 6, '0', STR_PAD_LEFT);
                                        }
                                    }
                                    $journey_details['status_reason'] = ($journey->status_reason_id) ? $journey->shipment_status_reason->name : NULL;

                                    if(in_array($journey->shipper_status_id, [8,17,20,52,54,12,66])){
                                        //12 and 66 status added for remarks (RVR and Requested Call Reattempt).
                                        $journey_details['status_remarks'] = ($journey->remarks) ? $journey->remarks : '';
                                    }else{
                                        $journey_details['status_remarks'] = '';
                                    }
                                    $received_or_refused_by = '';

                                    $shippers = GlobalSettings::where('type','mms_setting')->select('text')->first();
                                    $shippers = explode(',', $shippers->text);
                                    $special_dashboard_shippers = User::whereIn('id', $shippers)->pluck('id')->toArray();

                                    if(in_array($shipment->user_id,$special_dashboard_shippers))
                                    {
                                        $receiver_details = ShipementReceiveDetails::where('tracking_number',$shipment->tracking_number)->first();
                                        if($receiver_details)
                                        {
                                            if($journey->received_or_refused_by){
                                                $received_or_refused_by = $receiver_details->receiver_name;
                                            }
                                            if($journey->cnic){
                                                $received_or_refused_by .= "|".$receiver_details->receiver_cnic;
                                            }
                                            if($journey->relation){
                                                $received_or_refused_by .= "|".$receiver_details->receiver_relationship;
                                            }
                                        }
                                        else{
                                            if($journey->received_or_refused_by){
                                                $received_or_refused_by = $journey->received_or_refused_by;
                                            }
                                            if($journey->cnic){
                                                $received_or_refused_by .= "|".$journey->cnic;
                                            }
                                            if($journey->relation){
                                                $received_or_refused_by .= "|".$journey->relation;
                                            }
                                        }
                                    }
                                    else{
                                        if($journey->received_or_refused_by){
                                            $received_or_refused_by = $journey->received_or_refused_by;
                                        }
                                        if($journey->cnic){
                                            $received_or_refused_by .= "|".$journey->cnic;
                                        }
                                        if($journey->relation){
                                            $received_or_refused_by .= "|".$journey->relation;
                                        }
                                    }
                                    $journey_details['received_or_refused_by'] = $received_or_refused_by;

                                    $details['tracking_history'][] = $journey_details;
                                }
                            }
                        }

                        $shipment_payment_journey = $shipment->shipment_payment_journey;

                        if ($shipment_payment_journey) {
                            foreach ($shipment_payment_journey as $journey) {
                                $journey_details = array();

                                $journey_details['date_time'] = Carbon::parse($journey->created_at)->toDateTimeString();
                                $journey_details['status'] = $journey->status->name;
                                $journey_details['user'] = $journey->admin->name;
                                $journey_details['payable_remarks'] = ($journey->payable_remarks) ? $journey->payable_remarks : '';

                                $details['payment_history'][] = $journey_details;
                            }
                        }

                        $shipment_pickup_journey = $shipment->shipments_v2_pickup_journeys;

                        if ($shipment_pickup_journey) {
                            foreach ($shipment_pickup_journey as $journey) {
                                $journey_details = array();

                                $journey_details['date_time'] = Carbon::parse($journey->created_at)->toDateTimeString();
                                $journey_details['status'] = $journey->status->name;
                                if($journey->reason_id != NULL){
                                    $journey_details['reason'] = $journey->reason->name;
                                }
                                else{
                                    $journey_details['reason'] = '';
                                }

                                if ($journey->reference_1_id) {
                                    $journey_details['status'] .= ' (' . str_pad($journey->reference_1_id, 6, '0', STR_PAD_LEFT);

                                    if ($journey->reference_2_id) {
                                        if ($journey->status_id == 2) {
                                            $rider = Rider::find($journey->reference_2_id);
                                            if($rider){
                                                $journey_details['status'] .= $rider->name;
                                            }

                                        }
                                        else {
                                            $journey_details['status'] .= ' | ' . str_pad($journey->reference_2_id, 6, '0', STR_PAD_LEFT);
                                        }
                                    }

                                    $journey_details['status'] .= ')';
                                }

                                $admin = $journey->admin;

                                if ($admin) {
                                    $journey_details['user'] = $admin->name;
                                }
                                else {
                                    $journey_details['user'] = '';
                                }

                                $details['pickup_history_v2'][] = $journey_details;
                            }
                        }

                         //shipment_pickup_journey_v3 get direct table for temporary untile use both pms use v2 and v3

                        $shipment_pickup_journey_v3 = DB::table('shipments_v3_pickup_journeys')->where('shipment_id',$shipment->id);

                        if ($shipment_pickup_journey_v3->exists()) {

                            $shipment_pickup_journey_v3 = $shipment_pickup_journey_v3->get();

                            foreach ($shipment_pickup_journey_v3 as $journey) {
                                $journey_details = array();

                                $journey_details['date_time'] = Carbon::parse($journey->created_at)->toDateTimeString();

                                $v3_pickup_status = DB::table('v3_pickup_request_statuses')->where('id',$journey->status_id)->first();

                                $journey_details['status'] = $v3_pickup_status->name;
                                if($journey->reason_id != NULL){
                                    $v3_pickup_request_reason = DB::table('v3_pickup_request_reasons')->where('id',$journey->reason_id)->first();
                                    $journey_details['reason'] = $v3_pickup_request_reason->name;
                                }
                                else{
                                    $journey_details['reason'] = '';
                                }

                                if ($journey->reference_1_id) {
                                    $journey_details['status'] .= ' (' . str_pad($journey->reference_1_id, 6, '0', STR_PAD_LEFT);

                                    if ($journey->reference_2_id) {
                                        if ($journey->status_id == 6) {
                                            $rider = Rider::find($journey->reference_2_id);
                                            if($rider){
                                                $journey_details['status'] .= $rider->name;
                                            }

                                        }
                                        else {
                                            $journey_details['status'] .= ' | ' . str_pad($journey->reference_2_id, 6, '0', STR_PAD_LEFT);
                                        }
                                    }

                                    $journey_details['status'] .= ')';
                                }

                                // $admin = $journey->admin;
                                $admin = DB::table('admins')->where('id',$journey->admin_id)->first();

                                if ($admin) {
                                    $journey_details['user'] = $admin->name;
                                }
                                else {
                                    $journey_details['user'] = '';
                                }

                                $details['pickup_history_v3'][] = $journey_details;
                            }
                        }



                        $user_id = null;
                        $substitute_user_id = null;
                        if (session('user_type') == 1) {
                            $user_type = 2;
                            $user_id = Auth::id();
                        }
                        else {
                            $user_type = 3;
                            $substitute_user_id = Auth::id();
                        }
                        ShipmentScanningJourneyController::add($shipment->id , 9, $user_type , null, $user_id, $substitute_user_id,NULL,NULL, session('latitude'), session('longitude'), NULL);


                        $tracking['shipments'][$shipment->id] = $details;
                    }
                    else {
                        $tracking['disallowed'][] = $tracking_number;
                    }
                }
                else{
                    $tracking['disallowed'][] = $tracking_number;
                }
    		}
    		else {
    			$tracking['invalid'][] = $tracking_number;
    		}

            
            
    	}

    	return $tracking;
    }

    public function rider_information(Request $request){
        return $this->riderInformation($request->id);
    }
    public function order_index(){

        $case_nature = CrmRequestCaseNature::where('id', '!=', 3)->get();
        $case_nature_type_complaints = CrmRequestCaseNatureType::where('nature_id', '=', 1)->where('status_id',1)->get();
        $case_nature_type_service_requests = CrmRequestCaseNatureType::where('nature_id', '=', 2)->where('status_id',1)->get();
        $case_nature_type_claims = CrmRequestCaseNatureType::where('nature_id', '=', 4)->where('status_id',1)->get();
        return view('client.order_tracking')->with([ 'case_nature' => $case_nature, 'case_nature_complaints' => $case_nature_type_complaints, 'case_nature_service_requests' => $case_nature_type_service_requests, 'case_nature_type_claims' => $case_nature_type_claims]);
    }

    public function order_track(Request $request) {

        $order_id = $request->order_id;
        $tracking = array();
            $shipment = Shipment::where('order_id', $order_id)->where(function ($query) {
                $query->where('user_id', session('user_id'));
                // TO-7240
                if (!empty(session('sister_users'))) {
                    $query->orWhereIn('user_id', session('sister_users'));
                }
            });;

            if ($shipment->exists()) {
                $shipments = $shipment->get();

                foreach($shipments as $shipment){
                    $sub_shipment = true;
                    if(session('user_type') == 2){
                        if(session('restriction') == 1){
                            $sub_check = SubstituteUserShipment::where('substitute_user_id', Auth::id())->where('shipment_id', $shipment->id);
                            if(!$sub_check->exists()){
                                $sub_shipment = false;
                            }
                        }
                    }
                    if($sub_shipment == true){
                        $track_check = false;
                        if(count(session('sister_users')) > 0){
                            foreach (session('sister_users') as $user_id){
                                if($user_id == $shipment->user_id){
                                    $track_check = true;
                                }

                                if (session('user_id') == $shipment->user_id) {
                                    $track_check = true;
                                }
                            }
                        }else{
                            if (session('user_id') == $shipment->user_id) {
                                $track_check = true;
                            }
                        }


                        if ($track_check == true) {
                            $details = array();

                            $details['order_id'] = $order_id;
                            $details['tracking_number'] = $shipment->tracking_number;

                            $shipper = $shipment->user;

                            $allowed_user_id = GlobalSettings::where('setting_value', 0)->where('type', 'brand_and_vendor_rights')->pluck('text');
                            $details['shipper']['id'] = $shipper->id;
                            $details['shipper']['assigned'] = $allowed_user_id;
                            $details['shipper']['name'] = $shipper->name;
                            $details['shipper']['account_number'] = str_pad($shipper->id, 6, '0', STR_PAD_LEFT);
                            $details['shipper']['city'] = $shipper->city->name;
                            $details['shipper']['phone_number_1'] = $shipper->phone;
                            $details['shipper']['phone_number_2'] = $shipper->phone2;
                            $details['shipper']['email'] = $shipper->email;

                            $pickup = $shipment->pickup_address;


                            $details['pickup']['person_of_contact'] = $pickup->poc;
                            $details['pickup']['vendor'] = $pickup->vendor;
                            $details['pickup']['pickup_brand_name'] = $pickup->pickup_brand_name;
                            $details['pickup']['phone_number'] = $pickup->phone;
                            $details['pickup']['email'] = $pickup->email;
                            $details['pickup']['origin'] = $pickup->city->name;
                            $details['pickup']['address'] = $pickup->pickup_address;

                            $details['consignee']['name'] = $shipment->consignee_name;
                            $details['consignee']['phone_number_1'] = $shipment->consignee_phone_number_1;
                            $details['consignee']['phone_number_2'] = $shipment->consignee_phone_number_2;
                            $details['consignee']['destination'] = $shipment->consignee_city->name;
                            $details['consignee']['address'] = $shipment->consignee_address;
                            $details['consignee']['email'] = $shipment->consignee_email;
                            foreach ($shipment->items as $item) {
                                $item_details = array();

                                $item_details['product_type'] = $item->product->product_name;
                                $item_details['description'] = $item->description;
                                $item_details['quantity'] = $item->quantity;

                                $details['order_information']['items'][] = $item_details;
                            }

                            $details['order_information']['order_id'] = $shipment->order_id;
                            if($shipment->order_date){
                                $details['order_information']['order_date'] = $shipment->order_date->order_date;
                            }
                            else{
                                $details['order_information']['order_date'] = NULL;
                            }

                            $details['order_information']['weight'] = ($shipment->actual_weight) ? floatval($shipment->actual_weight) : floatval($shipment->estimated_weight);
                            $details['order_information']['shipping_mode'] = $shipment->shipping_mode->mode;

                            $details['order_information']['booking_type_id'] = $shipment->booking_type_id;

                            if ($shipment->booking_type_id != 4) {
                                $details['order_information']['amount'] = $shipment->amount;
                            }
                            else {
                                if ($shipment->charges_mode_id == 1) {
                                    $details['order_information']['amount'] = 0;
                                }
                                else {
                                    $details['order_information']['amount'] = $shipment->amount;
                                }
                            }

                            $details['order_information']['account_type_id'] = $shipment->user->account_type_id;

                            $details['order_information']['charges_mode_id'] = $shipment->charges_mode_id;

                            if ($shipment->charges_mode_id) {
                                $details['order_information']['charges_mode'] = $shipment->charges_mode->charges_mode;
                            }

                            $details['order_information']['instructions'] = $shipment->special_instructions;
                            $details['order_information']['pieces'] = $shipment->pieces;
                            $details['order_information']['business_category'] = $shipment->business_category->name;

                            foreach ($shipment->shipment_journey as $journey) {
                                if ($journey->verification) {
                                    $journey_details = array();

                                    $journey_details['date_time'] = $journey->created_at->toDateTimeString();
                                    $journey_details['status'] = $journey->shipment_status_shipper->name;
                                    if(in_array($journey->shipper_status_id, [1])){
                                        if($shipment->booked_by == 1){
                                            $journey_details['status'] .= ' (Main User)';
                                        }
                                        else if($shipment->booked_by == 2){
                                            if($journey->reference_1_id != NULL){
                                                $sub_user = SubstituteUser::find($journey->reference_1_id);
                                                $journey_details['status'] .= ' (' . $sub_user->name . ' - Substitute User)';
                                            }
                                            else{
                                                $journey_details['status'] .= ' (Substitute User)';
                                            }
                                        }
                                    }
                                    if(in_array($journey->shipper_status_id, [52])){
                                        if($journey->reference_1_id != NULL){
                                            $sub_user = SubstituteUser::find($journey->reference_1_id);
                                            if($sub_user){
                                                $journey_details['status'] .= ' (' . $sub_user->name . ' - Substitute User)';
                                            }else{
                                                if($journey->user_id != null){
                                                    $journey_details['status'] .= ' (' . User::find($journey->user_id)->name . ' - Main User)';
                                                }
                                            }
                                        }
                                        else{
                                            if($journey->user_id != null){
                                                $journey_details['status'] .= ' (' . User::find($journey->user_id)->name . ' - Main User)';
                                            }else{
                                                $journey_details['status'] .= ' (Substitute User)';
                                            }
                                        }
                                    }

                                    if($journey->shipper_status_id == 25 && $journey->reference_1_id){
                                        $return_note = ReturnNote::find($journey->reference_1_id);
                                        if($return_note && $return_note->actual_date != null){
                                            $journey_details['status'] .= ' | ' . Carbon::parse($return_note->actual_date)->toDateString();
                                        }
                                    }

                                    $journey_details['status_reason'] = ($journey->status_reason_id) ? $journey->shipment_status_reason->name : NULL;

                                    if(in_array($journey->shipper_status_id, [20,52,54])){
                                        $journey_details['status_remarks'] = ($journey->remarks) ? $journey->remarks : '';
                                    }else{
                                        $journey_details['status_remarks'] = '';
                                    }
                                    
                                    $received_or_refused_by = '';

                                    $shippers = GlobalSettings::where('type','mms_setting')->select('text')->first();
                                $shippers = explode(',', $shippers->text);
                                $special_dashboard_shippers = User::whereIn('id', $shippers)->pluck('id')->toArray();

                                if(in_array($shipment->user_id,$special_dashboard_shippers))
                                {
                                    $receiver_details = ShipementReceiveDetails::where('tracking_number',$shipment->tracking_number)->first();
                                    if($receiver_details)
                                    {
                                        if($journey->received_or_refused_by){
                                            $received_or_refused_by = $receiver_details->receiver_name;
                                        }
                                        if($journey->cnic){
                                            $received_or_refused_by .= "|".$receiver_details->receiver_cnic;
                                        }
                                        if($journey->relation){
                                            $received_or_refused_by .= "|".$receiver_details->receiver_relationship;
                                        }
                                    }
                                    else{
                                        if($journey->received_or_refused_by){
                                            $received_or_refused_by = $journey->received_or_refused_by;
                                        }
                                        if($journey->cnic){
                                            $received_or_refused_by .= "|".$journey->cnic;
                                        }
                                        if($journey->relation){
                                            $received_or_refused_by .= "|".$journey->relation;
                                        }
                                    }
                                }
                                else{
                                    if($journey->received_or_refused_by){
                                        $received_or_refused_by = $journey->received_or_refused_by;
                                    }
                                    if($journey->cnic){
                                        $received_or_refused_by .= "|".$journey->cnic;
                                    }
                                    if($journey->relation){
                                        $received_or_refused_by .= "|".$journey->relation;
                                    }
                                }

                                    $details['tracking_history'][] = $journey_details;
                                }

                            }

                            $shipment_payment_journey = $shipment->shipment_payment_journey;

                            if ($shipment_payment_journey) {
                                foreach ($shipment_payment_journey as $journey) {
                                    $journey_details = array();

                                    $journey_details['date_time'] = Carbon::parse($journey->created_at)->toDateTimeString();
                                    $journey_details['status'] = $journey->status->name;
                                    $journey_details['user'] = $journey->admin->name;
                                    $journey_details['payable_remarks'] = ($journey->payable_remarks) ? $journey->payable_remarks : '';

                                    $details['payment_history'][] = $journey_details;
                                }
                            }
                            $user_id = null;
                            $substitute_user_id = null;
                            if (session('user_type') == 1) {
                                $user_type = 2;
                                $user_id = Auth::id();
                            }
                            else {
                                $user_type = 3;
                                $substitute_user_id = Auth::id();
                            }
                            ShipmentScanningJourneyController::add($shipment->id ,9,$user_type,null,$user_id,$substitute_user_id,NULL,NULL, session('latitude'), session('longitude'), NULL);

                            $tracking['shipments'][$shipment->id] = $details;
                        }
                        else {
                            $tracking['disallowed'][] = $order_id;
                        }
                    }
                    else{
                        $tracking['disallowed'][] = $order_id;
                    }
                }

            }
            else {
                $tracking['invalid'][] = $order_id;
            }


        return $tracking;
    }

    public function call_status_history(Request $request)
    {
        $mergedArray = [];
        $data = RvAgentCallHistory::with(['rv_call_finding' => function ($query) {
            $query->select('id', 'name');
        }, 'shipment.status_shipper' => function ($query) {
            $query->select('id', 'name');
        },  'updated_by'])->where('shipment_id', $request->shipment_id)->orderby('updated_at', 'desc')->get();
        
        if($data){
            foreach ($data as $item) {
                $mergedArray[] = [
                    'data' => $item,
                    'user_name' => $item->updated_by->name ?? '-',
                ];
            }
            return  $mergedArray;
        }
        else{
            return false;
        }
       
    }


    public  function get_shipment_geo_codes(Request $request)
    {

        $shipment_geo_code = ShipmentGeoCode::where('shipment_id',$request->shipment_id)->where('geo_code_type',2)
            ->select('latitude','longitude')->first();

        $lat = null;
        $long = null;
        if($shipment_geo_code){
            $shipment_id = $shipment_geo_code->shipment_id;
            $lat = $shipment_geo_code->latitude;
            $long = $shipment_geo_code->longitude;
        }

        return response()->json(['status'=>0,'lat'=>$lat,'long'=>$long]);
    }

    public function update_geo_codes(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'shipment_id' => 'required|integer|exists:shipments,id',
            'lat'         => 'required|numeric',
            'long'        => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 1,
                'error'  => $validator->errors()->first()
            ]);
        }

        // Check existing record
        $geoCode = ShipmentGeoCode::where('shipment_id', $request->shipment_id)
            ->where('geo_code_type', 2)
            ->first();

        if ($geoCode) {
            // Update existing
            $geoCode->latitude  = $request->lat;
            $geoCode->longitude = $request->long;
        } else {
            // New insert
            $geoCode = new ShipmentGeoCode();
            $geoCode->shipment_id = $request->geo_code_shipment_id;
            $geoCode->latitude    = $request->lat;
            $geoCode->longitude   = $request->long;
            $geoCode->geo_code_type        = 2;
        }

        $geoCode->save();

        return response()->json([
            'status'  => 0,
            'message' => 'Geo code saved successfully!'
        ]);
    }



}