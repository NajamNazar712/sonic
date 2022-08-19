<?php

namespace App\Http\Controllers;

use App\DailyVisit;
use App\Http\Controllers\Admins\AdminPickupsController;
use App\Http\Controllers\Admins\CheckDisputeShipmentsController;
use App\Http\Controllers\Admins\DisputeController;
use App\Http\Controllers\Admins\DwsWeightChargesController;
use App\Http\Controllers\Admins\LeadTaggingController;
use App\Http\Controllers\Admins\ShipmentChargesController;
use App\Http\Controllers\Retail\RetailShipmentBookController;
use App\Http\Controllers\Webhook\InitialChargesWebhookController;
use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\AdminAppSlider;
use App\Http\Models\Admin\AdminDepartment;
use App\Http\Models\Admin\AdminHub;
use App\Http\Models\Admin\AdminRoleModulePermission;
use App\Http\Models\Admin\AdminUserRequest;
use App\Http\Models\Admin\Attendance\EmployeeAttendance;
use App\Http\Models\Admin\Attendance\EmployeeAttendanceActionLog;
use App\Http\Models\Admin\BookingSmsForShippers;
use App\Http\Models\Admin\ByPassWeightShippers;
use App\Http\Models\Admin\CancelledShipmentArrival;
use App\Http\Models\Admin\CargoManifest\CargoManifest;
use App\Http\Models\Admin\CargoManifest\CargoManifestBag;
use App\Http\Models\Admin\CargoManifest\CargoManifestBagShipments;
use App\Http\Models\Admin\CargoManifest\ManifestBag;
use App\Http\Models\Admin\CargoManifest\V2Junctions;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Admin\Lead\Lead;
use App\Http\Models\Admin\Lead\LeadLog;
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
use App\Http\Models\Admin\ReturnNoteShipment;
use App\http\Models\Admin\ReturnReasonMandatoryShipper;
use App\Http\Models\Admin\RiderType;
use App\Http\Models\Admin\ShipmentsEstimatedWeight;
use App\Http\Models\AppNotification;
use App\Http\Models\BanksList;
use App\Http\Models\BusinessCategory;
use App\Http\Models\City;
use App\Http\Models\CityDelivery;
use App\Http\Models\ConsolidationShipments;
use App\Http\Models\CRM\CrmRequest;
use App\Http\Models\DailyVisitLeadStatus;
use App\Http\Models\DwsDetail;
use App\Http\Models\DwsWeightCharges;
use App\Http\Models\EmployeeDeviceToken;
use App\Http\Models\EmployeeNotificationHistory;
use App\Http\Models\EmployeeShift;
use App\Http\Models\HR\Employee;
use App\Http\Models\HR\EmployeeAttachment;
use App\Http\Models\HR\EmployeeAttendanceAdjustment;
use App\Http\Models\HR\EmployeeBankInformation;
use App\Http\Models\HR\EmployeeBloodGroup;
use App\Http\Models\HR\EmployeeDesignation;
use App\Http\Models\HR\EmployeeDomicile;
use App\Http\Models\HR\EmployeeEducationalBackground;
use App\Http\Models\HR\EmployeeEmployementHistory;
use App\Http\Models\HR\EmployeeGender;
use App\Http\Models\HR\EmployeeLeave;
use App\Http\Models\HR\EmployeeMaritalStatus;
use App\Http\Models\HR\EmployeeMedicalInformation;
use App\Http\Models\HR\EmployeeNationality;
use App\Http\Models\HR\EmployeePayslip;
use App\Http\Models\HR\EmployeeRelationship;
use App\Http\Models\HR\EmployeeReligion;
use App\Http\Models\HR\LeaveType;
use App\Http\Models\HR\StaffCategory;
use App\Http\Models\InternationalShipment;
use App\Http\Models\PayslipPdf;
use App\Http\Models\PendingPayment;
use App\Http\Models\PendingPaymentShipment;
use App\Http\Models\Product;
use App\Http\Models\ReceivingSheetReceived;
use App\Http\Models\ReportingLocation;
use App\Http\Models\Rider;
use App\Http\Models\RiderCategory;
use App\Http\Models\Route;
use App\Http\Models\SelfCollectionShipment;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentDetail;
use App\Http\Models\ShipmentPiece;
use App\Http\Models\ShipmentPiecesRequest;
use App\Http\Models\ShipmentsJourney;
use App\Http\Models\ShipmentStatus;
use App\Http\Models\Shipper\ReturnSheet;
use App\Http\Models\Shipper\User;
use App\Http\Models\Shipper\UserShippingInfo;
use App\Http\Models\V2Pickup\DwsPickupNote;
use App\Http\Models\V2Pickup\V2PickupNote;
use App\Http\Models\V2Pickup\V2PickupNoteRequest;
use App\Http\Models\V2Pickup\V2PickupReceivedShipment;
use App\Http\Models\V2Pickup\V2PickupRequest;
use App\Http\Models\V2Pickup\V2PickupRequestShipment;
use App\Http\Models\WMS\WmsCourierOrders;
use App\Http\Models\WMS\WmsOrderProcess;
use App\Http\Models\WMS\WmsPendingPicking;
use App\Http\Models\WMS\WmsPicklist;
use App\Http\Models\WMS\WmsPicklistItem;
use App\Http\Models\WMS\WmsProductBarcode;
use App\Http\Models\Zone;
use App\Models\Admin\Lead\LeadReason;
use App\RiderMainCategory;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Password;

class AdminAPIController extends Controller
{
    use SendsPasswordResetEmails;

    public function broker()
    {
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
        for ($date = $start_date->copy(); $date->lte($end_date); $date->addDay()) {
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
                if ($user->status == 0) {
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
                        $information['address'] = ($employee->address) ? $employee->address : "";
                    } else {
                        $information['address'] = '';
                    }
                    $information['role'] = 'staff';

                    if ($request->has('device_token')) {
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
                    } else {
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

    public function return_note_details(Request $request)
    {
        $admin_id = $request->admin_id;
        $return_note_id = $request->return_note_id;
        if ($return_note_id) {
            $return_note = ReturnNote::find($return_note_id);
            if ($return_note) {
                if ($return_note->status == 1 || $return_note->status == 3) {
                    if ($return_note->image !== null) {
                        $details = array();
                        $img_url = '';
                        $url = 'uploads/return_notes/' . $return_note->image;
                        if (file_exists($url)) {
                            $img_url = asset('uploads/return_notes/' . $return_note->image);
                        } else {
                            $exists = Storage::disk('s3')->exists('return_note_images/' . $return_note->image);
                            if ($exists) {
                                $img_url = Storage::disk('s3')->temporaryUrl('return_note_images/' . $return_note->image, now()->addMinutes(5));
                            }
                        }
                        $details['return_note_id'] = $return_note_id;
                        $details['images'] = array('id' => 0, 'image' => $img_url);
                        return response()->json(['status' => 0, 'message' => 'Images found', 'information' => $details]);
                    } else {
                        $return_note_images = ReturnNoteImage::where('return_note_id', $return_note_id);
                        if ($return_note_images->exists()) {
                            $return_note_images = $return_note_images->get();
                            $details = array();
                            foreach ($return_note_images as $return_note_image) {
                                $img_url = '';
                                $url = 'uploads/return_notes/' . $return_note_image->image;
                                if (file_exists($url)) {
                                    $img_url = asset('uploads/return_notes/' . $return_note_image->image);
                                } else {
                                    $exists = Storage::disk('public')->exists('uploads/return_notes/' . $return_note_image->image);
                                    if ($exists) {
                                        $img_url = asset('storage/uploads/return_notes/' . $return_note_image->image);
                                    } else {
                                        $exists = Storage::disk('s3')->exists('return_note_images/' . $return_note_image->image);
                                        if ($exists) {
                                            $img_url = Storage::disk('s3')->temporaryUrl('return_note_images/' . $return_note_image->image, now()->addMinutes(5));
                                        }
                                    }
                                }
                                $details['images'][] = array('id' => $return_note_image->id, 'image' => $img_url);
                            }
                            return response()->json(['status' => 0, 'message' => 'Images found', 'information' => $details]);
                        } else {
                            return response()->json(['status' => 0, 'message' => 'No images found!', 'information' => '']);
                        }
                    }
                } else {
                    return response()->json(['status' => 1, 'message' => 'Return Note not ready for image upload!']);
                }
            } else {
                return response()->json(['status' => 1, 'message' => 'Return Note Image not found!']);
            }
        } else {
            return response()->json(['status' => 1, 'message' => 'No return note scanned!']);
        }
    }

    public function history_update_image(Request $request)
    {
        $rules = [
            'added_at' => ['required'],
            'return_note_id' => ['required', 'integer', 'digits_between:1,10', 'exists:return_notes,id'],
            'pictures' => ['array', 'nullable'],
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
        $old_image_ids = ($request->old_image_ids != '') ? $request->old_image_ids : [];
        $return_note_id = $request->return_note_id;
        $return_note = ReturnNote::find($return_note_id);
        if ($return_note) {
            $present = false;

            $pictures = array();
            if ($request->has('pictures')) {
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
                            } else {
                                $exists = Storage::disk('public')->exists('uploads/return_notes/' . $return_note_image->image);
                                if ($exists) {
                                    Storage::disk('public')->delete('uploads/return_notes/' . $return_note_image->image);
                                } else {
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
        $employee_id = $request->admin_employee;
        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {

            $location_status = 0;
            $reporting_location = ReportingLocation::join('employees as e', 'reporting_locations.id', 'e.reporting_location_id')
                ->where('e.id', $employee_id);
            if ($reporting_location->exists()) {
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

            if ($request->has('attachment_update')) {
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
            } else {
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

    public function retail_index(Request $request)
    {
        $admin_default_hub = Admin::where('id', $request->admin_id)->select('default_hub_id')->first();
        $retail_trax_centers = RetailTraxCenter::where('default_hub', $admin_default_hub->default_hub_id)->where('status', 1)->select('name', 'code', 'pickup_address_id')->get();
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

    public function retail_bank_info(Request $request)
    {
        $shipper_info = RetailShipperInfo::where('shipper_phone_no', $request->shipper_phone_no)
            ->select('iban', 'account_number', 'cheque_image', 'bank_id');
        $shipment_count = RetailShipment::where('shipper_phone_no', $request->shipper_phone_no)->where('shipping_mode', 3)->count();
        if ($shipper_info->exists()) {
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
            if ($charges_mode_id == 2) {
                $amount = $amount + $total_charges;
            }
        } else {
            $amount = 0;
            if ($charges_mode_id == 2) {
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

        if ($business_category_id == 2) {
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
        if ($cash_deposit->exists()) {
            $cash_deposit = $cash_deposit->first();
            $total_shipments = $cash_deposit->total_cn + 1;
            $total_cash = $cash_deposit->total_cash + $total_charges;
            $cash_deposit->total_cn = $total_shipments;
            $cash_deposit->total_cash = $total_cash;
            $cash_deposit->save();
        } else {
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
            if (!empty($data)) {
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
            ->whereIn('status_id', [2, 4, 6, 8, 9, 10]);

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
                    foreach ($admin_hubs as $admin_hub) {
                        if (in_array($admin_hub, $junctions)) {
                            $datum["misroute"] = 0;
                        }
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
        $employee_id = $request->admin_employee;
        $admins = Admin::find($admin_id);
        if ($admins) {
            $response = array();
            $admin_shift = EmployeeShift::where('id', $admins->shift_id);
            if ($admin_shift->exists()) {
                $admin_shift = $admin_shift->first();
                $shift_time = Carbon::createFromFormat('H:i:s', $admin_shift->start_time);
                if ($shift_time->lt(Carbon::now())) {
                    $date = Carbon::now()->format("Y-m-d");
                } else {
                    if (Carbon::now()->format("l") == "Monday") {
                        $date = Carbon::now()->subDays(2)->format("Y-m-d");
                    } else {
                        $date = Carbon::now()->subDays(1)->format("Y-m-d");
                    }
                    $last_action_log = EmployeeAttendanceActionLog::where('employee_id', $employee_id)
                        ->where('employee_type', 1)->whereDate('attendance_date', $date)->orderBy('id', 'DESC');
                    if ($last_action_log->exists()) {
                        $last_action_log = $last_action_log->first();
                        if ($last_action_log->action_id == 2) {
                            $date = Carbon::now()->format("Y-m-d");
                        }
                    }

                }
            }
            $response["status"] = 0;
            if ($admin_shift->exists()) {
                $admin_shift = $admin_shift->first();
                $response["shift_name"] = $admin_shift->name;
                $response["start_time"] = $admin_shift->start_time;
                $response["end_time"] = $admin_shift->end_time;
            } else {
                $response["shift_name"] = "default";
                $response["start_time"] = NULL;
                $response["end_time"] = NULL;
            }
            $response["last_action_date"] = $date;
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
        $employee_id = $request->admin_employee;
        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $admin_attendance_action = EmployeeAttendanceActionLog::where('employee_id', $employee_id)
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
        $employee_id = $request->admin_employee;
        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {

            $location_status = $this->calculate_location_status($request->latitude, $request->longitude);
            $attendance_date = Carbon::parse($request->attendance_date)->format('Y-m-d');
            $action_date = Carbon::createFromFormat('Y-m-d H:i:s', $request->action_date);

            $admin_attendance = EmployeeAttendance::where('employee_id', $employee_id)
                ->whereDate('attendance_date', $attendance_date)
                ->where('employee_type', 1);
            $admin_attendance_action = new EmployeeAttendanceActionLog();
            if ($admin_attendance->exists()) {
                $admin_attendance = $admin_attendance->first();
            } else {
                $admin_attendance = new EmployeeAttendance();
                $admin_attendance->employee_id = $employee_id;
                $admin_attendance->employee_type = 1;
                $admin_attendance->attendance_date = $attendance_date;
            }
            if ($request->action == 1) {
                $admin_attendance->clock_in_datetime = $action_date;
                $admin_attendance->clock_in_latitude = $request->latitude;
                $admin_attendance->clock_in_longitude = $request->longitude;
                $admin_attendance->clock_in_location = $location_status;
                $admin_attendance->save();

                $admin_attendance_action->employee_id = $employee_id;
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

                $admin_attendance_action->employee_id = $employee_id;
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
                if ($admin->status == 0) {
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
            if ($reporting_location->exists()) {
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
            if ($admin_attendance_action->exists()) {
                $admin_attendance_action = $admin_attendance_action->first();
                $last_action = Carbon::parse($admin_attendance_action->action_date);
                if (Carbon::now()->diffInSeconds($last_action) < 15) {
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
                $payslip_pdf = PayslipPdf::where('payslip_id', $payslip->id);
                if ($payslip_pdf->exists()) {
                    $payslip_pdf = $payslip_pdf->first();
                    $file_url = $payslip_pdf->file_path;
                } else {
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

                    $filename = 'payslip_' . $payslip->id . Carbon::now()->format('Uu') . '-' . $payroll_month . '.pdf';
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
                $employee = Employee::find($leave->employee_id);
                if ($employee) {
                    $data['trax_id'] = $employee->trax_id;
                    $data['name'] = $employee->name;
                    if ($leave->employee_type_id == 1) {
                        $data['designation'] = $employee->designation->name;
                        $data['department'] = $employee->department->name;

                        $admin = Admin::where('trax_id', $employee->trax_id)->whereNotNull('trax_id')->first();
                        $role_id = $admin->role_id;
                        if ($role_id == 81) {
                            $data['user_type'] = 2;
                        } elseif (in_array($role_id, [1, 2, 3, 4, 5, 6, 35, 52, 58, 70, 63])) {
                            $data['user_type'] = 1;
                        } else {
                            $data['user_type'] = 0;
                        }
                    } else if ($leave->employee_type_id == 2) {
                        $data['designation'] = "Rider";
                        $data['department'] = "Operations";
                        $data['user_type'] = 0;
                    } else {
                        return response()->json(['status' => 1, 'message' => "Failed"]);
                    }
                    $data['approver_email'] = $employee->line_manager->email;
                    $data['approver_name'] = $employee->line_manager->name;
                    return response()->json(['status' => 0, 'data' => $data]);
                } else {
                    return response()->json(['status' => 1, 'message' => "User not found"]);
                }
            }
        } else {
            $employee_id = $request->admin_employee;
            $employee = Employee::find($employee_id);
            if ($employee) {
                if ($employee->line_manager_id == null || $employee->line_manager_id == 0) {
                    return response()->json(['status' => 1, 'message' => "Line Manager is not selected!"]);
                }
                $data = array();
                $data['trax_id'] = $employee->trax_id;
                $data['name'] = $employee->name;
                $data['designation'] = $employee->designation->name;
                $data['department'] = $employee->department->name;
                $data['approver_email'] = $employee->line_manager->email;
                $data['approver_name'] = $employee->line_manager->name;
                $role_id = $request->admin_role_id;
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
        $employee_id = $request->admin_employee;
        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $employee = Employee::find($employee_id);
            if ($employee) {
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
                    $leave = EmployeeLeave::where('employee_id', $employee_id)->where('employee_type_id', 1)->whereIn('status', [1, 2]);
                    if ($leave->exists()) {
                        return response()->json(['status' => 1, 'message' => 'Leave Request Already Submitted & Pending for Approval']);
                    }
                    $leave_request = new EmployeeLeave();
                    $leave_request->employee_id = $employee_id;
                    $leave_request->employee_type_id = 1;
                    if (in_array($request->admin_role_id, [1, 2, 3, 4, 5, 6, 35, 52, 58, 70])) {
                        $reporter_id = 139;
                    } else {
                        $reporter_id = $employee->line_manager_id;
                    }
                    $leave_request->reporter_id = $reporter_id;
                    $leave_request->from = $request->from;
                    $leave_request->to = $request->to;
                    $leave_request->applied_reason = $request->reason;
                    $leave_request->save();
                    $message = "Leave Request submitted successfully";
                    $reporter = Employee::find($leave_request->reporter_id);
                    $reporter = Admin::where('trax_id', $reporter->trax_id)->whereNotNull('trax_id')->first();
                    NotificationsController::app_notification(11, $admin_id, 1, $leave_request->id);
                    NotificationsController::app_notification(12, $reporter->id, 1, $leave_request->id);
                }
                return response()->json(['status' => 0, 'apply_message' => $message]);
            } else {
                return response()->json(['status' => 1, 'message' => 'User Not Found']);
            }
        }

    }

    public function employee_leave_list(Request $request)
    {
        $employee_id = $request->admin_employee;
        $employee_leaves = EmployeeLeave::join('leave_statuses as ls', 'employee_leaves.status', '=', 'ls.id')
            ->select('employee_leaves.id as id', 'employee_leaves.from as from', 'employee_leaves.to as to', 'employee_leaves.applied_reason as applied_reason', 'employee_leaves.rejected_reason as rejected_reason', 'employee_leaves.status as status_id', 'ls.name as status')
            ->where('employee_id', $employee_id)
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
                if ($employee_leave->to) {
                    $start_date = Carbon::createFromFormat('Y-m-d', $employee_leave->from);
                    $end_date = Carbon::createFromFormat('Y-m-d', $employee_leave->to);
                    $datum['days_count'] = $start_date->diffInDays($end_date) + 1;
                } else {
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
        $employee_id = $request->admin_employee;
        $admin = Employee::find($employee_id);
        if ($admin) {
            $admin_role = $request->admin_role_id;
            if ($admin_role == 63) {
                $employee_leaves = EmployeeLeave::join('leave_statuses as ls', 'employee_leaves.status', '=', 'ls.id')
                    ->select('employee_leaves.id as id', 'employee_leaves.from as from', 'employee_leaves.to as to', 'employee_leaves.applied_reason as applied_reason', 'employee_leaves.status as status_id', 'ls.name as status', 'employee_leaves.employee_id as employee_id', 'employee_leaves.employee_type_id as type_id', 'employee_leaves.rejected_reason as rejected_reason')
                    ->where('employee_leaves.status', 2);
            } elseif (in_array($admin_role, [1, 2, 3, 4, 5, 6, 35, 52, 58, 70, 81])) {
                $employee_leaves = EmployeeLeave::join('leave_statuses as ls', 'employee_leaves.status', '=', 'ls.id')
                    ->select('employee_leaves.id as id', 'employee_leaves.from as from', 'employee_leaves.to as to', 'employee_leaves.applied_reason as applied_reason', 'employee_leaves.status as status_id', 'ls.name as status', 'employee_leaves.employee_id as employee_id', 'employee_leaves.employee_type_id as type_id', 'employee_leaves.rejected_reason as rejected_reason')
                    ->where('employee_leaves.reporter_id', $employee_id);
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
                    if ($admin_role == 63) {
                        $datum['role'] = 0;
                    } else {
                        $datum['role'] = 1;
                    }
                    if ($employee_leave->to) {
                        $start_date = Carbon::createFromFormat('Y-m-d', $employee_leave->from);
                        $end_date = Carbon::createFromFormat('Y-m-d', $employee_leave->to);
                        $datum['days_count'] = $start_date->diffInDays($end_date) + 1;
                    } else {
                        $datum['days_count'] = 1;
                    }

                    $user = Employee::find($employee_leave->employee_id);
                    if ($user) {
                        $datum['name'] = $user->name;
                        $datum['trax_id'] = $user->trax_id;
                        if ($employee_leave->type_id == 1) {
                            $datum['designation'] = $user->designation->name;
                        } elseif ($employee_leave->type_id == 2) {
                            $datum['designation'] = "Rider";
                        }

                        $data[] = $datum;
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
                        $user = Employee::find($employee_leaves->employee_id);
                        if (!$user) {
                            return response()->json(['status' => 1, 'message' => "Invalid Employee ID"]);
                        }
                        $shift = EmployeeShift::find($user->shift_id);
                        if ($employee_leaves->to) {
                            $dates = $this->generateDateRange($employee_leaves->from, $employee_leaves->to);
                        } else {
                            $dates[] = $employee_leaves->from;
                        }
                        foreach ($dates as $date) {
                            $date = Carbon::parse($date)->format("Y-m-d");
                            $mark_attendance = EmployeeAttendance::where('employee_id', $employee_leaves->employee_id)
                                ->where('employee_type', $employee_leaves->employee_type_id)
                                ->whereDate('attendance_date', $date);
                            if ($mark_attendance->exists()) {
                                $mark_attendance = $mark_attendance->first();
                            } else {
                                $mark_attendance = new EmployeeAttendance();
                            }
                            $mark_attendance->attendance_date = $date;
                            $mark_attendance->employee_id = $employee_leaves->employee_id;
                            $mark_attendance->employee_type = $employee_leaves->employee_type_id;
                            $mark_attendance->clock_in_latitude = "24.85758065592256";
                            $mark_attendance->clock_in_longitude = "67.12476908400743";
                            $mark_attendance->clock_out_latitude = "24.85758065592256";
                            $mark_attendance->clock_out_longitude = "67.12476908400743";
                            if ($shift) {
                                $mark_attendance->clock_in_datetime = $date . ' ' . $shift->start_time;
                                $mark_attendance->clock_out_datetime = $date . ' ' . $shift->end_time;
                            } else {
                                $mark_attendance->clock_in_datetime = $date . ' 09:00:00';
                                $mark_attendance->clock_out_datetime = $date . ' 18:00:00';
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
                        if ($hr_admins->exists()) {
                            $hr_admins = $hr_admins->get();
                            foreach ($hr_admins as $hr_admin) {
                                NotificationsController::app_notification(13, $hr_admin->id, 1, $employee_leaves->id);
                            }
                        }
                    } else {
                        return response()->json(['status' => 1, 'message' => "Invalid Role"]);
                    }
                    $employee_leaves->save();
                    $notify = false;
                    if ($employee_leaves->employee_type_id == 1) {
                        $admin = Admin::where('employee_id', $employee_leaves->employee_id);
                        if ($admin->exists()) {
                            $admin = $admin->first();
                            $user_id = $admin->id;
                            $notify = true;
                        }
                    } else if ($employee_leaves->employee_type_id == 2) {
                        $rider = Rider::where('employee_id', $employee_leaves->employee_id);
                        if ($rider->exists()) {
                            $rider = $rider->first();
                            $user_id = $rider->id;
                            $notify = true;
                        }
                    }
                    if ($notify) {
                        NotificationsController::app_notification(11, $user_id, $employee_leaves->employee_type_id, $employee_leaves->id);
                    }
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
                    $notify = false;
                    if ($employee_leaves->employee_type_id == 1) {
                        $admin = Admin::where('employee_id', $employee_leaves->employee_id);
                        if ($admin->exists()) {
                            $admin = $admin->first();
                            $user_id = $admin->id;
                            $notify = true;
                        }
                    } else if ($employee_leaves->employee_type_id == 2) {
                        $rider = Rider::where('employee_id', $employee_leaves->employee_id);
                        if ($rider->exists()) {
                            $rider = $rider->first();
                            $user_id = $rider->id;
                            $notify = true;
                        }
                    }
                    if ($notify) {
                        NotificationsController::app_notification(11, $user_id, $employee_leaves->employee_type_id, $employee_leaves->id);
                    }
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

                $shipment = $shipment->first();
                $shipment_id = $shipment->id;

                //todo: now checking canceled shipment arrival
                $user = ShipmentsJourney::where('shipment_id',$shipment->id)->select('user_id','shipper_status_id')->orderby('id','desc')->first();
                if($user)
                {
                    if($user->shipper_status_id == 17)
                    {
                        $canceled_shipment = CancelledShipmentArrival::where('shipper_id',$user->user_id)->first();
                        if($canceled_shipment)
                        {
                            return response()->json(false);
                        }
                    }
                }
                //todo: now checking canceled shipment arrival end

                $dispute_check = CheckDisputeShipmentsController::check($shipment_id);
                if (!$dispute_check) {
                    return response()->json(false);
                }
                // arrive function
                // if ($shipment->actual_weight == null) {
                //     return response()->json(['status' => 1, 'message' => 'weight not found']);
                // }

                if ($shipment->warehouse == 1) {
                    if ($shipment->warehouse_order_status != 5) {
                        return response()->json(['status' => 1, 'message' => 'Shipment is not dispatched yet']);
                    }
                }


                if (($shipment->shipper_status_id == 1 || $shipment->shipper_status_id == 17 || $shipment->shipper_status_id == 53 || $shipment->shipper_status_id == 61 || $shipment->shipper_status_id == 62 || $shipment->shipper_status_id == 63) && ($shipment->booking_type_id != 3 && $shipment->pieces == 1)) {
                    if ($request->dimension_l < 0 || $request->dimension_w < 0 || $request->dimension_h < 0) {
                        return response()->json(false);

                    }
                    $volume_weight = (($request->dimension_l * $request->dimension_w * $request->dimension_h) / 5000);
                    if ($volume_weight < 0.01) {
                        $volume_weight = 0.01;
                    }
                    $dense_weight = $request->weight;
                    if ($dense_weight < 0.01) {
                        $dense_weight = 0.01;
                    }
                    $retail_shipment = RetailShipment::where('shipment_id', $shipment->id);
                    if ($retail_shipment->exists()) {
//                        if ($dense_weight < $volume_weight) {
//                            $actual_weight = $volume_weight;
//                        } else {
//                            $actual_weight = $dense_weight;
//                        }
                        $shipment->actual_weight = $shipment->estimated_weight;
                        $shipment->save();
                        $retail_flag = true;
                        $dws_charges_status = 1;
                    } else {
                        $retail_flag = false;
                    }

                    if ($retail_flag == false) {
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

                                $not_include_shippers1 = ByPassWeightShippers::all()->pluck('shipper_id')->toArray();
                                $not_include_shippers = [6693, 12412];
                                $not_include_shippers = array_merge($not_include_shippers, $not_include_shippers1);

                                if (!in_array($shipment->user_id, $not_include_shippers)) {
                                    $estimate_actual_difference = $shipment->estimated_weight - $actual_weight;

                                    if ($shipment->estimated_weight != 1 && $estimate_actual_difference > 0 && $estimate_actual_difference < 5) {

                                        $shipment_estimated_weight = ShipmentsEstimatedWeight::where('shipment_id', $shipment->id);
                                        if ($shipment_estimated_weight->exists()) {
                                            $shipment_estimated_weight = $shipment_estimated_weight->first();
                                        } else {
                                            $shipment_estimated_weight = new ShipmentsEstimatedWeight();
                                        }
                                        $shipment_estimated_weight->shipment_id = $shipment->id;
                                        $shipment_estimated_weight->estimated_weight = $shipment->estimated_weight;
                                        $shipment_estimated_weight->actual_weight = $actual_weight;
                                        if ($volume_weight) {
                                            $shipment_estimated_weight->length = $request->dimension_l;
                                            $shipment_estimated_weight->breadth = $request->dimension_w;
                                            $shipment_estimated_weight->height = $request->dimension_h;
                                        } else {
                                            $shipment_estimated_weight->length = null;
                                            $shipment_estimated_weight->breadth = null;
                                            $shipment_estimated_weight->height = null;
                                        }
                                        $shipment_estimated_weight->save();

                                        $actual_weight = $shipment->estimated_weight;

                                        $shipment->length = NULL;
                                        $shipment->breadth = NULL;
                                        $shipment->height = NULL;
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

                                $not_include_shippers1 = ByPassWeightShippers::all()->pluck('shipper_id')->toArray();
                                $not_include_shippers = [6693, 12412];
                                $not_include_shippers = array_merge($not_include_shippers, $not_include_shippers1);

                                if (!in_array($shipment->user_id, $not_include_shippers)) {
                                    $estimate_actual_difference = $shipment->estimated_weight - $actual_weight;

                                    if ($shipment->estimated_weight != 1 && $estimate_actual_difference > 0 && $estimate_actual_difference < 5) {

                                        $shipment_estimated_weight = ShipmentsEstimatedWeight::where('shipment_id', $shipment->id);
                                        if ($shipment_estimated_weight->exists()) {
                                            $shipment_estimated_weight = $shipment_estimated_weight->first();
                                        } else {
                                            $shipment_estimated_weight = new ShipmentsEstimatedWeight();
                                        }
                                        $shipment_estimated_weight->shipment_id = $shipment->id;
                                        $shipment_estimated_weight->estimated_weight = $shipment->estimated_weight;
                                        $shipment_estimated_weight->actual_weight = $actual_weight;
                                        if ($volume_weight) {
                                            $shipment_estimated_weight->length = $request->dimension_l;
                                            $shipment_estimated_weight->breadth = $request->dimension_w;
                                            $shipment_estimated_weight->height = $request->dimension_h;
                                        } else {
                                            $shipment_estimated_weight->length = null;
                                            $shipment_estimated_weight->breadth = null;
                                            $shipment_estimated_weight->height = null;
                                        }
                                        $shipment_estimated_weight->save();

                                        $actual_weight = $shipment->estimated_weight;

                                        $shipment->length = NULL;
                                        $shipment->breadth = NULL;
                                        $shipment->height = NULL;
                                    }
                                }

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
                    }

                    $pickup_request_shipment = V2PickupRequestShipment::where('shipment_id', $shipment->id)->where('status', 0);
                    $pickup_request_id = NULL;
                    $pickup_request = NULL;
                    $pickup_note_id = NULL;
                    $reference_2_id = null;
                    $rider_id = null;
                    if ($pickup_request_shipment->exists()) {
                        $pickup_request_shipment = $pickup_request_shipment->orderBy('id', 'DESC')->first();

                        $pickup_request_id = $pickup_request_shipment->pickup_request_id;
                        $pickup_request = V2PickupRequest::find($pickup_request_id);
                        $reference_1_id = $pickup_request_id;
                        $rider_id = $pickup_request->current_rider_id;

                        // if (!in_array($pickup_request_shipment->pickup_request_id, $pickup_request_ids)) {
                        // $pickup_request_ids[] = $pickup_request_shipment->pickup_request_id;

                        // }

                        $pickup_note_request = $pickup_request->pickup_note_request;
                        if ($pickup_note_request) {
                            $pickup_note_id = $pickup_note_request->pickup_note_id;
                            $reference_2_id = $pickup_note_id;
                        }
                    } else {
                        $reference_1_id = null;
                    }
                    if ($pickup_request && $pickup_request->current_rider_id == null) {
                        $rider_id = $pickup_rider_id;
                    }
                    if ($retail_flag == false) {
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
                    }

                    $shipment->shipper_status_id = 2;
                    $shipment->consignee_status_id = 2;

                    $shipment->save();

                    ShipmentsJourneyController::add($shipment_id, 2, 2, null, 'DWS Arrival', null, $request->admin_id, $reference_1_id, $reference_2_id, 1, null, $rider_id);

                    if ($retail_flag == false) {
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
                    if ($pickup_request) {
                        if ($pickup_request->received >= 1) {
                            $pickup_note_request = $pickup_request->pickup_note_request;
                            if ($pickup_note_request) {
                                $pickup_note_id = $pickup_note_request->pickup_note_id;
                                $pickup_note_request->status = 1;
                                $pickup_note_request->save();
                                //                            $pickup_note = V2PickupNote::find($pickup_note_id);

                            }

                            $retail_pickup_note = RetailPickupNote::where('pickup_request_id', $pickup_request_id)->where('status', 2);
                            if ($retail_pickup_note->exists()) {
                                $retail_pickup_note = $retail_pickup_note->first();
                                $retail_pickup_note->status = 3;
                                $retail_pickup_note->save();
                            }


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
                        $filename = 'image_' . $shipment->id . '_' . $date . '.' . $file->extension();
                        $directory = 'dws_images';
                        Storage::disk('public')->putFileAs($directory, $file, $filename);
                        $link = $directory . '/' . $filename;
                    } else {
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
                        $dws_detail = $dws_detail->first();
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
                    if ($pickup_note_id != NULL) {
                        $this->dws_pickup_note($pickup_note_id, $rider_id);
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

    public function dws_pickup_note($pickup_note_id, $rider_id)
    {
        $dws_pickup_note = DwsPickupNote::where('pickup_note_id', $pickup_note_id);
        if ($dws_pickup_note->exists()) {
            $dws_pickup_note = $dws_pickup_note->first();
            $dws_pickup_note->shipments_count = $dws_pickup_note->shipments_count + 1;
            $dws_pickup_note->save();
        } else {
            $dws_pickup_note = new DwsPickupNote();
            $dws_pickup_note->pickup_note_id = $pickup_note_id;
            $dws_pickup_note->rider_id = $rider_id;
            $dws_pickup_note->shipments_count = 1;
            $dws_pickup_note->save();
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
            $employee_id = $request->admin_employee;
            $dates = $this->generateDateRange($request->first_day, $request->last_day);
            $data = array();
            $shift = EmployeeShift::join('employees as a', 'employee_shifts.id', '=', 'a.shift_id')
                ->where('a.id', $employee_id)
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
                $attendance = EmployeeAttendance::where('employee_id', $employee_id)
                    ->where('employee_type', 1)
                    ->whereDate('attendance_date', Carbon::parse($date)->format("Y-m-d"));
                if ($attendance->exists()) {
                    $attendance = $attendance->first();
                    if ($attendance->leave_status == 2) {
                        $datum["status"] = 4;
                    } else {
                        if ($shift_exists == 1) {
                            if ($attendance->clock_in_datetime) {
                                $clock_in_date = Carbon::parse($attendance->clock_in_datetime)->format("Y-m-d");
                                $attendance_date = Carbon::parse($attendance->attendance_date)->format("Y-m-d");
                                if ($attendance_date == $clock_in_date) {
                                    $expected_clockin = Carbon::createFromFormat('Y-m-d H:i:s', $attendance->attendance_date . $shift->start_time)->addMinutes((int)$shift->grace_time);
                                    $clock_in = Carbon::parse($attendance->clock_in_datetime);
                                    $time_diff = $expected_clockin->diffInMinutes(Carbon::parse($clock_in), false);
                                    if ($time_diff > 0) {
                                        $datum["status"] = 2;//Late
                                    } else {
                                        $datum["status"] = 1;//Present
                                    }
                                } else {
                                    $datum["status"] = 2;//Late
                                }
                            } else {
                                $datum["status"] = 3;//Absent
                            }
                        } else {
                            if ($attendance->clock_in_datetime) {
                                $datum["status"] = 1;//Present
                            } else {
                                $datum["status"] = 3;//Absent
                            }
                        }
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
            $user = Admin::where('phone_number', substr_replace($request->input('phone_number'), '-', 4, 0))->orWhere('official_phone_number', substr_replace($request->input('phone_number'), '-', 4, 0));
            if ($user->exists()) {
                $user = $user->first();
                if ($user->status == 0) {
                    return response()->json(['status' => 1, 'message' => 'Account disabled, Please contact admin!']);
                }
                if (Hash::check($request->input('pin'), $user->password)) {
                    $employee = Employee::where('trax_id', $user->trax_id);
                    $information = array();

                    $information['id'] = $user->id;
                    $information['name'] = $user->name;
                    $information['phone'] = $user->phone_number;
                    $information['cnic'] = $user->cnic;
                    $information['cargo_user'] = (in_array($user->role_id, [11, 10, 15, 55, 23, 33, 46])) ? 1 : 0;
                    if ($user->designation_id) {
                        $user_department = $user->Edesignation->department_id;
                    } else {
                        $user_department = $user->role->department_id;
                    }
                    $information['sales_person'] = ($user_department == 7) ? 1 : 0;
                    if ($employee->exists()) {
                        $employee = $employee->first();
                        $information['address'] = ($employee->address) ? $employee->address : "";
                    } else {
                        $information['address'] = '';
                    }
                    $information['role'] = 'staff';
                    if ($request->has('device_token')) {
                        EmployeeDeviceToken::where('device_token', $request->get('device_token'))->delete();
                        EmployeeDeviceToken::where('employee_type_id', 1)->where('employee_id', $user->id)->delete();
                        $employee_device_token = new EmployeeDeviceToken();
                        $employee_device_token->employee_id = $user->id;
                        $employee_device_token->employee_type_id = 1;
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
                    } else {
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
            $admins = Admin::where('phone_number', substr_replace($request->input('phone_number'), '-', 4, 0))->orWhere('official_phone_number', substr_replace($request->input('phone_number'), '-', 4, 0));
            if ($admins->exists()) {
                $admins = $admins->first();
                if ($admins->status == 1) {
                    $pin = rand(100000, 999999);
                    $admins->reset_pin_otp = $pin;
                    $admins->save();
                    NotificationsController::send(162, $admins->id, $request->phone_number);
                    return response()->json(['status' => 0, 'message' => 'Otp has been sent to your phone number', 'otp' => $pin]);
                } else {
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
            $admin = Admin::where('phone_number', substr_replace($request->input('phone_number'), '-', 4, 0))->orWhere('official_phone_number', substr_replace($request->input('phone_number'), '-', 4, 0));
            if ($admin->exists()) {
                $admin = $admin->first();
                if ($request->input('otp') == $admin->reset_pin_otp) {
                    $admin->password = bcrypt($request->pin);
                    $admin->dummy_pin = $request->pin;
                    $admin->reset_pin_otp = NULL;
                    $admin->save();

                    $employee = Employee::where('trax_id', $admin->trax_id)->where('trax_id', '!=', null);
                    if ($employee->exists()) {
                        $employee = $employee->first();
                        $employee->pin = $request->pin;
                        $employee->update();
                    }
                    $admin->reset_pin_status = 1;
                    $admin->save();
                    return response()->json(['status' => 0, 'reset_message' => 'Pin has been reset successfully']);
                } else {
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
                'line_manager_id' => ['nullable', 'integer', 'digits_between:1,10'],

                //BankInformation
                'bank_id' => ['nullable', 'integer', 'digits_between:1,10', 'exists:banks_lists,id'],
                'account_title' => ['nullable'],
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
                        $employee_request->line_manager_id = $request->line_manager_id;
                        $employee_request->save();

                        if ($request->has("bank_id") && $request->has("account_title") && $request->has("iban")) {
                            $employee_bank_info = new EmployeeBankInformation();
                            $employee_bank_info->employee_id = $employee_request->id;
                            $employee_bank_info->account_title = $request->account_title;
                            $employee_bank_info->bank_id = $request->bank_id;
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
                        $message = "Welcome to TRAX " . $request->name . "- Your Request have been received by Trax, and is pending for Approval from HR.";
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
                        $message = "Welcome to TRAX " . $employee_request->name . "- Your Request have been received by Trax, and is pending for Approval from HR.";
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
        $admin_profile = Admin::join('employees as e', 'admins.trax_id', '=', 'e.trax_id')
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
        $admin_profile = Admin::join('employees as e', 'admins.trax_id', '=', 'e.trax_id')
            ->select('e.id as employee_id', 'e.blood_group as blood_group_id', 'e.emergency_contact as emergency_contact_no', 'e.emergency_contact_person as emergency_contact_person')
            ->where('admins.id', $admin_id);
        if ($admin_profile->exists()) {
            $admin_profile = $admin_profile->first();
            if (!$admin_profile->blood_group_id || !$admin_profile->emergency_contact_no || !$admin_profile->emergency_contact_person) {
                return response()->json(['status' => 0, 'message' => "Please Update Your Profile"]);
            } else {
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
        $admin_profile = Admin::join('employees as e', 'admins.trax_id', '=', 'e.trax_id')
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

    public function check_pin(Request $request)
    {
        $admin_id = $request->admin_id;
        $admin = Admin::find($admin_id);
        if ($admin) {
            if ($admin->reset_pin_status == 1) {
                return response()->json(['status' => 0, 'pin_status' => 1]);
            }
            return response()->json(['status' => 0, 'pin_status' => 0]);
        }
        return response()->json(['status' => 0, 'pin_status' => 0]);
    }

    public function logout(Request $request)
    {
        $admin_id = $request->admin_id;
        $admin = Admin::find($admin_id);
        if ($admin) {
            if ($admin->reset_pin_status == 1) {
                $admin->reset_pin_status = 0;
                $admin->save();
                return response()->json(['status' => 0, 'message' => "Logout Successfully"]);
            }
            return response()->json(['status' => 0, 'message' => "Logout Successfully"]);
        }
        return response()->json(['status' => 0, 'message' => "Logout Successfully"]);
    }

    public function leads_list(Request $request)
    {
        $admin_id = $request->admin_id;
        $admin = Admin::find($admin_id);

        $lead_statuses = LeadStatus::whereNotIn('id', [3, 11, 12])->select('id', 'name')->get();
        $cities = City::where('business_category_id', 1)->where('status', 1)->get();

        $leads = Lead::join('cities as c', 'c.id', '=', 'leads.city_id')
            ->leftjoin('lead_statuses as ls', 'ls.id', '=', 'leads.status_id')
            ->leftjoin('service_list as sl', 'sl.id', '=', 'leads.service_id')
            ->leftjoin('admins as ad', 'ad.id', '=', 'leads.sale_person_id')
            ->select('leads.id as lead_id', 'leads.contact_person as contact_person', 'leads.phone_number as phone_number', 'leads.email_address as email_address', 'leads.requested_date as requested_date', 'leads.message as message', 'leads.status_id as status_id', 'ls.name as status', 'c.name as city', 'sl.name as service', 'leads.brand as brand', 'ad.name as sale_person');

        if ($admin->role_id != 4 && $admin->role_id != 44 && $admin->role_id != 60) {
            $leads = $leads->where('leads.sale_person_id', $admin_id)->wherenotin('leads.status_id', [3, 11, 12]);
        }

        if ($request->city_id) {
            $leads = $leads->where('leads.city_id', $request->city_id);
        }
        if ($request->status_id) {
            $leads = $leads->where('leads.status_id', $request->status_id);
        }
        if ($request->date_from) {
            if ($request->date_to) {
                $from = $request->date_from . ' 00:00:00';
                $to = $request->date_to . ' 23:59:59';
                $leads = $leads->whereBetween('leads.requested_date', [$from, $to]);
            } else {
                $leads = $leads->whereDate('leads.requested_date', $request->date_from);
            }
        }
        if ($leads->exists()) {
            $leads->orderBy('leads.requested_date', "DESC");
            $leads = $leads->get();
            $data = array();
            foreach ($leads as $lead) {
                $datum = array();
                $datum["lead_id"] = $lead->lead_id;
                $datum["contact_person"] = $lead->contact_person;
                $datum["phone_number"] = $lead->phone_number;
                $datum["email_address"] = $lead->email_address;
                $datum["requested_date"] = $lead->requested_date;
                $datum["message"] = ($lead->message != null) ? $lead->message : "";
                $datum["status"] = $lead->status;
                $datum["status_id"] = $lead->status_id;
                $datum["city"] = $lead->city;
                $datum["service"] = $lead->service;
                $datum["brand"] = $lead->brand;
                $datum["sale_person"] = ($lead->sale_person != null) ? $lead->sale_person : "";
                $data[] = $datum;
            }
            return response()->json(['status' => 0, 'message' => "Leads Found!", 'data' => $data, 'cities' => $cities, 'lead_status' => $lead_statuses]);
        } else {
            return response()->json(['status' => 0, 'message' => "No data found!", 'cities' => $cities, 'lead_status' => $lead_statuses]);
        }
    }

    public function add_remarks(Request $request)
    {
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
            if ($remarks != NULL) {
                $lead_remarks = new LeadRemark();
                $lead_remarks->lead_id = $lead->id;
                $lead_remarks->remarks = $remarks;
                $lead_remarks->updated_by = $admin_id;
                $lead_remarks->save();
                if ($lead->sale_person_id) {
                    if ($lead->sale_person_id != $admin_id) {
                        NotificationsController::app_notification(15, $lead->sale_person_id, 1, $lead_id);
                    }
                }
                return response()->json(['status' => 0, 'message' => 'Remarks added Successfully!']);
            } else {
                return response()->json(['status' => 1, 'message' => 'Invalid Remarks!']);
            }
        }
    }

    public function view_remarks(Request $request)
    {
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
            if ($lead_remarks->exists()) {
                $lead_remarks = $lead_remarks->get();
                return response()->json(['status' => 0, 'data' => $lead_remarks]);
            }
            return response()->json(['status' => 1, 'message' => 'No Remarks Found!']);
        }
    }

    public function trax_directory_v2(Request $request)
    {
        $rules = [
            'search_param' => ['nullable'],
            'search_with' => ['nullable'],
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {

            $admin_profile = Admin::join('employees as e', 'admins.trax_id', '=', 'e.trax_id')
                ->join('employee_designations as d', 'd.id', '=', 'admins.designation_id')
                ->join('admin_departments as ad', 'd.department_id', '=', 'ad.id')
                ->join('cities as c', 'c.id', '=', 'e.city_id')
                ->leftjoin('employee_blood_groups as bg', 'bg.id', '=', 'e.blood_group')
                ->select('e.trax_id as trax_id', 'e.name as name', 'e.official_email as email', 'e.phone_number as phone', 'd.name as designation', 'ad.name as department_name', 'bg.name as blood_group', 'e.emergency_contact as emergency_contact_no', 'e.emergency_contact_person as emergency_contact_person', 'c.name as city', 'e.official_phone_number as official_phone_number')
                ->where('admins.status', 1);
            if ($request->search_with == 1) {
                $admin_profile = $admin_profile->where('e.name', 'like', '%' . $request->search_param . '%');
            } elseif ($request->search_with == 2) {
                $admin_profile = $admin_profile->where(function ($query) use ($request) {
                    $query->where('e.phone_number', substr_replace($request->input('search_param'), '-', 4, 0))
                        ->orwhere('e.official_phone_number', substr_replace($request->input('search_param'), '-', 4, 0));
                });
            } elseif ($request->search_with == 3) {
                $admin_profile = $admin_profile->where('e.trax_id', $request->search_param);
            }
            if ($request->has("city_id")) {
                $admin_profile = $admin_profile->where('e.city_id', $request->city_id);
            }
            if ($admin_profile->exists()) {
                $admin_profile = $admin_profile->get();
                return response()->json(['status' => 0, 'data' => $admin_profile]);
            } else {
                return response()->json(['status' => 1, 'message' => 'No User found!']);
            }
        }
    }

    public function check_profile_v2(Request $request)
    {
        $admin_id = $request->admin_id;
        $admin = Admin::find($admin_id);
        if ($admin) {
            $admin_profile = Employee::where('trax_id', $admin->trax_id);
            if ($admin_profile->exists()) {
                $admin_profile = $admin_profile->first();
                if (!$admin_profile->blood_group || !$admin_profile->emergency_contact || !$admin_profile->emergency_contact_person || !$admin_profile->guardian_name || !$admin_profile->mother_name || !$admin_profile->address || !$admin_profile->employee_gender_id || !$admin_profile->religion_id || !$admin_profile->marital_status_id || !$admin_profile->date_of_birth || !$admin_profile->staff_category_id || !$admin_profile->shift_id || !$admin_profile->domicile_id || !$admin_profile->nationality_id) {
                    return response()->json(['status' => 0, 'message' => "Please Update Your Profile"]);
                } else {
                    return response()->json(['status' => 1, 'message' => "Profile already updated"]);
                }
            } else {
                return response()->json(['status' => 1, 'message' => "Admin Profile Not Found"]);
            }
        } else {
            return response()->json(['status' => 1, 'message' => "Admin Profile Not Found"]);
        }

    }

    public function get_profile_v2(Request $request)
    {
        $admin_id = $request->admin_id;
        $admin = Admin::find($admin_id);
        if ($admin) {
            $blood_group_list = EmployeeBloodGroup::all();
            $gender_list = EmployeeGender::all();
            $religion_list = EmployeeReligion::all();
            $marital_status_list = EmployeeMaritalStatus::all();
            $staff_category_list = StaffCategory::all();
            $shift_list = EmployeeShift::all();
            $domecile_list = EmployeeDomicile::all();
            $nationalities_list = EmployeeNationality::all();
            $admin_profile = Employee::where('trax_id', $admin->trax_id);
            if ($admin_profile->exists()) {
                $admin_profile = $admin_profile->get();
                return response()->json(['status' => 0, 'blood_group_list' => $blood_group_list, 'gender_list' => $gender_list, 'religion_list' => $religion_list, 'marital_status_list' => $marital_status_list, 'staff_category_list' => $staff_category_list, 'shift_list' => $shift_list, 'domecile_list' => $domecile_list, 'nationalities_list' => $nationalities_list, 'employee_data' => $admin_profile]);
            } else {
                return response()->json(['status' => 1, 'message' => "Admin Profile Not Found"]);
            }
        } else {
            return response()->json(['status' => 1, 'message' => "Admin Profile Not Found"]);
        }
    }

    public function update_profile_v2(Request $request)
    {
        $rules = [
            'employee_id' => ['required', 'integer', 'digits_between:1,10', 'exists:employees,id'],
            'mother_name' => ['nullable'],
            'employee_gender_id' => ['nullable', 'integer', 'digits_between:1,10', 'exists:employee_genders,id'],
            'shift_id' => ['nullable', 'integer', 'digits_between:1,10', 'exists:employee_shifts,id'],
            'staff_category_id' => ['nullable', 'integer', 'digits_between:1,10', 'exists:staff_categories,id'],
            'guardian_name' => ['nullable'],
            'religion_id' => ['nullable', 'integer', 'digits_between:1,10', 'exists:employee_religions,id'],
            'domicile_id' => ['nullable', 'integer', 'digits_between:1,10', 'exists:employee_domiciles,id'],
            'marital_status_id' => ['nullable', 'integer', 'digits_between:1,10', 'exists:employee_marital_statuses,id'],
            'blood_group_id' => ['required', 'integer', 'digits_between:1,10', 'exists:employee_blood_groups,id'],
            'nationality_id' => ['nullable', 'integer', 'digits_between:1,10', 'exists:employee_nationalities,id'],
            'address' => ['nullable'],
            'emergency_contact' => ['required', 'regex:/^[0][0-9]{3}-[0-9]{7}$/'],
            'emergency_contact_person' => ['required'],
            'official_email' => ['nullable', 'email'],
            'date_of_birth' => ['nullable'],
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $employee_request = Employee::find($request->employee_id);
            if ($employee_request) {
                $admin = Admin::where('trax_id', $employee_request->trax_id);
                if ($admin->exists()) {
                    $admin = $admin->first();
                    $city = City::find($employee_request->city_id);
                    $employee_request->zone_id = $city->zone_id;
                    if ($request->has('employee_gender_id')) {
                        $employee_request->employee_gender_id = $request->employee_gender_id;
                    }
                    if ($request->has('guardian_name')) {
                        $employee_request->guardian_name = $request->guardian_name;
                    }

                    if ($request->has('religion_id')) {
                        $employee_request->religion_id = $request->religion_id;
                    }

                    if ($request->has('domicile_id')) {
                        $employee_request->domicile_id = $request->domicile_id;
                    }

                    if ($request->has('marital_status_id')) {
                        $employee_request->marital_status_id = $request->marital_status_id;
                    }

                    if ($request->has('blood_group_id')) {
                        $employee_request->blood_group = $request->blood_group_id;
                    }

                    if ($request->has('address')) {
                        $employee_request->address = $request->address;
                    }

                    if ($request->has('emergency_contact')) {
                        $employee_request->emergency_contact = $request->emergency_contact;
                    }

                    if ($request->has('emergency_contact_person')) {
                        $employee_request->emergency_contact_person = $request->emergency_contact_person;
                    }

                    if ($request->has('official_email')) {
                        $employee_request->official_email = $request->official_email;
                        $admin->email = $request->official_email;
                    }

                    if ($request->has('date_of_birth')) {
                        $employee_request->date_of_birth = $request->date_of_birth;
                    }

                    if ($request->has('mother_name')) {
                        $employee_request->mother_name = $request->mother_name;
                    }

                    if ($request->has('shift_id')) {
                        $employee_request->shift_id = $request->shift_id;
                        $admin->shift_id = $request->shift_id;
                    }

                    if ($request->has('staff_category_id')) {
                        $employee_request->staff_category_id = $request->staff_category_id;
                    }
                    if ($request->has('nationality_id')) {
                        $employee_request->nationality_id = $request->nationality_id;
                    }

                    $employee_request->save();
                    $admin->save();
                    return response()->json(['status' => 0, 'message' => "Profile update successfully"]);
                } else {
                    return response()->json(['status' => 1, 'message' => 'User not found!']);
                }
            } else {
                return response()->json(['status' => 1, 'message' => 'User not found!']);
            }
        }
    }

    public function lead_statuses(Request $request)
    {
        $rules = [
            'status_id' => ['nullable', 'integer', 'digits_between:1,10', 'exists:lead_statuses,id'],
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            if (!$request->has('status_id')) {
                $lead_statuses = LeadStatus::wherenotin('id', [1, 12])->select('id', 'name')->get();
                return response()->json(['status' => 0, 'statuses' => $lead_statuses]);
            } else {
                $lead_reason = LeadReason::join('lead_status_reasons as lsr', 'lsr.reason_id', 'lead_reasons.id')
                    ->select('lead_reasons.id as id', 'lead_reasons.name as name')
                    ->where('lsr.status_id', $request->status_id);
                if ($lead_reason->exists()) {
                    $lead_reason = $lead_reason->get();
                    return response()->json(['status' => 0, 'reasons' => $lead_reason]);
                } else {
                    return response()->json(['status' => 0, 'reasons' => []]);
                }
            }
        }

    }

    public function lead_status_update(Request $request)
    {
        $rules = [
            'lead_id' => ['required', 'integer', 'digits_between:1,10', 'exists:leads,id'],
            'status_id' => ['required', 'integer', 'digits_between:1,10', 'exists:lead_statuses,id'],
            'reason_id' => ['nullable'],
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $lead = Lead::find($request->lead_id);
            $admin_id = $request->admin_id;
            if ($lead) {
                if ($lead->sale_person_id) {
                    $reason_id = NULL;
                    if ($request->has('reason_id')) {
                        $reason_id = $request->reason_id;
                    }
                    $lead_log = new LeadLog();
                    $lead_log->lead_id = $lead->id;
                    $lead_log->prev_status_id = $lead->status_id;
                    $lead_log->status_id = $request->status_id;
                    $lead_log->reason = $reason_id;
                    $lead_log->sale_person_id = $lead->sale_person_id;
                    if ($lead->reference_person_id == NULL) {
                        $lead_log->reference_person_id = $admin_id;
                    } else {
                        $lead_log->reference_person_id = $lead->reference_person_id;
                    }
                    $lead_log->updated_by = $admin_id;
                    $lead_log->save();

                    $lead->status_id = $request->status_id;
                    $lead->reason = $reason_id;
                    $lead->updated_by = $admin_id;
                    $lead->save();

                    if ($request->status_id == 9) {
                        NotificationsController::send(113, $lead);
                    } elseif ($request->status_id == 2) {
                        LeadTaggingController::notification_unresponsive($lead->id);
                    }
                    $lead_status = LeadStatus::find($request->status_id);
                    return response()->json(['status' => 0, 'message' => 'Status updated Successfully!', 'status_name' => $lead_status->name]);
                } else {
                    return response()->json(['status' => 1, 'message' => 'Sales Person not Tagged']);
                }
            } else {
                return response()->json(['status' => 1, 'message' => 'Invalid Lead']);
            }
        }
    }

    public function check_permissions(Request $request)
    {
        $admin_id = $request->admin_id;
        $admin = Admin::find($admin_id);
        if ($admin) {
            $permissions = array();
            $wms_user_permissions = DB::table('wms_admin_role_module_permissions')->where('role_id', $admin->role_id)->pluck('permission_id')->toArray();
            $user_permissions = AdminRoleModulePermission::where('role_id', $admin->role_id)->pluck('permission_id')->toArray();
            $permissions['cargo_user'] = (in_array($admin->role_id, [11, 10, 15, 55, 23, 33, 46])) ? 1 : 0;
            if ($admin->designation_id) {
                $user_department = $admin->Edesignation->department_id;
            } else {
                $user_department = $admin->role->department_id;
            }
            $permissions['sales_person'] = ($user_department == 7) ? 1 : 0;
            $permissions['pick_list_user'] = (in_array(23, $wms_user_permissions)) ? 1 : 0;
            $permissions['daily_visit_report'] = (in_array(264, $user_permissions)) ? 1 : 0;
            $permissions['daily_visit_form'] = (in_array(265, $user_permissions)) ? 1 : 0;
            return response()->json(['status' => 0, 'permissions' => $permissions]);

        }
    }

    public function pending_pick_list(Request $request)
    {
        $admin_id = $request->admin_id;
        $pending_picklist = WmsPicklist::join('admins', 'admins.id', '=', 'wms_picklists.created_by')
            ->join('wms_pickers as wp', 'wp.id', '=', 'wms_picklists.picker_id')
            ->select('wms_picklists.id as picklist_id', 'admins.name as created_by', 'wms_picklists.created_at as picking_date', 'wms_picklists.sku_count', 'wms_picklists.tracking_count', 'wms_picklists.quantity')
            ->where('wms_picklists.status', 0)
            ->where('wp.admin_id', $admin_id);

        if ($pending_picklist->exists()) {
            $pending_picklist = $pending_picklist->get();
            return response()->json(['status' => 0, 'pending_picklist' => $pending_picklist]);
        } else {
            return response()->json(['status' => 1, 'message' => "No Picklist Assigned!"]);
        }
    }

    public function pick_list_details(Request $request)
    {
        $rules = [
            'picklist_id' => ['required', 'integer', 'digits_between:1,10'],
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $picklist = WmsPicklist::find($request->picklist_id);
            if ($picklist) {
                if ($picklist->status == 0) {
                    $picklist_data = array();
                    foreach ($picklist->items as $item) {
                        $picklist_datum = array();
                        $pending_picking = WmsPendingPicking::find($item->pending_picking_id);
                        $picklist_datum['pending_picking_id'] = $pending_picking->id;
                        $picklist_datum['product_id'] = $pending_picking->product_id;
                        $picklist_datum['sku_id'] = $pending_picking->product->sku_id;
                        $picklist_datum['product_name'] = $pending_picking->product->name;
                        $picklist_datum['shipper_name'] = $pending_picking->product->shipper->name;
                        $picklist_datum['shipper_id'] = $pending_picking->product->user_id;
                        $picklist_datum['listed_quantity'] = $item->quantity;
                        $picklist_datum['item_id'] = $item->id;
                        $picklist_data[] = $picklist_datum;
                    }
                    return response()->json(['status' => 0, 'picklist_id' => $picklist->id, 'picklist_data' => $picklist_data]);
                } else {
                    return response()->json(['status' => 1, 'message' => "Picklist already updated"]);
                }
            } else {
                return response()->json(['status' => 1, 'message' => "Invalid Picklist"]);
            }
        }
    }

    public function pick_list_barcode_validate(Request $request)
    {
        $rules = [
            'picklist_id' => ['required', 'integer', 'digits_between:1,10'],
            'barcode' => ['required'],
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $barcode = $request->barcode;
            $product_ids = WmsPicklistItem::join('wms_pending_pickings as wpp', 'wms_picklist_items.pending_picking_id', '=', 'wpp.id')
                ->where('wms_picklist_items.picklist_id', $request->picklist_id)->pluck('wpp.product_id')->toArray();

            $product = WmsProductBarcode::where('picklist_id', $request->picklist_id)->where('status', 3)->whereIn('product_id', $product_ids)->where(function ($query) use ($barcode) {
                $query->where('barcode', '=', $barcode)
                    ->orWhere('id', '=', $barcode);
            });
            if ($product->exists()) {
                $product = $product->first();
                return response()->json(['status' => 0, 'message' => "Barcode Valid", 'product_id' => $product->product_id, 'barcode' => $product->barcode, 'barcode_id' => $product->id]);
            } else {
                return response()->json(['status' => 1, 'message' => "Barcode Invalid"]);
            }

        }
    }

    public function pick_list_receive12(Request $request)
    {
        $rules = [
            'picklist_id' => ['required'],
            'barcode_list' => ['required'],
        ];
        $validate = Validator::make($request->all(), $rules, $this->messages);
        $validate->setAttributeNames($this->names);
        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $admin_id = $request->admin_id;
            $picklist_id = (int)$request->picklist_id;
            $picklist = WmsPicklist::find($picklist_id);
            if ($picklist) {
                if ($picklist->status == 0) {
                    $pending_picking_ids = WmsPicklistItem::where('picklist_id', $picklist->id)->pluck('pending_picking_id')->toArray();
                    $shipment_ids = WmsPendingPicking::whereIn('id', $pending_picking_ids)->where('courier_id', 1)->pluck('shipment_id')->toArray();
                    $courier_order_ids = WmsPendingPicking::whereIn('id', $pending_picking_ids)->where('courier_id', '!=', 1)->pluck('shipment_id')->toArray();
                    Shipment::whereIn('id', $shipment_ids)->where('warehouse_order_status', '=', 10)->update(['warehouse_order_status' => 8]);
                    WmsCourierOrders::whereIn('id', $courier_order_ids)->where('status', '=', 7)->update(['status' => 3]);
                    $picklist->status = 1;
                    $picklist->save();
                    foreach ($picklist->items as $item) {
                        $item->status = 1;
                        $item->save();
                    }
                    $shipment_ids = array_merge($shipment_ids, $courier_order_ids);
                    WmsOrderProcess::whereIn('shipment_id', $shipment_ids)->update(['picklist_confirmed_at' => Carbon::now(), 'picklist_confirmed_by' => $admin_id]);
                    return response()->json(['status' => 0, 'message' => 'Picklist successfully updated!']);
                } else {
                    return response()->json(['status' => 1, 'message' => 'Picklist already updated']);
                }
            } else {
                return response()->json(['status' => 1, 'message' => 'Invalid Picklist']);
            }
        }
    }

    public function pick_list_receive(Request $request)
    {
        $rules = [
            'picklist_id' => ['required'],
            'barcode_list' => ['required'],
        ];
        $validate = Validator::make($request->all(), $rules, $this->messages);
        $validate->setAttributeNames($this->names);
        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $picklist_id = $request->picklist_id;
            $picklist = WmsPicklist::find($picklist_id);
            $admin_id = $request->admin_id;
            if ($picklist) {
                if ($picklist->status == 0) {
                    $pending_picking_ids = WmsPicklistItem::where('picklist_id', $picklist->id)->pluck('pending_picking_id')->toArray();
                    $shipment_ids = WmsPendingPicking::whereIn('id', $pending_picking_ids)->where('courier_id', 1)->pluck('shipment_id')->toArray();
                    $courier_order_ids = WmsPendingPicking::whereIn('id', $pending_picking_ids)->where('courier_id', '!=', 1)->pluck('shipment_id')->toArray();
                    Shipment::whereIn('id', $shipment_ids)->where('warehouse_order_status', '=', 10)->update(['warehouse_order_status' => 8]);
                    WmsCourierOrders::whereIn('id', $courier_order_ids)->where('status', '=', 7)->update(['status' => 3]);
                    $picklist->status = 1;
                    $picklist->save();
                    foreach ($picklist->items as $item) {
                        $item->status = 1;
                        $item->save();
                    }
                    $shipment_ids = array_merge($shipment_ids, $courier_order_ids);
                    WmsOrderProcess::whereIn('shipment_id', $shipment_ids)->update(['picklist_confirmed_at' => Carbon::now(), 'picklist_confirmed_by' => $admin_id]);
                    return response()->json(['status' => 0, 'message' => 'Picklist successfully updated!']);
                } else {
                    return response()->json(['status' => 0, 'message' => 'Picklist already updated']);
                }

            } else {
                return response()->json(['status' => 0, 'message' => 'Picklist not found!']);
            }
        }
    }

    public function check_bolt_version(Request $request)
    {
        $admin_id = $request->admin_id;
        $admin = Admin::find($admin_id);
        if ($admin) {
            $permissions = array();
            $wms_user_permissions = DB::table('wms_admin_role_module_permissions')->where('role_id', $admin->role_id)->pluck('permission_id')->toArray();
            $user_permissions = AdminRoleModulePermission::where('role_id', $admin->role_id)->pluck('permission_id')->toArray();
            $permissions['cargo_user'] = (in_array($admin->role_id, [11, 10, 15, 55, 23, 33, 46])) ? 1 : 0;
            if ($admin->designation_id) {
                $user_department = $admin->Edesignation->department_id;
            } else {
                $user_department = $admin->role->department_id;
            }
            $permissions['sales_person'] = ($user_department == 7) ? 1 : 0;
            $permissions['pick_list_user'] = (in_array(23, $wms_user_permissions)) ? 1 : 0;
            $permissions['daily_visit_report'] = (in_array(264, $user_permissions)) ? 1 : 0;
            $permissions['daily_visit_form'] = (in_array(265, $user_permissions)) ? 1 : 0;
            $permissions['return_note_create'] = (in_array(48, $user_permissions)) ? 1 : 0;
            $permissions['return_note_view'] = (in_array(49, $user_permissions)) ? 1 : 0;
            $permissions['return_note_receive'] = (in_array(50, $user_permissions)) ? 1 : 0;
            $global_settings = GlobalSettings::where('type', 'bolt_updated_version')->select('setting_value as setting_value');
            if ($global_settings->exists()) {
                $global_settings = $global_settings->first();
                return response()->json(['status' => 0, 'app_version' => $global_settings->setting_value, 'permissions' => $permissions]);
            } else {
                return response()->json(['status' => 0, 'app_version' => 24, 'permissions' => $permissions]);
            }
        }
    }

    public function daily_visit_index()
    {
        $lead_statuses = DailyVisitLeadStatus::select('id', 'name')->get();
        $shippers = User::where('status', 3)->get(['id', 'name']);
        return response()->json(['status' => 0, 'lead_statuses' => $lead_statuses, 'shippers' => $shippers]);
    }

    public function shipper_details(Request $request)
    {

        $shipper_detail = User::where('status', 3)->where('id', $request->shipper_id)->select('name', 'poc', 'address', 'email', 'phone');
        if ($shipper_detail) {
            $shipper_detail = $shipper_detail->get();
            return response()->json(['status' => 0, 'shipper_detail' => $shipper_detail]);
        } else {
            return response()->json(['status' => 1, 'message' => "Invalid Shipper!"]);
        }
    }

    public function daily_visit_store(Request $request)
    {
        $rules = [
            'company_name' => ['required'],
            'customer_name' => ['required'],
            'customer_address' => ['required'],
            'phone_no' => ['required'],
            'email_address' => ['required'],
            'lead_status' => ['required'],
            'feedback' => ['required'],
            'latitude' => ['required'],
            'longitude' => ['required'],
            'business_card_image' => ['nullable'],
            'location_image' => ['nullable'],
            'shipper_id' => ['nullable', 'integer'],
        ];
        $validate = Validator::make($request->all(), $rules, $this->messages);
        $validate->setAttributeNames($this->names);
        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            try {
                $edit = 0;
                if($request->has("daily_visit_id")){
                    $daily_visit = DailyVisit::where('id', $request->daily_visit_id);
                    if($daily_visit->exists()){
                        $daily_visit = $daily_visit->first();
                        $daily_visit->updated_by = $request->admin_id;
                        $edit = 1;
                    }else{
                        return response()->json(['status' => 1, 'message' => 'Invalid Daily Visit ID']);
                    }
                } else{
                    $daily_visit = new DailyVisit();
                    $daily_visit->admin_id = $request->admin_id;
                }
                $daily_visit->shipper_id = $request->shipper_id;
                $daily_visit->company_name = str_replace('"', "", $request->company_name);
                $daily_visit->customer_name = str_replace('"', "", $request->customer_name);
                $daily_visit->customer_address = str_replace('"', "", $request->customer_address);
                $daily_visit->phone_no = str_replace('"', "", $request->phone_no);
                $daily_visit->email = str_replace('"', "", $request->email_address);
                $daily_visit->lead_status_id = $request->lead_status;
                $daily_visit->feedback = str_replace('"', "", $request->feedback);
                $daily_visit->latitude = $request->latitude;
                $daily_visit->longitude = $request->longitude;
                $daily_visit->save();

                if ($request->hasFile('business_card_image')) {
                    if($daily_visit->business_card_image != null){
                        Storage::disk('public')->delete($daily_visit->business_card_image);
                    }
                    $filename = 'daily_visit_bc_' . $daily_visit->id . '.png';
                    $file = $request->file('business_card_image');
                    Storage::disk('public')->putFileAs('daily_visit\business_card', $file, $filename);
                    $daily_visit->business_card_image = $filename;
                    $daily_visit->save();
                }

                if ($request->hasFile('location_image')) {
                    if($daily_visit->location_image != null){
                        Storage::disk('public')->delete($daily_visit->location_image);
                    }
                    $filename = 'daily_visit_l_' . $daily_visit->id . '.png';
                    $file = $request->file('location_image');
                    Storage::disk('public')->putFileAs('daily_visit\location', $file, $filename);
                    $daily_visit->location_image = $filename;
                    $daily_visit->save();
                }
                return response()->json(['status' => 0, 'message' => 'Daily Visit Has been Submitted', 'edit' => $edit]);
            } catch (Exception $ex) {
                return response()->json(['status' => 1, 'message' => 'Error ', 'errors' => $ex]);
            }
        }
    }

    public function daily_visit_report(Request $request)
    {
        if ($request->isMethod('get')) {
            $sale_users_bypass = array();
            $settings = GlobalSettings::where('type', 'sales_user_restriction_bypass');
            if ($settings->exists()) {
                $settings = $settings->first();
                $sale_users_bypass = explode(',', $settings->text);
            }
            if (in_array($request->admin_id, $sale_users_bypass)) {
                $admin_list = Admin::join('admin_roles as ar', 'admins.role_id', '=', 'ar.id')->select(['admins.name', 'admins.id'])->where('status', 1)->where('ar.department_id', 7)->get();
                return response()->json(['status' => 0, 'admin_list' => $admin_list]);
            } else {
                $admin_list = Admin::where('status', 1)->where('id', $request->admin_id)->select('id', 'name')->get();
                return response()->json(['status' => 0, 'admin_list' => $admin_list]);
            }
        }
        if ($request->isMethod('post')) {
            $daily_visit = DB::connection('reports')->table('daily_visits')
                ->join('daily_visit_lead_statuses as dvls', 'dvls.id', '=', 'daily_visits.lead_status_id')
                ->join('admins as a', 'a.id', '=', 'daily_visits.admin_id')
                ->select('daily_visits.id as daily_visit_id', 'daily_visits.shipper_id as shipper_id', 'daily_visits.lead_status_id as lead_status_id', 'a.name as admin', 'daily_visits.company_name as company_name', 'daily_visits.customer_name as customer_name', 'daily_visits.customer_address as customer_address', 'daily_visits.phone_no as phone_no', 'daily_visits.email as email', 'dvls.name as lead_status', 'daily_visits.feedback as feedback', 'daily_visits.latitude as latitude', 'daily_visits.longitude as longitude', 'daily_visits.created_at as created_at', 'daily_visits.business_card_image as business_card_image', 'daily_visits.location_image as location_image')
                ->orderBy('daily_visits.created_at', 'DESC');
            if ($request->from_date) {
                if ($request->to_date) {
                    $daily_visit = $daily_visit->whereBetween('daily_visits.created_at', [$request->from_date . ' 00:00:00', $request->to_date . ' 23:59:59']);
                } else {
                    $daily_visit = $daily_visit->whereDate('daily_visits.created_at', $request->from_date);
                }
            }
            if (!$request->admin_id) {
                $daily_visit = $daily_visit->where('a.id', $request->user_id);
            } else {
                $daily_visit = $daily_visit->where('a.id', $request->admin_id);
            }
            if ($daily_visit->exists()) {
                $daily_visit = $daily_visit->get();
                return response()->json(['status' => 0, 'data' => $daily_visit]);
            }
            return response()->json(['status' => 1, 'message' => 'No data found!']);
        }
    }

    public function check_profile_v3(Request $request)
    {
        $admin_id = $request->admin_id;
        $admin = Admin::find($admin_id);
        if ($admin) {
            if ($request->has('device_token')) {
                EmployeeDeviceToken::where('device_token', $request->get('device_token'))->delete();
                EmployeeDeviceToken::where('employee_type_id', 1)->where('employee_id', $admin->id)->delete();
                $employee_device_token = new EmployeeDeviceToken();
                $employee_device_token->employee_id = $admin->id;
                $employee_device_token->employee_type_id = 1;
                $employee_device_token->device_token = $request->get('device_token');
                $employee_device_token->save();
            }
            $admin->current_app_version = $request->app_version;
            $admin->save();
            $admin_profile = Employee::where('trax_id', $admin->trax_id);
            if ($admin_profile->exists()) {
                $admin_profile = $admin_profile->first();
                if (!$admin_profile->blood_group || !$admin_profile->emergency_contact || !$admin_profile->emergency_contact_person || !$admin_profile->guardian_name || !$admin_profile->mother_name || !$admin_profile->address || !$admin_profile->employee_gender_id || !$admin_profile->religion_id || !$admin_profile->marital_status_id || !$admin_profile->date_of_birth || !$admin_profile->staff_category_id || !$admin_profile->shift_id || !$admin_profile->domicile_id || !$admin_profile->nationality_id) {
                    return response()->json(['status' => 1, 'message' => "Please Update Your Profile"]);
                } else {
                    return response()->json(['status' => 1, 'message' => "Profile already updated"]);
                }
            } else {
                return response()->json(['status' => 1, 'message' => "Admin Profile Not Found"]);
            }
        } else {
            return response()->json(['status' => 1, 'message' => "Admin Profile Not Found"]);
        }

    }

    public function store_dws_image(Request $request)
    {
        $rules = [
            'tracking_number' => ['required', 'integer', 'digits_between:10,20', Rule::exists('shipments', 'tracking_number')],
            'picture' => ['required', 'mimes:png,jpeg,jpg']
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(false);
        } else {
            $shipment = Shipment::where('tracking_number', $request->tracking_number)->first();
            $shipment_details = ShipmentDetail::where('shipment_id', $shipment->id);
            if ($shipment_details->exists()) {
                $shipment_details = $shipment_details->first();
                if ($shipment_details->dws_status == 1 && $shipment_details->dws_image == null) {
                    $date = Carbon::now()->format('Y_m_d');
                    $file = $request->file('picture');
                    $filename = 'image_' . $shipment->id . '_' . $date . '.' . $file->extension();
                    $directory = 'dws_images';
                    $picture_path = $directory . '/' . $filename;
                    Storage::disk('public')->put($picture_path, file_get_contents($request->picture));
                    $shipment_details->dws_image = $picture_path;
                    $shipment_details->save();
                    return response()->json(true);
                }
                return response()->json(false);
            }
            return response()->json(false);
        }
    }

    public function signup_data(Request $request)
    {
        $cities = City::where('status', 1)->where('business_category_id', 1)->select('id', 'name')->get();
        $designation = EmployeeDesignation::where('status', 1)->select('id', 'name', 'department_id')->get();
        $domicile = EmployeeDomicile::select('id', 'name')->get();
        $marital_status = EmployeeMaritalStatus::select('id', 'name')->get();
        $nationality = EmployeeNationality::select('id', 'name')->get();
        $religion = EmployeeReligion::select('id', 'name')->get();
        $gender = EmployeeGender::select('id', 'name')->get();
        $zone = Zone::where('status', 1)->where('business_category_id', 1)->select('id', 'name')->get();
        $department = AdminDepartment::select('id', 'name')->get();
        $hub = City::where('status', 1)->where('business_category_id', 1)->select('id', 'name')->get();
        $relationships = EmployeeRelationship::select('id', 'name')->get();
        $blood_group = EmployeeBloodGroup::select('id', 'name')->get();
        $banks = BanksList::select('id', 'name')->where('status', 1)->get();
        $rider_type = RiderType::select('id', 'name')->get();
        $staff_categories = StaffCategory::select('id', 'name')->get();
        $shifts = EmployeeShift::where('id', '!=', 1)->select('id', 'name', 'start_time', 'end_time')->get();
        $category = RiderCategory::all();
        $main_category = RiderMainCategory::all();
        $shift_data = array();
        foreach ($shifts as $shift) {
            $datum = array();
            $datum['id'] = $shift->id;
            $datum['name'] = $shift->name . ' (' . $shift->start_time . ' - ' . $shift->end_time . ') ';
            $shift_data[] = $datum;
        }
        return response()->json(['status' => 0, "cities" => $cities, "designation" => $designation, "domicile" => $domicile, "marital_status" => $marital_status, "nationality" => $nationality, "religion" => $religion, "gender" => $gender, "zone" => $zone, "department" => $department, "hub" => $hub, "blood_group" => $blood_group, "relationships" => $relationships, 'banks' => $banks, 'rider_type' => $rider_type, 'staff_categories' => $staff_categories, 'shifts' => $shift_data, 'rider_sub_category' => $category, 'rider_main_category' => $main_category]);
    }

    public function mark_attendance_v3(Request $request)
    {

        $rules = [
            'attendance_date' => ['required'],
            'latitude' => ['required', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'longitude' => ['required', 'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'],
            'action' => ['required', 'integer', 'digits_between:1,10', 'exists:attendance_actions,id'],
        ];

        $employee_id = $request->admin_employee;
        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $attendance_date = Carbon::createFromFormat('Y-m-d', $request->attendance_date);
            $last_action_log = EmployeeAttendanceActionLog::where('employee_id', $employee_id)
                ->where('employee_type', 1)->whereDate('attendance_date', $attendance_date->format("Y-m-d"))->orderBy('id', 'DESC');
            if ($last_action_log->exists()) {
                $last_action_log = $last_action_log->first();
                if (($attendance_date->lt(Carbon::now()->format("Y-m-d")) && $last_action_log->action_id == 2) || $attendance_date->format('l') == "Sunday") {
                    $attendance_date = $attendance_date->addDays(1);
                }
            }
            $admin_attendance = EmployeeAttendance::where('employee_id', $employee_id)
                ->whereDate('attendance_date', Carbon::parse($attendance_date)->format("Y-m-d"))
                ->where('employee_type', 1);
            $admin_attendance_action = new EmployeeAttendanceActionLog();
            if ($admin_attendance->exists()) {
                $admin_attendance = $admin_attendance->first();
            } else {
                $admin_attendance = new EmployeeAttendance();
                $admin_attendance->employee_id = $employee_id;
                $admin_attendance->employee_type = 1;
                $admin_attendance->attendance_date = $attendance_date;
            }
            $location_status = $this->calculate_location_status($request->latitude, $request->longitude);
            if ($request->action == 1) {
                if ($admin_attendance->clock_in_datetime == null) {
                    $admin_attendance->clock_in_datetime = Carbon::now()->format("Y-m-d H:i:s");
                    $admin_attendance->clock_in_latitude = $request->latitude;
                    $admin_attendance->clock_in_longitude = $request->longitude;
                    $admin_attendance->clock_in_location = $location_status;
                    $admin_attendance->save();
                }

                $admin_attendance_action->employee_id = $employee_id;
                $admin_attendance_action->employee_type = 1;
                $admin_attendance_action->action_id = $request->action;
                $admin_attendance_action->action_date = Carbon::now()->format("Y-m-d H:i:s");
                $admin_attendance_action->attendance_date = $attendance_date;
                $admin_attendance_action->latitude = $request->latitude;
                $admin_attendance_action->longitude = $request->longitude;
                $admin_attendance_action->location_status = $location_status;
                $admin_attendance_action->save();

                return response()->json(['status' => 0, 'message' => 'Clocked-In Successfully', 'response' => $admin_attendance_action, 'attendance_date' => Carbon::parse($attendance_date)->format("Y-m-d")]);
            } elseif ($request->action == 2) {
                $last_clockin_action = EmployeeAttendanceActionLog::where('employee_id', $employee_id)
                    ->where('employee_type', 1)->orderBy('id', 'DESC')->where('action_id', 1)->first();
                $attendance_date = $last_clockin_action->attendance_date;

                $admin_attendance->clock_out_datetime = Carbon::now()->format("Y-m-d H:i:s");
                $admin_attendance->clock_out_latitude = $request->latitude;
                $admin_attendance->clock_out_longitude = $request->longitude;
                $admin_attendance->clock_out_location = $location_status;
                $admin_attendance->save();

                $admin_attendance_action->employee_id = $employee_id;
                $admin_attendance_action->employee_type = 1;
                $admin_attendance_action->action_id = $request->action;
                $admin_attendance_action->action_date = Carbon::now()->format("Y-m-d H:i:s");
                $admin_attendance_action->attendance_date = $attendance_date;
                $admin_attendance_action->latitude = $request->latitude;
                $admin_attendance_action->longitude = $request->longitude;
                $admin_attendance_action->location_status = $location_status;
                $admin_attendance_action->save();
                return response()->json(['status' => 0, 'message' => 'Clocked-Out Successfully', 'response' => $admin_attendance_action, 'attendance_date' => Carbon::parse($attendance_date)->format("Y-m-d")]);
            }

            return response()->json(['status' => 1, 'message' => 'Failed']);
        }

    }

    public function adjustment_index(Request $request)
    {
        if ($request->has('adjustment_id')) {
            $leave = EmployeeAttendanceAdjustment::find($request->adjustment_id);
            if ($leave) {
                $user = Employee::find($leave->employee_id);
                if ($user) {
                    $data['trax_id'] = $user->trax_id;
                    $data['name'] = $user->name;
                    if ($leave->employee_type_id == 1) {
                        $data['designation'] = $user->designation->name;
                        $data['department'] = $user->department->name;
                        $role_id = $request->admin_role_id;
                        if ($role_id == 81) {
                            $data['user_type'] = 2;
                        } elseif (in_array($role_id, [1, 2, 3, 4, 5, 6, 35, 52, 58, 70, 63])) {
                            $data['user_type'] = 1;
                        } else {
                            $data['user_type'] = 0;
                        }
                    } else if ($leave->employee_type_id == 2) {
                        $data['designation'] = "Rider";
                        $data['department'] = "Operations";
                        $data['user_type'] = 0;
                    } else {
                        return response()->json(['status' => 1, 'message' => "Failed"]);
                    }
                    $data['approver_email'] = $user->line_manager->email;
                    $data['approver_name'] = $user->line_manager->name;
                } else {
                    return response()->json(['status' => 1, 'message' => "User not found"]);
                }
            }
        } else {
            $employee_id = $request->admin_employee;
            $employee = Employee::find($employee_id);
            if ($employee) {
                if ($employee->line_manager_id == null) {
                    return response()->json(['status' => 1, 'message' => "Department Head is not present!"]);
                }
                $data = array();
                $data['trax_id'] = $employee->trax_id;
                $data['name'] = $employee->name;
                $data['designation'] = $employee->designation->name;
                $data['department'] = $employee->department->name;
                $data['approver_email'] = $employee->line_manager->email;
                $data['approver_name'] = $employee->line_manager->name;
                $role_id = $request->admin_role_id;
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

    public function adjustment_apply(Request $request)
    {
        $rules = [
            'date' => ['required'],
            'reason' => ['required', 'max:500'],
            'adjustment_id' => ['nullable', 'integer', 'digits_between:1,10', 'exists:employee_attendance_adjustments,id'],
        ];

        $admin_id = $request->admin_id;
        $employee_id = $request->admin_employee;
        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $employee = Employee::find($employee_id);
            if ($employee) {
                if ($request->has('adjustment_id')) {
                    $leave_request = EmployeeAttendanceAdjustment::where('id', $request->adjustment_id);
                    if ($leave_request->exists()) {
                        $leave_request = $leave_request->first();
                        $leave_request->date = $request->date;
                        $leave_request->applied_reason = $request->reason;

                        if ($leave_request->status == 2) {
                            $leave_request->updated_by = $admin_id;
                        }
                        $leave_request->save();
                        $message = "Adjustment Request edited successfully";
                    } else {
                        return response()->json(['status' => 1, 'message' => 'Invalid Adjustment Request ID']);
                    }
                } else {
                    $leave = EmployeeAttendanceAdjustment::where('employee_id', $employee_id)->where('employee_type_id', 1)->whereIn('status', [1, 2])->where('date', $request->date);
                    if ($leave->exists()) {
                        return response()->json(['status' => 1, 'message' => 'Adjustment Request Already Submitted & Pending for Approval']);
                    }
                    $leave_request = new EmployeeAttendanceAdjustment();
                    $leave_request->employee_id = $employee_id;
                    $leave_request->employee_type_id = 1;
                    if (in_array($request->admin_role_id, [1, 2, 3, 4, 5, 6, 35, 52, 58, 70])) {
                        $reporter_id = 139;
                    } else {
                        $reporter_id = $employee->line_manager_id;
                    }
                    $leave_request->reporter_id = $reporter_id;
                    $leave_request->date = $request->date;
                    $leave_request->applied_reason = $request->reason;
                    $leave_request->save();
                    $message = "Adjustment Request submitted successfully";
                    $notify = false;
                    if ($leave_request->employee_type_id == 1) {
                        $user = Admin::where('employee_id', $reporter_id)->first();
                        if ($user) {
                            $user_id = $user->id;
                            $notify = true;
                        }
                    } elseif ($leave_request->employee_type_id == 1) {
                        $user = Rider::where('employee_id', $reporter_id)->first();
                        if ($user) {
                            $user_id = $user->id;
                            $notify = true;
                        }
                    }
                    NotificationsController::app_notification(17, $admin_id, 1, $leave_request->id);
                    if ($notify) {
                        NotificationsController::app_notification(18, $leave_request->reporter_id, 1, $leave_request->id);
                    }
                }
                return response()->json(['status' => 0, 'apply_message' => $message]);
            } else {
                return response()->json(['status' => 1, 'message' => 'User Not Found']);
            }
        }

    }

    public function employee_adjustment_list(Request $request)
    {
        $employee_id = $request->admin_employee;
        $employee_leaves = EmployeeAttendanceAdjustment::join('leave_statuses as ls', 'employee_attendance_adjustments.status', '=', 'ls.id')
            ->select('employee_attendance_adjustments.id as id', 'employee_attendance_adjustments.date as date', 'employee_attendance_adjustments.applied_reason as applied_reason', 'employee_attendance_adjustments.rejected_reason as rejected_reason', 'employee_attendance_adjustments.status as status_id', 'ls.name as status')
            ->where('employee_id', $employee_id)
            ->where('employee_type_id', 1);
        if ($employee_leaves->exists()) {
            $employee_leaves = $employee_leaves->get();
            $data = array();
            foreach ($employee_leaves as $employee_leave) {
                $datum = array();
                $datum['id'] = $employee_leave->id;
                $datum['date'] = $employee_leave->date;
                $datum['applied_reason'] = $employee_leave->applied_reason;
                $datum['rejected_reason'] = $employee_leave->rejected_reason;
                $datum['status_id'] = $employee_leave->status_id;
                $datum['status'] = $employee_leave->status;
                $data[] = $datum;
            }
            return response()->json(['status' => 0, 'response' => $data]);
        }
        return response()->json(['status' => 1, 'message' => "No Adjustment Found!"]);
    }

    public function approver_adjustment_list(Request $request)
    {
        $employee_id = $request->admin_employee;
        $admin = Employee::find($employee_id);
        if ($admin) {
            $admin_role = $request->admin_role_id;
            if (in_array($admin_role, [1, 2, 3, 4, 5, 6, 35, 52, 58, 70, 81])) {
                $employee_leaves = EmployeeAttendanceAdjustment::join('leave_statuses as ls', 'employee_attendance_adjustments.status', '=', 'ls.id')
                    ->select('employee_attendance_adjustments.id as id', 'employee_attendance_adjustments.date as date', 'employee_attendance_adjustments.applied_reason as applied_reason', 'employee_attendance_adjustments.rejected_reason as rejected_reason', 'employee_attendance_adjustments.status as status_id', 'ls.name as status', 'employee_attendance_adjustments.employee_id as employee_id', 'employee_attendance_adjustments.employee_type_id as type_id')
                    ->where('employee_attendance_adjustments.reporter_id', $request->admin_id)
                    ->where('employee_attendance_adjustments.status', 1);
            } else {
                return response()->json(['status' => 1, 'message' => "Invalid Role"]);
            }
            if ($employee_leaves->exists()) {
                $employee_leaves = $employee_leaves->get();
                $data = array();
                foreach ($employee_leaves as $employee_leave) {
                    $datum = array();
                    $datum['id'] = $employee_leave->id;
                    $datum['date'] = $employee_leave->date;
                    $datum['applied_reason'] = $employee_leave->applied_reason;
                    $datum['rejected_reason'] = $employee_leave->rejected_reason;
                    $datum['status_id'] = $employee_leave->status_id;
                    $datum['status'] = $employee_leave->status;

                    $user = Employee::find($employee_leave->employee_id);
                    if ($user) {
                        $datum['name'] = $user->name;
                        $datum['trax_id'] = $user->trax_id;
                        if ($employee_leave->type_id == 1) {
                            $datum['designation'] = $user->designation->name;
                        } elseif ($employee_leave->type_id == 2) {
                            $datum['designation'] = "Rider";
                        }
                    }
                    $data[] = $datum;
                }
                return response()->json(['status' => 0, 'approver_response' => $data]);
            }
            return response()->json(['status' => 1, 'message' => "No Pending Adjustment For Approval!"]);
        }
    }

    public function adjustment_approve(Request $request)
    {
        $rules = [
            'adjustment_id' => ['required', 'integer', 'digits_between:1,10', 'exists:employee_attendance_adjustments,id'],
        ];

        $admin_id = $request->admin_id;
        $employee_id = $request->admin_employee;
        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $admin = Employee::find($employee_id);
            if ($admin) {
                $employee_leaves = EmployeeAttendanceAdjustment::where('id', $request->adjustment_id)->where('reporter_id', $employee_id);
                if ($employee_leaves->exists()) {
                    $employee_leaves = $employee_leaves->first();
                    $employee_leaves->status = 2;
                    $employee_leaves->updated_by = $admin_id;
                    $user = Employee::find($employee_leaves->employee_id);
                    if (!$user) {
                        return response()->json(['status' => 1, 'message' => "Invalid Employee ID"]);
                    }
                    $shift = EmployeeShift::find($user->shift_id);
                    $date = Carbon::parse($employee_leaves->date)->format("Y-m-d");
                    $mark_attendance = EmployeeAttendance::where('employee_id', $employee_leaves->employee_id)
                        ->where('employee_type', $employee_leaves->employee_type_id)
                        ->whereDate('attendance_date', $date);
                    if ($mark_attendance->exists()) {
                        $mark_attendance = $mark_attendance->first();
                    } else {
                        $mark_attendance = new EmployeeAttendance();
                    }
                    $mark_attendance->attendance_date = $date;
                    $mark_attendance->employee_id = $employee_leaves->employee_id;
                    $mark_attendance->employee_type = $employee_leaves->employee_type_id;
                    $mark_attendance->clock_in_latitude = "24.85758065592256";
                    $mark_attendance->clock_in_longitude = "67.12476908400743";
                    $mark_attendance->clock_out_latitude = "24.85758065592256";
                    $mark_attendance->clock_out_longitude = "67.12476908400743";
                    if ($shift) {
                        $mark_attendance->clock_in_datetime = $date . ' ' . $shift->start_time;
                        $mark_attendance->clock_out_datetime = $date . ' ' . $shift->end_time;
                    } else {
                        $mark_attendance->clock_in_datetime = $date . ' 09:00:00';
                        $mark_attendance->clock_out_datetime = $date . ' 18:00:00';
                    }
                    $mark_attendance->leave_status = 2;
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
                    $employee_leaves->save();
                    $notify = false;
                    if ($employee_leaves->employee_type_id == 1) {
                        $admin = Admin::where('employee_id', $employee_leaves->employee_id)->first();
                        if ($admin) {
                            $user_id = $admin->id;
                            $notify = true;
                        }
                    } else if ($employee_leaves->employee_type_id == 2) {
                        $rider = Rider::where('employee_id', $employee_leaves->employee_id)->first();
                        if ($rider) {
                            $user_id = $rider->id;
                            $notify = true;
                        }
                    }
                    if ($notify) {
                        NotificationsController::app_notification(17, $user_id, $employee_leaves->employee_type_id, $employee_leaves->id);
                    }
                    return response()->json(['status' => 0, 'message' => "Adjustment request has been approved!"]);
                }
                return response()->json(['status' => 1, 'message' => "No Adjustment Found!"]);
            }
        }
    }

    public function adjustment_reject(Request $request)
    {
        $rules = [
            'adjustment_id' => ['required', 'integer', 'digits_between:1,10', 'exists:employee_attendance_adjustments,id'],
            'rejection_reason' => ['required', 'max:500'],
        ];

        $admin_id = $request->admin_id;
        $employee_id = $request->admin_employee;
        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $admin = Employee::find($employee_id);
            if ($admin) {
                $employee_leaves = EmployeeAttendanceAdjustment::where('id', $request->adjustment_id)->where('reporter_id', $employee_id);
                if ($employee_leaves->exists()) {
                    $employee_leaves = $employee_leaves->first();
                    $employee_leaves->status = 3;
                    $employee_leaves->rejected_reason = $request->rejection_reason;
                    $employee_leaves->updated_by = $admin_id;
                    $employee_leaves->save();
                    $notify = false;
                    if ($employee_leaves->employee_type_id == 1) {
                        $admin = Admin::where('employee_id', $employee_leaves->employee_id)->first();
                        if ($admin) {
                            $user_id = $admin->id;
                            $notify = true;
                        }
                    } else if ($employee_leaves->employee_type_id == 2) {
                        $rider = Rider::where('employee_id', $employee_leaves->employee_id)->first();
                        if ($rider) {
                            $user_id = $rider->id;
                            $notify = true;
                        }
                    }
                    if ($notify) {
                        NotificationsController::app_notification(17, $user_id, $employee_leaves->employee_type_id, $employee_leaves->id);
                    }
                    return response()->json(['status' => 0, 'message' => "Adjustment request has been rejected!"]);
                }
                return response()->json(['status' => 1, 'message' => "No Leave Found!"]);
            }
        }
    }

    public function login_v4(Request $request)
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
            $user = Admin::where('phone_number', substr_replace($request->input('phone_number'), '-', 4, 0))->orWhere('official_phone_number', substr_replace($request->input('phone_number'), '-', 4, 0));
            if ($user->exists()) {
                $user = $user->first();
                if ($user->status == 0) {

                    if ($user->first_login == 0) {
                        return response()->json(['status' => 1, 'message' => "Dear " . $user->name . "- Your request is in process and is pending for approval from HR."]);
                    } else {
                        return response()->json(['status' => 1, 'message' => 'Account disabled, Please contact admin!']);
                    }
                }
                if (Hash::check($request->input('pin'), $user->password)) {
                    $employee = Employee::where('trax_id', $user->trax_id);
                    $information = array();

                    $information['id'] = $user->id;
                    $information['name'] = $user->name;
                    $information['phone'] = $user->phone_number;
                    $information['cnic'] = $user->cnic;
                    $information['cargo_user'] = (in_array($user->role_id, [11, 10, 15, 55, 23, 33, 46])) ? 1 : 0;
                    if ($user->designation_id) {
                        $user_department = $user->Edesignation->department_id;
                    } else {
                        $user_department = $user->role->department_id;
                    }
                    $information['sales_person'] = ($user_department == 7) ? 1 : 0;
                    if ($employee->exists()) {
                        $employee = $employee->first();
                        $information['address'] = ($employee->address) ? $employee->address : "";
                    } else {
                        $information['address'] = '';
                    }
                    $information['role'] = 'staff';
                    if ($request->has('device_token')) {
                        EmployeeDeviceToken::where('device_token', $request->get('device_token'))->delete();
                        EmployeeDeviceToken::where('employee_type_id', 1)->where('employee_id', $user->id)->delete();
                        $employee_device_token = new EmployeeDeviceToken();
                        $employee_device_token->employee_id = $user->id;
                        $employee_device_token->employee_type_id = 1;
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
                    } else {
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

                    $information['welcome_bit'] = 0;
                    if (!$user->first_login) {
                        $information['welcome_bit'] = 1;
                        $information['welcome_message'] = "Welcome to TRAX " . $user->name;
                    }
                    $user->first_login = 1;
                    $user->save();
                    return response()->json(['status' => 0, 'message' => 'Logged In Successfully', 'information' => $information]);
                } else {
                    return response()->json(['status' => 1, 'message' => 'Invalid PIN!']);
                }
            } else {
                $employee = Employee::whereIn('request_status_id', [1, 2])->where('employee_type_id', 1)->where('phone_number', substr_replace($request->input('phone_number'), '-', 4, 0))->orWhere('official_phone_number', substr_replace($request->input('phone_number'), '-', 4, 0));
                if ($employee->exists()) {
                    $employee = $employee->first();
                    return response()->json(['status' => 1, 'message' => "Dear " . $employee->name . "- Your request is in process and is pending for approval from HR."]);
                } else {
                    return response()->json(['status' => 1, 'message' => 'Invalid Credentials']);
                }
            }
        }
    }

    public function admin_attachments_check(Request $request)
    {
        $rules = [
            //Attachments
            'employee_id' => ['required', 'integer', 'digits_between:1,10', 'exists:employees,id'],
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $data = array();
            $employee_id = $request->employee_id;
            $attachments = EmployeeAttachment::where('employee_id', $employee_id);
            if ($attachments->exists()) {
                $attachments = $attachments->first();
                $data["cv"] = ($attachments->cv != NULL) ? 1 : 0;
                $data["cnic"] = ($attachments->cnic != NULL) ? 1 : 0;
                $data["photo"] = ($attachments->photo != NULL) ? 1 : 0;
                $data["academic"] = ($attachments->academic != NULL) ? 1 : 0;
                $data["experience"] = ($attachments->experience != NULL) ? 1 : 0;
                $data["last_pay_slip"] = ($attachments->last_pay_slip != NULL) ? 1 : 0;
                $data["nikkah_nama"] = ($attachments->nikkah_nama != NULL) ? 1 : 0;
                $data["cnic_spouse"] = ($attachments->cnic_spouse != NULL) ? 1 : 0;
                $data["child_b_form"] = ($attachments->child_b_form != NULL) ? 1 : 0;
                $data["cnic_nominee"] = ($attachments->cnic_nominee != NULL) ? 1 : 0;
                $data["utility_bill"] = ($attachments->utility_bill != NULL) ? 1 : 0;
                $data["affidavit"] = ($attachments->affidavit != NULL) ? 1 : 0;
                $data["cheque"] = ($attachments->cheque != NULL) ? 1 : 0;

                return response()->json(['status' => 0, "data" => $data]);
            } else {
                return response()->json(['status' => 1, "message" => "Attachment Not Found!"]);
            }
        }
    }

    public function get_line_managers(Request $request)
    {
        $rules = [
            //Attachments
            'department_id' => ['required', 'integer', 'digits_between:1,10'],
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $line_managers = $line_managers = Employee::leftjoin('cities as c', 'c.id', 'employees.city_id')
                ->leftjoin('cities as h', 'h.id', 'c.hub_id')
                ->where('is_line_manager', 1)
                ->where('department_id', $request->department_id)
                ->select(['employees.name as name', 'employees.trax_id as trax_id', 'employees.id as id', 'h.name as hub as hub']);
            if ($line_managers->exists()) {
                $line_managers = $line_managers->get();
                $data = array();
                foreach ($line_managers as $line_manager) {
                    $datum = array();
                    $datum["id"] = $line_manager->id;
                    $datum["name"] = $line_manager->name . " " . "(" . $line_manager->trax_id . " | " . $line_manager->hub . ")";
                    $data[] = $datum;
                }
                return response()->json(['status' => 0, 'line_managers' => $data]);
            }
            return response()->json(['status' => 1, 'message' => "No Line Manager Found"]);
        }

    }

    public function leads_list_v2(Request $request)
    {
        $admin_id = $request->admin_id;
        $admin = Admin::find($admin_id);

        $lead_statuses = LeadStatus::whereNotIn('id', [3, 11, 12])->select('id', 'name')->get();
        $cities = City::where('business_category_id', 1)->where('status', 1)->get();

        $leads = Lead::join('cities as c', 'c.id', '=', 'leads.city_id')
            ->leftjoin('lead_statuses as ls', 'ls.id', '=', 'leads.status_id')
            ->leftjoin('service_list as sl', 'sl.id', '=', 'leads.service_id')
            ->leftjoin('admins as ad', 'ad.id', '=', 'leads.sale_person_id')
            ->select('leads.id as lead_id', 'leads.contact_person as contact_person', 'leads.phone_number as phone_number', 'leads.email_address as email_address', 'leads.requested_date as requested_date', 'leads.message as message', 'leads.status_id as status_id', 'ls.name as status', 'c.name as city', 'sl.name as service', 'leads.brand as brand', 'ad.name as sale_person');

        if ($admin->role_id != 4 && $admin->role_id != 44 && $admin->role_id != 60) {
            $leads = $leads->where('leads.sale_person_id', $admin_id)->wherenotin('leads.status_id', [3, 11, 12]);
        }

        if ($request->lead_id) {
            $leads = $leads->where('leads.id', $request->lead_id);
        }
        if ($request->city_id) {
            $leads = $leads->where('leads.city_id', $request->city_id);
        }
        if ($request->status_id) {
            $leads = $leads->where('leads.status_id', $request->status_id);
        }
        if ($request->date_from) {
            if ($request->date_to) {
                $from = $request->date_from . ' 00:00:00';
                $to = $request->date_to . ' 23:59:59';
                $leads = $leads->whereBetween('leads.requested_date', [$from, $to]);
            } else {
                $leads = $leads->whereDate('leads.requested_date', $request->date_from);
            }
        }
        if ($leads->exists()) {
            $leads->orderBy('leads.requested_date', "DESC");
            $leads = $leads->paginate(10);
            $data = array();
            foreach ($leads as $lead) {
                $datum = array();
                $datum["lead_id"] = $lead->lead_id;
                $datum["contact_person"] = $lead->contact_person;
                $datum["phone_number"] = $lead->phone_number;
                $datum["email_address"] = $lead->email_address;
                $datum["requested_date"] = $lead->requested_date;
                $datum["message"] = ($lead->message != null) ? $lead->message : "";
                $datum["status"] = $lead->status;
                $datum["status_id"] = $lead->status_id;
                $datum["city"] = $lead->city;
                $datum["service"] = $lead->service;
                $datum["brand"] = $lead->brand;
                $datum["sale_person"] = ($lead->sale_person != null) ? $lead->sale_person : "";
                $data[] = $datum;
            }
            return response()->json(['status' => 0, 'message' => "Leads Found!", 'data' => $data, 'cities' => $cities, 'lead_status' => $lead_statuses]);
        } else {
            return response()->json(['status' => 0, 'message' => "No data found!", 'cities' => $cities, 'lead_status' => $lead_statuses]);
        }
    }

    public function leave_index_v2(Request $request)
    {
        if ($request->has('leave_id')) {
            $leave = EmployeeLeave::find($request->leave_id);
            if ($leave) {
                $employee = Employee::find($leave->employee_id);
                if ($employee) {
                    if ($employee->employee_gender_id == 1) {
                        if ($employee->religion_id == 1) {
                            $leave_types = LeaveType::where('id', '<>', 2)->select('id', 'name')->get();
                        } else {
                            $leave_types = LeaveType::whereIn('id', [1, 3, 5, 6])->select('id', 'name')->get();
                        }
                    } else {
                        if ($employee->religion_id == 1) {
                            $leave_types = LeaveType::where('id', '<>', 3)->select('id', 'name')->get();
                        } else {
                            $leave_types = LeaveType::whereIn('id', [1, 2, 5, 6])->select('id', 'name')->get();
                        }
                    }
                    if ($employee->line_manager_id != null) {
                        $data = array();
                        $data['trax_id'] = $employee->trax_id;
                        $data['name'] = $employee->name;
                        $data['designation'] = $employee->designation->name;
                        $data['department'] = $employee->department->name;
                        $data['approver_email'] = $employee->line_manager->email;
                        $data['approver_name'] = $employee->line_manager->name;
                        $data['user_type'] = 0;
                        if ($employee->is_line_manager) {
                            $data['user_type'] = ($employee->designation_id == 68) ? 2 : 1;
                        }
                        return response()->json(['status' => 0, 'data' => $data, 'leave_types' => $leave_types]);
                    }
                    return response()->json(['status' => 1, 'message' => "Line Manager is not selected!"]);
                }
                return response()->json(['status' => 1, 'message' => "Employee Not Found"]);
            }
            return response()->json(['status' => 1, 'message' => "Invalid Leave ID"]);
        } else {
            $employee = Employee::where('trax_id', $request->trax_id);
            if ($employee->exists()) {
                $employee = $employee->first();
                if ($employee->employee_gender_id == 1) {
                    if ($employee->religion_id == 1) {
                        $leave_types = LeaveType::where('id', '<>', 2)->select('id', 'name')->get();
                    } else {
                        $leave_types = LeaveType::whereIn('id', [1, 3, 5, 6])->select('id', 'name')->get();
                    }
                } else {
                    if ($employee->religion_id == 1) {
                        $leave_types = LeaveType::where('id', '<>', 3)->select('id', 'name')->get();
                    } else {
                        $leave_types = LeaveType::whereIn('id', [1, 2, 5, 6])->select('id', 'name')->get();
                    }
                }
                if ($employee->line_manager_id != null) {
                    $data = array();
                    $data['trax_id'] = $employee->trax_id;
                    $data['name'] = $employee->name;
                    $data['designation'] = $employee->designation->name;
                    $data['department'] = $employee->department->name;
                    $data['approver_email'] = $employee->line_manager->email;
                    $data['approver_name'] = $employee->line_manager->name;
                    $data['user_type'] = 0;
                    if ($employee->is_line_manager) {
                        $data['user_type'] = ($employee->designation_id == 68) ? 2 : 1;
                    }
                    return response()->json(['status' => 0, 'data' => $data, 'leave_types' => $leave_types]);
                }
                return response()->json(['status' => 1, 'message' => "Line Manager is not selected!"]);
            }
            return response()->json(['status' => 1, 'message' => "Employee not found"]);
        }
    }

    public function leave_apply_v2(Request $request)
    {
        $rules = [
            'from' => ['required'],
            'to' => ['required'],
            'reason' => ['required', 'max:500'],
            'leave_type' => ['required', 'integer', 'digits_between:1,10', 'exists:leave_types,id'],
            'leave_id' => ['nullable', 'integer', 'digits_between:1,10', 'exists:employee_leaves,id'],
        ];
        $validate = Validator::make($request->all(), $rules, $this->messages);
        $validate->setAttributeNames($this->names);
        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $employee = Employee::where('trax_id', $request->trax_id);
            $leave_type = LeaveType::find($request->leave_type);
            if ($employee->exists()) {
                $employee = $employee->first();
                $working_days = $employee->department->working_days;
                $from_date = Carbon::parse($request->from);
                $to_date = Carbon::parse($request->to);
                if ($request->leave_type == 1) {
                    //checking for fiscal year start
                    $start_year = Carbon::today()->month(7)->startOfMonth();
                    $end_year = Carbon::today()->month(6)->endOfMonth();
                    if (Carbon::now() > $start_year) {
                        $end_year = $end_year->addYear(1);
                    } else {
                        $start_year = $start_year->subYear(1);
                    }
                    if (!($from_date >= $start_year && $to_date <= $end_year)) {
                        return response()->json(['status' => 1, 'message' => 'Leave Request Can\'t be approve']);
                    }
                    //checking for fiscal year end
                }
                if ($working_days == 1) {
                    $diffDays = $from_date->diffInWeekdays($to_date, Carbon::setWeekendDays([Carbon::SUNDAY]));
                } else {
                    $diffDays = $from_date->diffInWeekdays($to_date, Carbon::setWeekendDays([Carbon::SATURDAY, Carbon::SATURDAY]));
                }
                $diffDays++;
                if ($diffDays <= 56) {
                    if ($request->leave_type == 1) {
                        if ($employee->leave_count < $diffDays) {
                            return response()->json(['status' => 1, 'message' => 'Exceed Quota: Dear user, Your limit for applying leaves is greater than your available Annual Quota.']);
                        } else {
                            $employee->leave_count = $employee->leave_count - $diffDays;
                        }
                    }
                    if ($request->leave_type == 2) {
                        if ($employee->employee_gender_id == 1) {
                            return response()->json(['status' => 1, 'message' => 'Maternity for males : Your gender doesn\'t allow to apply this leave category.']);
                        }
                    }
                    if ($request->leave_type == 3) {
                        if ($employee->employee_gender_id == 2 || $diffDays > $leave_type->count) {
                            return response()->json(['status' => 1, 'message' => 'Your gender doesn\'t allow to apply this leave category.']);
                        }
                    }
                    if ($request->leave_type == 4) {
                        if ($employee->religion_id != 1 || $diffDays > $leave_type->count) {
                            return response()->json(['status' => 1, 'message' => 'Leave Request Can\'t be approve']);
                        }
                    }
                    if ($request->leave_type == 5) {
                        if ($diffDays > $leave_type->count) {
                            return response()->json(['status' => 1, 'message' => 'Exceed Quota: Dear user, Your limit can\'t be exceed from ' . $leave_type->count . ' days']);
                        }
                    }

                    if (!$employee->line_manager_id) {
                        return response()->json(['status' => 1, 'message' => 'Line Manager is not selected!']);
                    }

                    if ($request->has('leave_id')) {
                        $leave_request = EmployeeLeave::where('id', $request->leave_id);
                        if ($leave_request->exists()) {
                            $leave_request = $leave_request->first();
                            $leave_request->from = $request->from;
                            $leave_request->to = $request->to;
                            $leave_request->applied_reason = $request->reason;
                            $leave_request->leave_type = $request->leave_type;
                            $leave_request->save();
                            $message = "Leave Request edited successfully";
                        } else {
                            return response()->json(['status' => 1, 'message' => 'Invalid Leave Request ID']);
                        }
                    } else {
                        $leave = EmployeeLeave::where('employee_id', $employee->id)->where('employee_type_id', 1)->whereIn('status', [1, 2]);
                        if ($leave->exists()) {
                            return response()->json(['status' => 1, 'message' => 'Leave Request Already Submitted & Pending for Approval']);
                        }
                        $leave_request = new EmployeeLeave();
                        $leave_request->employee_id = $employee->id;;
                        $leave_request->employee_type_id = 1;
                        $leave_request->reporter_id = $employee->line_manager->admin->id;
                        $leave_request->from = $request->from;
                        $leave_request->to = $request->to;
                        $leave_request->applied_reason = $request->reason;
                        $leave_request->leave_type = $request->leave_type;
                        $leave_request->save();
                        $message = "Leave Request submitted successfully";
                    }
                    $employee->save();
                    NotificationsController::app_notification(11, $request->admin_id, 1, $leave_request->id);
                    NotificationsController::app_notification(12, $leave_request->reporter_id, 1, $leave_request->id);
                    return response()->json(['status' => 0, 'apply_message' => $message]);
                } else {
                    return response()->json(['status' => 1, 'message' => 'Exceed Quota: Dear user, Your limit can\'t be exceed from 56 days.']);
                }
            }
            return response()->json(['status' => 1, 'message' => 'Employee not Found!']);
        }
    }

    public function employee_leave_list_v2(Request $request)
    {
        if (!$request->has('admin_employee')) {
            return response()->json(['status' => 1, 'message' => "No Leave Found!"]);
        }
        $admin_employee = $request->admin_employee;
        $employee_leaves = EmployeeLeave::join('leave_statuses as ls', 'employee_leaves.status', '=', 'ls.id')
            ->join('leave_types as lt', 'employee_leaves.leave_type', '=', 'lt.id')
            ->select('employee_leaves.id as id', 'employee_leaves.from as from', 'employee_leaves.to as to', 'employee_leaves.applied_reason as applied_reason', 'employee_leaves.rejected_reason as rejected_reason', 'employee_leaves.status as status_id', 'ls.name as status', 'lt.name as leave_type', 'lt.id as leave_type_id')
            ->where('employee_id', $admin_employee)
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
                $datum['leave_type'] = $employee_leave->leave_type;
                $datum['leave_type_id'] = $employee_leave->leave_type_id;
                if ($employee_leave->to) {
                    $start_date = Carbon::createFromFormat('Y-m-d', $employee_leave->from);
                    $end_date = Carbon::createFromFormat('Y-m-d', $employee_leave->to);
                    $datum['days_count'] = $start_date->diffInDays($end_date) + 1;
                } else {
                    $datum['days_count'] = 1;
                }
                $data[] = $datum;
            }
            return response()->json(['status' => 0, 'response' => $data]);
        }
        return response()->json(['status' => 1, 'message' => "No Leave Found!"]);
    }

    public function approver_leave_list_v2(Request $request)
    {
        $admin_id = $request->admin_id;
        $admin = Admin::find($admin_id);
        $is_hr = false;
        $is_hod = false;
        $is_line_manger = false;
        if ($admin) {
            $admin_role = $admin->role_id;
            $department_head_ids = AdminDepartment::pluck('department_head_id')->toArray();
            $employee = $admin->employee;
            if (!$employee) {
                return response()->json(['status' => 1, 'message' => "Employee profile not found!"]);
            }
            if (in_array($admin_id, $department_head_ids)) {
                $employee_leaves = EmployeeLeave::join('leave_statuses as ls', 'employee_leaves.status', '=', 'ls.id')
                    ->join('employees as emp', 'employee_leaves.employee_id', '=', 'emp.id')
                    ->join('leave_types as lt', 'employee_leaves.leave_type', '=', 'lt.id')
                    ->select('employee_leaves.id as id', 'employee_leaves.from as from', 'employee_leaves.to as to', 'employee_leaves.applied_reason as applied_reason', 'employee_leaves.status as status_id', 'ls.name as status', 'employee_leaves.employee_id as employee_id', 'employee_leaves.employee_type_id as type_id', 'employee_leaves.rejected_reason as rejected_reason', 'lt.name as leave_type', 'lt.id as leave_type_id')
                    ->where(function ($query) use ($admin_id) {
                        $query->where('employee_leaves.reporter_id', $admin_id);
                    })
                    ->orwhere(function ($query) use ($employee) {
                        $query->where('employee_leaves.status', 6)
                            ->where('employee_leaves.leave_type', '<>', 1)
                            ->where('emp.department_id', $employee->department_id);
                    });
                $is_hod = true;
            } elseif ($admin->employee->is_line_manager) {
                $employee_leaves = EmployeeLeave::join('leave_statuses as ls', 'employee_leaves.status', '=', 'ls.id')
                    ->join('leave_types as lt', 'employee_leaves.leave_type', '=', 'lt.id')
                    ->select('employee_leaves.id as id', 'employee_leaves.from as from', 'employee_leaves.to as to', 'employee_leaves.applied_reason as applied_reason', 'employee_leaves.status as status_id', 'ls.name as status', 'employee_leaves.employee_id as employee_id', 'employee_leaves.employee_type_id as type_id', 'employee_leaves.rejected_reason as rejected_reason', 'lt.name as leave_type', 'lt.id as leave_type_id')
                    ->where('employee_leaves.reporter_id', $admin_id);
                $is_line_manger = true;
            } elseif (in_array($admin_role, [63, 69, 70])) {
                $employee_leaves = EmployeeLeave::join('leave_statuses as ls', 'employee_leaves.status', '=', 'ls.id')
                    ->join('leave_types as lt', 'employee_leaves.leave_type', '=', 'lt.id')
                    ->select('employee_leaves.id as id', 'employee_leaves.from as from', 'employee_leaves.to as to', 'employee_leaves.applied_reason as applied_reason', 'employee_leaves.status as status_id', 'ls.name as status', 'employee_leaves.employee_id as employee_id', 'employee_leaves.employee_type_id as type_id', 'employee_leaves.rejected_reason as rejected_reason', 'lt.name as leave_type', 'lt.id as leave_type_id')
                    ->where('employee_leaves.status', 2);
                $is_hr = true;
            } else {
                return response()->json(['status' => 1, 'message' => "Invalid Role"]);
            }
            if ($employee_leaves->exists()) {
                $employee_leaves = $employee_leaves->get();
                $data = array();
                foreach ($employee_leaves as $employee_leave) {
                    $leave_employee = $employee_leave->employee;
                    $datum = array();
                    $datum['id'] = $employee_leave->id;
                    $datum['from'] = $employee_leave->from;
                    $datum['to'] = $employee_leave->to;
                    $datum['applied_reason'] = $employee_leave->applied_reason;
                    $datum['rejected_reason'] = $employee_leave->rejected_reason;
                    $datum['status_id'] = $employee_leave->status_id;
                    $datum['status'] = $employee_leave->status;
                    $datum['leave_type'] = $employee_leave->leave_type;
                    if ($is_hod) {
                        if ($employee_leave->status_id == 6) {
                            $datum['role'] = 1;
                        } else {
                            $datum['role'] = 2;
                        }
                    } elseif ($is_line_manger) {
                        $datum['role'] = 2;
                    } elseif ($is_hr) {
                        $datum['role'] = 0;
                    }
                    if ($employee_leave->to) {
                        $start_date = Carbon::createFromFormat('Y-m-d', $employee_leave->from);
                        $end_date = Carbon::createFromFormat('Y-m-d', $employee_leave->to);
                        $datum['days_count'] = $start_date->diffInDays($end_date) + 1;
                    } else {
                        $datum['days_count'] = 1;
                    }
                    $datum['name'] = $leave_employee->name;
                    $datum['trax_id'] = $leave_employee->trax_id;
                    if ($employee_leave->type_id == 1) {
                        $datum['designation'] = $leave_employee->designation->name;
                    } elseif ($employee_leave->type_id == 2) {
                        $datum['designation'] = "Rider";
                    }
                    $data[] = $datum;
                }
                return response()->json(['status' => 0, 'approver_response' => $data]);
            }
            return response()->json(['status' => 1, 'message' => "No Pending Leave For Approval!"]);
        }
    }

    public function leave_approve_v2(Request $request)
    {
        $admin_id = $request->admin_id;
        $admin = Admin::find($admin_id);
        if ($admin) {
            $employee_leaves = EmployeeLeave::where('id', $request->leave_id);
            if ($employee_leaves->exists()) {
                $employee_leaves = $employee_leaves->first();
                $department_head_ids = AdminDepartment::pluck('department_head_id')->toArray();
                if (in_array($employee_leaves->status, [1, 2, 3])) {
                    if ($admin->employee->is_line_manager) {
                        if (in_array($admin_id, $department_head_ids)) {
                            $employee_leaves->status = 2;
                        } else {
                            $employee_leaves->status = 6;
                        }
                        if ($employee_leaves->leave_type != 1) {
                            $employee_leaves->updated_by = $admin_id;
                            $employee_leaves->save();
                            NotificationsController::app_notification(11, $employee_leaves->employee->admin->id, $employee_leaves->employee_type_id, $employee_leaves->id);
                            return response()->json(['status' => 0, 'message' => "Leave Approved Successfully!"]);
                        }
                    } else {
                        $employee_leaves->status = 4;
                    }
                    $employee_leaves->updated_by = $admin_id;
                    $user = Employee::find($employee_leaves->employee_id);
                    if (!$user) {
                        return response()->json(['status' => 1, 'message' => "Invalid Employee"]);
                    }
                    $shift = EmployeeShift::find($user->shift_id);
                    if ($employee_leaves->to) {
                        $dates = AdminAPIController::generateDateRange($employee_leaves->from, $employee_leaves->to);
                    } else {
                        $dates[] = $employee_leaves->from;
                    }
                    foreach ($dates as $date) {
                        $date = Carbon::parse($date)->format("Y-m-d");
                        $mark_attendance = EmployeeAttendance::where('employee_id', $employee_leaves->employee_id)
                            ->where('employee_type', $employee_leaves->employee_type_id)
                            ->whereDate('attendance_date', $date);
                        if ($mark_attendance->exists()) {
                            $mark_attendance = $mark_attendance->first();
                        } else {
                            $mark_attendance = new EmployeeAttendance();
                        }
                        $mark_attendance->attendance_date = $date;
                        $mark_attendance->employee_id = $employee_leaves->employee_id;
                        $mark_attendance->employee_type = $employee_leaves->employee_type_id;
                        $mark_attendance->clock_in_latitude = "24.85758065592256";
                        $mark_attendance->clock_in_longitude = "67.12476908400743";
                        $mark_attendance->clock_out_latitude = "24.85758065592256";
                        $mark_attendance->clock_out_longitude = "67.12476908400743";
                        if ($shift) {
                            $mark_attendance->clock_in_datetime = $date . ' ' . $shift->start_time;
                            $mark_attendance->clock_out_datetime = $date . ' ' . $shift->end_time;
                        } else {
                            $mark_attendance->clock_in_datetime = $date . ' 09:00:00';
                            $mark_attendance->clock_out_datetime = $date . ' 18:00:00';
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

                        $employee_leaves->save();
                    }
                    NotificationsController::app_notification(11, $employee_leaves->employee->admin->id, $employee_leaves->employee_type_id, $employee_leaves->id);
                    return response()->json(['status' => 0, 'message' => "Leave Approved Successfully!"]);
                } else {
                    return response()->json(['status' => 1, 'message' => 'Leave Already Approved']);
                }
            }
            return response()->json(['status' => 1, 'message' => 'Invalid Leave ID']);
        }
    }

    public function hod_approve(Request $request)
    {
        $leave_id = $request->leave_id;
        $leave = EmployeeLeave::find($leave_id);
        $leave->updated_by = $request->admin_id;
        $leave->status = 2;
        $leave->save();
        NotificationsController::app_notification(11, $leave->employee->admin->id, $leave->employee_type_id, $leave->id);
        return response()->json(['status' => 0, 'message' => 'Leave Approved Successfully']);
    }

    public function leave_reject_v2(Request $request)
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
                $employee_leaves = EmployeeLeave::where('id', $request->leave_id);

                if ($employee_leaves->exists()) {
                    $employee_leaves = $employee_leaves->first();
                    $admin_profile = $employee_leaves->employee;
                    if ($employee_leaves->status == 2) {
                        $employee_leaves->status = 5;
                    } elseif ($employee_leaves->status == 1) {
                        if ($employee_leaves->leave_type == 1) {
                            $working_days = $admin_profile->department->working_days;

                            $old_start_date = Carbon::createFromFormat('Y-m-d', $employee_leaves->from);
                            $old_end_date = Carbon::createFromFormat('Y-m-d', $employee_leaves->to);

                            if ($working_days == 1) {
                                $old_diffDays = $old_start_date->diffInWeekdays($old_end_date, Carbon::setWeekendDays([Carbon::SUNDAY]));
                            } else {
                                $old_diffDays = $old_start_date->diffInWeekdays($old_end_date, Carbon::setWeekendDays([Carbon::SATURDAY, Carbon::SUNDAY]));
                            }
                            $old_diffDays++;
                            $admin_profile->leave_count = $admin_profile->leave_count + $old_diffDays;
                            $admin_profile->save();
                        }
                        $employee_leaves->status = 7;
                    } else if (in_array($employee_leaves->status == 6)) {
                        $employee_leaves->status = 3;
                    } else {
                        return response()->json(['status' => 1, 'message' => "Invalid Role"]);
                    }
                    $employee_leaves->rejected_reason = $request->rejection_reason;
                    $employee_leaves->updated_by = $admin_id;
                    $employee_leaves->save();
                    NotificationsController::app_notification(11, $employee_leaves->employee->admin->id, $employee_leaves->employee_type_id, $employee_leaves->id);
                    return response()->json(['status' => 0, 'message' => "Leave request has been rejected!"]);
                }
                return response()->json(['status' => 1, 'message' => "No Leave Found!"]);
            }
        }
    }

    public function return_create_index(Request $request)
    {
        $role_id = $request->admin_role_id;
        $admin_id = $request->admin_id;
        $admin_hubs = AdminHub::where('admin_id', $admin_id)->pluck('hub_id')->toArray();
        $routes = Route::where('status', 1);
        $hubs = City::where([['status', 1], ['hub', 1]]);
        if ($role_id != 1) {
            $routes = $routes->whereHas('city', function ($query) use ($admin_hubs) {
                $query->whereIn('hub_id', $admin_hubs);
            });
            $hubs = $hubs->WhereIn('id', session('hubs'));
        }
        $routes = $routes->get();
        $hubs = $hubs->get(['id', 'name']);
        return response()->json(['status' => 0, 'routes' => $routes, 'hubs' => $hubs]);
    }

    public function get_riders_by_hub(Request $request)
    {
        $rules = [
            'hub_id' => ['required', 'integer', 'digits_between:1,10', 'exists:cities,id']
        ];
        $validate = Validator::make($request->all(), $rules, $this->messages);
        $validate->setAttributeNames($this->names);
        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $hub_id = $request->hub_id;
            $riders = Rider::where('status', 1)
                ->whereHas('city', function ($query) use ($hub_id) {
                    $query->where('hub_id', $hub_id);
                })->select('id', 'name', 'route_id', 'trax_id');
            if ($riders->exists()) {
                $riders = $riders->get();
                $data = array();
                foreach ($riders as $rider) {
                    $datum = array();
                    $datum["id"] = $rider->id;
                    $datum["name"] = $rider->name;
                    $datum["route_id"] = $rider->route_id;
                    $datum["trax_id"] = $rider->trax_id;
                    $datum["router_name"] = ($rider->route_id) ? $rider->route->code . " (" . $rider->route->start . " to " . $rider->route->end . ")" : NULL;
                    $data[] = $datum;
                }
                return response()->json(['status' => 0, 'riders' => $data]);
            }
            return response()->json(['status' => 1, 'message' => "Riders not found!"]);
        }
    }

    public function get_shipment_details(Request $request)
    {
        $rules = [
            'tracking' => ['required']
        ];
        $validate = Validator::make($request->all(), $rules, $this->messages);
        $validate->setAttributeNames($this->names);
        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $role_id = $request->admin_role_id;
            $admin_id = $request->admin_id;
            $admin_hubs = AdminHub::where('admin_id', $admin_id)->pluck('hub_id')->toArray();
            $different_city_statuses_2 = array(22, 24, 27, 29, 33, 35, 42, 44, 45, 46, 47, 48, 60);
            $different_city_statuses = array(22, 24, 27, 29, 33, 35, 37, 42, 44, 45, 46, 47, 48, 60);
            $allowed_statuses = array(20, 22, 24, 27, 29, 30, 33, 35, 37, 42, 44, 45, 46, 47, 48, 60);
            $return_note_statuses = array(20, 22, 24, 27, 29, 30, 33, 35, 37, 42, 44, 45, 46, 47, 48, 60);
            $shipment = Shipment::where('tracking_number', $request->tracking)->whereIn('shipper_status_id', $allowed_statuses);
            $status = '';
            if ($shipment->exists()) {
                $shipment = $shipment->first();
                $dispute_check = CheckDisputeShipmentsController::check($shipment->id);
                if (!$dispute_check) {
                    return response()->json(['status' => 1, 'message' => 'Shipment is in Dispute! For further assistance, please contact QA (CX)']);
                }
                ShipmentScanningJourneyController::add($shipment->id, 7, 1, $admin_id, null, null);
                if ($request->shipper_id != null) {
                    $mandatory_shipper = ReturnReasonMandatoryShipper::pluck('shipper_id')->toArray();
                    if ($request->shipper_id != $shipment->user_id) {
                        if (in_array($request->shipper_id, $mandatory_shipper) || in_array($shipment->user_id, $mandatory_shipper)) {
                            return response()->json(['status' => 1, 'message' => 'Different Shipper, scan shipments of same shipper!.']);
                        }
                    }
                }
                if ($shipment->return_address_id != NULL) {
                    $destination_id = $shipment->return_address->city_id;
                } else {
                    $destination_id = $shipment->pickup_address->city_id;
                }
                $destination_id = City::where('id', $destination_id)->select('hub_id')->first();
                $destination_id = $destination_id->hub_id;//first it was origin now for return its destination
                if ($role_id == 1 || in_array($destination_id, $admin_hubs)) {
                    $origin = $shipment->consignee_city->hub_id;//let's suppose consignee city is origin now
                    if (!$request->has('hub_id')) {
                        if ($destination_id == $origin && (in_array($shipment->shipper_status_id, $return_note_statuses))) {
                            if ($shipment->return_address_id != NULL) {
                                $destination_city_id = $shipment->return_address->city_id;
                            } else {
                                $destination_city_id = $shipment->pickup_address->city_id;
                            }

                            $destination_city = City::find($destination_city_id);
                            if ($destination_city->id == $destination_city->hub_id) {
                                $destination = $destination_city->name;
                                $hub = $destination_city->id;
                            } else {
                                $hubid = $destination_city->hub_id;
                                $destinationHub = City::find($hubid);
                                $destination = $destinationHub->name;
                                $hub = $destinationHub->id;
                            }
                            $service = $shipment->booking_type->booking_type;
                            $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id);
                            if ($shipment_journey->exists()) {
                                $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->select('shipper_status_id', 'remarks')->latest()->first();
                                $status_id = ($shipment_journey->shipper_status_id) ? $shipment_journey->shipper_status_id : '';
                                if ($status_id != '') {
                                    $status_name = ShipmentStatus::where('id', $status_id)->select('name')->first();
                                    $status = $status_name->name;
                                } else {
                                    $status = ' - ';
                                }
                            }
                            if ($shipment->booking_type_id != 4) {
                                $settings = GlobalSettings::where('type', 'return_note_restriction_bypass');
                                if ($settings->exists()) {
                                    $settings = $settings->first();
                                    $role_ids = array_map('intval', explode(',', $settings->text));
                                } else {
                                    $role_ids = array();
                                }
                                array_push($role_ids, 1);

                                if (!in_array($role_id, $role_ids)) {
                                    if (!$shipment->packaging_material_request) {
                                        $shipper_payable = 0;
                                        $pending_payment = PendingPayment::where('user_id', $shipment->user_id);
                                        if ($pending_payment->exists()) {
                                            $pending_payment = $pending_payment->first();

                                            $pending_payment_shipments = PendingPaymentShipment::where('pending_payment_id', $pending_payment->id);
                                            if ($pending_payment_shipments->exists()) {
                                                $pending_payment_shipments = $pending_payment_shipments->get();
                                                foreach ($pending_payment_shipments as $pending_payment_shipment) {
                                                    $shipper_payable += $pending_payment_shipment->payable;
                                                }
                                            }
                                        }
                                        if ($shipper_payable < 0) {
                                            return response()->json(['status' => 1, 'message' => 'Shipper with Negative Balance, Contact Sales Team!']);
                                        }
                                    }
                                }
                            }
                            $crm_row = 0;
                            if (CrmRequest::where('shipment_id', $shipment->id)->where('case_nature_id', 1)->whereIn('status_id', [2, 3, 5])->exists()) {
                                $crm_row = 1;
                            }
                            if (!$request->has('pieces_confirm')) {
                                if ($shipment->booking_type_id == 1 && $shipment->pieces > 1) {
                                    $details = array();
                                    $shipment_pieces = ShipmentPiece::where('shipment_id', $shipment->id)->pluck('tracking_number')->toArray();

                                    $details['id'] = $shipment->id;
                                    $details['shipper_id'] = $shipment->user_id;
                                    $details['tracking_number'] = $shipment->tracking_number;
                                    $details['pieces_count'] = $shipment->pieces;
                                    $details['pieces_tracking_numbers'] = $shipment_pieces;
                                    return ['status' => 0, 'message' => 'Shipment Piece(s) found!', 'details' => $details, 'pieces_found' => 1];
                                }
                            }
                            return response()->json(['status' => 0, "shipment_details" => ['shId' => $shipment->id, 'tracking_number' => $shipment->tracking_number, 'destination' => $destination, 'hub' => $hub, 'consignee_name' => $shipment->consignee_name, 'phone' => $shipment->consignee_phone_number_1, 'address' => $shipment->consignee_address, 'amount' => number_format($shipment->amount), 'service_type' => $service, 'shipment_status' => $status, 'crm_row' => $crm_row, 'shipper_id' => $shipment->user_id]]);

                        } else
                            if ($destination_id != $origin && (in_array($shipment->shipper_status_id, $different_city_statuses))) {
                                if ($shipment->return_address_id != NULL) {
                                    $destination_city_id = $shipment->return_address->city_id;
                                } else {
                                    $destination_city_id = $shipment->pickup_address->city_id;
                                }

                                $destination_city = City::find($destination_city_id);
                                if ($destination_city->id == $destination_city->hub_id) {
                                    $destination = $destination_city->name;
                                    $hub = $destination_city->id;
                                } else {
                                    $hubid = $destination_city->hub_id;
                                    $destinationHub = City::find($hubid);
                                    $destination = $destinationHub->name;
                                    $hub = $destinationHub->id;
                                }

                                $service = $shipment->booking_type->booking_type;
                                $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id);
                                if ($shipment_journey->exists()) {
                                    $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->select('shipper_status_id', 'remarks')->latest()->first();
                                    $status_id = ($shipment_journey->shipper_status_id) ? $shipment_journey->shipper_status_id : '';
                                    if ($status_id != '') {
                                        $status_name = ShipmentStatus::where('id', $status_id)->select('name')->first();
                                        $status = $status_name->name;
                                    } else {
                                        $status = ' - ';
                                    }
                                }
                                if ($shipment->booking_type_id != 4) {
                                    $settings = GlobalSettings::where('type', 'return_note_restriction_bypass');

                                    if ($settings->exists()) {
                                        $settings = $settings->first();
                                        $role_ids = array_map('intval', explode(',', $settings->text));
                                        array_push($role_ids, 1);
                                        if (!in_array($role_id, $role_ids)) {
                                            if (!$shipment->packaging_material_request) {
                                                $shipper_payable = 0;
                                                $pending_payment = PendingPayment::where('user_id', $shipment->user_id);
                                                if ($pending_payment->exists()) {
                                                    $pending_payment = $pending_payment->first();

                                                    $pending_payment_shipments = PendingPaymentShipment::where('pending_payment_id', $pending_payment->id);
                                                    if ($pending_payment_shipments->exists()) {
                                                        $pending_payment_shipments = $pending_payment_shipments->get();
                                                        foreach ($pending_payment_shipments as $pending_payment_shipment) {
                                                            $shipper_payable += $pending_payment_shipment->payable;
                                                        }
                                                    }
                                                }
                                                if ($shipper_payable < 0) {
                                                    return response()->json(['status' => 1, 'message' => 'Shipper with Negative Balance, Contact Sales Team!']);
                                                }
                                            }
                                        }
                                    }
                                }
                                $crm_row = 0;
                                if (CrmRequest::where('shipment_id', $shipment->id)->where('case_nature_id', 1)->whereIn('status_id', [2, 3, 5])->exists()) {
                                    $crm_row = 1;
                                }
                                if (!$request->has('pieces_confirm')) {
                                    if ($shipment->booking_type_id == 1 && $shipment->pieces > 1) {
                                        $details = array();
                                        $shipment_pieces = ShipmentPiece::where('shipment_id', $shipment->id)->pluck('tracking_number')->toArray();

                                        $details['id'] = $shipment->id;
                                        $details['shipper_id'] = $shipment->user_id;
                                        $details['tracking_number'] = $shipment->tracking_number;
                                        $details['pieces_count'] = $shipment->pieces;
                                        $details['pieces_tracking_numbers'] = $shipment_pieces;
                                        return response()->json(['status' => 0, 'message' => 'Shipment Piece(s) found!', 'details' => $details, 'pieces_found' => 1]);
                                    }
                                }
                                return response()->json(['status' => 0, "shipment_details" => ['shipper_id' => $shipment->user_id, 'shId' => $shipment->id, 'tracking_number' => $shipment->tracking_number, 'destination' => $destination, 'hub' => $hub, 'consignee_name' => $shipment->consignee_name, 'phone' => $shipment->consignee_phone_number_1, 'address' => $shipment->consignee_address, 'amount' => number_format($shipment->amount), 'service_type' => $service, 'shipment_status' => $status, 'crm_row' => $crm_row]]);

                            } else {
                                return response()->json(['status' => 1, 'message' => 'Return Shipment not arrived at origin center yet.']);
                            }
                    } else
                        if ($request->has('hub_id') && ($destination_id == $request->hub_id)) {
                            $same_city_statuses = array(20, 24, 27, 29, 30, 33, 35, 37, 42, 44, 45, 46, 47, 48, 60);
                            if ($destination_id == $origin && (in_array($shipment->shipper_status_id, $same_city_statuses))) {
                                if ($shipment->return_address_id != NULL) {
                                    $destination_city_id = $shipment->return_address->city_id;
                                } else {
                                    $destination_city_id = $shipment->pickup_address->city_id;
                                }

                                $destination_city = City::find($destination_city_id);
                                if ($destination_city->id == $destination_city->hub_id) {
                                    $destination = $destination_city->name;
                                    $hub = $destination_city->id;
                                } else {
                                    $hubid = $destination_city->hub_id;
                                    $destinationHub = City::find($hubid);
                                    $destination = $destinationHub->name;
                                    $hub = $destinationHub->id;
                                }
                                $service = $shipment->booking_type->booking_type;
                                $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id);
                                if ($shipment_journey->exists()) {
                                    $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->select('shipper_status_id', 'remarks')->latest()->first();
                                    $status_id = ($shipment_journey->shipper_status_id) ? $shipment_journey->shipper_status_id : '';
                                    if ($status_id != '') {
                                        $status_name = ShipmentStatus::where('id', $status_id)->select('name')->first();
                                        $status = $status_name->name;
                                    } else {
                                        $status = ' - ';
                                    }
                                }
                                if ($shipment->booking_type_id != 4) {
                                    $settings = GlobalSettings::where('type', 'return_note_restriction_bypass');
                                    if ($settings->exists()) {
                                        $settings = $settings->first();
                                        $role_ids = array_map('intval', explode(',', $settings->text));
                                        array_push($role_ids, 1);
                                        if (!in_array($role_id, $role_ids)) {
                                            if (!$shipment->packaging_material_request) {
                                                $shipper_payable = 0;
                                                $pending_payment = PendingPayment::where('user_id', $shipment->user_id);
                                                if ($pending_payment->exists()) {
                                                    $pending_payment = $pending_payment->first();

                                                    $pending_payment_shipments = PendingPaymentShipment::where('pending_payment_id', $pending_payment->id);
                                                    if ($pending_payment_shipments->exists()) {
                                                        $pending_payment_shipments = $pending_payment_shipments->get();
                                                        foreach ($pending_payment_shipments as $pending_payment_shipment) {
                                                            $shipper_payable += $pending_payment_shipment->payable;
                                                        }
                                                    }
                                                }
                                                if ($shipper_payable < 0) {
                                                    return response()->json(['status' => 1, 'message' => 'Shipper with Negative Balance, Contact Sales Team!']);
                                                }
                                            }
                                        }
                                    }
                                }
                                $crm_row = 0;
                                if (CrmRequest::where('shipment_id', $shipment->id)->where('case_nature_id', 1)->whereIn('status_id', [2, 3, 5])->exists()) {
                                    $crm_row = 1;
                                }
                                if (!$request->has('pieces_confirm')) {
                                    if ($shipment->booking_type_id == 1 && $shipment->pieces > 1) {
                                        $details = array();
                                        $shipment_pieces = ShipmentPiece::where('shipment_id', $shipment->id)->pluck('tracking_number')->toArray();

                                        $details['id'] = $shipment->id;
                                        $details['tracking_number'] = $shipment->tracking_number;
                                        $details['pieces_count'] = $shipment->pieces;
                                        $details['pieces_tracking_numbers'] = $shipment_pieces;
                                        return response()->json(['status' => 0, 'message' => 'Shipment Piece(s) found!', 'details' => $details, 'pieces_found' => 1]);
                                    }
                                }
                                return response()->json(['status' => 0, "shipment_details" => ['shipper_id' => $shipment->user_id, 'shId' => $shipment->id, 'tracking_number' => $shipment->tracking_number, 'destination' => $destination, 'hub' => $hub, 'consignee_name' => $shipment->consignee_name, 'phone' => $shipment->consignee_phone_number_1, 'address' => $shipment->consignee_address, 'amount' => number_format($shipment->amount), 'service_type' => $service, 'shipment_status' => $status, 'crm_row' => $crm_row]]);

                            } else
                                if ($destination_id != $origin && (in_array($shipment->shipper_status_id, $different_city_statuses_2))) {
                                    if ($shipment->return_address_id != NULL) {
                                        $destination_city_id = $shipment->return_address->city_id;
                                    } else {
                                        $destination_city_id = $shipment->pickup_address->city_id;
                                    }
                                    $destination_city = City::find($destination_city_id);
                                    if ($destination_city->id == $destination_city->hub_id) {
                                        $destination = $destination_city->name;
                                        $hub = $destination_city->id;
                                    } else {
                                        $hubid = $destination_city->hub_id;
                                        $destinationHub = City::find($hubid);
                                        $destination = $destinationHub->name;
                                        $hub = $destinationHub->id;
                                    }

                                    $service = $shipment->booking_type->booking_type;
                                    $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id);
                                    if ($shipment_journey->exists()) {
                                        $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->select('shipper_status_id', 'remarks')->latest()->first();
                                        $status_id = ($shipment_journey->shipper_status_id) ? $shipment_journey->shipper_status_id : '';
                                        if ($status_id != '') {
                                            $status_name = ShipmentStatus::where('id', $status_id)->select('name')->first();
                                            $status = $status_name->name;
                                        } else {
                                            $status = ' - ';
                                        }
                                    }
                                    if ($shipment->booking_type_id != 4) {
                                        $settings = GlobalSettings::where('type', 'return_note_restriction_bypass');

                                        if ($settings->exists()) {
                                            $settings = $settings->first();
                                            $role_ids = array_map('intval', explode(',', $settings->text));
                                            array_push($role_ids, 1);
                                            if (!in_array($role_id, $role_ids)) {
                                                if (!$shipment->packaging_material_request) {
                                                    $shipper_payable = 0;
                                                    $pending_payment = PendingPayment::where('user_id', $shipment->user_id);
                                                    if ($pending_payment->exists()) {
                                                        $pending_payment = $pending_payment->first();

                                                        $pending_payment_shipments = PendingPaymentShipment::where('pending_payment_id', $pending_payment->id);
                                                        if ($pending_payment_shipments->exists()) {
                                                            $pending_payment_shipments = $pending_payment_shipments->get();
                                                            foreach ($pending_payment_shipments as $pending_payment_shipment) {
                                                                $shipper_payable += $pending_payment_shipment->payable;
                                                            }
                                                        }
                                                    }
                                                    if ($shipper_payable < 0) {
                                                        return response()->json(['status' => 1, 'message' => 'Shipper with Negative Balance, Contact Sales Team!']);
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    $crm_row = 0;
                                    if (CrmRequest::where('shipment_id', $shipment->id)->where('case_nature_id', 1)->whereIn('status_id', [2, 3, 5])->exists()) {
                                        $crm_row = 1;
                                    }
                                    if (!$request->has('pieces_confirm')) {
                                        if ($shipment->booking_type_id == 1 && $shipment->pieces > 1) {
                                            $details = array();
                                            $shipment_pieces = ShipmentPiece::where('shipment_id', $shipment->id)->pluck('tracking_number')->toArray();

                                            $details['id'] = $shipment->id;
                                            $details['tracking_number'] = $shipment->tracking_number;
                                            $details['pieces_count'] = $shipment->pieces;
                                            $details['pieces_tracking_numbers'] = $shipment_pieces;
                                            return response()->json(['status' => 0, 'message' => 'Shipment Piece(s) found!', 'details' => $details, 'pieces_found' => 1]);
                                        }
                                    }
                                    return response()->json(['status' => 0, "shipment_details" => ['shipper_id' => $shipment->user_id, 'shId' => $shipment->id, 'tracking_number' => $shipment->tracking_number, 'destination' => $destination, 'hub' => $hub, 'consignee_name' => $shipment->consignee_name, 'phone' => $shipment->consignee_phone_number_1, 'address' => $shipment->consignee_address, 'amount' => ($shipment->amount), 'service_type' => $service, 'shipment_status' => $status, 'crm_row' => $crm_row]]);

                                } else {
                                    return response()->json(['status' => 1, 'message' => 'Return Shipment not arrived at origin center yet.']);
                                }
                        } else {
                            return response()->json(['status' => 1, 'message' => 'Different hub, scan shipments of same hub!.']);
                        }
                } else {
                    return response()->json(['status' => 1, 'message' => 'This Shipment doesn\'t belongs to your assigned hubs!']);
                }
            } else {
                return response()->json(['status' => 1, 'message' => 'No Shipment with given Tracking Number is present, Check tracking!']);
            }


        }
    }

    public function get_piece_details(Request $request)
    {
        $rules = [
            'tracking' => ['required'],
            'piece_id' => ['required'],
        ];
        $validate = Validator::make($request->all(), $rules, $this->messages);
        $validate->setAttributeNames($this->names);
        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $tracking = $request->tracking;
            $shipment = Shipment::where('tracking_number', $tracking);
            if ($shipment->exists()) {
                $shipment = $shipment->first();
                $shipment_id = $shipment->id;
                $shipment_piece_id = $request->piece_id;
                $shipment_piece = ShipmentPiece::where('tracking_number', $shipment_piece_id);
                if ($shipment_piece->exists()) {
                    $shipment_piece = $shipment_piece->first();
                    if ($shipment_piece->shipment_id == $shipment_id) {
                        $scanned_shipment_piece = $shipment_piece->tracking_number;
                        return response()->json(['status' => 0, 'message' => 'Shipment Piece found!', "piece_details" => ["tracking_no" => $shipment->tracking_number, "piece_id" => $request->piece_id]]);
                    } else {
                        return response()->json(['status' => 1, 'message' => 'Given Item ID does not belong here']);
                    }
                } else {
                    return response()->json(['status' => 1, 'message' => 'No Shipment Item with given Item ID is present']);
                }
            } else {
                return response()->json(['status' => 1, 'message' => 'No Shipment Found!']);
            }
        }
    }

    public function return_note_create(Request $request)
    {
        $rules = [
            'rider_id' => ['required'],
            'route_id' => ['required'],
            'hub_id' => ['required'],
            'open_box' => ['nullable'],
            'trackings' => ['required'],
        ];
        $validate = Validator::make($request->all(), $rules, $this->messages);
        $validate->setAttributeNames($this->names);
        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $trackings = explode(',', $request->trackings);
            $open_box_ids = explode(',', $request->open_box);
            $rider = $request->rider_id;
            $route = $request->route_id;
            $hub_id = $request->hub_id;
            $admin = $request->admin_id;
            $return_statuses = array(20, 22, 24, 27, 29, 30, 33, 35, 37, 42, 44, 45, 46, 47, 48, 60);
            $valid_shipments = array();
            $shipments_count = 0;
            $invalid_shipments = array();
            if (!empty($trackings)) {
                foreach ($trackings as $shipment_id) {
                    $shipment_details = Shipment::where('tracking_number', $shipment_id);
                    if ($shipment_details->exists()) {
                        $shipment_details = $shipment_details->first();
                        if (in_array($shipment_details->shipper_status_id, $return_statuses)) {
                            $valid_shipments[] = $shipment_details->id;
                            $shipments_count++;
                        } else {
                            array_push($invalid_shipments, $shipment_details->tracking_number);
                        }
                    }
                }
                if (!empty($invalid_shipments)) {
                    return response()->json(['status' => 1, 'message' => "Return Note Already Created For Following Shipment(s) " . implode(',', $invalid_shipments)]);
                } elseif ($shipments_count != 0) {

                    $note = ReturnNote::create(['hub_id' => $hub_id, 'rider_id' => $rider, 'route_id' => $route, 'shipments_count' => $shipments_count, 'admin_id' => $admin, 'created_via_app' => 1]);

                    if ($note) {
                        foreach ($valid_shipments as $index => $shipment_id) {
                            $shipment = Shipment::where('id', $shipment_id);

                            $shipment = $shipment->first();

                            $old_return_note_id = ReturnNoteShipment::where('shipment_id', $shipment_id)->where('status', '=', 1)->orderBy('return_note_id', 'desc');

                            if ($old_return_note_id->exists()) {
                                $old_return_note_id = $old_return_note_id->first();

                                if (ReturnNote::where('id', $old_return_note_id->return_note_id)->where('status', 0)->exists()) {
                                    $journey = ShipmentsJourney::where('shipment_id', $shipment_id)->latest()->first();

                                    ShipmentsJourneyController::add($journey->shipment_id, 57, NULL, $journey->status_reason_id, $journey->remarks, NULL, $admin, $journey->reference_1_id, NULL, 1, NULL);
                                }
                            }
                            if (in_array($shipment_id, $open_box_ids)) {
                                $shipment->open_box = 1;
                                ShipmentOpenBoxJourneyController::add($shipment_id, 6, $admin);
                            }
                            if (in_array($shipment->booking_type_id, [1, 4, 5])) {
                                ReturnNoteShipment::create(['return_note_id' => $note->id, 'shipment_id' => $shipment_id]);
                                $shipment->shipper_status_id = 23;
                                $shipment->consignee_status_id = 23;
                                $shipment->save();
                                ShipmentsJourneyController::add($shipment->id, 23, 23, NULL, NULL, NULL, $admin, $note->id, $rider);
                            } else {
                                if ($shipment->booking_type_id == 2) {//attempt failed and arrived at origin center

                                    ReturnNoteShipment::create(['return_note_id' => $note->id, 'shipment_id' => $shipment_id]);
                                    $shipper_status_id = 28;
                                    $consignee_status_id = 28;
                                    if ($shipment->shipper_status_id == 22) {
                                        $shipper_status_id = 23;
                                        $consignee_status_id = 23;
                                    }
                                    $shipment->shipper_status_id = $shipper_status_id;
                                    $shipment->consignee_status_id = $consignee_status_id;
                                    $shipment->save();
                                    ShipmentsJourneyController::add($shipment->id, $shipper_status_id, $consignee_status_id, NULL, NULL, NULL, $admin, $note->id, $rider);


                                } else if ($shipment->booking_type_id == 3) {//attempt failed and arrived at origin center

                                    ReturnNoteShipment::create(['return_note_id' => $note->id, 'shipment_id' => $shipment_id]);
                                    $shipment->shipper_status_id = 34;
                                    $shipment->consignee_status_id = 34;
                                    $shipment->save();
                                    ShipmentsJourneyController::add($shipment->id, 34, 34, NULL, NULL, NULL, $admin, $note->id, $rider);


                                } else {
                                    ReturnNoteShipment::create(['return_note_id' => $note->id, 'shipment_id' => $shipment_id]);
                                    $shipment->shipper_status_id = 23;
                                    $shipment->consignee_status_id = 23;
                                    $shipment->save();
                                    ShipmentsJourneyController::add($shipment->id, 23, 23, NULL, NULL, NULL, $admin, $note->id, $rider);
                                }
                            }
                            $return_sheet = ReturnSheet::where('shipment_id', $shipment_id);
                            if ($return_sheet->exists()) {
                                $return_sheet = $return_sheet->first();
                                $return_sheet->return_note_id = $note->id;
                            } else {
                                $return_sheet = new ReturnSheet();
                                $return_sheet->user_id = $shipment->user_id;
                                $return_sheet->shipment_id = $shipment->id;
                                $return_sheet->return_note_id = $note->id;
                            }
                            $return_sheet->save();

                        }
                        NotificationsController::app_notification(6, $rider, 2, $note->id);
                    }
                    EmployeeAttendanceController::riders_attendance_mark($rider);

                    return response()->json(['status' => 0, 'create_message' => "Return note has been created with Return Note Number:" . $note->id]);
                } else {
                    return response()->json(['status' => 1, 'message' => "Failed to Create Return Note"]);
                }

            } else {
                return response()->json(['status' => 1, 'message' => "No shipments scanned"]);
            }
        }
    }

    public function get_return_note_list(Request $request)
    {
        $role_id = $request->admin_role_id;
        $admin_id = $request->admin_id;
        $admin_hubs = AdminHub::where('admin_id', $admin_id)->pluck('hub_id')->toArray();

        $deliveries = ReturnNote::join('cities AS oc', 'return_notes.hub_id', '=', 'oc.id')
            ->join('riders', 'return_notes.rider_id', '=', 'riders.id')
            ->join('admins', 'admins.id', '=', 'return_notes.admin_id')
            ->select(['return_notes.id as return_note_id', 'oc.name as hub', 'riders.name as rider', 'admins.name as assignee', 'return_notes.created_at', 'return_notes.shipments_count', 'return_notes.status as status_id', DB::raw('(SELECT COUNT(shipment_id) FROM return_note_shipments WHERE return_note_id = return_notes.id AND status = 0) AS shipments_unverified_count'), DB::raw('(SELECT COUNT(id) FROM shipments_journey where shipper_status_id in (25, 31, 38) and reference_1_id = return_notes.id and verification = 1 ) as delivered_to_shipper_count')])
            ->whereIn('return_notes.status', [0, 3])->orderBy('return_notes.id', 'DESC');

        if ($role_id != 1) {
            $deliveries = $deliveries->whereIn('oc.hub_id', $admin_hubs);
        }

        if ($deliveries->exists()) {
            $deliveries = $deliveries->get();
            $data = array();
            foreach ($deliveries as $notes) {
                $notes['status_name'] = ($notes->status_id == 0) ? "Created" : "Updated";
                $data[] = $notes;
            }
            return response()->json(['status' => 0, "return_notes" => $data]);
        }
        return response()->json(['status' => 1, 'message' => 'No Return Note Found!']);
    }

    public function return_note_shipments_list(Request $request)
    {
        $rules = [
            'return_note_id' => ['required']
        ];
        $validate = Validator::make($request->all(), $rules, $this->messages);
        $validate->setAttributeNames($this->names);
        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $role_id = $request->admin_role_id;
            $admin_id = $request->admin_id;
            $admin_hubs = AdminHub::where('admin_id', $admin_id)->pluck('hub_id')->toArray();
            $deliveries = ReturnNote::join('return_note_shipments as dns', 'dns.return_note_id', '=', 'return_notes.id')
                ->join('shipments', 'shipments.id', '=', 'dns.shipment_id')
                ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
                ->leftjoin('user_shipping_infos AS rsi', 'shipments.return_address_id', '=', 'rsi.id')
                ->leftjoin('cities AS rc', 'rsi.city_id', '=', 'rc.id')
                ->join('users', 'shipments.user_id', '=', 'users.id')
                ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
                ->join('booking_types as bt', 'bt.id', '=', 'shipments.booking_type_id')
                ->join('shipment_status as ss', 'ss.id', '=', 'shipments.shipper_status_id')
                ->leftjoin('crm_requests as crm', function ($join) {
                    $join->on('crm.shipment_id', '=', 'shipments.id')
                        ->whereIn('crm.status_id', [2, 3, 5])
                        ->where('crm.case_nature_id', 1);
                })
                ->leftjoin('shipments_journey as rrb', function ($join) {
                    $join->on('rrb.shipment_id', '=', 'shipments.id')
                        ->whereIn('shipments.shipper_status_id', [25, 31, 38])
                        ->where('rrb.id', '=',
                            DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id)'));
                })
                ->select(['return_notes.id as return_note', 'shipments.tracking_number', 'shipments.id as shId', 'oc.name as destination', 'usi.pickup_address as address', 'users.name as shipper', 'bt.booking_type as service_type', 'shipments.booking_type_id', 'shipments.shipper_status_id', 'ss.name as current_status_name', 'usi.poc', 'shipments.charges_mode_id', 'shipments.amount', 'shipments.return_charges', 'crm.id as complaint', 'rrb.received_or_refused_by', 'rrb.remarks as remarks', 'rsi.pickup_address as return_address_location', 'rc.name as return_city_name'])
                ->where('return_notes.id', $request->return_note_id);

            if ($role_id != 1) {
                $deliveries = $deliveries->whereIn('return_notes.hub_id', $admin_hubs);
            }
            if ($deliveries->exists()) {
                $deliveries = $deliveries->get();
                $delivered_array = array(25, 31, 38);
                $return_array = array(23, 25);
                $data = array();
                $bulk_statuses = ShipmentStatus::whereIn('id', [24, 25, 47, 48, 60])->select('id', 'name')->get();
                foreach ($deliveries as $shipment) {
                    $shipment["crm_row"] = ($shipment->complaint != null) ? 1 : 0;
                    $shipment["shipper"] = ($shipment->booking_type_id == 4) ? $shipment->shipper . ' (' . $shipment->poc . ')' : $shipment->shipper;
                    $shipment["return_address"] = ($shipment->return_address_location != NULL) ? $shipment->return_address_location : $shipment->address;
                    $shipment["return_city"] = ($shipment->return_city_name != NULL) ? $shipment->return_city_name : $shipment->destination;
                    $shipment["remarks"] = (in_array($shipment->shipper_status_id, $delivered_array)) ? ($shipment->remarks != null) ? $shipment->remarks : " " : NULL;
                    $shipment["reason"] = (in_array($shipment->shipper_status_id, $delivered_array)) ? " " : NULL;
                    $shipment["received_or_refused_by"] = (in_array($shipment->shipper_status_id, $delivered_array)) ? ($shipment->received_or_refused_by != null) ? $shipment->received_or_refused_by : " " : NULL;
                    $shipment["hide_dropdowns"] = (in_array($shipment->shipper_status_id, $delivered_array)) ? 1 : 0;
                    $shipment["check_box"] = (in_array($shipment->shipper_status_id, $delivered_array)) ? 0 : 1;

                    if (in_array($shipment->shipper_status_id, $delivered_array)) {
                        $statuses = [['id' => $shipment->shipper_status_id, 'name' => $shipment->current_status_name]];
                    } else {
                        if (in_array($shipment->booking_type_id, [1, 4, 5])) {
                            $where = array(24, 47, 48, 60);
                        } else if ($shipment->booking_type_id == 2) {
                            $where = array(47, 48, 60);
                            if (in_array($shipment->shipper_status_id, $return_array)) {
                                $where[] = 25;
                            } else {
                                $where[] = 29;
                            }

                        } else if ($shipment->booking_type_id == 3) {
                            $where = array(35, 47, 48, 60);
                        }
                        $statuses = ShipmentStatus::whereIn('id', $where)->select('id', 'name')->get();
                    }

                    if ($shipment->booking_type_id == 4) {
                        if ($shipment->charges_mode_id == 1) {
                            $charges = number_format($shipment->return_charges);
                        } else {
                            $charges = number_format($shipment->amount);
                        }
                    } else {
                        $charges = '';
                    }

                    $shipment["status"] = $statuses;
                    $shipment["charges"] = $charges;
                    $data[] = $shipment;
                }
                return response()->json(['status' => 0, 'data' => $data, 'bulk_statuses' => $bulk_statuses]);
            } else {
                return response()->json(['status' => 1, 'message' => "Shipments Not Found!"]);
            }
        }
    }

    public function return_reason(Request $request)
    {
        $rules = [
            'status_id' => ['required']
        ];
        $validate = Validator::make($request->all(), $rules, $this->messages);
        $validate->setAttributeNames($this->names);
        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $status_id = $request->status_id;
            $statuses = ShipmentStatus::find($status_id)->reasons()->select('id', 'name')->orderBy('name')->get();
            if (!$statuses->isEmpty()) {
                return response()->json(['status' => 0, 'reasons' => $statuses]);
            } else {
                return response()->json(['status' => 0, 'message' => 'No reasons are defined for this status!']);
            }
        }

    }

    public function return_status_submit_individual(Request $request)
    {
        $new_return_note_shipments = NULL;
        $shipments_updated_flag = FALSE;
        $shipment = $request->shipment_id;
        $open_box = $request->is_open_box;
        $return_note_id = $request->return_note_id;
        $array_returned = array(25, 31, 38);
        $array_returned_status = array(24, 29, 35, 47, 48, 60);
        $actual_date = $request->date;
        $admin_id = $request->admin_id;
        $remarks = $request->remarks;
        $reasonId = $request->reason_id;
        $statusId = $request->status_id;
        $received_or_refused_by = ($request->has("received_or_refused_by")) ? $request->received_or_refused_by : NULL;
        if ($return_note_id != '') {
            $return_note_details = ReturnNote::find($return_note_id);
            $parcel = Shipment::where('id', $shipment)->first();
            if (!ReturnNoteShipment::join('return_notes', 'return_notes.id', '=', 'return_note_shipments.return_note_id')->where('return_note_shipments.return_note_id', '>', $return_note_id)->where('shipment_id', $shipment)->exists()) {
                if ($open_box == 1) {
                    $parcel->open_box = 1;
                    $parcel->save();
                    ShipmentOpenBoxJourneyController::add($shipment, 7, $admin_id);
                }
                if (!in_array($parcel->shipper_status_id, $array_returned)) {
                    if (in_array($statusId, $array_returned_status)) {
                        if ($statusId != $parcel->shipper_status_id) {
                            ShipmentsJourneyController::add($shipment, $statusId, NULL, ($reasonId != -1) ? $reasonId : null, $remarks, NULL, $admin_id, $return_note_id, NULL, 1, $received_or_refused_by);
                            Shipment::where('id', $shipment)->update(['shipper_status_id' => $statusId]);
                            ReturnNoteShipment::where(['return_note_id' => $return_note_id, 'shipment_id' => $shipment])->update(['status' => 1]);
                            $shipments_updated_flag = TRUE;
                        }
                    }

                }
            } else {
                $new_return_note_shipments .= $parcel->tracking_number . ' ';
            }
            $shipment_status = ReturnNoteShipment::where(['return_note_id' => $return_note_id, 'status' => 0])->count();
            if ($shipment_status == 0) {
                if ($return_note_details->completion_status == 0) {
                    $return_note_details->status = 1;
                    $return_note_details->updated_by = $admin_id;
                } else {
                    $return_note_details->status = 3;
                    $return_note_details->updated_by = $admin_id;
                }
                $return_note_details->actual_date = $actual_date;
                $return_note_details->save();
            }

            NotificationsController::send(15, $return_note_id);
            NotificationsController::send(16, $return_note_id);

            if ($new_return_note_shipments != null) {
                if ($shipments_updated_flag) {
                    return response()->json(['status' => 0, 'update_message' => 'Return Note Status Has Been Updated', 'error' => 'Following shipments are already in new return note ' . $new_return_note_shipments]);

                } else {
                    return response()->json(['status' => 1, 'message' => 'Following shipments are already in new return note ' . $new_return_note_shipments]);

                }
            } else {
                return response()->json(['status' => 0, 'update_message' => 'Return Note Status Has Been Updated']);
            }
        }
    }

    public function return_status_submit_all(Request $request)
    {
        $rules = [
            'shipments' => ['required'],
            'shipment_status' => ['required'],
            'shipment_reason' => ['required'],
            'remarks' => ['required'],
            'received_or_refused_by' => ['nullable'],
            'open_box' => ['nullable'],
            'date' => ['required'],
        ];
        $validate = Validator::make($request->all(), $rules, $this->messages);
        $validate->setAttributeNames($this->names);
        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $shipment_ids = explode(',', $request->shipments);
            $shipment_status = $request->shipment_status;
            $shipment_reason = ($request->shipment_reason == -1) ? NULL : $request->shipment_reason;
            $actual_date = $request->date;
            $shipment_status_mandatory = array(24, 47, 48);
            $shipment_remark = $request->remarks;
            $admin_id = $request->admin_id;
            $received_or_refused_by = ($request->has("received_or_refused_by")) ? $request->received_or_refused_by : NULL;
            $open_box_ids = array();
            if ($request->has('open_box')) {
                $open_box_ids = explode(',', $request->open_box);;
            }
            $return_note_details = ReturnNote::find($request->return_note_id);
            if (in_array($shipment_status, $shipment_status_mandatory) && $shipment_reason == -1) {
                return response()->json(['status' => 1, 'message' => 'Reason is Mandatory for Selected Status']);
            }
            if ($shipment_status == 25) {
                foreach ($shipment_ids as $shipment_id) {
                    $parcel = Shipment::where('id', $shipment_id)->first();
                    if (count($open_box_ids) > 0) {
                        if (in_array($shipment_id, $open_box_ids)) {
                            $parcel->open_box = 1;
                            $parcel->save();
                            ShipmentOpenBoxJourneyController::add($shipment_id, 7, $admin_id);
                        }
                    }
                    if (!ReturnNoteShipment::join('return_notes', 'return_notes.id', '=', 'return_note_shipments.return_note_id')->where('return_note_shipments.return_note_id', '>', $request->return_note_id)->where('shipment_id', $shipment_id)->exists()) {

                        if ($parcel->booking_type_id == 2) {
                            $shipper_status_id = 31;
                            $consignee_status_id = 31;
                            if (in_array($parcel->shipper_status_id, [23, 24])) {
                                $shipper_status_id = 25;
                                $consignee_status_id = 25;
                            }
                            ShipmentsJourneyController::add($shipment_id, $shipper_status_id, $consignee_status_id, $shipment_reason, $shipment_remark, NULL, $admin_id, $request->return_note_id, NULL, 1, $received_or_refused_by);

                            Shipment::where('id', $shipment_id)->update(['shipper_status_id' => $shipper_status_id, 'consignee_status_id' => $consignee_status_id]);

                        } else if ($parcel->booking_type_id == 3) {
                            ShipmentsJourneyController::add($shipment_id, 38, 38, $shipment_reason, $shipment_remark, NULL, $admin_id, $request->return_note_id, NULL, 1, $received_or_refused_by);

                            Shipment::where('id', $shipment_id)->update(['shipper_status_id' => 38, 'consignee_status_id' => 38]);

                        } else {
                            ShipmentsJourneyController::add($shipment_id, 25, 25, $shipment_reason, $shipment_remark, NULL, $admin_id, $request->return_note_id, NULL, 1, $received_or_refused_by);

                            Shipment::where('id', $shipment_id)->update(['shipper_status_id' => 25, 'consignee_status_id' => 25]);
                        }
                    }
                    ReturnNoteShipment::where(['return_note_id' => $request->return_note_id, 'shipment_id' => $shipment_id])->update(['status' => 1]);
                    if ($return_note_details->completion_status == 0) {
                        $return_note_details->completion_status = 1;
                        $return_note_details->save();
                    }
                }
                $shipment_status_count = ReturnNoteShipment::where(['return_note_id' => $request->return_note_id, 'status' => 0])->count();
                if ($shipment_status_count == 0) {
                    $return_note_details->updated_by = $admin_id;
                    $return_note_details->status = 3;
                }
                $return_note_details->actual_date = $actual_date;
                $return_note_details->save();

                NotificationsController::send(15, $request->return_note_id);
                NotificationsController::send(16, $request->return_note_id);
                return response()->json(['status' => 0, 'bulk_update_message' => 'Return note shipments status are updated to : Delivered to Shipper']);
            } else {
                $remarks = $request->remarks;
                foreach ($shipment_ids as $shipment_id) {
                    $shipment = Shipment::find($shipment_id);

                    $shipment->shipper_status_id = $shipment_status;
                    $shipment->save();
                    ShipmentsJourneyController::add($shipment_id, $shipment_status, NULL, $shipment_reason, $remarks, NULL, $admin_id, $request->return_note_id);

                    ReturnNoteShipment::where(['return_note_id' => $request->return_note_id, 'shipment_id' => $shipment_id])->update(['status' => 1]);
                    if (count($open_box_ids) > 0) {
                        if (in_array($shipment_id, $open_box_ids)) {
                            $shipment->open_box = 1;
                            $shipment->save();
                            ShipmentOpenBoxJourneyController::add($shipment_id, 7, $admin_id);
                        }
                    }
                }
                $shipment_status = ReturnNoteShipment::where(['return_note_id' => $request->return_note_id, 'status' => 0])->count();
                if ($shipment_status == 0) {
                    if ($return_note_details->completion_status == 0) {
                        $return_note_details->status = 1;
                        $return_note_details->updated_by = $admin_id;

                    } else {
                        $return_note_details->status = 3;
                        $return_note_details->updated_by = $admin_id;
                    }
                    $return_note_details->actual_date = $actual_date;
                    $return_note_details->save();

                }
                NotificationsController::send(15, $request->return_note_id);
                NotificationsController::send(16, $request->return_note_id);
                return response()->json(['status' => 0, 'bulk_update_message' => 'Return Note Status Has Been Updated']);
            }

        }
    }

    public function return_image_upload(Request $request)
    {
        $rules = [
            'return_note_id' => ['required', 'integer', 'digits_between:1,10', 'exists:return_notes,id'],
            'images' => ['array', 'required'],
            'images.*image' => ['required', 'mimes:jpeg,png,jpg', 'max:2048'],
        ];
        $validate = Validator::make($request->all(), $rules, $this->messages);
        $validate->setAttributeNames($this->names);
        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $admin_id = $request->admin_id;
            $return_note_id = $request->return_note_id;
            $return_note = ReturnNote::find($return_note_id);
            $present = false;
            if ($return_note) {
                $pictures = array();
                if ($request->has('images')) {
                    $pictures = $request->images;
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
                } else {
                    return response()->json(['status' => 1, 'message' => 'Images are not provided!']);
                }
                if ($present && in_array($return_note->status, [1, 3])) {
                    $return_note->updated_by = $admin_id;
                    $return_note->status = 1;
                    $return_note->save();
                    return response()->json(['status' => 0, 'message' => 'Images inserted successfully!']);
                } else {
                    return response()->json(['status' => 1, 'message' => 'Failed to upload Images!']);
                }
            }
            return response()->json(['status' => 1, 'message' => 'Return Note not found!']);
        }
    }

    public function trax_directory_index(Request $request)
    {
        $cities = City::where('business_category_id', 1)->where('status', 1)->select('id', 'name')->get();
        return response()->json(['status' => 0, 'cities' => $cities]);
    }

    public function login_v5(Request $request)
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
            $user = Admin::where('phone_number', substr_replace($request->input('phone_number'), '-', 4, 0))->orWhere('official_phone_number', substr_replace($request->input('phone_number'), '-', 4, 0));
            if ($user->exists()) {
                $user = $user->first();
                if ($user->status == 0) {

                    if ($user->first_login == 0) {
                        return response()->json(['status' => 1, 'message' => "Dear " . $user->name . "- Your request is in process and is pending for approval from HR."]);
                    } else {
                        return response()->json(['status' => 1, 'message' => 'Account disabled, Please contact admin!']);
                    }
                }
                if (Hash::check($request->input('pin'), $user->password)) {
                    $generate_otp = false;
                    $environment = config('app.env');
                    $settings = GlobalSettings::where('type', 'admin_otp');
                    if ($settings->exists()) {
                        $settings = $settings->first();
                        if ($settings->setting_value) {
                            if ($environment == 'production' || $environment == 'staging') {
                                $otp = mt_rand(100000, 999999);
                                $user->otp = $otp;
                                $user->last_login_attempt = Carbon::now();
                                $user->save();
                                $data = array("otp" => $otp, "phone_number" => $request->phone_number);
                                NotificationsController::send(138, $user, $data);
                                $generate_otp = true;
                            }
                        }
                    }

                    if (!$user->api_token) {
                        $api_token = uniqid(base64_encode(str_random(60)));
                        $user->api_token = $api_token;
                        $user->save();
                    }
                    $api_token = $user->api_token;
                    if ($generate_otp) {
                        return response()->json(['status' => 0, 'message' => 'Otp Generated', 'api_token' => $api_token, 'otp_generated' => 1]);
                    } else {
                        $employee = Employee::where('trax_id', $user->trax_id);
                        $information = array();
                        $information['id'] = $user->id;
                        $information['name'] = $user->name;
                        $information['phone'] = $user->phone_number;
                        $information['cnic'] = $user->cnic;
                        $information['api_token'] = $api_token;
                        $information['cargo_user'] = (in_array($user->role_id, [11, 10, 15, 55, 23, 33, 46])) ? 1 : 0;
                        if ($user->designation_id) {
                            $user_department = $user->Edesignation->department_id;
                        } else {
                            $user_department = $user->role->department_id;
                        }
                        $information['sales_person'] = ($user_department == 7) ? 1 : 0;
                        if ($employee->exists()) {
                            $employee = $employee->first();
                            $information['address'] = ($employee->address) ? $employee->address : "";
                        } else {
                            $information['address'] = '';
                        }
                        $information['role'] = 'staff';
                        if ($request->has('device_token')) {
                            EmployeeDeviceToken::where('device_token', $request->get('device_token'))->delete();
                            EmployeeDeviceToken::where('employee_type_id', 1)->where('employee_id', $user->id)->delete();
                            $employee_device_token = new EmployeeDeviceToken();
                            $employee_device_token->employee_id = $user->id;
                            $employee_device_token->employee_type_id = 1;
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
                        } else {
                            $information['distance'] = 0;
                            $information['lat'] = 0;
                            $information['long'] = 0;
                        }
                        $information['welcome_bit'] = 0;
                        if (!$user->first_login) {
                            $information['welcome_bit'] = 1;
                            $information['welcome_message'] = "Welcome to TRAX " . $user->name;
                        }
                        $user->first_login = 1;
                        $user->save();
                        return response()->json(['status' => 0, 'message' => 'Logged In Successfully', 'information' => $information]);
                    }
                } else {
                    return response()->json(['status' => 1, 'message' => 'Invalid PIN!']);
                }
            } else {
                $employee = Employee::whereIn('request_status_id', [1, 2])->where('employee_type_id', 1)->where('phone_number', substr_replace($request->input('phone_number'), '-', 4, 0))->orWhere('official_phone_number', substr_replace($request->input('phone_number'), '-', 4, 0));
                if ($employee->exists()) {
                    $employee = $employee->first();
                    return response()->json(['status' => 1, 'message' => "Dear " . $employee->name . "- Your request is in process and is pending for approval from HR."]);
                } else {
                    return response()->json(['status' => 1, 'message' => 'Invalid Credentials']);
                }
            }
        }
    }

    public function validate_otp(Request $request){
        $rules = [
            'otp' => ['required', 'integer', 'digits:6'],
            'device_token' => ['nullable']
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $admin_id = $request->admin_id;
            $user = Admin::find($admin_id);
            if ($user->otp == $request->otp) {
                $employee = Employee::where('trax_id', $user->trax_id);
                $information = array();
                $information['id'] = $user->id;
                $information['name'] = $user->name;
                $information['phone'] = $user->phone_number;
                $information['cnic'] = $user->cnic;
                $information['api_token'] = $user->api_token;
                $information['cargo_user'] = (in_array($user->role_id, [11, 10, 15, 55, 23, 33, 46])) ? 1 : 0;
                if ($user->designation_id) {
                    $user_department = $user->Edesignation->department_id;
                } else {
                    $user_department = $user->role->department_id;
                }
                $information['sales_person'] = ($user_department == 7) ? 1 : 0;
                if ($employee->exists()) {
                    $employee = $employee->first();
                    $information['address'] = ($employee->address) ? $employee->address : "";
                } else {
                    $information['address'] = '';
                }
                $information['role'] = 'staff';
                if ($request->has('device_token')) {
                    EmployeeDeviceToken::where('device_token', $request->get('device_token'))->delete();
                    EmployeeDeviceToken::where('employee_type_id', 1)->where('employee_id', $user->id)->delete();
                    $employee_device_token = new EmployeeDeviceToken();
                    $employee_device_token->employee_id = $user->id;
                    $employee_device_token->employee_type_id = 1;
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
                } else {
                    $information['distance'] = 0;
                    $information['lat'] = 0;
                    $information['long'] = 0;
                }
                $information['welcome_bit'] = 0;
                if (!$user->first_login) {
                    $information['welcome_bit'] = 1;
                    $information['welcome_message'] = "Welcome to TRAX " . $user->name;
                }
                $user->first_login = 1;
                $user->save();
                return response()->json(['status' => 0, 'message' => 'Logged In Successfully', 'information' => $information]);
            } else {
                return response()->json(['status' => 1, 'message' => 'Invalid OTP']);
            }
        }
    }

}
