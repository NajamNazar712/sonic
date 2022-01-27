<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Admins\AdminPickupsController;
use App\Http\Controllers\Admins\DisputeController;
use App\Http\Controllers\Admins\DwsWeightChargesController;
use App\Http\Controllers\Admins\ShipmentChargesController;
use App\Http\Controllers\Retail\RetailShipmentBookController;
use App\Http\Controllers\Webhook\InitialChargesWebhookController;
use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\AdminAppSlider;
use App\Http\Models\Admin\AdminDepartment;
use App\Http\Models\Admin\AdminHub;
use App\Http\Models\Admin\AdminUserRequest;
use App\Http\Models\Admin\Attendance\EmployeeAttendance;
use App\Http\Models\Admin\Attendance\EmployeeAttendanceActionLog;
use App\Http\Models\Admin\BookingSmsForShippers;
use App\Http\Models\Admin\CargoManifest\CargoManifest;
use App\Http\Models\Admin\CargoManifest\CargoManifestBag;
use App\Http\Models\Admin\CargoManifest\CargoManifestBagShipments;
use App\Http\Models\Admin\CargoManifest\ManifestBag;
use App\Http\Models\Admin\CargoManifest\V2Junctions;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Admin\Lead\Lead;
use App\Http\Models\Admin\Lead\LeadRemark;
use App\Http\Models\Admin\Lead\LeadStatus;
use App\Http\Models\Admin\Retail\RetailCashDeposit;
use App\Http\Models\Admin\Retail\RetailCashDepositShipment;
use App\Http\Models\Admin\Retail\RetailPaymentMode;
use App\Http\Models\Admin\Retail\RetailShipment;
use App\Http\Models\Admin\Retail\RetailShipperInfo;
use App\Http\Models\Admin\Retail\RetailShippingMode;
use App\Http\Models\Admin\Retail\RetailTraxBox;
use App\Http\Models\Admin\Retail\RetailTraxCenter;
use App\Http\Models\Admin\RetailPickupNote;
use App\Http\Models\Admin\ReturnNote;
use App\Http\Models\Admin\ReturnNoteImage;
use App\Http\Models\AppNotification;
use App\Http\Models\BanksList;
use App\Http\Models\BusinessCategory;
use App\Http\Models\City;
use App\Http\Models\CityDelivery;
use App\Http\Models\ConsolidationShipments;
use App\Http\Models\DwsDetail;
use App\Http\Models\DwsWeightCharges;
use App\Http\Models\EmployeeDeviceToken;
use App\Http\Models\EmployeeNotificationHistory;
use App\Http\Models\EmployeeShift;
use App\Http\Models\HR\Employee;
use App\Http\Models\HR\EmployeeAttachment;
use App\Http\Models\HR\EmployeeBankInformation;
use App\Http\Models\HR\EmployeeBloodGroup;
use App\Http\Models\HR\EmployeeEducationalBackground;
use App\Http\Models\HR\EmployeeEmployementHistory;
use App\Http\Models\HR\EmployeeLeave;
use App\Http\Models\HR\EmployeeMedicalInformation;
use App\Http\Models\HR\EmployeePayslip;
use App\Http\Models\InternationalShipment;
use App\Http\Models\PayslipPdf;
use App\Http\Models\PendingDwsWeightCharges;
use App\Http\Models\Product;
use App\Http\Models\RateStatus;
use App\Http\Models\ReceivingSheetReceived;
use App\Http\Models\ReportingLocation;
use App\Http\Models\Rider;
use App\Http\Models\SelfCollectionShipment;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentDetail;
use App\Http\Models\ShipmentPiecesRequest;
use App\Http\Models\Shipper\UserShippingInfo;
use App\Http\Models\V2Pickup\V2PickupNote;
use App\Http\Models\V2Pickup\V2PickupNoteRequest;
use App\Http\Models\V2Pickup\V2PickupReceivedShipment;
use App\Http\Models\V2Pickup\V2PickupRequest;
use App\Http\Models\V2Pickup\V2PickupRequestShipment;
use App\Http\Models\Zone;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Password;
use phpDocumentor\Reflection\PseudoTypes\False_;

class AdminAPIController extends Controller
{
    use SendsPasswordResetEmails;

    public function broker(){
        return Password::broker('admins');
    }

    private $names = [
        'email_address' => 'Email Address',
        'password' => 'Password',
        'attendance_date' => 'Attendance Date',
        'latitude' => 'Latitude',
        'longitude' => 'Longitude',
        'action' => 'Action',
        'from_date' => 'From Date'

    ];

    private $messages = [
        'integer' => ':attribute must be an Integer.',
        'digits' => ':attribute must be of :digits Digits.',
        'exists' => 'Given :attribute is of Invalid ID.',
        'image' => ':attribute must be an Image.'
    ];

    private function distance($origin, $destination)
    {
        return $this->vincenty_distance($origin, $destination);
    }

    private function vincenty_distance($origin, $destination)
    {
        $earth_radius = 6371;

        list($origin_latitude, $origin_longitude) = explode(',', $origin);
        list($destination_latitude, $destination_longitude) = explode(',', $destination);

        $origin_latitude = deg2rad($origin_latitude);
        $origin_longitude = deg2rad($origin_longitude);
        $destination_latitude = deg2rad($destination_latitude);
        $destination_longitude = deg2rad($destination_longitude);

        $longitude_delta = $destination_longitude - $origin_longitude;

        $distance = round($earth_radius * (atan2(sqrt(pow(cos($destination_latitude) * sin($longitude_delta), 2) + pow(cos($origin_latitude) * sin($destination_latitude) - sin($origin_latitude) * cos($destination_latitude) * cos($longitude_delta), 2)), (sin($origin_latitude) * sin($destination_latitude) + cos($origin_latitude) * cos($destination_latitude) * cos($longitude_delta)))), 2);

        return $distance;
    }

    public static function generateDateRange($start_date, $end_date)
    {
        $start_date = Carbon::parse($start_date);
        $end_date = Carbon::parse($end_date);
        $dates = [];
        for($date = $start_date->copy(); $date->lte($end_date); $date->addDay()) {
            $dates[] = $date->format('Y-m-d');
        }

        return $dates;
    }

    private function calculate_location_status($latitude, $longitude)
    {
        $location_status = 1;
        $reporting_locations = ReportingLocation::where('status', 1);
        if ($reporting_locations->exists()) {
            $reporting_locations = $reporting_locations->get();
            foreach ($reporting_locations as $reporting_location) {
                $reporting_location->radius;
                $destination = $reporting_location->lat . ',' . $reporting_location->long;
                $origin = $latitude . ',' . $longitude;
                $distance = $this->distance($origin, $destination);
                if ($distance <= $reporting_location->radius / 1000) {
                    $location_status = 2;
                    return $location_status;
                }
            }
        } else {
            $location_status = 0;
        }
        return $location_status;
    }

    public function verify(Request $request)
    {
        return response()->json(['status' => 0, 'message' => 'API Key is Valid']);
    }

    public function login(Request $request)
    {
        $rules = [
            'email_address' => ['required', 'email'],
            'password' => ['required', 'min:6'],
            'device_token' => ['nullable']
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $user = Admin::where('email', $request->input('email_address'));
            $password = substr($request->input('password'), 2);
            if ($user->exists()) {
                $user = $user->first();
                if($user->status == 0){
                    return response()->json(['status' => 1, 'message' => 'Account disabled, Please contact admin!']);
                }
                if (Hash::check($password, $user->password)) {
                    $employee = Employee::where('trax_id', $user->trax_id);
                    $information = array();

                    $information['id'] = $user->id;
                    $information['name'] = $user->name;
                    $information['phone'] = $user->phone_number;
                    $information['cnic'] = $user->cnic;
                    $information['cargo_user'] = ($user->role_id == 11) ? 1 : 0;
                    if ($employee->exists()) {
                        $employee = $employee->first();
                        $information['address'] = ($employee->address) ? $employee->address : "" ;
                    } else {
                        $information['address'] = '';
                    }
                    $information['role'] = 'staff';

                    if($request->has('device_token')){
                        EmployeeDeviceToken::where('device_token', $request->get('device_token'))->delete();
                        $employee_device_token = EmployeeDeviceToken::where('employee_id', $user->id)
                            ->where('employee_type_id', 1);
                        if ($employee_device_token->exists()) {
                            $employee_device_token = $employee_device_token->first();
                        } else {
                            $employee_device_token = new EmployeeDeviceToken();
                            $employee_device_token->employee_id = $user->id;
                            $employee_device_token->employee_type_id = 1;
                        }
                        $employee_device_token->device_token = $request->get('device_token');
                        $employee_device_token->save();
                    }

                    $reporting_location = ReportingLocation::join('employees as e', 'reporting_locations.id', 'e.reporting_location_id')
                        ->join('admins as a', 'e.id', 'a.employee_id')
                        ->where('a.id', $user->id);

                    if ($reporting_location->exists()) {
                        $reporting_location = $reporting_location->first();
                        $information['distance'] = $reporting_location->radius;
                        $information['lat'] = $reporting_location->lat;
                        $information['long'] = $reporting_location->long;
                    }else{
                        $information['distance'] = 0;
                        $information['lat'] = 0;
                        $information['long'] = 0;
                    }

                    if ($user->api_token) {
                        $information['api_token'] = $user->api_token;
                    } else {
                        $api_token = uniqid(base64_encode(str_random(60)));

                        $user->api_token = $api_token;

                        $user->save();

                        $information['api_token'] = $api_token;
                    }

                    return response()->json(['status' => 0, 'message' => 'Logged In Successfully', 'information' => $information]);
                } else {
                    return response()->json(['status' => 1, 'message' => 'Invalid Password']);
                }
            } else {
                return response()->json(['status' => 1, 'message' => 'Wrong Email/Password!']);
            }
        }
    }

    public function return_note_details(Request $request){
        $admin_id = $request->admin_id;
        $return_note_id = $request->return_note_id;
        if($return_note_id){
            $return_note = ReturnNote::find($return_note_id);
            if($return_note){
                if($return_note->status == 1 || $return_note->status == 3){
                    if($return_note->image !== null){
                        $details = array();
                        $img_url = '';
                        $url = 'uploads/return_notes/' . $return_note->image;
                        if(file_exists($url)){
                            $img_url = asset('uploads/return_notes/' . $return_note->image);
                        }
                        else{
                            $exists = Storage::disk('s3')->exists('return_note_images/'.$return_note->image);
                            if($exists){
                                $img_url = Storage::disk('s3')->temporaryUrl('return_note_images/'.$return_note->image, now()->addMinutes(5));
                            }
                        }
                        $details['return_note_id'] = $return_note_id;
                        $details['images'] = array('id' => 0,'image'=> $img_url);
                        return response()->json(['status' => 0, 'message' => 'Images found', 'information' => $details]);
                    }else{
                        $return_note_images = ReturnNoteImage::where('return_note_id', $return_note_id);
                        if($return_note_images->exists()){
                            $return_note_images = $return_note_images->get();
                            $details = array();
                            foreach ($return_note_images as $return_note_image) {
                                $img_url = '';
                                $url = 'uploads/return_notes/' . $return_note_image->image;
                                if(file_exists($url)){
                                    $img_url = asset('uploads/return_notes/' . $return_note_image->image);
                                }else{
                                    $exists = Storage::disk('public')->exists('uploads/return_notes/'.$return_note_image->image);
                                    if($exists){
                                        $img_url = asset('storage/uploads/return_notes/'.$return_note_image->image);
                                    }
                                    else{
                                        $exists = Storage::disk('s3')->exists('return_note_images/'.$return_note_image->image);
                                        if($exists){
                                            $img_url = Storage::disk('s3')->temporaryUrl('return_note_images/'.$return_note_image->image, now()->addMinutes(5));
                                        }
                                    }
                                }
                                $details['images'][] = array('id' => $return_note_image->id,'image'=> $img_url);
                            }
                            return response()->json(['status' => 0, 'message' => 'Images found', 'information' => $details]);
                        }else{
                            return response()->json(['status' => 0, 'message' => 'No images found!', 'information' => '']);
                        }
                    }
                }else{
                    return response()->json(['status' => 1, 'message' => 'Return Note not ready for image upload!']);
                }
            }else{
                return response()->json(['status' => 1, 'message' => 'Return Note Image not found!']);
            }
        }else {
            return response()->json(['status' => 1, 'message' => 'No return note scanned!']);
        }
    }

    public function history_update_image(Request $request)
    {
        $rules = [
            'added_at' => ['required'],
            'return_note_id' => ['required', 'integer', 'digits_between:1,10', 'exists:return_notes,id'],
            'pictures' => ['array','nullable'],
            'pictures.*image' => ['nullable', 'image', 'mimes:jpeg,png', 'max:2048'],
            'old_image_ids' => ['array', 'min:0']
        ];
        $validate = Validator::make($request->all(), $rules, $this->messages);
        $validate->setAttributeNames($this->names);
        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        }
        $admin_id = $request->admin_id;
        $old_image_ids = array();
        $old_image_ids = ($request->old_image_ids != '')? $request->old_image_ids:[];
        $return_note_id = $request->return_note_id;
        $return_note = ReturnNote::find($return_note_id);
        if ($return_note) {
            $present = false;

            $pictures = array();
            if($request->has('pictures')){
                $pictures = $request->pictures;
            }
            $return_note_image_ids = ReturnNoteImage::where('return_note_id', $return_note_id)->pluck('id')->toArray();
            if (count($return_note_image_ids) > 0) {
                foreach ($return_note_image_ids as $image_id) {
                    if (!in_array($image_id, $old_image_ids)) {
                        $return_note_image = ReturnNoteImage::where('id', $image_id)->first();
                        if ($return_note_image) {
                            $url = 'uploads/return_notes/' . $return_note_image->image;
                            if (file_exists($url)) {
                                File::delete($url);
                            }
                            else {
                                $exists = Storage::disk('public')->exists('uploads/return_notes/'.$return_note_image->image);
                                if($exists){
                                    Storage::disk('public')->delete('uploads/return_notes/'.$return_note_image->image);
                                }else{
                                    $exists = Storage::disk('s3')->exists('return_note_images/' . $return_note_image->image);
                                    if ($exists) {
                                        Storage::disk('s3')->delete('return_note_images/' . $return_note_image->image);
                                    }
                                }
                            }
                            ReturnNoteImage::where('id', $image_id)->delete();
                        }
                    }
                }

                $present = true;
            }
            if (count($pictures) > 0) {
                foreach ($pictures as $picture) {
                    $image = $picture;
                    $extension = 'png';
                    $random = rand(1000, 100000);
                    $now = Carbon::now();
                    $time = $now->year . '_' . $now->month;
                    $generated_image_name = $time . $random . $admin_id . '.' . $extension;
                    Storage::disk('public')->put('uploads/return_notes/' . $generated_image_name, file_get_contents($image));
                    $return_note_image = new ReturnNoteImage();
                    $return_note_image->return_note_id = $return_note_id;
                    $return_note_image->image = $generated_image_name;
                    $return_note_image->save();
                }

                $present = true;
            }

            if ($present && in_array($return_note->status, [1, 3])) {
                $return_note->updated_by = $admin_id;
                $return_note->status = 1;
                $return_note->save();
            }

            return response()->json(['status' => 0, 'success' => 'Image insert successfully!']);
        }
        return response()->json(['status' => 1, 'error' => 'Return Note not found!']);
    }

    public function mark_attendance(Request $request)
    {

        $rules = [
            'attendance_date' => ['required'],
            'latitude' => ['required', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'longitude' => ['required', 'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'],
            'action' => ['required', 'integer', 'digits_between:1,10', 'exists:attendance_actions,id'],
        ];

        $admin_id = $request->admin_id;
        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {

            $location_status = 0;
            $reporting_location = ReportingLocation::join('employees as e', 'reporting_locations.id', 'e.reporting_location_id')
                ->join('admins as a', 'e.id', 'a.employee_id')
                ->where('a.id', $admin_id);
            if($reporting_location->exists()){
                $reporting_location = $reporting_location->first();
                $reporting_location->radius;
                $destination = $reporting_location->lat . ',' . $reporting_location->long;
                $origin = $request->latitude . ',' . $request->longitude;
                $distance = $this->distance($origin, $destination);
                if ($distance > $reporting_location->radius / 1000) {
                    $location_status = 1;
                } else {
                    $location_status = 2;
                }
            }

            $attendance_datetime = Carbon::parse($request->attendance_date)->format('Y-m-d H:i:s');
            $attendance_date = Carbon::parse($request->attendance_date)->format('Y-m-d');
            $attendance_time = Carbon::parse($request->attendance_date)->format('H:i:s');

            $admin_attendance = EmployeeAttendance::where('employee_id', $admin_id)
                ->whereDate('attendance_date', $attendance_date)
                ->where('employee_type', 1);
            $admin_attendance_action = new EmployeeAttendanceActionLog();
            if ($admin_attendance->exists()) {
                $admin_attendance = $admin_attendance->first();
            } else {
                $admin_attendance = new EmployeeAttendance();
                $admin_attendance->employee_id = $admin_id;
                $admin_attendance->employee_type = 1;
                $admin_attendance->attendance_date = $attendance_date;
            }
            if ($request->action == 1) {
                $admin_attendance->clock_in = $attendance_time;
                $admin_attendance->clock_in_datetime = $attendance_datetime;
                $admin_attendance->clock_in_latitude = $request->latitude;
                $admin_attendance->clock_in_longitude = $request->longitude;
                $admin_attendance->clock_in_location = $location_status;
                $admin_attendance->save();

                $admin_attendance_action->employee_id = $admin_id;
                $admin_attendance_action->employee_type = 1;
                $admin_attendance_action->action_id = $request->action;
                $admin_attendance_action->action_date = $attendance_datetime;
                $admin_attendance_action->attendance_date = $attendance_date;
                $admin_attendance_action->latitude = $request->latitude;
                $admin_attendance_action->longitude = $request->longitude;
                $admin_attendance_action->location_status = $location_status;
                $admin_attendance_action->save();

                return response()->json(['status' => 0, 'message' => 'Clocked-In Successfully', 'response' => $admin_attendance_action]);
            } elseif ($request->action == 2) {
                $admin_attendance->clock_out = $attendance_time;
                $admin_attendance->clock_out_datetime = $attendance_datetime;
                $admin_attendance->clock_out_latitude = $request->latitude;
                $admin_attendance->clock_out_longitude = $request->longitude;
                $admin_attendance->clock_out_location = $location_status;
                $admin_attendance->save();

                $admin_attendance_action->employee_id = $admin_id;
                $admin_attendance_action->employee_type = 1;
                $admin_attendance_action->action_id = $request->action;
                $admin_attendance_action->action_date = $attendance_datetime;
                $admin_attendance_action->attendance_date = $attendance_date;
                $admin_attendance_action->latitude = $request->latitude;
                $admin_attendance_action->longitude = $request->longitude;
                $admin_attendance_action->location_status = $location_status;
                $admin_attendance_action->save();
                return response()->json(['status' => 0, 'message' => 'Clocked-Out Successfully', 'response' => $admin_attendance_action]);
            }

            return response()->json(['status' => 1, 'message' => 'Failed']);
        }

    }

    public function attendance_details(Request $request)
    {
        $rules = [
            'attendance_date' => ['required']
        ];
        $admin_id = $request->admin_id;
        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $admin_attendance_action = EmployeeAttendanceActionLog::where('employee_id', $admin_id)
                ->whereDate('action_date', $request->attendance_date)
                ->where('employee_type', 1)
                ->select('action_id', 'action_date', 'latitude', 'longitude', 'location_status')
                ->orderBy('action_date', 'ASC');
            if ($admin_attendance_action->exists()) {
                $admin_attendance_action = $admin_attendance_action->get();
                return response()->json(['status' => 0, 'attendance_details' => $admin_attendance_action]);
            }
            return response()->json(['status' => 0, 'attendance_details' => []]);
        }
    }

    public function attendance_history(Request $request)
    {
        $rules = [
            'from_date' => ['required'],
            'to_date' => ['nullable']
        ];
        $admin_id = $request->admin_id;
        $from_date = $request->get('from_date');
        $to_date = $request->get('to_date');
        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {

            $admin_attendance = EmployeeAttendance::where('admin_id', $admin_id)
                ->orderBy('attendance_date', 'ASC');

            if ($to_date != null) {
                $admin_attendance = $admin_attendance->whereBetween('attendance_date', [$from_date, $to_date]);
            } else {
                $admin_attendance = $admin_attendance->whereDate('attendance_date', $from_date);
            }

            if ($admin_attendance->exists()) {
                $admin_attendance = $admin_attendance->get();
                return response()->json(['status' => 0, 'history_details' => $admin_attendance]);
            }
            return response()->json(['status' => 1, 'message' => "No Details Found"]);
        }
    }

    public function admin_signup(Request $request)
    {
        if ($request->isMethod('post')) {
            $rules = [
                //Employees
                'name' => ['nullable'],
                'employee_gender_id' => ['nullable'],
                'city_id' => ['nullable', 'integer', 'digits_between:1,10', 'exists:cities,id'],
                'cnic_no' => ['nullable', 'regex:/^[0-9]{5}-[0-9]{7}-[0-9]{1}$/'],
                'phone_number' => ['nullable', 'regex:/^[0][0-9]{3}-[0-9]{7}$/'],
                'guardian_name' => ['nullable'],
                'religion_id' => ['nullable', 'integer', 'digits_between:1,10', 'exists:employee_religions,id'],
                'nationality_id' => ['nullable', 'integer', 'digits_between:1,10', 'exists:employee_nationalities,id'],
                'domicile_id' => ['nullable', 'integer', 'digits_between:1,10', 'exists:employee_domiciles,id'],
                'marital_status_id' => ['nullable', 'integer', 'digits_between:1,10', 'exists:employee_marital_statuses,id'],
                'blood_group_id' => ['nullable', 'integer', 'digits_between:1,10', 'exists:employee_blood_groups,id'],
                'personal_email' => ['nullable', 'email'],
                'address' => ['nullable'],
                'emergency_contact' => ['nullable'],
                'cnic_issue_date' => ['nullable'],
                'cnic_expiry_date' => ['nullable'],
                'designation_id' => ['nullable', 'integer', 'digits_between:1,10', 'exists:employee_designations,id'],
                'department_id' => ['nullable', 'integer', 'digits_between:1,10', 'exists:admin_departments,id'],
                'zone_id' => ['nullable', 'integer', 'digits_between:1,10', 'exists:zones,id'],
                'official_email' => ['nullable', 'email'],
                'official_phone_number' => ['nullable', 'regex:/^[0][0-9]{3}-[0-9]{7}$/'],
                'sonic_id' => ['nullable'],
                'place_of_birth' => ['nullable', 'integer', 'digits_between:1,10', 'exists:cities,id'],
                'date_of_birth' => ['nullable'],
                'pin' => ['nullable', 'integer', 'digits:4'],

                //EducationalDetails
                'education_details' => ['nullable'],

                //BankInformation
                'bank_details' => ['nullable'],

                //EmploymentHistory
                'employment_history' => ['nullable'],

                //MedicalDetails
                'medical_details' => ['nullable'],
            ];
            $response = ['status' => 1];
            $message = 'Unknown';

            $validate = Validator::make($request->all(), $rules, $this->messages);

            $validate->setAttributeNames($this->names);

            if ($validate->fails()) {
                $message = 'Error(s) in Input';
                $response['errors'] = $validate->errors();
            } else {
                $admin = Admin::where('phone_number', $request->input('phone_number'))
                    ->orWhere('cnic', $request->input('cnic_no'));

                $employee = Employee::where('employee_type_id', 1)
                    ->where('phone_number', $request->input('phone_number'))
                    ->orWhere('cnic', $request->input('cnic_no'));

                $user_request = AdminUserRequest::where('phone_number', $request->input('phone_number'))
                    ->orWhere('cnic', $request->input('cnic_no'));

                //Check Admin Already Exist
                if ($admin->exists()) {
                    $admin = $admin->first();
                    if ($admin->phone_number == $request->input('phone_number') && $admin->cnic == $request->input('cnic_no')) {
                        $message = "Phone Number & CNIC Already Exists";

                    } else if ($admin->phone_number == $request->input('phone_number')) {
                        $message = "Phone Number Already Exist";

                    } else if ($admin->cnic == $request->input('cnic_no')) {
                        $message = "CNIC Already Exist";
                    }
                } else if ($employee->exists()) {
                    $employee = $employee->first();
                    if ($employee->phone_number == $request->input('phone_number') && $employee->cnic == $request->input('cnic_no')) {
                        $message = "Phone Number & CNIC Already Exists";

                    } else if ($employee->phone_number == $request->input('phone_number')) {
                        $message = "Phone Number Already Exist";

                    } else if ($employee->cnic == $request->input('cnic_no')) {
                        $message = "CNIC Already Exist";
                    }
                } else if ($user_request->exists()) {
                    $user_request = $user_request->first();
                    if ($user_request->phone_number == $request->input('phone_number') && $user_request->cnic == $request->input('cnic_no')) {
                        $message = "Phone Number & CNIC Already Exists";

                    } else if ($user_request->phone_number == $request->input('phone_number')) {
                        $message = "Phone Number Already Exist";

                    } else if ($user_request->cnic == $request->input('cnic_no')) {
                        $message = "CNIC Already Exist";
                    }
                } else {
                    try {
                        $employee_request = new Employee();
                        $employee_request->name = $request->name;
                        $employee_request->employee_gender_id = $request->employee_gender_id;
                        $employee_request->city_id = $request->city_id;
                        $employee_request->cnic = $request->cnic_no;
                        $employee_request->phone_number = $request->phone_number;
                        $employee_request->employee_type_id = 1;
                        $employee_request->status_id = 2;
                        $employee_request->guardian_name = $request->guardian_name;
                        $employee_request->religion_id = $request->religion_id;
                        $employee_request->nationality_id = $request->nationality_id;
                        $employee_request->domicile_id = $request->domicile_id;
                        $employee_request->marital_status_id = $request->marital_status_id;
                        $employee_request->blood_group = $request->blood_group_id;
                        $employee_request->personal_email = $request->personal_email;
                        $employee_request->address = $request->address;
                        $employee_request->emergency_contact = $request->emergency_contact;
                        $employee_request->cnic_issue_date = $request->cnic_issue_date;
                        $employee_request->cnic_expiry_date = $request->cnic_expiry_date;
                        $employee_request->designation_id = $request->designation_id;
                        $employee_request->department_id = $request->department_id;
                        $employee_request->zone_id = $request->zone_id;
                        $employee_request->official_email = $request->official_email;
                        $employee_request->official_phone_number = $request->official_phone_number;
                        $employee_request->sonic_id = $request->sonic_id;
                        $employee_request->place_of_birth = $request->place_of_birth;
                        $employee_request->date_of_birth = $request->date_of_birth;
                        $employee_request->pin = $request->pin;
                        $employee_request->save();

                        if ($request->has('employment_history')) {
                            $employment_histories = json_decode($request->employment_history, true);
                            foreach ($employment_histories as $employment_history) {
                                $history = new EmployeeEmployementHistory();
                                $history->employee_id = $employee_request->id;
                                $history->name = $employment_history['organization_company_name'];
                                $history->designation = $employment_history['position_designation'];
                                $history->from = $employment_history['from_date'];
                                $history->to = $employment_history['to_date'];
                                $history->reason = $employment_history['reason'];
                                $history->save();
                            }
                        }

                        if ($request->has('medical_details')) {
                            $medical_details = json_decode($request->medical_details, true);
                            foreach ($medical_details as $medical_detail) {
                                $medical_info = new EmployeeMedicalInformation();
                                $medical_info->employee_id = $employee_request->id;
                                $medical_info->name = $medical_detail['name_of_family_member'];
                                $medical_info->relationship_id = $medical_detail['relation_ship'];
                                $medical_info->date_of_birth = $medical_detail['date_of_birth'];
                                $medical_info->marital_status = $medical_detail['marital_status'];
                                $medical_info->save();
                            }
                        }

                        if ($request->has('education_details')) {
                            $education_details = json_decode($request->education_details, true);
                            foreach ($education_details as $education_detail) {
                                $employee_education = new EmployeeEducationalBackground();
                                $employee_education->employee_id = $employee_request->id;
                                $employee_education->name = $education_detail['institute'];
                                $employee_education->degree = $education_detail['degree'];
                                $employee_education->grade = $education_detail['position_grade'];
                                $employee_education->passing_year = $education_detail['graduation_year'];
                                $employee_education->save();
                            }
                        }

                        if ($request->has('bank_details')) {
                            $bank_details = json_decode($request->bank_details, true);
                            foreach ($bank_details as $bank_detail) {
                                $employee_bank_info = new EmployeeBankInformation();
                                $employee_bank_info->employee_id = $employee_request->id;
                                $employee_bank_info->account_title = $bank_detail['account_tile'];
                                $employee_bank_info->branch_code = $bank_detail['branch_code'];
                                $employee_bank_info->account_no = $bank_detail['account_number'];
                                $employee_bank_info->bank_id = $bank_detail['bank'];
                                $employee_bank_info->branch_name = $bank_detail['branch'];
                                $employee_bank_info->iban = $bank_detail['iban_no'];
                                $employee_bank_info->save();
                            }
                        }
                        $response['status'] = 0;
                        $response['employee_id'] = $employee_request->id;
                        $message = 'Request Has Been Submitted and Pending for Approval';
                    } catch (Exception $ex) {
                        $response['message'] = $ex;
                    }
                }
            }
        } else {
            $message = 'Post Method is Required';
        }
        $response['message'] = $message;
        return response()->json($response);
    }

    public function admin_attachments_store(Request $request)
    {
        $rules = [
            //Attachments
            'employee_id' => ['required', 'integer', 'digits_between:1,10', 'exists:employees,id'],
            'cv' => ['mimes:png,jpeg,jpg,pdf'],
            'academic_credentials' => ['mimes:png,jpeg,jpg,pdf'],
            'cnic' => ['mimes:png,jpeg,jpg,pdf'],
            'photo' => ['mimes:png,jpeg,jpg,pdf'],
            'experience_certificates' => ['mimes:png,jpeg,jpg,pdf'],
            'pay_slip' => ['mimes:png,jpeg,jpg,pdf'],
            'nikkah_nama' => ['mimes:png,jpeg,jpg,pdf'],
            'cnic_spouse' => ['mimes:png,jpeg,jpg,pdf'],
            'bform' => ['mimes:png,jpeg,jpg,pdf'],
            'cnic_nominee' => ['mimes:png,jpeg,jpg,pdf'],
            'utility_bill' => ['mimes:png,jpeg,jpg,pdf'],
            'affidavit' => ['mimes:png,jpeg,jpg,pdf'],
            'cheque' => ['mimes:png,jpeg,jpg,pdf'],
        ];
        $response = ['status' => 1];
        $message = 'Unknown';

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            $message = 'Error(s) in Input';
            $response['errors'] = $validate->errors();
        } else {

            $employee_id = $request->employee_id;
            $attachments = EmployeeAttachment::where('employee_id', $employee_id);
            if ($attachments->exists()) {
                $attachments = $attachments->first();
            } else {
                $attachments = new EmployeeAttachment();
                $attachments->employee_id = $employee_id;
            }

            $date = Carbon::now()->format('Y_m_d');

            if ($request->hasFile('cv')) {
                if ($attachments->cv != NULL) {
                    Storage::disk('public')->delete($attachments->cv);
                }

                $file = $request->file('cv');
                $filename = 'cv_' . $date . '.' . $file->extension();
                $directory = 'employee_directory/employee_' . $employee_id . '';
                Storage::disk('public')->putFileAs($directory, $file, $filename);
                $attachments->cv = $directory . '/' . $filename;
                $response['link'] = $attachments->cv;
            }

            if ($request->hasFile('cnic')) {
                if ($attachments->cnic != NULL) {
                    Storage::disk('public')->delete($attachments->cnic);
                }

                $file = $request->file('cnic');
                $filename = 'cnic_' . $date . '.' . $file->extension();
                $directory = 'employee_directory/employee_' . $employee_id . '';
                Storage::disk('public')->putFileAs($directory, $file, $filename);
                $attachments->cnic = $directory . '/' . $filename;
                $response['link'] = $attachments->cnic;
            }

            if ($request->hasFile('photo')) {
                if ($attachments->photo != NULL) {
                    Storage::disk('public')->delete($attachments->cnic);
                }

                $file = $request->file('photo');
                $filename = 'photo_' . $date . '.' . $file->extension();
                $directory = 'employee_directory/employee_' . $employee_id . '';
                Storage::disk('public')->putFileAs($directory, $file, $filename);
                $attachments->photo = $directory . '/' . $filename;
                $response['link'] = $attachments->photo;
            }

            if ($request->hasFile('academic_credentials')) {
                if ($attachments->academic != NULL) {
                    Storage::disk('public')->delete($attachments->academic);
                }

                $file = $request->file('academic_credentials');
                $filename = 'academic_credentials_' . $date . '.' . $file->extension();
                $directory = 'employee_directory/employee_' . $employee_id . '';
                Storage::disk('public')->putFileAs($directory, $file, $filename);
                $attachments->academic = $directory . '/' . $filename;
                $response['link'] = $attachments->academic;
            }

            if ($request->hasFile('experience_certificates')) {
                if ($attachments->experience != NULL) {
                    Storage::disk('public')->delete($attachments->experience);
                }

                $file = $request->file('experience_certificates');
                $filename = 'experience_certificates_' . $date . '.' . $file->extension();
                $directory = 'employee_directory/employee_' . $employee_id . '';
                Storage::disk('public')->putFileAs($directory, $file, $filename);
                $attachments->experience = $directory . '/' . $filename;
                $response['link'] = $attachments->experience;
            }

            if ($request->hasFile('pay_slip')) {
                if ($attachments->last_pay_slip != NULL) {
                    Storage::disk('public')->delete($attachments->last_pay_slip);
                }

                $file = $request->file('pay_slip');
                $filename = 'pay_slip_' . $date . '.' . $file->extension();
                $directory = 'employee_directory/employee_' . $employee_id . '';
                Storage::disk('public')->putFileAs($directory, $file, $filename);
                $attachments->last_pay_slip = $directory . '/' . $filename;
                $response['link'] = $attachments->last_pay_slip;
            }

            if ($request->hasFile('nikkah_nama')) {
                if ($attachments->nikkah_nama != NULL) {
                    Storage::disk('public')->delete($attachments->nikkah_nama);
                }

                $file = $request->file('nikkah_nama');
                $filename = 'nikkah_nama_' . $date . '.' . $file->extension();
                $directory = 'employee_directory/employee_' . $employee_id . '';
                Storage::disk('public')->putFileAs($directory, $file, $filename);
                $attachments->nikkah_nama = $directory . '/' . $filename;
                $response['link'] = $attachments->nikkah_nama;
            }

            if ($request->hasFile('cnic_spouse')) {
                if ($attachments->cnic_spouse != NULL) {
                    Storage::disk('public')->delete($attachments->cnic_spouse);
                }

                $file = $request->file('cnic_spouse');
                $filename = 'cnic_spouse_' . $date . '.' . $file->extension();
                $directory = 'employee_directory/employee_' . $employee_id . '';
                Storage::disk('public')->putFileAs($directory, $file, $filename);
                $attachments->cnic_spouse = $directory . '/' . $filename;
                $response['link'] = $attachments->cnic_spouse;
            }

            if ($request->hasFile('bform')) {
                if ($attachments->child_b_form != NULL) {
                    Storage::disk('public')->delete($attachments->child_b_form);
                }

                $file = $request->file('bform');
                $filename = 'child_b_form_' . $date . '.' . $file->extension();
                $directory = 'employee_directory/employee_' . $employee_id . '';
                Storage::disk('public')->putFileAs($directory, $file, $filename);
                $attachments->child_b_form = $directory . '/' . $filename;
                $response['link'] = $attachments->child_b_form;
            }

            if ($request->hasFile('cnic_nominee')) {
                if ($attachments->cnic_nominee != NULL) {
                    Storage::disk('public')->delete($attachments->cnic_nominee);
                }

                $file = $request->file('cnic_nominee');
                $filename = 'cnic_nominee_' . $date . '.' . $file->extension();
                $directory = 'employee_directory/employee_' . $employee_id . '';
                Storage::disk('public')->putFileAs($directory, $file, $filename);
                $attachments->cnic_nominee = $directory . '/' . $filename;
                $response['link'] = $attachments->cnic_nominee;
            }

            if ($request->hasFile('utility_bill')) {
                if ($attachments->utility_bill != NULL) {
                    Storage::disk('public')->delete($attachments->utility_bill);
                }

                $file = $request->file('utility_bill');
                $filename = 'utility_bill_' . $date . '.' . $file->extension();
                $directory = 'employee_directory/employee_' . $employee_id . '';
                Storage::disk('public')->putFileAs($directory, $file, $filename);
                $attachments->utility_bill = $directory . '/' . $filename;
                $response['link'] = $attachments->utility_bill;
            }

            if ($request->hasFile('affidavit')) {
                if ($attachments->affidavit != NULL) {
                    Storage::disk('public')->delete($attachments->affidavit);
                }

                $file = $request->file('affidavit');
                $filename = 'affidavit_' . $date . '.' . $file->extension();
                $directory = 'employee_directory/employee_' . $employee_id . '';
                Storage::disk('public')->putFileAs($directory, $file, $filename);
                $attachments->affidavit = $directory . '/' . $filename;
                $response['link'] = $attachments->affidavit;
            }

            if ($request->hasFile('cheque')) {
                if ($attachments->cheque != NULL) {
                    Storage::disk('public')->delete($attachments->cheque);
                }

                $file = $request->file('cheque');
                $filename = 'cheque_' . $date . '.' . $file->extension();
                $directory = 'employee_directory/employee_' . $employee_id . '';
                Storage::disk('public')->putFileAs($directory, $file, $filename);
                $attachments->cheque = $directory . '/' . $filename;
                $response['link'] = $attachments->cheque;
            }
            $attachments->save();

            $response['status'] = 0;
            $message = 'Document Has Been Submitted';
        }
        $response['message'] = $message;
        return response()->json($response);
    }

    public function validate_cnic_phone_number(Request $request)
    {
        $rules = [
            //Employees
            'cnic_no' => ['required', 'regex:/^[0-9]{5}-[0-9]{7}-[0-9]{1}$/'],
            'phone_number' => ['required', 'regex:/^[0][0-9]{3}-[0-9]{7}$/'],
        ];
        $validate = Validator::make($request->all(), $rules, $this->messages);
        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            $message = 'Error(s) in Input';
            return response()->json(['status' => 1, 'message' => $message, 'errors' => $validate->errors()]);
        } else {
            $admin = Admin::where('phone_number', $request->input('phone_number'))
                ->orWhere('cnic', $request->input('cnic_no'));

            $employee = Employee::where('phone_number', $request->input('phone_number'))
                ->orWhere('cnic', $request->input('cnic_no'));

            $user_request = AdminUserRequest::where('phone_number', $request->input('phone_number'))
                ->orWhere('cnic', $request->input('cnic_no'));

            //Check Admin Already Exist
            if ($admin->exists()) {
                $admin = $admin->first();
                if ($admin->phone_number == $request->input('phone_number') && $admin->cnic == $request->input('cnic_no')) {
                    $message = "Phone Number & CNIC Already Exists";

                } else if ($admin->phone_number == $request->input('phone_number')) {
                    $message = "Phone Number Already Exist";

                } else if ($admin->cnic == $request->input('cnic_no')) {
                    $message = "CNIC Already Exist";
                }
                return response()->json(['status' => 1, 'message' => $message]);
            } //Check Employee Already Exist
            else if ($employee->exists()) {
                $employee = $employee->first();
                if ($employee->phone_number == $request->input('phone_number') && $employee->cnic == $request->input('cnic_no')) {
                    $message = "Phone Number & CNIC Already Exists";

                } else if ($employee->phone_number == $request->input('phone_number')) {
                    $message = "Phone Number Already Exist";

                } else if ($employee->cnic == $request->input('cnic_no')) {
                    $message = "CNIC Already Exist";
                }
                return response()->json(['status' => 1, 'message' => $message]);
            } //Check User Request Already Exist
            else if ($user_request->exists()) {
                $user_request = $user_request->first();
                if ($user_request->phone_number == $request->input('phone_number') && $user_request->cnic == $request->input('cnic_no')) {
                    $message = "Phone Number & CNIC Already Exists";

                } else if ($user_request->phone_number == $request->input('phone_number')) {
                    $message = "Phone Number Already Exist";

                } else if ($user_request->cnic == $request->input('cnic_no')) {
                    $message = "CNIC Already Exist";
                }
                return response()->json(['status' => 1, 'message' => $message]);
            }
            return response()->json(['status' => 0, 'message' => 'Success']);
        }
    }

    public function admin_attachments_store_v2(Request $request)
    {
        $rules = [
            //Attachments
            'employee_id' => ['required', 'integer', 'digits_between:1,10', 'exists:employees,id'],
            'attachment_update' => ['nullable', 'integer', 'digits_between:1,10'],

            'cv_1' => 'mimes:pdf,png,jpeg,jpg,docx,doc',
            'cv_2' => 'mimes:pdf,png,jpeg,jpg,docx,doc',
            'cv_3' => 'mimes:pdf,png,jpeg,jpg,docx,doc',
            'cv_4' => 'mimes:pdf,png,jpeg,jpg,docx,doc',
            'academic_1' => 'mimes:pdf,png,jpeg,jpg,docx,doc',
            'academic_2' => 'mimes:pdf,png,jpeg,jpg,docx,doc',
            'academic_3' => 'mimes:pdf,png,jpeg,jpg,docx,doc',
            'academic_4' => 'mimes:pdf,png,jpeg,jpg,docx,doc',
            'cnic_1' => 'mimes:pdf,png,jpeg,jpg,docx,doc',
            'cnic_2' => 'mimes:pdf,png,jpeg,jpg,docx,doc',
            'cnic_3' => 'mimes:pdf,png,jpeg,jpg,docx,doc',
            'cnic_4' => 'mimes:pdf,png,jpeg,jpg,docx,doc',
            'photo_1' => 'mimes:pdf,png,jpeg,jpg,docx,doc',
            'photo_2' => 'mimes:pdf,png,jpeg,jpg,docx,doc',
            'photo_3' => 'mimes:pdf,png,jpeg,jpg,docx,doc',
            'photo_4' => 'mimes:pdf,png,jpeg,jpg,docx,doc',
            'experience_certificate_1' => 'mimes:pdf,png,jpeg,jpg,docx,doc',
            'experience_certificate_2' => 'mimes:pdf,png,jpeg,jpg,docx,doc',
            'experience_certificate_3' => 'mimes:pdf,png,jpeg,jpg,docx,doc',
            'experience_certificate_4' => 'mimes:pdf,png,jpeg,jpg,docx,doc',
            'last_pay_slip_1' => 'mimes:pdf,png,jpeg,jpg,docx,doc',
            'last_pay_slip_2' => 'mimes:pdf,png,jpeg,jpg,docx,doc',
            'last_pay_slip_3' => 'mimes:pdf,png,jpeg,jpg,docx,doc',
            'last_pay_slip_4' => 'mimes:pdf,png,jpeg,jpg,docx,doc',
            'nikkah_nama_1' => 'mimes:pdf,png,jpeg,jpg,docx,doc',
            'nikkah_nama_2' => 'mimes:pdf,png,jpeg,jpg,docx,doc',
            'nikkah_nama_3' => 'mimes:pdf,png,jpeg,jpg,docx,doc',
            'nikkah_nama_4' => 'mimes:pdf,png,jpeg,jpg,docx,doc',
            'cnic_spouse_1' => 'mimes:pdf,png,jpeg,jpg,docx,doc',
            'cnic_spouse_2' => 'mimes:pdf,png,jpeg,jpg,docx,doc',
            'cnic_spouse_3' => 'mimes:pdf,png,jpeg,jpg,docx,doc',
            'cnic_spouse_4' => 'mimes:pdf,png,jpeg,jpg,docx,doc',
            'child_b_form_1' => 'mimes:pdf,png,jpeg,jpg,docx,doc',
            'child_b_form_2' => 'mimes:pdf,png,jpeg,jpg,docx,doc',
            'child_b_form_3' => 'mimes:pdf,png,jpeg,jpg,docx,doc',
            'child_b_form_4' => 'mimes:pdf,png,jpeg,jpg,docx,doc',
            'cnic_nominee_1' => 'mimes:pdf,png,jpeg,jpg,docx,doc',
            'cnic_nominee_2' => 'mimes:pdf,png,jpeg,jpg,docx,doc',
            'cnic_nominee_3' => 'mimes:pdf,png,jpeg,jpg,docx,doc',
            'cnic_nominee_4' => 'mimes:pdf,png,jpeg,jpg,docx,doc',
            'utility_bill_1' => 'mimes:pdf,png,jpeg,jpg,docx,doc',
            'utility_bill_2' => 'mimes:pdf,png,jpeg,jpg,docx,doc',
            'utility_bill_3' => 'mimes:pdf,png,jpeg,jpg,docx,doc',
            'utility_bill_4' => 'mimes:pdf,png,jpeg,jpg,docx,doc',
            'affidavit_1' => 'mimes:pdf,png,jpeg,jpg,docx,doc',
            'affidavit_2' => 'mimes:pdf,png,jpeg,jpg,docx,doc',
            'affidavit_3' => 'mimes:pdf,png,jpeg,jpg,docx,doc',
            'affidavit_4' => 'mimes:pdf,png,jpeg,jpg,docx,doc',
            'cheque_1' => 'mimes:pdf,png,jpeg,jpg,docx,doc',
            'cheque_2' => 'mimes:pdf,png,jpeg,jpg,docx,doc',
            'cheque_3' => 'mimes:pdf,png,jpeg,jpg,docx,doc',
            'cheque_4' => 'mimes:pdf,png,jpeg,jpg,docx,doc'
        ];
        $response = ['status' => 1];
        $message = 'Unknown';
        $link = "";

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            $message = 'Error(s) in Input';
            $response['errors'] = $validate->errors();
        } else {
            $employee_id = $request->employee_id;
            $employees = Employee::find($request->employee_id);
            $attachments = EmployeeAttachment::where('employee_id', $employee_id);
            if ($attachments->exists()) {
                $attachments = $attachments->first();
            } else {
                $attachments = new EmployeeAttachment();
                $attachments->employee_id = $employee_id;
            }

            $date = Carbon::now()->format('Y_m_d');

            if ($request->hasFile('cv_1') || $request->hasFile('cv_2') || $request->hasFile('cv_3') || $request->hasFile('cv_4')) {
                $cv_array = [];
                if ($attachments->cv != NULL) {
                    $cvs = explode(',', $attachments->cv);
                    foreach ($cvs as $cv) {
                        $pos = strpos($cv, "cv_1_");
                        if ($pos !== false) {
                            if ($request->hasFile('cv_1')) {
                                Storage::disk('public')->delete($cv);
                            }
                            $cv_array[0] = $cv;
                        }
                        $pos = strpos($cv, "cv_2_");
                        if ($pos !== false) {
                            if ($request->hasFile('cv_2')) {
                                Storage::disk('public')->delete($cv);
                            }
                            $cv_array[1] = $cv;
                        }
                        $pos = strpos($cv, "cv_3_");
                        if ($pos !== false) {
                            if ($request->hasFile('cv_3')) {
                                Storage::disk('public')->delete($cv);
                            }
                            $cv_array[2] = $cv;
                        }
                        $pos = strpos($cv, "cv_4_");
                        if ($pos !== false) {
                            if ($request->hasFile('cv_4')) {
                                Storage::disk('public')->delete($cv);
                            }
                            $cv_array[3] = $cv;
                        }
                    }
                }
                if ($request->hasFile('cv_1')) {
                    $file = $request->file('cv_1');
                    $filename = 'cv_1_' . $date . '.' . $file->extension();
                    $directory = 'employee_directory/employee_' . $employee_id . '';
                    Storage::disk('public')->putFileAs($directory, $file, $filename);
                    $cv_array[0] = $directory . '/' . $filename;
                    $link = $directory . '/' . $filename;
                }
                if ($request->hasFile('cv_2')) {
                    $file = $request->file('cv_2');
                    $filename = 'cv_2_' . $date . '.' . $file->extension();
                    $directory = 'employee_directory/employee_' . $employee_id . '';
                    Storage::disk('public')->putFileAs($directory, $file, $filename);
                    $cv_array[1] = $directory . '/' . $filename;
                    $link = $directory . '/' . $filename;
                }
                if ($request->hasFile('cv_3')) {
                    $file = $request->file('cv_3');
                    $filename = 'cv_3_' . $date . '.' . $file->extension();
                    $directory = 'employee_directory/employee_' . $employee_id . '';
                    Storage::disk('public')->putFileAs($directory, $file, $filename);
                    $cv_array[2] = $directory . '/' . $filename;
                    $link = $directory . '/' . $filename;
                }
                if ($request->hasFile('cv_4')) {
                    $file = $request->file('cv_4');
                    $filename = 'cv_4_' . $date . '.' . $file->extension();
                    $directory = 'employee_directory/employee_' . $employee_id . '';
                    Storage::disk('public')->putFileAs($directory, $file, $filename);
                    $cv_array[3] = $directory . '/' . $filename;
                    $link = $directory . '/' . $filename;
                }

                $attachments->cv = implode(',', $cv_array);
            }

            if ($request->hasFile('cnic_1') || $request->hasFile('cnic_2') || $request->hasFile('cnic_3') || $request->hasFile('cnic_4')) {
                $cnic_array = [];
                if ($attachments->cnic != NULL) {
                    $cnics = explode(',', $attachments->cnic);
                    foreach ($cnics as $cnic) {
                        $pos = strpos($cnic, "cnic_1_");
                        if ($pos !== false) {
                            if ($request->hasFile('cnic_1')) {
                                Storage::disk('public')->delete($cnic);
                            }
                            $cnic_array[0] = $cnic;
                        }
                        $pos = strpos($cnic, "cnic_2_");
                        if ($pos !== false) {
                            if ($request->hasFile('cnic_2')) {
                                Storage::disk('public')->delete($cnic);
                            }
                            $cnic_array[1] = $cnic;
                        }
                        $pos = strpos($cnic, "cnic_3_");
                        if ($pos !== false) {
                            if ($request->hasFile('cnic_3')) {
                                Storage::disk('public')->delete($cnic);
                            }
                            $cnic_array[2] = $cnic;
                        }
                        $pos = strpos($cnic, "cnic_4_");
                        if ($pos !== false) {
                            if ($request->hasFile('cnic_4')) {
                                Storage::disk('public')->delete($cnic);
                            }
                            $cnic_array[3] = $cnic;
                        }
                    }
                }

                if ($request->hasFile('cnic_1')) {
                    $file = $request->file('cnic_1');
                    $filename = 'cnic_1_' . $date . '.' . $file->extension();
                    $directory = 'employee_directory/employee_' . $employee_id . '';
                    Storage::disk('public')->putFileAs($directory, $file, $filename);
                    $cnic_array[0] = $directory . '/' . $filename;
                    $link = $directory . '/' . $filename;
                }
                if ($request->hasFile('cnic_2')) {
                    $file = $request->file('cnic_2');
                    $filename = 'cnic_2_' . $date . '.' . $file->extension();
                    $directory = 'employee_directory/employee_' . $employee_id . '';
                    Storage::disk('public')->putFileAs($directory, $file, $filename);
                    $cnic_array[1] = $directory . '/' . $filename;
                    $link = $directory . '/' . $filename;
                }
                if ($request->hasFile('cnic_3')) {
                    $file = $request->file('cnic_3');
                    $filename = 'cnic_3_' . $date . '.' . $file->extension();
                    $directory = 'employee_directory/employee_' . $employee_id . '';
                    Storage::disk('public')->putFileAs($directory, $file, $filename);
                    $cnic_array[2] = $directory . '/' . $filename;
                    $link = $directory . '/' . $filename;
                }
                if ($request->hasFile('cnic_4')) {
                    $file = $request->file('cnic_4');
                    $filename = 'cnic_4_' . $date . '.' . $file->extension();
                    $directory = 'employee_directory/employee_' . $employee_id . '';
                    Storage::disk('public')->putFileAs($directory, $file, $filename);
                    $cnic_array[3] = $directory . '/' . $filename;
                    $link = $directory . '/' . $filename;
                }

                $attachments->cnic = implode(',', $cnic_array);

            }

            if ($request->hasFile('photo_1') || $request->hasFile('photo_2') || $request->hasFile('photo_3') || $request->hasFile('photo_4')) {
                $photo_array = [];
                if ($attachments->photo != NULL) {
                    $photos = explode(',', $attachments->photo);
                    foreach ($photos as $photo) {
                        $pos = strpos($photo, "photo_1_");
                        if ($pos !== false) {
                            if ($request->hasFile('photo_1')) {
                                Storage::disk('public')->delete($photo);
                            }
                            $photo_array[0] = $photo;
                        }
                        $pos = strpos($photo, "photo_2_");
                        if ($pos !== false) {
                            if ($request->hasFile('photo_2')) {
                                Storage::disk('public')->delete($photo);
                            }
                            $photo_array[1] = $photo;
                        }
                        $pos = strpos($photo, "photo_3_");
                        if ($pos !== false) {
                            if ($request->hasFile('photo_3')) {
                                Storage::disk('public')->delete($photo);
                            }
                            $photo_array[2] = $photo;
                        }
                        $pos = strpos($photo, "photo_4_");
                        if ($pos !== false) {
                            if ($request->hasFile('photo_4')) {
                                Storage::disk('public')->delete($photo);
                            }
                            $photo_array[3] = $photo;
                        }
                    }
                }
                if ($request->hasFile('photo_1')) {
                    $file = $request->file('photo_1');
                    $filename = 'photo_1_' . $date . '.' . $file->extension();
                    $directory = 'employee_directory/employee_' . $employee_id . '';
                    Storage::disk('public')->putFileAs($directory, $file, $filename);
                    $photo_array[0] = $directory . '/' . $filename;
                    $link = $directory . '/' . $filename;
                }
                if ($request->hasFile('photo_2')) {
                    $file = $request->file('photo_2');
                    $filename = 'photo_2_' . $date . '.' . $file->extension();
                    $directory = 'employee_directory/employee_' . $employee_id . '';
                    Storage::disk('public')->putFileAs($directory, $file, $filename);
                    $photo_array[1] = $directory . '/' . $filename;
                    $link = $directory . '/' . $filename;
                }
                if ($request->hasFile('photo_3')) {
                    $file = $request->file('photo_3');
                    $filename = 'photo_3_' . $date . '.' . $file->extension();
                    $directory = 'employee_directory/employee_' . $employee_id . '';
                    Storage::disk('public')->putFileAs($directory, $file, $filename);
                    $photo_array[2] = $directory . '/' . $filename;
                    $link = $directory . '/' . $filename;
                }
                if ($request->hasFile('photo_4')) {
                    $file = $request->file('photo_4');
                    $filename = 'photo_4_' . $date . '.' . $file->extension();
                    $directory = 'employee_directory/employee_' . $employee_id . '';
                    Storage::disk('public')->putFileAs($directory, $file, $filename);
                    $photo_array[3] = $directory . '/' . $filename;
                    $link = $directory . '/' . $filename;
                }

                $attachments->photo = implode(',', $photo_array);
            }

            if ($request->hasFile('academic_1') || $request->hasFile('academic_2') || $request->hasFile('academic_3') || $request->hasFile('academic_4')) {
                $academic_array = [];
                if ($attachments->academic != NULL) {
                    $academics = explode(',', $attachments->academic);
                    foreach ($academics as $academic) {
                        $pos = strpos($academic, "academic_1_");
                        if ($pos !== false) {
                            if ($request->hasFile('academic_1')) {
                                Storage::disk('public')->delete($academic);
                            }
                            $academic_array[0] = $academic;
                        }
                        $pos = strpos($academic, "academic_2_");
                        if ($pos !== false) {
                            if ($request->hasFile('academic_2')) {
                                Storage::disk('public')->delete($academic);
                            }
                            $academic_array[1] = $academic;
                        }
                        $pos = strpos($academic, "academic_3_");
                        if ($pos !== false) {
                            if ($request->hasFile('academic_3')) {
                                Storage::disk('public')->delete($academic);
                            }
                            $academic_array[2] = $academic;
                        }
                        $pos = strpos($academic, "academic_4_");
                        if ($pos !== false) {
                            if ($request->hasFile('academic_4')) {
                                Storage::disk('public')->delete($academic);
                            }
                            $academic_array[3] = $academic;
                        }
                    }
                }
                if ($request->hasFile('academic_1')) {
                    $file = $request->file('academic_1');
                    $filename = 'academic_1_' . $date . '.' . $file->extension();
                    $directory = 'employee_directory/employee_' . $employee_id . '';
                    Storage::disk('public')->putFileAs($directory, $file, $filename);
                    $academic_array[0] = $directory . '/' . $filename;
                    $link = $directory . '/' . $filename;
                }
                if ($request->hasFile('academic_2')) {
                    $file = $request->file('academic_2');
                    $filename = 'academic_2_' . $date . '.' . $file->extension();
                    $directory = 'employee_directory/employee_' . $employee_id . '';
                    Storage::disk('public')->putFileAs($directory, $file, $filename);
                    $academic_array[1] = $directory . '/' . $filename;
                    $link = $directory . '/' . $filename;
                }
                if ($request->hasFile('academic_3')) {
                    $file = $request->file('academic_3');
                    $filename = 'academic_3_' . $date . '.' . $file->extension();
                    $directory = 'employee_directory/employee_' . $employee_id . '';
                    Storage::disk('public')->putFileAs($directory, $file, $filename);
                    $academic_array[2] = $directory . '/' . $filename;
                    $link = $directory . '/' . $filename;
                }
                if ($request->hasFile('academic_4')) {
                    $file = $request->file('academic_4');
                    $filename = 'academic_4_' . $date . '.' . $file->extension();
                    $directory = 'employee_directory/employee_' . $employee_id . '';
                    Storage::disk('public')->putFileAs($directory, $file, $filename);
                    $academic_array[3] = $directory . '/' . $filename;
                    $link = $directory . '/' . $filename;
                }

                $attachments->academic = implode(',', $academic_array);
            }

            if ($request->hasFile('experience_certificate_1') || $request->hasFile('experience_certificate_2') || $request->hasFile('experience_certificate_3') || $request->hasFile('experience_certificate_4')) {
                $experience_certificate_array = [];
                if ($attachments->experience != NULL) {
                    $experience_certificates = explode(',', $attachments->experience);
                    foreach ($experience_certificates as $experience_certificate) {
                        $pos = strpos($experience_certificate, "experience_certificate_1_");
                        if ($pos !== false) {
                            if ($request->hasFile('experience_certificate_1')) {
                                Storage::disk('public')->delete($experience_certificate);
                            }
                            $experience_certificate_array[0] = $experience_certificate;
                        }
                        $pos = strpos($experience_certificate, "experience_certificate_2_");
                        if ($pos !== false) {
                            if ($request->hasFile('experience_certificate_2')) {
                                Storage::disk('public')->delete($experience_certificate);
                            }
                            $experience_certificate_array[1] = $experience_certificate;
                        }
                        $pos = strpos($experience_certificate, "experience_certificate_3_");
                        if ($pos !== false) {
                            if ($request->hasFile('experience_certificate_3')) {
                                Storage::disk('public')->delete($experience_certificate);
                            }
                            $experience_certificate_array[2] = $experience_certificate;
                        }
                        $pos = strpos($experience_certificate, "experience_certificate_4_");
                        if ($pos !== false) {
                            if ($request->hasFile('experience_certificate_4')) {
                                Storage::disk('public')->delete($experience_certificate);
                            }
                            $experience_certificate_array[3] = $experience_certificate;
                        }
                    }
                }
                if ($request->hasFile('experience_certificate_1')) {
                    $file = $request->file('experience_certificate_1');
                    $filename = 'experience_certificate_1_' . $date . '.' . $file->extension();
                    $directory = 'employee_directory/employee_' . $employee_id . '';
                    Storage::disk('public')->putFileAs($directory, $file, $filename);
                    $experience_certificate_array[0] = $directory . '/' . $filename;
                    $link = $directory . '/' . $filename;
                }
                if ($request->hasFile('experience_certificate_2')) {
                    $file = $request->file('experience_certificate_2');
                    $filename = 'experience_certificate_2_' . $date . '.' . $file->extension();
                    $directory = 'employee_directory/employee_' . $employee_id . '';
                    Storage::disk('public')->putFileAs($directory, $file, $filename);
                    $experience_certificate_array[1] = $directory . '/' . $filename;
                    $link = $directory . '/' . $filename;
                }
                if ($request->hasFile('experience_certificate_3')) {
                    $file = $request->file('experience_certificate_3');
                    $filename = 'experience_certificate_3_' . $date . '.' . $file->extension();
                    $directory = 'employee_directory/employee_' . $employee_id . '';
                    Storage::disk('public')->putFileAs($directory, $file, $filename);
                    $experience_certificate_array[2] = $directory . '/' . $filename;
                    $link = $directory . '/' . $filename;
                }
                if ($request->hasFile('experience_certificate_4')) {
                    $file = $request->file('experience_certificate_4');
                    $filename = 'experience_certificate_4_' . $date . '.' . $file->extension();
                    $directory = 'employee_directory/employee_' . $employee_id . '';
                    Storage::disk('public')->putFileAs($directory, $file, $filename);
                    $experience_certificate_array[3] = $directory . '/' . $filename;
                    $link = $directory . '/' . $filename;
                }

                $attachments->experience = implode(',', $experience_certificate_array);
            }

            if ($request->hasFile('last_pay_slip_1') || $request->hasFile('last_pay_slip_2') || $request->hasFile('last_pay_slip_3') || $request->hasFile('last_pay_slip_4')) {
                $last_pay_slip_array = [];
                if ($attachments->last_pay_slip != NULL) {
                    $last_pay_slips = explode(',', $attachments->last_pay_slip);
                    foreach ($last_pay_slips as $last_pay_slip) {
                        $pos = strpos($last_pay_slip, "last_pay_slip_1_");
                        if ($pos !== false) {
                            if ($request->hasFile('last_pay_slip_1')) {
                                Storage::disk('public')->delete($last_pay_slip);
                            }
                            $last_pay_slip_array[0] = $last_pay_slip;
                        }
                        $pos = strpos($last_pay_slip, "last_pay_slip_2_");
                        if ($pos !== false) {
                            if ($request->hasFile('last_pay_slip_2')) {
                                Storage::disk('public')->delete($last_pay_slip);
                            }
                            $last_pay_slip_array[1] = $last_pay_slip;
                        }
                        $pos = strpos($last_pay_slip, "last_pay_slip_3_");
                        if ($pos !== false) {
                            if ($request->hasFile('last_pay_slip_3')) {
                                Storage::disk('public')->delete($last_pay_slip);
                            }
                            $last_pay_slip_array[2] = $last_pay_slip;
                        }
                        $pos = strpos($last_pay_slip, "last_pay_slip_4_");
                        if ($pos !== false) {
                            if ($request->hasFile('last_pay_slip_4')) {
                                Storage::disk('public')->delete($last_pay_slip);
                            }
                            $last_pay_slip_array[3] = $last_pay_slip;
                        }
                    }
                }
                if ($request->hasFile('last_pay_slip_1')) {
                    $file = $request->file('last_pay_slip_1');
                    $filename = 'last_pay_slip_1_' . $date . '.' . $file->extension();
                    $directory = 'employee_directory/employee_' . $employee_id . '';
                    Storage::disk('public')->putFileAs($directory, $file, $filename);
                    $last_pay_slip_array[0] = $directory . '/' . $filename;
                    $link = $directory . '/' . $filename;
                }
                if ($request->hasFile('last_pay_slip_2')) {
                    $file = $request->file('last_pay_slip_2');
                    $filename = 'last_pay_slip_2_' . $date . '.' . $file->extension();
                    $directory = 'employee_directory/employee_' . $employee_id . '';
                    Storage::disk('public')->putFileAs($directory, $file, $filename);
                    $last_pay_slip_array[1] = $directory . '/' . $filename;
                    $link = $directory . '/' . $filename;
                }
                if ($request->hasFile('last_pay_slip_3')) {
                    $file = $request->file('last_pay_slip_3');
                    $filename = 'last_pay_slip_3_' . $date . '.' . $file->extension();
                    $directory = 'employee_directory/employee_' . $employee_id . '';
                    Storage::disk('public')->putFileAs($directory, $file, $filename);
                    $last_pay_slip_array[2] = $directory . '/' . $filename;
                    $link = $directory . '/' . $filename;
                }
                if ($request->hasFile('last_pay_slip_4')) {
                    $file = $request->file('last_pay_slip_4');
                    $filename = 'last_pay_slip_4_' . $date . '.' . $file->extension();
                    $directory = 'employee_directory/employee_' . $employee_id . '';
                    Storage::disk('public')->putFileAs($directory, $file, $filename);
                    $last_pay_slip_array[3] = $directory . '/' . $filename;
                    $link = $directory . '/' . $filename;
                }

                $attachments->last_pay_slip = implode(',', $last_pay_slip_array);
            }

            if ($request->hasFile('nikkah_nama_1') || $request->hasFile('nikkah_nama_2') || $request->hasFile('nikkah_nama_3') || $request->hasFile('nikkah_nama_4')) {
                $nikkah_nama_array = [];
                if ($attachments->nikkah_nama != NULL) {
                    $nikkah_namas = explode(',', $attachments->nikkah_nama);
                    foreach ($nikkah_namas as $nikkah_nama) {
                        $pos = strpos($nikkah_nama, "nikkah_nama_1_");
                        if ($pos !== false) {
                            if ($request->hasFile('nikkah_nama_1')) {
                                Storage::disk('public')->delete($nikkah_nama);
                            }
                            $nikkah_nama_array[0] = $nikkah_nama;
                        }
                        $pos = strpos($nikkah_nama, "nikkah_nama_2_");
                        if ($pos !== false) {
                            if ($request->hasFile('nikkah_nama_2')) {
                                Storage::disk('public')->delete($nikkah_nama);
                            }
                            $nikkah_nama_array[1] = $nikkah_nama;
                        }
                        $pos = strpos($nikkah_nama, "nikkah_nama_3_");
                        if ($pos !== false) {
                            if ($request->hasFile('nikkah_nama_3')) {
                                Storage::disk('public')->delete($nikkah_nama);
                            }
                            $nikkah_nama_array[2] = $nikkah_nama;
                        }
                        $pos = strpos($nikkah_nama, "nikkah_nama_4_");
                        if ($pos !== false) {
                            if ($request->hasFile('nikkah_nama_4')) {
                                Storage::disk('public')->delete($nikkah_nama);
                            }
                            $nikkah_nama_array[3] = $nikkah_nama;
                        }
                    }
                }
                if ($request->hasFile('nikkah_nama_1')) {
                    $file = $request->file('nikkah_nama_1');
                    $filename = 'nikkah_nama_1_' . $date . '.' . $file->extension();
                    $directory = 'employee_directory/employee_' . $employee_id . '';
                    Storage::disk('public')->putFileAs($directory, $file, $filename);
                    $nikkah_nama_array[0] = $directory . '/' . $filename;
                    $link = $directory . '/' . $filename;
                }
                if ($request->hasFile('nikkah_nama_2')) {
                    $file = $request->file('nikkah_nama_2');
                    $filename = 'nikkah_nama_2_' . $date . '.' . $file->extension();
                    $directory = 'employee_directory/employee_' . $employee_id . '';
                    Storage::disk('public')->putFileAs($directory, $file, $filename);
                    $nikkah_nama_array[1] = $directory . '/' . $filename;
                    $link = $directory . '/' . $filename;
                }
                if ($request->hasFile('nikkah_nama_3')) {
                    $file = $request->file('nikkah_nama_3');
                    $filename = 'nikkah_nama_3_' . $date . '.' . $file->extension();
                    $directory = 'employee_directory/employee_' . $employee_id . '';
                    Storage::disk('public')->putFileAs($directory, $file, $filename);
                    $nikkah_nama_array[2] = $directory . '/' . $filename;
                    $link = $directory . '/' . $filename;
                }
                if ($request->hasFile('nikkah_nama_4')) {
                    $file = $request->file('nikkah_nama_4');
                    $filename = 'nikkah_nama_4_' . $date . '.' . $file->extension();
                    $directory = 'employee_directory/employee_' . $employee_id . '';
                    Storage::disk('public')->putFileAs($directory, $file, $filename);
                    $nikkah_nama_array[3] = $directory . '/' . $filename;
                    $link = $directory . '/' . $filename;
                }

                $attachments->nikkah_nama = implode(',', $nikkah_nama_array);
            }

            if ($request->hasFile('cnic_spouse_1') || $request->hasFile('cnic_spouse_2') || $request->hasFile('cnic_spouse_3') || $request->hasFile('cnic_spouse_4')) {
                $cnic_spouse_array = [];
                if ($attachments->cnic_spouse != NULL) {
                    $cnic_spouses = explode(',', $attachments->cnic_spouse);
                    foreach ($cnic_spouses as $cnic_spouse) {
                        $pos = strpos($cnic_spouse, "cnic_spouse_1_");
                        if ($pos !== false) {
                            if ($request->hasFile('cnic_spouse_1')) {
                                Storage::disk('public')->delete($cnic_spouse);
                            }
                            $cnic_spouse_array[0] = $cnic_spouse;
                        }
                        $pos = strpos($cnic_spouse, "cnic_spouse_2_");
                        if ($pos !== false) {
                            if ($request->hasFile('cnic_spouse_2')) {
                                Storage::disk('public')->delete($cnic_spouse);
                            }
                            $cnic_spouse_array[1] = $cnic_spouse;
                        }
                        $pos = strpos($cnic_spouse, "cnic_spouse_3_");
                        if ($pos !== false) {
                            if ($request->hasFile('cnic_spouse_3')) {
                                Storage::disk('public')->delete($cnic_spouse);
                            }
                            $cnic_spouse_array[2] = $cnic_spouse;
                        }
                        $pos = strpos($cnic_spouse, "cnic_spouse_4_");
                        if ($pos !== false) {
                            if ($request->hasFile('cnic_spouse_4')) {
                                Storage::disk('public')->delete($cnic_spouse);
                            }
                            $cnic_spouse_array[3] = $cnic_spouse;
                        }
                    }
                }
                if ($request->hasFile('cnic_spouse_1')) {
                    $file = $request->file('cnic_spouse_1');
                    $filename = 'cnic_spouse_1_' . $date . '.' . $file->extension();
                    $directory = 'employee_directory/employee_' . $employee_id . '';
                    Storage::disk('public')->putFileAs($directory, $file, $filename);
                    $cnic_spouse_array[0] = $directory . '/' . $filename;
                    $link = $directory . '/' . $filename;
                }
                if ($request->hasFile('cnic_spouse_2')) {
                    $file = $request->file('cnic_spouse_2');
                    $filename = 'cnic_spouse_2_' . $date . '.' . $file->extension();
                    $directory = 'employee_directory/employee_' . $employee_id . '';
                    Storage::disk('public')->putFileAs($directory, $file, $filename);
                    $cnic_spouse_array[1] = $directory . '/' . $filename;
                    $link = $directory . '/' . $filename;
                }
                if ($request->hasFile('cnic_spouse_3')) {
                    $file = $request->file('cnic_spouse_3');
                    $filename = 'cnic_spouse_3_' . $date . '.' . $file->extension();
                    $directory = 'employee_directory/employee_' . $employee_id . '';
                    Storage::disk('public')->putFileAs($directory, $file, $filename);
                    $cnic_spouse_array[2] = $directory . '/' . $filename;
                    $link = $directory . '/' . $filename;
                }
                if ($request->hasFile('cnic_spouse_4')) {
                    $file = $request->file('cnic_spouse_4');
                    $filename = 'cnic_spouse_4_' . $date . '.' . $file->extension();
                    $directory = 'employee_directory/employee_' . $employee_id . '';
                    Storage::disk('public')->putFileAs($directory, $file, $filename);
                    $cnic_spouse_array[3] = $directory . '/' . $filename;
                    $link = $directory . '/' . $filename;
                }

                $attachments->cnic_spouse = implode(',', $cnic_spouse_array);
            }

            if ($request->hasFile('child_b_form_1') || $request->hasFile('child_b_form_2') || $request->hasFile('child_b_form_3') || $request->hasFile('child_b_form_4')) {
                $child_b_form_array = [];
                if ($attachments->child_b_form != NULL) {
                    $child_b_forms = explode(',', $attachments->child_b_form);
                    foreach ($child_b_forms as $child_b_form) {
                        $pos = strpos($child_b_form, "child_b_form_1_");
                        if ($pos !== false) {
                            if ($request->hasFile('child_b_form_1')) {
                                Storage::disk('public')->delete($child_b_form);
                            }
                            $child_b_form_array[0] = $child_b_form;
                        }
                        $pos = strpos($child_b_form, "child_b_form_2_");
                        if ($pos !== false) {
                            if ($request->hasFile('child_b_form_2')) {
                                Storage::disk('public')->delete($child_b_form);
                            }
                            $child_b_form_array[1] = $child_b_form;
                        }
                        $pos = strpos($child_b_form, "child_b_form_3_");
                        if ($pos !== false) {
                            if ($request->hasFile('child_b_form_3')) {
                                Storage::disk('public')->delete($child_b_form);
                            }
                            $child_b_form_array[2] = $child_b_form;
                        }
                        $pos = strpos($child_b_form, "child_b_form_4_");
                        if ($pos !== false) {
                            if ($request->hasFile('child_b_form_4')) {
                                Storage::disk('public')->delete($child_b_form);
                            }
                            $child_b_form_array[3] = $child_b_form;
                        }
                    }
                }
                if ($request->hasFile('child_b_form_1')) {
                    $file = $request->file('child_b_form_1');
                    $filename = 'child_b_form_1_' . $date . '.' . $file->extension();
                    $directory = 'employee_directory/employee_' . $employee_id . '';
                    Storage::disk('public')->putFileAs($directory, $file, $filename);
                    $child_b_form_array[0] = $directory . '/' . $filename;
                    $link = $directory . '/' . $filename;
                }
                if ($request->hasFile('child_b_form_2')) {
                    $file = $request->file('child_b_form_2');
                    $filename = 'child_b_form_2_' . $date . '.' . $file->extension();
                    $directory = 'employee_directory/employee_' . $employee_id . '';
                    Storage::disk('public')->putFileAs($directory, $file, $filename);
                    $child_b_form_array[1] = $directory . '/' . $filename;
                    $link = $directory . '/' . $filename;
                }
                if ($request->hasFile('child_b_form_3')) {
                    $file = $request->file('child_b_form_3');
                    $filename = 'child_b_form_3_' . $date . '.' . $file->extension();
                    $directory = 'employee_directory/employee_' . $employee_id . '';
                    Storage::disk('public')->putFileAs($directory, $file, $filename);
                    $child_b_form_array[2] = $directory . '/' . $filename;
                    $link = $directory . '/' . $filename;
                }
                if ($request->hasFile('child_b_form_4')) {
                    $file = $request->file('child_b_form_4');
                    $filename = 'child_b_form_4_' . $date . '.' . $file->extension();
                    $directory = 'employee_directory/employee_' . $employee_id . '';
                    Storage::disk('public')->putFileAs($directory, $file, $filename);
                    $child_b_form_array[3] = $directory . '/' . $filename;
                    $link = $directory . '/' . $filename;
                }

                $attachments->child_b_form = implode(',', $child_b_form_array);
            }

            if ($request->hasFile('cnic_nominee_1') || $request->hasFile('cnic_nominee_2') || $request->hasFile('cnic_nominee_3') || $request->hasFile('cnic_nominee_4')) {
                $cnic_nominee_array = [];
                if ($attachments->cnic_nominee != NULL) {
                    $cnic_nominees = explode(',', $attachments->cnic_nominee);
                    foreach ($cnic_nominees as $cnic_nominee) {
                        $pos = strpos($cnic_nominee, "cnic_nominee_1_");
                        if ($pos !== false) {
                            if ($request->hasFile('cnic_nominee_1')) {
                                Storage::disk('public')->delete($cnic_nominee);
                            }
                            $cnic_nominee_array[0] = $cnic_nominee;
                        }
                        $pos = strpos($cnic_nominee, "cnic_nominee_2_");
                        if ($pos !== false) {
                            if ($request->hasFile('cnic_nominee_2')) {
                                Storage::disk('public')->delete($cnic_nominee);
                            }
                            $cnic_nominee_array[1] = $cnic_nominee;
                        }
                        $pos = strpos($cnic_nominee, "cnic_nominee_3_");
                        if ($pos !== false) {
                            if ($request->hasFile('cnic_nominee_3')) {
                                Storage::disk('public')->delete($cnic_nominee);
                            }
                            $cnic_nominee_array[2] = $cnic_nominee;
                        }
                        $pos = strpos($cnic_nominee, "cnic_nominee_4_");
                        if ($pos !== false) {
                            if ($request->hasFile('cnic_nominee_4')) {
                                Storage::disk('public')->delete($cnic_nominee);
                            }
                            $cnic_nominee_array[3] = $cnic_nominee;
                        }
                    }
                }
                if ($request->hasFile('cnic_nominee_1')) {
                    $file = $request->file('cnic_nominee_1');
                    $filename = 'cnic_nominee_1_' . $date . '.' . $file->extension();
                    $directory = 'employee_directory/employee_' . $employee_id . '';
                    Storage::disk('public')->putFileAs($directory, $file, $filename);
                    $cnic_nominee_array[0] = $directory . '/' . $filename;
                    $link = $directory . '/' . $filename;
                }
                if ($request->hasFile('cnic_nominee_2')) {
                    $file = $request->file('cnic_nominee_2');
                    $filename = 'cnic_nominee_2_' . $date . '.' . $file->extension();
                    $directory = 'employee_directory/employee_' . $employee_id . '';
                    Storage::disk('public')->putFileAs($directory, $file, $filename);
                    $cnic_nominee_array[1] = $directory . '/' . $filename;
                    $link = $directory . '/' . $filename;
                }
                if ($request->hasFile('cnic_nominee_3')) {
                    $file = $request->file('cnic_nominee_3');
                    $filename = 'cnic_nominee_3_' . $date . '.' . $file->extension();
                    $directory = 'employee_directory/employee_' . $employee_id . '';
                    Storage::disk('public')->putFileAs($directory, $file, $filename);
                    $cnic_nominee_array[2] = $directory . '/' . $filename;
                    $link = $directory . '/' . $filename;
                }
                if ($request->hasFile('cnic_nominee_4')) {
                    $file = $request->file('cnic_nominee_4');
                    $filename = 'cnic_nominee_4_' . $date . '.' . $file->extension();
                    $directory = 'employee_directory/employee_' . $employee_id . '';
                    Storage::disk('public')->putFileAs($directory, $file, $filename);
                    $cnic_nominee_array[3] = $directory . '/' . $filename;
                    $link = $directory . '/' . $filename;
                }

                $attachments->cnic_nominee = implode(',', $cnic_nominee_array);
            }

            if ($request->hasFile('utility_bill_1') || $request->hasFile('utility_bill_2') || $request->hasFile('utility_bill_3') || $request->hasFile('utility_bill_4')) {
                $utility_bill_array = [];
                if ($attachments->utility_bill != NULL) {
                    $utility_bills = explode(',', $attachments->utility_bill);
                    foreach ($utility_bills as $utility_bill) {
                        $pos = strpos($utility_bill, "utility_bill_1_");
                        if ($pos !== false) {
                            if ($request->hasFile('utility_bill_1')) {
                                Storage::disk('public')->delete($utility_bill);
                            }
                            $utility_bill_array[0] = $utility_bill;
                        }
                        $pos = strpos($utility_bill, "utility_bill_2_");
                        if ($pos !== false) {
                            if ($request->hasFile('utility_bill_2')) {
                                Storage::disk('public')->delete($utility_bill);
                            }
                            $utility_bill_array[1] = $utility_bill;
                        }
                        $pos = strpos($utility_bill, "utility_bill_3_");
                        if ($pos !== false) {
                            if ($request->hasFile('utility_bill_3')) {
                                Storage::disk('public')->delete($utility_bill);
                            }
                            $utility_bill_array[2] = $utility_bill;
                        }
                        $pos = strpos($utility_bill, "utility_bill_4_");
                        if ($pos !== false) {
                            if ($request->hasFile('utility_bill_4')) {
                                Storage::disk('public')->delete($utility_bill);
                            }
                            $utility_bill_array[3] = $utility_bill;
                        }
                    }
                }
                if ($request->hasFile('utility_bill_1')) {
                    $file = $request->file('utility_bill_1');
                    $filename = 'utility_bill_1_' . $date . '.' . $file->extension();
                    $directory = 'employee_directory/employee_' . $employee_id . '';
                    Storage::disk('public')->putFileAs($directory, $file, $filename);
                    $utility_bill_array[0] = $directory . '/' . $filename;
                    $link = $directory . '/' . $filename;
                }
                if ($request->hasFile('utility_bill_2')) {
                    $file = $request->file('utility_bill_2');
                    $filename = 'utility_bill_2_' . $date . '.' . $file->extension();
                    $directory = 'employee_directory/employee_' . $employee_id . '';
                    Storage::disk('public')->putFileAs($directory, $file, $filename);
                    $utility_bill_array[1] = $directory . '/' . $filename;
                    $link = $directory . '/' . $filename;
                }
                if ($request->hasFile('utility_bill_3')) {
                    $file = $request->file('utility_bill_3');
                    $filename = 'utility_bill_3_' . $date . '.' . $file->extension();
                    $directory = 'employee_directory/employee_' . $employee_id . '';
                    Storage::disk('public')->putFileAs($directory, $file, $filename);
                    $utility_bill_array[2] = $directory . '/' . $filename;
                    $link = $directory . '/' . $filename;
                }
                if ($request->hasFile('utility_bill_4')) {
                    $file = $request->file('utility_bill_4');
                    $filename = 'utility_bill_4_' . $date . '.' . $file->extension();
                    $directory = 'employee_directory/employee_' . $employee_id . '';
                    Storage::disk('public')->putFileAs($directory, $file, $filename);
                    $utility_bill_array[3] = $directory . '/' . $filename;
                    $link = $directory . '/' . $filename;
                }

                $attachments->utility_bill = implode(',', $utility_bill_array);
            }

            if ($request->hasFile('affidavit_1') || $request->hasFile('affidavit_2') || $request->hasFile('affidavit_3') || $request->hasFile('affidavit_4')) {
                $affidavit_array = [];
                if ($attachments->affidavit != NULL) {
                    $affidavits = explode(',', $attachments->affidavit);
                    foreach ($affidavits as $affidavit) {
                        $pos = strpos($affidavit, "affidavit_1_");
                        if ($pos !== false) {
                            if ($request->hasFile('affidavit_1')) {
                                Storage::disk('public')->delete($affidavit);
                            }
                            $affidavit_array[0] = $affidavit;
                        }
                        $pos = strpos($affidavit, "affidavit_2_");
                        if ($pos !== false) {
                            if ($request->hasFile('affidavit_2')) {
                                Storage::disk('public')->delete($affidavit);
                            }
                            $affidavit_array[1] = $affidavit;
                        }
                        $pos = strpos($affidavit, "affidavit_3_");
                        if ($pos !== false) {
                            if ($request->hasFile('affidavit_3')) {
                                Storage::disk('public')->delete($affidavit);
                            }
                            $affidavit_array[2] = $affidavit;
                        }
                        $pos = strpos($affidavit, "affidavit_4_");
                        if ($pos !== false) {
                            if ($request->hasFile('affidavit_4')) {
                                Storage::disk('public')->delete($affidavit);
                            }
                            $affidavit_array[3] = $affidavit;
                        }
                    }
                }
                if ($request->hasFile('affidavit_1')) {
                    $file = $request->file('affidavit_1');
                    $filename = 'affidavit_1_' . $date . '.' . $file->extension();
                    $directory = 'employee_directory/employee_' . $employee_id . '';
                    Storage::disk('public')->putFileAs($directory, $file, $filename);
                    $affidavit_array[0] = $directory . '/' . $filename;
                    $link = $directory . '/' . $filename;
                }
                if ($request->hasFile('affidavit_2')) {
                    $file = $request->file('affidavit_2');
                    $filename = 'affidavit_2_' . $date . '.' . $file->extension();
                    $directory = 'employee_directory/employee_' . $employee_id . '';
                    Storage::disk('public')->putFileAs($directory, $file, $filename);
                    $affidavit_array[1] = $directory . '/' . $filename;
                    $link = $directory . '/' . $filename;
                }
                if ($request->hasFile('affidavit_3')) {
                    $file = $request->file('affidavit_3');
                    $filename = 'affidavit_3_' . $date . '.' . $file->extension();
                    $directory = 'employee_directory/employee_' . $employee_id . '';
                    Storage::disk('public')->putFileAs($directory, $file, $filename);
                    $affidavit_array[2] = $directory . '/' . $filename;
                    $link = $directory . '/' . $filename;
                }
                if ($request->hasFile('affidavit_4')) {
                    $file = $request->file('affidavit_4');
                    $filename = 'affidavit_4_' . $date . '.' . $file->extension();
                    $directory = 'employee_directory/employee_' . $employee_id . '';
                    Storage::disk('public')->putFileAs($directory, $file, $filename);
                    $affidavit_array[3] = $directory . '/' . $filename;
                    $link = $directory . '/' . $filename;
                }

                $attachments->affidavit = implode(',', $affidavit_array);
            }

            if ($request->hasFile('cheque_1') || $request->hasFile('cheque_2') || $request->hasFile('cheque_3') || $request->hasFile('cheque_4')) {
                $cheque_array = [];
                if ($attachments->cheque != NULL) {
                    $cheques = explode(',', $attachments->cheque);
                    foreach ($cheques as $cheque) {
                        $pos = strpos($cheque, "cheque_1_");
                        if ($pos !== false) {
                            if ($request->hasFile('cheque_1')) {
                                Storage::disk('public')->delete($cheque);
                            }
                            $cheque_array[0] = $cheque;
                        }
                        $pos = strpos($cheque, "cheque_2_");
                        if ($pos !== false) {
                            if ($request->hasFile('cheque_2')) {
                                Storage::disk('public')->delete($cheque);
                            }
                            $cheque_array[1] = $cheque;
                        }
                        $pos = strpos($cheque, "cheque_3_");
                        if ($pos !== false) {
                            if ($request->hasFile('cheque_3')) {
                                Storage::disk('public')->delete($cheque);
                            }
                            $cheque_array[2] = $cheque;
                        }
                        $pos = strpos($cheque, "cheque_4_");
                        if ($pos !== false) {
                            if ($request->hasFile('cheque_4')) {
                                Storage::disk('public')->delete($cheque);
                            }
                            $cheque_array[3] = $cheque;
                        }
                    }
                }
                if ($request->hasFile('cheque_1')) {
                    $file = $request->file('cheque_1');
                    $filename = 'cheque_1_' . $date . '.' . $file->extension();
                    $directory = 'employee_directory/employee_' . $employee_id . '';
                    Storage::disk('public')->putFileAs($directory, $file, $filename);
                    $cheque_array[0] = $directory . '/' . $filename;
                    $link = $directory . '/' . $filename;
                }
                if ($request->hasFile('cheque_2')) {
                    $file = $request->file('cheque_2');
                    $filename = 'cheque_2_' . $date . '.' . $file->extension();
                    $directory = 'employee_directory/employee_' . $employee_id . '';
                    Storage::disk('public')->putFileAs($directory, $file, $filename);
                    $cheque_array[1] = $directory . '/' . $filename;
                    $link = $directory . '/' . $filename;
                }
                if ($request->hasFile('cheque_3')) {
                    $file = $request->file('cheque_3');
                    $filename = 'cheque_3_' . $date . '.' . $file->extension();
                    $directory = 'employee_directory/employee_' . $employee_id . '';
                    Storage::disk('public')->putFileAs($directory, $file, $filename);
                    $cheque_array[2] = $directory . '/' . $filename;
                    $link = $directory . '/' . $filename;
                }
                if ($request->hasFile('cheque_4')) {
                    $file = $request->file('cheque_4');
                    $filename = 'cheque_4_' . $date . '.' . $file->extension();
                    $directory = 'employee_directory/employee_' . $employee_id . '';
                    Storage::disk('public')->putFileAs($directory, $file, $filename);
                    $cheque_array[3] = $directory . '/' . $filename;
                    $link = $directory . '/' . $filename;
                }

                $attachments->cheque = implode(',', $cheque_array);
            }

            if($request->has('attachment_update')){
                $employees->attachment_update = $request->attachment_update;
                $employees->save();
            }
            $attachments->save();
            $response['status'] = 0;
            $response['link'] = $link;
            $message = 'Attachment Has Been Uploaded';
            $response['message'] = $message;
        }
        return response()->json($response);
    }

    public function admin_attachments_view(Request $request)
    {
        $rules = [
            'employee_id' => ['required', 'integer', 'digits_between:1,10', 'exists:employees,id'],
            'attachment_type' => ['required'],
        ];
        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            $message = 'Error(s) in Input';
            return response()->json(['status' => 1, 'message' => $message, 'errors' => $validate->errors()]);
        } else {
            $employee_id = $request->employee_id;
            $attachments = EmployeeAttachment::where('employee_id', $employee_id);
            if ($attachments->exists()) {
                $attachments = $attachments->first();

                $attachment_type = $request->attachment_type;
                if ($attachment_type == 'cv') {
                    $cv_array = [];
                    for ($x = 0; $x <= 3; $x++) {
                        $cv_array[$x]['cv_' . ($x + 1)] = "";
                    }
                    if ($attachments->cv != NULL) {
                        $cvs = explode(',', $attachments->cv);
                        foreach ($cvs as $cv) {
                            $pos = strpos($cv, "cv_1_");
                            if ($pos !== false) {
                                $cv_array[0]['cv_1'] = $cv;
                            }
                            $pos = strpos($cv, "cv_2_");
                            if ($pos !== false) {
                                $cv_array[1]['cv_2'] = $cv;
                            }
                            $pos = strpos($cv, "cv_3_");
                            if ($pos !== false) {
                                $cv_array[2]['cv_3'] = $cv;
                            }
                            $pos = strpos($cv, "cv_4_");
                            if ($pos !== false) {
                                $cv_array[3]['cv_4'] = $cv;
                            }
                        }
                    }
                    return response()->json(['status' => 0, 'data' => $cv_array]);
                } elseif ($attachment_type == 'cnic') {
                    $cnic_array = [];
                    for ($x = 0; $x <= 3; $x++) {
                        $cnic_array[$x]['cnic_' . ($x + 1)] = "";
                    }
                    if ($attachments->cnic != NULL) {
                        $cnics = explode(',', $attachments->cnic);
                        foreach ($cnics as $cnic) {
                            $pos = strpos($cnic, "cnic_1_");
                            if ($pos !== false) {
                                $cnic_array[0]["cnic_1"] = $cnic;
                            }
                            $pos = strpos($cnic, "cnic_2_");
                            if ($pos !== false) {
                                $cnic_array[1]["cnic_2"] = $cnic;
                            }
                            $pos = strpos($cnic, "cnic_3_");
                            if ($pos !== false) {
                                $cnic_array[2]["cnic_3"] = $cnic;
                            }
                            $pos = strpos($cnic, "cnic_4_");
                            if ($pos !== false) {
                                $cnic_array[3]["cnic_4"] = $cnic;
                            }
                        }
                    }
                    return response()->json(['status' => 0, 'data' => $cnic_array]);
                } elseif ($attachment_type == 'photo') {
                    $photo_array = [];
                    for ($x = 0; $x <= 3; $x++) {
                        $photo_array[$x]['photo_' . ($x + 1)] = "";
                    }
                    if ($attachments->photo != NULL) {
                        $photos = explode(',', $attachments->photo);
                        foreach ($photos as $photo) {
                            $pos = strpos($photo, "photo_1_");
                            if ($pos !== false) {
                                $photo_array[0]["photo_1"] = $photo;
                            }
                            $pos = strpos($photo, "photo_2_");
                            if ($pos !== false) {
                                $photo_array[1]["photo_2"] = $photo;
                            }
                            $pos = strpos($photo, "photo_3_");
                            if ($pos !== false) {
                                $photo_array[2]["photo_3"] = $photo;
                            }
                            $pos = strpos($photo, "photo_4_");
                            if ($pos !== false) {
                                $photo_array[3]["photo_4"] = $photo;
                            }
                        }
                    }
                    return response()->json(['status' => 0, 'data' => $photo_array]);
                } elseif ($attachment_type == 'academic') {
                    $academic_array = [];
                    for ($x = 0; $x <= 3; $x++) {
                        $academic_array[$x]['academic_' . ($x + 1)] = "";
                    }
                    if ($attachments->academic != NULL) {
                        $academics = explode(',', $attachments->academic);
                        foreach ($academics as $academic) {
                            $pos = strpos($academic, "academic_1_");
                            if ($pos !== false) {
                                $academic_array[0]["academic_1"] = $academic;
                            }
                            $pos = strpos($academic, "academic_2_");
                            if ($pos !== false) {
                                $academic_array[1]["academic_2"] = $academic;
                            }
                            $pos = strpos($academic, "academic_3_");
                            if ($pos !== false) {
                                $academic_array[2]["academic_3"] = $academic;
                            }
                            $pos = strpos($academic, "academic_4_");
                            if ($pos !== false) {
                                $academic_array[3]["academic_4"] = $academic;
                            }
                        }
                    }
                    return response()->json(['status' => 0, 'data' => $academic_array]);
                } elseif ($attachment_type == 'cheque') {
                    $cheque_array = [];
                    for ($x = 0; $x <= 3; $x++) {
                        $cheque_array[$x]['cheque_' . ($x + 1)] = "";
                    }
                    if ($attachments->cheque != NULL) {
                        $cheques = explode(',', $attachments->cheque);
                        foreach ($cheques as $cheque) {
                            $pos = strpos($cheque, "cheque_1_");
                            if ($pos !== false) {
                                $cheque_array[0]["cheque_1"] = $cheque;
                            }
                            $pos = strpos($cheque, "cheque_2_");
                            if ($pos !== false) {
                                $cheque_array[1]["cheque_2"] = $cheque;
                            }
                            $pos = strpos($cheque, "cheque_3_");
                            if ($pos !== false) {
                                $cheque_array[2]["cheque_3"] = $cheque;
                            }
                            $pos = strpos($cheque, "cheque_4_");
                            if ($pos !== false) {
                                $cheque_array[3]["cheque_4"] = $cheque;
                            }
                        }
                    }
                    return response()->json(['status' => 0, 'data' => $cheque_array]);
                } elseif ($attachment_type == 'affidavit') {
                    $affidavit_array = [];
                    for ($x = 0; $x <= 3; $x++) {
                        $affidavit_array[$x]['affidavit_' . ($x + 1)] = "";
                    }
                    if ($attachments->affidavit != NULL) {
                        $affidavits = explode(',', $attachments->affidavit);
                        foreach ($affidavits as $affidavit) {
                            $pos = strpos($affidavit, "affidavit_1_");
                            if ($pos !== false) {
                                $affidavit_array[0]["affidavit_1"] = $affidavit;
                            }
                            $pos = strpos($affidavit, "affidavit_2_");
                            if ($pos !== false) {
                                $affidavit_array[1]["affidavit_2"] = $affidavit;
                            }
                            $pos = strpos($affidavit, "affidavit_3_");
                            if ($pos !== false) {
                                $affidavit_array[2]["affidavit_3"] = $affidavit;
                            }
                            $pos = strpos($affidavit, "affidavit_4_");
                            if ($pos !== false) {
                                $affidavit_array[3]["affidavit_4"] = $affidavit;
                            }
                        }
                    }
                    return response()->json(['status' => 0, 'data' => $affidavit_array]);
                } elseif ($attachment_type == 'utility_bill') {
                    $utility_bill_array = [];
                    for ($x = 0; $x <= 3; $x++) {
                        $utility_bill_array[$x]['utility_bill_' . ($x + 1)] = "";
                    }
                    if ($attachments->utility_bill != NULL) {
                        $utility_bills = explode(',', $attachments->utility_bill);
                        foreach ($utility_bills as $utility_bill) {
                            $pos = strpos($utility_bill, "utility_bill_1_");
                            if ($pos !== false) {
                                $utility_bill_array[0]["utility_bill_1"] = $utility_bill;
                            }
                            $pos = strpos($utility_bill, "utility_bill_2_");
                            if ($pos !== false) {
                                $utility_bill_array[1]["utility_bill_2"] = $utility_bill;
                            }
                            $pos = strpos($utility_bill, "utility_bill_3_");
                            if ($pos !== false) {
                                $utility_bill_array[2]["utility_bill_3"] = $utility_bill;
                            }
                            $pos = strpos($utility_bill, "utility_bill_4_");
                            if ($pos !== false) {
                                $utility_bill_array[3]["utility_bill_4"] = $utility_bill;
                            }
                        }
                    }
                    return response()->json(['status' => 0, 'data' => $utility_bill_array]);
                } elseif ($attachment_type == 'cnic_nominee') {
                    $cnic_nominee_array = [];
                    for ($x = 0; $x <= 3; $x++) {
                        $cnic_nominee_array[$x]['cnic_nominee_' . ($x + 1)] = "";
                    }
                    if ($attachments->cnic_nominee != NULL) {
                        $cnic_nominees = explode(',', $attachments->cnic_nominee);
                        foreach ($cnic_nominees as $cnic_nominee) {
                            $pos = strpos($cnic_nominee, "cnic_nominee_1_");
                            if ($pos !== false) {
                                $cnic_nominee_array[0]["cnic_nominee_1"] = $cnic_nominee;
                            }
                            $pos = strpos($cnic_nominee, "cnic_nominee_2_");
                            if ($pos !== false) {
                                $cnic_nominee_array[1]["cnic_nominee_2"] = $cnic_nominee;
                            }
                            $pos = strpos($cnic_nominee, "cnic_nominee_3_");
                            if ($pos !== false) {
                                $cnic_nominee_array[2]["cnic_nominee_3"] = $cnic_nominee;
                            }
                            $pos = strpos($cnic_nominee, "cnic_nominee_4_");
                            if ($pos !== false) {
                                $cnic_nominee_array[3]["cnic_nominee_4"] = $cnic_nominee;
                            }
                        }
                    }
                    return response()->json(['status' => 0, 'data' => $cnic_nominee_array]);
                } elseif ($attachment_type == 'child_b_form') {
                    $child_b_form_array = [];
                    for ($x = 0; $x <= 3; $x++) {
                        $child_b_form_array[$x]['child_b_form_' . ($x + 1)] = "";
                    }
                    if ($attachments->child_b_form != NULL) {
                        $child_b_forms = explode(',', $attachments->child_b_form);
                        foreach ($child_b_forms as $child_b_form) {
                            $pos = strpos($child_b_form, "child_b_form_1_");
                            if ($pos !== false) {
                                $child_b_form_array[0]["child_b_form_1"] = $child_b_form;
                            }
                            $pos = strpos($child_b_form, "child_b_form_2_");
                            if ($pos !== false) {
                                $child_b_form_array[1]["child_b_form_2"] = $child_b_form;
                            }
                            $pos = strpos($child_b_form, "child_b_form_3_");
                            if ($pos !== false) {
                                $child_b_form_array[2]["child_b_form_3"] = $child_b_form;
                            }
                            $pos = strpos($child_b_form, "child_b_form_4_");
                            if ($pos !== false) {
                                $child_b_form_array[3]["child_b_form_4"] = $child_b_form;
                            }
                        }
                    }
                    return response()->json(['status' => 0, 'data' => $child_b_form_array]);
                } elseif ($attachment_type == 'cnic_spouse') {
                    $cnic_spouse_array = [];
                    for ($x = 0; $x <= 3; $x++) {
                        $cnic_spouse_array[$x]['cnic_spouse_' . ($x + 1)] = "";
                    }
                    if ($attachments->cnic_spouse != NULL) {
                        $cnic_spouses = explode(',', $attachments->cnic_spouse);
                        foreach ($cnic_spouses as $cnic_spouse) {
                            $pos = strpos($cnic_spouse, "cnic_spouse_1_");
                            if ($pos !== false) {
                                $cnic_spouse_array[0]["cnic_spouse_1"] = $cnic_spouse;
                            }
                            $pos = strpos($cnic_spouse, "cnic_spouse_2_");
                            if ($pos !== false) {
                                $cnic_spouse_array[1]["cnic_spouse_2"] = $cnic_spouse;
                            }
                            $pos = strpos($cnic_spouse, "cnic_spouse_3_");
                            if ($pos !== false) {
                                $cnic_spouse_array[2]["cnic_spouse_3"] = $cnic_spouse;
                            }
                            $pos = strpos($cnic_spouse, "cnic_spouse_4_");
                            if ($pos !== false) {
                                $cnic_spouse_array[3]["cnic_spouse_5"] = $cnic_spouse;
                            }
                        }
                    }
                    return response()->json(['status' => 0, 'data' => $cnic_spouse_array]);
                } elseif ($attachment_type == 'last_pay_slip') {
                    $last_pay_slip_array = [];
                    for ($x = 0; $x <= 3; $x++) {
                        $last_pay_slip_array[$x]['last_pay_slip_' . ($x + 1)] = "";
                    }
                    if ($attachments->last_pay_slip != NULL) {
                        $last_pay_slips = explode(',', $attachments->last_pay_slip);
                        foreach ($last_pay_slips as $last_pay_slip) {
                            $pos = strpos($last_pay_slip, "last_pay_slip_1_");
                            if ($pos !== false) {
                                $last_pay_slip_array[0]["last_pay_slip_1"] = $last_pay_slip;
                            }
                            $pos = strpos($last_pay_slip, "last_pay_slip_2_");
                            if ($pos !== false) {
                                $last_pay_slip_array[1]["last_pay_slip_2"] = $last_pay_slip;
                            }
                            $pos = strpos($last_pay_slip, "last_pay_slip_3_");
                            if ($pos !== false) {
                                $last_pay_slip_array[2]["last_pay_slip_3"] = $last_pay_slip;
                            }
                            $pos = strpos($last_pay_slip, "last_pay_slip_4_");
                            if ($pos !== false) {
                                $last_pay_slip_array[3]["last_pay_slip_4"] = $last_pay_slip;
                            }
                        }
                    }
                    return response()->json(['status' => 0, 'data' => $last_pay_slip_array]);
                } elseif ($attachment_type == 'nikkah_nama') {
                    $nikkah_nama_array = [];
                    for ($x = 0; $x <= 3; $x++) {
                        $nikkah_nama_array[$x]['nikkah_nama_' . ($x + 1)] = "";
                    }
                    if ($attachments->nikkah_nama != NULL) {
                        $nikkah_namas = explode(',', $attachments->nikkah_nama);
                        foreach ($nikkah_namas as $nikkah_nama) {
                            $pos = strpos($nikkah_nama, "nikkah_nama_1_");
                            if ($pos !== false) {
                                $nikkah_nama_array[0]["nikkah_nama_1"] = $nikkah_nama;
                            }
                            $pos = strpos($nikkah_nama, "nikkah_nama_2_");
                            if ($pos !== false) {
                                $nikkah_nama_array[1]["nikkah_nama_2"] = $nikkah_nama;
                            }
                            $pos = strpos($nikkah_nama, "nikkah_nama_3_");
                            if ($pos !== false) {
                                $nikkah_nama_array[2]["nikkah_nama_3"] = $nikkah_nama;
                            }
                            $pos = strpos($nikkah_nama, "nikkah_nama_4_");
                            if ($pos !== false) {
                                $nikkah_nama_array[3]["nikkah_nama_4"] = $nikkah_nama;
                            }
                        }
                    }
                    return response()->json(['status' => 0, 'data' => $nikkah_nama_array]);
                } elseif ($attachment_type == 'experience_certificate') {
                    $experience_certificate_array = [];
                    for ($x = 0; $x <= 3; $x++) {
                        $experience_certificate_array[$x]['experience_certificate_' . ($x + 1)] = "";
                    }
                    if ($attachments->experience != NULL) {
                        $experience_certificates = explode(',', $attachments->experience);
                        foreach ($experience_certificates as $experience_certificate) {
                            $pos = strpos($experience_certificate, "experience_certificate_1_");
                            if ($pos !== false) {
                                $experience_certificate_array[0]["experience_certificate_1"] = $experience_certificate;
                            }
                            $pos = strpos($experience_certificate, "experience_certificate_2_");
                            if ($pos !== false) {
                                $experience_certificate_array[1]["experience_certificate_2"] = $experience_certificate;
                            }
                            $pos = strpos($experience_certificate, "experience_certificate_3_");
                            if ($pos !== false) {
                                $experience_certificate_array[2]["experience_certificate_3"] = $experience_certificate;
                            }
                            $pos = strpos($experience_certificate, "experience_certificate_4_");
                            if ($pos !== false) {
                                $experience_certificate_array[3]["experience_certificate_4"] = $experience_certificate;
                            }
                        }
                    }
                    return response()->json(['status' => 0, 'data' => $experience_certificate_array]);
                }
            }
            else {
                $attachment_type = $request->attachment_type;
                if ($attachment_type == 'cv') {
                    $cv_array = [];
                    for ($x = 0; $x <= 3; $x++) {
                        $cv_array[$x]['cv_' . ($x + 1)] = "";
                    }
                    return response()->json(['status' => 0, 'data' => $cv_array]);
                } elseif ($attachment_type == 'cnic') {
                    $cnic_array = [];
                    for ($x = 0; $x <= 3; $x++) {
                        $cnic_array[$x]['cnic_' . ($x + 1)] = "";
                    }
                    return response()->json(['status' => 0, 'data' => $cnic_array]);
                } elseif ($attachment_type == 'photo') {
                    $photo_array = [];
                    for ($x = 0; $x <= 3; $x++) {
                        $photo_array[$x]['photo_' . ($x + 1)] = "";
                    }
                    return response()->json(['status' => 0, 'data' => $photo_array]);
                } elseif ($attachment_type == 'academic') {
                    $academic_array = [];
                    for ($x = 0; $x <= 3; $x++) {
                        $academic_array[$x]['academic_' . ($x + 1)] = "";
                    }
                    return response()->json(['status' => 0, 'data' => $academic_array]);
                } elseif ($attachment_type == 'cheque') {
                    $cheque_array = [];
                    for ($x = 0; $x <= 3; $x++) {
                        $cheque_array[$x]['cheque_' . ($x + 1)] = "";
                    }
                    return response()->json(['status' => 0, 'data' => $cheque_array]);
                } elseif ($attachment_type == 'affidavit') {
                    $affidavit_array = [];
                    for ($x = 0; $x <= 3; $x++) {
                        $affidavit_array[$x]['affidavit_' . ($x + 1)] = "";
                    }
                    return response()->json(['status' => 0, 'data' => $affidavit_array]);
                } elseif ($attachment_type == 'utility_bill') {
                    $utility_bill_array = [];
                    for ($x = 0; $x <= 3; $x++) {
                        $utility_bill_array[$x]['utility_bill_' . ($x + 1)] = "";
                    }
                    return response()->json(['status' => 0, 'data' => $utility_bill_array]);
                } elseif ($attachment_type == 'cnic_nominee') {
                    $cnic_nominee_array = [];
                    for ($x = 0; $x <= 3; $x++) {
                        $cnic_nominee_array[$x]['cnic_nominee_' . ($x + 1)] = "";
                    }
                    return response()->json(['status' => 0, 'data' => $cnic_nominee_array]);
                } elseif ($attachment_type == 'child_b_form') {
                    $child_b_form_array = [];
                    for ($x = 0; $x <= 3; $x++) {
                        $child_b_form_array[$x]['child_b_form_' . ($x + 1)] = "";
                    }
                    return response()->json(['status' => 0, 'data' => $child_b_form_array]);
                } elseif ($attachment_type == 'cnic_spouse') {
                    $cnic_spouse_array = [];
                    for ($x = 0; $x <= 3; $x++) {
                        $cnic_spouse_array[$x]['cnic_spouse_' . ($x + 1)] = "";
                    }
                    return response()->json(['status' => 0, 'data' => $cnic_spouse_array]);
                } elseif ($attachment_type == 'last_pay_slip') {
                    $last_pay_slip_array = [];
                    for ($x = 0; $x <= 3; $x++) {
                        $last_pay_slip_array[$x]['last_pay_slip_' . ($x + 1)] = "";
                    }
                    return response()->json(['status' => 0, 'data' => $last_pay_slip_array]);
                } elseif ($attachment_type == 'nikkah_nama') {
                    $nikkah_nama_array = [];
                    for ($x = 0; $x <= 3; $x++) {
                        $nikkah_nama_array[$x]['nikkah_nama_' . ($x + 1)] = "";
                    }
                    return response()->json(['status' => 0, 'data' => $nikkah_nama_array]);
                } elseif ($attachment_type == 'experience_certificate') {
                    $experience_certificate_array = [];
                    for ($x = 0; $x <= 3; $x++) {
                        $experience_certificate_array[$x]['experience_certificate_' . ($x + 1)] = "";
                    }
                    return response()->json(['status' => 0, 'data' => $experience_certificate_array]);
                }
            }

        }
    }

    public function admin_attachments_delete(Request $request)
    {
        $rules = [
            'employee_id' => ['required', 'integer', 'digits_between:1,10', 'exists:employees,id'],
            'attachment_type' => ['required'],
            'attachment_path' => ['required'],

        ];
        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            $message = 'Error(s) in Input';
            return response()->json(['status' => 1, 'message' => $message, 'errors' => $validate->errors()]);
        } else {
            $employee_id = $request->employee_id;
            $attachment_path = $request->attachment_path;
            $attachment_type = $request->attachment_type;

            $attachments = EmployeeAttachment::where('employee_id', $employee_id);
            if ($attachments->exists()) {
                $attachments = $attachments->first();

                if ($attachment_type == 'cv') {
                    if ($attachments->cv != NULL) {
                        $cvs = explode(',', $attachments->cv);
                        if (($key = array_search($attachment_path, $cvs)) !== false) {
                            Storage::disk('public')->delete($attachment_path);
                            unset($cvs[$key]);
                        }
                        $attachments->cv = implode(',', $cvs);
                        $attachments->save();
                    }
                } elseif ($attachment_type == 'cnic') {
                    if ($attachments->cnic != NULL) {
                        $cnics = explode(',', $attachments->cnic);
                        if (($key = array_search($attachment_path, $cnics)) !== false) {
                            Storage::disk('public')->delete($attachment_path);
                            unset($cnics[$key]);
                        }
                        $attachments->cnic = implode(',', $cnics);
                        $attachments->save();
                    }
                } elseif ($attachment_type == 'photo') {
                    if ($attachments->photo != NULL) {
                        $photos = explode(',', $attachments->photo);
                        if (($key = array_search($attachment_path, $photos)) !== false) {
                            Storage::disk('public')->delete($attachment_path);
                            unset($photos[$key]);
                        }
                        $attachments->photo = implode(',', $photos);
                        $attachments->save();
                    }
                } elseif ($attachment_type == 'academic') {
                    if ($attachments->academic != NULL) {
                        $academics = explode(',', $attachments->academic);
                        if (($key = array_search($attachment_path, $academics)) !== false) {
                            Storage::disk('public')->delete($attachment_path);
                            unset($academics[$key]);
                        }
                        $attachments->academic = implode(',', $academics);
                        $attachments->save();
                    }
                } elseif ($attachment_type == 'experience_certificate') {
                    if ($attachments->experience != NULL) {
                        $experience_certificates = explode(',', $attachments->experience);
                        if (($key = array_search($attachment_path, $experience_certificates)) !== false) {
                            Storage::disk('public')->delete($attachment_path);
                            unset($experience_certificates[$key]);
                        }
                        $attachments->experience = implode(',', $experience_certificates);
                        $attachments->save();
                    }
                } elseif ($attachment_type == 'last_pay_slip') {
                    if ($attachments->last_pay_slip != NULL) {
                        $last_pay_slips = explode(',', $attachments->last_pay_slip);
                        if (($key = array_search($attachment_path, $last_pay_slips)) !== false) {
                            Storage::disk('public')->delete($attachment_path);
                            unset($last_pay_slips[$key]);
                        }
                        $attachments->last_pay_slip = implode(',', $last_pay_slips);
                        $attachments->save();
                    }
                } elseif ($attachment_type == 'nikkah_nama') {
                    if ($attachments->nikkah_nama != NULL) {
                        $nikkah_namas = explode(',', $attachments->nikkah_nama);
                        if (($key = array_search($attachment_path, $nikkah_namas)) !== false) {
                            Storage::disk('public')->delete($attachment_path);
                            unset($nikkah_namas[$key]);
                        }
                        $attachments->nikkah_nama = implode(',', $nikkah_namas);
                        $attachments->save();
                    }
                } elseif ($attachment_type == 'cnic_spouse') {
                    if ($attachments->cnic_spouse != NULL) {
                        $cnic_spouses = explode(',', $attachments->cnic_spouse);
                        if (($key = array_search($attachment_path, $cnic_spouses)) !== false) {
                            Storage::disk('public')->delete($attachment_path);
                            unset($cnic_spouses[$key]);
                        }
                        $attachments->cnic_spouse = implode(',', $cnic_spouses);
                        $attachments->save();
                    }
                } elseif ($attachment_type == 'child_b_form') {
                    if ($attachments->child_b_form != NULL) {
                        $child_b_forms = explode(',', $attachments->child_b_form);
                        if (($key = array_search($attachment_path, $child_b_forms)) !== false) {
                            Storage::disk('public')->delete($attachment_path);
                            unset($child_b_forms[$key]);
                        }
                        $attachments->child_b_form = implode(',', $child_b_forms);
                        $attachments->save();
                    }
                } elseif ($attachment_type == 'cnic_nominee') {
                    if ($attachments->cnic_nominee != NULL) {
                        $cnic_nominies = explode(',', $attachments->cnic_nominee);
                        if (($key = array_search($attachment_path, $cnic_nominies)) !== false) {
                            Storage::disk('public')->delete($attachment_path);
                            unset($cnic_nominies[$key]);
                        }
                        $attachments->cnic_nominee = implode(',', $cnic_nominies);
                        $attachments->save();
                    }
                } elseif ($attachment_type == 'utility_bill') {
                    if ($attachments->utility_bill != NULL) {
                        $utility_bills = explode(',', $attachments->utility_bill);
                        if (($key = array_search($attachment_path, $utility_bills)) !== false) {
                            Storage::disk('public')->delete($attachment_path);
                            unset($utility_bills[$key]);
                        }
                        $attachments->utility_bill = implode(',', $utility_bills);
                        $attachments->save();
                    }
                } elseif ($attachment_type == 'affidavit') {
                    if ($attachments->affidavit != NULL) {
                        $affidavits = explode(',', $attachments->affidavit);
                        if (($key = array_search($attachment_path, $affidavits)) !== false) {
                            Storage::disk('public')->delete($attachment_path);
                            unset($affidavits[$key]);
                        }
                        $attachments->affidavit = implode(',', $affidavits);
                        $attachments->save();
                    }
                } elseif ($attachment_type == 'cheque') {
                    if ($attachments->cheque != NULL) {
                        $cheques = explode(',', $attachments->cheque);
                        if (($key = array_search($attachment_path, $cheques)) !== false) {
                            Storage::disk('public')->delete($attachment_path);
                            unset($cheques[$key]);
                        }
                        $attachments->cheque = implode(',', $cheques);
                        $attachments->save();
                    }
                }
            }

        }
        return response()->json(['status' => 0, 'message' => 'Attachment Has Been Deleted']);
    }

    public function retail_index(Request $request){
        $admin_default_hub = Admin::where('id', $request->admin_id)->select('default_hub_id')->first();
        $retail_trax_centers = RetailTraxCenter::where('default_hub', $admin_default_hub->default_hub_id)->where('status', 1)->select('name','code', 'pickup_address_id')->get();
        $products = Product::all();
        $business_categories = BusinessCategory::all();
        $shipping_modes = RetailShippingMode::all();
        $domestic_cities = City::where('business_category_id', 1)->where('status', 1)->get();
        $international_cities = City::where('business_category_id', 2)->where('status', 1)->get();
        $domestic_overland_cities = CityDelivery::join('cities as c', 'c.id', '=', 'city_deliveries.city_id')->where('city_deliveries.booking_type_id', 1)->where('city_deliveries.shipping_mode_id', 2)->where('c.business_category_id', 1)->where('c.status', 1)->select('c.id', 'c.name')->get();
        $payment_modes = RetailPaymentMode::where('id', '=', 1)->get();
        $trax_boxes = RetailTraxBox::all();
        $banks = BanksList::all();
        return response()->json(["status" => 0, 'products' => $products, 'business_categories' => $business_categories, 'shipping_modes' => $shipping_modes, 'domestic_cities' => $domestic_cities, 'domestic_overland_cities' => $domestic_overland_cities, 'payment_modes' => $payment_modes, 'trax_boxes' => $trax_boxes, 'banks' => $banks, 'trax_centers' => $retail_trax_centers, 'international_cities' => $international_cities]);
    }

    public function retail_bank_info(Request $request){
        $shipper_info = RetailShipperInfo::where('shipper_phone_no', $request->shipper_phone_no)
            ->select('iban', 'account_number', 'cheque_image', 'bank_id');
        $shipment_count = RetailShipment::where('shipper_phone_no', $request->shipper_phone_no)->where('shipping_mode', 3)->count();
        if($shipper_info->exists()){
            $shipper_info = $shipper_info->get();
            return response()->json(["status" => 0, "shipper_bank_info" => $shipper_info, "shipment_count" => $shipment_count]);
        }
        return response()->json(["status" => 0, "shipper_bank_info" => "", "shipment_count" => ""]);
    }

    public function retail_shipment_store(Request $request)
    {
        $admin_id = $request->admin_id;
        $setting = GlobalSettings::where('type', 'retail_store')->first();
        $user_id = $setting->setting_value;
        $pickup_address_id = $request->pickup_address_id;
        $user_shipping_info = UserShippingInfo::find($pickup_address_id);
        $pickup_city_id = $user_shipping_info->city_id;
        $information_display = TRUE;

        $consignee_name = $request->input('consignee_name');
        $consignee_address = $request->input('consignee_address');
        $consignee_phone_number_1 = $request->input('consignee_cell_no');
        $consignee_phone_number_2 = NULL;
        $consignee_email_address = NULL;
        $order_id = $request->input('order_id');
        $package_type = FALSE;
        $special_instructions = NULL;
        $charges_mode_id = ($request->has('charges_mode')) ? $request->input('charges_mode') : 1;

        $shipping_mode_check = $request->input('shipping_mode_id');
        $consignee_city_id = $request->input('city_id');
        if ($shipping_mode_check == 1) {
            $shipping_mode_id = 2;
        } elseif ($shipping_mode_check == 4) {
            $shipping_mode_id = 3;
        } else {
            $shipping_mode_id = 1;
        }
        $same_day_timing_id = NULL;

        $request->weight_charges = str_replace(',', '', $request->input('weight_charges'));
        $request->fuel_surcharge = str_replace(',', '', $request->input('fuel_surcharge'));

        $city = City::find($pickup_city_id);
        $gst = $city->zone->gst;
        $total_charges_without_gst = $request->weight_charges + $request->fuel_surcharge;
        $gst = $gst * $total_charges_without_gst;
        $total_charges = $total_charges_without_gst + $gst;
        if ($shipping_mode_check == 3) {
            $amount = str_replace(',', '', $request->input('cod_amount'));
            $r_amount = 0;
            if($charges_mode_id == 2){
                $amount = $amount + $total_charges;
            }
        } else {
            $amount = 0;
            if($charges_mode_id == 2){
                $amount = $total_charges;
            }
            $r_amount = 0;
        }
        $payment_mode_id = 1;
        $try_and_buy_charges = NULL;

        $pieces_quantity = $request->input('pieces');
        $business_category_id = $request->input('business_category_id');

        if ($request->volumetric_weight == 1) {
            $estimated_weight = (($request->input('length') * $request->input('breadth') * $request->input('height')) / 5000);
            $length = $request->length;
            $breadth = $request->breadth;
            $height = $request->height;
        } else {
            $estimated_weight = $request->input('weight');
            $length = null;
            $breadth = null;
            $height = null;
        }

        $shipment_id = RetailShipmentBookController::book($user_id, 1, $pickup_address_id, $information_display, $consignee_city_id, $consignee_name, $consignee_address, $consignee_phone_number_1, $consignee_phone_number_2, $consignee_email_address, $order_id, $package_type, $special_instructions, $estimated_weight, $shipping_mode_id, $same_day_timing_id, $amount, $r_amount, $payment_mode_id, $charges_mode_id, $try_and_buy_charges, $pieces_quantity, $business_category_id, $length, $breadth, $height);

        if($business_category_id == 2) {
            $international_shipment_booking = new InternationalShipment();
            $international_shipment_booking->shipment_id = $shipment_id;
            $international_shipment_booking->postal_code = 00000;
            $international_shipment_booking->save();
        }


        $tracking_number = RetailShipmentBookController::generate_tracking_number($shipment_id, $pickup_city_id, $consignee_city_id);

        $product_type_id = $request->product_id;

        $item_description = NULL;

        $item_quantity = 1;

        if ($request->input('insurance') == 1) {
            $price = str_replace(',', '', $request->input('insurance_amount'));
            $insurance = TRUE;
        } else {
            $price = NULL;
            $insurance = FALSE;
        }

        $type = 0;

        RetailShipmentBookController::add_item($shipment_id, $product_type_id, $item_description, $item_quantity, $price, $insurance, $type);

        if ($pieces_quantity > 1) {
            RetailShipmentBookController::create_shipment_pieces($shipment_id, $pieces_quantity);
        }
        $destination = $request->city_id;

        $shipper_info = RetailShipperInfo::where('shipper_phone_no', $request->shipper_cell_no);
        if ($shipper_info->exists()) {
            $shipper_info = $shipper_info->first();
            $shipper_info->shipper_phone_no = $request->shipper_cell_no;
            $shipper_info->shipper_name = $request->shipper_name;
            $shipper_info->shipper_cnic = $request->shipper_cnic;
            $shipper_info->shipper_address = $request->shipper_address;
            $shipper_info->city_id = $pickup_city_id;


            if ($request->iban_no != null && $request->account_no != null && $request->bank_id != null) {
                $shipper_info->bank_id = $request->bank_id;
                $shipper_info->iban = $request->iban_no;
                $shipper_info->account_number = $request->account_no;
                if ($request->hasFile('cheque')) {
                    $filename = 'retail_shipper_' . $shipper_info->id . '_cheque_image.png';
                    $file = $request->file('cheque');
                    Storage::disk('public')->putFileAs('retail_shipper_cheque', $file, $filename);
                    $shipper_info->cheque_image = $filename;
                    $shipper_info->completed_status = 1;
                }
            }
            $shipper_info->save();
        } else {
            $shipper_info = new RetailShipperInfo();
            $shipper_info->shipper_phone_no = $request->shipper_cell_no;
            $shipper_info->shipper_name = $request->shipper_name;
            $shipper_info->shipper_cnic = $request->shipper_cnic;
            $shipper_info->shipper_address = $request->shipper_address;
            $shipper_info->city_id = $pickup_city_id;
            $shipper_info->save();
            if ($request->hasFile('cheque') && $request->iban_no != null && $request->account_no != null && $request->bank_id != null) {
                $shipper_info->bank_id = $request->bank_id;
                $shipper_info->iban = $request->iban_no;
                $shipper_info->account_number = $request->account_no;
                if ($request->hasFile('cheque')) {
                    $filename = 'retail_shipper_' . $shipper_info->id . '_cheque_image.png';
                    $file = $request->file('cheque');
                    Storage::disk('public')->putFileAs('retail_shipper_cheque', $file, $filename);
                    $shipper_info->cheque_image = $filename;
                    $shipper_info->completed_status = 1;
                }
            }
            $shipper_info->save();
        }
        $retail_shipment = new RetailShipment();
        $retail_shipment->shipment_id = $shipment_id;
        $retail_shipment->product_type_id = $request->product_id;
        $retail_shipment->shipping_mode = $request->shipping_mode_id;
        $retail_shipment->destination = $destination;
        $retail_shipment->payment_mode_id = $request->payment_mode_id;
        $retail_shipment->shipper_phone_no = $request->shipper_cell_no;
        $retail_shipment->shipper_name = $request->shipper_name;
        $retail_shipment->shipper_cnic = $request->shipper_cnic;
        $retail_shipment->shipper_address = $request->shipper_address;
        $retail_shipment->trax_box_id = ($request->trax_box_id != -1) ? $request->trax_box_id : null;
        $retail_shipment->total_charges_without_gst = $total_charges_without_gst;
        $retail_shipment->gst = $gst;
        $retail_shipment->total_charges = $total_charges;
        $retail_shipment->weight_charges = $request->weight_charges;
//        $retail_shipment->cash_handling_charges = $request->cash_handling_charges;
        $retail_shipment->fuel_surcharge = $request->fuel_surcharge;
        $retail_shipment->shipper_account_no = $shipper_info->id;
        $retail_shipment->weight = $estimated_weight;
        $retail_shipment->length = $length;
        $retail_shipment->breadth = $breadth;
        $retail_shipment->height = $height;
        $retail_shipment->admin_id = $admin_id;
        $retail_shipment->save();


        $date = Carbon::today()->toDateString();
        $cash_deposit = RetailCashDeposit::whereDate('created_at', $date)->where('category', 3)->where('admin_id', $admin_id);
        if($cash_deposit->exists()){
            $cash_deposit = $cash_deposit->first();
            $total_shipments = $cash_deposit->total_cn + 1;
            $total_cash = $cash_deposit->total_cash + $total_charges;
            $cash_deposit->total_cn = $total_shipments;
            $cash_deposit->total_cash = $total_cash;
            $cash_deposit->save();
        }
        else{
            $cash_deposit = new RetailCashDeposit();
            $cash_deposit->category = 3;
            $cash_deposit->admin_id = $admin_id;
            $cash_deposit->total_cn = 1;
            $cash_deposit->total_cash = $total_charges;
            $cash_deposit->save();

        }

        $cash_deposit_shipment = new RetailCashDepositShipment();
        $cash_deposit_shipment->cash_deposit_id = $cash_deposit->id;
        $cash_deposit_shipment->shipment_id = $shipment_id;
        $cash_deposit_shipment->shipping_mode_id = $request->shipping_mode_id;
        $cash_deposit_shipment->save();


        AdminPickupsController::generate($shipment_id);
        NotificationsController::send(115, $tracking_number, $shipper_info->id);

        return response()->json(['status' => 0, 'message' => 'Shipment Booked with Tracking Number: ' . $tracking_number]);

    }

    public function notification_history(Request $request)
    {
        $admin_id = $request->admin_id;
        $from_date = Carbon::now()->subDays(30)->format('Y-m-d 00:00:00');
        $to_date = Carbon::now()->format('Y-m-d 23:59:59');

        $notifiction_history = EmployeeNotificationHistory::where('employee_id', $admin_id)
            ->where('employee_type_id', 1)
            ->whereBetween('created_at', [$from_date, $to_date])
            ->orderBy('created_at', 'desc');
        if ($notifiction_history->exists()) {
            $notifiction_history = $notifiction_history->get();
            return response()->json(['status' => 0, 'data' => $notifiction_history]);
        }
        return response()->json(['status' => 1, 'message' => "Notification History Not Found"]);
    }

    public function master_cargo(Request $request)
    {
        $admin_id = $request->admin_id;
        $bag_no = $request->bag_no;
        $admin = Admin::find($admin_id);
        $cargos = CargoManifest::join('cities as oh', 'cargo_manifests.origin_hub_id', '=', 'oh.id')
            ->join('cities as dh', 'cargo_manifests.destination_hub_id', '=', 'dh.id')
            ->leftjoin('v2_junction_mappings as jm', 'cargo_manifests.junction_mapping_id', '=', 'jm.id')
            ->where('cargo_manifests.status_id', 1)
            ->select('cargo_manifests.id as cargo_id', 'cargo_manifests.bags as bags', 'cargo_manifests.shipments as shipments', 'dh.name as destination', 'oh.name as origin', 'cargo_manifests.vehicle_number as vehicle_no', 'cargo_manifests.destination_hub_id as destination_id', 'cargo_manifests.junction_mapping_id as junction_mapping_id', 'jm.destination_id as j_dest_id');

        if ($bag_no != null) {
            $cargos = $cargos->join('manifest_bags as mb', 'cargo_manifests.id', '=', 'mb.cargo_manifest_id')
                ->join('cargo_manifest_bags as b', 'mb.cargo_manifest_bag_id', '=', 'b.id')
                ->where('b.seal_number', $bag_no)
                ->whereIn('b.status_id', [2, 4, 6, 8, 9, 10])
                ->groupBy('cargo_manifests.id');
        }
        if ($cargos->exists()) {
            $cargos = $cargos->get();
            $data = array();
            foreach ($cargos as $cargo) {
                $junctions = V2Junctions::where('junction_mapping_id', $cargo->junction_mapping_id)->pluck('junction_id')->toArray();
                if ($cargo->destination_id == $admin->default_hub_id || $cargo->j_dest_id == $admin->default_hub_id || in_array($admin->default_hub_id, $junctions)) {
                    $datum = array();
                    $datum['cargo_id'] = $cargo->cargo_id;
                    $datum['bags'] = $cargo->bags;
                    $datum['shipments'] = $cargo->shipments;
                    $datum['destination'] = $cargo->destination;
                    $datum['origin'] = $cargo->origin;
                    $datum['vehicle_no'] = $cargo->vehicle_no;
                    $data[] = $datum;
                }
            }
            if(!empty($data)){
                return response()->json(['status' => 0, 'data' => $data]);
            }
            return response()->json(['status' => 1, 'message' => "No cargo found!"]);
        }
        return response()->json(['status' => 1, 'message' => "No cargo found!"]);
    }

    public function cargo_bags(Request $request)
    {
        $cargo_id = $request->cargo_id;
        $cargo_bags = CargoManifestBag::join('manifest_bags as mb', 'cargo_manifest_bags.id', '=', 'mb.cargo_manifest_bag_id')
            ->where('mb.cargo_manifest_id', $cargo_id)
            ->whereIn('cargo_manifest_bags.status_id', [2, 4, 6, 8, 9, 10])
            ->select('cargo_manifest_bags.seal_number as bag_no');
        if ($cargo_bags->exists()) {
            $cargo_bags = $cargo_bags->get();
            return response()->json(['status' => 0, 'bags' => $cargo_bags]);
        }
        return response()->json(['status' => 1, 'message' => "No bags found!"]);
    }

    public function cargo_bags_validator(Request $request)
    {
        $bag_id = $request->bag_no;
        $bags = CargoManifestBag::where('seal_number', $bag_id)
            ->whereIn('status_id',[2, 4, 6, 8, 9, 10]);

        if ($bags->exists()) {
            $bags = $bags->latest()->first();
            return response()->json(['status' => 0, 'bag_no' => $bags->seal_number, 'message' => "Valid Bag No."]);
        }
        return response()->json(['status' => 0, 'bag_no' => null, 'message' => "Invalid Bag No."]);
    }

    public function cargo_bags_details(Request $request)
    {
        $admin_id = $request->admin_id;
        $admin = Admin::find($admin_id);
        $bag_ids = explode(',', $request->bags);
        $admin_hubs = AdminHub::where('admin_id', $admin_id)->pluck('hub_id')->toArray();
        if($admin->default_hub_id){
            array_push($admin_hubs,$admin->default_hub_id);
        }
        $bags = CargoManifestBag::join('manifest_bags as mb', 'cargo_manifest_bags.id', '=', 'mb.cargo_manifest_bag_id')
            ->join('cities as oh', 'cargo_manifest_bags.origin_hub_id', '=', 'oh.id')
            ->join('cities as dh', 'cargo_manifest_bags.destination_hub_id', '=', 'dh.id')
            ->join('v2_junction_mappings as jm', 'cargo_manifest_bags.junction_mapping_id', '=', 'jm.id')
            ->join('cargo_manifests as cm', 'mb.cargo_manifest_id', '=', 'cm.id')
            ->whereIn('cargo_manifest_bags.status_id', [2, 4, 6, 8, 9, 10])
            ->whereIn('cargo_manifest_bags.seal_number', $bag_ids)
            ->where('cm.status_id', 1)
            ->where('mb.status', 0)
            ->select('cargo_manifest_bags.seal_number as bag_no', 'mb.cargo_manifest_id as manifest_id', 'dh.name as destination', 'oh.name as origin', 'cargo_manifest_bags.destination_hub_id as dest_id', 'cargo_manifest_bags.junction_mapping_id as junction_mapping_id', 'jm.destination_id as j_dest_id');
        if ($bags->exists()) {
            $bags = $bags->get();
            $data = array();
            foreach ($bags as $bag) {
                $datum = array();
                $datum["bag_no"] = $bag->bag_no;
                $datum["manifest_id"] = $bag->manifest_id;
                $datum["destination"] = $bag->destination;
                $datum["destination_id"] = $bag->dest_id;
                $datum["origin"] = $bag->origin;
                $junctions = V2Junctions::where('junction_mapping_id', $bag->junction_mapping_id);
                $datum["misroute"] = 1;
                if (in_array($bag->dest_id, $admin_hubs)) {
                    $datum["misroute"] = 0;
                }
                if (in_array($bag->j_dest_id, $admin_hubs)) {
                    $datum["misroute"] = 0;
                }
                if ($junctions->exists()) {
                    $junctions = $junctions->pluck('junction_id')->toArray();
                    if (in_array($admin->default_hub_id, $junctions)) {
                        $datum["misroute"] = 0;
                    }
                }
                $data[] = $datum;
            }
            return response()->json(['status' => 0, 'data' => $data]);
        }
        return response()->json(['status' => 1, 'message' => "No details found!"]);
    }

    public function cargo_bag_recieve(Request $request)
    {
        $admin_id = $request->admin_id;
        $admin = Admin::find($admin_id);
        $admin_hubs = AdminHub::where('admin_id', $admin_id)->pluck('hub_id')->toArray();
        if($admin->default_hub_id){
            array_push($admin_hubs,$admin->default_hub_id);
        }
        if ($request->has('bags')) {
            $bag_details = json_decode($request->bags, true);
            $bag_numbers = array();
            foreach ($bag_details as $bag_detail) {
                $bag_no = $bag_detail['bag_no'];
                $destination_id = $bag_detail['destination_id'];
                $manifest_id = $bag_detail['manifest_id'];
                $status = $bag_detail['status'];
                $cargo_manifest_bags = CargoManifestBag::where('seal_number', $bag_no);
                if ($cargo_manifest_bags->exists()) {
                    $cargo_manifest_bags = $cargo_manifest_bags->latest()->first();
                    if ($status == 1) {
                        $cargo_manifest_bags->status_id = 5;
                        $cargo_manifest_bags->junction_mapping_id = null;
                        $cargo_manifest_bags->save();
                        $cargo_manifest_bags_shipments = CargoManifestBagShipments::where('cargo_manifest_bag_id', $cargo_manifest_bags->id)->pluck('shipment_id')->toArray();
                        $shipments = Shipment::whereIn('id', $cargo_manifest_bags_shipments);
                        if ($shipments->exists()) {
                            $shipments = $shipments->get();
                            foreach ($shipments as $shipment) {
                                $shipment->shipper_status_id = 11;
                                $shipment->consignee_status_id = 11;
                                $shipment->save();
                                ShipmentsJourneyController::add($shipment->id, 11, 11, NULL, NULL, NULL, $admin_id, NULL, NULL, 1, NULL, NULL);
                            }
                        }
                    } else {
                        if (in_array($destination_id, $admin_hubs)) {
                            $cargo_manifest_bags->status_id = 7;
                            $cargo_manifest_bags->save();
                        } else {
                            $cargo_manifest_bags->status_id = 3;
                            $cargo_manifest_bags->save();
                        }
                    }
                }
                CargoManifestBagJourneyController::add($cargo_manifest_bags->id, $bag_no, $cargo_manifest_bags->status_id, $admin_id);
                $manifest_bag = ManifestBag::where('cargo_manifest_bag_id', $cargo_manifest_bags->id)
                    ->where('cargo_manifest_id', $manifest_id)->update(['status' => 1]);
                array_push($bag_numbers, $bag_no);
            }
            $bag_short_received = array();
            foreach ($bag_numbers as $bag_id) {
                $bag = CargoManifestBag::where('seal_number', $bag_id)->latest()->first();
                $cargo_bag = CargoManifest::leftjoin('manifest_bags as mb', function ($join) use ($bag) {
                    $join->on('mb.cargo_manifest_id', 'cargo_manifests.id');
                })
                    ->select(['cargo_manifests.*', 'mb.cargo_manifest_bag_id'])
                    ->where('cargo_manifests.status_id', 1)
                    ->where('mb.cargo_manifest_bag_id', $bag->id);

                if ($cargo_bag->exists()) {
                    $cargo_bag = $cargo_bag->first();
                    $manifest_bags = ManifestBag::where('cargo_manifest_id', $cargo_bag->id)->get();
                    $bag_short_received_count = 0;
                    $cargo_short_received = array();
                    foreach ($manifest_bags as $manifest_bag) {
                        if ($manifest_bag->status == 0) {
                            if (!in_array($manifest_bag->cargo_manifest_bag_id, $bag_short_received)) {
                                $short_received_bag = CargoManifestBag::find($manifest_bag->cargo_manifest_bag_id);
                                $short_received_bag->status_id = 9;
                                $short_received_bag->update();
                                CargoManifestBagJourneyController::add($short_received_bag->id, $short_received_bag->seal_number, $short_received_bag->status_id, $admin_id);
                                $bag_short_received_count++;
                                array_push($bag_short_received, $short_received_bag->id);
                                array_push($cargo_short_received, $short_received_bag->id);
                            } else {
                                $bag_short_received_count++;
                            }
                        }
                    }

                    if ($bag_short_received_count == 0) {
                        CargoManifest::find($cargo_bag->id)->update(['status_id' => 2]);
                    }
                    if (count($cargo_short_received) > 0) {
                        foreach ($cargo_short_received as $cargo_short) {
                            $bag_shipments = CargoManifestBagShipments::where('cargo_manifest_bag_id', $cargo_short)->get(['shipment_id']);
                            $shipments = array();
                            foreach ($bag_shipments as $shipment) {
                                array_push($shipments, $shipment->shipment_id);
                            }

                            DisputeController::add_cargo_short_received($cargo_bag->id, $shipments, null, 2, $admin_id);
                        }
                    }
                }
            }
            return response()->json(['status' => 0, 'message' => "Bags Recieved!"]);
        }
    }

    public function admin_signup_v2(Request $request)
    {
        if ($request->isMethod('post')) {
            $rules = [
                //Employees
                'name' => ['nullable'],
                'mother_name' => ['required'],
                'employee_gender_id' => ['nullable'],
                'city_id' => ['nullable', 'integer', 'digits_between:1,10', 'exists:cities,id'],
                'cnic_no' => ['nullable', 'regex:/^[0-9]{5}-[0-9]{7}-[0-9]{1}$/'],
                'phone_number' => ['nullable', 'regex:/^[0][0-9]{3}-[0-9]{7}$/'],
                'guardian_name' => ['nullable'],
                'religion_id' => ['nullable', 'integer', 'digits_between:1,10', 'exists:employee_religions,id'],
                'nationality_id' => ['nullable', 'integer', 'digits_between:1,10', 'exists:employee_nationalities,id'],
                'domicile_id' => ['nullable', 'integer', 'digits_between:1,10', 'exists:employee_domiciles,id'],
                'marital_status_id' => ['nullable', 'integer', 'digits_between:1,10', 'exists:employee_marital_statuses,id'],
                'blood_group_id' => ['nullable', 'integer', 'digits_between:1,10', 'exists:employee_blood_groups,id'],
                'personal_email' => ['nullable', 'email'],
                'address' => ['nullable'],
                'emergency_contact' => ['nullable'],
                'cnic_issue_date' => ['nullable'],
                'cnic_expiry_date' => ['nullable'],
                'designation_id' => ['nullable', 'integer', 'digits_between:1,10', 'exists:employee_designations,id'],
                'department_id' => ['nullable', 'integer', 'digits_between:1,10', 'exists:admin_departments,id'],
                'zone_id' => ['nullable', 'integer', 'digits_between:1,10', 'exists:zones,id'],
                'official_email' => ['nullable', 'email'],
                'official_phone_number' => ['nullable', 'regex:/^[0][0-9]{3}-[0-9]{7}$/'],
                'sonic_id' => ['nullable'],
                'place_of_birth' => ['nullable', 'integer', 'digits_between:1,10', 'exists:cities,id'],
                'date_of_birth' => ['nullable'],
                'pin' => ['required', 'integer', 'digits:4'],
                'cnic_1' => ['required', 'image', 'mimes:png,jpeg,jpg,pdf,doc,docx'],
                'cnic_2' => ['required', 'image', 'mimes:png,jpeg,jpg,pdf,doc,docx'],

                //EducationalDetails
                'education_details' => ['nullable'],

                //BankInformation
                'bank_details' => ['nullable'],

                //EmploymentHistory
                'employment_history' => ['nullable'],

                //MedicalDetails
                'medical_details' => ['nullable'],
            ];
            $response = ['status' => 1];
            $message = 'Unknown';

            $validate = Validator::make($request->all(), $rules, $this->messages);

            $validate->setAttributeNames($this->names);

            if ($validate->fails()) {
                $message = 'Error(s) in Input';
                $response['errors'] = $validate->errors();
            } else {
                $admin = Admin::where('phone_number', $request->input('phone_number'))
                    ->orWhere('cnic', $request->input('cnic_no'));

                $employee = Employee::where('employee_type_id', 1)
                    ->where('phone_number', $request->input('phone_number'))
                    ->orWhere('cnic', $request->input('cnic_no'));

                $user_request = AdminUserRequest::where('phone_number', $request->input('phone_number'))
                    ->orWhere('cnic', $request->input('cnic_no'));

                //Check Admin Already Exist
                if ($admin->exists()) {
                    $admin = $admin->first();
                    if ($admin->phone_number == $request->input('phone_number') && $admin->cnic == $request->input('cnic_no')) {
                        $message = "Phone Number & CNIC Already Exists";

                    } else if ($admin->phone_number == $request->input('phone_number')) {
                        $message = "Phone Number Already Exist";

                    } else if ($admin->cnic == $request->input('cnic_no')) {
                        $message = "CNIC Already Exist";
                    }
                } else if ($employee->exists()) {
                    $employee = $employee->first();
                    if ($employee->phone_number == $request->input('phone_number') && $employee->cnic == $request->input('cnic_no')) {
                        $message = "Phone Number & CNIC Already Exists";

                    } else if ($employee->phone_number == $request->input('phone_number')) {
                        $message = "Phone Number Already Exist";

                    } else if ($employee->cnic == $request->input('cnic_no')) {
                        $message = "CNIC Already Exist";
                    }
                } else if ($user_request->exists()) {
                    $user_request = $user_request->first();
                    if ($user_request->phone_number == $request->input('phone_number') && $user_request->cnic == $request->input('cnic_no')) {
                        $message = "Phone Number & CNIC Already Exists";

                    } else if ($user_request->phone_number == $request->input('phone_number')) {
                        $message = "Phone Number Already Exist";

                    } else if ($user_request->cnic == $request->input('cnic_no')) {
                        $message = "CNIC Already Exist";
                    }
                } else {
                    try {
                        $employee_request = new Employee();
                        $employee_request->name = $request->name;
                        $employee_request->employee_gender_id = $request->employee_gender_id;
                        $employee_request->city_id = $request->city_id;
                        $employee_request->cnic = $request->cnic_no;
                        $employee_request->phone_number = $request->phone_number;
                        $employee_request->employee_type_id = 1;
                        $employee_request->status_id = 2;
                        $employee_request->guardian_name = $request->guardian_name;
                        $employee_request->religion_id = $request->religion_id;
                        $employee_request->nationality_id = $request->nationality_id;
                        $employee_request->domicile_id = $request->domicile_id;
                        $employee_request->marital_status_id = $request->marital_status_id;
                        $employee_request->blood_group = $request->blood_group_id;
                        $employee_request->personal_email = $request->personal_email;
                        $employee_request->address = $request->address;
                        $employee_request->emergency_contact = $request->emergency_contact;
                        $employee_request->cnic_issue_date = $request->cnic_issue_date;
                        $employee_request->cnic_expiry_date = $request->cnic_expiry_date;
                        $employee_request->designation_id = $request->designation_id;
                        $employee_request->department_id = $request->department_id;
                        $employee_request->zone_id = $request->zone_id;
                        $employee_request->official_email = $request->official_email;
                        $employee_request->official_phone_number = $request->official_phone_number;
                        $employee_request->sonic_id = $request->sonic_id;
                        $employee_request->place_of_birth = $request->place_of_birth;
                        $employee_request->date_of_birth = $request->date_of_birth;
                        $employee_request->pin = $request->pin;
                        $employee_request->mother_name = $request->mother_name;
                        $employee_request->save();

                        if ($request->has('employment_history')) {
                            $employment_histories = json_decode($request->employment_history, true);
                            foreach ($employment_histories as $employment_history) {
                                $history = new EmployeeEmployementHistory();
                                $history->employee_id = $employee_request->id;
                                $history->name = $employment_history['organization_company_name'];
                                $history->designation = $employment_history['position_designation'];
                                $history->from = $employment_history['from_date'];
                                $history->to = $employment_history['to_date'];
                                $history->reason = $employment_history['reason'];
                                $history->save();
                            }
                        }

                        if ($request->has('medical_details')) {
                            $medical_details = json_decode($request->medical_details, true);
                            foreach ($medical_details as $medical_detail) {
                                $medical_info = new EmployeeMedicalInformation();
                                $medical_info->employee_id = $employee_request->id;
                                $medical_info->name = $medical_detail['name_of_family_member'];
                                $medical_info->relationship_id = $medical_detail['relation_ship'];
                                $medical_info->date_of_birth = $medical_detail['date_of_birth'];
                                $medical_info->marital_status = $medical_detail['marital_status'];
                                $medical_info->save();
                            }
                        }

                        if ($request->has('education_details')) {
                            $education_details = json_decode($request->education_details, true);
                            foreach ($education_details as $education_detail) {
                                $employee_education = new EmployeeEducationalBackground();
                                $employee_education->employee_id = $employee_request->id;
                                $employee_education->name = $education_detail['institute'];
                                $employee_education->degree = $education_detail['degree'];
                                $employee_education->grade = $education_detail['position_grade'];
                                $employee_education->passing_year = $education_detail['graduation_year'];
                                $employee_education->save();
                            }
                        }

                        if ($request->has('bank_details')) {
                            $bank_details = json_decode($request->bank_details, true);
                            foreach ($bank_details as $bank_detail) {
                                $employee_bank_info = new EmployeeBankInformation();
                                $employee_bank_info->employee_id = $employee_request->id;
                                $employee_bank_info->account_title = $bank_detail['account_tile'];
                                $employee_bank_info->branch_code = $bank_detail['branch_code'];
                                $employee_bank_info->account_no = $bank_detail['account_number'];
                                $employee_bank_info->bank_id = $bank_detail['bank'];
                                $employee_bank_info->branch_name = $bank_detail['branch'];
                                $employee_bank_info->iban = $bank_detail['iban_no'];
                                $employee_bank_info->save();
                            }
                        }

                        if ($request->hasFile('cnic_1') && $request->hasFile('cnic_2')) {
                            $employee_id = $employee_request->id;
                            $date = Carbon::now()->format('Y_m_d');
                            $attachments = new EmployeeAttachment();
                            $attachments->employee_id = $employee_id;
                            $cnic_array = [];
                            if ($request->hasFile('cnic_1')) {
                                $file = $request->file('cnic_1');
                                $filename = 'cnic_1_' . $date . '.' . $file->extension();
                                $directory = 'employee_directory/employee_' . $employee_id . '';
                                Storage::disk('public')->putFileAs($directory, $file, $filename);
                                $cnic_array[0] = $directory . '/' . $filename;
                            }
                            if ($request->hasFile('cnic_2')) {
                                $file = $request->file('cnic_2');
                                $filename = 'cnic_2_' . $date . '.' . $file->extension();
                                $directory = 'employee_directory/employee_' . $employee_id . '';
                                Storage::disk('public')->putFileAs($directory, $file, $filename);
                                $cnic_array[1] = $directory . '/' . $filename;
                            }
                            $attachments->cnic = implode(',', $cnic_array);
                            $attachments->save();
                        }

                        $response['status'] = 0;
                        $response['employee_id'] = $employee_request->id;
                        $message = 'Request Has Been Submitted and Pending for Approval';
                    } catch (Exception $ex) {
                        $response['message'] = $ex;
                    }
                }
            }
        } else {
            $message = 'Post Method is Required';
        }
        $response['message'] = $message;
        return response()->json($response);
    }

    public function employee_shift(Request $request)
    {
        $admin_id = $request->admin_id;
        $admins = Admin::find($admin_id);
        if($admins){
            $response = array();
            $employee_shift = EmployeeShift::where('id', $admins->shift_id);
            $response["status"] = 0;
            if ($employee_shift->exists()){
                $employee_shift = $employee_shift->first();
                $response["shift_name"] = $employee_shift->name;
                $response["start_time"] = $employee_shift->start_time;
                $response["end_time"] = $employee_shift->end_time;
            }
            else{
                $response["shift_name"] = "default";
                $response["start_time"] = NULL;
                $response["end_time"] = NULL;
            }
            return response()->json($response);
        }
        return response()->json(['status' => 1]);
    }

    public function month_attendance_history(Request $request)
    {
        $rules = [
            'first_day' => ['required'],
            'last_day' => ['required'],
        ];
        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            $message = 'Error(s) in Input';
            return response()->json(['status' => 1, 'message' => $message, 'errors' => $validate->errors()]);
        } else {
            $admin_id = $request->admin_id;
            $dates = $this->generateDateRange($request->first_day, $request->last_day);
            $data = array();
            $shift = EmployeeShift::join('admins as a', 'employee_shifts.id', '=', 'a.shift_id')
                ->where('a.id', $admin_id)
                ->select('employee_shifts.start_time as start_time', 'employee_shifts.extension_minutes as grace_time');
            $shift_exists = 0;
            if ($shift->exists()) {
                $shift = $shift->first();
                $shift_exists = 1;
            }
            foreach ($dates as $date) {
                $datum = array();
                $datum["date"] = Carbon::parse($date)->format("d");
                $datum["month"] = Carbon::parse($date)->format("m");
                $datum["year"] = Carbon::parse($date)->format("Y");
                $attendance = EmployeeAttendance::where('employee_id', $admin_id)
                    ->where('employee_type', 1)
                    ->whereDate('attendance_date', $date);
                if ($attendance->exists()) {
                    $attendance = $attendance->first();
                    if ($shift_exists == 1) {
                        if ($attendance->clock_in_datetime) {
                            $clock_in_date = Carbon::parse($attendance->clock_in_datetime)->format("Y-m-d");
                            $attendance_date = Carbon::parse($attendance->attendance_date)->format("Y-m-d");
                            if($attendance_date == $clock_in_date){
                                $clock_in = Carbon::parse($attendance->clock_in_datetime)->format("H:i:s");
                                $time_diff = Carbon::parse($clock_in)->diffInMinutes(Carbon::parse($shift->start_time));
                                if ($time_diff > $shift->grace_time) {
                                    $datum["status"] = 2;//Late
                                } else {
                                    $datum["status"] = 1;//Present
                                }
                            }
                            else{
                                $datum["status"] = 2;//Late
                            }
                        } else {
                            $clock_in = Carbon::parse($attendance->clock_in)->format("H:i:s");
                            $time_diff = Carbon::parse($clock_in)->diffInMinutes(Carbon::parse($shift->start_time));
                            if ($time_diff > $shift->grace_time) {
                                $datum["status"] = 2;//Late
                            } else {
                                $datum["status"] = 1;//Present
                            }
                        }
                    } else {
                        $datum["status"] = 1;
                    }
                } else {
                    $datum["status"] = 3;//Absent
                }
                $data[] = $datum;
            }
            return response()->json(['status' => 0, 'data' => $data]);
        }
    }

    public function mark_attendance_v2(Request $request)
    {

        $rules = [
            'attendance_date' => ['required'],
            'latitude' => ['required', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'longitude' => ['required', 'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'],
            'action' => ['required', 'integer', 'digits_between:1,10', 'exists:attendance_actions,id'],
        ];

        $admin_id = $request->admin_id;
        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $attendance_datetime = Carbon::parse($request->attendance_date)->format('Y-m-d H:i:s');
            $attendance_date = Carbon::parse($request->attendance_date)->format('Y-m-d');
            $attendance_time = Carbon::parse($request->attendance_date)->format('H:i:s');

            $admin_attendance = EmployeeAttendance::where('employee_id', $admin_id)
                ->whereDate('attendance_date', $attendance_date)
                ->where('employee_type', 1);
            $admin_attendance_action = new EmployeeAttendanceActionLog();
            if ($admin_attendance->exists()) {
                $admin_attendance = $admin_attendance->first();
            } else {
                $admin_attendance = new EmployeeAttendance();
                $admin_attendance->employee_id = $admin_id;
                $admin_attendance->employee_type = 1;
                $admin_attendance->attendance_date = $attendance_date;
            }
            $location_status = $this->calculate_location_status($request->latitude, $request->longitude);
            if ($request->action == 1) {
                $admin_attendance->clock_in_datetime = Carbon::now()->format("Y-m-d H:i:s");
                $admin_attendance->clock_in_latitude = $request->latitude;
                $admin_attendance->clock_in_longitude = $request->longitude;
                $admin_attendance->clock_in_location = $location_status;
                $admin_attendance->save();

                $admin_attendance_action->employee_id = $admin_id;
                $admin_attendance_action->employee_type = 1;
                $admin_attendance_action->action_id = $request->action;
                $admin_attendance_action->action_date = Carbon::now()->format("Y-m-d H:i:s");
                $admin_attendance_action->attendance_date = $attendance_date;
                $admin_attendance_action->latitude = $request->latitude;
                $admin_attendance_action->longitude = $request->longitude;
                $admin_attendance_action->location_status = $location_status;
                $admin_attendance_action->save();

                return response()->json(['status' => 0, 'message' => 'Clocked-In Successfully', 'response' => $admin_attendance_action]);
            } elseif ($request->action == 2) {
                $admin_attendance->clock_out_datetime = Carbon::now()->format("Y-m-d H:i:s");
                $admin_attendance->clock_out_latitude = $request->latitude;
                $admin_attendance->clock_out_longitude = $request->longitude;
                $admin_attendance->clock_out_location = $location_status;
                $admin_attendance->save();

                $admin_attendance_action->employee_id = $admin_id;
                $admin_attendance_action->employee_type = 1;
                $admin_attendance_action->action_id = $request->action;
                $admin_attendance_action->action_date = Carbon::now()->format("Y-m-d H:i:s");
                $admin_attendance_action->attendance_date = $attendance_date;
                $admin_attendance_action->latitude = $request->latitude;
                $admin_attendance_action->longitude = $request->longitude;
                $admin_attendance_action->location_status = $location_status;
                $admin_attendance_action->save();
                return response()->json(['status' => 0, 'message' => 'Clocked-Out Successfully', 'response' => $admin_attendance_action]);
            }

            return response()->json(['status' => 1, 'message' => 'Failed']);
        }

    }

    public function attendance_details_v2(Request $request)
    {
        $rules = [
            'attendance_date' => ['required']
        ];
        $admin_id = $request->admin_id;
        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $admin_attendance_action = EmployeeAttendanceActionLog::where('employee_id', $admin_id)
                ->whereDate('attendance_date', $request->attendance_date)
                ->where('employee_type', 1)
                ->select('action_id', 'action_date', 'latitude', 'longitude', 'location_status', 'attendance_date')
                ->orderBy('action_date', 'ASC');
            if ($admin_attendance_action->exists()) {
                $admin_attendance_action = $admin_attendance_action->get();
                return response()->json(['status' => 0, 'attendance_details' => $admin_attendance_action]);
            }
            return response()->json(['status' => 0, 'attendance_details' => []]);
        }
    }

    public function mark_attendance_api(Request $request)
    {

        $rules = [
            'attendance_date' => ['required'],
            'action_date' => ['required'],
            'latitude' => ['required', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'longitude' => ['required', 'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'],
            'action' => ['required', 'integer', 'digits_between:1,10', 'exists:attendance_actions,id'],
        ];

        $admin_id = $request->admin_id;
        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {

            $location_status = 0;
            $reporting_location = ReportingLocation::join('employees as e', 'reporting_locations.id', 'e.reporting_location_id')
                ->join('admins as a', 'e.id', 'a.employee_id')
                ->where('a.id', $admin_id);
            if($reporting_location->exists()){
                $reporting_location = $reporting_location->first();
                $reporting_location->radius;
                $destination = $reporting_location->lat . ',' . $reporting_location->long;
                $origin = $request->latitude . ',' . $request->longitude;
                $distance = $this->distance($origin, $destination);
                if ($distance > $reporting_location->radius / 1000) {
                    $location_status = 1;
                } else {
                    $location_status = 2;
                }
            }
            $attendance_date = Carbon::parse($request->attendance_date)->format('Y-m-d');
            $action_date = Carbon::createFromFormat('Y-m-d H:i:s', $request->action_date);

            $admin_attendance = EmployeeAttendance::where('employee_id', $admin_id)
                ->whereDate('attendance_date', $attendance_date)
                ->where('employee_type', 1);
            $admin_attendance_action = new EmployeeAttendanceActionLog();
            if ($admin_attendance->exists()) {
                $admin_attendance = $admin_attendance->first();
            } else {
                $admin_attendance = new EmployeeAttendance();
                $admin_attendance->employee_id = $admin_id;
                $admin_attendance->employee_type = 1;
                $admin_attendance->attendance_date = $attendance_date;
            }
            if ($request->action == 1) {
                $admin_attendance->clock_in_datetime = $action_date;
                $admin_attendance->clock_in_latitude = $request->latitude;
                $admin_attendance->clock_in_longitude = $request->longitude;
                $admin_attendance->clock_in_location = $location_status;
                $admin_attendance->save();

                $admin_attendance_action->employee_id = $admin_id;
                $admin_attendance_action->employee_type = 1;
                $admin_attendance_action->action_id = $request->action;
                $admin_attendance_action->action_date = $action_date;
                $admin_attendance_action->attendance_date = $attendance_date;
                $admin_attendance_action->latitude = $request->latitude;
                $admin_attendance_action->longitude = $request->longitude;
                $admin_attendance_action->location_status = $location_status;
                $admin_attendance_action->save();

                return response()->json(['status' => 0, 'message' => 'Clocked-In Successfully', 'response' => $admin_attendance_action]);
            } elseif ($request->action == 2) {
                $admin_attendance->clock_out_datetime = $action_date;
                $admin_attendance->clock_out_latitude = $request->latitude;
                $admin_attendance->clock_out_longitude = $request->longitude;
                $admin_attendance->clock_out_location = $location_status;
                $admin_attendance->save();

                $admin_attendance_action->employee_id = $admin_id;
                $admin_attendance_action->employee_type = 1;
                $admin_attendance_action->action_id = $request->action;
                $admin_attendance_action->action_date = $action_date;
                $admin_attendance_action->attendance_date = $attendance_date;
                $admin_attendance_action->latitude = $request->latitude;
                $admin_attendance_action->longitude = $request->longitude;
                $admin_attendance_action->location_status = $location_status;
                $admin_attendance_action->save();
                return response()->json(['status' => 0, 'message' => 'Clocked-Out Successfully', 'response' => $admin_attendance_action]);
            }

            return response()->json(['status' => 1, 'message' => 'Failed']);
        }

    }

    public function forget_password(Request $request)
    {
        $rules = [
            'email' => ['required', 'email']
        ];
        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $admin = Admin::where('email', $request->email);
            if ($admin->exists()) {
                $admin = $admin->first();
                if($admin->status == 0){
                    return response()->json(['status' => 1, 'message' => 'Account disabled, Please contact admin!']);
                }
                $this->sendResetLinkEmail($request);
                return response()->json(['status' => 0, 'message' => 'Password reset link has been sent to your email']);
            } else {
                return response()->json(['status' => 1, 'message' => 'Email is not registered']);
            }

        }
    }

    public function attendance_notification(Request $request)
    {
        $rules = [
            'latitude' => ['required', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'longitude' => ['required', 'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'],
        ];
        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $notification = AppNotification::find(10);
            if ($notification) {
                if ($notification->status) {
                    $title = $notification->title;
                    $body = $notification->body;
                    $admin_id = $request->admin_id;
                    $admin = Admin::find($admin_id);
                    if ($admin) {
                        if ($admin->reporting_location_id) {
                            $reporting_location = ReportingLocation::join('admins as a', 'reporting_locations.id', 'a.reporting_location_id')
                                ->where('a.id', $admin_id);
                        } else {
                            $reporting_location = ReportingLocation::join('employees as e', 'reporting_locations.id', 'e.reporting_location_id')
                                ->join('admins as a', 'e.id', 'a.employee_id')
                                ->where('a.id', $admin_id);
                        }
                        if ($reporting_location->exists()) {
                            $reporting_location = $reporting_location->first();
                            $reporting_location->radius;
                            $destination = $reporting_location->lat . ',' . $reporting_location->long;
                            $origin = $request->latitude . ',' . $request->longitude;
                            $distance = $this->distance($origin, $destination);
                            if ($distance <= $reporting_location->radius / 1000) {
                                if ($admin->shift_id) {
                                    $shift = EmployeeShift::where('id', $admin->shift_id);
                                } else {
                                    $shift = EmployeeShift::join('employees as e', 'employee_shifts.id', '=', 'e.shift_id')
                                        ->where('e.id', $admin->employee_id);
                                }
                                if ($shift->exists()) {
                                    $shift = $shift->first();
                                    $now_time = Carbon::createFromFormat("H:i:s", Carbon::now()->format("H:i") . ':00');
                                    $grace_time = $shift->extension_minutes;
                                    if (strpos($body, '[time]') !== FALSE) {
                                        $body = str_replace('[time]', Carbon::parse($shift->start_time)->addMinutes($grace_time + 1)->toTimeString(), $body);
                                    }
                                    if (strpos($body, '[name]') !== FALSE) {
                                        $body = str_replace('[name]', $admin->name, $body);
                                    }
                                    $attendance = EmployeeAttendance::where('employee_id', $admin_id)->where('employee_type', 1)->whereDate('attendance_date', Carbon::now()->format("Y-m-d"))
                                        ->whereNotNull('clock_in_datetime');
                                    if (!$attendance->exists()) {
                                        if ($now_time->diffInMinutes(Carbon::parse($shift->start_time)) == 0) {
                                            $notification_history = new EmployeeNotificationHistory();
                                            $notification_history->employee_id = $admin_id;
                                            $notification_history->employee_type_id = 1;
                                            $notification_history->title = $title;
                                            $notification_history->message = $body;
                                            $notification_history->save();
                                            return response()->json(['status' => 0, 'data' => ['body' => $body, 'title' => $title], 'notification' => 0, 'message' => "success"]);
                                        } elseif ($now_time->diffInMinutes(Carbon::parse($shift->start_time)->addMinutes(ceil($grace_time / 2))) == 0) {
                                            $notification_history = new EmployeeNotificationHistory();
                                            $notification_history->employee_id = $admin_id;
                                            $notification_history->employee_type_id = 1;
                                            $notification_history->title = $title;
                                            $notification_history->message = $body;
                                            $notification_history->save();
                                            return response()->json(['status' => 0, 'data' => ['body' => $body, 'title' => $title], 'notification' => 0, 'message' => "success"]);
                                        } elseif ($now_time->diffInMinutes(Carbon::parse($shift->start_time)->addMinutes(($grace_time - 1))) == 0) {
                                            $notification_history = new EmployeeNotificationHistory();
                                            $notification_history->employee_id = $admin_id;
                                            $notification_history->employee_type_id = 1;
                                            $notification_history->title = $title;
                                            $notification_history->message = $body;
                                            $notification_history->save();
                                            return response()->json(['status' => 0, 'data' => ['body' => $body, 'title' => $title], 'notification' => 0, 'message' => "success"]);
                                        } else {
                                            return response()->json(['status' => 1, 'message' => 'Time error', 'notification' => 1]);
                                        }
                                    } else {
                                        return response()->json(['status' => 1, 'message' => 'Attendance already marked', 'notification' => 1]);
                                    }
                                } else {
                                    return response()->json(['status' => 1, 'message' => 'Shift not found!', 'notification' => 1]);
                                }
                            } else {
                                return response()->json(['status' => 1, 'message' => 'User is off-site', 'notification' => 1]);
                            }
                        } else {
                            return response()->json(['status' => 1, 'message' => 'Reporting location not found', 'notification' => 1]);
                        }
                    } else {
                        return response()->json(['status' => 1, 'message' => 'User Not Found!', 'notification' => 1]);
                    }
                } else {
                    return response()->json(['status' => 1, 'message' => 'Notification Disabled', 'notification' => 1]);
                }
            } else {
                return response()->json(['status' => 1, 'message' => 'Notification Not Found!', 'notification' => 1]);
            }
        }
    }

    public function flutter_mark_attendance(Request $request)
    {

        $rules = [
            'latitude' => ['required', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'longitude' => ['required', 'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'],
            'action' => ['required', 'integer', 'digits_between:1,10', 'exists:attendance_actions,id'],
        ];

        $admin_id = $request->admin_id;
        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            //Date Decision
            $admin = Admin::find($admin_id);
            $attendance_date = Carbon::now()->format("Y-m-d");
            $admin_shift = EmployeeShift::where('id', $admin->shift_id);
            if ($admin_shift->exists()) {
                $admin_shift = $admin_shift->first();
                $shift_time = Carbon::createFromFormat('H:i:s', $admin_shift->start_time);
                if (Carbon::now()->lt($shift_time)) {
                    $attendance_date = Carbon::now()->subDays(1)->format("Y-m-d");
                }
            }
            //-------------

            //Location Check
            $location_status = 0;
            $reporting_location = ReportingLocation::join('employees as e', 'reporting_locations.id', 'e.reporting_location_id')
                ->join('admins as a', 'e.id', 'a.employee_id')
                ->where('a.id', $admin_id);
            if($reporting_location->exists()){
                $reporting_location = $reporting_location->first();
                $reporting_location->radius;
                $destination = $reporting_location->lat . ',' . $reporting_location->long;
                $origin = $request->latitude . ',' . $request->longitude;
                $distance = $this->distance($origin, $destination);
                if ($distance > $reporting_location->radius / 1000) {
                    $location_status = 1;
                } else {
                    $location_status = 2;
                }
            }
            //-------------

            //15 Seconds Check
            $admin_attendance_action = EmployeeAttendanceActionLog::where('employee_id', $admin_id)
                ->whereDate('attendance_date', $attendance_date)
                ->where('employee_type', 1)->select('action_date')->orderBy('id', 'DESC');
            if($admin_attendance_action->exists()){
                $admin_attendance_action = $admin_attendance_action->first();
                $last_action = Carbon::parse($admin_attendance_action->action_date);
                if(Carbon::now()->diffInSeconds($last_action) < 15){
                    return response()->json(['status' => 1, 'message' => 'Wait for 15 Seconds']);
                }
            }
            //-------------
            $admin_attendance = EmployeeAttendance::where('employee_id', $admin_id)
                ->whereDate('attendance_date', $attendance_date)
                ->where('employee_type', 1);
            $admin_attendance_action = new EmployeeAttendanceActionLog();
            if ($admin_attendance->exists()) {
                $admin_attendance = $admin_attendance->first();
            } else {
                $admin_attendance = new EmployeeAttendance();
                $admin_attendance->employee_id = $admin_id;
                $admin_attendance->employee_type = 1;
                $admin_attendance->attendance_date = $attendance_date;
            }
            if ($request->action == 1) {
                $admin_attendance->clock_in_datetime = Carbon::now()->format("Y-m-d H:i:s");
                $admin_attendance->clock_in_latitude = $request->latitude;
                $admin_attendance->clock_in_longitude = $request->longitude;
                $admin_attendance->clock_in_location = $location_status;
                $admin_attendance->save();

                $admin_attendance_action->employee_id = $admin_id;
                $admin_attendance_action->employee_type = 1;
                $admin_attendance_action->action_id = $request->action;
                $admin_attendance_action->action_date = Carbon::now()->format("Y-m-d H:i:s");
                $admin_attendance_action->attendance_date = $attendance_date;
                $admin_attendance_action->latitude = $request->latitude;
                $admin_attendance_action->longitude = $request->longitude;
                $admin_attendance_action->location_status = $location_status;
                $admin_attendance_action->save();

                return response()->json(['status' => 0, 'message' => 'Clocked-In Successfully', 'response' => $admin_attendance_action]);
            } elseif ($request->action == 2) {
                $admin_attendance->clock_out_datetime = Carbon::now()->format("Y-m-d H:i:s");
                $admin_attendance->clock_out_latitude = $request->latitude;
                $admin_attendance->clock_out_longitude = $request->longitude;
                $admin_attendance->clock_out_location = $location_status;
                $admin_attendance->save();

                $admin_attendance_action->employee_id = $admin_id;
                $admin_attendance_action->employee_type = 1;
                $admin_attendance_action->action_id = $request->action;
                $admin_attendance_action->action_date = Carbon::now()->format("Y-m-d H:i:s");
                $admin_attendance_action->attendance_date = $attendance_date;
                $admin_attendance_action->latitude = $request->latitude;
                $admin_attendance_action->longitude = $request->longitude;
                $admin_attendance_action->location_status = $location_status;
                $admin_attendance_action->save();
                return response()->json(['status' => 0, 'message' => 'Clocked-Out Successfully', 'response' => $admin_attendance_action]);
            }

            return response()->json(['status' => 1, 'message' => 'Failed']);
        }

    }

    public function flutter_attendance_details(Request $request)
    {
        $admin_id = $request->admin_id;
        $admin = Admin::find($admin_id);
        $date = Carbon::now()->format("Y-m-d");
        $admin_shift = EmployeeShift::where('id', $admin->shift_id);
        if ($admin_shift->exists()) {
            $admin_shift = $admin_shift->first();
            $shift_time = Carbon::createFromFormat('H:i:s', $admin_shift->start_time);
            if (Carbon::now()->lt($shift_time)) {
                $date = Carbon::now()->subDays(1)->format("Y-m-d");
            }
        }
        $admin_attendance_action = EmployeeAttendanceActionLog::where('employee_id', $admin_id)
            ->whereDate('attendance_date', $date)
            ->where('employee_type', 1)
            ->select('action_id', 'action_date', 'latitude', 'longitude', 'location_status', 'attendance_date')
            ->orderBy('action_date', 'ASC');
        if ($admin_attendance_action->exists()) {
            $admin_attendance_action = $admin_attendance_action->get();
            return response()->json(['status' => 0, 'attendance_details' => $admin_attendance_action]);
        }
        return response()->json(['status' => 0, 'attendance_details' => []]);
    }

    public function admin_payslip(Request $request)
    {
        $rules = [
            'date' => ['required']
        ];
        $admin_id = $request->admin_id;
        $admins = Admin::find($admin_id);
        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);
        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $payslip = EmployeePayslip::where('trax_id', $admins->trax_id)
                ->whereMonth('payroll_month', Carbon::parse($request->date)->format("m"))
                ->whereYear('payroll_month', Carbon::parse($request->date)->format("Y"));
            if ($payslip->exists()) {
                $payslip_obj = $payslip->get();
                $payslip = $payslip->first();
                $payroll_month = Carbon::parse($payslip->payroll_month)->format('F Y');
                $payroll_cut_off_date = Carbon::parse($payslip->payroll_cut_off_date)->toDateString();
                $personal_contact = $admins->phone_number;
                $payslip_pdf = PayslipPdf::where('payslip_id',$payslip->id);
                if ($payslip_pdf->exists()) {
                    $payslip_pdf = $payslip_pdf->first();
                    $file_url = $payslip_pdf->file_path;
                }
                else {
                    $basic_salary = ($payslip->basic_salary != NULL) ? number_format($payslip->basic_salary) : '-';
                    $house_rent = ($payslip->house_rent != NULL) ? number_format($payslip->house_rent) : '-';
                    $medical = ($payslip->medical != NULL) ? number_format($payslip->medical) : '-';
                    $gross_salary = ($payslip->gross_salary != NULL) ? number_format($payslip->gross_salary) : '-';
                    $payroll_days = ($payslip->payroll_days != NULL) ? $payslip->payroll_days : '-';
                    $present_days = ($payslip->present_days != NULL) ? $payslip->present_days : '-';
                    $absent_days = ($payslip->absent_days != NULL) ? $payslip->absent_days : '-';
                    $pay_cut_days = ($payslip->pay_cut_days != NULL) ? $payslip->pay_cut_days : '-';
                    $extra_paid_days = ($payslip->extra_paid_days != NULL) ? $payslip->extra_paid_days : '-';
                    $fuel_days = ($payslip->fuel_days != NULL) ? $payslip->fuel_days : '-';


                    $mobile_allowance = ($payslip->mobile_allowance != NULL) ? number_format($payslip->mobile_allowance) : '-';
                    $vehicle_allowance = ($payslip->vehicle_allowance != NULL) ? number_format($payslip->vehicle_allowance) : '-';
                    $fuel_allowance = ($payslip->fuel_allowance != NULL) ? number_format($payslip->fuel_allowance) : '-';
                    $conveyance_allowance = ($payslip->conveyance_allowance != NULL) ? number_format($payslip->conveyance_allowance) : '-';
                    $vehicle_maintenance = ($payslip->vehicle_maintenance != NULL) ? number_format($payslip->vehicle_maintenance) : '-';
                    $fixed_incentive = ($payslip->fixed_incentive != NULL) ? number_format($payslip->fixed_incentive) : '-';
                    $holiday_allowance = ($payslip->holiday_allowance != NULL) ? number_format($payslip->holiday_allowance) : '-';
                    $overtime = ($payslip->overtime != NULL) ? number_format($payslip->overtime) : '-';
                    $bonus = ($payslip->bonus != NULL) ? number_format($payslip->bonus) : '-';
                    $arrears = ($payslip->arrears != NULL) ? number_format($payslip->arrears) : '-';
                    $pickup_incentive = ($payslip->pickup_incentive != NULL) ? number_format($payslip->pickup_incentive) : '-';
                    $delivery_incentive = ($payslip->delivery_incentive != NULL) ? number_format($payslip->delivery_incentive) : '-';
                    $operations_incentive = ($payslip->operation_incentive != NULL) ? number_format($payslip->operation_incentive) : '-';
                    $extra_duty_allowance = ($payslip->extra_duty_allowance != NULL) ? number_format($payslip->extra_duty_allowance) : '-';
                    $others_addition = ($payslip->others_addition != NULL) ? number_format($payslip->others_addition) : '-';

                    $total_addition = ($payslip->total_salary != NULL) ? number_format($payslip->total_salary) : '-';

                    $paycut = ($payslip->paycut != NULL) ? number_format($payslip->paycut) : '-';
                    $absent = ($payslip->absent != NULL) ? number_format($payslip->absent) : '-';
                    $late_deduction = ($payslip->late_deduction != NULL) ? number_format($payslip->late_deduction) : '-';
                    $income_tax = ($payslip->income_tax != NULL) ? number_format($payslip->income_tax) : '-';
                    $eobi = ($payslip->eobi != NULL) ? number_format($payslip->eobi) : '-';
                    $advance_salary = ($payslip->advance_salary != NULL) ? number_format($payslip->advance_salary) : '-';
                    $month_closing = ($payslip->month_closing != NULL) ? number_format($payslip->month_closing) : '-';
                    $loan = ($payslip->loan != NULL) ? number_format($payslip->loan) : '-';
                    $fuel_card = ($payslip->fuel_card != NULL) ? number_format($payslip->fuel_card) : '-';
                    $open_parcel = ($payslip->open_parcel != NULL) ? number_format($payslip->open_parcel) : '-';
                    $phone_call = ($payslip->phone_call != NULL) ? number_format($payslip->phone_call) : '-';
                    $recovery = ($payslip->recovery != NULL) ? number_format($payslip->recovery) : '-';
                    $auction_sale = ($payslip->auction_sale != NULL) ? number_format($payslip->auction_sale) : '-';
                    $penalty = ($payslip->penalty != NULL) ? number_format($payslip->penalty) : '-';
                    $medical_insurance = ($payslip->medical_insurance != NULL) ? number_format($payslip->medical_insurance) : '-';
                    $van_deduction = ($payslip->van_deduction != NULL) ? number_format($payslip->van_deduction) : '-';
                    $others_deduction = ($payslip->others_deduction != NULL) ? number_format($payslip->others_deduction) : '-';

                    $total_deduction = ($payslip->total_deduction != NULL) ? number_format($payslip->total_deduction) : '-';
                    $net_salary = ($payslip->net_salary != NULL) ? number_format($payslip->net_salary) : '-';

                    $html = '<!doctype html>
                <html lang="en">
                  <head>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

                    <link rel="stylesheet" type="text/css" href="' . asset('app-assets/css/bootstrap.min.css') . '">

                    <title>Payslip</title>

                     <style>
                     @page {
                        size: A4 portrait;
                      }
                      body {
                        font-size: 0.95rem !important;
                        
                      }
                      .color.primary {
                        background: #c8c8c8 !important;
                      }

                      .color.secondary {
                        background: #ebebeb !important;
                      }
                      .border.twice {
                        border-width: 1px !important;
                      }

                      .border.twice-top {
                        border-top-width: 1px !important;
                      }

                      .border.twice-bottom {
                        border-bottom-width: 1px !important;
                      }

                      .border.twice-left {
                        border-left-width: 1px !important;
                      }

                      .border.twice-right {
                        border-right-width: 1px !important;
                      }

                      .font-small {
                        font-size: 0.65rem !important;
                      }
                      
                      .table-borderless td, .table th {
                        border: none;
                     }
                     td{
                        color: #000;
                     }
                    </style>';

                    $html .= '</head>
                  <body>
                   
                      <div class="table-responsive">
                          <table class="table table-borderless mb-0">
                          
                          <tbody>
                            <tr>
                              <td class="text-left align-middle"><img src="' . asset('img/trax_logo_new.png') . '" width="100" class=""></td>
                       
                                 <td class="text-right align-middle"><h1 class="d-block">SALARY SLIP</h1></td>
                             </tr>
                             <tr>
                                <td class="text-left align-middle">Head Office (Karachi): </td>
                                <td class="text-right align-middle"><b>Payroll Month: </b><u>' . $payroll_month . '</u></td>
                             </tr>
                             <tr>
                                <td class="text-left align-middle">Plot 105, Sector 7-A, Mehran Town, Korangi, Karachi.</td>
                                <td class="text-right align-middle"><b>Payroll Cut Off Date: </b> <u>' . $payroll_cut_off_date . '</u></td>
                                
                             </tr>
                             </tbody>
                         </table>';

                    $html .= '<table class="table border table-sm">
                    
                    <tbody>
                        <tr class="text-center">
                            <td class="color primary border twice" colspan="8"><b>Employee Information</b></td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">Employee ID</td>
                            <td colspan="2"  class="border twice-right">' . $payslip->trax_id . '</td>
                            <td colspan="2"  class="border twice-right">Date of Joining</td>
                            <td colspan="2"  class="border twice-right">' . $payslip->joining_date . '</td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">Employee Name</td>
                            <td colspan="2"  class="border twice-right">' . $payslip->name . '</td>
                            <td colspan="2"  class="border twice-right">Date of Confirmation</td>
                            <td colspan="2"  class="border twice-right">' . $payslip->confirmation_date . '</td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">Designation</td>
                            <td colspan="2"  class="border twice-right">' . $payslip->designation . '</td>
                            <td colspan="2"  class="border twice-right">Employee Type</td>
                            <td colspan="2"  class="border twice-right">' . $payslip->employee_type . '</td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">Department</td>
                            <td colspan="2"  class="border twice-right">' . $payslip->department . '</td>
                            <td colspan="2"  class="border twice-right">Employee Status</td>
                            <td colspan="2"  class="border twice-right">' . $payslip->employee_status . '</td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">Location</td>
                            <td colspan="2"  class="border twice-right">' . $payslip->hub . '</td>
                            <td colspan="2"  class="border twice-right">Personal Contact #</td>
                            <td colspan="2"  class="border twice-right">' . $personal_contact . '</td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">CNIC No.</td>
                            <td colspan="2"  class="border twice-right">' . $payslip->cnic . '</td>
                            <td colspan="2"  class="border twice-right">Bank Account No.</td>
                            <td colspan="2"  class="border twice-right">' . $payslip->iban . '</td>
                        </tr>
                        <tr class="text-center">
                            <td class="color primary border twice" colspan="8"><b>Salary Breakup</b></td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">Basic Salary</td>
                            <td colspan="2"  class="border twice-right">' . $basic_salary . '</td>
                            <td colspan="1"  class="border twice-right">Payroll Days</td>
                            <td colspan="1"  class="border twice-right">' . $payroll_days . '</td>
                            <td colspan="1"  class="border twice-right">Absent Days</td>
                            <td colspan="1"  class="border twice-right">' . $absent_days . '</td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">House Rent</td>
                            <td colspan="2"  class="border twice-right">' . $house_rent . '</td>
                            <td colspan="1"  class="border twice-right">Present Days</td>
                            <td colspan="1"  class="border twice-right">' . $present_days . '</td>
                            <td colspan="1"  class="border twice-right">Extra Paid Days</td>
                            <td colspan="1"  class="border twice-right">' . $extra_paid_days . '</td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">Medical</td>
                            <td colspan="2"  class="border twice-right">' . $medical . '</td>
                            <td colspan="1"  class="border twice-right">Pay Cut Days</td>
                            <td colspan="1"  class="border twice-right">' . $pay_cut_days . '</td>
                            <td colspan="1"  class="border twice-right">Fuel Days</td>
                            <td colspan="1"  class="border twice-right">' . $fuel_days . '</td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right"><b>Gross Salary</b></td>
                            <td colspan="2"  class="border twice-right">' . $gross_salary . '</td>
                            <td colspan="4"  class="border twice-right"></td>
                        </tr>
                        <tr class="text-center">
                            <td class="color primary border twice" colspan="4"><b>Addition</b></td>
                            <td class="color primary border twice" colspan="4"><b>Deduction</b></td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">Mobile Allowance</td>
                            <td colspan="2"  class="border twice-right">' . $mobile_allowance . '</td>
                            <td colspan="2"  class="border twice-right">Pay Cut</td>
                            <td colspan="2"  class="border twice-right">' . $paycut . '</td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">Vehicle Allowance</td>
                            <td colspan="2"  class="border twice-right">' . $vehicle_allowance . '</td>
                            <td colspan="2"  class="border twice-right">Absent</td>
                            <td colspan="2"  class="border twice-right">' . $absent . '</td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">Fuel Allowance</td>
                            <td colspan="2"  class="border twice-right">' . $fuel_allowance . '</td>
                            <td colspan="2"  class="border twice-right">Late Deduction</td>
                            <td colspan="2"  class="border twice-right">' . $late_deduction . '</td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">Conveyance Allowance</td>
                            <td colspan="2"  class="border twice-right">' . $conveyance_allowance . '</td>
                            <td colspan="2"  class="border twice-right">Income Tax</td>
                            <td colspan="2"  class="border twice-right">' . $income_tax . '</td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">Vehicle Maintenance</td>
                            <td colspan="2"  class="border twice-right">' . $vehicle_maintenance . '</td>
                            <td colspan="2"  class="border twice-right">EOBI</td>
                            <td colspan="2"  class="border twice-right">' . $eobi . '</td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">Fixed Incentive</td>
                            <td colspan="2"  class="border twice-right">' . $fixed_incentive . '</td>
                            <td colspan="2"  class="border twice-right">Advance Salary</td>
                            <td colspan="2"  class="border twice-right">' . $advance_salary . '</td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">Sunday / Holiday Allowance</td>
                            <td colspan="2"  class="border twice-right">' . $holiday_allowance . '</td>
                            <td colspan="2"  class="border twice-right">Month Closing</td>
                            <td colspan="2"  class="border twice-right">' . $month_closing . '</td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">Overtime</td>
                            <td colspan="2"  class="border twice-right">' . $overtime . '</td>
                            <td colspan="2"  class="border twice-right">Loan</td>
                            <td colspan="2"  class="border twice-right">' . $loan . '</td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">Bonus</td>
                            <td colspan="2"  class="border twice-right">' . $bonus . '</td>
                            <td colspan="2"  class="border twice-right">Fuel Card</td>
                            <td colspan="2"  class="border twice-right">' . $fuel_card . '</td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">Arrears</td>
                            <td colspan="2"  class="border twice-right">' . $arrears . '</td>
                            <td colspan="2"  class="border twice-right">Open Parcel</td>
                            <td colspan="2"  class="border twice-right">' . $open_parcel . '</td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">Pickup Incentive</td>
                            <td colspan="2"  class="border twice-right">' . $pickup_incentive . '</td>
                            <td colspan="2"  class="border twice-right">Phone Call</td>
                            <td colspan="2"  class="border twice-right">' . $phone_call . '</td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">Delivery Incentive</td>
                            <td colspan="2"  class="border twice-right">' . $delivery_incentive . '</td>
                            <td colspan="2"  class="border twice-right">Recovery</td>
                            <td colspan="2"  class="border twice-right">' . $recovery . '</td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">Operations Incentive</td>
                            <td colspan="2"  class="border twice-right">' . $operations_incentive . '</td>
                            <td colspan="2"  class="border twice-right">Auction Sale</td>
                            <td colspan="2"  class="border twice-right">' . $auction_sale . '</td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">Extra Duty Allowance</td>
                            <td colspan="2"  class="border twice-right">' . $extra_duty_allowance . '</td>
                            <td colspan="2"  class="border twice-right">Penalty</td>
                            <td colspan="2"  class="border twice-right">' . $penalty . '</td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">Others Addition</td>
                            <td colspan="2"  class="border twice-right">' . $others_addition . '</td>
                            <td colspan="2"  class="border twice-right">Medical Insurance</td>
                            <td colspan="2"  class="border twice-right">' . $medical_insurance . '</td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right"></td>
                            <td colspan="2"  class="border twice-right"></td>
                            <td colspan="2"  class="border twice-right">Van Deduction</td>
                            <td colspan="2"  class="border twice-right">' . $van_deduction . '</td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right"></td>
                            <td colspan="2"  class="border twice-right"></td>
                            <td colspan="2"  class="border twice-right">Others Deduction</td>
                            <td colspan="2"  class="border twice-right">' . $others_deduction . '</td>
                        </tr>
                        
                        <tr class="text-center">
                            <td class="color primary border twice" colspan="2"><b>Total Addition</b></td>
                            <td class="color primary border twice" colspan="2">' . $total_addition . '</td>
                            <td class="color primary border twice" colspan="2"><b>Total Deduction</b></td>
                            <td class="color primary border twice" colspan="2">' . $total_deduction . '</td>
                        </tr>
                        <tr class="text-left">
                            <td class="color primary border twice" colspan="6"><b>Net Salary</b></td>
                            <td class="color primary border twice text-center" colspan="2">' . $net_salary . '</td>
                        </tr>
                        <tr class="text-left">
                            <td class="border twice" colspan="8" rowspan="5"><i>Note: This is a system generated document and does not require any signature.</i></td>
                        </tr>
                   </tbody>
                         </table>';


                    $html .= ' 
                      </div>
                      </body>
                      </html>';

                    $pdf = SnappyPDF::loadHTML($html);

                    $filename = 'payslip_'. $payslip->id .  Carbon::now()->format('Uu') . '-' . $payroll_month . '.pdf';
                    $path = 'payslip_pdf/' . $filename;
                    $result = $pdf->download($filename);
                    Storage::disk('public')->put($path, $result);
                    $payslip_pdf = new PayslipPdf();
                    $payslip_pdf->payslip_id = $payslip->id;
                    $payslip_pdf->file_path = $path;
                    $payslip_pdf->save();
                    $file_url = $payslip_pdf->file_path;
                }
                return response()->json(['status' => 0, 'payroll_month' => $payroll_month, 'data' => $payslip_obj, 'file_url' => $file_url]);
            } else {
                return response()->json(['status' => 1, 'message' => "Payslip not found"]);
            }

        }
    }

    public function leave_index(Request $request)
    {
        if ($request->has('leave_id')) {
            $leave = EmployeeLeave::find($request->leave_id);
            if ($leave) {
                if ($leave->employee_type_id == 1) {
                    $admin = Admin::find($leave->employee_id);
                    if ($admin) {
                        if(!$admin->role->department->department_head_id){
                            return response()->json(['status' => 1, 'message' => "Department Head is not present!"]);
                        }
                        $data = array();
                        $data['trax_id'] = $admin->trax_id;
                        $data['name'] = $admin->name;
                        $data['designation'] = $admin->designation;
                        $data['department'] = $admin->role->department->name;
                        $data['approver_email'] = $admin->role->department->department_head->email;
                        $data['approver_name'] = $admin->role->department->department_head->name;
                        $role_id = $admin->role_id;
                        if ($role_id == 81) {
                            $data['user_type'] = 2;
                        } elseif (in_array($role_id, [1, 2, 3, 4, 5, 6, 35, 52, 58, 70, 63])) {
                            $data['user_type'] = 1;
                        } else {
                            $data['user_type'] = 0;
                        }
                        return response()->json(['status' => 0, 'data' => $data]);
                    }
                    return response()->json(['status' => 1, 'message' => "User not found"]);
                } elseif ($leave->employee_type_id == 2) {
                    $rider = Rider::find($leave->employee_id);
                    $department = AdminDepartment::find(6);
                    if ($department) {
                        if ($rider) {
                            if(!$department->department_head_id){
                                return response()->json(['status' => 1, 'message' => "Department Head is not present!"]);
                            }
                            $data = array();
                            $data['trax_id'] = $rider->trax_id;
                            $data['name'] = $rider->name;
                            $data['designation'] = "Rider";
                            $data['department'] = "Operations";
                            $data['approver_email'] = $department->department_head->email;
                            $data['approver_name'] = $department->department_head->name;
                            $data['user_type'] = 0;
                            return response()->json(['status' => 0, 'data' => $data]);
                        }
                        return response()->json(['status' => 1, 'message' => "Rider not found"]);
                    }
                    return response()->json(['status' => 1, 'message' => "Department Not Found"]);
                } else {
                    return response()->json(['status' => 1, 'message' => "Failed"]);
                }
            }
        } else {
            $admin_id = $request->admin_id;
            $admin = Admin::find($admin_id);
            if ($admin) {
                if(!$admin->role->department->department_head_id){
                    return response()->json(['status' => 1, 'message' => "Department Head is not present!"]);
                }
                $data = array();
                $data['trax_id'] = $admin->trax_id;
                $data['name'] = $admin->name;
                $data['designation'] = $admin->designation;
                $data['department'] = $admin->role->department->name;
                $data['approver_email'] = $admin->role->department->department_head->email;
                $data['approver_name'] = $admin->role->department->department_head->name;
                $role_id = $admin->role_id;
                if ($role_id == 81) {
                    $data['user_type'] = 2;
                } elseif (in_array($role_id, [1, 2, 3, 4, 5, 6, 35, 52, 58, 70, 63])) {
                    $data['user_type'] = 1;
                } else {
                    $data['user_type'] = 0;
                }
                return response()->json(['status' => 0, 'data' => $data]);
            }
            return response()->json(['status' => 1, 'message' => "User not found"]);
        }
    }

    public function leave_apply(Request $request)
    {
        $rules = [
            'from' => ['required'],
            'to' => ['nullable'],
            'reason' => ['required', 'max:500'],
            'leave_id' => ['nullable', 'integer', 'digits_between:1,10', 'exists:employee_leaves,id'],
        ];

        $admin_id = $request->admin_id;
        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $admin = Admin::find($admin_id);
            if ($admin) {
                if ($request->has('leave_id')) {
                    $leave_request = EmployeeLeave::where('id', $request->leave_id);
                    if ($leave_request->exists()) {
                        $leave_request = $leave_request->first();
                        $leave_request->from = $request->from;
                        $leave_request->to = $request->to;
                        $leave_request->applied_reason = $request->reason;

                        if ($leave_request->status == 2) {
                            $leave_request->updated_by = $admin_id;
                        }
                        $leave_request->save();
                        $message = "Leave Request edited successfully";
                    } else {
                        return response()->json(['status' => 1, 'message' => 'Invalid Leave Request ID']);
                    }
                } else {
                    $leave = EmployeeLeave::where('employee_id', $admin_id)->where('employee_type_id', 1)->whereIn('status', [1, 2]);
                    if ($leave->exists()) {
                        return response()->json(['status' => 1, 'message' => 'Leave Request Already Submitted & Pending for Approval']);
                    }
                    $leave_request = new EmployeeLeave();
                    $leave_request->employee_id = $admin_id;
                    $leave_request->employee_type_id = 1;
                    if (in_array($admin->role_id, [1, 2, 3, 4, 5, 6, 35, 52, 58, 70])) {
                        $reporter_id = 8;
                    } else {
                        $reporter_id = $admin->role->department->department_head_id;
                    }
                    $leave_request->reporter_id = $reporter_id;
                    $leave_request->from = $request->from;
                    $leave_request->to = $request->to;
                    $leave_request->applied_reason = $request->reason;
                    $leave_request->save();
                    $message = "Leave Request submitted successfully";
                    NotificationsController::app_notification(11, $admin_id, 1, $leave_request->id);
                    NotificationsController::app_notification(12, $leave_request->reporter_id, 1, $leave_request->id);
                }
                return response()->json(['status' => 0, 'apply_message' => $message]);
            } else {
                return response()->json(['status' => 1, 'message' => 'User Not Found']);
            }
        }

    }

    public function employee_leave_list(Request $request)
    {
        $admin_id = $request->admin_id;
        $employee_leaves = EmployeeLeave::join('leave_statuses as ls', 'employee_leaves.status', '=', 'ls.id')
            ->select('employee_leaves.id as id', 'employee_leaves.from as from', 'employee_leaves.to as to', 'employee_leaves.applied_reason as applied_reason', 'employee_leaves.rejected_reason as rejected_reason', 'employee_leaves.status as status_id', 'ls.name as status')
            ->where('employee_id', $admin_id)
            ->where('employee_type_id', 1);
        if ($employee_leaves->exists()) {
            $employee_leaves = $employee_leaves->get();
            $data = array();
            foreach ($employee_leaves as $employee_leave) {
                $datum = array();
                $datum['id'] = $employee_leave->id;
                $datum['from'] = $employee_leave->from;
                $datum['to'] = $employee_leave->to;
                $datum['applied_reason'] = $employee_leave->applied_reason;
                $datum['rejected_reason'] = $employee_leave->rejected_reason;
                $datum['status_id'] = $employee_leave->status_id;
                $datum['status'] = $employee_leave->status;
                if($employee_leave->to){
                    $start_date = Carbon::createFromFormat('Y-m-d', $employee_leave->from);
                    $end_date = Carbon::createFromFormat('Y-m-d', $employee_leave->to);
                    $datum['days_count'] = $start_date->diffInDays($end_date) + 1;
                }else{
                    $datum['days_count'] = 1;
                }
                $data[] = $datum;
            }
            return response()->json(['status' => 0, 'response' => $data]);
        }
        return response()->json(['status' => 1, 'message' => "No Leave Found!"]);
    }

    public function approver_leave_list(Request $request)
    {
        $admin_id = $request->admin_id;
        $admin = Admin::find($admin_id);
        if ($admin) {
            $admin_role = $admin->role_id;
            if ($admin_role == 63) {
                $employee_leaves = EmployeeLeave::join('leave_statuses as ls', 'employee_leaves.status', '=', 'ls.id')
                    ->select('employee_leaves.id as id', 'employee_leaves.from as from', 'employee_leaves.to as to', 'employee_leaves.applied_reason as applied_reason', 'employee_leaves.status as status_id', 'ls.name as status', 'employee_leaves.employee_id as employee_id', 'employee_leaves.employee_type_id as type_id', 'employee_leaves.rejected_reason as rejected_reason')
                    ->where('employee_leaves.status', 2);
            } elseif (in_array($admin_role, [1, 2, 3, 4, 5, 6, 35, 52, 58, 70, 81])) {
                $employee_leaves = EmployeeLeave::join('leave_statuses as ls', 'employee_leaves.status', '=', 'ls.id')
                    ->select('employee_leaves.id as id', 'employee_leaves.from as from', 'employee_leaves.to as to', 'employee_leaves.applied_reason as applied_reason', 'employee_leaves.status as status_id', 'ls.name as status', 'employee_leaves.employee_id as employee_id', 'employee_leaves.employee_type_id as type_id', 'employee_leaves.rejected_reason as rejected_reason')
                    ->where('employee_leaves.reporter_id', $admin_id);
            } else {
                return response()->json(['status' => 1, 'message' => "Invalid Role"]);
            }
            if ($employee_leaves->exists()) {
                $employee_leaves = $employee_leaves->get();
                $data = array();
                foreach ($employee_leaves as $employee_leave) {
                    $datum = array();
                    $datum['id'] = $employee_leave->id;
                    $datum['from'] = $employee_leave->from;
                    $datum['to'] = $employee_leave->to;
                    $datum['applied_reason'] = $employee_leave->applied_reason;
                    $datum['rejected_reason'] = $employee_leave->rejected_reason;
                    $datum['status_id'] = $employee_leave->status_id;
                    $datum['status'] = $employee_leave->status;
                    if ($admin_role == 63){
                        $datum['role'] = 0;
                    }else{
                        $datum['role'] = 1;
                    }
                    if($employee_leave->to){
                        $start_date = Carbon::createFromFormat('Y-m-d', $employee_leave->from);
                        $end_date = Carbon::createFromFormat('Y-m-d', $employee_leave->to);
                        $datum['days_count'] = $start_date->diffInDays($end_date) + 1;
                    }else{
                        $datum['days_count'] = 1;
                    }
                    if ($employee_leave->type_id == 1) {
                        $admin = Admin::find($employee_leave->employee_id);
                        if ($admin) {
                            $datum['name'] = $admin->name;
                            $datum['trax_id'] = $admin->trax_id;
                            $datum['designation'] = $admin->designation;
                            $data[] = $datum;
                        }
                    } elseif ($employee_leave->type_id == 2) {
                        $rider = Rider::find($employee_leave->employee_id);
                        if ($rider) {
                            $datum['name'] = $rider->name;
                            $datum['trax_id'] = $rider->trax_id;
                            $datum['designation'] = "Rider";
                            $data[] = $datum;
                        }
                    }
                }
                return response()->json(['status' => 0, 'approver_response' => $data]);
            }
            return response()->json(['status' => 1, 'message' => "No Pending Leave For Approval!"]);
        }
    }

    public function leave_approve(Request $request)
    {
        $rules = [
            'leave_id' => ['required', 'integer', 'digits_between:1,10', 'exists:employee_leaves,id'],
        ];

        $admin_id = $request->admin_id;
        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $admin = Admin::find($admin_id);
            if ($admin) {
                $employee_leaves = EmployeeLeave::where('id', $request->leave_id);
                if ($employee_leaves->exists()) {
                    $employee_leaves = $employee_leaves->first();
                    if ($employee_leaves->status == 2) {
                        $employee_leaves->status = 4;
                        $employee_leaves->updated_by = $admin_id;
                        if($employee_leaves->employee_id == 1){
                            $user = Admin::find($employee_leaves->employee_id);
                        }else{
                            $user = Rider::find($employee_leaves->employee_id);
                        }
                        if(!$user){
                            return response()->json(['status' => 1, 'message' => "Invalid Employee ID"]);
                        }
                        $shift = EmployeeShift::find($user->shift_id);
                        if($employee_leaves->to) {
                            $dates = $this->generateDateRange($employee_leaves->from, $employee_leaves->to);
                        }else {
                            $dates[] = $employee_leaves->from;
                        }
                            foreach ($dates as $date){
                                $date = Carbon::parse($date)->format("Y-m-d");
                                $mark_attendance = EmployeeAttendance::where('employee_id', $employee_leaves->employee_id)
                                    ->where('employee_type', $employee_leaves->employee_type_id)
                                    ->whereDate('attendance_date', $date);
                                if ($mark_attendance->exists()){
                                    $mark_attendance = $mark_attendance->first();
                                }else{
                                    $mark_attendance = new EmployeeAttendance();
                                }
                                $mark_attendance->attendance_date = $date;
                                $mark_attendance->employee_id = $employee_leaves->employee_id;
                                $mark_attendance->employee_type = $employee_leaves->employee_type_id;
                                $mark_attendance->clock_in_latitude = "24.85758065592256";
                                $mark_attendance->clock_in_longitude = "67.12476908400743";
                                $mark_attendance->clock_out_latitude = "24.85758065592256";
                                $mark_attendance->clock_out_longitude = "67.12476908400743";
                                if($shift){
                                    $mark_attendance->clock_in_datetime = $date.' '.$shift->start_time;
                                    $mark_attendance->clock_out_datetime = $date.' '.$shift->end_time;
                                }else{
                                    $mark_attendance->clock_in_datetime = $date.' 09:00:00';
                                    $mark_attendance->clock_out_datetime = $date.' 18:00:00';
                                }
                                $mark_attendance->leave_status = 1;
                                $mark_attendance->save();

                                $attendance_action = new EmployeeAttendanceActionLog();
                                $attendance_action->employee_id = $mark_attendance->employee_id;
                                $attendance_action->employee_type = $mark_attendance->employee_type;
                                $attendance_action->action_id = 1;
                                $attendance_action->action_date = $mark_attendance->clock_in_datetime;
                                $attendance_action->attendance_date = $date;
                                $attendance_action->latitude = $mark_attendance->clock_in_latitude;
                                $attendance_action->longitude = $mark_attendance->clock_in_longitude;
                                $attendance_action->save();

                                $attendance_action = new EmployeeAttendanceActionLog();
                                $attendance_action->employee_id = $mark_attendance->employee_id;
                                $attendance_action->employee_type = $mark_attendance->employee_type;
                                $attendance_action->action_id = 2;
                                $attendance_action->action_date = $mark_attendance->clock_out_datetime;
                                $attendance_action->attendance_date = $date;
                                $attendance_action->latitude = $mark_attendance->clock_out_latitude;
                                $attendance_action->longitude = $mark_attendance->clock_out_longitude;
                                $attendance_action->save();
                            }

                    } elseif ($employee_leaves->status == 1) {
                        $employee_leaves->status = 2;
                        $employee_leaves->updated_by = $admin_id;
                        $hr_admins = Admin::where('role_id', 63);
                        if($hr_admins->exists()){
                            $hr_admins = $hr_admins->get();
                            foreach ($hr_admins as $hr_admin){
                                NotificationsController::app_notification(13, $hr_admin->id, 1, $employee_leaves->id);
                            }
                        }
                    } else {
                        return response()->json(['status' => 1, 'message' => "Invalid Role"]);
                    }
                    $employee_leaves->save();
                    NotificationsController::app_notification(11, $employee_leaves->employee_id, $employee_leaves->employee_type_id, $employee_leaves->id);
                    return response()->json(['status' => 0, 'message' => "Leave request has been approved!"]);
                }
                return response()->json(['status' => 1, 'message' => "No Leave Found!"]);
            }
        }
    }

    public function leave_reject(Request $request)
    {
        $rules = [
            'leave_id' => ['required', 'integer', 'digits_between:1,10', 'exists:employee_leaves,id'],
            'rejection_reason' => ['required', 'max:500'],
        ];

        $admin_id = $request->admin_id;
        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $admin = Admin::find($admin_id);
            if ($admin) {
                $admin_role = $admin->role_id;
                $employee_leaves = EmployeeLeave::where('id', $request->leave_id);
                if ($employee_leaves->exists()) {
                    $employee_leaves = $employee_leaves->first();
                    if ($employee_leaves->status == 2) {
                        $employee_leaves->status = 5;
                    } elseif ($employee_leaves->status == 1) {
                        $employee_leaves->status = 3;
                    } else {
                        return response()->json(['status' => 1, 'message' => "Invalid Role"]);
                    }
                    $employee_leaves->rejected_reason = $request->rejection_reason;
                    $employee_leaves->updated_by = $admin_id;
                    $employee_leaves->save();
                    NotificationsController::app_notification(11, $employee_leaves->employee_id, $employee_leaves->employee_type_id, $employee_leaves->id);
                    return response()->json(['status' => 0, 'message' => "Leave request has been rejected!"]);
                }
                return response()->json(['status' => 1, 'message' => "No Leave Found!"]);
            }
        }
    }

    public function view_calender(Request $request)
    {
        $rules = [
            'leave_id' => ['required', 'integer', 'digits_between:1,10', 'exists:employee_leaves,id'],
        ];
        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $employee_leaves = EmployeeLeave::where('id', $request->leave_id);
            if ($employee_leaves->exists()) {
                $employee_leaves = $employee_leaves->first();
                if ($employee_leaves->to) {
                    $dates = self::generateDateRange($employee_leaves->from, $employee_leaves->to);
                    $data = array();
                    foreach ($dates as $date) {
                        $datum = array();
                        $datum['date'] = $date;
                        $datum['status'] = $employee_leaves->status;
                        $data[] = $datum;
                    }
                } else {
                    $datum = array();
                    $datum['date'] = $employee_leaves->from;
                    $datum['status'] = $employee_leaves->status;
                    $data[] = $datum;
                }
                return response()->json(['status' => 0, 'data' => $data]);
            }
            return response()->json(['status' => 1, 'message' => "No Leave Found!"]);

        }
    }

    public function hr_leave_edit(Request $request)
    {

        $rules = [
            'leave_id' => ['required', 'integer', 'digits_between:1,10', 'exists:employee_leaves,id'],
            'from' => ['required'],
            'to' => ['nullable'],
        ];

        $admin_id = $request->admin_id;
        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $leave = EmployeeLeave::where('id', $request->leave_id);
            if ($leave->exists()) {
                $leave = $leave->first();
                $leave->from = $request->from;
                $leave->to = $request->to;
                $leave->updated_by = $admin_id;
                $leave->save();
                return response()->json(['status' => 0, 'message' => 'Leave Request Edited Successfully']);
            }
            return response()->json(['status' => 1, 'message' => 'Invalid Leave Id']);
        }
    }

    public function dws_weight(Request $request)
    {

        $rules = [
            'tracking_number' => ['required', 'integer'],
            'weight' => ['required'],
            'dimension_l' => ['required'],
            'dimension_w' => ['required'],
            'dimension_h' => ['required'],
            'image_name' => ['nullable', 'mimes:pdf,png,jpeg,jpg,docx,doc'],
            'machine' => ['required'],
            'date' => ['required'],
            'package_type' => ['required'],
            'is_uploaded' => ['required'],

        ];
        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(false);
        } else {
            $shipment = Shipment::where('tracking_number', $request->tracking_number);
            if ($shipment->exists()) {
                $settings = GlobalSettings::where('type', 'global_rider_id')->first();

                if ($settings) {
                    $global_rider_id = $settings->setting_value;
                }
                $pickup_rider_id = $global_rider_id;

                $shipment = $shipment->get()->first();
                $shipment_id = $shipment->id;
                // arrive function
                // if ($shipment->actual_weight == null) {
                //     return response()->json(['status' => 1, 'message' => 'weight not found']);
                // }

                if (($shipment->shipper_status_id == 1 || $shipment->shipper_status_id == 17 || $shipment->shipper_status_id == 53 || $shipment->shipper_status_id == 61 || $shipment->shipper_status_id == 62 || $shipment->shipper_status_id == 63) && ($shipment->booking_type_id != 3 && $shipment->pieces == 1)) {
                    if($request->dimension_l < 0 || $request->dimension_w < 0 || $request->dimension_h < 0){
                            return response()->json(false);
    
                    }
                    $retail_shipment = RetailShipment::where('shipment_id',$shipment->id);
                    if($retail_shipment->exists()){
                        return response()->json(false);

                    }
                    $volume_weight = (($request->dimension_l * $request->dimension_w * $request->dimension_h) / 5000);
                    $dense_weight = $request->weight;

                    if ($shipment->business_category_id == 2) {
                        if ($dense_weight < $volume_weight) {
                            $actual_weight = $volume_weight;
                            $shipment->length = $request->dimension_l;
                            $shipment->breadth = $request->dimension_w;
                            $shipment->height = $request->dimension_h;
                        } else {
                            $actual_weight = $dense_weight;
                        }
                    } else {
                        $dws_charges = DwsWeightCharges::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id);
                        if ($dws_charges->exists()) {
                            $dws_charges = $dws_charges->get()->first();
                            $dws_charges_status = $dws_charges->dws_weight_status;
                            if ($dws_charges_status == 1) {
                                if ($dense_weight < $volume_weight) {
                                    $actual_weight = $volume_weight;
                                    $shipment->length = $request->dimension_l;
                                    $shipment->breadth = $request->dimension_w;
                                    $shipment->height = $request->dimension_h;
                                } else {
                                    $actual_weight = $dense_weight;
                                }
                            } else {
                                if ($dense_weight < $volume_weight) {
                                    $actual_weight = $dense_weight;
                                } else {
                                    $actual_weight = $volume_weight;
                                    $shipment->length = $request->dimension_l;
                                    $shipment->breadth = $request->dimension_w;
                                    $shipment->height = $request->dimension_h;
                                }
                            }
                        } else {
                            // insert High status
                            DwsWeightCharges::create([
                                'user_id' => $shipment->user_id,
                                'shipping_mode_id' => $shipment->shipping_mode_id,
                                'dws_weight_status' => 1,
                                'admin_id' => 174
                            ]);
                            
                            DwsWeightChargesController::add($shipment->user_id, $shipment->shipping_mode_id, 1, 174);
                           
                            if ($dense_weight < $volume_weight) {
                                $actual_weight = $volume_weight;
                                $shipment->length = $request->dimension_l;
                                $shipment->breadth = $request->dimension_w;
                                $shipment->height = $request->dimension_h;
                            } else {
                                $actual_weight = $dense_weight;
                            }
                            $dws_charges_status = 1; 

                            // insert High status end
                           
                        }

                    }

                    //check weight from dws 


                    //check weight from dws end
                    $shipment->actual_weight = $actual_weight;
                    $shipment->save();

                    $piece_request_remarks = null;
                    if ($shipment->shipper_status_id == 62) {
                        $shipment_pieces_request = ShipmentPiecesRequest::where('shipment_id', $shipment->id)->where('status', 1);
                        if ($shipment_pieces_request->exists()) {
                            $shipment_pieces_request = $shipment_pieces_request->first();
                            $shipment_pieces_request->status = 2;
                            $shipment_pieces_request->request_status_id = 4;
                            $shipment_pieces_request->last_updated_by_admin = $request->admin_id;
                            $shipment_pieces_request->last_updated_at = Carbon::now();
                            $shipment_pieces_request->department_id = session('department_id');
                            $shipment_pieces_request->save();
                            $piece_request_remarks = 'Resolved through Arrival';
                        }
                    }

                    $pickup_request_shipment = V2PickupRequestShipment::where('shipment_id', $shipment->id)->where('status', 0);
                    $pickup_request_id = NULL;
                    $pickup_request = NULL;
                     if ($pickup_request_shipment->exists()) {
                        $pickup_request_shipment = $pickup_request_shipment->orderBy('id', 'DESC')->first();

                        $pickup_request_id = $pickup_request_shipment->pickup_request_id;
                        $pickup_request = V2PickupRequest::where('id', $pickup_request_id)->first();
                        $reference_1_id = $pickup_request_id;
                        $rider_id = $pickup_request->current_rider_id;

                        // if (!in_array($pickup_request_shipment->pickup_request_id, $pickup_request_ids)) {
                        // $pickup_request_ids[] = $pickup_request_shipment->pickup_request_id;

                        // }

                    } else {
                        $reference_1_id = null;
                    }
                    if ($pickup_request && $pickup_request->current_rider_id == null) {
                        $rider_id = $pickup_rider_id;
                    }
                    if ($receiving_sheet_shipment = $shipment->receiving_sheet_shipment) {
                        $receiving_sheet_shipment->status = 1;
                        $receiving_sheet_shipment->save();

                        $receiving_sheet_id = $receiving_sheet_shipment->receiving_sheet_id;

                        $receiving_sheet = $receiving_sheet_shipment->receiving_sheet;

                        $receiving_sheet->received = $receiving_sheet->received + 1;

                        $receiving_sheet->save();

                        if (!ReceivingSheetReceived::where('shipment_id', $shipment_id)->exists()) {
                            $receiving_sheet_received = new ReceivingSheetReceived();

                            $receiving_sheet_received->receiving_sheet_id = $receiving_sheet_id;
                            $receiving_sheet_received->user_id = $shipment->user_id;
                            $receiving_sheet_received->pickup_address_id = $shipment->pickup_address_id;
                            $receiving_sheet_received->shipment_id = $shipment_id;

                            $receiving_sheet_received->save();
                        }
                    } else {
                        if (!ReceivingSheetReceived::where('shipment_id', $shipment_id)->exists()) {
                            $receiving_sheet_received = new ReceivingSheetReceived();

                            $receiving_sheet_received->user_id = $shipment->user_id;
                            $receiving_sheet_received->pickup_address_id = $shipment->pickup_address_id;
                            $receiving_sheet_received->shipment_id = $shipment_id;

                            $receiving_sheet_received->save();
                        }
                    }

                    $shipment->shipper_status_id = 2;
                    $shipment->consignee_status_id = 2;

                    $shipment->save();
                    $reference_2_id = null;
                    ShipmentsJourneyController::add($shipment_id, 2, 2, null, 'DWS Arrival', null, $request->admin_id, $reference_1_id, $reference_2_id, 1, null, $rider_id);

                    $self_collection_shipment = SelfCollectionShipment::where('shipment_id', $shipment_id);
                    if ($self_collection_shipment->exists()) {
                        if ($shipment->pickup_address->city->hub_id == $shipment->consignee_city->hub_id) {
                            $shipment->shipper_status_id = 15;
                            $shipment->consignee_status_id = 15;

                            $shipment->save();
                            ShipmentsJourneyController::add($shipment_id, 15, 15, null, $piece_request_remarks, null, $request->admin_id);
                            NotificationsController::send(126, $shipment_id);
                        }
                    }
                    $shipment->refresh();
                    if ($shipment->walk_in_delivery_type_id == 2 && $shipment->pickup_address->city->hub_id == $shipment->consignee_city->hub_id) {
                        $shipment->shipper_status_id = 15;
                        $shipment->consignee_status_id = 15;
                        $shipment->save();
                        ShipmentsJourneyController::add($shipment_id, 15, 15, null, $piece_request_remarks, null, $request->admin_id);
                        NotificationsController::send(126, $shipment_id);
                    }
                    if ($shipment->booking_type_id == 4) {
                        $print_shipment_ids[] = $shipment_id;
                    }
                    //Consolidated Shipments
                    $consolidated_shipment = ConsolidationShipments::where('shipment_id', $shipment_id)->first();
                    if ($consolidated_shipment) {
//                $user_shipping_info = UserShippingInfo::find($shipment->pickup_address_id);
                        if ($shipment->pickup_address->city->hub_id == $shipment->consignee_city->hub_id) {
                            $check_all_consolidation_shipments = true;

                            $shipment->shipper_status_id = 58;
                            $shipment->consignee_status_id = 58;
                            $shipment->save();

                            ShipmentsJourneyController::add($shipment_id, 58, 58, null, $piece_request_remarks, null, $request->admin_id);

                            $consolidation_id = $consolidated_shipment->consolidation_id;
                            $remaining_consolidated_shipments = ConsolidationShipments::where('consolidation_id', $consolidation_id)->get();

                            foreach ($remaining_consolidated_shipments as $remaining_consolidated_shipment) {
                                $check_remaining_consolidated_shipment = Shipment::find($remaining_consolidated_shipment->shipment_id);
                                if ($check_remaining_consolidated_shipment->shipper_status_id != 58) {
                                    $check_all_consolidation_shipments = false;
                                }
                            }

                            if ($check_all_consolidation_shipments == true) {
                                foreach ($remaining_consolidated_shipments as $update_remaining_consolidated_shipment) {
                                    $update_all_consolidated_shipment = Shipment::find($update_remaining_consolidated_shipment->shipment_id);

                                    $update_all_consolidated_shipment->shipper_status_id = 59;
                                    $update_all_consolidated_shipment->consignee_status_id = 59;

                                    $update_all_consolidated_shipment->save();

                                    ShipmentsJourneyController::add($update_remaining_consolidated_shipment->shipment_id, 59, 59, null, $piece_request_remarks, null, $request->admin_id);
                                }
                            }
                        }
                    }
                    //Consolidated Shipments

                    $booking_sms = BookingSmsForShippers::where('user_id', $shipment->user_id)->where('status', 1);
                    if ($booking_sms->exists()) {
                        NotificationsController::send(3, $shipment_id);
                    }
                    if ($shipment->packaging_material_request == 0 && $shipment->shipment_type == 1) {
                        if ($shipment->booking_type_id == 4) {
                            ShipmentChargesController::walkin_weight($shipment_id);
                        } else {
                            ShipmentChargesController::weight($shipment_id);
                            if ($shipment->business_category_id == 1) {
                                ShipmentChargesController::cash_handling($shipment_id);
                                ShipmentChargesController::insurance($shipment_id);
                                ShipmentChargesController::fuel_surcharge($shipment_id);
                            } else {
                                ShipmentChargesController::international_fuel_surcharge($shipment_id);
                            }
                        }

                        if ($shipment->walk_in_status == 0) {
                            InitialChargesWebhookController::webhook_subscription($shipment_id);
                        }
                    }

                    if ($shipment->shipment_type != 2 && $shipment->charges_mode_id == 2 && $shipment->booking_type_id != 4) {
                        $shipment = Shipment::find($shipment_id);

                        $charges = $shipment->weight_charges + $shipment->cash_handling_charges + $shipment->insurance_charges + $shipment->fuel_surcharge;

                        $gst = Zone::find($shipment->pickup_address->city->zone_id)->gst;

                        $gst = ROUND(($charges * $gst), 0, PHP_ROUND_HALF_DOWN);

                        $shipment->amount = $shipment->amount + $charges + $gst;

                        $shipment->save();

                        $print_shipment_ids[] = $shipment_id;
                    }
                    if (($shipment->charges_mode_id == 2 || $shipment->charges_mode_id == 1) && $shipment->booking_type_id == 4) {
                        // $shipment_ids = array($shipment->id);
                        // NotificationsController::send(85, $shipment_ids, $request->admin_id);
                    }

                    $pickup_request_shipment = V2PickupRequestShipment::where('shipment_id', $shipment->id)->where('pickup_request_id', $pickup_request_id);

                    if ($pickup_request_shipment->exists()) {
                        $pickup_request_shipment = $pickup_request_shipment->first();
                        $pickup_request_id = $pickup_request_shipment->pickup_request_id;
                        $pickup_request_shipment->status = 1;
                        $pickup_request_shipment->save();
                        $pickup_request_received_shipment = new V2PickupReceivedShipment();
                        $pickup_request_received_shipment->pickup_request_id = $pickup_request_id;
                        $pickup_request_received_shipment->shipment_id = $shipment->id;
                        $pickup_note_id = NULL;
                        
                        $pickup_note_request = V2PickupNoteRequest::where('pickup_request_id', $pickup_request_id)->latest()->first();
                        if ($pickup_note_request) {
                            $pickup_note_id = $pickup_note_request->pickup_note_id;
                            $pickup_note = V2PickupNote::find($pickup_note_id);
                            $pickup_rider_id = $pickup_note->rider_id;
                        }
                        $pickup_request_received_shipment->pickup_note_id = $pickup_note_id;
                        $pickup_request_received_shipment->rider_id = $pickup_rider_id;
                        $pickup_request_received_shipment->save();
                        $pickup_request = $pickup_request_shipment->pickup_request;
                        ShipmentsPickupJourneyController::add($shipment_id, 2, $request->admin_id, $pickup_request_id);

                        $pickup_request->received = $pickup_request->received + 1;
                        $pickup_request->status_id = 2;
                        $pickup_request->save();

                    }
                    $pickup_request = V2PickupRequest::find($pickup_request_id);
                    if ($pickup_request->received >= 1) {
                        $pickup_note_request = $pickup_request->pickup_note_request;
                        if ($pickup_note_request) {
                            $pickup_note_id = $pickup_note_request->pickup_note_id;
                            $pickup_note_request->status = 1;
                            $pickup_note_request->save();
                            $pickup_note = V2PickupNote::find($pickup_note_id);

                        }

                        $retail_pickup_note = RetailPickupNote::where('pickup_request_id', $pickup_request_id)->where('status', 2);
                        if ($retail_pickup_note->exists()) {
                            $retail_pickup_note = $retail_pickup_note->first();
                            $retail_pickup_note->status = 3;
                            $retail_pickup_note->save();
                        }


                    }
                    $pickup_note_requests_count = V2PickupNoteRequest::where('pickup_note_id', $pickup_note_id)->where('status', 0)->count();
                    if ($pickup_note_requests_count == 0) {
                        V2PickupNote::where('id', $pickup_note_id)->update(['status' => 1]);
                    }
                    // NotificationsController::send(4, $shipment_ids);
                    $date = Carbon::now()->format('Y_m_d');
                    if ($request->hasFile('image_name')) {
                        $file = $request->file('image_name');
                        $filename = 'image_name' . $date . '.' . $file->extension();
                        $directory = 'dws_images';
                        Storage::disk('public')->putFileAs($directory, $file, $filename);
                        $link = $directory . '/' . $filename;
                    }else{
                        $link = null;
                    }

                        $shipment_detail = ShipmentDetail::where('shipment_id', $shipment_id);
                        if ($shipment_detail->exists()) {
                            $shipment_detail = $shipment_detail->get()->first();
                            $shipment_detail->dws_image = $link;
                            if ($shipment->business_category_id == 2) {
                                $shipment_detail->dws_status = 1;
                            } else {
                                $shipment_detail->dws_status = $dws_charges_status;
                            }
                            $shipment_detail->dense_weight = $dense_weight;
                            $shipment_detail->dimension_l = $request->dimension_l;
                            $shipment_detail->dimension_w = $request->dimension_w;
                            $shipment_detail->dimension_h = $request->dimension_h;
                            $shipment_detail->save();
                        } else {
                            $shipment_detail = new ShipmentDetail;
                            $shipment_detail->shipment_id = $shipment_id;
                            $shipment_detail->dws_image = $link;
                            if ($shipment->business_category_id == 2) {
                                $shipment_detail->dws_status = 1;
                            } else {
                                $shipment_detail->dws_status = $dws_charges_status;
                            }
                            $shipment_detail->dense_weight = $dense_weight;
                            $shipment_detail->dimension_l = $request->dimension_l;
                            $shipment_detail->dimension_w = $request->dimension_w;
                            $shipment_detail->dimension_h = $request->dimension_h;
                            $shipment_detail->save();
                        }

                        $dws_detail = DwsDetail::where('shipment_id', $shipment_id);
                        if ($dws_detail->exists()) {
                            $dws_detail = $dws_detail->get()->first();
                            $dws_detail->dws_machine = $request->machine;
                            $dws_detail->dws_package_type = $request->package_type;
                            $dws_detail->dws_is_uploaded = $request->is_uploaded;
                            $dws_detail->dws_date = $request->date;
                            $dws_detail->save();
                        } else {
                            $dws_detail = new DwsDetail;
                            $dws_detail->shipment_id = $shipment_id;
                            $dws_detail->dws_machine = $request->machine;
                            $dws_detail->dws_package_type = $request->package_type;
                            $dws_detail->dws_is_uploaded = $request->is_uploaded;
                            $dws_detail->dws_date = $request->date;
                            $dws_detail->save();
                        }
                        
                    return response()->json(true);

                } else {
                    return response()->json(false);
                }

                // arrive function end

            } else {
                return response()->json(false);
            }


        }
    }

    public function month_attendance_history_v2(Request $request)
    {
        $rules = [
            'first_day' => ['required'],
            'last_day' => ['required'],
        ];
        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            $message = 'Error(s) in Input';
            return response()->json(['status' => 1, 'message' => $message, 'errors' => $validate->errors()]);
        } else {
            $admin_id = $request->admin_id;
            $dates = $this->generateDateRange($request->first_day, $request->last_day);
            $data = array();
            $shift = EmployeeShift::join('admins as a', 'employee_shifts.id', '=', 'a.shift_id')
                ->where('a.id', $admin_id)
                ->select('employee_shifts.start_time as start_time', 'employee_shifts.extension_minutes as grace_time');
            $shift_exists = 0;
            if ($shift->exists()) {
                $shift = $shift->first();
                $shift_exists = 1;
            } else {
                return response()->json(['status' => 0, 'data' => $data]);
            }
            foreach ($dates as $date) {
                $datum = array();
                $datum["date"] = Carbon::parse($date)->format("d");
                $datum["month"] = Carbon::parse($date)->format("m");
                $datum["year"] = Carbon::parse($date)->format("Y");
                $attendance = EmployeeAttendance::where('employee_id', $admin_id)
                    ->where('employee_type', 1)
                    ->whereDate('attendance_date', $date);
                if ($attendance->exists()) {
                    $attendance = $attendance->first();
                    if ($shift_exists == 1) {
                        if ($attendance->clock_in_datetime) {
                            $clock_in_date = Carbon::parse($attendance->clock_in_datetime)->format("Y-m-d");
                            $attendance_date = Carbon::parse($attendance->attendance_date)->format("Y-m-d");
                            if ($attendance_date == $clock_in_date) {
                                $clock_in = Carbon::parse($attendance->clock_in_datetime)->format("H:i:s");
                                $time_diff = Carbon::parse($clock_in)->diffInMinutes(Carbon::parse($shift->start_time));
                                if ($time_diff > $shift->grace_time) {
                                    $datum["status"] = 2;//Late
                                } else {
                                    $datum["status"] = 1;//Present
                                }
                            } else {
                                $datum["status"] = 2;//Late
                            }
                        } else {
                            $clock_in = Carbon::parse($attendance->clock_in)->format("H:i:s");
                            $time_diff = Carbon::parse($clock_in)->diffInMinutes(Carbon::parse($shift->start_time));
                            if ($time_diff > $shift->grace_time) {
                                $datum["status"] = 2;//Late
                            } else {
                                $datum["status"] = 1;//Present
                            }
                        }
                    } else {
                        $datum["status"] = 1;
                    }
                } else {
                    $datum["status"] = 3;//Absent
                }
                $data[] = $datum;
            }
            return response()->json(['status' => 0, 'data' => $data]);
        }
    }

	public function login_v3(Request $request)
    {
        $rules = [
            'phone_number' => ['required', 'regex:/^[0][0-9]{10}$/'],
            'pin' => ['required', 'integer', 'digits:4'],
            'device_token' => ['nullable']
        ];


        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $user = Admin::where('phone_number', substr_replace($request->input('phone_number'), '-', 4, 0))->orWhere('official_phone_number',substr_replace($request->input('phone_number'), '-', 4, 0));
            if ($user->exists()) {
                $user = $user->first();
                if($user->status == 0){
                    return response()->json(['status' => 1, 'message' => 'Account disabled, Please contact admin!']);
                }
                if (Hash::check($request->input('pin'), $user->password)) {
                    $employee = Employee::where('trax_id', $user->trax_id);
                    $information = array();

                    $information['id'] = $user->id;
                    $information['name'] = $user->name;
                    $information['phone'] = $user->phone_number;
                    $information['cnic'] = $user->cnic;
                    $information['cargo_user'] = (in_array($user->role_id,[11,10, 15, 55, 23, 33, 46])) ? 1 : 0;
                    if($user->designation_id){
                        $user_department = $user->Edesignation->department_id;
                    }else{
                        $user_department = $user->role->department_id;
                    }
                    $information['sales_person'] = ($user_department == 7) ? 1 : 0;
                    if ($employee->exists()) {
                        $employee = $employee->first();
                        $information['address'] = ($employee->address) ? $employee->address : "" ;
                    } else {
                        $information['address'] = '';
                    }
                    $information['role'] = 'staff';

                    if($request->has('device_token')){
                        $employee_device_token = EmployeeDeviceToken::where('employee_id', $user->id)
                            ->where('employee_type_id', 1);
                        if ($employee_device_token->exists()) {
                            $employee_device_token = $employee_device_token->first();
                        } else {
                            $employee_device_token = new EmployeeDeviceToken();
                            $employee_device_token->employee_id = $user->id;
                            $employee_device_token->employee_type_id = 1;
                        }
                        $employee_device_token->device_token = $request->get('device_token');
                        $employee_device_token->save();
                    }

                    $reporting_location = ReportingLocation::join('employees as e', 'reporting_locations.id', 'e.reporting_location_id')
                        ->join('admins as a', 'e.id', 'a.employee_id')
                        ->where('a.id', $user->id);

                    if ($reporting_location->exists()) {
                        $reporting_location = $reporting_location->first();
                        $information['distance'] = $reporting_location->radius;
                        $information['lat'] = $reporting_location->lat;
                        $information['long'] = $reporting_location->long;
                    }else{
                        $information['distance'] = 0;
                        $information['lat'] = 0;
                        $information['long'] = 0;
                    }

                    if ($user->api_token) {
                        $information['api_token'] = $user->api_token;
                    } else {
                        $api_token = uniqid(base64_encode(str_random(60)));

                        $user->api_token = $api_token;

                        $user->save();

                        $information['api_token'] = $api_token;
                    }

                    return response()->json(['status' => 0, 'message' => 'Logged In Successfully', 'information' => $information]);
                } else {
                    return response()->json(['status' => 1, 'message' => 'Invalid PIN!']);
                }
            } else {
                return response()->json(['status' => 1, 'message' => 'Invalid Credentials']);
            }
        }
    }

    public function forget_pin(Request $request)
    {
        $rules = [
            'phone_number' => ['required', 'regex:/^[0][0-9]{10}$/'],
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $admins = Admin::where('phone_number', substr_replace($request->input('phone_number'), '-', 4, 0))->orWhere('official_phone_number',substr_replace($request->input('phone_number'), '-', 4, 0));
            if ($admins->exists()) {
                $admins = $admins->first();
                if($admins->status == 1){
                    $pin = rand(100000, 999999);
                    $admins->reset_pin_otp = $pin;
                    $admins->save();
                    NotificationsController::send(162, $admins->id, $request->phone_number);
                    return response()->json(['status' => 0, 'message' => 'Otp has been sent to your phone number', 'otp' => $pin]);
                }else{
                    return response()->json(['status' => 1, 'message' => 'Your Account is Disabled']);
                }
            } else {
                return response()->json(['status' => 1, 'message' => 'Phone number not registered']);
            }
        }
    }

    public function reset_pin(Request $request)
    {
        $rules = [
            'phone_number' => ['required', 'regex:/^[0][0-9]{10}$/'],
            'otp' => ['required', 'integer', 'digits:6'],
            'pin' => ['required', 'integer', 'digits:4'],
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $admin = Admin::where('phone_number', substr_replace($request->input('phone_number'), '-', 4, 0))->orWhere('official_phone_number',substr_replace($request->input('phone_number'), '-', 4, 0));
            if ($admin->exists()) {
                $admin = $admin->first();
                if($request->input('otp') == $admin->reset_pin_otp){
                    $admin->password = bcrypt($request->pin);
                    $admin->dummy_pin = $request->pin;
                    $admin->reset_pin_otp = NULL;
                    $admin->save();

                    $employee = Employee::where('trax_id',$admin->trax_id)->where('trax_id','!=',null);
                    if($employee->exists())
                    {
                        $employee = $employee->first();
                        $employee->pin = $request->pin;
                        $employee->update();
                    }
                    $admin->reset_pin_status = 1;
                    $admin->save();
                    return response()->json(['status' => 0, 'reset_message' => 'Pin has been reset successfully']);
                }else {
                    return response()->json(['status' => 1, 'message' => 'Invalid OTP']);
                }
            } else {
                return response()->json(['status' => 1, 'message' => 'Phone number not registered']);
            }
        }
    }

    public function signup_required_details(Request $request)
    {
        if ($request->isMethod('post')) {
            $rules = [
                //Employees
                'name' => ['required'],
                'mother_name' => ['required'],
                'employee_gender_id' => ['required', 'integer', 'digits_between:1,10', 'exists:employee_genders,id'],
                'city_id' => ['required', 'integer', 'digits_between:1,10', 'exists:cities,id'],
                'shift_id' => ['required', 'integer', 'digits_between:1,10', 'exists:employee_shifts,id'],
                'staff_category_id' => ['required', 'integer', 'digits_between:1,10', 'exists:staff_categories,id'],
                'cnic_no' => ['required', 'regex:/^[0-9]{5}-[0-9]{7}-[0-9]{1}$/'],
                'phone_number' => ['required', 'regex:/^[0][0-9]{3}-[0-9]{7}$/'],
                'guardian_name' => ['required'],
                'religion_id' => ['required', 'integer', 'digits_between:1,10', 'exists:employee_religions,id'],
                'nationality_id' => ['required', 'integer', 'digits_between:1,10', 'exists:employee_nationalities,id'],
                'domicile_id' => ['nullable', 'integer', 'digits_between:1,10', 'exists:employee_domiciles,id'],
                'marital_status_id' => ['required', 'integer', 'digits_between:1,10', 'exists:employee_marital_statuses,id'],
                'blood_group_id' => ['nullable', 'integer', 'digits_between:1,10', 'exists:employee_blood_groups,id'],
                'personal_email' => ['nullable', 'email'],
                'address' => ['required'],
                'emergency_contact' => ['nullable', 'regex:/^[0][0-9]{3}-[0-9]{7}$/'],
                'emergency_contact_person' => ['nullable'],
                'designation_id' => ['required', 'integer', 'digits_between:1,10', 'exists:employee_designations,id'],
                'department_id' => ['required', 'integer', 'digits_between:1,10', 'exists:admin_departments,id'],
                'zone_id' => ['nullable', 'integer', 'digits_between:1,10', 'exists:zones,id'],
                'date_of_birth' => ['required'],
                'pin' => ['required', 'integer', 'digits:4'],
                'cnic_1' => ['required', 'mimes:png,jpeg,jpg,pdf,doc,docx'],
                'cnic_2' => ['required', 'mimes:png,jpeg,jpg,pdf,doc,docx'],

                //BankInformation
                'bank_id' => ['nullable', 'integer', 'digits_between:1,10', 'exists:banks_lists,id'],
                'account_title' => ['nullable'],
                'branch_name' => ['nullable'],
                'iban' => ['nullable'],
            ];
            $response = ['status' => 1];
            $message = 'Unknown';

            $validate = Validator::make($request->all(), $rules, $this->messages);

            $validate->setAttributeNames($this->names);

            if ($validate->fails()) {
                $message = 'Error(s) in Input';
                $response['errors'] = $validate->errors();
            } else {
                $admin = Admin::where('phone_number', $request->input('phone_number'))
                    ->orWhere('cnic', $request->input('cnic_no'));

                $employee = Employee::where('phone_number', $request->input('phone_number'))
                    ->orWhere('cnic', $request->input('cnic_no'));

                $user_request = AdminUserRequest::where('phone_number', $request->input('phone_number'))
                    ->orWhere('cnic', $request->input('cnic_no'));

                //Check Admin Already Exist
                if ($admin->exists()) {
                    $admin = $admin->first();
                    if ($admin->phone_number == $request->input('phone_number') && $admin->cnic == $request->input('cnic_no')) {
                        $message = "Phone Number & CNIC Already Exists";

                    } else if ($admin->phone_number == $request->input('phone_number')) {
                        $message = "Phone Number Already Exist";

                    } else if ($admin->cnic == $request->input('cnic_no')) {
                        $message = "CNIC Already Exist";

                    }
                } else if ($employee->exists()) {
                    $employee = $employee->first();
                    if ($employee->phone_number == $request->input('phone_number') && $employee->cnic == $request->input('cnic_no')) {
                        $message = "Phone Number & CNIC Already Exists";

                    } else if ($employee->phone_number == $request->input('phone_number')) {
                        $message = "Phone Number Already Exist";

                    } else if ($employee->cnic == $request->input('cnic_no')) {
                        $message = "CNIC Already Exist";

                    }
                } else if ($user_request->exists()) {
                    $user_request = $user_request->first();
                    if ($user_request->phone_number == $request->input('phone_number') && $user_request->cnic == $request->input('cnic_no')) {
                        $message = "Phone Number & CNIC Already Exists";

                    } else if ($user_request->phone_number == $request->input('phone_number')) {
                        $message = "Phone Number Already Exist";

                    } else if ($user_request->cnic == $request->input('cnic_no')) {
                        $message = "CNIC Already Exist";

                    }
                } else {
                    try {
                        $employee_request = new Employee();
                        $employee_request->name = $request->name;
                        $employee_request->employee_gender_id = $request->employee_gender_id;
                        $employee_request->city_id = $request->city_id;
                        $employee_request->cnic = $request->cnic_no;
                        $employee_request->phone_number = $request->phone_number;
                        $employee_request->employee_type_id = 1;
                        $employee_request->status_id = 2;
                        $employee_request->guardian_name = $request->guardian_name;
                        $employee_request->religion_id = $request->religion_id;
                        $employee_request->nationality_id = $request->nationality_id;
                        $employee_request->domicile_id = $request->domicile_id;
                        $employee_request->marital_status_id = $request->marital_status_id;
                        $employee_request->blood_group = $request->blood_group_id;
                        $employee_request->personal_email = $request->personal_email;
                        $employee_request->address = $request->address;
                        $employee_request->emergency_contact = $request->emergency_contact;
                        $employee_request->emergency_contact_person = $request->emergency_contact_person;
                        $employee_request->designation_id = $request->designation_id;
                        $employee_request->department_id = $request->department_id;
                        $employee_request->zone_id = $request->zone_id;
                        $employee_request->date_of_birth = $request->date_of_birth;
                        $employee_request->pin = $request->pin;
                        $employee_request->mother_name = $request->mother_name;
                        $employee_request->shift_id = $request->shift_id;
                        $employee_request->staff_category_id = $request->staff_category_id;
                        $employee_request->save();

                        if($request->has("bank_id") && $request->has("account_title") && $request->has("branch_name") && $request->has("iban")){
                            $employee_bank_info = new EmployeeBankInformation();
                            $employee_bank_info->employee_id = $employee_request->id;
                            $employee_bank_info->account_title = $request->account_title;
                            $employee_bank_info->bank_id = $request->bank_id;
                            $employee_bank_info->branch_name = $request->branch_name;
                            $employee_bank_info->iban = $request->iban;
                            $employee_bank_info->save();
                        }

                        if ($request->hasFile('cnic_1') && $request->hasFile('cnic_2')) {
                            $employee_id = $employee_request->id;
                            $date = Carbon::now()->format('Y_m_d');
                            $attachments = new EmployeeAttachment();
                            $attachments->employee_id = $employee_id;
                            $cnic_array = [];
                            if ($request->hasFile('cnic_1')) {
                                $file = $request->file('cnic_1');
                                $filename = 'cnic_1_' . $date . '.' . $file->extension();
                                $directory = 'employee_directory/employee_' . $employee_id . '';
                                Storage::disk('public')->putFileAs($directory, $file, $filename);
                                $cnic_array[0] = $directory . '/' . $filename;
                            }
                            if ($request->hasFile('cnic_2')) {
                                $file = $request->file('cnic_2');
                                $filename = 'cnic_2_' . $date . '.' . $file->extension();
                                $directory = 'employee_directory/employee_' . $employee_id . '';
                                Storage::disk('public')->putFileAs($directory, $file, $filename);
                                $cnic_array[1] = $directory . '/' . $filename;
                            }
                            $attachments->cnic = implode(',', $cnic_array);
                            $attachments->save();
                        }

                        $response['status'] = 0;
                        $response['employee_id'] = $employee_request->id;
                        $message = 'Request Has Been Submitted and Pending for Approval';
                    } catch (Exception $ex) {
                        $response['message'] = $ex;
                    }
                }
            }
        } else {
            $message = 'Post Method is Required';
        }
        $response['message'] = $message;
        return response()->json($response);
    }

    public function signup_optional_details(Request $request)
    {
        if ($request->isMethod('post')) {
            $rules = [
                //Employees
                'employees_id' => ['required', 'integer', 'digits_between:1,10', 'exists:employees,id'],

                //EducationalDetails
                'education_details' => ['nullable'],

                //EmploymentHistory
                'employment_history' => ['nullable'],

                //MedicalDetails
                'medical_details' => ['nullable'],
            ];
            $response = ['status' => 1];
            $message = 'Unknown';

            $validate = Validator::make($request->all(), $rules, $this->messages);

            $validate->setAttributeNames($this->names);

            if ($validate->fails()) {
                $message = 'Error(s) in Input';
                $response['errors'] = $validate->errors();
            } else {
                $employee_request = Employee::find($request->employees_id);
                if ($employee_request) {
                    try {
                        if ($request->has('employment_history')) {
                            $employment_histories = json_decode($request->employment_history, true);
                            foreach ($employment_histories as $employment_history) {
                                $history = new EmployeeEmployementHistory();
                                $history->employee_id = $employee_request->id;
                                $history->name = $employment_history['organization_name'];
                                $history->designation = $employment_history['designation'];
                                $history->from = $employment_history['from_date'];
                                $history->to = $employment_history['to_date'];
                                $history->reason = $employment_history['reason_for_leaving'];
                                $history->save();
                            }
                        }

                        if ($request->has('medical_details')) {
                            $medical_details = json_decode($request->medical_details, true);
                            foreach ($medical_details as $medical_detail) {
                                $medical_info = new EmployeeMedicalInformation();
                                $medical_info->employee_id = $employee_request->id;
                                $medical_info->name = $medical_detail['name_of_family_member'];
                                $medical_info->relationship_id = $medical_detail['relation_ship'];
                                $medical_info->date_of_birth = $medical_detail['date_of_birth'];
                                $medical_info->marital_status = $medical_detail['marital_status'];
                                $medical_info->save();
                            }
                        }

                        if ($request->has('education_details')) {
                            $education_details = json_decode($request->education_details, true);
                            foreach ($education_details as $education_detail) {
                                $employee_education = new EmployeeEducationalBackground();
                                $employee_education->employee_id = $employee_request->id;
                                $employee_education->name = $education_detail['institute'];
                                $employee_education->degree = $education_detail['degree'];
                                $employee_education->grade = $education_detail['position'];
                                $employee_education->passing_year = $education_detail['graduation_year'];
                                $employee_education->save();
                            }
                        }

                        $response['status'] = 0;
                        $response['employee_id'] = $employee_request->id;
                        $message = 'Optional details Has Been Submitted and Pending for Approval';
                    } catch (Exception $ex) {
                        $response['message'] = $ex;
                    }
                }
            }
        } else {
            $message = 'Post Method is Required';
        }
        $response['message'] = $message;
        return response()->json($response);
    }

    public function admin_ticker_images(Request $request)
    {
        $admin_ticker_images = AdminAppSlider::orderBy('id', 'ASC');
        if ($admin_ticker_images->exists()) {
            $admin_ticker_images = $admin_ticker_images->get();
            return response()->json(['status' => 0, 'images' => $admin_ticker_images]);
        } else {
            return response()->json(['status' => 1, 'message' => 'No Images Found']);
        }
    }

    public function admin_profile(Request $request)
    {
        $admin_id = $request->admin_id;
        $admin_profile = Admin::join('employees as e','admins.trax_id', '=', 'e.trax_id')
            ->join('employee_designations as d', 'd.id', '=', 'admins.designation_id')
            ->join('admin_departments as ad', 'd.department_id', '=', 'ad.id')
            ->leftjoin('employee_blood_groups as bg', 'bg.id', '=', 'e.blood_group')
            ->select('e.trax_id as trax_id', 'e.name as name', 'e.official_email as email', 'e.phone_number as phone', 'd.name as designation', 'ad.name as department_name', 'bg.name as blood_group', 'e.emergency_contact as emergency_contact_no', 'e.emergency_contact_person as emergency_contact_person', 'e.official_phone_number as official_phone_number', 'e.personal_email as personal_email')
            ->where('admins.id', $admin_id);
        if ($admin_profile->exists()) {
        $admin_profile = $admin_profile->get();
            return response()->json(['status' => 0, 'admin' => $admin_profile]);
        } else {
            return response()->json(['status' => 1, 'message' => "Admin Profile Not Found"]);
        }
    }

    public function get_employee_id(Request $request)
    {
        $admin_id = $request->admin_id;
        $admins = Admin::find($admin_id);
        if ($admins) {
            $employee = Employee::where('trax_id', $admins->trax_id);
            if ($employee->exists()) {
                $employee = $employee->first();
                return response()->json(['status' => 0, 'employee_id' => $employee->id]);
            } else {
                return response()->json(['status' => 1, 'message' => "Employee Not Found"]);
            }
        } else {
            return response()->json(['status' => 1, 'message' => "Employee Not Found"]);
        }
    }

    public function trax_directory(Request $request)
    {
        $rules = [
            'search_param' => ['required'],
            'search_with' => ['required'],
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            if ($request->search_with == 1) {

                $admin_profile = Admin::join('employees as e', 'admins.trax_id', '=', 'e.trax_id')
                    ->join('employee_designations as d', 'd.id', '=', 'admins.designation_id')
                    ->join('admin_departments as ad', 'd.department_id', '=', 'ad.id')
                    ->join('cities as c', 'c.id', '=', 'e.city_id')
                    ->leftjoin('employee_blood_groups as bg', 'bg.id', '=', 'e.blood_group')
                    ->select('e.trax_id as trax_id', 'e.name as name', 'e.official_email as email', 'e.phone_number as phone', 'd.name as designation', 'ad.name as department_name', 'bg.name as blood_group', 'e.emergency_contact as emergency_contact_no', 'e.emergency_contact_person as emergency_contact_person', 'c.name as city')
                    ->where('e.name', $request->search_param)
                    ->where('admins.status', 1);

            } elseif ($request->search_with == 2) {
                $admin_profile = Admin::join('employees as e', 'admins.trax_id', '=', 'e.trax_id')
                    ->join('employee_designations as d', 'd.id', '=', 'admins.designation_id')
                    ->join('admin_departments as ad', 'd.department_id', '=', 'ad.id')
                    ->join('cities as c', 'c.id', '=', 'e.city_id')
                    ->leftjoin('employee_blood_groups as bg', 'bg.id', '=', 'e.blood_group')
                    ->select('e.trax_id as trax_id', 'e.name as name', 'e.personal_email as email', 'e.phone_number as phone', 'd.name as designation', 'ad.name as department_name', 'bg.name as blood_group', 'e.emergency_contact as emergency_contact_no', 'e.emergency_contact_person as emergency_contact_person', 'c.name as city')
                    ->where('e.phone_number', substr_replace($request->input('search_param'), '-', 4, 0))
                    ->where('admins.status', 1);
            } elseif ($request->search_with == 3) {
                $admin_profile = Admin::join('employees as e', 'admins.trax_id', '=', 'e.trax_id')
                    ->join('employee_designations as d', 'd.id', '=', 'admins.designation_id')
                    ->join('admin_departments as ad', 'd.department_id', '=', 'ad.id')
                    ->join('cities as c', 'c.id', '=', 'e.city_id')
                    ->leftjoin('employee_blood_groups as bg', 'bg.id', '=', 'e.blood_group')
                    ->select('e.trax_id as trax_id', 'e.name as name', 'e.personal_email as email', 'e.phone_number as phone', 'd.name as designation', 'ad.name as department_name', 'bg.name as blood_group', 'e.emergency_contact as emergency_contact_no', 'e.emergency_contact_person as emergency_contact_person', 'c.name as city')
                    ->where('e.trax_id', $request->search_param)
                    ->where('admins.status', 1);
            } else {
                return response()->json(['status' => 1, 'message' => 'Provide atleast one parameter']);
            }
            if ($admin_profile->exists()) {
                $admin_profile = $admin_profile->get();
                return response()->json(['status' => 0, 'data' => $admin_profile]);
            } else {
                return response()->json(['status' => 1, 'message' => 'No User found!']);
            }
        }
    }

    public function check_profile(Request $request)
    {
        $admin_id = $request->admin_id;
        $admin_profile = Admin::join('employees as e','admins.trax_id', '=', 'e.trax_id')
            ->select('e.id as employee_id', 'e.blood_group as blood_group_id', 'e.emergency_contact as emergency_contact_no', 'e.emergency_contact_person as emergency_contact_person')
            ->where('admins.id', $admin_id);
        if ($admin_profile->exists()) {
            $admin_profile = $admin_profile->first();
            if(!$admin_profile->blood_group_id || !$admin_profile->emergency_contact_no || !$admin_profile->emergency_contact_person){
                return response()->json(['status' => 0, 'message' => "Please Update Your Profile"]);
            }else{
                return response()->json(['status' => 1, 'message' => "Profile already updated"]);
            }
        } else {
            return response()->json(['status' => 1, 'message' => "Admin Profile Not Found"]);
        }
    }

    public function get_profile(Request $request)
    {
        $admin_id = $request->admin_id;
        $blood_group_list = EmployeeBloodGroup::all();
        $admin_profile = Admin::join('employees as e','admins.trax_id', '=', 'e.trax_id')
            ->leftjoin('employee_blood_groups as bg', 'bg.id', '=', 'e.blood_group')
            ->select('e.id as employee_id', 'bg.name as blood_group_name', 'bg.id as blood_group_id', 'e.emergency_contact as emergency_contact_no', 'e.emergency_contact_person as emergency_contact_person')
            ->where('admins.id', $admin_id);
        if ($admin_profile->exists()) {
            $admin_profile = $admin_profile->first();
            return response()->json(['status' => 0, 'blood_group_list' => $blood_group_list, 'blood_group_id' => $admin_profile->blood_group_id, 'blood_group_name' => $admin_profile->blood_group_name, 'employee_id' => $admin_profile->employee_id, 'emergency_contact_no' => $admin_profile->emergency_contact_no, 'emergency_contact_person' => $admin_profile->emergency_contact_person]);
        } else {
            return response()->json(['status' => 1, 'message' => "Admin Profile Not Found"]);
        }
    }

    public function update_profile(Request $request)
    {
        $rules = [
            'employee_id' => ['required', 'integer', 'digits_between:1,10', 'exists:employees,id'],
            'blood_group_id' => ['required', 'integer', 'digits_between:1,10', 'exists:employee_blood_groups,id'],
            'emergency_contact_no' => ['required', 'regex:/^[0][0-9]{3}-[0-9]{7}$/'],
            'emergency_contact_person' => ['required'],
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $employee = Employee::find($request->employee_id);
            if ($employee) {
                $employee->blood_group = $request->blood_group_id;
                $employee->emergency_contact = $request->emergency_contact_no;
                $employee->emergency_contact_person = $request->emergency_contact_person;
                $employee->save();
                return response()->json(['status' => 0, 'message' => "Profile update successfully"]);
            } else {
                return response()->json(['status' => 1, 'message' => 'User not found!']);
            }
        }
    }

    public function check_pin(Request $request){
        $admin_id = $request->admin_id;
        $admin = Admin::find($admin_id);
        if($admin){
            if($admin->reset_pin_status == 1){
                return response()->json(['status' => 0, 'pin_status' => 1]);
            }
            return response()->json(['status' => 0, 'pin_status' => 0]);
        }
        return response()->json(['status' => 0, 'pin_status' => 0]);
    }

    public function logout(Request $request){
        $admin_id = $request->admin_id;
        $admin = Admin::find($admin_id);
        if($admin){
            if($admin->reset_pin_status == 1){
                $admin->reset_pin_status = 0;
                $admin->save();
                return response()->json(['status' => 0, 'message' => "Logout Successfully"]);
            }
            return response()->json(['status' => 0, 'message' => "Logout Successfully"]);
        }
        return response()->json(['status' => 0, 'message' => "Logout Successfully"]);
    }

    public function leads_list(Request $request){
        $admin_id = $request->admin_id;

        $lead_statuses = LeadStatus::whereNotIn('id', [3, 11, 12])->select('id', 'name')->get();
        $cities = City::where('business_category_id', 1)->where('status', 1)->get();

        $leads = Lead::join('cities as c', 'c.id', '=', 'leads.city_id')
            ->leftjoin('lead_statuses as ls', 'ls.id', '=', 'leads.status_id')
            ->leftjoin('service_list as sl', 'sl.id', '=', 'leads.service_id')
            ->select('leads.id as lead_id','leads.contact_person as contact_person', 'leads.phone_number as phone_number', 'leads.email_address as email_address', 'leads.requested_date as requested_date', 'leads.message as message', 'leads.status_id', 'ls.name as status', 'c.name as city', 'sl.name as service','leads.brand as brand')
            ->where('leads.sale_person_id', $admin_id)
            ->wherenotin('leads.status_id', [3, 11, 12]);

        if($request->city_id){
            $leads = $leads->where('leads.city_id', $request->city_id);
        }
        if($request->status_id){
            $leads = $leads->where('leads.status_id', $request->status_id);
        }
        if($request->date_from){
            if($request->date_to){
                $from = $request->date_from.' 00:00:00';
                $to = $request->date_to.' 23:59:59';
                $leads = $leads->whereBetween('leads.requested_date', [$from, $to]);
            }
            else{
                $leads = $leads->whereDate('leads.requested_date', $request->date_from);
            }
        }
        if($leads->exists()){
            $leads->orderBy('leads.requested_date', "DESC");
            $leads = $leads->get();
            return response()->json(['status' => 0, 'data' => $leads, 'cities' => $cities, 'lead_status' => $lead_statuses]);
        }else{
            return response()->json(['status' => 0, 'message' => "No data found!", 'cities' => $cities, 'lead_status' => $lead_statuses]);
        }
    }

    public function add_remarks(Request $request){
        $rules = [
            'remarks' => ['required', 'max:500'],
            'lead_id' => ['required', 'integer', 'digits_between:1,10', 'exists:leads,id'],
        ];

        $admin_id = $request->admin_id;
        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $lead_id = $request->lead_id;
            $lead = Lead::find($lead_id);
            $remarks = $request->remarks;
            if($remarks != NULL){
                $lead_remarks = new LeadRemark();
                $lead_remarks->lead_id = $lead->id;
                $lead_remarks->remarks = $remarks;
                $lead_remarks->updated_by = $admin_id;
                $lead_remarks->save();
                return response()->json(['status' => 0, 'message' => 'Remarks added Successfully!']);
            }
            else{
                return response()->json(['status' => 1, 'message' => 'Invalid Remarks!']);
            }
        }
    }

    public function view_remarks(Request $request){
        $rules = [
            'lead_id' => ['required', 'integer', 'digits_between:1,10', 'exists:leads,id'],
        ];
        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $lead_id = $request->lead_id;
            $lead_remarks = LeadRemark::join('admins as a', 'a.id', '=', 'lead_remarks.updated_by')
                ->select('lead_remarks.id as id', 'lead_remarks.remarks as remarks', 'lead_remarks.created_at as created_at', 'a.name as updated_by')
                ->where('lead_id', $lead_id)
                ->orderBy('lead_remarks.created_at', 'ASC');
            if($lead_remarks->exists()){
                $lead_remarks = $lead_remarks->get();
                return response()->json(['status' => 0, 'data' => $lead_remarks]);
            }
            return response()->json(['status' => 1, 'message' => 'No Remarks Found!']);
        }
    }

    public function trax_directory_v2(Request $request)
    {
        $rules = [
            'search_param' => ['required', 'min:3'],
            'search_with' => ['required'],
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            if ($request->search_with == 1) {

                $admin_profile = Admin::join('employees as e', 'admins.trax_id', '=', 'e.trax_id')
                    ->join('employee_designations as d', 'd.id', '=', 'admins.designation_id')
                    ->join('admin_departments as ad', 'd.department_id', '=', 'ad.id')
                    ->join('cities as c', 'c.id', '=', 'e.city_id')
                    ->leftjoin('employee_blood_groups as bg', 'bg.id', '=', 'e.blood_group')
                    ->select('e.trax_id as trax_id', 'e.name as name', 'e.official_email as email', 'e.phone_number as phone', 'd.name as designation', 'ad.name as department_name', 'bg.name as blood_group', 'e.emergency_contact as emergency_contact_no', 'e.emergency_contact_person as emergency_contact_person', 'c.name as city','e.official_phone_number as official_phone_number')
                    ->where('e.name', 'like','%' . $request->search_param . '%')
                    ->where('admins.status', 1);

            } elseif ($request->search_with == 2) {
                $admin_profile = Admin::join('employees as e', 'admins.trax_id', '=', 'e.trax_id')
                    ->join('employee_designations as d', 'd.id', '=', 'admins.designation_id')
                    ->join('admin_departments as ad', 'd.department_id', '=', 'ad.id')
                    ->join('cities as c', 'c.id', '=', 'e.city_id')
                    ->leftjoin('employee_blood_groups as bg', 'bg.id', '=', 'e.blood_group')
                    ->select('e.trax_id as trax_id', 'e.name as name', 'e.official_email as email', 'e.phone_number as phone', 'd.name as designation', 'ad.name as department_name', 'bg.name as blood_group', 'e.emergency_contact as emergency_contact_no', 'e.emergency_contact_person as emergency_contact_person', 'c.name as city','e.official_phone_number as official_phone_number')
                    ->where('e.phone_number', substr_replace($request->input('search_param'), '-', 4, 0))
                    ->where('admins.status', 1);
            } elseif ($request->search_with == 3) {
                $admin_profile = Admin::join('employees as e', 'admins.trax_id', '=', 'e.trax_id')
                    ->join('employee_designations as d', 'd.id', '=', 'admins.designation_id')
                    ->join('admin_departments as ad', 'd.department_id', '=', 'ad.id')
                    ->join('cities as c', 'c.id', '=', 'e.city_id')
                    ->leftjoin('employee_blood_groups as bg', 'bg.id', '=', 'e.blood_group')
                    ->select('e.trax_id as trax_id', 'e.name as name', 'e.official_email as email', 'e.phone_number as phone', 'd.name as designation', 'ad.name as department_name', 'bg.name as blood_group', 'e.emergency_contact as emergency_contact_no', 'e.emergency_contact_person as emergency_contact_person', 'c.name as city','e.official_phone_number as official_phone_number')
                    ->where('e.trax_id', $request->search_param)
                    ->where('admins.status', 1);
            } else {
                return response()->json(['status' => 1, 'message' => 'Provide atleast one parameter']);
            }
            if ($admin_profile->exists()) {
                $admin_profile = $admin_profile->get();
                return response()->json(['status' => 0, 'data' => $admin_profile]);
            } else {
                return response()->json(['status' => 1, 'message' => 'No User found!']);
            }
        }
    }


}
