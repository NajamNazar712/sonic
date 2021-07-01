<?php

namespace App\Http\Controllers;

use App\Http\Models\EmployeeNotificationHistory;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentsJourney;
use App\Http\Models\Shipper\User;
use App\Http\Models\ShipperShipmentsSubscription;
use Carbon\Carbon;
use Illuminate\Http\Request;
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
                if($shipper->status == 4){
                    return response()->json(['status' => 1, 'message' => 'Your account is disabled']);
                }
                elseif ($shipper->blacklist == 1){
                    return response()->json(['status' => 1, 'message' => 'Your account is blocked']);
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
                    $shipment_info['origin'] = $shipment->consignee_city->name;
                    $shipment_info['destination'] = $shipment->pickup_address->city->name;
                    $shipment_info['shipper'] = $shipment->pickup_address->user->name;
                    $shipment_info['amount'] = $shipment->amount;
                    $shipment_info['order_id'] = $shipment->order_id;
                    $shipment_info['pickup_address'] = $shipment->pickup_address->pickup_address;
                    $shipment_info['weight'] = ($shipment->actual_weight) ? $shipment->actual_weight : $shipment->estimated_weight;

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
                    return response()->json(['status' => 1, 'message' => "Following Tracking Number don't belong to you : ".$request->tracking_no]);
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
}
