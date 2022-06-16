<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Admins\AdminFinanceController;
use App\Http\Controllers\Admins\ShipmentChargesController;
use App\Http\Controllers\CRM\CRMController;
use App\Http\Models\Admin\Fleet;
use App\Http\Models\Admin\FtlRequest;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Admin\NonServiceArea;
use App\Http\Models\BookingType;
use App\Http\Models\ChargesModes;
use App\Http\Models\City;
use App\Http\Models\CityDelivery;
use App\Http\Models\CorporateDefaultRateStatus;
use App\Http\Models\CorporateDeliveryTypeStatus;
use App\Http\Models\CorporateRateStatus;
use App\Http\Models\CRM\CrmRequest;
use App\Http\Models\CRM\CrmRequestCaseNature;
use App\Http\Models\CRM\CrmRequestCaseNatureType;
use App\Http\Models\DeliveryType;
use App\Http\Models\DistributionProduct;
use App\Http\Models\EmployeeNotificationHistory;
use App\Http\Models\InterceptReBookRequest;
use App\Http\Models\InterceptReBookRequestHistory;
use App\Http\Models\PaymentMode;
use App\Http\Models\Product;
use App\Http\Models\RateStatus;
use App\Http\Models\RestrictedCityIntercept;
use App\Http\Models\ReturnAssignedShipmentLogs;
use App\Http\Models\ReturnAssignedShipments;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentReplacementParcelImage;
use App\Http\Models\ShipmentsJourney;
use App\Http\Models\Shipper\ShipperAirWaybillSettings;
use App\Http\Models\Shipper\User;
use App\Http\Models\Shipper\UserShippingInfo;
use App\Http\Models\ShipperShipmentsSubscription;
use App\Http\Models\ShippingMode;
use App\Http\Models\ShippingModeSameDayTiming;
use App\Http\Models\V2Pickup\V2PickupRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
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
                    } else {
                        $api_token = uniqid(base64_encode(str_random(60)));

                        $shipper->api_token = $api_token;

                        $shipper->save();

                        $information['api_token'] = $api_token;
                    }
                    $information['account_type'] = $shipper->account_type_id;
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

    public function shipper_subscription_list(Request $request)
    {
        $shipper_id = $request->shipper_id;
        $subscription_list = ShipperShipmentsSubscription::join('shipments as s', 's.id', '=', 'shipper_shipments_subscriptions.shipment_id')
            ->join('shipment_status as ss', 's.shipper_status_id', '=', 'ss.id')
            ->where('shipper_shipments_subscriptions.shipper_id', $shipper_id)
            ->select('s.id as shipment_id', 'ss.name as shipment_status', 's.tracking_number as tracking_no');
        if ($subscription_list->exists()) {
            $subscription_list = $subscription_list->get();
            return response()->json(['status' => 0, 'information' => $subscription_list]);
        }
        return response()->json(['status' => 1, 'message' => "No Shipment Found"]);
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
                ->where('shipment_id', $request->shipment_id)->delete();
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
        return response()->json(['status' => 1, 'message' => "No Notification Found"]);
    }

    public function add_request_index(Request $request)
    {
        $case_nature = CrmRequestCaseNature::get();
        $case_nature_type_complaints = CrmRequestCaseNatureType::where('nature_id', '=', 1)->where('status_id', 1)->get();
        $case_nature_type_service_requests = CrmRequestCaseNatureType::where('nature_id', '=', 2)->where('status_id', 1)->get();
        $case_nature_type_claims = CrmRequestCaseNatureType::where('nature_id', '=', 4)->where('status_id', 1)->get();
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

    public function lost_claim(Request $request)
    {
        $shipment = Shipment::find($request->shipment_id);
        if ($shipment) {
            $receiving_sheet_id = -1;
            if ($shipment->receiving_sheet_shipment) {
                $receiving_sheet_id = $shipment->receiving_sheet_shipment->receiving_sheet_id;
                return response()->json(['status' => 0, 'receiving_sheet' => [['id' => strval($receiving_sheet_id)]]]);
            }
            return response()->json(['status' => 0, 'receiving_sheet' => [['id' => strval($receiving_sheet_id)]], 'error_message' => 'Receiving Sheet does not exists']);
        }
        return response()->json(['status' => 1, 'message' => 'No Shipments Found']);
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

    public function confirmation_pending_list(Request $request)
    {

        $rules = [
            'shipper_id' => ['required', 'digits_between:1,10', 'exists:users,id']
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $shipments = Shipment::join('users as u', 'shipments.user_id', '=', 'u.id')
                ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
                ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
                ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
                ->join('cities as h', 'dc.hub_id', '=', 'h.id')
                ->join('shipping_modes as sm', 'sm.id', '=', 'shipments.shipping_mode_id')
                ->join('booking_types as bt', 'bt.id', '=', 'shipments.booking_type_id')
                ->join('shipment_status as ss', 'ss.id', '=', 'shipments.shipper_status_id')
                ->leftJoin('shipments_journey', function ($join) {
                    $join->on('shipments_journey.shipment_id', '=', 'shipments.id')
                        ->where('shipments_journey.created_at', '=',
                            DB::raw('(select max(created_at) from shipments_journey where shipments_journey.shipment_id = shipments.id)'));
                })
                ->leftJoin('shipments_journey as sj', function ($join) {
                    $join->on('sj.shipment_id', '=', 'shipments.id')
                        ->where('sj.created_at', '=',
                            DB::raw('(select max(created_at) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)'));
                })
                ->leftJoin('shipment_status_reason as ssr', 'ssr.id', '=', 'shipments_journey.status_reason_id')
                ->leftjoin('consolidation_shipments as consolidations', function ($join) {
                    $join->on('consolidations.shipment_id', '=', 'shipments.id')
                        ->where('consolidations.consolidation_id', '=',
                            DB::raw('(select consolidation_id from consolidation_shipments where consolidation_shipments.shipment_id = shipments.id)'));
                })
                ->select('shipments.id as shId', 'shipments.tracking_number', 'shipments.tracking_number as tracking', 'u.name as shipper', 'u.phone as shipper_phone1', 'u.phone2 as shipper_phone2', 'oc.name as origin', 'dc.name as destination', 'shipments.order_id', 'h.name as hub', 'shipments.consignee_name', 'shipments.consignee_phone_number_1', 'shipments.consignee_phone_number_2', 'shipments.consignee_address', 'shipments.amount', 'sm.mode', 'bt.booking_type as service_type', 'ss.name as status', 'shipments_journey.remarks as remarks', 'ssr.id as reason_id', 'ssr.name as reason', 'shipments_journey.created_at as status_date', 'shipments_journey.created_at as last_status_date', 'sj.created_at as arrival', 'shipments.shipper_status_id as shipper_status_id', 'shipments_journey.shipper_status_id as journey_shipper_status_id', 'dc.pickup as pickup', 'shipments.intercepted as intercepted', 'shipments.nsa_osa_estimated_charges', 'consolidations.consolidation_id')
                ->where('shipments.shipper_status_id', DB::raw(12))
                ->where('shipments.user_id', $request->shipper_id)
                ->groupBy('shipments.id');

            if ($shipments->exists()) {
                $shipments = $shipments->get();
                return response()->json(['status' => 0, 'data' => $shipments, 'message' => 'Shipments Found!']);

            } else {
                return response()->json(['status' => 1, 'message' => 'No Shipments Found']);
            }
        }


        /*return Datatables::of($shipments)
            ->setRowAttr([
                'class' => function ($shipments) {
                    if ($shipments->reason_id == 12) {
                        return 'nsa_osa_reason';
                    }
                },
                'consolidation_id' => function ($shipments) {
                    if ($shipments->consolidation_id != null) {
                        return $shipments->consolidation_id;
                    } else {
                        return '';
                    }
                }
            ])
            ->editColumn('tracking_number',function ($shipments){
                $route = route('cod.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->editColumn('amount', function($shipment){
                return number_format($shipment->amount);
            })
            ->editColumn('consignee_phone', function ($shipments) {
                return '<button type="button" class="btn btn-sm btn-outline-info align-middle consignee_info_label" rel="'. $shipments->consignee_phone_number_1 .'"><i class="la la-lg la-phone align-middle"></i> <span class="align-middle">' . $shipments->consignee_phone_number_1 . '|' . $shipments->consignee_phone_number_2 .'</span></button>';
            })
            ->filterColumn('consignee_phone',function ($query,$keyword){
                $keyword = strtolower($keyword);
                if ($keyword != '') {
                    $query->where('shipments.consignee_phone_number_1', 'like', '%'.$keyword.'%')->orWhere('shipments.consignee_phone_number_2', 'like', '%'.$keyword.'%');
                }

                else {
                    $query->whereRaw('false');
                }
            })
            ->addColumn('shipment_remarks',function ($shipments){
                $remark = '<textarea style="width:200px;" placeholder="Enter Remarks" class="form-control form-control-sm" rows="4" cols="100" >'.$shipments->remarks.'</textarea>';
                return $remark;
            })
            ->orderColumn('consignee_phone', 'shipments.consignee_phone_number_1 $1, shipments.consignee_phone_number_2 $1')
            ->editColumn('arrival',function($shipments){
                if($shipments->arrival){
                    return $shipments->arrival;
                }else{
                    return " - ";
                }
            })
            ->filterColumn('status',function ($query,$keyword){

                if ($keyword != '') {
                    $query->where('ss.id',$keyword);
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->addColumn('consolidation', function ($shipments) {
                $consolidations = ShipperReturnController::check_consolidation($shipments->shId);
                $consol = '';
                if ($consolidations) {
                    $consol = $consolidations['order'] . '/' . $consolidations['count'];
                } else {
                    $consol = '-';
                }
                return $consol;
            })
            ->addColumn('consolidated_id', function ($shipments) {
                if ($shipments->consolidation_id) {
                    return $shipments->consolidation_id;
                } else {
                    return '-';
                }
            })
            ->addColumn("action", function ($result) {
                $confirm_button = '<a href="javascript:void(0);" class="dropdown-item returnMarkStatus" data-action="confirm"><i class="ft-plus-circle primary"></i> Confirm</a>';
                $reattempt_button = '<a href="javascript:void(0);" class="dropdown-item returnReattemptStatus"><i class="ft-plus-circle primary"></i> Re-Attempt Request</a>';
                $intercept = '<a href="javascript:void(0);" class="dropdown-item intercept"><i class="ft-plus-circle primary"></i> Intercept/Re-Book</a>';
                $self_collection_button = '<a href="javascript:void(0);" class="dropdown-item selfCollection" data-action="selfCollection"><i class="ft-plus-circle primary"></i> Mark for Self Collection</a>';

                $dropdown = "
                        <div class='btn-group'>
                            <button type='button' class='btn btn-sm btn-success dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>Actions</button>
                            <div class='dropdown-menu dropdown-menu-sm'>";
                if (!$result->consolidation_id) {
                    if($result->shipper_status_id != 52){
                        $dropdown .= $confirm_button;
                        $dropdown .= $reattempt_button;
                    }
                    if (($result->shipper_status_id == 12 || $result->shipper_status_id == 52) && $result->journey_shipper_status_id != 53 && $result->intercepted == 0) {
                        $dropdown .= $intercept;
                    }
                }



                if($result->reason_id == 12){
                    $dropdown .= $self_collection_button;
                }


                $dropdown .= "
                            </div>
                        </div>
                    ";

                return $dropdown;

            })
            ->make(true);*/
    }

    public function mark_return_confirm(Request $request)
    {
        $rules = [
            'shipment_id' => ['required', 'digits_between:1,10', 'exists:shipments,id']
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $parcel = Shipment::find($request->shipment_id);
            $shipper_id = $request->shipper_id;
            if ($parcel) {
                if (!in_array($parcel->shipper_status_id, [20, 52])) {

                    if (!$parcel->packaging_material_request) {
                        Shipment::where('id', $request->shipment_id)->update(['shipper_status_id' => 20, 'consignee_status_id' => 20]);
                        $shipment_history = ShipmentsJourney::where('shipment_id', $request->shipment_id)->latest()->first();
                        ShipmentChargesController::return($request->shipment_id);

                        AdminFinanceController::add_payment($request->shipment_id, 1);
                        ShipmentsJourneyController::add($request->shipment_id, 20, 20, $shipment_history->status_reason_id, $request->remark, $shipper_id, NULL);

                    } else {
                        Shipment::where('id', $request->shipment_id)->update(['shipper_status_id' => 17, 'consignee_status_id' => 17]);
                        $shipment_history = ShipmentsJourney::where('shipment_id', $request->shipment_id)->latest()->first();
                        ShipmentsJourneyController::add($request->shipment_id, 17, 17, $shipment_history->status_reason_id, NULL, $shipper_id, NULL);
                    }
                    $return_assign_shipment = ReturnAssignedShipments::where('shipment_id', $request->shipment_id);
                    if ($return_assign_shipment->exists()) {
                        $return_assign_shipment = $return_assign_shipment->latest()->first();
                        $return_assign_shipment->status = 0;
                        $return_assign_shipment->save();

                        $return_assign_log = new ReturnAssignedShipmentLogs();
                        $return_assign_log->return_assign_shipment_id = $return_assign_shipment->id;
                        $return_assign_log->status = 2;
                        $return_assign_log->assigned_by = $shipper_id;
                        $return_assign_log->save();
                    }

                    return response()->json(['status' => 0, 'message' => "Shipment successfully marked as Shipment - Return Confirm"]);

                }
                return response()->json(['status' => 1, 'message' => "Status already marked"]);
            }
            return response()->json(['status' => 1, 'message' => "Invalid Shipment"]);
        }

    }

    public function mark_reattempt(Request $request)
    {
        $rules = [
            'shipment_id' => ['required', 'digits_between:1,10', 'exists:shipments,id']
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $parcel = Shipment::find($request->shipment_id);
            $shipper_id = $request->shipper_id;
            if ($parcel) {
                if ($parcel->shipper_status_id != 52) {
                    if ($parcel->shipper_status_id == 12) {
                        $journey = ShipmentsJourney::where('shipment_id', $request->shipment_id)->where('shipper_status_id', 12)->where('status_reason_id', 12)->latest('id')->first();
                        Shipment::where('id', $request->shipment_id)->update(['shipper_status_id' => 52, 'consignee_status_id' => 52]);

                        $last_reason = ShipmentsJourney::where('shipment_id', $parcel->id)->orderBy('id', 'DESC');
                        if ($last_reason->exists()) {
                            $last_reason = $last_reason->first();
                            $last_reason_id = $last_reason->status_reason_id;
                        } else {
                            $last_reason_id = NULL;
                        }
                        ShipmentsJourneyController::add($request->shipment_id, 52, 52, $last_reason_id, $request->remark, $shipper_id, NULL, NULL);

                        $return_assign_shipment = ReturnAssignedShipments::where('shipment_id', $request->shipment_id)->latest()->first();
                        if ($return_assign_shipment) {
                            $return_assign_shipment->status = 0;
                            $return_assign_shipment->save();

                            $return_assign_log = new ReturnAssignedShipmentLogs();
                            $return_assign_log->return_assign_shipment_id = $return_assign_shipment->id;
                            $return_assign_log->status = 5;
                            $return_assign_log->assigned_by = $shipper_id;
                            $return_assign_log->save();
                        }

                        if ($journey) {
                            NotificationsController::send(33, $request->shipment_id);
                        }

                        return response()->json(['status' => 0, 'message' => "Shipment has been requested for Re-Attempt"]);
                    } else {
                        return response()->json(['status' => 1, 'message' => "Status is already marked"]);
                    }
                }
                return response()->json(['status' => 1, 'message' => "Status is already marked"]);
            }
            return response()->json(['status' => 1, 'message' => "Invalid Shipment"]);
        }
    }

    public function intercept_re_book_index(Request $request)
    {
        $rules = [
            'shipment_id' => ['required', 'digits_between:1,10', 'exists:shipments,id']
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $shipment_id = $request->shipment_id;
            $shipment = Shipment::find($shipment_id);
            if ($shipment) {
                if ($shipment->shipping_mode_id == 2) {
                    $restricted_cities = RestrictedCityIntercept::pluck('city_id')->toArray();
                    $consignee_cities = Shipment::leftjoin('city_deliveries as cd', 'cd.booking_type_id', '=', 'shipments.booking_type_id')
                        ->leftjoin('cities as c', 'c.id', '=', 'cd.city_id')
                        ->select('c.id as id', 'c.name as name')
                        ->where('shipments.id', $shipment_id)
                        ->where('c.status', 1)
                        ->where('cd.shipping_mode_id', 2)
                        ->whereNotNull('c.zone_id')
                        ->whereNotIn('c.id', $restricted_cities);
                } else {
                    $consignee_cities = Shipment::leftjoin('city_deliveries as cd', 'cd.booking_type_id', '=', 'shipments.booking_type_id')
                        ->leftjoin('cities as c', 'c.id', '=', 'cd.city_id')
                        ->select('c.id as id', 'c.name as name')
                        ->where('shipments.id', $shipment_id)
                        ->where('c.status', 1)
                        ->whereNotNull('c.zone_id');
                }
                $consignee_cities = $consignee_cities->orderBy('c.name')
                    ->groupBy('c.name')
                    ->get();
                return response()->json(['status' => 0, 'message' => 'Shipment Found!', 'shipment' => $shipment, 'consignee_cities' => $consignee_cities]);
            }
            return response()->json(['status' => 1, 'message', 'Shipment not found!']);
        }

    }

    public function intercept_re_book_submit(Request $request)
    {
        $rules = [
            'shipment_id' => ['required', 'digits_between:1,10', 'exists:shipments,id'],
            'consignee_city' => ['required', 'digits_between:1,10', 'exists:cities,id'],
            'consignee_name' => ['required'],
            'consignee_address' => ['required'],
            'consignee_phone_number_1' => ['required'],
            'consignee_phone_number_2' => ['nullable'],
            'consignee_email' => ['nullable', 'email'],
            'amount' => ['required'],
            'replacement_parcel_image' => ['nullable', 'mimes:png,jpeg,jpg'],
            'consignee' => ['required'], //1 for different consignee //2 for same consignee
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $amount = intval($request->amount);
            $shipment = Shipment::find($request->shipment_id);
            $user_id = $request->shipper_id;
            $intercept_type = $request->consignee;

            $shipment_status = $shipment->status_shipper->name;

            if ($shipment->shipper_status_id == 12) {
                if ($shipment->consignee_city_id != $request->consignee_city || $shipment->consignee_name != $request->consignee_name || $shipment->consignee_address != $request->consignee_address || $shipment->consignee_phone_number_1 != substr_replace($request->consignee_phone_number_1, '-', 4, 0) || $shipment->consignee_phone_number_2 != ($request->consignee_phone_number_2) ? substr_replace($request->consignee_phone_number_2, '-', 4, 0) : NULL || $shipment->consignee_email != $request->consignee_email || $shipment->amount != $amount) {
                    if ($shipment['intercepted'] == 1) {
                        return response()->json(['status' => 1, 'message' => 'Intercept/Re-Book is already requested against Tracking Number: ' . $shipment['tracking_number']]);
                    } else {
                        $amount = intval($request->amount);
                        if ($intercept_type == 1) {
                            InterceptReBookRequest::create([
                                'shipment_id' => $request->shipment_id,
                                'consignee_city_id' => $request->consignee_city,
                                'consignee_name' => $request->consignee_name,
                                'consignee_address' => $request->consignee_address,
                                'consignee_phone_number_1' => substr_replace($request->consignee_phone_number_1, '-', 4, 0),
                                'consignee_phone_number_2' => substr_replace($request->consignee_phone_number_2, '-', 4, 0),
                                'consignee_email' => $request->consignee_email,
                                'amount' => $amount,
                                'shipper_id' => $user_id,
                                'status' => 0,
                                'intercept_type' => $intercept_type,
                                'admin_id' => NULL
                            ]);
                            $shipment->consignee_status_id = 54;
                            $shipment->shipper_status_id = 54;
                            $shipment->intercepted = 1;
                            $shipment->save();

                            ShipmentsJourneyController::add($request->shipment_id, 54, 54, NULL, NULL, $user_id, NULL);
                        } else {
                            InterceptReBookRequestHistory::create([
                                'shipment_id' => $request->shipment_id,
                                'old_consignee_city_id' => $shipment->consignee_city_id,
                                'new_consignee_city_id' => $request->consignee_city,
                                'old_consignee_name' => $shipment->consignee_name,
                                'new_consignee_name' => $request->consignee_name,
                                'old_consignee_address' => $shipment->consignee_address,
                                'new_consignee_address' => $request->consignee_address,
                                'old_consignee_phone_number_1' => $shipment->consignee_phone_number_1,
                                'new_consignee_phone_number_1' => substr_replace($request->consignee_phone_number_1, '-', 4, 0),
                                'old_consignee_phone_number_2' => $shipment->consignee_phone_number_2,
                                'new_consignee_phone_number_2' => substr_replace($request->consignee_phone_number_2, '-', 4, 0),
                                'old_consignee_email' => $shipment->consignee_email,
                                'new_consignee_email' => $request->consignee_email,
                                'old_amount' => $shipment->amount,
                                'new_amount' => $amount,
                                'shipper_id' => $user_id,
                            ]);
                            $shipment->consignee_status_id = 55;
                            $shipment->shipper_status_id = 55;
                            $shipment->intercepted = 1;
                            $shipment->save();

                            ShipmentsJourneyController::add($request->shipment_id, 55, 55, NULL, NULL, $user_id, NULL);

                            if ($request->hasFile('replacement_parcel_image')) {
                                $shipment_parcel_image = ShipmentReplacementParcelImage::where('shipment_id', $request->shipment_id);
                                if ($shipment_parcel_image->exists()) {
                                    $shipment_parcel_image = $shipment_parcel_image->first();
                                    Storage::disk('public')->delete($shipment_parcel_image->picture_path);
                                } else {
                                    $shipment_parcel_image = new ShipmentReplacementParcelImage();
                                    $shipment_parcel_image->shipment_id = $request->shipment_id;
                                }
                                $time = Carbon::now()->toDateString();
                                $picture_path = 'replacement_parcel/' . $request->shipment_id . '_' . $time . '.png';
                                Storage::disk('public')->put($picture_path, file_get_contents($request->replacement_parcel_image));
                                $shipment_parcel_image->picture_path = $picture_path;
                                $shipment_parcel_image->save();
                            }

                        }
                        return response()->json(['status' => 0, 'message' => 'Intercept/Re-Book request submitted against Tracking Number: ' . $shipment['tracking_number']]);
                    }
                } else {
                    return response()->json(['status' => 1, 'message' => 'Shipment is already book with same details against Tracking Number: ' . $shipment['tracking_number']]);
                }
            } else {
                return response()->json(['status' => 1, 'message' => 'Shipment is already updated with Status : ' . $shipment_status . ' against Tracking Number: ' . $shipment['tracking_number']]);
            }
        }
    }

    public function booking_types(Request $request)
    {
        $shipper = User::find($request->shipper_id);
        if ($shipper) {
            if ($shipper->account_type_id == 1) {
                $booking_types = BookingType::whereNotIn('id', [4, 6])->get();
            } elseif ($shipper->account_type_id == 2) {
                $booking_types = BookingType::whereNotIn('id', [4])->get();
            } else {
                return response()->json(['status' => 1, 'message' => 'Invalid Account Type']);
            }
            return response()->json(['status' => 0, 'message' => 'Booking Types Found!', 'booking_types' => $booking_types]);
        }
        return response()->json(['status' => 1, 'message' => 'Invalid Shipper']);
    }

    public function corporate_index(Request $request)
    {
        $user_id = $request->shipper_id;
        $date = Carbon::today();
        $user = User::find($user_id);
        $booking_types = BookingType::where('id', '!=', 4)->get();
        $user_shipping_address = UserShippingInfo::join('cities as c', 'c.id', '=', 'user_shipping_infos.city_id')
            ->where('user_shipping_infos.user_id', $user_id)->where('user_shipping_infos.status', 1)
            ->select('user_shipping_infos.*', 'c.name as city_name')
            ->get();
        $multi_piece = $user->multipiece_status;
        $cities = City::where('pickup', 1)->where('status', 1)->where('business_category_id', 1)->whereNotNull('zone_id')->orderBy('name')->get();
        if (in_array($user_id, [5982, 3324, 10104, 14110, 16292])) {
            $consignee_cities = City::where('status', 1)->where('business_category_id', 1)->whereNotNull('zone_id')->orderBy('name')->get();
        } else {
            $consignee_cities = City::where('id', '!=', 1244)->where('status', 1)->where('business_category_id', 1)->whereNotNull('zone_id')->orderBy('name')->get();
        }
        $products = Product::orderBy('product_name')->get();
        $distribution_products = DistributionProduct::orderBy('name')->get();
        $shipping_mode_same_day_timings = ShippingModeSameDayTiming::all();

        $ccd_booking = GlobalSettings::where('type', 'ccd_booking');
        if ($ccd_booking->exists()) {
            $ccd_booking = $ccd_booking->first();
            $ccd_account_tags = array_map('intval', explode(',', $ccd_booking->text));
            if (!in_array($user_id, $ccd_account_tags)) {
                $payment_modes = PaymentMode::whereNotIn('id', [2, 3])->get();
            } else {
                $payment_modes = PaymentMode::whereNotIn('id', [3])->get();
            }
        } else {
            $payment_modes = PaymentMode::whereNotIn('id', [2, 3])->get();
        }
        $user_delivery_types = CorporateDeliveryTypeStatus::where('user_id', $user_id)->pluck('shipping_mode_id')->toArray();
        $delivery_type = DeliveryType::orderBy('delivery_type')->get();
        $charges_modes = ChargesModes::whereIn('id', [3])->get();
        $check = NonServiceArea::pluck('name')->toArray();
        $air_waybill = ShipperAirWaybillSettings::where('user_id', $user_id);
        if ($air_waybill->exists()) {
            $air_waybill = $air_waybill->first();
        } else {
            $air_waybill = null;
        }
        $approve_ftl_requests = FtlRequest::where('shipper_id', $user_id)->where('status_id', 3)->get();
        $omni_user = 0;
        $settings = GlobalSettings::where('type', 'omni_users');
        if ($settings->exists()) {
            $settings = $settings->first();
            if ($settings->text != NULL) {
                $omni_accounts = array_map('intval', explode(',', $settings->text));
                if (in_array($user_id, $omni_accounts)) {
                    $omni_user = 1;
                }
            }
        }
        $ftl_collection_type = [['id' => 1, 'type' => 'Invoice'],['id' => 2, 'type' => 'Cash']];
        return response()->json(['status' => 0, 'shipping_address' => $user_shipping_address, 'multi_piece' => $multi_piece, 'user' => $user, 'cities' => $cities, 'distribution_products' => $distribution_products, 'products' => $products, 'shipping_mode_same_day_timings' => $shipping_mode_same_day_timings, 'payment_modes' => $payment_modes, 'consignee_cities' => $consignee_cities, 'check' => $check, 'delivery_type' => $delivery_type, 'charges_modes' => $charges_modes, 'date' => $date, 'air_waybill' => $air_waybill, 'user_delivery_types' => $user_delivery_types, 'approve_ftl_requests' => $approve_ftl_requests, 'omni_user' => $omni_user, 'booking_types' => $booking_types, 'ftl_collection_type' => $ftl_collection_type]);
    }

    public function corporate_shipping_modes(Request $request)
    {
        $rules = [
            'service_type_id' => ['required', 'digits_between:1,10'],
            'pickup_city_id' => ['required', 'digits_between:1,10', 'exists:cities,id'],
            'consignee_city_id' => ['required', 'digits_between:1,10', 'exists:cities,id'],
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $user_id = $request->shipper_id;
            $corporate_rate_type_id = User::find($user_id)->corporate_rate_type_id;
            if ($corporate_rate_type_id != 3) {
                $shipper_shipping_modes = CorporateRateStatus::where('user_id', $user_id)->where('status', 1);
                $user = User::where('id', $user_id)->first();
            } else {
                $shipper_shipping_modes = CorporateDefaultRateStatus::where('user_id', $user_id)->where('status', 1);
                $user = User::where('id', $user_id)->first();
            }

            if ($shipper_shipping_modes->exists()) {
                $shipper_shipping_modes = $shipper_shipping_modes->pluck('shipping_mode_id')->toArray();

                $city_shipping_modes = CityDelivery::where('city_id', $request->consignee_city_id)->where('booking_type_id', $request->service_type_id)->whereIn('shipping_mode_id', $shipper_shipping_modes);

                if ($city_shipping_modes->exists()) {
                    $city_shipping_modes = $city_shipping_modes->pluck('shipping_mode_id')->toArray();

                    if ($request->pickup_city_id != $request->consignee_city_id) {
                        $city_shipping_modes = array_diff($city_shipping_modes, [4]);
                    }

                    if (!empty($city_shipping_modes)) {
                        $shipping_modes = ShippingMode::whereIn('id', $city_shipping_modes)->get();

                        return response()->json(['status' => 0, 'message' => 'Shipping Modes Updated', 'shipping_modes' => $shipping_modes, 'default_shipping_mode' => $user['default_shipping_mode']]);
                    } else {
                        return response()->json(['status' => 1, 'message' => 'No Shipping Modes Enabled for Selected Service Type, Pickup City and Consignee City']);
                    }
                } else {
                    return response()->json(['status' => 1, 'message' => 'No Shipping Modes Enabled for Selected Service Type and Consignee City']);
                }
            } else {
                return response()->json(['status' => 1, 'message' => 'No Shipping Modes has been Enabled for you']);
            }
        }
    }

    public function get_ftl_info(Request $request)
    {
        $rules = [
            'ftl_id' => ['required', 'digits_between:1,10', 'exists:ftl_requests,id'],
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $shipper_id = $request->shipper_id;
            $ftl_request = FtlRequest::where('id', $request->ftl_id)->where('shipper_id', $shipper_id);
            if($ftl_request->exists()){
                $ftl_request = $ftl_request->first();
                $data = array('origin_id' => $ftl_request->origin_id, 'destination_id' => $ftl_request->destination_id, 'weight' => $ftl_request->weight, 'quantity' => $ftl_request->quantity, 'total_charges' => $ftl_request->total_charges);
                return response()->json(['status' => 0, 'message'=> "FTL Found",'data' => $data]);
            } else{
                return response()->json(['status' => 1, 'message' => "No FTL Found!"]);
            }

        }
    }

    public function reimbursement_index(Request $request)
    {
        $date = Carbon::today();
        $user_id = $request->shipper_id;
        $user = User::find($user_id);
        $booking_types = BookingType::whereNotIn('id', [4, 6])->get();
        $user_shipping_address = UserShippingInfo::join('cities as c', 'c.id', '=', 'user_shipping_infos.city_id')
            ->where('user_shipping_infos.user_id', $user_id)->where('user_shipping_infos.status', 1)
            ->select('user_shipping_infos.*', 'c.name as city_name')
            ->get();
        $multi_piece = $user->multipiece_status;
        $cities = City::where('pickup', 1)->where('status', 1)->where('business_category_id', 1)->whereNotNull('zone_id')->orderBy('name')->get();
        if (in_array($user_id, [5982, 3324, 10104, 14110, 16292])) {
            $consignee_cities = City::where('status', 1)->where('business_category_id', 1)->whereNotNull('zone_id')->orderBy('name')->get();
        } else {
            $consignee_cities = City::where('id', '!=', 1244)->where('status', 1)->where('business_category_id', 1)->whereNotNull('zone_id')->orderBy('name')->get();
        }
        $products = Product::orderBy('product_name')->get();
        $shipping_mode_same_day_timings = ShippingModeSameDayTiming::all();
        $ccd_booking = GlobalSettings::where('type', 'ccd_booking');
        if ($ccd_booking->exists()) {
            $ccd_booking = $ccd_booking->first();
            $ccd_account_tags = array_map('intval', explode(',', $ccd_booking->text));
            if (!in_array($user_id, $ccd_account_tags)) {
                $payment_modes = PaymentMode::whereNotIn('id', [2, 3])->get();
            } else {
                $payment_modes = PaymentMode::whereNotIn('id', [3])->get();
            }
        } else {
            $payment_modes = PaymentMode::whereNotIn('id', [2, 3])->get();
        }
        $check = NonServiceArea::pluck('name')->toArray();
        $charges_modes = ChargesModes::whereIn('id', [4])->get();
        $air_waybill = ShipperAirWaybillSettings::where('user_id', $user_id);
        if ($air_waybill->exists()) {
            $air_waybill = $air_waybill->first();
        } else {
            $air_waybill = null;
        }

        $omni_user = 0;
        $settings = GlobalSettings::where('type', 'omni_users');
        if ($settings->exists()) {
            $settings = $settings->first();
            if ($settings->text != NULL) {
                $omni_accounts = array_map('intval', explode(',', $settings->text));
                if (in_array($user_id, $omni_accounts)) {
                    $omni_user = 1;
                }
            }
        }

        return response()->json(['status' => 0, 'shipping_address' => $user_shipping_address, 'user' => $user, 'multi_piece' => $multi_piece, 'cities' => $cities, 'products' => $products, 'shipping_mode_same_day_timings' => $shipping_mode_same_day_timings, 'payment_modes' => $payment_modes, 'consignee_cities' => $consignee_cities, 'check' => $check, 'charges_modes' => $charges_modes, 'date' => $date, 'air_waybill' => $air_waybill, 'omni_user' => $omni_user, 'booking_types' => $booking_types]);
    }

    public function reimbursement_shipping_modes(Request $request)
    {
        $rules = [
            'service_type_id' => ['required', 'digits_between:1,10'],
            'pickup_city_id' => ['required', 'digits_between:1,10', 'exists:cities,id'],
            'consignee_city_id' => ['required', 'digits_between:1,10', 'exists:cities,id'],
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $user_id = $request->shipper_id;
            $shipper_shipping_modes = RateStatus::where('user_id', $user_id)->where('status', 1);
            $user = User::where('id', $user_id)->first();

            if ($shipper_shipping_modes->exists()) {
                $shipper_shipping_modes = $shipper_shipping_modes->pluck('shipping_mode_id')->toArray();

                $city_shipping_modes = CityDelivery::where('city_id', $request->consignee_city_id)->where('booking_type_id', $request->service_type_id)->whereIn('shipping_mode_id', $shipper_shipping_modes);

                if ($city_shipping_modes->exists()) {
                    $city_shipping_modes = $city_shipping_modes->pluck('shipping_mode_id')->toArray();

                    if ($request->pickup_city_id != $request->consignee_city_id) {
                        $city_shipping_modes = array_diff($city_shipping_modes, [4]);
                    }

                    if (!empty($city_shipping_modes)) {
                        $shipping_modes = ShippingMode::whereIn('id', $city_shipping_modes)->get();

                        return response()->json(['status' => 0, 'message' => 'Shipping Modes Updated', 'shipping_modes' => $shipping_modes, 'default_shipping_mode' => $user['default_shipping_mode']]);
                    } else {
                        return response()->json(['status' => 1, 'message' => 'No Shipping Modes Enabled for Selected Service Type, Pickup City and Consignee City']);
                    }
                } else {
                    return response()->json(['status' => 1, 'message' => 'No Shipping Modes Enabled for Selected Service Type and Consignee City']);
                }
            } else {
                return response()->json(['status' => 1, 'message' => 'No Shipping Modes has been Enabled for you']);
            }
        }
    }
}
