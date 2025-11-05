<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Admins\AdminFinanceController;
use App\Http\Controllers\Admins\ShipmentChargesController;
use App\Http\Controllers\CRM\CRMController;
use App\Http\Controllers\Shippers\ShipperShipmentBookController;
use App\Http\Models\Admin\Fleet;
use App\Http\Models\Admin\FtlRequest;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Admin\NonServiceArea;
use App\Http\Models\Admin\RcpAssignedAgent;
use App\Http\Models\Admin\RcpAssignedShipment;
use App\Http\Models\Admin\RcpAssignedShipmentLog;
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
use App\Http\Models\EmployeeDeviceToken;
use App\Http\Models\EmployeeNotificationHistory;
use App\Http\Models\InterceptReBookRequest;
use App\Http\Models\InterceptReBookRequestHistory;
use App\Http\Models\PaymentMode;
use App\Http\Models\Product;
use App\Http\Models\RateStatus;
use App\Http\Models\RestrictedCityIntercept;
use App\Http\Models\ReturnAssignedShipmentLogs;
use App\Http\Models\ReturnAssignedShipments;
use App\Http\Models\Rider\RiderReturnDelivery;
use App\Http\Models\RiderDelivery;
use App\Http\Models\RvShipmentAssignAgent;
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
use App\Http\Traits\RvTrait;
use App\Models\UserOtp;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Finder\Glob;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Http\Models\Admin\Retail\RetailShipperInfo;
use App\Models\WalletShipperSetting;
use App\Http\Models\WalletUser;
use App\Jobs\WalletSignUpLPendingRecordLogs;
use App\Http\Models\Shipper\UserBankInfo;
use App\Http\Controllers\FingaIntegrationController;


class ShipperAPIController extends Controller
{

    use RvTrait;
    private $names = [
        'email_address' => 'Email Address',
        'password' => 'Password',
        'otp' => 'Otp',
        'phone_no' => 'Phone Number',

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

        'from_date' => 'From Date',
        'address' => 'Address',
        'pickup_address_id' => 'Pickup Address ID',
        'return_address_id' => 'Retrun Address ID',
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
//        'phone_no.exists' => 'This phone number is not registered.',
        'phone_no.regex' => ':attribute format is invalid. Allowed formats are: 0342-0803886 or 9239-4343434.',
        'phone_no.required' => ':attribute is required.',
    ];

    public function login(Request $request)
    {
        $rules = [
            'email_address' => ['sometimes', 'required', 'email'],
            'phone_no'      => ['sometimes', 'required'],
            'password'      => ['required', 'min:6'],
        ];  

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {

            $app_type  = $request->has('phone_no') ? 2 : 1;

            if($app_type == 2 ) {
                $retail_user = RetailShipperInfo::where('shipper_phone_no', $request->phone_no)->first();
                if($retail_user) {
                    if (Hash::check($request->input('password'), $retail_user->password)) {
                        $information = array();
                        $information['name'] = $retail_user->shipper_name;
                        //$information['shipper_id'] = $retail_user->id;
                        $information['phone_number'] = $retail_user->shipper_phone_no;
                        $information['app_type'] = 2;
                        if ($retail_user->api_token) {
                            $information['api_token'] = $retail_user->api_token;
                        } else {
                            $api_token = uniqid(base64_encode(Str::random(60)));

                            $retail_user->api_token = $api_token;

                            $retail_user->save();

                            $information['api_token'] = $api_token;
                           
                        }
                        return response()->json(['status' => 0, 'message' => 'Login Successful', 'information' => $information]);
                    } else {
                        return response()->json(['status' => 1, 'message' => 'Invalid Credentials']);
                    }
                } else {
                    return response()->json(['status' => 1, 'message' => 'Invalid Credentials']);
                }
            }else {
                //$shipper = User::where('email', $request->email_address);
                $shipper = User::with('wallet')->where('email', $request->email_address)->first();
                if ($shipper) {
                    //$shipper = $shipper->first();
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
                        $information['shipper_email'] = $shipper->email;
                        $information['app_type'] = 1;
                        $information['wallet_sign_up_allow'] = WalletShipperSetting::where('user_id', $shipper->id)->where('status', 1)->exists() ? 1 : 0;
                        $information['wallet_user'] = ($shipper->wallet) ? 1 : 0;

                        if ($shipper->api_token) {
                            $information['api_token'] = $shipper->api_token;
                        } else {
                            $api_token = uniqid(base64_encode(Str::random(60)));

                            $shipper->api_token = $api_token;

                            $shipper->save();

                            $information['api_token'] = $api_token;
                        }
                        $information['account_type'] = $shipper->account_type_id;
                        EmployeeDeviceToken::where('employee_type_id', 2)->where('employee_id', $shipper->id)->delete();
                        return response()->json(['status' => 0, 'message' => 'Login Successful', 'information' => $information]);
                    } else {
                        return response()->json(['status' => 1, 'message' => 'Invalid Credentials']);
                    }
                } else {
                    return response()->json(['status' => 1, 'message' => 'Invalid Credentials']);
                }
            }
            
        }
    }

    public function reset_password(Request $request)
    {
        $rules = [
            'otp' => ['required','digits:6'],
            'password' => ['required', 'min:6'],
        ];
        $validate = Validator::make($request->all(), $rules, $this->messages);
        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        }

            $retail_shipper = RetailShipperInfo::where('retail_otp', $request->otp)->first();
            if($retail_shipper) {

                if ($retail_shipper->otp_expire_at < now()) {
                    return response()->json(['status' => 1, 'message' => 'OTP has expired']);
                }

                // Reset password & clear API token & otp
                $retail_shipper->password = Hash::make($request->password);
                $retail_shipper->api_token = null;
                $retail_shipper->retail_otp = null;
                $retail_shipper->otp_expire_at = null;
                $retail_shipper->save();

                return response()->json(['status' => 0, 'message' => 'Password reset successful']);

            }

            // fallback to main shipper OTP check
            $shipper_otp = UserOtp::where('otp_code',$request->otp)->first();
            if($shipper_otp) {
                if($shipper_otp->otp_expire_at < now()) {
                    return response()->json(['status' => 1, 'message' => 'OTP has expired']);
                }

                $shipper = User::where('id',$shipper_otp->user_id)->where('status',3)->first();
                if($shipper){

                    $shipper->password =  Hash::make($request->password);
                    $shipper->save();
                    $shipper_otp->delete();

                    return response()->json(['status' => 0, 'message' => 'Password reset successful']);
                }

                return response()->json(['status' => 1, 'message' => 'Inactive Shipper']);

            }

            return response()->json(['status' => 1, 'message' => 'Invalid OTP']);

    }

    public function sendOtp(Request $request)
    {
        $rules = [
            'phone_no' => ['sometimes', 'required', 'regex:/^(03\d{2}-\d{7}|92\d{2}-\d{7})$/'],
            'email' => ['sometimes', 'required', 'email'],
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);
        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        }

        $bypass_otp = 0;
        $minutes = 10;
        $otp_expire_at = null;

        if ($request->has('phone_no')) {
            $retail = RetailShipperInfo::where('shipper_phone_no', $request->phone_no)->where('status', 1)->first();
            if (!$retail) {
                return response()->json(['status' => 1, 'message' => 'Retail shipper does not exist.']);
            }

            // OTP rate limit logic
//            if ($retail->password_reset_at && $retail->password_reset_at->isToday()) {
//                if ($retail->password_reset_limit >= 3) {
//                    return response()->json([
//                        'status' => 1,
//                        'message' => 'OTP request limit exceeded for today. Please try again tomorrow.'
//                    ]);
//                }
//                $retail->password_reset_limit += 1;
//            } else {
//                $retail->password_reset_limit = 1;
//                $retail->password_reset_at = Carbon::now();
//            }

            $otp_setting = GlobalSettings::where('type', 'retail_shipper_mobile_otp')->first();
            if ($otp_setting && $otp_setting->setting_value == 1) {
                $otp = mt_rand(100000, 999999);
                $retail->retail_otp = $otp;
                $otp_expire_at = Carbon::now()->addMinutes($minutes);
                $retail->otp_expire_at = $otp_expire_at;
                $retail->save();

                 NotificationsController::send(243, $retail->id, $minutes);

            } else {
                $bypass_otp = 1;
                $retail->save(); // still save updated limit
            }
        }

        elseif ($request->has('email')) {
            $shipper = User::where('email', $request->email)->where('status', 3)->first();
            if (!$shipper) {
                return response()->json(['status' => 1, 'message' => 'shipper does not exist.']);
            }

            $otp_setting = GlobalSettings::where('type', 'shipper_mobile_otp')->first();
            if ($otp_setting && $otp_setting->setting_value == 1) {
                $otp = mt_rand(100000, 999999);
                UserOtp::where('user_id', $shipper->id)->delete();

                $user_otp = new UserOtp();
                $user_otp->user_id = $shipper->id;
                $user_otp->otp_code = $otp;
                $otp_expire_at = Carbon::now()->addMinutes($minutes);
                $user_otp->otp_expire_at = $otp_expire_at;
                $user_otp->save();

                 NotificationsController::send(248, $shipper->id, $minutes);

            } else {
                $bypass_otp = 1;
            }
        } else {
            return response()->json(['status' => 1, 'message' => 'Shipper not validate']);
        }

        return response()->json([
            'status' => 0,
            'bypass_otp' => $bypass_otp,
            'message' => 'OTP sent successfully.',
            'otp_expire_at' => $otp_expire_at ? $otp_expire_at->toDateTimeString() : null,
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $rules = [
            'otp' => ['required', 'digits:6'],
            'phone_no' => ['sometimes', 'required', 'regex:/^(03\d{2}-\d{7}|92\d{2}-\d{7})$/'],
            'email' => ['sometimes', 'required', 'email'],
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);
        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        }

        if ($request->has('phone_no')) {
            $retail_shipper = RetailShipperInfo::where('shipper_phone_no', $request->phone_no)
                ->where('retail_otp', $request->otp)
                ->first();

            if (!$retail_shipper) {
                return response()->json(['status' => 1, 'message' => 'Invalid OTP']);
            }

            if (!$retail_shipper->otp_expire_at || $retail_shipper->otp_expire_at < now()) {
                return response()->json(['status' => 1, 'message' => 'OTP has expired']);
            }

            return response()->json(['status' => 0, 'message' => 'OTP verified successfully']);
        }

        elseif ($request->has('email')) {
            $shipper = User::join('users_otp', 'users_otp.user_id', 'users.id')
                ->where('users.email', $request->email)
                ->where('users_otp.otp_code', $request->otp)
                ->select('users.id', 'users_otp.otp_expire_at')
                ->first();

            if (!$shipper) {
                return response()->json(['status' => 1, 'message' => 'Invalid OTP']);
            }

            if (!$shipper->otp_expire_at || $shipper->otp_expire_at < now()) {
                return response()->json(['status' => 1, 'message' => 'OTP has expired']);
            }



            return response()->json(['status' => 0, 'message' => 'OTP verified successfully']);
        }

        return response()->json(['status' => 1, 'message' => 'Shipper not verified.']);
    }



//    public function sendOtp(Request $request)
//    {
//
//        $rules = [
//            'phone_no' => ['sometimes','required', 'regex:/^(03\d{2}-\d{7}|92\d{2}-\d{7})$/'],
//            'email' => ['sometimes', 'required', 'email'],
//        ];
//        $validate = Validator::make($request->all(), $rules, $this->messages);
//        $validate->setAttributeNames($this->names);
//
//        if ($validate->fails()) {
//            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
//        }
//
//        if($request->has('phone_no')) {
//
//            $retail = RetailShipperInfo::where('shipper_phone_no',$request->phone_no)->where('status',1)->first();
//            if($retail) {
//
//                if ($retail->password_reset_at && $retail->password_reset_at->isToday()) {
//                    if ($retail->password_reset_limit >= 3) {
//                        return response()->json([
//                            'status' => 1,
//                            'message' => 'OTP request limit exceeded for today. Please try again tomorrow.'
//                        ]);
//                    }
//
//                    // Same day: increment limit
//                    $retail->password_reset_limit += 1;
//
//                } else {
//                    // New day: start fresh
//                    $retail->password_reset_limit = 1;
//                    $retail->password_reset_at = Carbon::now();
//                }
//
//                $bypass_otp = 0;
//                $retail_shipper_otp = GlobalSettings::where('type','retail_shipper_mobile_otp')->first();
//                if($retail_shipper_otp && $retail_shipper_otp->setting_value == 1) {
//
//                    // Generate and save OTP
//                    $minutes = 10;
//                    $otp = mt_rand(100000, 999999);
//
//                    $retail->retail_otp = $otp;
//                    $retail->otp_expire_at =  Carbon::now()->addMinutes($minutes);
//                    $retail->save();
//
//                    //send notification
////                  NotificationsController::send(243, $retail->id, $minutes);
//
//                } else {
//                    $bypass_otp = 1;
//                }
//
//
//
//                return response()->json([
//                    'status' => 0,
//                    'bypass_otp' => $bypass_otp,
//                    'message' => 'OTP sent successfully.',
//                    'otp_expire_at' => $bypass_otp ? null : $retail->otp_expire_at->toDateTimeString()
//                ]);
//            }
//            return response()->json(['status' => 1, 'message' => 'Retail shipper does not exist.']);
//
//        }
//        else if($request->has('email')) {
//
//            $shipper = User::where('email',$request->email)->where('status',3)->first();
//            if($shipper) {
//                $bypass_otp = 0;
//                $shipper_otp = GlobalSettings::where('type','shipper_mobile_otp')->first();
//                if($shipper_otp && $shipper_otp->setting_value == 1) {
//                    $minutes = 10;
//                    $otp = mt_rand(100000, 999999);
//
//                    $user_otp = UserOtp::where('user_id',$shipper->id)->delete();
//
//                    $user_otp = new UserOtp();
//                    $user_otp->user_id = $shipper->id;
//                    $user_otp->otp_code = $otp;
//                    $user_otp->otp_expire_at = Carbon::now()->addMinutes($minutes);
//                    $user_otp->save();
//
//                    //send notification
////                NotificationsController::send(246, $shipper->id, $minutes);
//
//                } else {
//                    $bypass_otp = 1;
//                }
//
//                return response()->json([
//                    'status' => 0,
//                    'bypass_otp' => $bypass_otp,
//                    'message' => 'OTP sent successfully.',
//                    'otp_expire_at' => $bypass_otp ? null : $user_otp->otp_expire_at->toDateTimeString(),
//                ]);
//
//            }
//
//            return response()->json(['status' => 1, 'message' => 'shipper does not exist.']);
//
//        }
//        return response()->json(['status' => 1, 'message' => 'Shipper not validate']);
//
//    }
//    public function verifyOtp(Request $request)
//    {
//        $rules = [
//            'otp' => ['required','digits:6'],
//            'phone_no' => ['sometimes','required', 'regex:/^(03\d{2}-\d{7}|92\d{2}-\d{7})$/'],
//            'email' => ['sometimes', 'required', 'email'],
//
//        ];
//        $validate = Validator::make($request->all(), $rules, $this->messages);
//        $validate->setAttributeNames($this->names);
//
//        if ($validate->fails()) {
//            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
//        }
//
//        $user = null;
//        if($request->has('phone_no')) {
//
//            $retail_shipper = RetailShipperInfo::where('shipper_phone_no', $request->phone_no)
//                ->where('retail_otp', $request->otp)
//                ->first();
//
//            if (!$retail_shipper) {
//                return response()->json(['status' => 1, 'message' => 'Invalid OTP']);
//            }
//
//            if (!$retail_shipper->otp_expire_at || $retail_shipper->otp_expire_at < now()) {
//                return response()->json(['status' => 1, 'message' => 'OTP has expired']);
//            }
//
//            return response()->json(['status' => 0, 'message' => 'OTP verified successfully']);
//
//
//        } else if($request->has('email')) {
//
//            $shipper = User::join('users_otp','users_otp.user_id','users.id')
//            ->where('users.email', $request->email)
//                ->where('users_otp.otp_code', $request->otp)
//                ->select('users.id', 'users_otp.otp_expire_at')
//                ->first();
//
//            if (!$shipper) {
//                return response()->json(['status' => 1, 'message' => 'Invalid OTP']);
//            }
//
//            if (!$shipper->otp_expire_at || $shipper->otp_expire_at < now()) {
//                return response()->json(['status' => 1, 'message' => 'OTP has expired']);
//            }
//
//            return response()->json(['status' => 0, 'message' => 'OTP verified successfully']);
//        }
//
//        return response()->json(['status' => 1, 'message' => 'Shipper not validate']);
//
//
//    }
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
                    $shipment_info['notification'] = 0;
                    $shipment_subscription = ShipperShipmentsSubscription::where('shipment_id', $shipment->id);
                    if($shipment_subscription->exists()){
                        $shipment_info['notification'] = 1;
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
                    return response()->json(['status' => 1, 'message' => "Following Tracking Number doesn't belong to you : " . $request->tracking_no]);
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
                                    if (in_array($complaint_id, [11, 13])) {
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
                                if (in_array($complaint_id, [11, 13])) {
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
                ->leftJoin('shipping_modes as sm', 'sm.id', '=', 'shipments.shipping_mode_id')
                ->leftJoin('booking_types as bt', 'bt.id', '=', 'shipments.booking_type_id')
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


                    //When Shipment is marked as Shipment - Return Confirm
                    $rcp_assigned_shipment = RcpAssignedShipment::where('shipment_id', $request->shipment_id)->where('assigned_status', 1)->where('shipment_status', 0);
                    if(session('substitute_user_id') == NULL ){
                        if ($rcp_assigned_shipment->exists()) {
                            $rcp_assigned_shipment = $rcp_assigned_shipment->latest()->first();
                            $rcp_assigned_shipment->shipment_status = 4; //return confirm status
                            $rcp_assigned_shipment->assigned_status = 2; //unassign agent 
                            $rcp_assigned_shipment->user_id = $shipper_id;
                            $rcp_assigned_shipment->save();

                            //updating already_updated & pending of agent if shipment is updated by shipper 
                            $rcp_assigned_agent = RcpAssignedAgent::where('id',$rcp_assigned_shipment->rcp_assigned_agent_id)->first();
                            $already_updated = $rcp_assigned_agent->increment('already_updated');
                            $rcp_assigned_agent->decrement('pending_shipments');
                            $rcp_assigned_agent->save();


                            $return_assign_log = new RcpAssignedShipmentLog ();
                            $return_assign_log->rcp_assigned_shipment_id = $rcp_assigned_shipment->id;
                            $return_assign_log->shipment_id = $rcp_assigned_shipment->shipment_id;
                            $return_assign_log->status = 4; //return confirm status
                            $return_assign_log->user_id = $shipper_id;
                            $return_assign_log->save();
                        }
                    }
                    else{
                        if ($rcp_assigned_shipment->exists()) {
                            $rcp_assigned_shipment = $rcp_assigned_shipment->latest()->first();
                            $rcp_assigned_shipment->shipment_status = 4; //return confirm status
                            $rcp_assigned_shipment->assigned_status = 2; //unassign agent 
                            $rcp_assigned_shipment->substitute_user_id = $shipper_id;
                            $rcp_assigned_shipment->save();

                            //updating already_updated & pending of agent if shipment is updated by shipper 
                            $rcp_assigned_agent = RcpAssignedAgent::where('id',$rcp_assigned_shipment->rcp_assigned_agent_id)->first();
                            $already_updated = $rcp_assigned_agent->increment('already_updated');
                            $rcp_assigned_agent->decrement('pending_shipments');
                            $rcp_assigned_agent->save();


                            $return_assign_log = new RcpAssignedShipmentLog ();
                            $return_assign_log->rcp_assigned_shipment_id = $rcp_assigned_shipment->id;
                            $return_assign_log->shipment_id = $rcp_assigned_shipment->shipment_id;
                            $return_assign_log->status = 4; //return confirm status
                            $return_assign_log->user_id = $shipper_id;
                            $return_assign_log->save();
                        }
                    }

                           
                            $request->merge(['shipment_id' => $request->shipment_id]);
                            //$updated_type_id updated by shipper = 3
                            //$updated_rv_assign_agent_status_id to return confirm i.e is 1
                            //$updated_rv_state_id updating rv status to 4 i.e completed 
                            $this->shipment_status_update_shipper($request, $shipper_id, 3, 1, 4);

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
                // if ($parcel->shipper_status_id != 52) {
                if ($parcel->shipper_status_id != 52 || $parcel->shipper_status_id != 66) {
                    if ($parcel->shipper_status_id == 12) {
                        // $journey = ShipmentsJourney::where('shipment_id', $request->shipment_id)->where('shipper_status_id', 12)->where('status_reason_id', 12)->latest('id')->first();
                        // Shipment::where('id', $request->shipment_id)->update(['shipper_status_id' => 52, 'consignee_status_id' => 52]);
                        $journey = ShipmentsJourney::where('shipment_id', $request->shipment_id)->where('shipper_status_id', 12)->where('status_reason_id', 12)->latest('id')->first();
                        Shipment::where('id', $request->shipment_id)->update(['shipper_status_id' => 66, 'consignee_status_id' => 66]);

                        $last_reason = ShipmentsJourney::where('shipment_id', $parcel->id)->orderBy('id', 'DESC');
                        if ($last_reason->exists()) {
                            $last_reason = $last_reason->first();
                            $last_reason_id = $last_reason->status_reason_id;
                        } else {
                            $last_reason_id = NULL;
                        }
                        // ShipmentsJourneyController::add($request->shipment_id, 52, 52, $last_reason_id, $request->remark, $shipper_id, NULL, NULL);

                        //Update shipment status id to 66 (Shipment - Re-Attempt Call Requested)
                        ShipmentsJourneyController::add($request->shipment_id, 66, 66, $last_reason_id, $request->remark, $shipper_id, NULL, NULL);

                        //When Shipment is requested for Re-Attempt
                        $rcp_assigned_shipment = RcpAssignedShipment::where('shipment_id', $request->shipment_id)->where('assigned_status', 1)->where('shipment_status', 0);
                        if(session('substitute_user_id') == NULL ){
                            if ($rcp_assigned_shipment->exists()) {
                                $rcp_assigned_shipment = $rcp_assigned_shipment->latest()->first();
                                $rcp_assigned_shipment->shipment_status = 10; //reattempt request status
                                $rcp_assigned_shipment->assigned_status = 2; //unassign agent 
                                $rcp_assigned_shipment->user_id = $shipper_id;
                                $rcp_assigned_shipment->save();

                                //updating already_updated & pending of agent if shipment is updated by shipper 
                                $rcp_assigned_agent = RcpAssignedAgent::where('id',$rcp_assigned_shipment->rcp_assigned_agent_id)->first();
                                $already_updated = $rcp_assigned_agent->increment('already_updated');
                                $rcp_assigned_agent->decrement('pending_shipments');
                                $rcp_assigned_agent->save();


                                $return_assign_log = new RcpAssignedShipmentLog ();
                                $return_assign_log->rcp_assigned_shipment_id = $rcp_assigned_shipment->id;
                                $return_assign_log->shipment_id = $rcp_assigned_shipment->shipment_id;
                                $return_assign_log->status = 10; //reattempt request status
                                $return_assign_log->user_id = $shipper_id;
                                $return_assign_log->save();
                            }

                            $request->merge(['shipment_id' => $request->shipment_id]);
                            //$updated_by_id updated by shipper = $shipper_id
                            //$updated_type_id updated by shipper = 3
                            //$updated_rv_assign_agent_status_id to reattempt i.e is 2
                            //$updated_rv_state_id updating rv status to 3 i.e open 
                            $this->shipment_status_update_shipper($request, $shipper_id, 3, 2, 3);
                        }
                        else{
                            // Reattempt Request is from substitute user
                            if ($rcp_assigned_shipment->exists()) {
                                $rcp_assigned_shipment = $rcp_assigned_shipment->latest()->first();
                                $rcp_assigned_shipment->shipment_status = 10; //reattempt request status
                                $rcp_assigned_shipment->assigned_status = 2; //unassign agent 
                                $rcp_assigned_shipment->substitute_user_id = $shipper_id;
                                $rcp_assigned_shipment->save();

                                //updating already_updated & pending of agent if shipment is updated by shipper 
                                $rcp_assigned_agent = RcpAssignedAgent::where('id',$rcp_assigned_shipment->rcp_assigned_agent_id)->first();
                                $already_updated = $rcp_assigned_agent->increment('already_updated');
                                $rcp_assigned_agent->decrement('pending_shipments');
                                $rcp_assigned_agent->save();


                                $return_assign_log = new RcpAssignedShipmentLog ();
                                $return_assign_log->rcp_assigned_shipment_id = $rcp_assigned_shipment->id;
                                $return_assign_log->shipment_id = $rcp_assigned_shipment->shipment_id;
                                $return_assign_log->status = 10; //reattempt request status
                                $return_assign_log->user_id = $shipper_id;
                                $return_assign_log->save();
                                }
                            }

                          
                            $request->merge(['shipment_id' => $request->shipment_id]);
                            //$updated_by_id updated by shipper = $shipper_id
                            //$updated_type_id updated by substitute_user = 4
                            //$updated_rv_assign_agent_status_id, reattempt i.e is 2
                            //$updated_rv_state_id updating rv status to 3 i.e open 
                            $this->shipment_status_update_shipper($request, $shipper_id, 4, 2, 3);


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

                        //Same consignee
                        if ($intercept_type == 1) {
                            $city_area_id = ShipperShipmentBookController::consignee_address_area_intercept($request->consignee_city,$request->consignee_address);
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
                                'admin_id' => NULL,
                                'city_area_id'=>$city_area_id
                            ]);
                            $shipment->consignee_status_id = 54;
                            $shipment->shipper_status_id = 54;
                            $shipment->intercepted = 1;
                            $shipment->save();

                            ShipmentsJourneyController::add($request->shipment_id, 54, 54, NULL, NULL, $user_id, NULL);

                        //Updating New RcpAssigned Tables for Same Consignee Intercept
                        $rcp_assigned_shipment = RcpAssignedShipment::where('shipment_id', $request->shipment_id)->where('assigned_status', 1)->where('shipment_status', 0);
                        if ($rcp_assigned_shipment->exists()) {
                            
                            $rcp_assigned_shipment = $rcp_assigned_shipment ->latest()->first();
                            $rcp_assigned_shipment->shipment_status = 8; //intercept approved
                            $rcp_assigned_shipment->assigned_status = 2; //unassign agent 
                            $rcp_assigned_shipment->user_id = $user_id;
                            $rcp_assigned_shipment->save();

                            //updating already_updated & pending of agent if shipment is updated by shipper 
                            $rcp_assigned_agent = RcpAssignedAgent::where('id',$rcp_assigned_shipment->rcp_assigned_agent_id)->first();
                            $already_updated = $rcp_assigned_agent->increment('already_updated');
                            $rcp_assigned_agent->decrement('pending_shipments');
                            $rcp_assigned_agent->save();

                            //creating log for request intercept then approved
                            $return_assign_log = new RcpAssignedShipmentLog();
                            $return_assign_log->rcp_assigned_shipment_id = $rcp_assigned_shipment->id;
                            $return_assign_log->shipment_id = $rcp_assigned_shipment->shipment_id;
                            $return_assign_log->status = 7; //intercept request
                            $return_assign_log->user_id = $user_id;
                            $return_assign_log->save();

                            $return_assign_log = new RcpAssignedShipmentLog();
                            $return_assign_log->rcp_assigned_shipment_id = $rcp_assigned_shipment->id;
                            $return_assign_log->shipment_id = $rcp_assigned_shipment->shipment_id;
                            $return_assign_log->status = 8; //intercept approved
                            $return_assign_log->user_id = $user_id;
                            $return_assign_log->save(); 
                        }
                            
                        
                        $request->merge(['shipment_id' => $request->shipment_id]);
                            //$updated_by_id updated by shipper = $user_id
                            //$updated_type_id updated by Shipper = 3
                            //$updated_rv_assign_agent_status_id to Intercept approved i.e is 4
                            //$updated_rv_state_id updating rv status to 4 i.e completed 
                            $this->shipment_status_update_shipper($request, $user_id, 3, 4, 4);

                        
                        } 
                        //Different Consignee
                        else {

                            $new_con_city_area_id = ShipperShipmentBookController::consignee_address_area_intercept($request->consignee_city,$request->consignee_address);
                            $old_con_city_area_id = ShipperShipmentBookController::consignee_address_area_intercept($shipment->consignee_city_id,$shipment->consignee_address);
                            ShipperShipmentBookController::update_consignee_address_area($shipment->id,$new_con_city_area_id);
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
                                'intercept_type' => $intercept_type,
                                'shipper_id' => $user_id,
                                'new_con_city_area_id' => $new_con_city_area_id,
                                'old_con_city_area_id' => $old_con_city_area_id,
                            ]);
                            $shipment->consignee_status_id = 55;
                            $shipment->shipper_status_id = 55;
                            $shipment->intercepted = 1;
                            $shipment->save();

                            ShipmentsJourneyController::add($request->shipment_id, 55, 55, NULL, NULL, $user_id, NULL);

                        //Updating New RcpAssigned Tables for different Consignee Intercept
                        $rcp_assigned_shipment = RcpAssignedShipment::where('shipment_id', $request->shipment_id)->where('assigned_status', 1)->where('shipment_status', 0);
                        if ($rcp_assigned_shipment->exists()) {
                                $rcp_assigned_shipment = $rcp_assigned_shipment ->latest()->first();
                                $rcp_assigned_shipment->shipment_status = 7; //intercept request
                                $rcp_assigned_shipment->assigned_status = 2; //unassign agent 
                                $rcp_assigned_shipment->user_id = $user_id;
                                $rcp_assigned_shipment->save();

                                //updating already_updated & pending of agent if shipment is updated by shipper 
                                $rcp_assigned_agent = RcpAssignedAgent::where('id',$rcp_assigned_shipment->rcp_assigned_agent_id)->first();
                                $already_updated = $rcp_assigned_agent->increment('already_updated');
                                $rcp_assigned_agent->decrement('pending_shipments');
                                $rcp_assigned_agent->save();

                                //creating log 
                                $return_assign_log = new RcpAssignedShipmentLog();
                                $return_assign_log->rcp_assigned_shipment_id = $rcp_assigned_shipment->id;
                                $return_assign_log->shipment_id = $rcp_assigned_shipment->shipment_id;
                                $return_assign_log->status = 7; //intercept request
                                $return_assign_log->user_id = $user_id;
                                $return_assign_log->save();
                                    
                            }

                            $request->merge(['shipment_id' => $request->shipment_id]);
                            //$updated_by_id updated by shipper = $user_id
                            //$updated_type_id updated by Shipper = 3
                            //$updated_rv_assign_agent_status_id, intercept requested i.e is 3
                            //$updated_rv_state_id updating rv status to 3 i.e open 
                            $this->shipment_status_update_shipper($request, $user_id, 3, 3, 4);


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
            $booking_types = BookingType::whereNotIn('id', [4, 6])->get();
//            if ($shipper->account_type_id == 1) {
//                $booking_types = BookingType::whereNotIn('id', [4, 6])->get();
//            } elseif ($shipper->account_type_id == 2) {
//                $booking_types = BookingType::whereNotIn('id', [4])->get();
//            } else {
//                return response()->json(['status' => 1, 'message' => 'Invalid Account Type']);
//            }
            return response()->json(['status' => 0, 'message' => 'Booking Types Found!', 'booking_types' => $booking_types]);
        }
        return response()->json(['status' => 1, 'message' => 'Invalid Shipper']);
    }

    public function corporate_index(Request $request)
    {
        $user_id = $request->shipper_id;
        $date = Carbon::today();
        $user = User::find($user_id);
//        $booking_types = BookingType::where('id', '!=', 4)->get();
//        $user_shipping_address = UserShippingInfo::join('cities as c', 'c.id', '=', 'user_shipping_infos.city_id')
//            ->where('user_shipping_infos.user_id', $user_id)
//             ->where('user_shipping_infos.status', 1)
//            ->where('user_shipping_infos.hidden', 0)
//            ->where(function ($q){
//                $q->where('user_shipping_infos.default_address', 1)
//                    ->orWhere('user_shipping_infos.default_return_address', 1);
//            })
//            ->select('user_shipping_infos.id','user_shipping_infos.pickup_address','user_shipping_infos.default_return_address','user_shipping_infos.city_id','c.name as  city_name','user_shipping_infos.default_address')
//            ->get();

        $multi_piece = $user->multipiece_status;
//        $cities = City::where('pickup', 1)->where('status', 1)->where('business_category_id', 1)->whereNotNull('zone_id')->orderBy('name')->get();
//        if (in_array($user_id, [5982, 3324, 10104, 14110, 16292])) {
//            $consignee_cities = City::where('status', 1)->where('business_category_id', 1)->whereNotNull('zone_id')->orderBy('name')
//                ->select('id','name','hub','hub_id')
//                ->get();
//        } else {
//            $consignee_cities = City::where('id', '!=', 1244)->where('status', 1)->where('business_category_id', 1)->whereNotNull('zone_id')->orderBy('name')
//                ->select('id','name','hub','hub_id')
//                ->get();
//        }
//        $products = Product::orderBy('product_name')->get();
//        $distribution_products = DistributionProduct::orderBy('name')->get();
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
//        $charges_modes = ChargesModes::whereIn('id', [3])->get();
//        $check = NonServiceArea::pluck('name')->toArray();
//        $air_waybill = ShipperAirWaybillSettings::where('user_id', $user_id);
//        if ($air_waybill->exists()) {
//            $air_waybill = $air_waybill->first();
//        } else {
//            $air_waybill = null;
//        }
//        $airway_bill_address_visibility_users = 1;
        $air_waybill = 1;
        $bypass_settings = GlobalSettings::where('type', 'airway_bill_address_visibility_setting');
        if ($bypass_settings->exists()) {
            $bypass_settings = $bypass_settings->first();
            if ($bypass_settings->text != NULL) {
                $airway_bill_address_visibility_accounts = array_map('intval', explode(',', $bypass_settings->text));
                if (in_array(session('user_id'), $airway_bill_address_visibility_accounts)) {
                    $air_waybill = 0;
                }
            }
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
//        $ftl_collection_type = [['id' => 1, 'type' => 'Invoice'], ['id' => 2, 'type' => 'Cash']];
        return response()->json(['status' => 0, 'shipping_address' => [], 'multi_piece' => $multi_piece, 'user' => [], 'cities' => [], 'distribution_products' => [], 'products' => [], 'shipping_mode_same_day_timings' => $shipping_mode_same_day_timings, 'payment_modes' => $payment_modes, 'consignee_cities' => [], 'check' => [], 'delivery_type' => $delivery_type, 'charges_modes' => [], 'date' => $date, 'air_waybill' => $air_waybill, 'user_delivery_types' => $user_delivery_types, 'approve_ftl_requests' => $approve_ftl_requests, 'omni_user' => $omni_user, 'booking_types' => [], 'ftl_collection_type' => []]);
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
            if ($ftl_request->exists()) {
                $ftl_request = $ftl_request->first();
                $data = array('origin_id' => $ftl_request->origin_id, 'destination_id' => $ftl_request->destination_id, 'weight' => $ftl_request->weight, 'quantity' => $ftl_request->quantity, 'total_charges' => $ftl_request->total_charges);
                return response()->json(['status' => 0, 'message' => "FTL Found", 'data' => $data]);
            } else {
                return response()->json(['status' => 1, 'message' => "No FTL Found!"]);
            }

        }
    }

    public function reimbursement_index(Request $request)
    {
        $date = Carbon::today();
        $user_id = $request->shipper_id;
        $user = User::find($user_id);
//        $booking_types = BookingType::whereNotIn('id', [4, 6])->get();
//        $user_shipping_address = UserShippingInfo::join('cities as c', 'c.id', '=', 'user_shipping_infos.city_id')
//            ->where('user_shipping_infos.user_id', $user_id)
//            ->where('user_shipping_infos.status', 1)
//            ->where('user_shipping_infos.hidden', 0)
//            ->where(function ($q){
//                $q->where('user_shipping_infos.default_address', 1)
//                    ->orWhere('user_shipping_infos.default_return_address', 1);
//            })
//            ->select('user_shipping_infos.id','user_shipping_infos.pickup_address','user_shipping_infos.default_return_address','user_shipping_infos.city_id','c.name as city_name','user_shipping_infos.default_address')
//            ->get();
        $multi_piece = $user->multipiece_status;
//        $cities = City::where('pickup', 1)->where('status', 1)->where('business_category_id', 1)->whereNotNull('zone_id')->orderBy('name')->get();
//        if (in_array($user_id, [5982, 3324, 10104, 14110, 16292])) {
//            $consignee_cities = City::where('status', 1)->where('business_category_id', 1)
//                ->whereNotNull('zone_id')
//                ->orderBy('name')
//                ->select('id','name','hub','hub_id')
//                ->get();
//        } else {
//            $consignee_cities = City::where('id', '!=', 1244)->where('status', 1)->where('business_category_id', 1)->whereNotNull('zone_id')->orderBy('name')
//                ->select('id','name','hub','hub_id')
//                ->get();
//        }
//        $products = Product::orderBy('product_name')->get();
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
//        $check = NonServiceArea::pluck('name')->toArray();
//        $charges_modes = ChargesModes::whereIn('id', [4])->get();
//        $air_waybill = ShipperAirWaybillSettings::where('user_id', $user_id);
//        if ($air_waybill->exists()) {
//            $air_waybill = $air_waybill->first();
//        } else {
//            $air_waybill = null;
//        }
        $air_waybill = 1;
        $bypass_settings = GlobalSettings::where('type', 'airway_bill_address_visibility_setting');
        if ($bypass_settings->exists()) {
            $bypass_settings = $bypass_settings->first();
            if ($bypass_settings->text != NULL) {
                $airway_bill_address_visibility_accounts = array_map('intval', explode(',', $bypass_settings->text));
                if (in_array($request->shipper_id, $airway_bill_address_visibility_accounts)) {
                    $air_waybill = 0;
                }
            }
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

        return response()->json(['status' => 0, 'shipping_address' => [], 'user' => [], 'multi_piece' => $multi_piece, 'cities' => [], 'products' => [], 'shipping_mode_same_day_timings' => $shipping_mode_same_day_timings, 'payment_modes' => $payment_modes, 'consignee_cities' => [], 'check' => [], 'charges_modes' => [], 'date' => $date, 'air_waybill' => $air_waybill, 'omni_user' => $omni_user, 'booking_types' => []]);
    }

    public function shipping_address(Request $request)
    {
                $user_id = $request->shipper_id;
                $user_shipping_address = null;

                $request->merge([
                    'address' => $request->address ?: null,
                    'pickup_address_id' => $request->pickup_address_id ?: null,
                    'return_address_id' => $request->return_address_id ?: null
                ]);
                $rules = [
                    'address' => ['nullable', 'min:4'],
                    'pickup_address_id' => ['nullable', 'exists:user_shipping_infos,id'],
                    'return_address_id' => ['nullable', 'exists:user_shipping_infos,id'],
                ];

                $validate = Validator::make($request->all(), $rules, $this->messages);
                $validate->setAttributeNames($this->names);

                if ($validate->fails()) {
                    return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
                }



                if($request->filled('address')) {
                    $searchAddress  = trim($request->address);
                    $user_shipping_address = UserShippingInfo::leftjoin('cities as c', 'c.id', '=', 'user_shipping_infos.city_id')
                        ->where('user_shipping_infos.user_id', $user_id)
                        ->where('user_shipping_infos.status', 1)
                        ->where('user_shipping_infos.hidden', 0)
                        ->where('user_shipping_infos.pickup_address', 'like', '%' . $request->address . '%')
                        ->select('user_shipping_infos.id','user_shipping_infos.pickup_address','user_shipping_infos.default_return_address','user_shipping_infos.city_id','c.name as city_name','user_shipping_infos.default_address')
                        ->limit(5)
                        ->get();

                    if($user_shipping_address->isNotEmpty()) {
                        return response()->json([
                            'status' => 0,
                            'message' => 'Addresses found!',
                            'shipping_address' => $user_shipping_address
                        ]);
                    } else {
                        return response()->json([
                            'status' => 1,
                            'message' => 'Matching addresses not found!',
                        ]);
                    }

                }
                elseif ($request->filled('pickup_address_id') || $request->filled('return_address_id')) {

                    $updates = [
                        'pickup_address_id' => 'default_address',
                        'return_address_id' => 'default_return_address',
                    ];

                    foreach ($updates as $requestKey => $field) {
                        if ($addressId = $request->input($requestKey)) {
                            // Reset all previous defaults
                            UserShippingInfo::where('user_id', $user_id)->update([$field => 0]);

                            // Set the selected address as default
                            UserShippingInfo::where('id', $addressId)->update([$field => 1]);
                        }
                    }

                    return response()->json([
                        'status' => 0,
                        'message' => 'Default Shipper address updated successfully'
                    ]);
                }

                else {
                    $user_shipping_address = UserShippingInfo::join('cities as c', 'c.id', '=', 'user_shipping_infos.city_id')
                        ->where('user_shipping_infos.user_id', $user_id)
                        ->where('user_shipping_infos.status', 1)
                        ->where('user_shipping_infos.hidden', 0)
                        ->where(function ($q) {
                            $q->where('user_shipping_infos.default_address', 1)
                                ->orWhere('user_shipping_infos.default_return_address', 1);
                        })
                        ->select('user_shipping_infos.id', 'user_shipping_infos.pickup_address', 'user_shipping_infos.default_return_address', 'user_shipping_infos.city_id', 'c.name as city_name', 'user_shipping_infos.default_address')
                        ->get();

                    if ($user_shipping_address->isNotEmpty()) {
                        return response()->json([
                            'status' => 0,
                            'message' => 'Default addresses found!',
                            'shipping_address' => $user_shipping_address
                        ]);
                    } else {
                        return response()->json([
                            'status' => 1,
                            'message' => 'Default addresses not found!',
                        ]);
                    }

                }



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

    public function shipper_subscription_add(Request $request)
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
            if ($shipment->exists()) {
                $shipment = $shipment->first();
                if ($shipment->shipper_status_id != 14) {
                    $shipper_subscription = ShipperShipmentsSubscription::where('shipper_id', $request->shipper_id);
                    if ($shipper_subscription->count() < 5) {
                        $shipment_exists = $shipper_subscription->where('shipment_id', $shipment->id);
                        if (!$shipment_exists->exists()) {
                            $shipper_subscription_obj = new ShipperShipmentsSubscription();
                            $shipper_subscription_obj->shipper_id = $request->shipper_id;
                            $shipper_subscription_obj->shipment_id = $shipment->id;
                            $shipper_subscription_obj->save();
                            return response()->json(['status' => 0, 'message' => 'Shipment is Added to Subscription List']);
                        } else {
                            return response()->json(['status' => 1, 'message' => 'Shipment Already Added in Subscription List']);
                        }
                    } else {
                        return response()->json(['status' => 1, 'message' => 'Limit of Subscription List is Full']);
                    }
                } else {
                    return response()->json(['status' => 1, 'message' => 'Shipment is marked as Delivered']);
                }
            } else {
                return response()->json(['status' => 1, 'message' => 'Invalid Tracking Number']);
            }

        }
    }

    public function shipment_pod_tracking(Request $request)
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
            if ($shipment->exists()) {
                $shipment = $shipment->first();
                if ($request->shipper_id == $shipment->pickup_address->user->id) {
                        $journey = ShipmentsJourney::where('shipment_id', $shipment->id)->where('verification', 1)->orderBy('id', 'DESC')->first();
                        $message = array();
                        if (in_array($journey->shipper_status_id, [7, 8, 9, 12, 15, 18, 14, 30, 37, 56])) {
                            $rider_delivery = RiderDelivery::where('shipment_id', $shipment->id)->where('delivery_note_id', $journey->reference_1_id)->where('rider_status_id', $journey->shipper_status_id)->where('rider_status_reason_id', $journey->status_reason_id)->orderBy('id', 'DESC');
                            if ($rider_delivery->exists()) {
                                $data = array();
                                $rider_delivery = $rider_delivery->first();

                                if ($rider_delivery->picture_path != null) {
                                    $exists = Storage::disk('public')->exists($rider_delivery->picture_path);
                                    if ($exists) {
                                        $pod_image = asset(Storage::url($rider_delivery->picture_path));
                                    } else {
                                        $pod_image = Storage::disk('s3')->temporaryUrl($rider_delivery->picture_path, now()->addMinutes(15));
                                    }
                                    $data["pod_image"] = $pod_image;
                                } else {
                                    $data["pod_image"] = NULL;
                                    array_push($message, "POD");
                                }

                                if ($rider_delivery->cnic_image != null) {
                                    $exists = Storage::disk('public')->exists($rider_delivery->cnic_image);
                                    if ($exists) {
                                        $cnic_image = asset(Storage::url($rider_delivery->cnic_image));
                                    } else {
                                        $cnic_image = Storage::disk('s3')->temporaryUrl($rider_delivery->cnic_image, now()->addMinutes(15));
                                    }
                                    $data["cnic_image"] = $cnic_image;
                                } else {
                                    $data["cnic_image"] = NULL;
                                    array_push($message,"CNIC");
                                }

                                if ($rider_delivery->house_image != null) {
                                    $exists = Storage::disk('public')->exists($rider_delivery->house_image);
                                    if ($exists) {
                                        $house_image = asset(Storage::url($rider_delivery->house_image));
                                    } else {
                                        $house_image = Storage::disk('s3')->temporaryUrl($rider_delivery->house_image, now()->addMinutes(15));
                                    }
                                    $data["house_image"] = $house_image;
                                } else {
                                    $data["house_image"] = NULL;
                                    array_push($message, "House Image");
                                }

                                if ($rider_delivery->audio_path != null) {
                                    $exists = Storage::disk('public')->exists($rider_delivery->audio_path);
                                    if ($exists) {
                                        $audio_path = asset(Storage::url($rider_delivery->audio_path));
                                    } else {
                                        $audio_path = Storage::disk('s3')->temporaryUrl($rider_delivery->audio_path, now()->addMinutes(15));
                                    }
                                    $data["audio"] = $audio_path;
                                } else {
                                    $data["audio"] = NULL;
                                    array_push($message, "Audio");
                                }

                                if ($rider_delivery->actual_location_latitude != null && $rider_delivery->actual_location_longitude != null) {
                                    $data["latitude"] = $rider_delivery->actual_location_latitude;
                                    $data["longitude"] = $rider_delivery->actual_location_longitude;
                                } else {
                                    $data["latitude"] = NULL;
                                    $data["longitude"] = NULL;
                                    array_push($message, "Location");
                                }
                                return response()->json(['status' => 0, 'message' => implode(', ', $message). ' for the selected tracking number is not found', 'information' => $data]);
                            }
                            else{
                                return response()->json(['status' => 1, 'error' => 'PODs for the selected tracking number are not found']);
                            }
                        }
                        else if (in_array($journey->shipper_status_id, [47, 24, 48, 60, 25, 31, 38])) {
                            $rider_delivery = RiderReturnDelivery::where('shipment_id', $shipment->id)->where('return_note_id', $journey->reference_1_id)->where('rider_status_id', $journey->shipper_status_id)->where('rider_status_reason_id', $journey->status_reason_id)->orderBy('id', 'DESC');
                            if ($rider_delivery->exists()) {
                                $data = array();
                                $rider_delivery = $rider_delivery->first();
                                $data["cnic_image"] = NULL;
                                if ($rider_delivery->picture_path != null) {
                                    $exists = Storage::disk('public')->exists($rider_delivery->picture_path);
                                    if ($exists) {
                                        $pod_image = asset(Storage::url($rider_delivery->picture_path));
                                    } else {
                                        $pod_image = Storage::disk('s3')->temporaryUrl($rider_delivery->picture_path, now()->addMinutes(15));
                                    }
                                    $data["pod_image"] = $pod_image;
                                } else {
                                    $data["pod_image"] = NULL;
                                    array_push($message, "POD");
                                }

                                if ($rider_delivery->pod_image != null) {
                                    $exists = Storage::disk('public')->exists($rider_delivery->pod_image);
                                    if ($exists) {
                                        $house_image = asset(Storage::url($rider_delivery->pod_image));
                                    } else {
                                        $house_image = Storage::disk('s3')->temporaryUrl($rider_delivery->pod_image, now()->addMinutes(15));
                                    }
                                    $data["house_image"] = $house_image;
                                } else {
                                    $data["house_image"] = NULL;
                                    array_push($message, "House Image");
                                }

                                if ($rider_delivery->audio_path != null) {
                                    $exists = Storage::disk('public')->exists($rider_delivery->audio_path);
                                    if ($exists) {
                                        $audio_path = asset(Storage::url($rider_delivery->audio_path));
                                    } else {
                                        $audio_path = Storage::disk('s3')->temporaryUrl($rider_delivery->audio_path, now()->addMinutes(15));
                                    }
                                    $data["audio"] = $audio_path;
                                } else {
                                    $data["audio"] = NULL;
                                    array_push($message, "Audio");
                                }

                                if ($rider_delivery->actual_location_latitude != null && $rider_delivery->actual_location_longitude != null) {
                                    $data["latitude"] = $rider_delivery->actual_location_latitude;
                                    $data["longitude"] = $rider_delivery->actual_location_longitude;
                                } else {
                                    $data["latitude"] = NULL;
                                    $data["longitude"] = NULL;
                                    array_push($message, "Location");
                                }
                                return response()->json(['status' => 0, 'message' => implode(', ', $message). ' for the selected tracking number is not found', 'information' => $data]);
                            }
                            else{
                                return response()->json(['status' => 1, 'error' => 'PODs for the selected tracking number are not found']);
                            }
                        }
                        else{
                            return response()->json(['status' => 1, 'error' => 'Shipment is not on valid status']);
                        }
                } else {
                    return response()->json(['status' => 1, 'error' => "Following Tracking Number doesn't belong to you : " . $request->tracking_no]);
                }
            } else {
                return response()->json(['status' => 1, 'error' => 'Invalid Tracking Number']);
            }
        }
    }

    public function updateprofilewalletbulk(Request $request)
    {
        $names = [
            'name' => 'Name ',
            'phone' => 'Phone',
            'cnic' => 'CNIC',
            'email' => 'Email',
        ];

        $messages = [
            'required' => ':attribute is Required.',
            'required_if' => ':attribute is Required when :other is :value.',
            'filled' => ':attribute is Optional but cannot be Empty if Present.',
            'integer' => ':attribute must be an Integer.',
            'numeric' => ':attribute must be a Number.',
            'boolean' => ':attribute must be 0 or 1.',
            'digits_between' => ':attribute must be between :min and :max Digits.',
            'email' => ':attribute must be a Valid Email Address.',
            'exists' => 'Given :attribute is of Invalid ID.',
            'unique' => ':attribute is already Present.',
            'date_format' => ':attribute must be of valid Format, required Format is: YYYY-MM-DD.',
            'in' => ':attribute must be No or Yes.',
            'check_duplicate' => 'Phone Or Email Already Exists',
            'check_cnic' => 'Cnic Already Exists',
            //'phone' => 'Phone starts with 03 or 923 followed by 9 digits',
            'name' => 'Only alphabetic characters and spaces',
        ];

        $rules = [
            'name' => ['required', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],
            'email' => ['required', 'max:255', 'email', 'check_duplicate'],
            'phone' => ['required', 'max:255'],
            'cnic' => ['required', 'max:255','check_cnic']
        ];


        Validator::extend('check_duplicate', function ($attribute, $value, $parameters, $validator)  {
            $data = $validator->getData();
            $phone = $data['phone'];
            $email = $data['email'];

            $user = WalletUser::where('email', $email)->orWhere('phone', $phone);
            if($user->exists()) {
                return FALSE;
            } else{
                return true;
            }
        });

        Validator::extend('check_cnic', function ($attribute, $value, $parameters, $validator)  {
            $data = $validator->getData();
            $cnic = $data['cnic'];;

            $user = WalletUser::where('cnic', $cnic);
            if($user->exists()) {
                return FALSE;
            } else{
                return true;
            }
        });
        $errors = array();
        $data = array();

        $bank = 0;
        foreach ($request->users as $key => $row) {
            $row_id = $row['id'];
            $validate = Validator::make($row, $rules, $messages);

            $validate->setAttributeNames($names);

            if ($validate->fails()) {
                foreach ($validate->errors()->toArray() as $key => $error_array) {
                    foreach ($error_array as $error) {
                        if (!isset($errors[$row_id][$key])) {
                            $errors[$row_id][$key] = $error;
                        }
                    }
                }


            } else {
                $data[$key] = [
                    'name' => $row['name'],
                    'cnic' => $row['cnic'],
                    'phone' => $row['phone'],
                    'email' => $row['email'],
                    'user_id' => $request->shipper_id,
                    'status' => 1,
                    'substitute_user_id' => isset($row['substitute_user_id']) ? $row['substitute_user_id'] : 0
                ];
            }
        }
        if(!empty($errors)){
            return response()->json(['status' => 0, 'error' => $errors]);
        }else{
            $api = config('app.FINGA_URL');
            $token = FingaIntegrationController::getToken($api);
            $login_data = collect($data)->first();
            $url = FingaIntegrationController::getLoginUrl($api, $token, $login_data['phone'], $login_data['cnic'], $login_data['email']);
            $finja = FingaIntegrationController::signUp($data,$request->shipper_id );

            if (isset($finja['error'])) {
                $finjaArray = json_decode(json_encode($finja), true);

                $errorMessages = collect($finjaArray['error']['users']);

                foreach ($errorMessages as $error_val){
                    $key = array_key_first(array_filter($data, function ($row) use ($error_val) {
                        return $row['phone'] === $error_val['mobile_no'];
                    }));

                    $data[$key]['message'] = $error_val['message'];

                }
                return response()->json(['status' => 0, 'error_2' => $data]);
            } else {

                $final['url'] = $url;
                //$url = route('cod.wallet.finja_dashboard', ['url' => $finja['url']]);
                foreach ($data as $key=>$value) {
                    $data[$key]['wallet_id'] = $finja['wallet_id'];
                    $data[$key]['created_at'] = Carbon::now();
                    $data[$key]['updated_at'] = Carbon::now();
                }
                WalletUser::wallet_create($data);
                
                $hasSubstituteZero = array_filter($data, function ($item) {
                    return isset($item['substitute_user_id']) && $item['substitute_user_id'] == 0;
                });
                if (!empty($hasSubstituteZero)) {
                    if(UserBankInfo::where('user_id',$request->shipper_id)->exists())
                    {
                        UserBankInfo::where('user_id', $request->shipper_id)->update(['default_bank' => 0]);
                    }
                    $user_city_id = User::where('id', $request->shipper_id)->value('city_id');
                    $user_bank = new UserBankInfo();
                    $user_bank->user_id = $request->shipper_id;
                    $user_bank->bank_name = 48;
                    $user_bank->bank_branch = 'N/A';
                    $user_bank->account_no = '923322149092';
                    $user_bank->account_title = 'N/A';
                    $user_bank->iban = 'PK06TMFB0000000087042403';
                    $user_bank->city_id =  $user_city_id;
                    $user_bank->default_bank = 1; // always make the new bank info as default
                    $user_bank->save();

                }
                WalletSignUpLPendingRecordLogs::dispatch($request->shipper_id);
                return response()->json(['status' => 1, 'output' => $final]);

            }
        }
        return response()->json(['status' => 1, 'success'=>'Profile Information Successfully Updated"']);
    }
    
    public function wallet_login(Request $request) {

        $api = config('app.FINGA_URL');
        $token = FingaIntegrationController::getToken($api);
        $user = WalletUser::where('user_id', $request->shipper_id)->where('substitute_user_id', 0)->first();
        if(!empty($user)) {

            $phone = $user->phone;
            $cnic = $user->cnic;
            $email = $user->email;
            $url= FingaIntegrationController::getLoginUrl($api, $token, $phone, $cnic, $email);
            $final['url'] = $url;
            return response()->json(['status' => 1, 'output' => $final]);
        }else{
           
        }

    }

    public function get_shipper_info(Request $request)
    {
        $user = User::where('id',$request->shipper_id)->first();
        return response()->json(['status' => 0, 'shipper' => $user]);
    }

    public function is_wallet_user(Request $request) {

        $user = WalletUser::where('user_id', $request->shipper_id)->where('substitute_user_id', 0)->first();
        if($user) {
            return response()->json(['status' => 1, 'is_wallet_user' => 1]);
        } else {
            return response()->json(['status' => 0, 'is_wallet_user' => 0]);
        }
    }

    public function shipper_sar(Request $request)
    {

        // Validate request input
        // $validate = Validator::make($request->all(), [
        //     'shipper_id' => 'required|integer'
        // ]);

        // if ($validate->fails()) {
        //     return response()->json([
        //         'status' => 1,
        //         'message' => 'Error(s) in Input',
        //         'errors' => $validate->errors()
        //     ]);
        // }

        // Build query
        $query = Shipment::join('shipment_status as ss', 'ss.id', '=', 'shipments.shipper_status_id')
            ->leftJoin('rv_agent_call_histories as call', 'shipments.id', '=', 'call.shipment_id')
            ->leftJoin('rv_assign_agent_sub_statuses as raass', 'call.call_finding_id', '=', 'raass.id')
            ->leftJoin('rv_shipment_tickets as rst', 'rst.shipment_id', '=', 'shipments.id')
            ->leftJoin('shipment_status_reason as ssr', 'ssr.id', '=', 'rst.shipment_status_reason_id')
            ->select(
                'shipments.tracking_number as tracking_number',
                'ss.name as status',
                'raass.name as call_finding_status',
                DB::raw("COALESCE(call.remarks, '-') as remarks"),
                "ssr.name as ticket_status_reason",
                'call.created_at as datetime' 
            )
            ->where('shipments.shipper_status_id', DB::raw(65))
            ->where('shipments.user_id', $request->shipper_id)
            ->orderBy('shipments.id', 'desc');

        // Check if records exist before executing get()
        if (!$query->exists()) {
            return response()->json([
                'status' => 1,
                'message' => 'No Shipments Found'
            ]);
        }

        // Get all records
        $shipments = $query->get();

        // Group remarks by tracking number
        // $grouped = $shipments->groupBy('tracking_number')->map(function ($group) {
        //     $transactionRemarks = $group->map(function ($item) {
        //         return [
        //             'username' => 'Call Courier',
        //             'remarks' => $item->call_status,
        //             'remarksDateTime' => $item->created_at
        //                 ? \Carbon\Carbon::parse($item->created_at)->format('Y-m-d H:i:s')
        //                 : null
        //         ];
        //     })->values();

        //     return [
        //         'tracking_number' => $group->first()->tracking_number,
        //         'transactionDetails' => $transactionRemarks
        //     ];
        // })->values();

        // Return formatted API response
        return response()->json([
            'status' => 0,
            'data' => $shipments,
            'message' => 'Shipments Found!'
        ]);
    }
    
}
