<?php

namespace App\Http\Controllers;

use App\Http\Controllers\CRM\CRMController;
use App\Http\Models\Admin\DeliveryNoteShipment;
use App\Http\Models\Admin\Fleet;
use App\Http\Models\City;
use App\Http\Models\ConsigneeOtp;
use App\Http\Models\ConsigneeInfo;
use App\Http\Models\ConsigneeUser;
use App\Http\Models\CRM\CrmRequest;
use App\Http\Models\EmployeeNotificationHistory;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentsJourney;
use App\Http\Models\Shipper\User;
use App\Http\Models\Shipper\UserShippingInfo;
use Carbon\Carbon;
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
            $shipment_infos = Shipment::where('tracking_number', $request->tracking_no)
                ->select('consignee_name', 'consignee_address', 'consignee_phone_number_1 as phone_number');
            if ($shipment_infos->exists()) {
                $shipment_infos = $shipment_infos->get();
                $data = array();
                foreach ($shipment_infos as $shipment_info){
                    $data["consignee_name"] = $shipment_info->consignee_name;
                    $data["consignee_address"] = $shipment_info->consignee_address;
                    $data["phone_number"] = substr_replace($shipment_info->phone_number, '-', 4, 0);
                }
                return response()->json(['status' => 0, 'consignee_info' => $data]);
            }
            return response()->json(['status' => 1, 'message' => 'Invalid Tracking Number']);
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
            $otp_pin = rand(1000, 9999);
            $consignee_otp = ConsigneeOtp::where('phone_number', substr_replace($request->input('phone_number'), '-', 4, 0));
            if ($consignee_otp->exists()) {
                $consignee_otp = $consignee_otp->first();
            } else {
                $consignee_otp = new ConsigneeOtp();
                $consignee_otp->phone_number = $request->input('phone_number');
            }
            $consignee_otp->otp = bcrypt($otp_pin);
            $consignee_otp->save();
            NotificationsController::trax_otp_verification($request->input('phone_number'), $otp_pin);
            return response()->json(['status' => 0, 'otp_message' => 'OTP has been sent to your registered number']);
        }
    }

    public function consignee_forget_pin_otp(Request $request)
    {
        $rules = [
            'phone_number' => ['required', 'regex:/^[0][0-9]{10}$/']
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $consignee_user = ConsigneeUser::where('phone_number_1', substr_replace($request->input('phone_number'), '-', 4, 0));
            if ($consignee_user->exists()) {
                $otp_pin = rand(1000, 9999);
                $consignee_otp = ConsigneeOtp::where('phone_number', substr_replace($request->input('phone_number'), '-', 4, 0));
                if ($consignee_otp->exists()) {
                    $consignee_otp = $consignee_otp->first();
                } else {
                    $consignee_otp = new ConsigneeOtp();
                    $consignee_otp->phone_number = $request->input('phone_number');
                }
                $consignee_otp->otp = bcrypt($otp_pin);
                $consignee_otp->save();
                NotificationsController::trax_otp_verification($request->input('phone_number'), $otp_pin);
                return response()->json(['status' => 0, 'message' => 'OTP has been sent to your registered number']);
            }else{
                return response()->json(['status' => 1, 'message' => 'Given Phone Number is not Present!']);
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
            $consignee_otp = ConsigneeOtp::where('phone_number', $request->phone_number)->orderBy('id', 'DESC');
            if ($consignee_otp->exists()) {
                $consignee_otp = $consignee_otp->first();
                if (Hash::check($request->otp, $consignee_otp->otp)) {
                    return response()->json(['status' => 0, 'message' => 'OTP has been verified']);
                } else {
                    return response()->json(['status' => 1, 'message' => 'Invalid OTP']);
                }
            } else {
                return response()->json(['status' => 1, 'message' => 'Invalid OTP']);
            }
        }
    }

    public function consignee_signup(Request $request)
    {
        $rules = [
            'phone_number' => ['required', 'regex:/^[0][0-9]{3}-[0-9]{7}$/'],
            'pin' => ['required', 'integer', 'digits:4'],
            'name' => ['required'],
            'address' => ['required'],
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $consignee_info = ConsigneeUser::where('phone_number_1', $request->input('phone_number'));
            if ($consignee_info->exists()) {
                return response()->json(['status' => 1, 'message' => 'Account Already registered']);
            } else {
                $api_token = uniqid(base64_encode(str_random(60)));
                $consignee_info = new ConsigneeUser();
                $consignee_info->name = $request->name;
                $consignee_info->address = $request->address;
                $consignee_info->pin = bcrypt($request->pin);
                $consignee_info->phone_number_1 = $request->phone_number;
                $consignee_info->api_token = $api_token;
                $consignee_info->save();
                $information = ['message' => 'Account has been created', 'api_token' => $consignee_info->api_token, 'name' => $consignee_info->name, 'phone_number' => $consignee_info->phone_number_1];
                return response()->json(['status' => 0, 'information' => $information]);

            }
        }
    }

    public function login(Request $request)
    {
        $rules = [
            'phone_number' => ['required', 'regex:/^[0][0-9]{10}$/'],
            'pin' => ['required', 'integer', 'digits:4']
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $consignee_user = ConsigneeUser::where('phone_number_1', substr_replace($request->input('phone_number'), '-', 4, 0));
            if ($consignee_user->exists()) {
                $consignee_user = $consignee_user->first();
                    if (Hash::check($request->input('pin'), $consignee_user->pin)) {
                        $information = array();
                        $information['name'] = $consignee_user->name;
                        $information['consignee_id'] = $consignee_user->id;
                        $information['phone_number'] = $consignee_user->phone_number_1;
                        if ($consignee_user->api_token) {
                            $information['api_token'] = $consignee_user->api_token;
                        }
                        else {
                            $api_token = uniqid(base64_encode(str_random(60)));

                            $consignee_user->api_token = $api_token;

                            $consignee_user->save();

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

    public function active_shipments(Request $request)
    {
        $consignee_id = $request->consignee_id;
        $consignee_info = ConsigneeUser::find($consignee_id);
        $consignee_shipments = Shipment::join('shipment_status as ss', 'shipments.shipper_status_id', '=', 'ss.id')
            ->wherein('consignee_phone_number_1', [$consignee_info->phone_number_1, $consignee_info->phone_number_2])
            ->wherein('shipments.shipper_status_id', [2, 27, 33, 4, 13, 3, 26, 32, 5, 8, 29, 35, 9, 15, 7, 54, 55, 11, 49])
            ->select('shipments.id as shipment_id', 'shipments.tracking_number as tracking_no', 'shipments.shipper_status_id as status_id', 'ss.name as status', 'shipments.amount as amount', 'shipments.delivery_in_route as delivery_in_route', 'shipments.pickup_address_id as pickup_address_id', 'shipments.consignee_address as consignee_address', 'shipments.consignee_latitude as consignee_latitude', 'shipments.consignee_longitude as consignee_longitude', 'shipments.consignee_city_id as consignee_city_id')
            ->orderBy('shipments.id', 'DESC');

        if ($consignee_shipments->exists()) {
            $consignee_shipments = $consignee_shipments->get();
            $data = array();
            foreach ($consignee_shipments as $consignee_shipment) {
                $datum = array();
                $pickup_address = UserShippingInfo::find($consignee_shipment->pickup_address_id);
                $user = User::find($pickup_address->user_id);
                $datum['shipment_id'] = $consignee_shipment->shipment_id;
                $datum['tracking_no'] = $consignee_shipment->tracking_no;
                $datum['status_id'] = $consignee_shipment->status_id;
                $datum['status'] = $consignee_shipment->status;
                $datum['amount'] = $consignee_shipment->amount;
                $datum['address'] = $consignee_shipment->consignee_address;
                $datum['in_route'] = $consignee_shipment->delivery_in_route;
                $datum['shipper_name'] = $user->name;
                $datum['person_of_contact'] = $pickup_address->poc;

                $consignee_crm = CrmRequest::where('launched_by_id', $consignee_id)
                    ->where('launched_by', 3)
                    ->where('shipment_id', $consignee_shipment->shipment_id)
                    ->orderBy('id', 'DESC');
                if ($consignee_crm->exists()) {
                    $consignee_crm = $consignee_crm->first();
                    $request_status = $consignee_crm->status_id;
                    if (in_array($request_status, [1, 2, 5])) {
                        $datum['request_status'] = 0;
                    } else {
                        $datum['request_status'] = 1;
                        $datum['request_latitude'] = $consignee_shipment->consignee_latitude;
                        $datum['request_longitude'] = $consignee_shipment->consignee_longitude;
                    }
                } else {
                    $datum['request_status'] = 1;
                    $datum['request_latitude'] = $consignee_shipment->consignee_latitude;
                    $datum['request_longitude'] = $consignee_shipment->consignee_longitude;
                }

                $datum['latitude'] = null;
                $datum['longitude'] = null;
                $datum['runner_id'] = null;
                $datum['origin'] = null;
                $datum['destination'] = null;

                if ($consignee_shipment->status_id == 5) {
                    $datum['latitude'] = $consignee_shipment->consignee_latitude;
                    $datum['longitude'] = $consignee_shipment->consignee_longitude;
                    $delivery_note_shipment = DeliveryNoteShipment::where('shipment_id', $consignee_shipment->id)->latest('delivery_note_id')->first();
                    $delivery_note = $delivery_note_shipment->delivery_note;
                    $rider = $delivery_note->rider;
                    $datum['rider_name'] = $rider->name;
                    $datum['rider_phone_number'] = $rider->phone;
                } elseif (in_array($consignee_shipment->status_id, [3, 49])) {

                    $origin_city = City::where('id', $pickup_address->city_id);
                    if ($origin_city->exists()) {
                        $origin_city = $origin_city->first();
                        $datum['origin'] = $origin_city->name;
                    }

                    $destination_city = City::where('id', $consignee_shipment->consignee_city_id);
                    if ($destination_city->exists()) {
                        $destination_city = $destination_city->first();
                        $datum['destination'] = $destination_city->name;
                        $datum['latitude'] = $destination_city->location_latitude;
                        $datum['longitude'] = $destination_city->location_longitude;
                    }

                    $fleet = Fleet::leftjoin('cargo_manifests as cm', 'fleets.id', '=', 'cm.vehicle_id')
                        ->leftjoin('manifest_bags as mb', 'cm.id', '=', 'mb.cargo_manifest_id')
                        ->leftjoin('cargo_manifest_bag_shipments as cs', 'mb.cargo_manifest_bag_id', '=', 'cs.cargo_manifest_bag_id')
                        ->select('fleets.tracking_id as runner_id')
                        ->where('cs.shipment_id', $consignee_shipment->shipment_id)
                        ->where('cm.status_id', 1)
                        ->where('mb.status', 0);
                    if ($fleet->exists()) {
                        $fleet = $fleet->first();
                        $datum['runner_id'] = $fleet->runner_id;
                    }
                } else {
                    if(in_array($consignee_shipment->status_id, [14, 20])){
                        if($consignee_shipment->status_id == 14){
                            $datum['received_by'] = $consignee_shipment->shipment_journey->received_or_refused_by;
                        }
                        else if ($consignee_shipment->status_id == 20){
                            $datum['refused_by'] = $consignee_shipment->shipment_journey->received_or_refused_by;
                        }
                    }
                    $shipment_journey = ShipmentsJourney::where('shipment_id', $consignee_shipment->shipment_id)
                        ->where('shipper_status_id', $consignee_shipment->status_id)
                        ->orderBy('id', 'DESC');
                    if ($shipment_journey->exists()) {
                        $shipment_journey = $shipment_journey->first();
                        $city = City::where('id', $shipment_journey->city_id);
                        if ($city->exists()) {
                            $city = $city->first();
                            $datum['latitude'] = $city->location_latitude;
                            $datum['longitude'] = $city->location_longitude;
                        }
                    }
                }
                $data[] = $datum;
            }

            return response()->json(['status' => 0, 'data' => $data]);
        }
        return response()->json(['status' => 1, 'message' => "No Current Shipment Found"]);
    }

    public function previous_shipments(Request $request)
    {
        $consignee_id = $request->consignee_id;
        $consignee_info = ConsigneeUser::find($consignee_id);
        $consignee_shipments = Shipment::join('shipment_status as ss', 'shipments.shipper_status_id', '=', 'ss.id')
            ->wherein('consignee_phone_number_1', [$consignee_info->phone_number_1, $consignee_info->phone_number_2])
            ->wherein('shipments.shipper_status_id', [14, 16, 30, 36, 37, 20, 12])
            ->select('shipments.id as shipment_id', 'shipments.tracking_number as tracking_no', 'shipments.shipper_status_id as status_id', 'ss.name as status', 'shipments.amount as amount', 'shipments.delivery_in_route as delivery_in_route', 'shipments.pickup_address_id as pickup_address_id')
            ->orderBy('shipments.id', 'DESC');

        if ($consignee_shipments->exists()) {
            $consignee_shipments = $consignee_shipments->get();
            $data = array();
            foreach ($consignee_shipments as $consignee_shipment) {
                $datum = array();
                $pickup_address = UserShippingInfo::find($consignee_shipment->pickup_address_id);
                $user = User::find($pickup_address->user_id);
                $datum['shipment_id'] = $consignee_shipment->shipment_id;
                $datum['tracking_no'] = $consignee_shipment->tracking_no;
                $datum['status_id'] = $consignee_shipment->status_id;
                $datum['status'] = $consignee_shipment->status;
                $datum['amount'] = $consignee_shipment->amount;
                $datum['shipper_name'] = $user->name;
                $datum['person_of_contact'] = $pickup_address->poc;
                $data[] = $datum;
            }

            return response()->json(['status' => 0, 'data' => $data]);
        }
        return response()->json(['status' => 1, 'message' => "No Previous Shipment Found"]);
    }

    public function shipment_history(Request $request)
    {
        $rules = [
            'shipment_id' => ['required', 'integer', 'digits_between:1,10', 'exists:shipments,id']
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $shipment_info = array();
            $shipment = Shipment::where('id', $request->shipment_id);
            if ($shipment->exists()) {
                $shipment = $shipment->first();
                $shipment_info['tracking_no'] = $shipment->tracking_number;
                $shipment_info['origin'] = $shipment->consignee_city->name;
                $shipment_info['destination'] = $shipment->pickup_address->city->name;
                $shipment_info['shipper'] = $shipment->pickup_address->user->name;
                $shipment_info['amount'] = $shipment->amount;

                $consignee_shipments_journey = ShipmentsJourney::join('shipment_status as ss', 'shipments_journey.shipper_status_id', '=', 'ss.id')
                    ->where('shipments_journey.shipment_id', $request->shipment_id)
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
                return response()->json(['status' => 1, 'message' => "Shipment History Not Found"]);
            }

        }
    }

    public function update_profile(Request $request)
    {
        $consignee_id = $request->consignee_id;
        $rules = [
            'phone_number' => ['nullable', 'regex:/^[0][0-9]{3}-[0-9]{7}$/'],
            'phone_number_updated' => ['required'],
            'pin' => ['nullable', 'integer', 'digits:4'],
            'name' => ['nullable'],
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $consignee_info = ConsigneeUser::where('id', $consignee_id);
            if ($consignee_info->exists()) {
                $consignee_info = $consignee_info->first();
                if ($request->has('name')) {
                    $consignee_info->name = $request->name;
                }
                if ($request->has('phone_number') && $request->phone_number_updated == 1) {
                    $consignee_info->phone_number_2 = $request->phone_number;
                }
                if ($request->has('pin')) {
                    $consignee_info->pin = bcrypt($request->pin);
                }
                $consignee_info->save();
                return response()->json(['status' => 0, 'profile_message' => 'Profile Updated Successfully']);
            } else {
                return response()->json(['status' => 1, 'message' => 'Consignee Not Found']);
            }
        }
    }

    public function address_change_request(Request $request){
        $rules = [
            'shipment_id' => ['required', 'integer', 'digits_between:1,10', 'exists:shipments,id'],
            'address' => ['required', 'string', 'max:255'],
            'location_latitude' => ['required', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'location_longitude' => ['required', 'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'],
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $shipment = Shipment::find($request->shipment_id);
            $shipper_id = $shipment->pickup_address->user_id;
            $consignee_id = $request->consignee_id;
            $description = "Address Change Request From Consignee";
            $consignee_crm = CrmRequest::where('launched_by_id', $consignee_id)
                ->where('launched_by', 3)
                ->where('shipment_id', $request->shipment_id);
            if($consignee_crm->exists()) {
                $consignee_crm = $consignee_crm->first();
                $consignee_crm->address = $request->address;
                $consignee_crm->address_latitude = $request->location_latitude;
                $consignee_crm->address_longitude = $request->location_longitude;
                $consignee_crm->status_id = 5;
                $consignee_crm->save();
            }
            else{
                $crm_request_id = CRMController::add(2, 11, 7, 1, $consignee_id , 3, $request->shipment_id,$shipper_id, NULL, $description);
                $crm_request = CrmRequest::find($crm_request_id);
                $crm_request->address = $request->address;
                $crm_request->address_latitude = $request->location_latitude;
                $crm_request->address_longitude = $request->location_longitude;
                $crm_request->save();
            }
            return response()->json(['status' => 0, 'message' => 'Request For Address Change Has Been Submitted']);
        }
    }

    public function notification_history(Request $request)
    {
        $consignee_id = $request->consignee_id;
        $from_date = Carbon::now()->subDays(30)->format('Y-m-d 00:00:00');
        $to_date = Carbon::now()->format('Y-m-d 23:59:59');

        $notifiction_history = EmployeeNotificationHistory::where('employee_id', $consignee_id)
            ->where('employee_type_id', 4)
            ->whereBetween('created_at', [$from_date, $to_date])
            ->orderBy('created_at', 'desc');
        if ($notifiction_history->exists()) {
            $notifiction_history = $notifiction_history->get();
            return response()->json(['status' => 0, 'data' => $notifiction_history]);
        }
        return response()->json(['status' => 1, 'message' => "Notification History Not Found"]);
    }

    public function update_pin(Request $request)
    {
        $rules = [
            'phone_number' => ['required', 'regex:/^[0][0-9]{3}-[0-9]{7}$/'],
            'pin' => ['required', 'integer', 'digits:4'],
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $consignee_info = ConsigneeUser::where('phone_number_1',$request->input('phone_number'));
            if ($consignee_info->exists()) {
                $consignee_info = $consignee_info->first();
                $consignee_info->pin = bcrypt($request->pin);
                $consignee_info->save();
                return response()->json(['status' => 0, 'Update_pin_message' => 'PIN Updated Successfully']);
            } else {
                return response()->json(['status' => 1, 'message' => 'Consignee Not Found']);
            }
        }
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