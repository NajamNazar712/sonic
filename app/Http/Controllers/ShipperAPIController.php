<?php

namespace App\Http\Controllers;

use App\Http\Controllers\CRM\CRMController;
use App\Http\Models\Admin\Fleet;
use App\Http\Models\City;
use App\Http\Models\CRM\CrmRequest;
use App\Http\Models\CRM\CrmRequestCaseNature;
use App\Http\Models\CRM\CrmRequestCaseNatureType;
use App\Http\Models\CRM\CrmRequestChannel;
use App\Http\Models\DonePayment;
use App\Http\Models\DonePaymentShipment;
use App\Http\Models\EmployeeNotificationHistory;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentsJourney;
use App\Http\Models\Shipper\User;
use App\Http\Models\ShipperShipmentsSubscription;
use App\Http\Models\V2Pickup\V2PickupRequest;
use App\Http\Models\V2Pickup\V2PickupRequestShipment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Validator;

class ShipperAPIController extends Controller
{
    private $names = [
        'email_address' => 'Email Address',
        'password' => 'Password',

        'rider_location_latitude' => 'Rider Location Latitude',
        'rider_location_longtidue' => 'Rider Location Longitude',

        'added_at' => 'Added At',
        'pickup_note_id' => 'Pickup Note ID',
        'pickup_request_id' => 'Pickup Request ID',
        'actual_location_latitude' => 'Location Latitude',
        'actual_location_longtidue' => 'Location Longitude',
        'start_location_latitude' => 'Location Latitude',
        'start_location_longitude' => 'Location Longitude',

        'shipments' => 'Shipments',
        'tracking_no' => 'Tracking Number',

        'reason_id' => 'Reason ID',
        'picture' => 'Picture',

        'delivery_note_id' => 'Delivery Note ID',
        'receiver_name' => 'Receiver Name',
        'cnic' => 'CNIC',

        'shipper_status_id' => 'Shipper Status ID',
        'status_reason_id' => 'Status Reason ID',
        'remarks' => 'Remarks',

        'actions' => 'Actions',
        'actions.*' => 'Action',
        'actions.*.logged_at' => 'Logged At',
        'actions.*.type_id' => 'Type ID',
        'actions.*.pickup_note_id' => 'Pickup Note ID',
        'actions.*.pickup_request_id' => 'Pickup Request ID',

        'from_date' => 'From Date'
    ];

    private $messages = [
        'phone_number.regex' => ':attribute format is Invalid, required Format is: 0300-0000000.',
        'integer' => ':attribute must be an Integer.',
        'digits' => ':attribute must be of :digits Digits.',
        'exists' => 'Given :attribute is of Invalid ID.',
        'actual_location_latitude.regex' => ':attribute is Invalid Latitude Coordinates.',
        'actual_location_longitude.regex' => ':attribute is Invalid Longitude Coordinates.',
        'start_location_latitude.regex' => ':attribute is Invalid Latitude Coordinates.',
        'start_location_longitude.regex' => ':attribute is Invalid Longitude Coordinates.',
        'image' => ':attribute must be an Image.',
        'rider_location_latitude.regex' => ':attribute is Invalid Latitude Coordinates.',
        'rider_location_longitude.regex' => ':attribute is Invalid Longitude Coordinates.',
    ];

    public function login(Request $request)
    {
        $rules = [
            'email_address' => ['required', 'email'],
            'password' => ['required', 'min:6']
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $shipper = User::where('email', $request->email_address);
            if ($shipper->exists()) {
                $shipper = $shipper->first();
                if ($shipper->blacklist) {
                    return response()->json(['status' => 1, 'message' => 'Your Account is Blacklisted, Contact Admin']);
                } else if ($shipper->status != 3) {
                    return response()->json(['status' => 1, 'message' => 'Your Account is Not Activated Yet, Contact Admin']);
                } else if ($shipper->phone_number_verified == 0) {
                    return response()->json(['status' => 1, 'message' => 'Your Account phone number is not verified, Contact Admin']);
                }
                if (Hash::check($request->input('password'), $shipper->password)) {
                    $information = array();
                    $information['name'] = $shipper->name;
                    $information['shipper_id'] = $shipper->id;
                    $information['phone_number'] = $shipper->phone;
                    if ($shipper->api_token) {
                        $information['api_token'] = $shipper->api_token;
                    }
                    else {
                        $api_token = uniqid(base64_encode(str_random(60)));

                        $shipper->api_token = $api_token;

                        $shipper->save();

                        $information['api_token'] = $api_token;
                    }
                    return response()->json(['status' => 0, 'message' => 'Login Successful', 'information' => $information]);
                } else {
                    return response()->json(['status' => 1, 'message' => 'Invalid Credentials']);
                }
            } else {
                return response()->json(['status' => 1, 'message' => 'Invalid Credentials']);
            }
        }
    }

    public function shipment_history(Request $request)
    {
        $rules = [
            'tracking_no' => ['required']
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $shipment = Shipment::where('tracking_number', $request->tracking_no);
            $shipment_info = array();
            if ($shipment->exists()) {
                $shipment = $shipment->first();
                if ($request->shipper_id == $shipment->pickup_address->user->id) {
                    $shipment_info['tracking_no'] = $shipment->tracking_number;
                    $shipment_info['shipment_id'] = $shipment->id;
                    $shipment_info['status_id'] = $shipment->shipper_status_id;
                    $shipment_info['status'] = $shipment->status_shipper->name;
                    $shipment_info['origin'] = $shipment->pickup_address->city->name;
                    $shipment_info['destination'] = $shipment->consignee_city->name;
                    $shipment_info['shipper'] = $shipment->pickup_address->user->name;
                    $shipment_info['amount'] = $shipment->amount;
                    $shipment_info['order_id'] = $shipment->order_id;
                    $shipment_info['pickup_address'] = $shipment->pickup_address->pickup_address;
                    $shipment_info['weight'] = ($shipment->actual_weight) ? $shipment->actual_weight : $shipment->estimated_weight;
                    $shipment_info['in_route'] = $shipment->delivery_in_route;
                    $shipment_info['address'] = $shipment->consignee_address;
                    $shipment_info['track'] = 0;
                    $shipment_info['latitude'] = null;
                    $shipment_info['longitude'] = null;
                    $shipment_info['runner_id'] = null;
                    $shipment_info['pickup_request_id'] = null;
                    $pickup_address = $shipment->pickup_address;
                    if (in_array($shipment->shipper_status_id, [1, 2, 27, 33, 4, 13, 3, 26, 32, 5, 8, 29, 35, 9, 15, 7, 54, 55, 11, 49])) {
                        $shipment_info['track'] = 1;
                        if ($shipment->shipper_status_id == 5) {
                            $shipment_info['latitude'] = $shipment->consignee_latitude;
                            $shipment_info['longitude'] = $shipment->consignee_longitude;
                        } elseif ($shipment->shipper_status_id == 1) {
                            $pickup_request = V2PickupRequest::join('v2_pickup_request_shipments as prs', 'v2_pickup_requests.id', '=', 'prs.pickup_request_id')
                                ->select('v2_pickup_requests.pickup_in_route as pickup_in_route', 'v2_pickup_requests.id as id')
                                ->where('prs.shipment_id', $shipment->id);
                            $shipment_info['latitude'] = $pickup_address->location_latitude;
                            $shipment_info['longitude'] = $pickup_address->location_longitude;
                            $shipment_info['pickup_address'] = $pickup_address->pickup_address;
                            if ($pickup_request->exists()) {
                                $pickup_request = $pickup_request->first();
                                if ($pickup_request->pickup_in_route == 1) {
                                    $shipment_info['in_route'] = 2;
                                    $shipment_info['pickup_request_id'] = $pickup_request->id;
                                }
                            }
                        } elseif (in_array($shipment->shipper_status_id, [3, 49])) {
                            $shipment_info['origin'] = $pickup_address->city->name;
                            $destination_city = City::where('id', $shipment->consignee_city_id);
                            if ($destination_city->exists()) {
                                $destination_city = $destination_city->first();
                                $shipment_info['destination'] = $destination_city->name;
                                $shipment_info['latitude'] = $destination_city->location_latitude;
                                $shipment_info['longitude'] = $destination_city->location_longitude;
                            }
                            $fleet = Fleet::leftjoin('cargo_manifests as cm', 'fleets.id', '=', 'cm.vehicle_id')
                                ->leftjoin('manifest_bags as mb', 'cm.id', '=', 'mb.cargo_manifest_id')
                                ->leftjoin('cargo_manifest_bag_shipments as cs', 'mb.cargo_manifest_bag_id', '=', 'cs.cargo_manifest_bag_id')
                                ->select('fleets.tracking_id as runner_id')
                                ->where('cs.shipment_id', $shipment->id)
                                ->where('cm.status_id', 1)
                                ->where('mb.status', 0);
                            if ($fleet->exists()) {
                                $fleet = $fleet->first();
                                $shipment_info['runner_id'] = $fleet->runner_id;
                            }
                        } else {
                            $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)
                                ->where('shipper_status_id', $shipment->shipper_status_id)
                                ->orderBy('id', 'DESC');
                            if ($shipment_journey->exists()) {
                                $shipment_journey = $shipment_journey->first();
                                $city = City::where('id', $shipment_journey->city_id);
                                if ($city->exists()) {
                                    $city = $city->first();
                                    $shipment_info['latitude'] = $city->location_latitude;
                                    $shipment_info['longitude'] = $city->location_longitude;
                                }
                            }
                        }

                    }

                    if ($shipment->shipper_status_id != 14) {
                        $shipper_subscription = ShipperShipmentsSubscription::where('shipper_id', $request->shipper_id);
                        if ($shipper_subscription->count() < 5) {
                            $shipment_exists = $shipper_subscription->where('shipment_id', $shipment->id);
                            if (!$shipment_exists->exists()) {
                                $shipper_subscription_obj = new ShipperShipmentsSubscription();
                                $shipper_subscription_obj->shipper_id = $request->shipper_id;
                                $shipper_subscription_obj->shipment_id = $shipment->id;
                                $shipper_subscription_obj->save();
                            }
                        }
                    }
                    $consignee_shipments_journey = ShipmentsJourney::join('shipment_status as ss', 'shipments_journey.shipper_status_id', '=', 'ss.id')
                        ->where('shipments_journey.shipment_id', $shipment->id)
                        ->where('shipments_journey.verification', 1)
                        ->select('shipments_journey.shipper_status_id as status_id', 'ss.name as status', 'shipments_journey.created_at as created_at')
                        ->orderBy('shipments_journey.id', 'DESC');
                    if ($consignee_shipments_journey->exists()) {
                        $consignee_shipments_journey = $consignee_shipments_journey->get();
                        return response()->json(['status' => 0, 'shipment_info' => $shipment_info, 'shipment_journey' => $consignee_shipments_journey]);
                    } else {
                        return response()->json(['status' => 0, 'shipment_info' => $shipment_info, 'shipment_journey' => ""]);
                    }

                } else {
                    return response()->json(['status' => 1, 'message' => "Following Tracking Number don't belong to you : " . $request->tracking_no]);
                }
            } else {
                return response()->json(['status' => 1, 'message' => "Shipment not found"]);
            }

        }
    }

    public function shipper_subscription_list(Request $request){
        $shipper_id = $request->shipper_id;
        $subscription_list = ShipperShipmentsSubscription::join('shipments as s', 's.id', '=', 'shipper_shipments_subscriptions.shipment_id')
            ->join('shipment_status as ss', 's.shipper_status_id', '=', 'ss.id')
            ->where('shipper_shipments_subscriptions.shipper_id', $shipper_id)
            ->select('s.id as shipment_id', 'ss.name as shipment_status', 's.tracking_number as tracking_no');
        if($subscription_list->exists()){
            $subscription_list = $subscription_list->get();
            return response()->json(['status' => 0, 'information' => $subscription_list]);
        }
        return response()->json(['status' => 1, 'message' => "No Subscription Shipment Found"]);
    }

    public function shipper_subscription_delete(Request $request)
    {
        $rules = [
            'shipment_id' => ['required', 'integer', 'digits_between:1,10', 'exists:shipments,id']
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $shipper_id = $request->shipper_id;
            ShipperShipmentsSubscription::where('shipper_id', $shipper_id)
                ->where('shipment_id',$request->shipment_id)->delete();
            return response()->json(['status' => 0, 'msg' => 'Subscription Remove Successfully']);
        }
    }

    public function notification_history(Request $request)
    {
        $shipper_id = $request->shipper_id;
        $from_date = Carbon::now()->subDays(30)->format('Y-m-d 00:00:00');
        $to_date = Carbon::now()->format('Y-m-d 23:59:59');

        $notifiction_history = EmployeeNotificationHistory::where('employee_id', $shipper_id)
            ->where('employee_type_id', 3)
            ->whereBetween('created_at', [$from_date, $to_date])
            ->orderBy('created_at', 'desc');
        if ($notifiction_history->exists()) {
            $notifiction_history = $notifiction_history->get();
            return response()->json(['status' => 0, 'data' => $notifiction_history]);
        }
        return response()->json(['status' => 1, 'message' => "Notification History Not Found"]);
    }

    public function add_request_index(Request $request){
        $case_nature = CrmRequestCaseNature::get();
        $case_nature_type_complaints = CrmRequestCaseNatureType::where('nature_id', '=', 1)->where('status_id',1)->get();
        $case_nature_type_service_requests = CrmRequestCaseNatureType::where('nature_id', '=', 2)->where('status_id',1)->get();
        $case_nature_type_claims = CrmRequestCaseNatureType::where('nature_id', '=', 4)->where('status_id',1)->get();
        return response()->json(['status' => 0, 'case_nature' => $case_nature, 'complaints' => $case_nature_type_complaints, 'service_requests' => $case_nature_type_service_requests, 'claims' => $case_nature_type_claims]);
    }

    public function add_request_submit(Request $request)
    {
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
        }
        //Other Requests
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
                                    if (in_array($complaint_id, [11, 12, 13])) {
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
                                if (in_array($complaint_id, [11, 12, 13])) {
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

    public function lost_claim(Request $request){
        $shipment = Shipment::find($request->shipment_id);
        if($shipment){
            $receiving_sheet_id = -1;
            if($shipment->receiving_sheet_shipment){
                $receiving_sheet_id = $shipment->receiving_sheet_shipment->receiving_sheet_id;
                return response()->json(['status' => 0,'receiving_sheet'=>[$receiving_sheet_id]]);
            }
            return response()->json(['status' => 0,'receiving_sheet'=>[$receiving_sheet_id], 'error_message'=>'Receiving Sheet does not exists']);
        }
        return response()->json(['status' => 1,'message'=>'No Shipments Found']);
    }

    public function test(Request $request)
    {
        $shipment = Shipment::where('tracking_number', $request->tr_no)->first();
        $fleet = Fleet::leftjoin('cargo_manifests as cm', 'fleets.id', '=', 'cm.vehicle_id')
            ->leftjoin('manifest_bags as mb', 'cm.id', '=', 'mb.cargo_manifest_id')
            ->leftjoin('cargo_manifest_bag_shipments as cs', 'mb.cargo_manifest_bag_id', '=', 'cs.cargo_manifest_bag_id')
            ->select('fleets.tracking_id as runner_id')
            ->where('cs.shipment_id', $shipment->id)
            ->where('cm.status_id', 1)
            ->where('mb.status', 0);
        return response()->json(['status' => 1, 'data' => $fleet->get()]);
    }
}
