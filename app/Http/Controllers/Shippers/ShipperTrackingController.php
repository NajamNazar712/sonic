<?php

namespace App\Http\Controllers\Shippers;

use App\Http\Controllers\ShipmentScanningJourneyController;
use App\Http\Models\Admin\ReturnNote;
use App\Http\Models\CRM\CrmRequestCaseNature;
use App\Http\Models\CRM\CrmRequestCaseNatureType;
use App\Http\Models\InternationalShipment;
use App\Http\Models\Rider;
use App\Http\Models\Shipper\SubstituteUser;
use App\Http\Models\Shipper\User;
use App\Http\Models\Sister_account\MergedSisterAccountMapping;
use App\Http\Models\SubstituteUserShipment;
use App\Http\Models\Shipper\ReturnSheetShipments;
use Cassandra\Session;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\Admin\DeliveryLocationMapping;
use App\Http\Models\Admin\DeliveryLocationMappingKeyword;
use App\Http\Models\Shipment;
use App\Http\Models\CargoConsignment;
use App\Http\Models\CRM\CrmRequest;
use App\Http\Models\RiderDelivery;
use App\Http\Models\ShipmentReplacementParcelImage;
use Auth;

use Yajra\Datatables\Datatables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class ShipperTrackingController extends Controller
{
    public function __construct() {
      $this->middleware('auth:web,substitute_users');

      $this->middleware('Permission');
    }

    public function index() {
//        dd(session()->all());
        $permission = session('permissions');

        $case_nature = CrmRequestCaseNature::get();
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
        $case_nature_type_complaints = CrmRequestCaseNatureType::where('nature_id', '=', 1)->where('status_id',1)->get();
        $case_nature_type_service_requests = CrmRequestCaseNatureType::where('nature_id', '=', 2)->where('status_id',1)->get();
        $case_nature_type_claims = CrmRequestCaseNatureType::where('nature_id', '=', 4)->where('status_id',1)->get();
      return view('client.tracking')->with([ 'case_nature' => $case_nature, 'case_nature_complaints' => $case_nature_type_complaints, 'case_nature_service_requests' => $case_nature_type_service_requests, 'case_nature_type_claims' => $case_nature_type_claims,'case_permission'=>$permission]);
    }

    public function track(Request $request) {
    	$tracking_numbers = explode(',', $request->tracking_numbers);

    	$tracking = array();

    	foreach ($tracking_numbers as $tracking_number) {
    		$shipment = Shipment::where('tracking_number', $tracking_number);

    		if ($shipment->exists()) {
                $shipment = $shipment->first();
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

                        $details['shipper']['name'] = $shipper->name;
                        $details['shipper']['account_number'] = str_pad($shipper->id, 6, '0', STR_PAD_LEFT);
                        $details['shipper']['city'] = $shipper->city->name;
                        $details['shipper']['phone_number_1'] = $shipper->phone;
                        $details['shipper']['phone_number_2'] = $shipper->phone2;
                        $details['shipper']['email'] = $shipper->email;

                        $pickup = $shipment->pickup_address;

                        $details['pickup']['person_of_contact'] = $pickup->poc;
                        $details['pickup']['vendor'] = $pickup->vendor;
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

                                $journey_details['status_reason'] = ($journey->status_reason_id) ? $journey->shipment_status_reason->name : NULL;

                                if(in_array($journey->shipper_status_id, [8,17,20,52,54])){
                                    $journey_details['status_remarks'] = ($journey->remarks) ? $journey->remarks : '';
                                }else{
                                    $journey_details['status_remarks'] = '';
                                }
                                $received_or_refused_by = '';
                                if($journey->received_or_refused_by){
                                    $received_or_refused_by = $journey->received_or_refused_by;
                                }
                                if($journey->cnic){
                                    $received_or_refused_by .= "|".$journey->cnic;
                                }
                                if($journey->relation){
                                    $received_or_refused_by .= "|".$journey->relation;
                                }
                                $journey_details['received_or_refused_by'] = $received_or_refused_by;
//                            $journey_details['city'] = ($journey->city_id) ? $journey->city->name : '';

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

                                $details['pickup_history'][] = $journey_details;
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

                        ShipmentScanningJourneyController::add($shipment->id, 9, $user_type, null, $user_id, $substitute_user_id);

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

    public function order_index(){

        $case_nature = CrmRequestCaseNature::get();
        $case_nature_type_complaints = CrmRequestCaseNatureType::where('nature_id', '=', 1)->where('status_id',1)->get();
        $case_nature_type_service_requests = CrmRequestCaseNatureType::where('nature_id', '=', 2)->where('status_id',1)->get();
        $case_nature_type_claims = CrmRequestCaseNatureType::where('nature_id', '=', 4)->where('status_id',1)->get();
        return view('client.order_tracking')->with([ 'case_nature' => $case_nature, 'case_nature_complaints' => $case_nature_type_complaints, 'case_nature_service_requests' => $case_nature_type_service_requests, 'case_nature_type_claims' => $case_nature_type_claims]);
    }

    public function order_track(Request $request) {

        $order_id = $request->order_id;
        $tracking = array();
            $shipment = Shipment::where('order_id', $order_id)->where('user_id',session('user_id'));

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

                            $details['shipper']['name'] = $shipper->name;
                            $details['shipper']['account_number'] = str_pad($shipper->id, 6, '0', STR_PAD_LEFT);
                            $details['shipper']['city'] = $shipper->city->name;
                            $details['shipper']['phone_number_1'] = $shipper->phone;
                            $details['shipper']['phone_number_2'] = $shipper->phone2;
                            $details['shipper']['email'] = $shipper->email;

                            $pickup = $shipment->pickup_address;


                            $details['pickup']['person_of_contact'] = $pickup->poc;
                            $details['pickup']['vendor'] = $pickup->vendor;
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
                                    if($journey->received_or_refused_by){
                                        $received_or_refused_by = $journey->received_or_refused_by;
                                    }
                                    if($journey->cnic){
                                        $received_or_refused_by .= "|".$journey->cnic;
                                    }
                                    if($journey->relation){
                                        $received_or_refused_by .= "|".$journey->relation;
                                    }
                                    $journey_details['received_or_refused_by'] = $received_or_refused_by;
//                            $journey_details['city'] = ($journey->city_id) ? $journey->city->name : '';

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

                            ShipmentScanningJourneyController::add($shipment->id, 9, $user_type, null, $user_id, $substitute_user_id);

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


}