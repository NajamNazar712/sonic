<?php

namespace App\Http\Controllers;

use App\Http\Models\ConsigneeOtp;
use App\Http\Models\ConsigneeInfo;
use App\Http\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Validator;
use DB;

class ConsigneeAPIController extends Controller
{
    private $names = [
        'phone_number' => 'Phone Number',
        'pin' => 'PIN',
        'tracking_no' => 'Tracking Number',

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

    public function consignee_info(Request $request)
    {
        $rules = [
            'tracking_no' => ['required']
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $shipment_info = Shipment::where('tracking_number', $request->tracking_no)
                ->select('consignee_name', 'consignee_address', 'consignee_phone_number_1 as phone_number');
            if ($shipment_info->exists()) {
                $shipment_info = $shipment_info->get();
                return response()->json(['status' => 0, 'consignee_info' => $shipment_info]);
            }
            return response()->json(['status' => 1, 'message' => 'Consignee Information Not Found']);
        }
    }

    public function consignee_otp(Request $request)
    {
        $rules = [
            'phone_number' => ['required', 'regex:/^[0][0-9]{10}$/']
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $consignee_info = ConsigneeInfo::where('phone_number_1', substr_replace($request->input('phone_number'), '-', 4, 0));
            if ($consignee_info->exists()) {
                $consignee_info = $consignee_info->first();
                $otp_pin = rand(1000, 9999);
                $consignee_otp = ConsigneeOtp::where('consignee_id', $consignee_info->id);
                if ($consignee_otp->exists()) {
                    $consignee_otp = $consignee_otp->first();
                } else {
                    $consignee_otp = new ConsigneeOtp();
                    $consignee_otp->consignee_id = $consignee_info->id;
                }
                $consignee_otp->otp = bcrypt($otp_pin);
                $consignee_otp->save();
                NotificationsController::bolt_forget_pin($consignee_info->phone_number_1, $otp_pin, $consignee_info->name);
                return response()->json(['status' => 0, 'otp_message' => 'OTP has been sent to your registered number']);
            } else {
                return response()->json(['status' => 1, 'message' => 'Phone number not registered']);
            }
        }
    }

    public function consignee_otp_verification(Request $request)
    {
        $rules = [
            'phone_number' => ['required', 'regex:/^[0][0-9]{10}$/'],
            'otp' => ['required', 'digits:4']
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $consignee_info = ConsigneeInfo::where('phone_number_1', substr_replace($request->input('phone_number'), '-', 4, 0));
            if ($consignee_info->exists()) {
                $consignee_info = $consignee_info->first();
                $consignee_otp = ConsigneeOtp::where('consignee_id', $consignee_info->id);
                if ($consignee_otp->exists()) {
                    $consignee_otp = $consignee_otp->first();
                    if (Hash::check($request->input('otp'), $consignee_otp->otp)) {
                        return response()->json(['status' => 0, 'message' => 'OTP has been verified']);
                    } else {
                        return response()->json(['status' => 0, 'message' => 'Invalid OTP']);
                    }
                } else {
                    return response()->json(['status' => 0, 'message' => 'Invalid OTP']);
                }
            } else {
                return response()->json(['status' => 1, 'message' => 'Phone number not registered']);
            }
        }
    }

    public function consignee_signup(Request $request)
    {
        $rules = [
            'phone_number' => ['required', 'regex:/^[0][0-9]{10}$/'],
            'pin' => ['required', 'integer', 'digits:4'],
            'name' => ['required'],
            'address' => ['required'],
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $consignee_info = ConsigneeInfo::where('phone_number_1', substr_replace($request->input('phone_number'), '-', 4, 0));
            if ($consignee_info->exists()) {
                $consignee_info = $consignee_info->first();
                if($consignee_info->pin){
                    return response()->json(['status' => 1, 'message' => 'Account Already Exist']);
                }else{
                    $consignee_info->name = $request->name;
                    $consignee_info->address = $request->address;
                    $consignee_info->pin = bcrypt($request->pin);
                    return response()->json(['status' => 1, 'message' => 'Account has been created']);
                }
            } else {
                return response()->json(['status' => 1, 'message' => 'Phone number not registered']);
            }
        }
    }
}
