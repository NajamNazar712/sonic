<?php

namespace App\Http\Controllers\Retail;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ShipmentScanningJourneyController;
use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\CargoManifest\CargoManifestBagShipments;
use App\Http\Models\Admin\KeyAccountDailyShipment;
use App\Http\Models\Admin\KeyAccountDailySummary;
use App\Http\Models\Admin\MasterCargo\Bag;
use App\Http\Models\Admin\MasterCargo\MasterCargoBag;
use App\Http\Models\Admin\Retail\RetailFranchise;
use App\Http\Models\Admin\Retail\RetailShipment;
use App\Http\Models\Admin\Retail\RetailShipperInfo;
use App\Http\Models\Admin\Retail\RetailTraxCenter;
use App\Http\Models\Admin\Retail\RetailUser;
use App\Http\Models\Admin\ReturnNote;
use App\Http\Models\Admin\SalePersonTag;
use App\Http\Models\City;
use App\Http\Models\CityArea;
use App\Http\Models\CRM\CrmRequest;
use App\Http\Models\CRM\CrmRequestCaseNature;
use App\Http\Models\CRM\CrmRequestCaseNatureType;
use App\Http\Models\CRM\CrmRequestChannel;
use App\Http\Models\CRM\CrmRequestStatusHistory;
use App\Http\Models\CRM\CrmSettings;
use App\Http\Models\CRM\CrmTatHolidays;
use App\Http\Models\DonePaymentShipment;
use App\Http\Models\Handover\Handover;
use App\Http\Models\InternationalShipment;
use App\Http\Models\RetailDonePaymentShipment;
use App\Http\Models\Rider;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentScanningJourney;
use App\Http\Models\ShipmentStatus;
use App\Http\Models\Shipper\User;
use App\Http\Models\SubstituteUserShipment;
use App\Http\Traits\CommonTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;
use DB;
use App\Models\RetailShipmentFlyerNumber;

class RetailTrackingController extends Controller
{
    use CommonTrait;
    public function __construct()
    {
        $this->middleware('auth:retail');

//        $this->middleware('Permission');
    }

    public function index(Request $request) {
        $case_nature = CrmRequestCaseNature::whereNotIn('id' , [3])->get();
        $case_nature_type_complaints = CrmRequestCaseNatureType::where('nature_id', '=', 1)->where('status_id',1)->get();
        $case_nature_type_service_requests = CrmRequestCaseNatureType::where('nature_id', '=', 2)->where('status_id',1)->get();
        $case_nature_channels = CrmRequestChannel::where('id', '!=', 1)->get();
        $case_nature_type_claims = CrmRequestCaseNatureType::where('nature_id', '=', 4)->where('status_id',1)->get();

        return view('retail.tracking')->with(['case_nature' => $case_nature, 'case_nature_complaints' => $case_nature_type_complaints, 'case_nature_service_requests' => $case_nature_type_service_requests, 'case_nature_channels' => $case_nature_channels, 'case_nature_type_claims' => $case_nature_type_claims]);
    }

    public function track_v2(Request $request) {
        $tracking_numbers = explode(',', $request->tracking_numbers);

        $tracking = array();

        foreach ($tracking_numbers as $tracking_number) {

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

                    if($shipment->shipment_type == 2){
                        $check = false;

                        if (session('department_id') == 7) {
                            if (!in_array(session('id'), session('sale_users_bypass'))) {
                                if (in_array($shipment->user->id, session('tagged_shippers')) || in_array(273, session('permissions'))) {
                                    $check = true;
                                }
                            }
                        }
                        ShipmentScanningJourneyController::add($shipment->id ,18,4,Auth::id(),NULL,NULL,NULL,NULL, session('latitude'), session('longitude'), NULL);

                        if ($shipment->booking_type_id == 4 || (session('department_id') == 7 && $check == true) || (session('department_id') != 7 && $check == false) || (session('department_id') == 7 && in_array(session('id'), session('sale_users_bypass')))) {
                            $details = array();

                            $details['tracking_number'] = $tracking_number;

                            $details['open_box'] = $shipment->open_box;

                            $shipper = $shipment->user;

                            $sales_person = SalePersonTag::where('user_id', $shipper->id)->leftjoin('admins as a', 'a.id', '=', 'sale_person_tags.admin_id')->where('sale_person_tags.status', 0);
                            if ($sales_person->exists()){
                                $sales_person = $sales_person->first();
                                $sales_person_name = $sales_person->name;
                            }
                            else{
                                $sales_person_name = null;
                            }

                            if ($shipment->business_category_id == 2) {
                                $international_shipment = InternationalShipment::where('shipment_id', $shipment->id)->whereNotNull('international_tracking_number');
                                if ($international_shipment->exists()) {
                                    $international_shipment = $international_shipment->first();
                                    $details['international_shipment'] = 1;
                                    $details['international_tracking_number'] = $international_shipment->international_tracking_number;
                                } else {
                                    $details['international_shipment'] = 0;
                                }
                            } else {
                                $details['international_shipment'] = 0;
                            }


                            $pickup = $shipment->pickup_address;

                            $details['pickup']['person_of_contact'] = $pickup->poc;
                            $details['pickup']['vendor'] = $pickup->vendor;
                            $details['pickup']['phone_number'] = $pickup->phone;
                            $details['pickup']['email'] = $pickup->email;
                            $details['pickup']['origin'] = $pickup->city->name;
                            $details['pickup']['address'] = $pickup->pickup_address;
                            $retail_shipment = RetailShipment::where('shipment_id',$shipment->id)->first();
                            if($retail_shipment){
                                $retail_user_id = $retail_shipment->retail_user_id;
                                $retail_admin_id = $retail_shipment->admin_id;
                                $retail_rider_id = $retail_shipment->rider_id;
                                $parcelAmount = $retail_shipment->parcel_amount;
                                $retailShipmentQuantity = $retail_shipment->quantity;
                                if($retail_user_id){
                                    $retail_user = RetailUser::find($retail_user_id);
                                    if($retail_user->category == 1){
                                        $franchise = RetailFranchise::find($retail_user->category_id);
                                        $details['retail_user']['name'] = $franchise->name;
                                        $details['retail_user']['code'] = 'Franchise';
                                        $details['shipper']['city'] = $franchise->pickup_address->city->name;
                                    }
                                    else{
                                        $trax_center  = RetailTraxCenter::find($retail_user->category_id);
                                        $details['retail_user']['name'] = $trax_center->name;
                                        $details['retail_user']['code'] = 'Trax Center';
                                        $details['shipper']['city'] = $trax_center->pickup_address->city->name;
                                    }
                                }
                                elseif($retail_admin_id){
                                    $admin_id = $retail_shipment->admin_id;
                                    $admin_info = Admin::find($admin_id);
                                    $details['retail_user']['name'] = $admin_info->name;
                                    $details['retail_user']['code'] = 'Trax Center';
                                    $details['shipper']['city'] = $shipment->pickup_address->city->name;

                                }
                                elseif($retail_rider_id){
                                    $rider_id = $retail_shipment->rider_id;
                                    $rider_info = Rider::find($rider_id);
                                    $details['retail_user']['name'] = $rider_info->name;
                                    $details['retail_user']['code'] = 'Trax Center';
                                    $details['shipper']['city'] = $shipment->pickup_address->city->name;

                                }

                                $shipper = RetailShipperInfo::find($retail_shipment->shipper_account_no);

                                $shipment_name_verification = \DB::table('retail_shipments')->where('shipment_id', $retail_shipment->shipment_id)->first();
                                $details['shipper']['name'] = $shipment_name_verification->shipper_name;
                                $details['shipper']['account_number'] = str_pad($shipper->id, 6, '0', STR_PAD_LEFT);
                                $details['shipper']['phone_number_1'] = $shipper->shipper_phone_no;
                                $details['shipper']['sales_person'] = $sales_person_name;

                            }
                            else{
                                $details['retail_user']['name'] = null;
                                $details['retail_user']['code'] = null;
                                $tracking['invalid'][] = $tracking_number;
                            }

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
                            $details['order_information']['weight'] = ($shipment->actual_weight) ? floatval($shipment->actual_weight) : floatval($shipment->estimated_weight);
                            if($shipment->shipment_type == 1){

                                $details['order_information']['shipping_mode'] = $shipment->shipping_mode->mode;
                            }
                            else{
                                $retail_shipment = RetailShipment::where('shipment_id',$shipment->id)->first();
                                if($retail_shipment){
                                    $details['order_information']['shipping_mode'] = $retail_shipment->shipping_modes->name;
                                }
                            }

                            $details['order_information']['flyer_count'] = RetailShipmentFlyerNumber::where('shipment_id', $shipment->id)->count();
                            $details['order_information']['booking_type'] = $shipment->booking_type->booking_type;
                            $details['order_information']['booking_type_id'] = $shipment->booking_type_id;

                            if ($shipment->booking_type_id != 4) {
                                $details['order_information']['amount'] =  number_format($shipment->amount);
                            }
                            else {
                                if ($shipment->charges_mode_id == 1) {
                                    $details['order_information']['amount'] = 0;
                                }
                                else {
                                    $details['order_information']['amount'] = number_format($shipment->amount);
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

                            $details['order_information']['parcel_value'] = $parcelAmount;
                            $details['order_information']['quantity'] = $retailShipmentQuantity;

                            $details['order_information']['sub_segment'] = $sub_segment_name;

                            foreach ($shipment->shipment_journey as $journey) {
                                $journey_details = array();

                                $journey_details['date_time'] = Carbon::parse($journey->created_at)->toDateTimeString();
                                $journey_details['status'] = $journey->shipment_status_shipper->name;
                                if(in_array($journey->shipper_status_id, [1])){
                                    if($shipment->booked_by == 1){
                                        $journey_details['status'] .= ' (Main User)';
                                    }
                                    else if($shipment->booked_by == 2){
                                        $journey_details['status'] .= ' (Substitute User)';
                                    }
                                }

                           if ($journey->reference_1_id && !in_array($journey->shipper_status_id, [1, 52])) {
                               if ($journey->shipper_status_id == 3) {
                                   $bag = Bag::where('id', $journey->reference_1_id);
                                   if($bag->exists()){
                                       $bag = $bag->first();
                                       $master_cargo_bags = MasterCargoBag::where('bag_id', $bag->id);
                                       if($master_cargo_bags->exists()){
                                           $journey_details['status'] .= ' (<button class="btn btn-sm btn-outline-info align-middle cargo_note_print" data-id="' . $bag->seal_number . '">' . $bag->seal_number . '</button>';
                                       }
                                       else{
                                           $journey_details['status'] .= ' (<button class="btn btn-sm btn-outline-info align-middle cargo_note_print" data-id="' . $bag->seal_number . '" disabled>' . $bag->seal_number . '</button>';
                                       }
                                   }
                                   else{
                                       $journey_details['status'] .= ' (<button class="btn btn-sm btn-outline-info align-middle cargo_note_print" data-id="' . $journey->reference_1_id . '">' . $journey->reference_1_id . '</button>';
                                   }
                               }
                               elseif (in_array($journey->shipper_status_id, [21, 26, 32])) {
                                   $journey_details['status'] .= ' (<button class="btn btn-sm btn-outline-info align-middle cargo_note_print" data-id="' . $journey->reference_1_id . '">' . str_pad($journey->reference_1_id, 6, '0', STR_PAD_LEFT) . '</button>';
                               }
                               else {
                                   if(in_array($journey->shipper_status_id, [23, 24, 25, 28, 29, 31, 44, 45, 47, 48])){
    //                                    $journey_details['status'] .= ' (<button class="btn btn-sm btn-outline-info align-middle return_note_print" data-id="' . $journey->reference_1_id . '">' . str_pad($journey->reference_1_id, 6, '0', STR_PAD_LEFT) . '</button>';
                                       $journey_details['status'] .= $journey->reference_1_id;
                                       if($journey->shipper_status_id == 25 && $journey->reference_1_id){
                                           $return_note = ReturnNote::find($journey->reference_1_id);
                                           if($return_note && $return_note->actual_date != null){
                                               $journey_details['status'] .= ' | ' . Carbon::parse($return_note->actual_date)->toDateString();
                                           }
                                       }
                                   }
                                   else if(in_array($journey->shipper_status_id, [5, 6, 7, 8, 9, 11, 12, 14, 15, 18, 56, 30, 20])){
                                       $journey_details['status'] .= ' (<button class="btn btn-sm btn-outline-info align-middle delivery_note_print" data-id="' . $journey->reference_1_id . '">' . str_pad($journey->reference_1_id, 6, '0', STR_PAD_LEFT) . '</button>';
    //                                    $journey_details['status'] .= $journey->reference_1_id;
                                   }
                                   else{
                                       //$journey_details['status'] .= ' (' . str_pad($journey->reference_1_id, 6, '0', STR_PAD_LEFT);
                                       $journey_details['status'] .= $journey->reference_1_id;
                                   }
    
                                   if ($journey->reference_2_id) {
                                       if (in_array($journey->shipper_status_id, [5, 23, 28, 34])) {
                                           $rider = Rider::find($journey->reference_2_id);
                                           if($rider){
                                               $journey_details['status'] .= ' | <button class="btn btn-sm btn-outline-info align-middle rider_information" data-id="' . $rider->id . '">' . $rider->name . '</button>';
    
                                           }
    
                                       }
                                       else {
                                           $journey_details['status'] .= ' | ' . str_pad($journey->reference_2_id, 6, '0', STR_PAD_LEFT);
                                       }
                                   }
                               }

                                $journey_details['status'] .= ')';
                                }
                                $user = '';
                                if($journey->admin_id){
                                    $user = $journey->admin->name;
                                }else if($journey->user_id){
                                    $user = $journey->user->name;
                                }
                            $shipment_scanning_query = ShipmentScanningJourney::select(
                                'ssjal.location_status',
                                'shipment_scanning_journeys.latitude',
                                'shipment_scanning_journeys.longitude',
                                'ssjal.area_id',
                                'shipment_scanning_journeys.created_at',
                                'ssjal.hub_id as hub_id_scanning',
                                'ssjal.shipment_scanning_journey_id as shipment_scanning_journey_id',
                                'sj.city_id as city_id_scanning'

                            )
                            ->join('shipments_journey as sj', function ($join) use ($journey) {
                                $join->on('sj.shipment_id', '=', 'shipment_scanning_journeys.shipment_id')
                                ->where('sj.id', '=', $journey->id);
                            })
                                ->join('shipment_scanning_journey_area_logs as ssjal', function ($join) use ($journey) {
                                    $join->on('ssjal.shipment_scanning_journey_id', '=', 'shipment_scanning_journeys.id')
                                        ->where('ssjal.hub_id', '=', $journey->city_id);
                                })
                                ->where('shipment_scanning_journeys.updated_at', '<=', $journey->updated_at);
                            if (isset($journey->admin['role_id']) && $journey->admin['role_id'] != 1 || isset($journey->rider_id)) {
                                switch ($journey->shipper_status_id) {
                                    case 2:
                                        $scanning_data = $shipment_scanning_query->where('screen_location_id', 1)->latest()->first();
                                        break;
                                    case 3:
                                        $scanning_data = $shipment_scanning_query->where('screen_location_id', 2)->latest()->first();
                                        break;
                                    case 4:
                                        $scanning_data = $shipment_scanning_query->whereIn('screen_location_id', [20, 21])->latest()->first();
                                        break;
                                    case 5:
                                        $scanning_data = $shipment_scanning_query->where('screen_location_id', 4)->latest()->first();
                                        break;
                                    case 11:
                                        $scanning_data = $shipment_scanning_query->whereIn('screen_location_id', [3, 10, 20, 21])->latest()->first();
                                        break;
                                    case 21:
                                        $scanning_data = $shipment_scanning_query->where('screen_location_id', 2)->latest()->first();
                                        break;
                                    case 22:
                                        $scanning_data = $shipment_scanning_query->where('screen_location_id', 20)->latest()->first();
                                        break;
                                    case 23:
                                        $scanning_data = $shipment_scanning_query->where('screen_location_id', 7)->latest()->first();
                                        break;
                                    case 26:
                                        $scanning_data = $shipment_scanning_query->where('screen_location_id', 2)->latest()->first();
                                        break;
                                    case 27:
                                        $scanning_data = $shipment_scanning_query->where('screen_location_id', 20)->latest()->first();
                                        break;
                                    case 28:
                                        $scanning_data = $shipment_scanning_query->where('screen_location_id', 7)->latest()->first();
                                        break;
                                    case 32:
                                        $scanning_data = $shipment_scanning_query->where('screen_location_id', 2)->latest()->first();
                                        break;
                                    case 33:
                                        $scanning_data = $shipment_scanning_query->where('screen_location_id', 20)->latest()->first();
                                        break;
                                    case 34:
                                        $scanning_data = $shipment_scanning_query->where('screen_location_id', 7)->latest()->first();
                                        break;
                                    case 53:
                                        $scanning_data = $shipment_scanning_query->where('screen_location_id', 31)->latest()->first();
                                        break;
                                    default:
                                        $scanning_data = null;
                                        break;
                                }
                            } else {
                                $scanning_data = null;
                            }
                            $journey_details['area_log'] = $this->setJourneyDetails($scanning_data);
                                $journey_details['status_reason'] = ($journey->status_reason_id) ? $journey->shipment_status_reason->name : NULL;
                                $journey_details['remarks'] = ($journey->remarks) ? $journey->remarks : '';
                                $journey_details['user'] = $user;
                                $journey_details['city'] = ($journey->city_id) ? $journey->city->name : '';
                                $journey_details['received_or_refused_by'] = ($journey->received_or_refused_by) ? $journey->received_or_refused_by : '';
                                $journey_details['ip'] = ($journey->ip_address) ? $journey->ip_address : '';
                                $journey_details['rider'] = ($journey->rider_id) ? $journey->rider->name : '';

                                $details['tracking_history'][] = $journey_details;
                            }

                            $shipment_payment_journey = $shipment->shipment_payment_journey;

                            if ($shipment_payment_journey) {
                                foreach ($shipment_payment_journey as $journey) {
                                    $journey_details = array();
                                    $payment = RetailDonePaymentShipment::where('shipment_id', $shipment->id)->first();
                                    $journey_details['date_time'] = Carbon::parse($journey->created_at)->toDateTimeString();
                                    if($journey->payment_id == null){
                                        $journey_details['status'] = $journey->status->name;
                                    }
                                    else{
                                        $journey_details['status'] = $journey->status->name . ' (<button class="btn btn-sm btn-outline-info align-middle payment_print" data-id="' . $journey->payment_id . '">' . str_pad($journey->payment_id, 6, '0', STR_PAD_LEFT) . '</button>)';
                                    }
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

                                    if ($journey->reference_1_id) {
                                        $journey_details['status'] .= ' (' . str_pad($journey->reference_1_id, 6, '0', STR_PAD_LEFT);

                                        if ($journey->reference_2_id) {
                                            if ($journey->status_id == 2) {
                                                $rider = Rider::find($journey->reference_2_id);
                                                if($rider){
                                                    $journey_details['status'] .= ' | <button class="btn btn-sm btn-outline-info align-middle rider_information" data-id="' . $rider->id . '">' . $rider->name . '</button>';
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

                            $old_shipment_pickup_journey = $shipment->shipment_pickup_journey;

                            if ($old_shipment_pickup_journey) {
                                foreach ($old_shipment_pickup_journey as $journey) {
                                    $journey_details = array();

                                    $journey_details['date_time'] = Carbon::parse($journey->created_at)->toDateTimeString();
                                    $journey_details['status'] = $journey->status->name;

                                    if ($journey->reference_1_id) {
                                        $journey_details['status'] .= ' (' . str_pad($journey->reference_1_id, 6, '0', STR_PAD_LEFT);

                                        if ($journey->reference_2_id) {
                                            if ($journey->status_id == 2) {
                                                $rider = Rider::find($journey->reference_2_id);
                                                if($rider){
                                                    $journey_details['status'] .= ' | <button class="btn btn-sm btn-outline-info align-middle rider_information" data-id="' . $rider->id . '">' . $rider->name . '</button>';
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

                                    $details['old_pickup_history'][] = $journey_details;
                                }
                            }

                            $handover_shipment_journey = $shipment->handover_shipments_journeys;

                            if ($handover_shipment_journey) {
                                foreach ($handover_shipment_journey as $journey) {
                                if ($journey->status == 1) {
                                    $handover_created_by = Handover::find($journey->handover_id)->created_by;
                                    $admin_created_by = Admin::find($handover_created_by);
                                } else {
                                    $handover_received_by = Handover::find($journey->handover_id)->received_by;
                                    $admin_received_by = Admin::find($handover_received_by);
                                }

                                if ((isset($admin_created_by->role_id) && $admin_created_by->role_id != 1) || (isset($admin_received_by->role_id) && $admin_received_by->role_id != 1)) {
                                    $shipment_scanning_query = ShipmentScanningJourney::select(
                                        'ssjal.location_status',
                                        'shipment_scanning_journeys.latitude',
                                        'shipment_scanning_journeys.longitude',
                                        'ssjal.area_id',
                                        'shipment_scanning_journeys.created_at',
                                        'ssjal.hub_id'
                                    )
                                        ->join('handover_shipments_journeys as hsj', 'hsj.shipment_id', '=', 'shipment_scanning_journeys.shipment_id')
                                        ->join('shipment_scanning_journey_area_logs as ssjal', 'ssjal.shipment_scanning_journey_id', '=', 'shipment_scanning_journeys.id')
                                        ->join('handovers', 'hsj.handover_id', '=', 'handovers.id') // added this join
                                        ->where('shipment_scanning_journeys.updated_at', '<=', $journey->updated_at)
                                        ->where('hsj.handover_id', '=', $journey->handover_id);

                                    switch ($journey->status) {
                                        case 1:
                                            $scanning_data = $shipment_scanning_query->where('screen_location_id', 26)->latest()->first();
                                            break;
                                        case 2:
                                            $scanning_data = $shipment_scanning_query->where('screen_location_id', 27)->latest()->first();
                                            break;

                                        default:
                                            $scanning_data = null;
                                            break;
                                    }
                                    } else {
                                        $scanning_data = null;
                                    }
                                    $journey_details = array();

                                    $journey_details['handover_id'] = $journey->handover_id;
                                    $journey_details['status'] = $journey->my_status->name;
                                // $journey_details['status'] = "adf>name";
                                    $journey_details['area_log'] = $this->setJourneyDetails($scanning_data);
                                    $journey_details['created_at'] = Carbon::parse($journey->created_at)->toDateTimeString();

                                    $details['handover_history'][] = $journey_details;
                                }
                            }


                            $shipment_amount_log = $shipment->amount_change_log;

                            if ($shipment_amount_log) {
                                foreach ($shipment_amount_log as $journey) {
                                    $journey_details = array();

                                    $journey_details['date_time'] = Carbon::parse($journey->created_at)->toDateTimeString();
                                    $journey_details['old_amount'] = number_format($journey->old_amount);
                                    $journey_details['new_amount'] = number_format($journey->new_amount);
                                    $journey_details['remarks'] = $journey->remarks;
                                    $journey_details['user'] = $journey->admin->name;

                                    $details['amount_history'][] = $journey_details;
                                }
                            }

                            $shipment_weight_log = $shipment->weight_change_log;

                            if ($shipment_weight_log) {
                                foreach ($shipment_weight_log as $journey) {
                                    $journey_details = array();

                                    $journey_details['date_time'] = Carbon::parse($journey->created_at)->toDateTimeString();
                                    $journey_details['old_weight'] = $journey->old_weight;
                                    $journey_details['new_weight'] = $journey->new_weight;
                                    $journey_details['user'] = $journey->admin->name;

                                    $details['weight_history'][] = $journey_details;
                                }
                            }

                            $complain = CrmRequest::where('shipment_id', $shipment->id)->whereIn('status_id', [2, 3, 5]);

                            if ($complain->exists()) {
                                $complain = $complain->first();

                                $details['complain'] = array();
                                $details['complain']['id'] = $complain->id;
                                $details['complain']['padded_id'] = str_pad($complain->id, 6, '0', STR_PAD_LEFT);
                                $details['complain']['tat'] = Carbon::parse($complain->created_at)->diffInWeekdays(Carbon::now());
                            }

                            $crm_requests = CrmRequest::leftjoin('crm_request_status_histories as crsh', 'crsh.crm_request_id', '=', 'crm_requests.id')
                                ->leftjoin('admins as a', 'a.id', '=', 'crm_requests.agent_id')
                                ->leftjoin('users as u', 'u.id', '=', 'crm_requests.launched_by_id')
                                ->leftjoin('retail_users as ru', 'ru.id', '=', 'crm_requests.launched_by_id')
                                ->leftjoin('substitute_users as su', 'su.id', '=', 'crm_requests.launched_by_id')
                                ->leftjoin('crm_request_statuses as crs', 'crs.id', '=', 'crsh.status_id')
                                ->select('crm_requests.id as id', 'crs.name as status', 'a.id as admin_id',
                                    'a.name as created_by_admin', 'u.name as created_by_user', 'su.name as created_by_sub_user',
                                    'crsh.created_at as created_at', 'crsh.status_id as status_id', 'crm_requests.launched_by as launched_added_by','crm_requests.launched_by_id', 'ru.name as created_by_retail_user')
                                ->where('crm_requests.shipment_id', $shipment->id);

                            if($crm_requests->exists()){
                                $crm_requests = $crm_requests->get();

                                foreach ($crm_requests as $crm_request){
                                    $crm_request_journey = array();

                                    $crm_request_journey['id'] = str_pad($crm_request->id, 6, '0', STR_PAD_LEFT);
                                    $crm_request_journey['status_id'] = $crm_request->status_id;
                                    $crm_request_journey['status'] = $crm_request->status;
                                    if($crm_request->status_id == 1){
                                        if($crm_request->launched_added_by == 0){
                                            $crm_request_journey['created_by'] = Admin::find($crm_request->launched_by_id)->name . ' (Admin)';
                                        }
                                        else if($crm_request->launched_added_by == 1){
                                            $crm_request_journey['created_by'] = $crm_request->created_by_user . ' (Shipper)';
                                        }
                                        else if($crm_request->launched_added_by == 3){
                                            $crm_request_journey['created_by'] = $crm_request->created_by_retail_user . ' (Retail User)';
                                        }
                                        else{
                                            $crm_request_journey['created_by'] = $crm_request->created_by_sub_user . ' (Substitute Shipper)';
                                        }
                                    }
                                    else{
                                        $crm_request_journey['created_by'] = $crm_request->created_by_admin . ' (Admin)';
                                    }
                                    $crm_request_journey['created_at'] = Carbon::parse($crm_request->created_at)->toDateTimeString();

                                    $details['crm_requests'][] = $crm_request_journey;
                                }

                            }

                            $shipment_open_box_journey = $shipment->open_box_journey;
                            if($shipment_open_box_journey){
                                foreach ($shipment_open_box_journey as $journey) {
                                    $open_box_journey_details = array();

                                    $open_box_journey_details['date_time'] = Carbon::parse($journey->created_at)->toDateTimeString();
                                    $open_box_journey_details['status'] = $journey->shipment_open_status->name;
                                    $open_box_journey_details['created_by'] = $journey->admin->name;

                                    $details['open_box_journey'][] = $open_box_journey_details;
                                }
                            }

                            // $details['complain']['id'] = 10;
                            // $details['complain']['tat'] = 3;
                            if($request->has('key_accounts')){
                                $date = Carbon::today()->toDateString();
                                $key_accounts_daily_summary = KeyAccountDailySummary::where('admin_id', Auth::id())->whereDate('created_at', $date);
                                if($key_accounts_daily_summary->exists()){
                                    $key_accounts_daily_summary = $key_accounts_daily_summary->first();
                                    $count = $key_accounts_daily_summary->count + 1;
                                    $key_accounts_daily_summary->count = $count;
                                    $key_accounts_daily_summary->save();
                                }
                                else{
                                    $key_accounts_daily_summary = new KeyAccountDailySummary();
                                    $key_accounts_daily_summary->admin_id = Auth::id();
                                    $key_accounts_daily_summary->count = 1;
                                    $key_accounts_daily_summary->save();
                                }

                                $key_accounts_daily_shipment = new KeyAccountDailyShipment();
                                $key_accounts_daily_shipment->admin_id = Auth::id();
                                $key_accounts_daily_shipment->shipment_id = $shipment->id;
                                $key_accounts_daily_shipment->save();
                            }
                            $cargo_manifest_bag_shipments = CargoManifestBagShipments::with('bag', 'manifestBag_latest', 'manifestBag_latest.cargo_manifest', 'manifestBag_latest.cargo_manifest.fleet.driver')
                            ->where('shipment_id', $shipment->id);

                            if ($cargo_manifest_bag_shipments->exists()) {
                                $cargo_manifest_bag_shipments = $cargo_manifest_bag_shipments->latest()->first();
                                $details['manifest_history']  = $cargo_manifest_bag_shipments;
                            }
                            $details['shipment_id'] = $shipment->id;

                            $tracking['shipments'][] = $details;
                        }
                        else {
                            $tracking['unauthorized'][] = $tracking_number;
                        }
                    }

                }

            else {
                $tracking['invalid'][] = $tracking_number;
            }
        }

        return $tracking;
    }
    

    public function rider_information(Request $request)
    {
        $rider = Rider::find($request->id);
        $information = array();
        $information['id'] = $rider->id;
        $information['name'] = $rider->name;
        $information['phone_number'] = $rider->phone;
        $information['city'] = $rider->city->name;
        $information['category'] = $rider->rider_category->name;
        if ($rider->route) {
            $information['route'] = $rider->route->code . ' (' . $rider->route->start . ' to ' . $rider->route->end . ')';
        } else {
            $information['route'] = '';
        }
        return $information;
    }

    public function deliveryNotePrint(Request $request){
        $delivery_note_id = $request->id;
        $data = $this->receiveDeliveryPrint($delivery_note_id);
        return $data;
    }
}
