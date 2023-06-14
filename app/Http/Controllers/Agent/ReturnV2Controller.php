<?php

namespace App\Http\Controllers\Agent;

use Carbon\Carbon;
use App\FakeStatusRemark;
use App\Http\Models\Rider;
use Illuminate\Http\Request;
use App\Http\Models\Shipment;
use App\Http\Models\DwsDetail;
use App\Http\Models\Admin\Admin;
use App\Http\Models\HR\Employee;
use App\Http\Models\SaleTierTag;
use App\Http\Models\StarShipper;
use App\Http\Models\RiderDelivery;
use Illuminate\Support\Facades\DB;
use App\Http\Models\CRM\CrmRequest;
use App\Http\Models\ShipmentDetail;
use App\Http\Models\ShipmentStatus;
use App\Http\Controllers\Controller;
use App\Http\Models\CRM\CrmSettings;
use Illuminate\Support\Facades\Auth;
use App\Http\Models\Admin\ReturnNote;
use App\Http\Models\ReportingLocation;
use App\Http\Models\CRM\CrmTatHolidays;
use Illuminate\Support\Facades\Storage;
use App\Http\Models\Admin\SalePersonTag;
use App\Http\Models\DonePaymentShipment;
use App\Http\Models\ShipmentStatusReason;
use App\Http\Models\Admin\MasterCargo\Bag;
use App\Http\Models\CRM\CrmRequestChannel;
use App\Http\Models\InternationalShipment;
use App\Http\Models\Admin\HighAlertShipper;
use App\Http\Models\ConsigneeRefusedReason;
use App\Http\Models\WMS\WmsUserInformation;
use App\Http\Models\Admin\Retail\RetailUser;
use App\Http\Models\CRM\CrmRequestCaseNature;
use App\Http\Models\RetailDonePaymentShipment;
use App\Http\Models\Rider\RiderReturnDelivery;
use App\Http\Models\Admin\SubStatusCallFinding;
use App\Http\Models\Admin\Retail\RetailShipment;
use App\Http\Models\CRM\CrmRequestStatusHistory;
use App\Http\Models\Admin\KeyAccountDailySummary;
use App\Http\Models\Admin\Retail\RetailFranchise;
use App\Http\Models\CRM\CrmRequestCaseNatureType;
use App\Http\Models\Shipper\ReturnSheetShipments;
use App\Http\Models\Admin\KeyAccountDailyShipment;
use App\Http\Models\Admin\MasterCargo\BagShipment;
use App\Http\Models\Admin\Retail\RetailTraxCenter;
use App\Http\Models\Admin\Retail\RetailShipperInfo;
use App\Http\Models\ShipmentReplacementParcelImage;
use App\Http\Models\Admin\CargoManifest\ManifestBag;
use App\Http\Models\Admin\MasterCargo\MasterCargoBag;
use App\Http\Models\Admin\ResolvedOutstandingShipment;
use App\Http\Models\Admin\Attendance\EmployeeAttendance;
use App\Http\Models\Admin\CargoManifest\CargoManifestBag;
use App\Http\Controllers\ShipmentScanningJourneyController;
use App\Http\Models\Admin\DeliveryShipmentsReceivedOperation;
use App\Http\Models\Admin\CargoManifest\CargoManifestBagShipments;

class ReturnV2Controller extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:agent');
        $this->middleware('Permission');

    }

    public function index()
    {

        $case_nature = CrmRequestCaseNature::get();
        $case_nature_type_complaints = CrmRequestCaseNatureType::where('nature_id', '=', 1)->where('status_id', 1)->get();
        $case_nature_type_service_requests = CrmRequestCaseNatureType::where('nature_id', '=', 2)->where('status_id', 1)->get();
        $case_nature_channels = CrmRequestChannel::where('id', '!=', 1)->get();
        $case_nature_type_claims = CrmRequestCaseNatureType::where('nature_id', '=', 4)->where('status_id', 1)->get();
        $return_confirm_reason_ids = DB::table('shipment_status_shipment_status_reason')->where('shipment_status_id', 20)->whereNotIn('shipment_status_reason_id', [2, 55])->pluck('shipment_status_reason_id')->toArray();
        $return_confirm_reasons = ShipmentStatusReason::whereIn('id', $return_confirm_reason_ids)->select('id', 'name')->get();
        $consignee_refused_reasons = ConsigneeRefusedReason::where('status', 1)->select('id', 'reasons')->where('status', 1)->get();
        $sub_status_call_finding = SubStatusCallFinding::get();
        $shipment_statuses = ShipmentStatus::whereIn('id', [20, 15, 52, 54])->get();
        $fake_status_remarks = FakeStatusRemark::get();
        return view('admin.return_v2.index')->with(['case_nature' => $case_nature, 'case_nature_complaints' => $case_nature_type_complaints, 'case_nature_service_requests' => $case_nature_type_service_requests, 'case_nature_channels' => $case_nature_channels, 'case_nature_type_claims' => $case_nature_type_claims, 'return_confirm_reasons' => $return_confirm_reasons, 'consignee_refused_reasons' => $consignee_refused_reasons, 'sub_status_call_finding' => $sub_status_call_finding, 'shipment_statuses' => $shipment_statuses, 'fake_status_remarks' => $fake_status_remarks]);
    }


    public function get_shipment_reason(Request $request)
    {
        $ids = DB::table('shipment_status_shipment_status_reason')->where('shipment_status_id', $request->id)->pluck('shipment_status_reason_id')->toArray();
        $reasons = DB::table('shipment_status_reason')->whereIn('id', $ids)->get();
        $unresponsive_reasons = SubStatusCallFinding::get();
        return response()->json(['reasons' => $reasons, 'unresponsive_reasons' => $unresponsive_reasons, 'status' => 1]);
    }


    public function getTicket(Request $request)
    {

        // $admin = Admin::where('id', $request->auth_id);
        // if ($admin->exists()) {
        //     $admin = $admin->first();
        //     $employee_attendance = EmployeeAttendance::where('employee_id', $admin->employee_id)->where('attendance_date', date('Y-m-d'));

        //     if($employee_attendance->exists()){
        //         $employee_attendance->update(['clock_in'=>date('H:i:s')]);
        //     } else {

        //         $employee_attendance = new EmployeeAttendance();
        //         $employee_attendance->employee_id = $admin->employee_id;
        //         $employee_attendance->attendance_date = date('Y-m-d');
        //         $employee_attendance->clock_in = date('H:i:s');
        //         $employee_attendance->employee_type = $admin->employee_type ? $admin->employee_type : '1';
        //         // $employee_attendance->clock_out_latitude = session('latitude');
        //         // $employee_attendance->clock_out_longitude = session('longitude');
        //         $employee_attendance->save();
        //     }


        //     return response()->json(['employee_attendance'=>$employee_attendance]);

        // }

        
        {
            dd(session('latitude'));
            $tracking_numbers = explode(',', $request->tracking_numbers);
    
            $tracking = array();
    
            foreach ($tracking_numbers as $tracking_number) {
                $details = array();
                $shipment = Shipment::where('tracking_number', $tracking_number);
    
                if ($shipment->exists()) {
                    $shipment = $shipment->first();
    
                    $star_user_id = $shipment->user_id;
    
                    $is_user_star = StarShipper::where('user_id',$star_user_id)
                        ->where('status',1);
                    if($is_user_star->exists())
                    {
                        $details['star_shipper'] = 1;
                    }
                    else
                    {
                        $details['star_shipper'] = 0;
                    }
    
    
    
                    if ($shipment->user->blacklist == 0) {
                        $check = false;
    
                        if (session('department_id') == 7) {
                            if (!in_array(session('id'), session('sale_users_bypass'))) {
                                if (in_array($shipment->user->id, session('tagged_shippers')) || in_array(273, session('permissions'))) {
                                    $check = true;
                                }
                            }
                        }
                        ShipmentScanningJourneyController::add($shipment->id, 9, 1, Auth::id(), null, null);
    
                        if ($shipment->booking_type_id == 4 || (session('department_id') == 7 && $check == true) || (session('department_id') != 7 && $check == false) || (session('department_id') == 7 && in_array(session('id'), session('sale_users_bypass')))) {
    
    
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
    
                            $details['tracking_number'] = $tracking_number;
                            if ($shipment->pod_image()->exists()) {
                                $details['pod_file'] = asset('uploads/pod_images/' . $shipment->pod_image->pod_file);
    
                            }
                            $details['open_box'] = $shipment->open_box;
                            if ($shipment->payment_mode_id == 2) {
                                $details['ccd'] = 1;
                            } else {
                                $details['ccd'] = 0;
                            }
    
                            $received_shipments = ReturnSheetShipments::where('shipment_id',$shipment->id);
                            if($received_shipments->exists()){
                            $details['received_img'] =  '<img src="' . asset('img/shipement_received.png').' ">';
                            }else{
                                $details['received_img'] ="";
                            }
    
                            $shipper = $shipment->user;
    
                            $sales_person = SalePersonTag::where('user_id', $shipper->id)->leftjoin('admins as a', 'a.id', '=', 'sale_person_tags.admin_id')->where('sale_person_tags.status', 0);
                            if ($sales_person->exists()) {
                                $sales_person = $sales_person->first();
                                $sales_person_name = $sales_person->name;
                            } else {
                                $sales_person_name = null;
                            }
                            $tagged_kae = SaleTierTag::where('user_id', $shipper->id);
                            if ($tagged_kae->exists()) {
                                $tagged_kae = $tagged_kae->first();
                                if ($tagged_kae->kam)
                                    $tagged_kae_name = $tagged_kae->kam_admin->name;
                                else
                                    $tagged_kae_name = "-";
                            } else {
                                $tagged_kae_name = "-";
                            }
                            $wms_user = WmsUserInformation::where('user_id', $shipper->id);
                            if ($wms_user->exists()) {
                                $wms_user = $wms_user->first();
                                if ($wms_user->warehousing == 1)
                                    $wms_user_name = "(W)";
                                else
                                    $wms_user_name = "";
                            } else {
                                $wms_user_name = "";
                            }
                            if ($shipment->shipment_type == 1) {
                                $details['shipment_type'] = 1;
                                $details['shipper']['name'] = $shipper->name . ' ' . $wms_user_name;
                                $details['shipper']['account_number'] = str_pad($shipper->id, 6, '0', STR_PAD_LEFT);
                                $details['shipper']['city'] = $shipper->city->name;
                                $details['shipper']['phone_number_1'] = $shipper->phone;
                                $details['shipper']['phone_number_2'] = $shipper->phone2;
                                $details['shipper']['email'] = $shipper->email;
                                $details['shipper']['sales_person'] = $sales_person_name;
                                $details['shipper']['tagged_kae'] = $tagged_kae_name;
    
                                $high_alert = HighAlertShipper::where('user_id', $shipper->id)->where('status', 1);
                                if($high_alert->exists()){
                                    $high_alert = $high_alert->latest()->first();
    
                                    $details['high_alert'] = "High alert marked on ". Carbon::parse($high_alert->created_at)->toDateTimeString() . " by " . $high_alert->alerted_by->name . " because of " . $high_alert->description;
    
                                }
    
    
                            } else {
                                $retail_shipment = RetailShipment::where('shipment_id', $shipment->id)->first();
                                if ($retail_shipment) {
                                    $retail_user_id = $retail_shipment->retail_user_id;
                                    $retail_admin_id = $retail_shipment->admin_id;
                                    $retail_rider_id = $retail_shipment->rider_id;
                                    if ($retail_user_id) {
                                        $retail_user = RetailUser::find($retail_user_id);
                                        if ($retail_user->category == 1) {
                                            $franchise = RetailFranchise::find($retail_user->category_id);
                                            $details['retail_user']['name'] = $franchise->name;
                                            $details['retail_user']['code'] = 'Franchise';
                                            $details['shipper']['city'] = $franchise->pickup_address->city->name;
                                        } else {
                                            $trax_center = RetailTraxCenter::find($retail_user->category_id);
                                            $details['retail_user']['name'] = $trax_center->name;
                                            $details['retail_user']['code'] = 'Trax Center';
                                            $details['shipper']['city'] = $trax_center->pickup_address->city->name;
                                        }
                                    } elseif ($retail_admin_id) {
                                        $admin_id = $retail_shipment->admin_id;
                                        $admin_info = Admin::find($admin_id);
                                        $details['retail_user']['name'] = $admin_info->name;
                                        $details['retail_user']['code'] = 'Trax Center';
                                        $details['shipper']['city'] = $shipment->pickup_address->city->name;
    
                                    } elseif ($retail_rider_id) {
                                        $rider_id = $retail_shipment->rider_id;
                                        $rider_info = Rider::find($rider_id);
                                        $details['retail_user']['name'] = $rider_info->name;
                                        $details['retail_user']['code'] = 'Trax Center';
                                        $details['shipper']['city'] = $shipment->pickup_address->city->name;
    
                                    }
    
                                    $shipper = RetailShipperInfo::find($retail_shipment->shipper_account_no);
    
                                    $details['shipper']['name'] = $shipper->shipper_name;
                                    $details['shipper']['account_number'] = str_pad($shipper->id, 6, '0', STR_PAD_LEFT);
                                    $details['shipper']['phone_number_1'] = $shipper->shipper_phone_no;
                                    $details['shipper']['sales_person'] = $sales_person_name;
    
                                } else {
                                    $details['retail_user']['name'] = null;
                                    $details['retail_user']['code'] = null;
                                    $tracking['invalid'][] = $tracking_number;
                                }
                            }
    
    
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
    
    
    
                            foreach ($shipment->items as $item) {
                                $item_details = array();
    
                                $item_details['product_type'] = $item->product->product_name;
                                $item_details['description'] = $item->description;
                                $item_details['quantity'] = $item->quantity;
    
                                $details['order_information']['items'][] = $item_details;
                            }
    
                            $details['order_information']['order_id'] = $shipment->order_id;
                            if ($shipment->breadth != null) {
                                $details['order_information']['height'] = $shipment->height;
                                $details['order_information']['length'] = $shipment->length;
                                $details['order_information']['breadth'] = $shipment->breadth;
                                $details['order_information']['weight'] = ($shipment->actual_weight) ? floatval($shipment->actual_weight) : floatval($shipment->estimated_weight);
    
                            } else {
                                $details['order_information']['weight'] = ($shipment->actual_weight) ? floatval($shipment->actual_weight) : floatval($shipment->estimated_weight);
                            }
    
                            if($shipment->shipment_type == 1){
    
                                $details['order_information']['shipping_mode'] = $shipment->shipping_mode->mode;
                            }
                            else{
                                $retail_shipment = RetailShipment::where('shipment_id',$shipment->id)->first();
                                if($retail_shipment){
                                    $details['order_information']['shipping_mode'] = $retail_shipment->shipping_modes->name;
                                }
                            }
                           
                          
                            $details['order_information']['shipping_mode_id'] = $shipment->shipping_mode->id;
    
                            $details['order_information']['booking_type'] = $shipment->booking_type->booking_type;
                            $details['order_information']['booking_type_id'] = $shipment->booking_type_id;
    
                            if ($shipment->booking_type_id != 4) {
                                $details['order_information']['amount'] = number_format($shipment->amount);
                            } else {
                                if ($shipment->charges_mode_id == 1) {
                                    $details['order_information']['amount'] = 0;
                                } else {
                                    $details['order_information']['amount'] = number_format($shipment->amount);
                                }
                            }
    
                            $details['order_information']['parcel_value'] = number_format($shipment->parcel_value);
    
                            $details['order_information']['account_type_id'] = $shipment->user->account_type_id;
    
                            $details['order_information']['charges_mode_id'] = $shipment->charges_mode_id;
    
                            if ($shipment->charges_mode_id) {
                                $details['order_information']['charges_mode'] = $shipment->charges_mode->charges_mode;
                            }
    
                            $details['order_information']['instructions'] = $shipment->special_instructions;
                            $details['order_information']['pieces'] = $shipment->pieces;
                            $details['order_information']['business_category'] = $shipment->business_category->name;
                            $manifest_bag_seal_number = 0;
                            $dws_details = DwsDetail::where('shipment_id', $shipment->id);
                            if ($dws_details->exists()) {
                                $dws_details = $dws_details->get()->first();
                                $machine_name = ' (' . $dws_details->dws_machine . ') ';
                            } else {
                                $machine_name = '';
                            }
                            foreach ($shipment->shipment_journey as $journey) {
                                $journey_details = array();
                                $journey_details['image_audio_location'] = '';
                                $journey_details['date_time'] = Carbon::parse($journey->created_at)->toDateTimeString();
                                $journey_details['status'] = $journey->shipment_status_shipper->name;
                                $journey_details['shipper_status_id'] = $journey->shipment_status_shipper->id;
                                if (in_array($journey->shipment_status_shipper->id, [7, 8, 9, 12, 15, 18, 14, 30, 37, 56])) {
                                    $rider_delivery = RiderDelivery::where('shipment_id', $shipment->id)->where('delivery_note_id', $journey->reference_1_id)->where('rider_status_id', $journey->shipper_status_id)->where('rider_status_reason_id', $journey->status_reason_id);
                                    if ($rider_delivery->exists()) {
                                        $rider_delivery = $rider_delivery->get()->first();
                                        if ($rider_delivery->picture_path != null) {
                                            $exists = Storage::disk('public')->exists($rider_delivery->picture_path);
                                            if ($exists) {
                                                $journey_details['image_audio_location'] = '<button type="button" class="btn btn-sm btn-outline-info align-middle picture p-0" data-link="' . asset(Storage::url($rider_delivery->picture_path)) . '"><i class=><i class="la la-lg la-image"></i></button>';
                                            }else {
                                                $image = Storage::disk('s3')->temporaryUrl($rider_delivery->picture_path, now()->addMinutes(5));
                                                $journey_details['image_audio_location'] .= '| <button type="button" class="btn btn-sm btn-outline-info align-middle picture p-0" data-link="' . $image . '" target="_blank"><i class="la la-lg la-image"></i></button>';
                                            }
                                        }
                                        if ($rider_delivery->audio_path != null) {
                                            $exists = Storage::disk('public')->exists($rider_delivery->audio_path);
                                            if ($exists) {
                                                $journey_details['image_audio_location'] .= '| <button type="button" class="btn btn-sm btn-outline-info align-middle picture p-0" data-link="' . asset(Storage::url($rider_delivery->audio_path)) . '"><i class="la la-file-sound-o"></i></button>';
                                            } else {
                                                $sound = Storage::disk('s3')->temporaryUrl($rider_delivery->audio_path, now()->addMinutes(5));
                                                $journey_details['image_audio_location'] .= '| <button type="button" class="btn btn-sm btn-outline-info align-middle picture p-0" data-link="' . $sound . '" target="_blank"><i class="la la-file-sound-o"></i></button>';
                                            }
                                        }
                                        if ($rider_delivery->actual_location_latitude != null && $rider_delivery->actual_location_longitude != null) {
                                            $journey_details['image_audio_location'] .= '| <a type="button" class="btn btn-sm btn-outline-info align-middle location p-0" href="https://www.google.com/maps/search/?api=1&query=' . $rider_delivery->actual_location_latitude . ',' . $rider_delivery->actual_location_longitude . '" target="_blank"><i class="la la-map-marker"></i></a></div>';
                                        }
                                    } else {
                                        $journey_details['image_audio_location'] = '-';
                                    }
                                }
                                else if (in_array($journey->shipment_status_shipper->id, [47, 24, 48, 60, 25, 31, 38])) {
                                    $rider_return_deliveries = RiderReturnDelivery::where('shipment_id', $shipment->id)->where('return_note_id', $journey->reference_1_id)->where('rider_status_id', $journey->shipper_status_id)->where('rider_status_reason_id', $journey->status_reason_id);
                                    if ($rider_return_deliveries->exists()) {
                                        $rider_return_deliveries = $rider_return_deliveries->get()->first();
                                        if ($rider_return_deliveries->picture_path != null) {
                                            $exists = Storage::disk('public')->exists($rider_return_deliveries->picture_path);
                                            if ($exists) {
                                                $journey_details['image_audio_location'] = '<button type="button" class="btn btn-sm btn-outline-info align-middle picture p-0" data-link="' . asset(Storage::url($rider_return_deliveries->picture_path)) . '"><i class=><i class="la la-lg la-image"></i></button>';
                                            }
                                            else {
                                                $image = Storage::disk('s3')->temporaryUrl($rider_return_deliveries->picture_path, now()->addMinutes(5));
                                                $journey_details['image_audio_location'] .= '| <button type="button" class="btn btn-sm btn-outline-info align-middle picture p-0" data-link="' . $image . '" target="_blank"><i class="la la-lg la-image"></i></button>';
                                            }
                                        }
                                        if ($rider_return_deliveries->audio_path != null) {
                                            $exists = Storage::disk('public')->exists($rider_return_deliveries->audio_path);
                                            if ($exists) {
                                                $journey_details['image_audio_location'] .= '| <button type="button" class="btn btn-sm btn-outline-info align-middle picture p-0" data-link="' . asset(Storage::url($rider_return_deliveries->audio_path)) . '"><i class="la la-file-sound-o"></i></button>';
                                            } else {
                                                $sound = Storage::disk('s3')->temporaryUrl($rider_return_deliveries->audio_path, now()->addMinutes(5));
                                                $journey_details['image_audio_location'] .= '| <button type="button" class="btn btn-sm btn-outline-info align-middle picture p-0" data-link="' . $sound . '" target="_blank"><i class="la la-file-sound-o"></i></button>';
                                            }
                                        }
                                        if ($rider_return_deliveries->actual_location_latitude != null && $rider_return_deliveries->actual_location_longitude != null) {
    
                                            $journey_details['image_audio_location'] .= '| <a type="button" class="btn btn-sm btn-outline-info align-middle location p-0" href="https://www.google.com/maps/search/?api=1&query=' . $rider_return_deliveries->actual_location_latitude . ',' . $rider_return_deliveries->actual_location_longitude . '" target="_blank"><i class="la la-map-marker"></i></a></div>';
                                        }
                                    } else {
                                        $journey_details['image_audio_location'] = '-';
                                    }
                                } else {
                                    $journey_details['image_audio_location'] = '-';
                                }
    
                                $journey_details['status_id'] = $journey->shipper_status_id;
                                if (in_array($journey->shipper_status_id, [1])) {
                                    if ($shipment->booked_by == 1) {
                                        $journey_details['status'] .= ' (Main User)';
                                    } else if ($shipment->booked_by == 2) {
                                        $journey_details['status'] .= ' (Substitute User)';
                                    }
                                }
                                $cargo_bag_shipment = CargoManifestBagShipments::where('shipment_id', $shipment->id);
    
                                if ($journey->reference_1_id && !in_array($journey->shipper_status_id, [1, 52])) {
                                    if ($journey->shipper_status_id == 3 || $journey->shipper_status_id == 21) {
                                        $bag_shipment = BagShipment::where('shipment_id', $shipment->id);
    
    
                                        if ($bag_shipment->exists()) {
                                            $bag_shipment = $bag_shipment->first();
                                            $bag = Bag::find($bag_shipment->bag_id);
                                            $master_cargo_bags = MasterCargoBag::where('bag_id', $bag->id);
                                            if ($master_cargo_bags->exists()) {
                                                $journey_details['status'] .= ' (<button class="btn btn-sm btn-outline-info align-middle cargo_note_print" data-id="' . $bag->seal_number . '">' . $bag->seal_number . '</button>';
                                            } else {
                                                $journey_details['status'] .= ' (<button class="btn btn-sm btn-outline-info align-middle cargo_note_print" data-id="' . $bag->seal_number . '" disabled>' . $bag->seal_number . '</button>';
                                            }
                                        } else if ($cargo_bag_shipment->exists()) {
                                            $bag_shipment = $cargo_bag_shipment->orderBy('id', 'desc')->skip($manifest_bag_seal_number)->take(1)->first();
                                            if ($bag_shipment) {
                                                $manifest_bag_seal_number++;
                                                $bag = CargoManifestBag::where('id',$bag_shipment->cargo_manifest_bag_id);
                                                if($bag->exists()){
                                                    $bag= $bag->latest()->first();
                                                    $cargo_manifest = ManifestBag::where('cargo_manifest_bag_id',$bag->id);
                                                    if($cargo_manifest->exists()){
    
                                                        $journey_details['status'] .= ' (<button class="btn btn-sm btn-outline-info align-middle cargo_note_print" data-id="' . $bag->seal_number . '">' . $bag->seal_number . '</button>';
                                                    } else {
                                                        $journey_details['status'] .= ' (<button class="btn btn-sm btn-outline-info align-middle cargo_note_print" data-id="' . $bag->seal_number . '" disabled>' . $bag->seal_number . '</button>';
                                                    }
                                                }
                                            }
                                        } else {
    
                                            $journey_details['status'] .= ' (<button class="btn btn-sm btn-outline-info align-middle cargo_note_print" data-id="' . $journey->reference_1_id . '">' . $journey->reference_1_id . '</button>';
                                        }
                                    } elseif (in_array($journey->shipper_status_id, [21, 26, 32])) {
    
                                        if ($cargo_bag_shipment->exists()) {
                                            $bag_shipment = $cargo_bag_shipment->orderBy('id', 'desc')->skip($manifest_bag_seal_number)->take(1)->first();
                                            $manifest_bag_seal_number++;
                                            $bag = CargoManifestBag::find($bag_shipment->cargo_manifest_bag_id);
                                            $cargo_manifest = ManifestBag::where('cargo_manifest_bag_id', $bag->id);
                                            if ($cargo_manifest->exists()) {
    
                                                $journey_details['status'] .= ' (<button class="btn btn-sm btn-outline-info align-middle cargo_note_print" data-id="' . $bag->seal_number . '">' . $bag->seal_number . '</button>';
                                            } else {
                                                $journey_details['status'] .= ' (<button class="btn btn-sm btn-outline-info align-middle cargo_note_print" data-id="' . $bag->seal_number . '" disabled>' . $bag->seal_number . '</button>';
                                            }
                                        } else {
                                            $journey_details['status'] .= ' (<button class="btn btn-sm btn-outline-info align-middle cargo_note_print" data-id="' . $journey->reference_1_id . '">' . str_pad($journey->reference_1_id, 6, '0', STR_PAD_LEFT) . '</button>';
    
                                        }
                                    } else {
                                        if (in_array($journey->shipper_status_id, [23, 24, 25, 28, 29, 31, 44, 45, 47, 48])) {
                                            $journey_details['status'] .= ' (<button class="btn btn-sm btn-outline-info align-middle return_note_print" data-id="' . $journey->reference_1_id . '">' . str_pad($journey->reference_1_id, 6, '0', STR_PAD_LEFT) . '</button>';
                                            if ($journey->shipper_status_id == 25 && $journey->reference_1_id) {
                                                $return_note = ReturnNote::find($journey->reference_1_id);
                                                if ($return_note && $return_note->actual_date != null) {
                                                    $journey_details['status'] .= ' | ' . Carbon::parse($return_note->actual_date)->toDateString();
                                                }
                                            }
                                        } else if (in_array($journey->shipper_status_id, [5, 6, 7, 8, 9, 11, 12, 14, 15, 18, 56, 30, 20])) {
                                            $bag = CargoManifestBag::where('seal_number', $journey->reference_1_id);
                                            if ($journey->shipper_status_id == 11 && $bag->exists()) {
                                                $bag= $bag->latest()->first();
                                                $cargo_manifest = ManifestBag::where('cargo_manifest_bag_id', $bag->id);
                                                if ($cargo_manifest->exists()) {
    
                                                    $journey_details['status'] .= ' (<button class="btn btn-sm btn-outline-info align-middle cargo_note_print" data-id="' . $bag->seal_number . '">' . $bag->seal_number . '</button>';
                                                } else {
                                                    $journey_details['status'] .= ' (<button class="btn btn-sm btn-outline-info align-middle cargo_note_print" data-id="' . $bag->seal_number . '" disabled>' . $bag->seal_number . '</button>';
                                                }
                                            } else {
                                                $journey_details['status'] .= ' (<button class="btn btn-sm btn-outline-info align-middle delivery_note_print" data-id="' . $journey->reference_1_id . '">' . str_pad($journey->reference_1_id, 6, '0', STR_PAD_LEFT) . '</button>';
                                            }
                                        } else {
                                            $journey_details['status'] .= ' (' . str_pad($journey->reference_1_id, 6, '0', STR_PAD_LEFT);
                                        }
                                        
    
                                        if ($journey->reference_2_id) {
                                            if (in_array($journey->shipper_status_id, [5, 23, 28, 34])) {
                                                $rider = Rider::find($journey->reference_2_id);
                                                if ($rider) {
                                                    $note = "";
                                                    if($journey->reference_1_id)
                                                    {
                                                        $note = $journey->reference_1_id;
                                                    }
                                                    $journey_details['status'] .= ' | <button class="btn btn-sm btn-outline-info align-middle rider_information" data-id="' . $rider->id . '" data-showRiderRespone="1" data-note="'.$note.'">' . $rider->name . '</button>';
                                                }
    
                                            } else {
                                                $journey_details['status'] .= ' | ' . str_pad($journey->reference_2_id, 6, '0', STR_PAD_LEFT);
                                            }
                                        }
                                    }
    
                                    $journey_details['status'] .= ')';
                                }
                                $user = '';
                                if ($journey->admin_id) {
                                    $user = $journey->admin->name;
                                } else if ($journey->user_id) {
                                    $user = $journey->user->name;
                                }
                                if ($journey->shipper_status_id == 2) {
                                    $user = $user . $machine_name;
    
                                }
                                if(in_array($journey->shipper_status_id, [1])){
    
                                    $replacement_image = ShipmentReplacementParcelImage::where('shipment_id',$journey->shipment_id);
                                    if($replacement_image->exists()){
                                        $replacement_image = $replacement_image->first();
                                        $journey_details['image_audio_location'] = '<button class="btn btn-sm btn-outline-info align-middle replacement_booked_image" data-link="' . asset(Storage::url($replacement_image->picture_path)).'" data-id="' . $journey->shipment_id . '"><i class=><i class="la la-lg la-image"></i></button>';
                                    }
                                }
                                if(in_array($journey->shipper_status_id, [30])){
                                    $replacement_image2 = RiderDelivery::where('shipment_id',$journey->shipment_id)->where('rider_status_id',14);
                                    if($replacement_image2->exists()){
                                        $replacement_image2 = $replacement_image2->first();
                                        if($replacement_image2->replacement_image != null){
    
                                            $journey_details['image_audio_location'] = '<button class="btn btn-sm btn-outline-info align-middle replacement_collected_image" data-link="' . asset(Storage::url($replacement_image2->replacement_image)).'" data-id="' . $journey->shipment_id . '"><i class=><i class="la la-lg la-image"></i></button>';
                                        }
    
                                    }
                                }
                                if($journey->shipper_status_id == 12){
                                    $rc_app = RiderDelivery::where('shipment_id',$journey->shipment_id)->where('delivery_note_id', $journey->reference_1_id)->where('rider_status_id', 12)->where('rider_status_reason_id', 8)->where('otp_entered', 1);
                                    if($rc_app->exists()){
                                        $journey_details['image_audio_location'] .= '<i class="la la-check-square primary"></i>';
    
                                    }
                                }
                                $journey_details['status_reason'] = ($journey->status_reason_id) ? $journey->shipment_status_reason->name : NULL;
                                $journey_details['remarks'] = ($journey->remarks) ? $journey->remarks : '';
                                $journey_details['user'] = $user;
                                $journey_details['city'] = ($journey->city_id) ? $journey->city->name : '';;
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
                                $journey_details['ip'] = ($journey->ip_address) ? $journey->ip_address : '';
                                $journey_details['rider'] = ($journey->rider_id) ? $journey->rider->name : '';
    
                                $details['tracking_history'][] = $journey_details;
                            }
    
                            $shipment_payment_journey = $shipment->shipment_payment_journey;
    
    
                            if ($shipment_payment_journey) {
    
                                foreach ($shipment_payment_journey as $journey) {
                                    $journey_details = array();
                                    if ($shipment->shipment_type == 1) {
                                        $payment = DonePaymentShipment::where('shipment_id', $shipment->id)->first();
                                    } else {
                                        $payment = RetailDonePaymentShipment::where('shipment_id', $shipment->id)->first();
                                    }
    
                                    $journey_details['date_time'] = Carbon::parse($journey->created_at)->toDateTimeString();
                                    if ($journey->payment_id == null) {
                                        $journey_details['status'] = $journey->status->name;
                                    } else {
                                        $journey_details['status'] = $journey->status->name . ' (<button class="btn btn-sm btn-outline-info align-middle payment_print" data-shipment_type="' . $shipment->shipment_type . '" data-id="' . $journey->payment_id . '">' . str_pad($journey->payment_id, 6, '0', STR_PAD_LEFT) . '</button>)';
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
                                    if ($journey->reason_id != NULL) {
                                        $journey_details['reason'] = $journey->reason->name;
                                    } else {
                                        $journey_details['reason'] = '';
                                    }
    
                                    if ($journey->reference_1_id) {
                                        $journey_details['status'] .= ' (' . str_pad($journey->reference_1_id, 6, '0', STR_PAD_LEFT);
    
                                        if ($journey->reference_2_id) {
                                            if ($journey->status_id == 2) {
                                                $rider = Rider::find($journey->reference_2_id);
                                                if ($rider) {
                                                    $journey_details['status'] .= ' | <button class="btn btn-sm btn-outline-info align-middle rider_information" data-id="' . $rider->id . '">' . $rider->name . '</button>';
                                                }
    
                                            } else {
                                                $journey_details['status'] .= ' | ' . str_pad($journey->reference_2_id, 6, '0', STR_PAD_LEFT);
                                            }
                                        }
    
                                        $journey_details['status'] .= ')';
                                    }
    
                                    $admin = $journey->admin;
    
                                    if ($admin) {
                                        $journey_details['user'] = $admin->name;
                                    } else {
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
                                                if ($rider) {
                                                    $journey_details['status'] .= ' | <button class="btn btn-sm btn-outline-info align-middle rider_information" data-id="' . $rider->id . '">' . $rider->name . '</button>';
                                                }
    
                                            } else {
                                                $journey_details['status'] .= ' | ' . str_pad($journey->reference_2_id, 6, '0', STR_PAD_LEFT);
                                            }
                                        }
    
                                        $journey_details['status'] .= ')';
                                    }
    
                                    $admin = $journey->admin;
    
                                    if ($admin) {
                                        $journey_details['user'] = $admin->name;
                                    } else {
                                        $journey_details['user'] = '';
                                    }
    
                                    $details['old_pickup_history'][] = $journey_details;
                                }
                            }
    
                            $handover_shipment_journey = $shipment->handover_shipments_journeys;
    
                            if ($handover_shipment_journey) {
                                foreach ($handover_shipment_journey as $journey) {
                                    $journey_details = array();
    
                                    $journey_details['handover_id'] = $journey->handover_id;
                                    $journey_details['status'] = $journey->my_status->name;
                                    // $journey_details['status'] = "adf>name";
                                    $journey_details['created_at'] = Carbon::parse($journey->created_at)->toDateTimeString();
    
                                    $details['handover_history'][] = $journey_details;
                                }
                            }
    
                            $quick_receiving_shipment_journey = DeliveryShipmentsReceivedOperation::where('shipment_id', $shipment->id);
    
                            if ($quick_receiving_shipment_journey->exists()) {
                                $quick_receiving_shipment_journey = $quick_receiving_shipment_journey->get();
                                foreach ($quick_receiving_shipment_journey as $quick_receiving_journey) {
                                    $journey_details = array();
    
                                    $journey_details['delivery_note_id'] = '<button class="btn btn-sm btn-outline-info align-middle delivery_note_print" data-id="' . $quick_receiving_journey->delivery_note_id . '">' . str_pad($quick_receiving_journey->delivery_note_id, 6, '0', STR_PAD_LEFT) . '</button>';
                                    $journey_details['received_by'] = $quick_receiving_journey->admin->name;
                                    $journey_details['created_at'] = Carbon::parse($quick_receiving_journey->created_at)->toDateTimeString();
    
                                    $details['quick_receiving_shipments_journeys'][] = $journey_details;
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
    
                            $resolved_outstanding_shipments = ResolvedOutstandingShipment::where('shipment_id', $shipment->id);
                            if ($resolved_outstanding_shipments->exists()) {
                                $resolved_outstanding_shipments = $resolved_outstanding_shipments->get();
                                $outstanding_details = array();
                                foreach ($resolved_outstanding_shipments as $resolved_outstanding_shipment) {
                                    $outstanding_details['date_time'] = Carbon::parse($resolved_outstanding_shipment->created_at)->toDateTimeString();
                                    $outstanding_details['resolved_by'] = $resolved_outstanding_shipment->admin->name;
                                    $details['outstanding_history'][] = $outstanding_details;
                                }
                            }
    
                            $complain = CrmRequest::where('shipment_id', $shipment->id)->whereIn('status_id', [2, 3, 5]);
    
                            if ($complain->exists()) {
                                $complain = $complain->first();
    
                                $details['complain'] = array();
                                $details['complain']['id'] = $complain->id;
                                $details['complain']['padded_id'] = str_pad($complain->id, 6, '0', STR_PAD_LEFT);
    
                                Carbon::setWeekendDays([
                                    Carbon::SUNDAY,
                                ]);
    
                                $re_open_count = CrmRequestStatusHistory::where('crm_request_id', $complain->id)->where('status_id', 5)->latest('id');
                                if ($re_open_count->exists()) {
                                    $re_open_count = $re_open_count->first();
    
                                    $launched = Carbon::parse($re_open_count->created_at);
                                    $last_closed = CrmRequestStatusHistory::where('crm_request_id', $complain->id)->where('status_id', 4)->where('created_at', '>=', $re_open_count->created_at)->first();
                                    if ($last_closed) {
                                        $current = $last_closed->created_at;
                                    } else {
                                        $current = Carbon::now();
                                    }
                                    $time_format = 'H:i';
                                    $time_from = CrmSettings::where('name', 'TAT Cut-Off Time From')->first();
                                    $time_to = CrmSettings::where('name', 'TAT Cut-Off Time To')->first();
                                    $to_formatted = date($time_format, strtotime($time_to->setting_value));
                                    $cut_off_check = $complain->created_at->format($time_format);
                                    $additional_tat = $current->diffInWeekdays($launched);
                                    $current_tat = $additional_tat;
                                    $launched_check = $launched->toDateString();
                                    $current_check = $current->toDateString();
                                    if ($launched_check <= $current_check) {
                                        if ($to_formatted < $cut_off_check) {
                                            $after_cut_off = $current_tat - 1;
                                            $current_tat = $after_cut_off;
                                        }
                                    }
                                    $holidays = CrmTatHolidays::whereBetween('holiday', [$launched, $current])->get();
                                    foreach ($holidays as $holiday) {
                                        $holiday_formatted = date('Y-m-d H:i:s', strtotime($holiday->holiday));
                                        $holiday_formatted_check = date('Y-m-d', strtotime($holiday->holiday));
                                        $launched_formatted_check = date('Y-m-d', strtotime($launched));
                                        if ($launched < $holiday_formatted || $current > $holiday_formatted) {
                                            if ($holiday_formatted_check == $launched_formatted_check) {
                                                if ($to_formatted < $cut_off_check) {
                                                    $after_cut_off = $current_tat + 1;
                                                    $current_tat = $after_cut_off;
                                                }
                                            }
                                            $after_holidays = $current_tat - 1;
                                            $current_tat = $after_holidays;
                                        }
                                    }
                                } else {
                                    $launched = Carbon::parse($complain->created_at);
                                    $first_closed = CrmRequestStatusHistory::where('crm_request_id', $complain->id)->where('status_id', 4)->first();
                                    if ($first_closed) {
                                        $current = $first_closed->created_at;
                                    } else {
                                        $current = Carbon::now();
                                    }
                                    $time_format = 'H:i';
                                    $time_from = CrmSettings::where('name', 'TAT Cut-Off Time From')->first();
                                    $time_to = CrmSettings::where('name', 'TAT Cut-Off Time To')->first();
                                    $to_formatted = date($time_format, strtotime($time_to->setting_value));
                                    $cut_off_check = $complain->created_at->format($time_format);
                                    $current_tat = $current->diffInWeekdays($launched);
                                    $launched_check = $launched->toDateString();
                                    $current_check = $current->toDateString();
                                    if ($launched_check <= $current_check) {
                                        if ($to_formatted < $cut_off_check) {
                                            $after_cut_off = $current_tat - 1;
                                            $current_tat = $after_cut_off;
                                        }
                                    }
                                    $holidays = CrmTatHolidays::whereBetween('holiday', [$launched, $current])->get();
                                    foreach ($holidays as $holiday) {
                                        $holiday_formatted = date('Y-m-d H:i:s', strtotime($holiday->holiday));
                                        $holiday_formatted_check = date('Y-m-d', strtotime($holiday->holiday));
                                        $launched_formatted_check = date('Y-m-d', strtotime($launched));
                                        if ($launched < $holiday_formatted || $current > $holiday_formatted) {
                                            if ($holiday_formatted_check == $launched_formatted_check) {
                                                if ($to_formatted < $cut_off_check) {
                                                    $after_cut_off = $current_tat + 1;
                                                    $current_tat = $after_cut_off;
                                                }
                                            }
                                            $after_holidays = $current_tat - 1;
                                            $current_tat = $after_holidays;
                                        }
                                    }
                                }
    
                                $details['complain']['tat'] = $current_tat;
                            }
    
                            $crm_requests = CrmRequest::leftjoin('crm_request_status_histories as crsh', 'crsh.crm_request_id', '=', 'crm_requests.id')
                                ->leftjoin('admins as a', 'a.id', '=', 'crm_requests.agent_id')
                                ->leftjoin('users as u', 'u.id', '=', 'crm_requests.launched_by_id')
                                ->leftjoin('retail_users as ru', 'ru.id', '=', 'crm_requests.launched_by_id')
                                ->leftjoin('consignee_users as cu', 'cu.id', '=', 'crm_requests.launched_by_id')
                                ->leftjoin('substitute_users as su', 'su.id', '=', 'crm_requests.launched_by_id')
                                ->leftjoin('crm_request_statuses as crs', 'crs.id', '=', 'crsh.status_id')
                                ->select('crm_requests.id as id', 'crs.name as status', 'a.id as admin_id',
                                    'a.name as created_by_admin', 'u.name as created_by_user', 'su.name as created_by_sub_user',
                                    'crsh.created_at as created_at', 'crsh.status_id as status_id', 'crm_requests.launched_by as launched_added_by', 'crm_requests.launched_by_id', 'ru.name as created_by_retail_user', 'cu.name as created_by_consignee_user')
                                ->where('crm_requests.shipment_id', $shipment->id);
    
                            if ($crm_requests->exists()) {
                                $crm_requests = $crm_requests->get();
    
                                foreach ($crm_requests as $crm_request) {
                                    $crm_request_journey = array();
    
                                    $crm_request_journey['id'] = str_pad($crm_request->id, 6, '0', STR_PAD_LEFT);
                                    $crm_request_journey['status_id'] = $crm_request->status_id;
                                    $crm_request_journey['status'] = $crm_request->status;
                                    if ($crm_request->status_id == 1) {
                                        if ($crm_request->launched_added_by == 0) {
                                            $crm_request_journey['created_by'] = $crm_request->created_by_admin . ' (Admin)';
                                        } else if ($crm_request->launched_added_by == 1) {
                                            $crm_request_journey['created_by'] = $crm_request->created_by_user . ' (Shipper)';
                                        } else if ($crm_request->launched_added_by == 2) {
                                            $crm_request_journey['created_by'] = $crm_request->created_by_sub_user . ' (Substitute Shipper)';
                                        } else if ($crm_request->launched_added_by == 3) {
                                            $crm_request_journey['created_by'] = $crm_request->created_by_retail_user . ' (Retail User)';
                                        } else if ($crm_request->launched_added_by == 4) {
                                            $crm_request_journey['created_by'] = $crm_request->created_by_consignee_user . ' (Consignee)';
                                        }
                                    } else {
                                        $crm_request_journey['created_by'] = $crm_request->created_by_admin . ' (Admin)';
                                    }
                                    $crm_request_journey['created_at'] = Carbon::parse($crm_request->created_at)->toDateTimeString();
    
                                    $details['crm_requests'][] = $crm_request_journey;
                                }
    
                            }
    
                            $shipment_open_box_journey = $shipment->open_box_journey;
                            if ($shipment_open_box_journey) {
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
                            if ($request->has('key_accounts')) {
                                $date = Carbon::today()->toDateString();
                                $key_accounts_daily_summary = KeyAccountDailySummary::where('admin_id', Auth::id())->whereDate('created_at', $date);
                                if ($key_accounts_daily_summary->exists()) {
                                    $key_accounts_daily_summary = $key_accounts_daily_summary->first();
                                    $count = $key_accounts_daily_summary->count + 1;
                                    $key_accounts_daily_summary->count = $count;
                                    $key_accounts_daily_summary->save();
                                } else {
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
    
                            $details['shipment_id'] = $shipment->id;
                            $ship_details = ShipmentDetail::where('shipment_id', $shipment->id);
                            if ($ship_details->exists()) {
    
                                if ($shipment->shipment_detail->dws_image != null) {
                                    $details['dws_image'] = Storage::url($shipment->shipment_detail->dws_image);
                                } else {
                                    $details['dws_image'] = $shipment->shipment_detail->dws_image;
                                }
                            }
    
    
                            $tracking['shipments'][] = $details;
                        } else {
                            $tracking['unauthorized'][] = $tracking_number;
                        }
                    } else {
                        $tracking['invalid'][] = $tracking_number;
                    }
                } else {
                    $tracking['invalid'][] = $tracking_number;
                }
            }
    
            return $tracking;
        }


    }
}
