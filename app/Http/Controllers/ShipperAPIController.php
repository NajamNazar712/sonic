<?php

namespace App\Http\Controllers;

use App\Http\Models\Shipment;
use App\Http\Models\ShipmentsJourney;
use App\Http\Models\Shipper\User;
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
                if (Hash::check($request->input('password'), $shipper->password)) {
                    $information = array();
                    $information['name'] = $shipper->name;
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
            if($request->shipper_id == $shipment->pickup_address->user->id){
                $shipment_info = array();
                if ($shipment->exists()) {
                    $shipment = $shipment->first();
                    $shipment_info['tracking_no'] = $shipment->tracking_number;
                    $shipment_info['origin'] = $shipment->consignee_city->name;
                    $shipment_info['destination'] = $shipment->pickup_address->city->name;
                    $shipment_info['shipper'] = $shipment->pickup_address->user->name;
                    $shipment_info['amount'] = $shipment->amount;
                    $shipment_info['order_id'] = $shipment->order_id;
                    $shipment_info['pickup_address'] = $shipment->pickup_address->pickup_address;
                    $shipment_info['weight'] = $shipment->actual_weight;

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
                    return response()->json(['status' => 1, 'message' => "Shipment Not Found"]);
                }
            }else{
                return response()->json(['status' => 1, 'message' => "This Shipment does'nt belongs to you"]);
            }

        }
    }
}
