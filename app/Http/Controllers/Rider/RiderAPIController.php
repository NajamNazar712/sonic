<?php

namespace App\Http\Controllers\Rider;

use App\Http\Controllers\AdminAPIController;
use App\Http\Controllers\Admins\AdminPickupsController;
use App\Http\Controllers\Admins\CheckDisputeShipmentsController;
use App\Http\Controllers\Admins\DeliveryController;
use App\Http\Controllers\Admins\Handover\HandoverShipmentJourneyController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\EmployeeAttendanceController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\Retail\RetailShipmentBookController;
use App\Http\Controllers\ShipmentOpenBoxJourneyController;
use App\Http\Controllers\ShipmentScanningJourneyController;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\AdminDepartment;
use App\Http\Models\Admin\Attendance\EmployeeAttendance;
use App\Http\Models\Admin\Attendance\EmployeeAttendanceActionLog;
use App\Http\Models\Admin\ChangeShipmentAmountLog;
use App\Http\Models\Admin\DeliveryNote;
use App\Http\Models\Admin\DeliveryNoteShipment;
use App\Http\Models\Admin\DeliveryRelation;
use App\Http\Models\Admin\GlobalSettings;
//use App\Http\Models\Admin\OneLink\OneLinkPaymentTransaction;
use App\Http\Models\Admin\OneLink\OneLinkOutForDeliveryShipmentPayment;
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
use App\Http\Models\Admin\ReturnNoteShipment;
use App\http\Models\Admin\ReturnReasonMandatoryShipper;
use App\Http\Models\Admin\RiderCategoryByPass;
use App\Http\Models\Admin\RiderType;
use App\Http\Models\Admin\ShipmentOnHold;
use App\Http\Models\AppNotification;
use App\Http\Models\BanksList;
use App\Http\Models\BusinessCategory;
use App\Http\Models\City;
use App\Http\Models\CityDelivery;
use App\Http\Models\ConsigneeLocation;
use App\Http\Models\ConsigneeShipmentLocation;
use App\Http\Models\CRM\CrmComments;
use App\Http\Models\CRM\CrmRequest;
use App\Http\Models\DeliveryNoteRequests;
use App\Http\Models\EmployeeDeviceToken;
use App\Http\Models\EmployeeNotificationHistory;
use App\Http\Models\EmployeeShift;
use App\Http\Models\Handover\Handover;
use App\Http\Models\Handover\HandoverShipments;
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
use App\http\Models\HR\EmployeeNature;
use App\Http\Models\HR\EmployeePayslip;
use App\Http\Models\HR\EmployeeRelationship;
use App\Http\Models\HR\EmployeeReligion;
use App\Http\Models\HR\LeaveType;
use App\Http\Models\HR\StaffCategory;
use App\Http\Models\InterceptReBookRequestHistory;
use App\Http\Models\MisroutedHistory;
use App\Http\Models\PackagingMaterialRequest;
use App\Http\Models\PayslipPdf;
use App\Http\Models\PickupNote;
use App\Http\Models\PickupNoteRequest;
use App\Http\Models\PickupRequest;
use App\Http\Models\Product;
use App\Http\Models\ReportingLocation;
use App\Http\Models\Rider;
use App\Http\Models\Rider\RiderDeliveryActionLog;
use App\Http\Models\Rider\RiderDeliveryNoteRequest;
use App\Http\Models\Rider\RiderIncentiveDelivery;
use App\Http\Models\Rider\RiderIncentivePickup;
use App\Http\Models\Rider\RiderRequest;
use App\Http\Models\Rider\RiderReturnDelivery;
use App\Http\Models\Rider\RiderReturnDeliveryActionLog;
use App\Http\Models\Rider\RiderReturnNoteStatus;
use App\Http\Models\Rider\RidersIncentive;
use App\Http\Models\Rider\RiderTickerImage;
use App\Http\Models\RiderCategory;
use App\Http\Models\RiderDelivery;
use App\Http\Models\RiderPickup;
use App\Http\Models\RiderPickupActionLog;
use App\Http\Models\RiderPickupInvalidLog;
use App\Http\Models\Route;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentDetail;
use App\Http\Models\ShipmentDistributionProduct;
use App\Http\Models\ShipmentItem;
use App\Http\Models\ShipmentOpenBox;
use App\Http\Models\ShipmentOtp;
use App\Http\Models\ShipmentPiece;
use App\Http\Models\ShipmentReplacementParcelImage;
use App\Http\Models\ShipmentsJourney;
use App\Http\Models\ShipmentStatus;
use App\Http\Models\Shipper\UserShippingInfo;
use App\Http\Models\V2Pickup\V2PickupNote;
use App\Http\Models\V2Pickup\V2PickupNoteRequest;
use App\Http\Models\V2Pickup\V2PickupRequest;
use App\Http\Models\V2Pickup\V2PickupRequestAttempt;
use App\Http\Models\V2Pickup\V2PickupRequestShipment;
use App\Http\Models\V2Pickup\V2RiderPickup;
use App\Http\Models\V2Pickup\V2RiderPickupActionLog;
use App\Http\Models\WarehouseStockRequest;
use App\Http\Models\Zone;
use App\Jobs\ProcessAgentCallMonitoring;
use App\Jobs\ProcessOneLinkExpireDeliveryNote;
use App\Http\Models\Rider\RiderDeliveryNoteRequestShipment;
use App\RiderDeliveryNoteStatus;
use App\RiderLocationLog;
use App\RiderMainCategory;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Carbon\Carbon;
use DB;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Validator;

class RiderAPIController extends Controller
{
    private $names = [
        'phone_number' => 'Phone Number',
        'pin' => 'PIN',

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

    static public function retail_pickup_assign($pickup_request_id, $rider_id)
    {
        $retail_pickup_note = RetailPickupNote::where('pickup_request_id', $pickup_request_id)->where('status', 1);
        if ($retail_pickup_note->exists()) {
            $retail_pickup_note = $retail_pickup_note->first();
            $retail_pickup_note->rider_id = $rider_id;
            $retail_pickup_note->assigned_by = Auth::id();
            $retail_pickup_note->assigned_at = Carbon::now();
            $retail_pickup_note->status = 2;
            $retail_pickup_note->save();
        }
    }

    static public function rider_pickup_invalid_logs($rider_id, $pickup_request_id, $pickup_note_id, $shipment_id, $status_id){
        $rider_pickup_invalid_logs = new RiderPickupInvalidLog();
        $rider_pickup_invalid_logs->rider_id = $rider_id;
        $rider_pickup_invalid_logs->pickup_request_id = $pickup_request_id;
        $rider_pickup_invalid_logs->pickup_note_id = $pickup_note_id;
        $rider_pickup_invalid_logs->shipment_id = $shipment_id;
        $rider_pickup_invalid_logs->shipment_status_id = $status_id;
        $rider_pickup_invalid_logs->save();
    }

    public function distance($origin, $destination)
    {
        return $this->vincenty_distance($origin, $destination);
    }

    private function google_distance($origin, $destination)
    {
        try {
            $client = new Client(['base_uri' => 'https://maps.googleapis.com/maps/api/distancematrix/json', 'http_errors' => FALSE, 'connect_timeout' => 120, 'timeout' => 120]);

            $response = $client->get('', [
                'query' => [
                    'key' => 'AIzaSyCV6MaF4JjDpjuYljaUw9NxEY5kf5ipOzc',
                    'origins' => $origin,
                    'destinations' => $destination
                ]
            ]);

            $response = json_decode($response->getBody()->getContents(), true);

            if ($response['status'] == 'OK') {
                if (isset($response['rows'][0]['elements'][0]['distance']['value'])) {
                    $distance = ROUND($response['rows'][0]['elements'][0]['distance']['value'] / 1000, 2);
                } else {
                    $distance = $this->vincenty_distance($origin, $destination);
                }
            } else {
                $distance = $this->vincenty_distance($origin, $destination);
            }
        } catch (RequestException $e) {
            $distance = $this->vincenty_distance($origin, $destination);
        }

        return $distance;
    }

    private function haversine_distance($origin, $destination)
    {
        $earth_radius = 6371;

        list($origin_latitude, $origin_longitude) = explode(',', $origin);
        list($destination_latitude, $destination_longitude) = explode(',', $destination);

        $difference_latitude = deg2rad($destination_latitude - $origin_latitude);
        $difference_longitude = deg2rad($destination_longitude - $origin_longitude);

        $distance = round($earth_radius * (2 * asin(sqrt(sin($difference_latitude / 2) * sin($difference_latitude / 2) + cos(deg2rad($origin_latitude)) * cos(deg2rad($destination_latitude)) * sin($difference_longitude / 2) * sin($difference_longitude / 2)))), 2);

        return $distance;
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

    private function set_order($starting_location, $pickup_note_id, $pickup_note_requests)
    {
        $ordered = TRUE;

        foreach ($pickup_note_requests as $pickup_note_request) {
            if (!$pickup_note_request->ordering) {
                $ordered = FALSE;

                break;
            }
        }

        if (!$ordered) {
            $order = array();

            $order_number = 1;

            $pickup_requests_with_location = array();

            foreach ($pickup_note_requests as $pickup_note_request) {
                $pickup_request = $pickup_note_request->pickup_request;
                $pickup_address = $pickup_request->pickup_address;

                if ($pickup_address->location_latitude && $pickup_address->location_longitude) {
                    $pickup_request = $pickup_note_request->pickup_request;

                    $pickup_requests_with_location[$pickup_request->id] = $pickup_request;
                } else {
                    $pickup_note_request->ordering = $order_number;

                    $pickup_note_request->save();

                    $order_number++;
                }
            }

            while (!empty($pickup_requests_with_location)) {
                $distances = array();

                foreach ($pickup_requests_with_location as $pickup_request) {
                    $pickup_address = $pickup_request->pickup_address;

                    $destination = $pickup_address->location_latitude . ',' . $pickup_address->location_longitude;

                    $distances[$pickup_request->id] = $this->distance($starting_location, $destination);
                }

                $pickup_request_id = min(array_keys($distances, min($distances)));

                PickupNoteRequest::where('pickup_note_id', $pickup_note_id)->where('pickup_request_id', $pickup_request_id)->update(['ordering' => $order_number]);

                $order_number++;

                $pickup_address = $pickup_requests_with_location[$pickup_request_id]->pickup_address;

                $starting_location = $pickup_address->location_latitude . ',' . $pickup_address->location_longitude;

                unset($pickup_requests_with_location[$pickup_request_id]);
            }
        }
    }

    private function set_order_v2($starting_location, $pickup_note_id, $pickup_note_requests)
    {
        $ordered = TRUE;

        foreach ($pickup_note_requests as $pickup_note_request) {
            if (!$pickup_note_request->ordering) {
                $ordered = FALSE;

                break;
            }
        }

        if (!$ordered) {
            $order = array();

            $order_number = 1;

            $pickup_requests_with_location = array();

            foreach ($pickup_note_requests as $pickup_note_request) {
                $pickup_request = $pickup_note_request->pickup_request;
                $pickup_address = $pickup_request->pickup_address;

                if ($pickup_address->location_latitude && $pickup_address->location_longitude) {
                    $pickup_request = $pickup_note_request->pickup_request;

                    $pickup_requests_with_location[$pickup_request->id] = $pickup_request;
                } else {
                    $pickup_note_request->ordering = $order_number;

                    $pickup_note_request->save();

                    $order_number++;
                }
            }

            while (!empty($pickup_requests_with_location)) {
                $distances = array();

                foreach ($pickup_requests_with_location as $pickup_request) {
                    $pickup_address = $pickup_request->pickup_address;

                    $destination = $pickup_address->location_latitude . ',' . $pickup_address->location_longitude;

                    $distances[$pickup_request->id] = $this->distance($starting_location, $destination);
                }

                $pickup_request_id = min(array_keys($distances, min($distances)));

                V2PickupNoteRequest::where('pickup_note_id', $pickup_note_id)->where('pickup_request_id', $pickup_request_id)->update(['ordering' => $order_number]);

                $order_number++;

                $pickup_address = $pickup_requests_with_location[$pickup_request_id]->pickup_address;

                $starting_location = $pickup_address->location_latitude . ',' . $pickup_address->location_longitude;

                unset($pickup_requests_with_location[$pickup_request_id]);
            }
        }
    }

    private function verify_pickup_address_location($pickup_address_id)
    {
        $number_of_entries = 5;

        $pickup_requests = PickupRequest::where('pickup_address_id', $pickup_address_id)->where('status_id', 2);

        if ($pickup_requests->exists() && $pickup_requests->count() >= $number_of_entries) {
            $pickup_request_ids = $pickup_requests->latest('id')->take($number_of_entries)->pluck('id')->toArray();

            $rider_pickups = RiderPickup::whereIn('pickup_request_id', $pickup_request_ids);

            $allowed_correct_instances = ROUND(($number_of_entries / 2), 0, PHP_ROUND_HALF_DOWN);

            if ($rider_pickups->exists() && $rider_pickups->count() >= $number_of_entries) {
                $rider_pickups = $rider_pickups->latest('id')->take($number_of_entries)->get()->toArray();

                $location = array();

                $correct_instances = 0;

                $replace = FALSE;

                for ($c1 = 0; $c1 < ($number_of_entries - $allowed_correct_instances); $c1++) {
                    $origin = $rider_pickups[$c1]['actual_location_latitude'] . ',' . $rider_pickups[$c1]['actual_location_longitude'];

                    for ($c2 = ($c1 + 1); $c2 < $number_of_entries; $c2++) {
                        $destination = $rider_pickups[$c2]['actual_location_latitude'] . ',' . $rider_pickups[$c2]['actual_location_longitude'];

                        $distance = $this->distance($origin, $destination);

                        if ($distance <= 0.01) {
                            $location['latitude'] = $rider_pickups[$c1]['actual_location_latitude'];
                            $location['longitude'] = $rider_pickups[$c1]['actual_location_longitude'];

                            $correct_instances++;
                        }
                    }

                    if ($correct_instances >= $allowed_correct_instances) {
                        $replace = TRUE;

                        break;
                    }
                }

                if ($replace) {
                    $pickup_address = UserShippingInfo::find($pickup_address_id);

                    $pickup_address->location_longitude = $location['latitude'];
                    $pickup_address->location_longitude = $location['longitude'];

                    $pickup_address->save();
                }
            }
        }
    }

    private function verify_pickup_address_location_v2($pickup_address_id)
    {
        $number_of_entries = 5;

        $pickup_requests = V2PickupRequest::where('pickup_address_id', $pickup_address_id)->where('status_id', 2);

        if ($pickup_requests->exists() && $pickup_requests->count() >= $number_of_entries) {
            $pickup_request_ids = $pickup_requests->latest('id')->take($number_of_entries)->pluck('id')->toArray();

            $rider_pickups = V2RiderPickup::whereIn('pickup_request_id', $pickup_request_ids);

            $allowed_correct_instances = ROUND(($number_of_entries / 2), 0, PHP_ROUND_HALF_DOWN);

            if ($rider_pickups->exists() && $rider_pickups->count() >= $number_of_entries) {
                $rider_pickups = $rider_pickups->latest('id')->take($number_of_entries)->get()->toArray();

                $location = array();

                $correct_instances = 0;

                $replace = FALSE;

                for ($c1 = 0; $c1 < ($number_of_entries - $allowed_correct_instances); $c1++) {
                    $origin = $rider_pickups[$c1]['actual_location_latitude'] . ',' . $rider_pickups[$c1]['actual_location_longitude'];

                    for ($c2 = ($c1 + 1); $c2 < $number_of_entries; $c2++) {
                        $destination = $rider_pickups[$c2]['actual_location_latitude'] . ',' . $rider_pickups[$c2]['actual_location_longitude'];

                        $distance = $this->distance($origin, $destination);

                        if ($distance <= 0.01) {
                            $location['latitude'] = $rider_pickups[$c1]['actual_location_latitude'];
                            $location['longitude'] = $rider_pickups[$c1]['actual_location_longitude'];

                            $correct_instances++;
                        }
                    }

                    if ($correct_instances >= $allowed_correct_instances) {
                        $replace = TRUE;

                        break;
                    }
                }

                if ($replace) {
                    $pickup_address = UserShippingInfo::find($pickup_address_id);

                    $pickup_address->location_longitude = $location['latitude'];
                    $pickup_address->location_longitude = $location['longitude'];

                    $pickup_address->save();
                }
            }
        }
    }

    private function generateDateRange($start_date, $end_date)
    {
        $start_date = Carbon::parse($start_date);
        $end_date = Carbon::parse($end_date);
        $dates = [];
        for($date = $start_date->copy(); $date->lte($end_date); $date->addDay()) {
            $dates[] = $date->format('Y-m-d');
        }

        return $dates;
    }

    public function login(Request $request)
    {
        return response()->json(['status' => 1, 'message' => 'Please Update Your Bolt App']);
    }

    public function get_rider_location(Request $request)
    {
        $rules = [
            'rider_location_latitude' => ['required', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'rider_location_longitude' => ['required', 'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'],
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);
        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $rider_id = $request->rider_id;
            $latitude = $request->rider_location_latitude;
            $longitude = $request->rider_location_longitude;

            $log = RiderLocationLog::where('rider_id', $rider_id);

            if ($log->exists()) {
                $log = $log->first();
            } else {
                $log = new RiderLocationLog();
                $log->rider_id = $rider_id;
            }
            $log->latitude = $latitude;
            $log->longitude = $longitude;
            $log->save();

            return response()->json(['status' => 0, 'message' => 'Location Marked Successfully']);
        }

    }

    public function pickup_summary(Request $request)
    {
        return response()->json(['status' => 0, 'message' => 'Please Update Your Bolt App']);
        $rider_id = $request->rider_id;

        $pickup_note = PickupNote::where('rider_id', $rider_id)->whereIn('status_id', [2, 3]);

        if ($pickup_note->exists()) {
            $pickup_note = $pickup_note->latest('id')->first();

            $information = array();

            $information['pickup_note_id'] = $pickup_note->id;

            $information['summary'] = array();
            $information['summary']['pickups'] = 0;

            $information['summary']['received']['pickups'] = 0;

            $rider = Rider::find($rider_id);
            $city = City::find($rider->city_id);

            if ($city->location_latitude && $city->location_longitude) {
                $starting_location = $city->location_latitude . ',' . $city->location_longitude;

                $pickup_note_requests = $pickup_note->pickup_note_requests;

                $this->set_order($starting_location, $pickup_note->id, $pickup_note_requests);
            }

            $pickup_note->fresh();

            $pickup_note_requests = $pickup_note->pickup_note_requests->sortBy('ordering');

            $information['pickups'] = array();

            foreach ($pickup_note_requests as $pickup_note_request) {
                $information['summary']['pickups']++;

                $pickup_request = $pickup_note_request->pickup_request;

                $pickup_address = $pickup_request->pickup_address;

                $pickup = array();

                $pickup['pickup_request_id'] = $pickup_request->id;
                $pickup['status'] = $pickup_note_request->status;
                $pickup['ordering'] = $pickup_note_request->ordering;

                if ($pickup_note_request->status) {
                    $information['summary']['received']['pickups']++;
                }

                $pickup['shipper_name'] = $pickup_address->user->name;
                $pickup['person_of_contact'] = $pickup_address->poc;
                $pickup['phone_number'] = $pickup_address->phone;
                $pickup['address'] = $pickup_address->pickup_address;
                $pickup['location_latitude'] = $pickup_address->location_latitude;
                $pickup['location_longitude'] = $pickup_address->location_longitude;

                $information['pickups'][] = $pickup;
            }

            return response()->json(['status' => 0, 'message' => 'Pickup(s) are Assigned', 'information' => $information]);
        } else {
            return response()->json(['status' => 0, 'message' => 'No Pickup(s) Assigned']);
        }
    }

    public function pickup_pick(Request $request)
    {
        $rules = [
            'added_at' => ['required'],
            'pickup_note_id' => ['required', 'integer', 'digits_between:1,10', 'exists:pickup_notes,id'],
            'pickup_request_id' => ['required', 'integer', 'digits_between:1,10', 'exists:pickup_requests,id'],
            'start_location_latitude' => ['required', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'start_location_longitude' => ['required', 'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'],
            'actual_location_latitude' => ['required', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'actual_location_longitude' => ['required', 'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'],
            'shipments' => ['required', 'integer', 'digits_between:1,10']
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        }
        else {
            $rider_id = $request->rider_id;

            $added_at = Carbon::createFromTimestampMs($request->added_at)->toDateTimeString();

            if (!RiderPickup::where('pickup_note_id', $request->pickup_note_id)->where('pickup_request_id', $request->pickup_request_id)->where('pickup_type', 1)->where('added_at', $added_at)->exists()) {

                $pickup_request = PickupRequest::find($request->pickup_request_id);
                $pickup_address = $pickup_request->pickup_address;

                $destination = $request->actual_location_latitude . ',' . $request->actual_location_longitude;

                $rider_pickup = new RiderPickup();

                $rider_pickup->added_at = $added_at;
                $rider_pickup->pickup_note_id = $request->pickup_note_id;
                $rider_pickup->pickup_request_id = $request->pickup_request_id;
                $rider_pickup->pickup_type = 1;
                $rider_pickup->start_location_latitude = $request->start_location_latitude;
                $rider_pickup->start_location_longitude = $request->start_location_longitude;
                $rider_pickup->actual_location_latitude = $request->actual_location_latitude;
                $rider_pickup->actual_location_longitude = $request->actual_location_longitude;

                if ($request->actual_location_latitude > 0 && $request->actual_location_longitude > 0) {
                    $origin = $request->start_location_latitude . ',' . $request->start_location_longitude;

                    $rider_pickup->distance_from_start_to_actual = $this->distance($origin, $destination);

                    if ($pickup_address->location_latitude && $pickup_address->location_longitude) {
                        $rider_pickup->current_location_latitude = $pickup_address->location_latitude;
                        $rider_pickup->current_location_longitude = $pickup_address->location_longitude;

                        $origin = $pickup_address->location_latitude . ',' . $pickup_address->location_longitude;

                        $distance = $this->distance($origin, $destination);

                        $rider_pickup->distance_from_current_to_actual = $distance;

                        if ($distance > 0.1) {
                            $this->verify_pickup_address_location($pickup_address->id);
                        }
                    } else {
                        $pickup_address->location_latitude = $request->actual_location_latitude;
                        $pickup_address->location_longitude = $request->actual_location_longitude;

                        $pickup_address->save();
                    }
                } else {
                    $rider_pickup->distance_from_start_to_actual = 0;

                    if ($pickup_address->location_latitude && $pickup_address->location_longitude) {
                        $rider_pickup->current_location_latitude = $pickup_address->location_latitude;
                        $rider_pickup->current_location_longitude = $pickup_address->location_longitude;
                        $rider_pickup->distance_from_current_to_actual = 0;
                    }
                }

                $rider_pickup->shipments = $request->shipments;

                $rider_pickup->save();

                PickupNoteRequest::where('pickup_note_id', $request->pickup_note_id)->where('pickup_request_id', $request->pickup_request_id)->update(['status' => 1]);

            }

            return response()->json(['status' => 0, 'message' => 'Pickup Pick Successfully', 'pickup_note_id' => $request->pickup_note_id, 'pickup_request_id' => $request->pickup_request_id]);
        }
    }

    public function pickup_not_pick(Request $request)
    {
        $rules = [
            'added_at' => ['required'],
            'pickup_note_id' => ['required', 'integer', 'digits_between:1,10', 'exists:pickup_notes,id'],
            'pickup_request_id' => ['required', 'integer', 'digits_between:1,10', 'exists:pickup_requests,id'],
            'start_location_latitude' => ['required', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'start_location_longitude' => ['required', 'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'],
            'actual_location_latitude' => ['required', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'actual_location_longitude' => ['required', 'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'],
            'reason_id' => ['required', 'integer', 'digits_between:1,10', 'exists:pickup_not_pick_reasons,id'],
            'picture' => ['required', 'image']
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $rider_id = $request->rider_id;

            $added_at = Carbon::createFromTimestampMs($request->added_at)->toDateTimeString();

            if (!RiderPickup::where('pickup_note_id', $request->pickup_note_id)->where('pickup_request_id', $request->pickup_request_id)->where('pickup_type', 0)->where('added_at', $added_at)->exists()) {
                $pickup_request = PickupRequest::find($request->pickup_request_id);
                $pickup_address = $pickup_request->pickup_address;

                $destination = $request->actual_location_latitude . ',' . $request->actual_location_longitude;

                $rider_pickup = new RiderPickup();

                $rider_pickup->added_at = $added_at;
                $rider_pickup->pickup_note_id = $request->pickup_note_id;
                $rider_pickup->pickup_request_id = $request->pickup_request_id;
                $rider_pickup->pickup_type = 0;
                $rider_pickup->start_location_latitude = $request->start_location_latitude;
                $rider_pickup->start_location_longitude = $request->start_location_longitude;
                $rider_pickup->actual_location_latitude = $request->actual_location_latitude;
                $rider_pickup->actual_location_longitude = $request->actual_location_longitude;

                if ($request->actual_location_latitude > 0 && $request->actual_location_longitude > 0) {
                    $origin = $request->start_location_latitude . ',' . $request->start_location_longitude;

                    $rider_pickup->distance_from_start_to_actual = $this->distance($origin, $destination);

                    if ($pickup_address->location_latitude && $pickup_address->location_longitude) {
                        $rider_pickup->current_location_latitude = $pickup_address->location_latitude;
                        $rider_pickup->current_location_longitude = $pickup_address->location_longitude;

                        $origin = $pickup_address->location_latitude . ',' . $pickup_address->location_longitude;

                        $rider_pickup->distance_from_current_to_actual = $this->distance($origin, $destination);
                    }
                } else {
                    $rider_pickup->distance_from_start_to_actual = 0;

                    if ($pickup_address->location_latitude && $pickup_address->location_longitude) {
                        $rider_pickup->current_location_latitude = $pickup_address->location_latitude;
                        $rider_pickup->current_location_longitude = $pickup_address->location_longitude;
                        $rider_pickup->distance_from_current_to_actual = 0;
                    }
                }

                $rider_pickup->pickup_not_pick_reason_id = $request->reason_id;

                $rider_pickup->save();

                $picture_path = 'rider_pickup/' . $rider_pickup->id . '.png';

                Storage::disk('public')->put($picture_path, file_get_contents($request->picture));

                $rider_pickup->picture_path = $picture_path;

                $rider_pickup->save();

                PickupNoteRequest::where('pickup_note_id', $request->pickup_note_id)->where('pickup_request_id', $request->pickup_request_id)->update(['status' => 1]);

            }

            return response()->json(['status' => 0, 'message' => 'Pickup Not Pick Successfully', 'pickup_note_id' => $request->pickup_note_id, 'pickup_request_id' => $request->pickup_request_id]);
        }
    }

    public function pickup_action_log(Request $request)
    {
        $rules = [
            'actions' => ['required', 'array', 'min:1'],
            'actions.*.logged_at' => ['required'],
            'actions.*.type_id' => ['required', 'integer', 'digits_between:1,10', 'exists:pickup_actions,id'],
            'actions.*.pickup_note_id' => ['required', 'integer', 'digits_between:1,10', 'exists:pickup_notes,id'],
            'actions.*.pickup_request_id' => ['required', 'integer', 'digits_between:1,10', 'exists:pickup_requests,id'],
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $rider_id = $request->rider_id;

            foreach ($request->actions as $action) {
                $rider_pickup_action_log = new RiderPickupActionLog();

                $rider_pickup_action_log->logged_at = Carbon::createFromTimestampMs($action['logged_at'])->toDateTimeString();
                $rider_pickup_action_log->type_id = $action['type_id'];
                $rider_pickup_action_log->pickup_note_id = $action['pickup_note_id'];
                $rider_pickup_action_log->pickup_request_id = $action['pickup_request_id'];

                $rider_pickup_action_log->save();
            }

            return response()->json(['status' => 0, 'message' => 'Pickup Action Log(s) Successfully']);
        }
    }

    public function delivery_summary(Request $request)
    {
        return response()->json(['status' => 0, 'message' => 'Please Update Your Bolt App']);
        $rider_id = $request->rider_id;

        $delivery_note = DeliveryNote::where('rider_id', $rider_id)->where('pending_status', 0);
        if ($delivery_note->exists()) {
            $delivery_note = $delivery_note->latest('id')->first();
            $information = array();

            $information['delivery_note_id'] = $delivery_note->id;
            $information['summary'] = array();
            $total_shipments = $delivery_note->shipments_count;
            $information['summary']['deliveries'] = $total_shipments;
            $information['summary']['completed'] = array();
            $information['summary']['completed']['pending'] = 0;
            $information['summary']['completed']['undelivered'] = 0;
            $information['summary']['completed']['delivered'] = 0;
            $rider_deliveries = RiderDelivery::where('delivery_note_id', $delivery_note->id);
            if ($rider_deliveries->exists()) {
                $updated_shipments = 0;
                $undelivered_shipments = 0;
                $delivered_shipments = 0;
                $updated_shipments_count = RiderDelivery::where('delivery_note_id', $delivery_note->id)->count(DB::raw('DISTINCT shipment_id'));
                if ($updated_shipments_count > 0) {
                    $updated_shipments = $updated_shipments_count;
                }

                $undelivered_shipments_count = RiderDelivery::where('delivery_note_id', $delivery_note->id)->where('delivered_status', 0)->count(DB::raw('DISTINCT shipment_id'));
                if ($undelivered_shipments_count > 0) {
                    $undelivered_shipments = $undelivered_shipments_count;
                }

                $delivered_shipments_count = RiderDelivery::where('delivery_note_id', $delivery_note->id)->where('delivered_status', 1)->count(DB::raw('DISTINCT shipment_id'));
                if ($delivered_shipments_count > 0) {
                    $delivered_shipments = $delivered_shipments_count;
                }
                $information['summary']['completed']['pending'] = $total_shipments - $updated_shipments;
                $information['summary']['completed']['undelivered'] = $undelivered_shipments;
                $information['summary']['completed']['delivered'] = $delivered_shipments;
            } else {
                $information['summary']['completed']['pending'] = $total_shipments;
            }

            $information['summary']['requests'] = array();
            $information['summary']['requests']['complains'] = 0;
            $information['summary']['requests']['service_requests'] = 0;
            $information['summary']['requests']['claims'] = 0;

            $information['deliveries'] = array();
            $delivery_note_shipments = $delivery_note->delivery_note_shipments->sortBy('ordering');
            foreach ($delivery_note_shipments as $delivery_note_shipment) {

                $shipment_data = $delivery_note_shipment->shipment;
                $shipment_id = $shipment_data->id;
                $tracking_number = $shipment_data->tracking_number;
                $consignee_name = $shipment_data->consignee_name;
                $consignee_address = $shipment_data->consignee_address;
                $consignee_phone = $shipment_data->consignee_phone_number_1;
                if ($shipment_data->consignee_phone_number_2 != null) {
                    $consignee_phone .= ' / ' . $shipment_data->consignee_phone_number_2;
                }
                $cod_amount = number_format($shipment_data->amount);
                $special_instructions = $shipment_data->special_instructions;
                $remarks = '';
                $journey = ShipmentsJourney::where('shipment_id', $shipment_data->id)->where('remarks', '!=', null)->select('remarks');
                if ($journey->exists()) {
                    $journey = $journey->orderBy('id', 'DESC')->first();
                    $remarks = $journey->remarks;
                }
                $rider_delivery = RiderDelivery::where('delivery_note_id', $delivery_note->id)->where('shipment_id', $shipment_id);
                if ($rider_delivery->exists()) {
                    $rider_delivery = $rider_delivery->first();
                    if ($rider_delivery->delivered_status == 0) {
                        $status = 3;
                    } else if ($rider_delivery->delivered_status == 1) {
                        $status = 2;
                    }
                } else {
                    $status = 1;
                }


                $deliveries = array();
                $deliveries['shipment_id'] = $shipment_id;
                $deliveries['tracking_number'] = $tracking_number;
                $deliveries['consignee_name'] = $consignee_name;
                $deliveries['consignee_address'] = $consignee_address;
                $deliveries['consignee_phone'] = $consignee_phone;
                $deliveries['cod_amount'] = $cod_amount;
                $deliveries['special_instructions'] = $special_instructions;
                $deliveries['remarks'] = $remarks;
                $deliveries['latitude'] = NULL;
                $deliveries['longitude'] = NULL;
                $deliveries['status'] = $status;
                $shipment_location = ConsigneeShipmentLocation::where('shipment_id', $shipment_id);
                if ($shipment_location->exists()) {
                    $shipment_location = $shipment_location->first();
                    $previous_location_id = $shipment_location->previous_location_id;
                    $location = ConsigneeLocation::find($previous_location_id);
                    $deliveries['latitude'] = $location->lat;
                    $deliveries['longitude'] = $location->long;
                }


                if (CrmRequest::where('shipment_id', $shipment_data->id)->whereIn('case_nature_id', [1, 2, 4])->whereNotIn('status_id', [3, 4])->exists()) {
                    $deliveries['request'] = array();
                    $crm_request = CrmRequest::where('shipment_id', $shipment_data->id)->where('case_nature_id', '!=', 3)->latest()->first();
                    $deliveries['request']['id'] = $crm_request->id;

                    if ($crm_request->case_nature_id == 1) {
                        $information['summary']['requests']['complains']++;
                        $deliveries['ordering'] = 1;
                        $deliveries['request']['type'] = 1;
                    }
                    if ($crm_request->case_nature_id == 2) {
                        $information['summary']['requests']['service_requests']++;
                        $deliveries['ordering'] = 2;
                        $deliveries['request']['type'] = 2;
                    }
                    if ($crm_request->case_nature_id == 4) {
                        $information['summary']['requests']['claims']++;
                        $deliveries['ordering'] = 3;
                        $deliveries['request']['type'] = 4;
                    }

                    $deliveries['request']['added_date'] = Carbon::parse($crm_request->created_at)->format('Y-m-d H:i:s');
                    $deliveries['request']['description'] = $crm_request->description;
                    $deliveries['request']['comments'] = array();

                    $crm_request_comments = $crm_request->comments->where('comment_type', 2);
                    if (count($crm_request_comments) > 0) {
                        foreach ($crm_request_comments as $crm_request_comment) {
                            if ($crm_request_comment->comment_by == 0) {
                                $comments = array();
                                $comments['name'] = Admin::find($crm_request_comment->comment_by_id)->name;
                                $comments['comment'] = $crm_request_comment->comment;
                                $comments['type'] = 'Admin';
                                $comments['commented_at'] = Carbon::parse($crm_request_comment->created_at)->format('Y-m-d H:i:s');
                                $deliveries['request']['comments'][] = $comments;
                            } else if ($crm_request_comment->comment_by == 2) {
                                $comments = array();
                                $comments['name'] = Rider::find($crm_request_comment->comment_by_id)->name;
                                $comments['comment'] = $crm_request_comment->comment;
                                $comments['type'] = 'Rider';
                                $comments['commented_at'] = Carbon::parse($crm_request_comment->created_at)->format('Y-m-d H:i:s');
                                $deliveries['request']['comments'][] = $comments;
                            }
                        }
                    }

                } else {
                    $deliveries['ordering'] = 4;
                }

                $information['deliveries'][] = $deliveries;
            }
            usort($information['deliveries'], function ($a, $b) {
                return $a['ordering'] <=> $b['ordering'];
            });
            return response()->json(['status' => 0, 'message' => 'Delivery Note Is Assigned', 'information' => $information]);
        }
        return response()->json(['status' => 0, 'message' => 'No Delivery Note Assigned']);
    }

    public function crm_comment_add(Request $request)
    {
        $rules = [
            'messages' => ['required', 'array', 'min:1'],
            'messages.*.crm_request_id' => ['required', 'integer', 'digits_between:1,10', 'exists:crm_requests,id'],
            'messages.*.comment' => ['required', 'between:0,190'],
            'messages.*.commented_at' => ['required'],
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);
        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $rider_id = $request->rider_id;

            foreach ($request->messages as $message) {
                $commented_at = Carbon::createFromTimestampMs($message['commented_at'])->toDateTimeString();
                $comment = new CrmComments();
                $comment->crm_request_id = $message['crm_request_id'];
                $comment->comment_by_id = $rider_id;
                $comment->comment_by = 2;
                $comment->comment_type = 2;
                $comment->comment = $message['comment'];
                $comment->created_at = $commented_at;
                $comment->updated_at = $commented_at;
                $comment->save();
            }

            return response()->json(['status' => 0, 'message' => 'Comment(s) Added Successfully']);
        }
    }

    public function delivery_action_log(Request $request)
    {
        $rules = [
            'actions' => ['required', 'array', 'min:1'],
            'actions.*.logged_at' => ['required'],
            'actions.*.type_id' => ['required', 'integer', 'digits_between:1,10', 'exists:delivery_actions,id'],
            'actions.*.delivery_note_id' => ['required', 'integer', 'digits_between:1,10', 'exists:delivery_notes,id'],
            'actions.*.shipment_id' => ['required', 'integer', 'digits_between:1,10', 'exists:shipments,id'],
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $rider_id = $request->rider_id;

            foreach ($request->actions as $action) {
                $rider_delivery_action_log = new RiderDeliveryActionLog();

                $rider_delivery_action_log->logged_at = Carbon::createFromTimestampMs($action['logged_at'])->toDateTimeString();
                $rider_delivery_action_log->type_id = $action['type_id'];
                $rider_delivery_action_log->delivery_note_id = $action['delivery_note_id'];
                $rider_delivery_action_log->shipment_id = $action['shipment_id'];

                $rider_delivery_action_log->save();
            }

            return response()->json(['status' => 0, 'message' => 'Delivery Action Log(s) Successfully']);
        }
    }

    public function shipment_delivered(Request $request)
    {
        $message = '';

        $rules = [
            'added_at' => ['required'],
            'delivery_note_id' => ['required', 'integer', 'digits_between:1,10', 'exists:delivery_notes,id'],
            'start_location_latitude' => ['required', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'start_location_longitude' => ['required', 'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'],
            'actual_location_latitude' => ['required', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'actual_location_longitude' => ['required', 'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'],
            'shipment_id' => ['required', 'integer', 'digits_between:1,10', 'exists:shipments,id'],
            'receiver_name' => ['nullable', 'string', 'max:255'],
            'cnic' => ['nullable', 'max:255'],
            'picture' => ['nullable', 'image']
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {

            $rider_id = $request->rider_id;

            $added_at = Carbon::createFromTimestampMs($request->added_at)->toDateTimeString();

            if (!RiderDelivery::where('delivery_note_id', $request->delivery_note_id)->where('shipment_id', $request->shipment_id)->where('delivered_status', 1)->exists()) {
                if (DeliveryNoteShipment::join('delivery_notes as dn', 'delivery_note_shipments.delivery_note_id', 'dn.id')->where('delivery_note_id', $request->delivery_note_id)->where('shipment_id', $request->shipment_id)->where('dn.rider_id', $rider_id)->exists()) {
                    {

                        $destination = $request->actual_location_latitude . ',' . $request->actual_location_longitude;

                        $rider_delivery = new RiderDelivery();

                        $rider_delivery->added_at = $added_at;
                        $rider_delivery->delivery_note_id = $request->delivery_note_id;
                        $rider_delivery->shipment_id = $request->shipment_id;
                        $rider_delivery->rider_id = $request->rider_id;
                        $rider_delivery->start_location_latitude = $request->start_location_latitude;
                        $rider_delivery->start_location_longitude = $request->start_location_longitude;
                        $rider_delivery->actual_location_latitude = $request->actual_location_latitude;
                        $rider_delivery->actual_location_longitude = $request->actual_location_longitude;
                        $rider_delivery->rider_status_id = 14;
                        $rider_delivery->delivered_status = 1;
                        $received_by = NULL;
                        if ($request->has('receiver_name')) {
                            $received_by = $request->receiver_name;
                        }
                        if ($request->has('cnic')) {
                            $rider_delivery->cnic = $request->cnic;
                            $received_by .= ' | ' . $request->cnic;
                        }

//                if($request->has('receiver_name')){
//                    $rider_delivery->receiver_name = $request->receiver_name;
//                }

                        $shipment = Shipment::find($request->shipment_id);

                        $consignee_phone_number_1 = $shipment->consignee_phone_number_1;
                        $consignee_phone_number_2 = $shipment->consignee_phone_number_2;
                        $consignee_address = $shipment->consignee_address;

                        $coordinates = ConsigneeLocation::where(function ($sub_query) use ($consignee_phone_number_1, $consignee_phone_number_2) {
                            $sub_query->where('phone_number', $consignee_phone_number_1)
                                ->orwhere('phone_number', $consignee_phone_number_2);
                        })->where('address', $consignee_address);

                        if ($request->actual_location_latitude > 0 && $request->actual_location_longitude > 0) {
                            $origin = $request->start_location_latitude . ',' . $request->start_location_longitude;

                            $rider_delivery->distance_from_start_to_actual = $this->distance($origin, $destination);

                            if ($coordinates->exists()) {
                                $coordinates = $coordinates->latest()->first();

                                $rider_delivery->current_location_latitude = $coordinates->lat;
                                $rider_delivery->current_location_longitude = $coordinates->long;

                                $origin = $coordinates->lat . ',' . $coordinates->long;

                                $distance = $this->distance($origin, $destination);

                                $rider_delivery->distance_from_current_to_actual = $distance;
                            }
                        } else {
                            $rider_delivery->distance_from_start_to_actual = 0;

                            if ($coordinates->exists()) {
                                $rider_delivery->current_location_latitude = $coordinates->lat;
                                $rider_delivery->current_location_longitude = $coordinates->long;
                                $rider_delivery->distance_from_current_to_actual = 0;
                            }
                        }
                        $rider_delivery->save();


                        if ($request->has('picture')) {
                            $picture_path = 'rider_delivery/' . $rider_delivery->id . '.png';
                            Storage::disk('public')->put($picture_path, file_get_contents($request->picture));
                            $rider_delivery->picture_path = $picture_path;
                            $rider_delivery->save();
                        }

                        if (DeliveryNote::where('id', $request->delivery_note_id)->where('pending_status', 0)->exists()) {
                            if ($shipment->booking_type_id == 2) {
                                $shipment->shipper_status_id = 30;
                                $shipment->consignee_status_id = 30;

                                $shipment->received_amount = $shipment->amount;
                                DeliveryNoteShipment::where('delivery_note_id', $request->delivery_note_id)->where('shipment_id', $shipment->id)->update(['status' => 2, 'update_type' => 1]);
                                ShipmentsJourneyController::add($shipment->id, 30, 30, NULL, NULL, NULL, NULL, $request->delivery_note_id, NULL, 1, $received_by, $rider_id);
                            } else if ($shipment->booking_type_id == 3) {
                                $shipment->shipper_status_id = 36;
                                $shipment->consignee_status_id = 36;

                                $shipment->received_amount = $shipment->amount;
                                DeliveryNoteShipment::where('delivery_note_id', $request->delivery_note_id)->where('shipment_id', $shipment->id)->update(['status' => 3, 'update_type' => 1]);
                                ShipmentsJourneyController::add($shipment->id, 36, 36, NULL, NULL, NULL, NULL, $request->delivery_note_id, NULL, 1, $received_by, $rider_id);
                            } else if ($shipment->booking_type_id == 4) {
                                ShipmentsJourneyController::add($shipment->id, 14, 14, NULL, NULL, NULL, NULL, $request->delivery_note_id, NULL, 1, $received_by, $rider_id);

                                if ($shipment->charges_mode_id == 1) {
                                    $shipment->shipper_status_id = 14;
                                    $shipment->consignee_status_id = 14;

                                    DeliveryNoteShipment::where('delivery_note_id', $request->delivery_note_id)->where('shipment_id', $shipment->id)->update(['status' => 7, 'update_type' => 1]);

                                } else {
                                    $shipment->shipper_status_id = 14;
                                    $shipment->consignee_status_id = 14;
                                    $shipment->received_amount = $shipment->amount;
                                    DeliveryNoteShipment::where('delivery_note_id', $request->delivery_note_id)->where('shipment_id', $shipment->id)->update(['status' => 6, 'update_type' => 1]);

                                }
                            } else {
                                $shipment->shipper_status_id = 14;
                                $shipment->consignee_status_id = 14;
                                $shipment->received_amount = $shipment->amount;
                                DeliveryNoteShipment::where('delivery_note_id', $request->delivery_note_id)->where('shipment_id', $shipment->id)->update(['status' => 6, 'update_type' => 1]);
                                ShipmentsJourneyController::add($shipment->id, 14, 14, NULL, NULL, NULL, NULL, $request->delivery_note_id, NULL, 1, $received_by, $rider_id);

                                /*if($shipment->packaging_material_request == 1){
                                    self::delivery_packaging_material_update($shipment->tracking_number);
                                }*/
                            }
                            $shipment->save();
                        }
                        $rider_delivery_note_status = RiderDeliveryNoteStatus::where('delivery_note_id', $request->delivery_note_id);
                        if (!$rider_delivery_note_status->exists()) {
                            $new_status = new RiderDeliveryNoteStatus();
                            $new_status->delivery_note_id = $request->delivery_note_id;
                            $new_status->status = 1;
                            $new_status->save();
                        }
                        $updated_shipments_count = DeliveryNoteShipment::where('delivery_note_id', $request->delivery_note_id)->where('status', 0)->count();

                        if ($updated_shipments_count == 0) {
                            DeliveryNote::where('id', $request->delivery_note_id)->update(['pending_status' => 1, 'pending_for_verification_at' => Carbon::now()]);
                            $rider_delivery_note_status = RiderDeliveryNoteStatus::where('delivery_note_id', $request->delivery_note_id);
                            if ($rider_delivery_note_status->exists()) {
                                $rider_delivery_note_status = $rider_delivery_note_status->first();
                                $rider_delivery_note_status->status = 2;
                                $rider_delivery_note_status->save();
                            }
                        }


                        $delivered_status = array(14, 30, 36, 37);
                        $delivered_shipment_ids = DeliveryNoteShipment::where('delivery_note_id', $request->delivery_note_id)->where('status', '>', 1)->where('status', '!=', 8)->select('shipment_id')->get();
                        $dncc_amount = Shipment::whereIn('id', $delivered_shipment_ids)->where(function ($query) {
                            $query->where(function ($sub_query) {
                                $sub_query->where('booking_type_id', '!=', 4);
                            })
                                ->orWhere(function ($sub_query) {
                                    $sub_query->where('booking_type_id', '=', 4)
                                        ->where('charges_mode_id', '=', 2);
                                });
                        })->sum('received_amount');
                        $count = count($delivered_shipment_ids);
                        $delivery_note_data = DeliveryNote::find($request->delivery_note_id);
                        $delivery_note_data->delivered_shipments = $count;
                        $delivery_note_data->received_cod_amount = $dncc_amount;
                        $delivery_note_data->last_updated_at = Carbon::now();
                        $delivery_note_data->status_updated_at = Carbon::now();
                        $delivery_note_data->save();

                        $message = 'Shipment is marked as delivered Successfully';
                    }
                } else {
                    $message = 'Shipment is marked as delivered already';
                }

            }
        }
        return response()->json(['status' => 0, 'message' => $message, 'delivery_note_id' => $request->delivery_note_id, 'shipment_id' => $request->shipment_id]);
    }

    public function shipment_undelivered(Request $request)
    {
        $rules = [
            'added_at' => ['required'],
            'delivery_note_id' => ['required', 'integer', 'digits_between:1,10', 'exists:delivery_notes,id'],
            'start_location_latitude' => ['required', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'start_location_longitude' => ['required', 'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'],
            'actual_location_latitude' => ['required', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'actual_location_longitude' => ['required', 'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'],
            'shipment_id' => ['required', 'integer', 'digits_between:1,10', 'exists:shipments,id'],
            'shipper_status_id' => ['required', 'integer', 'digits_between:1,10', 'exists:shipment_status,id'],
            'status_reason_id' => ['required', 'integer', 'digits_between:1,10', 'exists:shipment_status_reason,id'],
            'remarks' => ['nullable', 'string', 'max:255'],
            'picture' => ['required', 'image']
        ];
        $message = '';

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {

            $rider_id = $request->rider_id;

            $added_at = Carbon::createFromTimestampMs($request->added_at)->toDateTimeString();
            if (!RiderDelivery::where('delivery_note_id', $request->delivery_note_id)->where('shipment_id', $request->shipment_id)->where('delivered_status', 1)->exists()) {
                if (DeliveryNoteShipment::join('delivery_notes as dn', 'delivery_note_shipments.delivery_note_id', 'dn.id')->where('delivery_note_id', $request->delivery_note_id)->where('shipment_id', $request->shipment_id)->where('dn.rider_id', $rider_id)->exists()) {
                    $shipment = Shipment::find($request->shipment_id);

                    $destination = $request->actual_location_latitude . ',' . $request->actual_location_longitude;

                    $rider_delivery = new RiderDelivery();

                    $rider_delivery->added_at = $added_at;
                    $rider_delivery->delivery_note_id = $request->delivery_note_id;
                    $rider_delivery->shipment_id = $request->shipment_id;
                    $rider_delivery->rider_id = $request->rider_id;
                    $rider_delivery->start_location_latitude = $request->start_location_latitude;
                    $rider_delivery->start_location_longitude = $request->start_location_longitude;
                    $rider_delivery->actual_location_latitude = $request->actual_location_latitude;
                    $rider_delivery->actual_location_longitude = $request->actual_location_longitude;
                    $rider_delivery->rider_status_id = $request->shipper_status_id;
                    $rider_delivery->rider_status_reason_id = $request->status_reason_id;
                    $rider_delivery->delivered_status = 0;

                    $consignee_phone_number_1 = $shipment->consignee_phone_number_1;
                    $consignee_phone_number_2 = $shipment->consignee_phone_number_2;
                    $consignee_address = $shipment->consignee_address;

                    $coordinates = ConsigneeLocation::where(function ($sub_query) use ($consignee_phone_number_1, $consignee_phone_number_2) {
                        $sub_query->where('phone_number', $consignee_phone_number_1)
                            ->orwhere('phone_number', $consignee_phone_number_2);
                    })->where('address', $consignee_address);

                    if ($request->actual_location_latitude > 0 && $request->actual_location_longitude > 0) {
                        $origin = $request->start_location_latitude . ',' . $request->start_location_longitude;

                        $rider_delivery->distance_from_start_to_actual = $this->distance($origin, $destination);

                        if ($coordinates->exists()) {
                            $coordinates = $coordinates->latest()->first();

                            $rider_delivery->current_location_latitude = $coordinates->lat;
                            $rider_delivery->current_location_longitude = $coordinates->long;

                            $origin = $coordinates->lat . ',' . $coordinates->long;

                            $distance = $this->distance($origin, $destination);

                            $rider_delivery->distance_from_current_to_actual = $distance;
                        }
                    } else {
                        $rider_delivery->distance_from_start_to_actual = 0;

                        if ($coordinates->exists()) {
                            $rider_delivery->current_location_latitude = $coordinates->lat;
                            $rider_delivery->current_location_longitude = $coordinates->long;
                            $rider_delivery->distance_from_current_to_actual = 0;
                        }
                    }
                    $rider_delivery->save();

                    $picture_path = 'rider_delivery/' . $rider_delivery->id . '.png';
                    Storage::disk('public')->put($picture_path, file_get_contents($request->picture));
                    $rider_delivery->picture_path = $picture_path;
                    $rider_delivery->save();

                    if (DeliveryNote::where('id', $request->delivery_note_id)->where('pending_status', 0)->exists()) {

                        $shipment->shipper_status_id = $request->shipper_status_id;
                        $shipment->consignee_status_id = $request->shipper_status_id;
                        $shipment->save();

                        $remarks = NULL;

                        if ($request->has('remarks')) {
                            $remarks = $request->remarks;
                        }

                        ShipmentsJourneyController::add($shipment->id, $request->shipper_status_id, $request->shipper_status_id, $request->status_reason_id, $remarks, NULL, NULL, $request->delivery_note_id, NULL, 0, NULL, $rider_id);
                        DeliveryNoteShipment::where('delivery_note_id', $request->delivery_note_id)->where('shipment_id', $shipment->id)->update(['status' => 1, 'update_type' => 1]);
                        $rider_delivery_note_status = RiderDeliveryNoteStatus::where('delivery_note_id', $request->delivery_note_id);
                        if (!$rider_delivery_note_status->exists()) {
                            $new_status = new RiderDeliveryNoteStatus();
                            $new_status->delivery_note_id = $request->delivery_note_id;
                            $new_status->status = 2;
                            $new_status->save();
                        } else {
                            $rider_delivery_note_status = $rider_delivery_note_status->first();
                            $rider_delivery_note_status->status = 2;
                            $rider_delivery_note_status->save();
                        }
                    }


                    $updated_shipments_count = DeliveryNoteShipment::where('delivery_note_id', $request->delivery_note_id)->where('status', 0)->count();
                    if ($updated_shipments_count == 0) {
                        DeliveryNote::where('id', $request->delivery_note_id)->update(['pending_status' => 1, 'pending_for_verification_at' => Carbon::now()]);
                    }

                    $message = 'Shipment is marked as Undelivered Successfully';
                }
            } else {
                $message = 'Shipment is already marked as Delivered';
            }
            return response()->json(['status' => 0, 'message' => $message, 'delivery_note_id' => $request->delivery_note_id, 'shipment_id' => $request->shipment_id]);

        }
    }

    public function pickup_summary_v2(Request $request)
    {
        $rider_id = $request->rider_id;

        $pickup_note = V2PickupNote::where('rider_id', $rider_id)->where('status', 0)->orderBy('id', 'DESC');

        if ($pickup_note->exists()) {
            $pickup_note = $pickup_note->first();

            $information = array();

            $information['pickup_note_id'] = $pickup_note->id;

            $information['summary'] = array();
            $information['summary']['pickups'] = 0;

            $information['summary']['received']['pickups'] = 0;

            $rider = Rider::find($rider_id);
            $city = City::find($rider->city_id);

            if ($city->location_latitude && $city->location_longitude) {
                $starting_location = $city->location_latitude . ',' . $city->location_longitude;

                $pickup_note_requests = $pickup_note->pickup_note_requests;

                $this->set_order_v2($starting_location, $pickup_note->id, $pickup_note_requests);
            }

            $pickup_note->fresh();

            $pickup_note_requests = $pickup_note->pickup_note_requests->sortBy('ordering');

            $information['pickups'] = array();

            foreach ($pickup_note_requests as $pickup_note_request) {
                $information['summary']['pickups']++;

                $pickup_request = $pickup_note_request->pickup_request;

                $pickup_address = $pickup_request->pickup_address;

                $pickup = array();

                $pickup['pickup_request_id'] = $pickup_request->id;
                $pickup['status'] = $pickup_note_request->status;
                $pickup['ordering'] = $pickup_note_request->ordering;

                $booked_shipments = V2PickupRequestShipment::where('pickup_request_id', $pickup_request->id)->count('id');

                if ($booked_shipments != $pickup_request->booked) {
                    $pickup_request->booked = $booked_shipments;

                    $pickup_request->save();
                }

                $pickup['shipments'] = $booked_shipments;

                if ($pickup_note_request->status) {
                    $information['summary']['received']['pickups']++;
                }

                $pickup['shipper_name'] = $pickup_address->user->name;
                $pickup['person_of_contact'] = $pickup_address->poc;
                $pickup['phone_number'] = $pickup_address->phone;
                $pickup['address'] = $pickup_address->pickup_address;
                $pickup['location_latitude'] = $pickup_address->location_latitude;
                $pickup['location_longitude'] = $pickup_address->location_longitude;

                $information['pickups'][] = $pickup;
            }

            return response()->json(['status' => 0, 'message' => 'Pickup(s) are Assigned', 'information' => $information]);
        } else {
            return response()->json(['status' => 0, 'message' => 'No Pickup(s) Assigned']);
        }
    }

    public function pickup_pick_v2(Request $request)
    {
        $rules = [
            'added_at' => ['required'],
            'pickup_note_id' => ['required', 'integer', 'digits_between:1,10', 'exists:v2_pickup_notes,id'],
            'pickup_request_id' => ['required', 'integer', 'digits_between:1,10', 'exists:v2_pickup_requests,id'],
            'start_location_latitude' => ['required', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'start_location_longitude' => ['required', 'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'],
            'actual_location_latitude' => ['required', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'actual_location_longitude' => ['required', 'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'],
            'shipments' => ['required', 'integer', 'digits_between:1,10'],
            'picture' => ['nullable', 'image']
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $rider_id = $request->rider_id;

            $added_at = Carbon::createFromTimestampMs($request->added_at)->toDateTimeString();

            if (!V2RiderPickup::where('pickup_note_id', $request->pickup_note_id)->where('pickup_request_id', $request->pickup_request_id)->where('pickup_type', 1)->where('added_at', $added_at)->exists()) {
                if (V2PickupRequest::where('id', $request->pickup_request_id)->where('current_rider_id', $rider_id)->exists()) {
                    $pickup_request = V2PickupRequest::find($request->pickup_request_id);
                    $pickup_request->pickup_in_route = 0;
                    $pickup_request->save();
                    $pickup_address = $pickup_request->pickup_address;

                    $destination = $request->actual_location_latitude . ',' . $request->actual_location_longitude;

                    $rider_pickup = new V2RiderPickup();

                    $rider_pickup->added_at = $added_at;
                    $rider_pickup->pickup_note_id = $request->pickup_note_id;
                    $rider_pickup->pickup_request_id = $request->pickup_request_id;
                    $rider_pickup->pickup_type = 1;
                    $rider_pickup->start_location_latitude = $request->start_location_latitude;
                    $rider_pickup->start_location_longitude = $request->start_location_longitude;
                    $rider_pickup->actual_location_latitude = $request->actual_location_latitude;
                    $rider_pickup->actual_location_longitude = $request->actual_location_longitude;

                    if ($request->actual_location_latitude > 0 && $request->actual_location_longitude > 0) {
                        $origin = $request->start_location_latitude . ',' . $request->start_location_longitude;

                        $rider_pickup->distance_from_start_to_actual = $this->distance($origin, $destination);

                        if ($pickup_address->location_latitude && $pickup_address->location_longitude) {
                            $rider_pickup->current_location_latitude = $pickup_address->location_latitude;
                            $rider_pickup->current_location_longitude = $pickup_address->location_longitude;

                            $origin = $pickup_address->location_latitude . ',' . $pickup_address->location_longitude;

                            $distance = $this->distance($origin, $destination);

                            $rider_pickup->distance_from_current_to_actual = $distance;

                            if ($distance > 0.1) {
                                $this->verify_pickup_address_location_v2($pickup_address->id);
                            }
                        } else {
                            $pickup_address->location_latitude = $request->actual_location_latitude;
                            $pickup_address->location_longitude = $request->actual_location_longitude;

                            $pickup_address->save();
                        }
                    } else {
                        $rider_pickup->distance_from_start_to_actual = 0;

                        if ($pickup_address->location_latitude && $pickup_address->location_longitude) {
                            $rider_pickup->current_location_latitude = $pickup_address->location_latitude;
                            $rider_pickup->current_location_longitude = $pickup_address->location_longitude;
                            $rider_pickup->distance_from_current_to_actual = 0;
                        }
                    }

                    $rider_pickup->shipments = $request->shipments;

                    $rider_pickup->save();

                    if ($request->has('picture')) {
                        $picture_path = 'rider_pickup/' . $rider_pickup->id . '.png';
                        Storage::disk('public')->put($picture_path, file_get_contents($request->picture));
                        $rider_pickup->picture_path = $picture_path;

                        $rider_pickup->save();
                    }

                    V2PickupNoteRequest::where('pickup_note_id', $request->pickup_note_id)->where('pickup_request_id', $request->pickup_request_id)->update(['status' => 1]);
                    $pickup_note_requests_count = V2PickupNoteRequest::where('pickup_note_id', $request->pickup_note_id)->where('status', 0)->count();
                    if ($pickup_note_requests_count == 0) {
                        V2PickupNote::where('id', $request->pickup_note_id)->update(['status' => 1]);
                    }

                    if ($request->has('tracking_numbers')) {
                        foreach ($request->tracking_numbers as $tracking_number) {
                            $shipment = Shipment::where('tracking_number', $tracking_number);
                            if ($shipment->exists()) {
                                $shipment = $shipment->first();
                                if ($shipment->shipper_status_id == 17) {
                                    AdminPickupsController::generate($shipment->id);
                                }
                                $shipment->shipper_status_id = 53;
                                $shipment->consignee_status_id = 53;
                                $shipment->save();
                                ShipmentsJourneyController::add($shipment->id, 53, 53, NULL, NULL, NULL, NULL, $request->pickup_request_id, $request->pickup_note_id, 1, NULL, $rider_id);
                            }
                        }
                        NotificationsController::send(73, $request->tracking_numbers, $request->pickup_request_id);
                    }
                }

            }

            return response()->json(['status' => 0, 'message' => 'Pickup Pick Successfully', 'pickup_note_id' => $request->pickup_note_id, 'pickup_request_id' => $request->pickup_request_id]);
        }
    }

    public function pickup_not_pick_v2(Request $request)
    {
        $rules = [
            'added_at' => ['required'],
            'pickup_note_id' => ['required', 'integer', 'digits_between:1,10', 'exists:v2_pickup_notes,id'],
            'pickup_request_id' => ['required', 'integer', 'digits_between:1,10', 'exists:v2_pickup_requests,id'],
            'start_location_latitude' => ['required', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'start_location_longitude' => ['required', 'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'],
            'actual_location_latitude' => ['required', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'actual_location_longitude' => ['required', 'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'],
            'reason_id' => ['required', 'integer', 'digits_between:1,10', 'exists:v2_pickup_request_not_pick_reasons,id'],
            'rider_remarks' => ['nullable'],
            'picture' => ['required', 'image']
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $rider_id = $request->rider_id;

            $added_at = Carbon::createFromTimestampMs($request->added_at)->toDateTimeString();

            if (!V2RiderPickup::where('pickup_note_id', $request->pickup_note_id)->where('pickup_request_id', $request->pickup_request_id)->where('pickup_type', 0)->where('added_at', $added_at)->exists()) {
                if (V2PickupRequest::where('id', $request->pickup_request_id)->where('current_rider_id', $rider_id)->exists()) {
                    $pickup_request = V2PickupRequest::find($request->pickup_request_id);

                    $pickup_address = $pickup_request->pickup_address;

                    $pickup_request->status_id = 3;
                    $pickup_request->save();
                    $pickup_request_attempt = $pickup_request->pickup_attempt_latest->where('rider_id', $rider_id)->first();
                    $pickup_request_attempt->reason_id = $request->reason_id;
                    $pickup_request_attempt->save();

                    $destination = $request->actual_location_latitude . ',' . $request->actual_location_longitude;

                    $rider_pickup = new V2RiderPickup();

                    $rider_pickup->added_at = $added_at;
                    $rider_pickup->pickup_note_id = $request->pickup_note_id;
                    $rider_pickup->pickup_request_id = $request->pickup_request_id;
                    $rider_pickup->pickup_type = 0;
                    $rider_pickup->start_location_latitude = $request->start_location_latitude;
                    $rider_pickup->start_location_longitude = $request->start_location_longitude;
                    $rider_pickup->actual_location_latitude = $request->actual_location_latitude;
                    $rider_pickup->actual_location_longitude = $request->actual_location_longitude;

                    if ($request->actual_location_latitude > 0 && $request->actual_location_longitude > 0) {
                        $origin = $request->start_location_latitude . ',' . $request->start_location_longitude;

                        $rider_pickup->distance_from_start_to_actual = $this->distance($origin, $destination);

                        if ($pickup_address->location_latitude && $pickup_address->location_longitude) {
                            $rider_pickup->current_location_latitude = $pickup_address->location_latitude;
                            $rider_pickup->current_location_longitude = $pickup_address->location_longitude;

                            $origin = $pickup_address->location_latitude . ',' . $pickup_address->location_longitude;

                            $rider_pickup->distance_from_current_to_actual = $this->distance($origin, $destination);
                        }
                    } else {
                        $rider_pickup->distance_from_start_to_actual = 0;

                        if ($pickup_address->location_latitude && $pickup_address->location_longitude) {
                            $rider_pickup->current_location_latitude = $pickup_address->location_latitude;
                            $rider_pickup->current_location_longitude = $pickup_address->location_longitude;
                            $rider_pickup->distance_from_current_to_actual = 0;
                        }
                    }

                    $rider_pickup->pickup_not_pick_reason_id = $request->reason_id;
                    $rider_pickup->rider_remarks = str_replace("\"", "", $request->rider_remarks);

                    $rider_pickup->save();

                    $picture_path = 'rider_pickup/' . $rider_pickup->id . '.png';

                    Storage::disk('public')->put($picture_path, file_get_contents($request->picture));

                    $rider_pickup->picture_path = $picture_path;

                    $rider_pickup->save();

                    V2PickupNoteRequest::where('pickup_note_id', $request->pickup_note_id)->where('pickup_request_id', $request->pickup_request_id)->update(['status' => 1]);
                    $pickup_note_requests_count = V2PickupNoteRequest::where('pickup_note_id', $request->pickup_note_id)->where('status', 0)->count();
                    if ($pickup_note_requests_count == 0) {
                        V2PickupNote::where('id', $request->pickup_note_id)->update(['status' => 1]);
                    }

                    NotificationsController::send(105, $request->pickup_request_id, $request->reason_id);
                }
            }

            return response()->json(['status' => 0, 'message' => 'Pickup Not Pick Successfully', 'pickup_note_id' => $request->pickup_note_id, 'pickup_request_id' => $request->pickup_request_id]);
        }
    }

    public function pickup_action_log_v2(Request $request)
    {
        $rules = [
            'actions' => ['required', 'array', 'min:1'],
            'actions.*.logged_at' => ['required'],
            'actions.*.type_id' => ['required', 'integer', 'digits_between:1,10', 'exists:pickup_actions,id'],
            'actions.*.pickup_note_id' => ['required', 'integer', 'digits_between:1,10', 'exists:v2_pickup_notes,id'],
            'actions.*.pickup_request_id' => ['required', 'integer', 'digits_between:1,10', 'exists:v2_pickup_requests,id'],
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $rider_id = $request->rider_id;

            foreach ($request->actions as $action) {
                $rider_pickup_action_log = new V2RiderPickupActionLog();

                $rider_pickup_action_log->logged_at = Carbon::createFromTimestampMs($action['logged_at'])->toDateTimeString();
                $rider_pickup_action_log->type_id = $action['type_id'];
                $rider_pickup_action_log->pickup_note_id = $action['pickup_note_id'];
                $rider_pickup_action_log->pickup_request_id = $action['pickup_request_id'];

                $rider_pickup_action_log->save();
            }

            return response()->json(['status' => 0, 'message' => 'Pickup Action Log(s) Successfully']);
        }
    }

    public function pickup_check_tracking_number(Request $request)
    {
        return response()->json(['status' => 1, 'message' => 'Please Update Your Bolt App']);
        $rules = [
            'tracking_number' => ['required']
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Tracking Number Required']);
        } else {
            $tracking_number = NULL;
            $items = NULL;
            $pieces = NULL;

            $shipment = Shipment::where('tracking_number', $request->tracking_number);

            if ($shipment->exists()) {
                $shipment = $shipment->first();

                $tracking_number = $shipment->tracking_number;

                if ($shipment->booking_type_id == 3) {
                    $items = ShipmentItem::where('shipment_id', $shipment->id)->pluck('id')->toArray();
                } else if ($shipment->pieces > 1) {
                    $pieces = ShipmentPiece::where('shipment_id', $shipment->id)->pluck('tracking_number')->toArray();
                }
            } else {
                $shipment_item = ShipmentItem::find($request->tracking_number);

                if ($shipment_item) {
                    $shipment = Shipment::find('tracking_number', $shipment_item->shipment_id);

                    $tracking_number = $shipment->tracking_number;

                    $items = ShipmentItem::where('shipment_id', $shipment->id)->pluck('id')->toArray();
                } else {
                    $shipment_piece = ShipmentPiece::where('tracking_number', $request->tracking_number);

                    if ($shipment_piece->exists()) {
                        $shipment_piece = $shipment_piece->first();

                        $shipment = Shipment::find('tracking_number', $shipment_piece->shipment_id);

                        $tracking_number = $shipment->tracking_number;

                        $pieces = ShipmentPiece::where('shipment_id', $shipment->id)->pluck('tracking_number')->toArray();
                    }
                }
            }

            if ($tracking_number) {
                if ($shipment->shipper_status_id == 1 || $shipment->shipper_status_id == 17 || $shipment->shipper_status_id == 53) {
                    return response()->json(['status' => 0, 'message' => 'Shipment Found', 'tracking_number' => $tracking_number, 'items' => $items, 'pieces' => $pieces]);
                } else {
                    return response()->json(['status' => 1, 'message' => 'Shipment is already Picked!']);
                }
            } else {
                return response()->json(['status' => 1, 'message' => 'Invalid Tracking Number!']);
            }
        }
    }

    public function delivery_summary_multiple(Request $request)
    {
        return response()->json(['status' => 0, 'message' => 'Please Update Your Bolt App']);
        $rider_id = $request->rider_id;

        $delivery_notes = DeliveryNote::where('rider_id', $rider_id)->where('status', 0)->where('pending_status', 0);

        if ($delivery_notes->exists()) {
            $delivery_notes = $delivery_notes->get();

            $nodes = array();

            foreach ($delivery_notes as $delivery_note) {

                if ($delivery_note->shipments_count == $delivery_note->delivered_shipments) {
                    continue;
                }
                $information = array();

                $information['delivery_note_id'] = $delivery_note->id;
                $information['assigned_date'] = $delivery_note->created_at->toDateTimeString();
                $information['no_of_parcels'] = $delivery_note->shipments_count;
                $information['summary'] = array();
                $total_shipments = $delivery_note->shipments_count;
                $information['summary']['deliveries'] = $total_shipments;
                $information['summary']['completed'] = array();
                $information['summary']['completed']['pending'] = 0;
                $information['summary']['completed']['undelivered'] = 0;
                $information['summary']['completed']['delivered'] = 0;
                $rider_deliveries = RiderDelivery::where('delivery_note_id', $delivery_note->id);

                $updated_shipments = 0;

                if ($rider_deliveries->exists()) {
                    $undelivered_shipments = 0;
                    $delivered_shipments = 0;
                    $updated_shipments_count = RiderDelivery::where('delivery_note_id', $delivery_note->id)->count(DB::raw('DISTINCT shipment_id'));
                    if ($updated_shipments_count > 0) {
                        $updated_shipments = $updated_shipments_count;
                    }

                    $undelivered_shipments_count = RiderDelivery::where('delivery_note_id', $delivery_note->id)->where('delivered_status', 0)->count(DB::raw('DISTINCT shipment_id'));
                    if ($undelivered_shipments_count > 0) {
                        $undelivered_shipments = $undelivered_shipments_count;
                    }

                    $delivered_shipments_count = RiderDelivery::where('delivery_note_id', $delivery_note->id)->where('delivered_status', 1)->count(DB::raw('DISTINCT shipment_id'));
                    if ($delivered_shipments_count > 0) {
                        $delivered_shipments = $delivered_shipments_count;
                    }
                    $information['summary']['completed']['pending'] = $total_shipments - $updated_shipments;
                    $information['summary']['completed']['undelivered'] = $undelivered_shipments;
                    $information['summary']['completed']['delivered'] = $delivered_shipments;
                } else {
                    $information['summary']['completed']['pending'] = $total_shipments;
                }

                $information['summary']['requests'] = array();
                $information['summary']['requests']['complains'] = 0;
                $information['summary']['requests']['service_requests'] = 0;
                $information['summary']['requests']['claims'] = 0;

                $information['deliveries'] = array();
                $delivery_note_shipments = $delivery_note->delivery_note_shipments->sortBy('ordering');
                foreach ($delivery_note_shipments as $delivery_note_shipment) {

                    $shipment_data = $delivery_note_shipment->shipment;
                    $shipment_id = $shipment_data->id;
                    $tracking_number = $shipment_data->tracking_number;
                    $consignee_name = $shipment_data->consignee_name;
                    $consignee_address = $shipment_data->consignee_address;
                    $consignee_phone = $shipment_data->consignee_phone_number_1;
                    if ($shipment_data->consignee_phone_number_2 != null) {
                        $consignee_phone .= ' / ' . $shipment_data->consignee_phone_number_2;
                    }
                    $cod_amount = number_format($shipment_data->amount);
                    $special_instructions = $shipment_data->special_instructions;
                    $remarks = '';
                    $journey = ShipmentsJourney::where('shipment_id', $shipment_data->id)->where('remarks', '!=', null)->select('remarks');
                    if ($journey->exists()) {
                        $journey = $journey->orderBy('id', 'DESC')->first();
                        $remarks = $journey->remarks;
                    }
                    $rider_delivery = RiderDelivery::where('delivery_note_id', $delivery_note->id)->where('shipment_id', $shipment_id);
                    if ($rider_delivery->exists()) {
                        $rider_delivery = $rider_delivery->first();
                        if ($rider_delivery->delivered_status == 0) {
                            $status = 3;
                        } else if ($rider_delivery->delivered_status == 1) {
                            $status = 2;
                        }
                    } else {
                        $status = 1;
                    }


                    $deliveries = array();
                    $deliveries['shipment_id'] = $shipment_id;
                    $deliveries['tracking_number'] = $tracking_number;
                    $deliveries['consignee_name'] = $consignee_name;
                    $deliveries['consignee_address'] = $consignee_address;
                    $deliveries['consignee_phone'] = $consignee_phone;
                    $deliveries['cod_amount'] = $cod_amount;
                    $deliveries['special_instructions'] = $special_instructions;
                    $deliveries['remarks'] = $remarks;
                    $deliveries['latitude'] = NULL;
                    $deliveries['longitude'] = NULL;
                    $deliveries['status'] = $status;
                    $shipment_location = ConsigneeShipmentLocation::where('shipment_id', $shipment_id);
                    if ($shipment_location->exists()) {
                        $shipment_location = $shipment_location->first();
                        $previous_location_id = $shipment_location->previous_location_id;
                        $location = ConsigneeLocation::find($previous_location_id);
                        $deliveries['latitude'] = $location->lat;
                        $deliveries['longitude'] = $location->long;
                    }


                    if (CrmRequest::where('shipment_id', $shipment_data->id)->whereIn('case_nature_id', [1, 2, 4])->whereNotIn('status_id', [3, 4])->exists()) {
                        $deliveries['request'] = array();
                        $crm_request = CrmRequest::where('shipment_id', $shipment_data->id)->where('case_nature_id', '!=', 3)->latest()->first();
                        $deliveries['request']['id'] = $crm_request->id;

                        if ($crm_request->case_nature_id == 1) {
                            $information['summary']['requests']['complains']++;
                            $deliveries['ordering'] = 1;
                            $deliveries['request']['type'] = 1;
                        } else if ($crm_request->case_nature_id == 2) {
                            $information['summary']['requests']['service_requests']++;
                            $deliveries['ordering'] = 2;
                            $deliveries['request']['type'] = 2;
                        } else if ($crm_request->case_nature_id == 4) {
                            $information['summary']['requests']['claims']++;
                            $deliveries['ordering'] = 3;
                            $deliveries['request']['type'] = 4;
                        } else {
                            $deliveries['ordering'] = 4;
                        }

                        $deliveries['request']['added_date'] = Carbon::parse($crm_request->created_at)->format('Y-m-d H:i:s');
                        $deliveries['request']['description'] = $crm_request->description;
                        $deliveries['request']['comments'] = array();

                        $crm_request_comments = $crm_request->comments->where('comment_type', 2);
                        if (count($crm_request_comments) > 0) {
                            foreach ($crm_request_comments as $crm_request_comment) {
                                if ($crm_request_comment->comment_by == 0) {
                                    $comments = array();
                                    $comments['name'] = Admin::find($crm_request_comment->comment_by_id)->name;
                                    $comments['comment'] = $crm_request_comment->comment;
                                    $comments['type'] = 'Admin';
                                    $comments['commented_at'] = Carbon::parse($crm_request_comment->created_at)->format('Y-m-d H:i:s');
                                    $deliveries['request']['comments'][] = $comments;
                                } else if ($crm_request_comment->comment_by == 2) {
                                    $comments = array();
                                    $comments['name'] = Rider::find($crm_request_comment->comment_by_id)->name;
                                    $comments['comment'] = $crm_request_comment->comment;
                                    $comments['type'] = 'Rider';
                                    $comments['commented_at'] = Carbon::parse($crm_request_comment->created_at)->format('Y-m-d H:i:s');
                                    $deliveries['request']['comments'][] = $comments;
                                }
                            }
                        }

                    } else {
                        $deliveries['ordering'] = 4;
                    }

                    $information['deliveries'][] = $deliveries;
                }

                usort($information['deliveries'], function ($a, $b) {
                    return $a['ordering'] <=> $b['ordering'];
                });

                $nodes[] = $information;
            }
            return response()->json(['status' => 0, 'message' => 'Delivery Note Is Assigned', 'information' => $nodes]);
        }
        return response()->json(['status' => 0, 'message' => 'No Delivery Note Assigned']);
    }

    public function rider_signup(Request $request)
    {
        return response()->json(['status' => 1, 'message' => 'Please Update Your Bolt App']);
        if ($request->isMethod('post')) {
            $rules = [
                'name' => ['required'],
                'cnic' => ['required', 'regex:/^[0-9]{5}-[0-9]{7}-[0-9]{1}$/'],
                'phone_number' => ['required', 'regex:/^[0][0-9]{3}-[0-9]{7}$/'],
                'pin' => ['required', 'integer', 'digits:4'],
                'city_id' => ['required', 'integer']
            ];
            $response = ['status' => 1];
            $message = 'Unknown';

            $validate = Validator::make($request->all(), $rules, $this->messages);

            $validate->setAttributeNames($this->names);

            if ($validate->fails()) {
                $message = 'Error(s) in Input';
                $response['errors'] = $validate->errors();
            } else {
                $rider_request = RiderRequest::where('phone_no', $request->input('phone_number'))
                    ->orWhere('cnic', $request->input('cnic'));

                //Check RiderRequest Already Exist
                if ($rider_request->exists()) {
                    $rider_request = $rider_request->first();
                    if ($rider_request->phone_no == $request->input('phone_number') && $rider_request->cnic == $request->input('cnic')) {
                        $message = "Phone Number & CNIC Already Exists";

                    } else if ($rider_request->phone_no == $request->input('phone_number')) {
                        $message = "Phone Number Already Exist";

                    } else if ($rider_request->cnic == $request->input('cnic')) {
                        $message = "CNIC Already Exist";
                    }
                } //Check Rider Already Exist
                else {
                    $rider = Rider::where('phone', $request->input('phone_number'))->orWhere('cnic', $request->input('cnic'));

                    if ($rider->exists()) {
                        $rider = $rider->first();
                        if ($rider->phone == $request->phone_number && $rider->cnic == $request->input('cnic')) {
                            $message = "Phone Number & CNIC Already Exists";

                        } else if ($rider->phone == $request->phone_number) {
                            $message = "Phone Number Already Exist";

                        } else if ($rider->cnic == $request->input('cnic')) {
                            $message = "CNIC Already Exist";
                        }

                    } else {
                        try {
                            $rider_request = new RiderRequest();
                            $rider_request->name = $request->name;
                            $rider_request->cnic = $request->cnic;
                            $rider_request->phone_no = $request->phone_number;
                            $rider_request->pin = $request->pin;
                            $rider_request->city_id = $request->city_id;
                            $rider_request->save();

                            $employee_request = new Employee();
                            $employee_request->name = $request->name;
                            $employee_request->cnic = $request->cnic;
                            $employee_request->phone_number = $request->phone_number;
                            $employee_request->pin = $request->pin;
                            $employee_request->city_id = $request->city_id;
                            $employee_request->employee_type_id = 2;
                            $employee_request->rider_request_id = $rider_request->id;
                            $employee_request->status_id = 2;
                            $employee_request->save();
                            $response['status'] = 0;
                            $message = 'Rider Request Has Been Submitted and Pending for Approval';
                        } catch (Exception $ex) {
                            $response['message'] = $ex;
                        }
                    }
                }
            }
        } else {
            $message = 'Post Method is Required';
        }
        $response['message'] = $message;
        return response()->json($response);
    }

    public function pickups_history(Request $request)
    {
        return response()->json(['status' => 1, 'message' => 'Please Update Your Bolt App']);
        $rider_id = $request->rider_id;
        $from_date = $request->get('date');
        $pickup_request_id = $request->get('pickup_request_id');
        $pickup_note_id = $request->get('pickup_note_id');

        if ($from_date == null && $pickup_request_id == null && $pickup_note_id == null) {
            return response()->json(["status" => 1, "message" => "Please provide parameter(s)"]);
        } else {

            $rider_pickups = V2RiderPickup::leftjoin('v2_pickup_request_not_pick_reasons as pnpr', 'v2_rider_pickups.pickup_not_pick_reason_id', 'pnpr.id')
                ->join('v2_pickup_notes as pn', 'v2_rider_pickups.pickup_note_id', 'pn.id')
                ->join('v2_pickup_requests as pr', 'v2_rider_pickups.pickup_request_id', 'pr.id')
                ->join('users as u', 'pr.shipper_id', 'u.id')
                ->select('v2_rider_pickups.id', 'v2_rider_pickups.shipments', 'pnpr.name as reason', 'v2_rider_pickups.pickup_note_id', 'v2_rider_pickups.pickup_request_id', 'v2_rider_pickups.pickup_type', 'u.name as shipper')
                ->where('pn.rider_id', '=', $rider_id);

            if ($from_date != null) {
                $rider_pickups = $rider_pickups->whereDate('v2_rider_pickups.created_at', $from_date);
            }
            if ($pickup_request_id != null) {
                $rider_pickups = $rider_pickups->where('v2_rider_pickups.pickup_request_id', $pickup_request_id);
            }
            if ($pickup_note_id != null) {
                $rider_pickups = $rider_pickups->where('v2_rider_pickups.pickup_note_id', $pickup_note_id);
            }

            if ($rider_pickups->exists()) {
                $rider_pickups = $rider_pickups->get();
                return response()->json(["status" => 0, "pickups" => $rider_pickups]);
            } else {
                return response()->json(["status" => 1, "message" => "No pickups found!"]);
            }
        }
    }

    public function delivery_history(Request $request)
    {
        return response()->json(['status' => 1, 'message' => 'Please Update Your Bolt App']);
        $rider_id = $request->rider_id;
        $from_date = $request->get('date');
        $delivery_note_id = $request->get('delivery_note_id');
        $tracking_no = $request->get('tracking_no');

        if ($from_date == null && $delivery_note_id == null && $tracking_no == null) {
            return response()->json(["status" => 1, "message" => "Please provide parameter(s)"]);
        } else {
            $rider_deliveries = DeliveryNote::
            join('cities AS oc', 'delivery_notes.hub_id', '=', 'oc.id')
                ->join('riders', 'delivery_notes.rider_id', '=', 'riders.id')
                ->join('routes', 'delivery_notes.route_id', '=', 'routes.id')
                ->leftjoin('admins as ccb', 'delivery_notes.cash_collected_by', '=', 'ccb.id')
                ->join('admins', 'admins.id', '=', 'delivery_notes.admin_id')
                ->join('delivery_note_shipments', 'delivery_note_shipments.delivery_note_id', '=', 'delivery_notes.id')
                ->join('shipments', 'shipments.id', '=', 'delivery_note_shipments.shipment_id')
                ->leftjoin('admins as ub', 'ub.id', '=', 'delivery_notes.updated_by')
                ->leftjoin('rider_delivery_note_statuses as rdns', 'rdns.delivery_note_id', '=', 'delivery_notes.id')
                ->select(['delivery_notes.id as delivery_note', 'delivery_notes.delivered_shipments', 'delivery_notes.shipments_count'])
                ->where('delivery_notes.pending_status', '=', 1)
                ->where('riders.id', '=', $rider_id);

            if ($from_date != null) {
                $rider_deliveries = $rider_deliveries->whereDate('delivery_notes.created_at', $from_date)
                    ->groupBy('delivery_notes.id');
            }

            if ($delivery_note_id != null) {
                $rider_deliveries = $rider_deliveries->where('delivery_notes.id', $delivery_note_id)
                    ->groupBy('delivery_notes.id');
            }

            if ($tracking_no != null) {
                $rider_deliveries = $rider_deliveries->where('shipments.tracking_number', $tracking_no)
                    ->groupBy('delivery_notes.id');
            }


            if ($rider_deliveries->exists()) {
                $rider_deliveries = $rider_deliveries->get();
                return response()->json(["status" => 0, "deliveries" => $rider_deliveries]);
            } else {
                return response()->json(["status" => 1, "message" => "No deliveries found!"]);
            }
        }


    }

    public function cities(Request $request)
    {
        return response()->json(['status' => 1, 'message' => 'Please Update Your Bolt App']);
        $cities = City::where('status', 1)->where('business_category_id', 1);

        if ($cities->exists()) {
            $cities = $cities->get();

            $details = array();

            foreach ($cities as $city) {
                $detail = array();

                $detail['id'] = $city->id;
                $detail['name'] = $city->name;

                $details[] = $detail;
            }

            return response()->json(['status' => 0, 'message' => 'Pickup and Delivery Information of Cities', 'cities' => $details]);
        } else {
            return response()->json(['status' => 1, 'message' => ' No City Present']);
        }
    }

    public function return_summary(Request $request)
    {
        $rider_id = $request->rider_id;

        $return_note = ReturnNote::where('rider_id', $rider_id)->where('completion_status', 0);
        if ($return_note->exists()) {
            $return_note = $return_note->latest('id')->first();
            $information = array();

            $information['return_note_id'] = $return_note->id;
            $information['summary'] = array();
            $total_shipments = $return_note->shipments_count;
            $information['summary']['return_note_shipments'] = $total_shipments;
            $information['summary']['completed'] = array();
            $information['summary']['completed']['pending'] = 0;
            $information['summary']['completed']['undelivered'] = 0;
            $information['summary']['completed']['delivered'] = 0;
            $rider_return_deliveries = RiderReturnDelivery::where('return_note_id', $return_note->id);
            if ($rider_return_deliveries->exists()) {
                $updated_shipments = 0;
                $undelivered_shipments = 0;
                $delivered_shipments = 0;
                $updated_shipments_count = RiderReturnDelivery::where('return_note_id', $return_note->id)->count(DB::raw('DISTINCT return_note_shipment_id'));
                if ($updated_shipments_count > 0) {
                    $updated_shipments = $updated_shipments_count;
                }

                $undelivered_shipments_count = RiderReturnDelivery::where('return_note_id', $return_note->id)->where('delivered_status', 0)->count(DB::raw('DISTINCT return_note_shipment_id'));
                if ($undelivered_shipments_count > 0) {
                    $undelivered_shipments = $undelivered_shipments_count;
                }

                $delivered_shipments_count = RiderReturnDelivery::where('return_note_id', $return_note->id)->where('delivered_status', 1)->count(DB::raw('DISTINCT return_note_shipment_id'));
                if ($delivered_shipments_count > 0) {
                    $delivered_shipments = $delivered_shipments_count;
                }
                $information['summary']['completed']['pending'] = $total_shipments - $updated_shipments;
                $information['summary']['completed']['undelivered'] = $undelivered_shipments;
                $information['summary']['completed']['delivered'] = $delivered_shipments;
            } else {
                $information['summary']['completed']['pending'] = $total_shipments;
            }

            $information['summary']['requests'] = array();
            $information['summary']['requests']['complains'] = 0;
            $information['summary']['requests']['service_requests'] = 0;
            $information['summary']['requests']['claims'] = 0;

            $information['return_deliveries'] = array();
            /*$delivery_note_shipments = $return_note->delivery_note_shipments->sortBy('ordering');*/
            $return_note_shipments = $return_note->return_note_shipments;
            foreach ($return_note_shipments as $return_note_shipment) {
                $shipment_data = $return_note_shipment->shipment;
                $shipper_data = $shipment_data->user;


                $shipment_id = $shipment_data->id;
                $tracking_number = $shipment_data->tracking_number;
                $shipper_name = $shipper_data->name;
                $shipper_poc = $shipper_data->poc;
                $shipper_address = $shipper_data->address;
                $shipper_phone = $shipper_data->phone;
                if ($shipper_data->phone2 != null) {
                    $shipper_phone .= ' / ' . $shipper_data->phone2;
                }
                $special_instructions = $shipment_data->special_instructions;
                $remarks = '';
                $journey = ShipmentsJourney::where('shipment_id', $shipment_data->id)->where('remarks', '!=', null)->select('remarks');
                if ($journey->exists()) {
                    $journey = $journey->orderBy('id', 'DESC')->first();
                    $remarks = $journey->remarks;
                }
                $rider_return_deliveries = RiderReturnDelivery::where('return_note_id', $return_note->id)->where('return_note_shipment_id', $shipment_id);
                if ($rider_return_deliveries->exists()) {
                    $rider_return_deliveries = $rider_return_deliveries->first();
                    if ($rider_return_deliveries->delivered_status == 0) {
                        $status = 3;
                    } else if ($rider_return_deliveries->delivered_status == 1) {
                        $status = 2;
                    }
                } else {
                    $status = 1;
                }


                $deliveries = array();
                $deliveries['shipment_id'] = $shipment_id;
                $deliveries['tracking_number'] = $tracking_number;
                $deliveries['shipper_name'] = $shipper_name;
                $deliveries['shipper_poc'] = $shipper_poc;
                $deliveries['shipper_address'] = $shipper_address;
                $deliveries['shipper_phone'] = $shipper_phone;
                $deliveries['special_instructions'] = $special_instructions;
                $deliveries['remarks'] = $remarks;
                $deliveries['latitude'] = NULL;
                $deliveries['longitude'] = NULL;
                $deliveries['status'] = $status;
                $shipment_location = ConsigneeShipmentLocation::where('shipment_id', $shipment_id);
                if ($shipment_location->exists()) {
                    $shipment_location = $shipment_location->first();
                    $previous_location_id = $shipment_location->previous_location_id;
                    $location = ConsigneeLocation::find($previous_location_id);
                    $deliveries['latitude'] = $location->lat;
                    $deliveries['longitude'] = $location->long;
                }


                if (CrmRequest::where('shipment_id', $shipment_data->id)->whereIn('case_nature_id', [1, 2, 4])->whereNotIn('status_id', [3, 4])->exists()) {
                    $deliveries['request'] = array();
                    $crm_request = CrmRequest::where('shipment_id', $shipment_data->id)->where('case_nature_id', '!=', 3)->latest()->first();
                    $deliveries['request']['id'] = $crm_request->id;

                    if ($crm_request->case_nature_id == 1) {
                        $information['summary']['requests']['complains']++;
                        $deliveries['ordering'] = 1;
                        $deliveries['request']['type'] = 1;
                    }
                    if ($crm_request->case_nature_id == 2) {
                        $information['summary']['requests']['service_requests']++;
                        $deliveries['ordering'] = 2;
                        $deliveries['request']['type'] = 2;
                    }
                    if ($crm_request->case_nature_id == 4) {
                        $information['summary']['requests']['claims']++;
                        $deliveries['ordering'] = 3;
                        $deliveries['request']['type'] = 4;
                    }

                    $deliveries['request']['added_date'] = Carbon::parse($crm_request->created_at)->format('Y-m-d H:i:s');
                    $deliveries['request']['description'] = $crm_request->description;
                    $deliveries['request']['comments'] = array();

                    $crm_request_comments = $crm_request->comments->where('comment_type', 2);
                    if (count($crm_request_comments) > 0) {
                        foreach ($crm_request_comments as $crm_request_comment) {
                            if ($crm_request_comment->comment_by == 0) {
                                $comments = array();
                                $comments['name'] = Admin::find($crm_request_comment->comment_by_id)->name;
                                $comments['comment'] = $crm_request_comment->comment;
                                $comments['type'] = 'Admin';
                                $comments['commented_at'] = Carbon::parse($crm_request_comment->created_at)->format('Y-m-d H:i:s');
                                $deliveries['request']['comments'][] = $comments;
                            } else if ($crm_request_comment->comment_by == 2) {
                                $comments = array();
                                $comments['name'] = Rider::find($crm_request_comment->comment_by_id)->name;
                                $comments['comment'] = $crm_request_comment->comment;
                                $comments['type'] = 'Rider';
                                $comments['commented_at'] = Carbon::parse($crm_request_comment->created_at)->format('Y-m-d H:i:s');
                                $deliveries['request']['comments'][] = $comments;
                            }
                        }
                    }

                } else {
                    $deliveries['ordering'] = 4;
                }

                $information['return_deliveries'][] = $deliveries;
            }
            usort($information['return_deliveries'], function ($a, $b) {
                return $a['ordering'] <=> $b['ordering'];
            });
            return response()->json(['status' => 0, 'message' => 'Return Note Is Assigned', 'information' => $information]);
        }
        return response()->json(['status' => 1, 'message' => 'No Return Note Assigned']);
    }

    public function return_summary_multiple(Request $request)
    {
        return response()->json(['status' => 0, 'message' => 'Please Update Your Bolt App']);
        $rider_id = $request->rider_id;

        $return_notes = ReturnNote::where('rider_id', $rider_id)->where('status', 0)->where('shipments_count', '!=', 0);

        if ($return_notes->exists()) {
            $return_notes = $return_notes->get();

            $nodes = array();

            foreach ($return_notes as $return_note) {
                $information = array();

                $information['return_note_id'] = $return_note->id;
                $information['assigned_date'] = $return_note->created_at->toDateTimeString();
                $information['no_of_parcels'] = $return_note->shipments_count;
                $information['summary'] = array();
                $total_shipments = $return_note->shipments_count;
                $information['summary']['deliveries'] = $total_shipments;
                $information['summary']['completed'] = array();
                $information['summary']['completed']['pending'] = 0;
                $information['summary']['completed']['undelivered'] = 0;
                $information['summary']['completed']['delivered'] = 0;
                $rider_return_deliveries = RiderReturnDelivery::where('return_note_id', $return_note->id);

                $updated_shipments = 0;

                if ($rider_return_deliveries->exists()) {
                    $undelivered_shipments = 0;
                    $delivered_shipments = 0;
                    $updated_shipments_count = RiderReturnDelivery::where('return_note_id', $return_note->id)->count(DB::raw('DISTINCT shipment_id'));
                    if ($updated_shipments_count > 0) {
                        $updated_shipments = $updated_shipments_count;
                    }

                    $undelivered_shipments_count = RiderReturnDelivery::where('return_note_id', $return_note->id)->where('delivered_status', 0)->count(DB::raw('DISTINCT shipment_id'));
                    if ($undelivered_shipments_count > 0) {
                        $undelivered_shipments = $undelivered_shipments_count;
                    }

                    $delivered_shipments_count = RiderReturnDelivery::where('return_note_id', $return_note->id)->where('delivered_status', 1)->count(DB::raw('DISTINCT shipment_id'));
                    if ($delivered_shipments_count > 0) {
                        $delivered_shipments = $delivered_shipments_count;
                    }
                    $information['summary']['completed']['pending'] = $total_shipments - $updated_shipments;
                    $information['summary']['completed']['undelivered'] = $undelivered_shipments;
                    $information['summary']['completed']['delivered'] = $delivered_shipments;
                } else {
                    $information['summary']['completed']['pending'] = $total_shipments;
                }

                $information['summary']['requests'] = array();
                $information['summary']['requests']['complains'] = 0;
                $information['summary']['requests']['service_requests'] = 0;
                $information['summary']['requests']['claims'] = 0;

                $information['return_deliveries'] = array();
                $return_note_shipments = $return_note->return_note_shipments;
                foreach ($return_note_shipments as $return_note_shipment) {

                    $shipment_data = $return_note_shipment->shipment;
                    $pickup_address = $shipment_data->pickup_address;

                    $shipment_id = $shipment_data->id;
                    $tracking_number = $shipment_data->tracking_number;
                    $shipper_name = $pickup_address->user->name;
                    $shipper_poc = $pickup_address->poc;
                    $shipper_address = $pickup_address->pickup_address;
                    $shipper_phone = $pickup_address->phone;
                    $special_instructions = $shipment_data->special_instructions;
                    $remarks = '';
                    $journey = ShipmentsJourney::where('shipment_id', $shipment_data->id)->where('remarks', '!=', null)->select('remarks');
                    if ($journey->exists()) {
                        $journey = $journey->orderBy('id', 'DESC')->first();
                        $remarks = $journey->remarks;
                    }
                    $rider_return_deliveries = RiderReturnDelivery::where('return_note_id', $return_note->id)->where('shipment_id', $shipment_id);
                    if ($rider_return_deliveries->exists()) {
                        $rider_return_deliveries = $rider_return_deliveries->first();
                        if ($rider_return_deliveries->delivered_status == 0) {
                            $status = 3;
                        } else if ($rider_return_deliveries->delivered_status == 1) {
                            $status = 2;
                        }
                    } else {
                        $status = 1;
                    }

                    $deliveries = array();
                    $deliveries['shipment_id'] = $shipment_id;
                    $deliveries['tracking_number'] = $tracking_number;
                    $deliveries['consignee_name'] = $shipper_name;
                    $deliveries['consignee_poc'] = $shipper_poc;
                    $deliveries['consignee_address'] = $shipper_address;
                    $deliveries['consignee_phone'] = $shipper_phone;
                    $deliveries['special_instructions'] = $special_instructions;
                    $deliveries['remarks'] = $remarks;
                    $deliveries['latitude'] = NULL;
                    $deliveries['longitude'] = NULL;
                    $deliveries['status'] = $status;
                    $shipper_lat = $pickup_address->location_latitude;
                    $shipper_long = $pickup_address->location_longitude;
                    if ($shipper_lat != null && $shipper_long != null) {
                        $deliveries['latitude'] = $shipper_lat;
                        $deliveries['longitude'] = $shipper_long;
                    }
                    if (CrmRequest::where('shipment_id', $shipment_data->id)->whereIn('case_nature_id', [1, 2, 4])->whereNotIn('status_id', [3, 4])->exists()) {
                        $deliveries['request'] = array();
                        $crm_request = CrmRequest::where('shipment_id', $shipment_data->id)->where('case_nature_id', '!=', 3)->latest()->first();
                        $deliveries['request']['id'] = $crm_request->id;

                        if ($crm_request->case_nature_id == 1) {
                            $information['summary']['requests']['complains']++;
                            $deliveries['ordering'] = 1;
                            $deliveries['request']['type'] = 1;
                        } else if ($crm_request->case_nature_id == 2) {
                            $information['summary']['requests']['service_requests']++;
                            $deliveries['ordering'] = 2;
                            $deliveries['request']['type'] = 2;
                        } else if ($crm_request->case_nature_id == 4) {
                            $information['summary']['requests']['claims']++;
                            $deliveries['ordering'] = 3;
                            $deliveries['request']['type'] = 4;
                        } else {
                            $deliveries['ordering'] = 4;
                        }

                        $deliveries['request']['added_date'] = Carbon::parse($crm_request->created_at)->format('Y-m-d H:i:s');
                        $deliveries['request']['description'] = $crm_request->description;
                        $deliveries['request']['comments'] = array();

                        $crm_request_comments = $crm_request->comments->where('comment_type', 2);
                        if (count($crm_request_comments) > 0) {
                            foreach ($crm_request_comments as $crm_request_comment) {
                                if ($crm_request_comment->comment_by == 0) {
                                    $comments = array();
                                    $comments['name'] = Admin::find($crm_request_comment->comment_by_id)->name;
                                    $comments['comment'] = $crm_request_comment->comment;
                                    $comments['type'] = 'Admin';
                                    $comments['commented_at'] = Carbon::parse($crm_request_comment->created_at)->format('Y-m-d H:i:s');
                                    $deliveries['request']['comments'][] = $comments;
                                } else if ($crm_request_comment->comment_by == 2) {
                                    $comments = array();
                                    $comments['name'] = Rider::find($crm_request_comment->comment_by_id)->name;
                                    $comments['comment'] = $crm_request_comment->comment;
                                    $comments['type'] = 'Rider';
                                    $comments['commented_at'] = Carbon::parse($crm_request_comment->created_at)->format('Y-m-d H:i:s');
                                    $deliveries['request']['comments'][] = $comments;
                                }
                            }
                        }

                    } else {
                        $deliveries['ordering'] = 4;
                    }

                    $information['return_deliveries'][] = $deliveries;
                }

                usort($information['return_deliveries'], function ($a, $b) {
                    return $a['ordering'] <=> $b['ordering'];
                });

                $nodes[] = $information;
            }
            return response()->json(['status' => 0, 'message' => 'Return Delivery Note Is Assigned', 'information' => $nodes]);
        }
        return response()->json(['status' => 0, 'message' => 'No Return Delivery Note Assigned']);
    }

    public function return_shipment_delivered(Request $request)
    {
        $rules = [
            'added_at' => ['required'],
            'return_note_id' => ['required', 'integer', 'digits_between:1,10', 'exists:return_notes,id'],
            'start_location_latitude' => ['required', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'start_location_longitude' => ['required', 'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'],
            'actual_location_latitude' => ['required', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'actual_location_longitude' => ['required', 'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'],
            'shipment_id' => ['required', 'integer', 'digits_between:1,10', 'exists:shipments,id'],
            'receiver_name' => ['required', 'string', 'max:255'],
            'cnic' => ['nullable', 'max:255'],
            'picture' => ['nullable', 'image']
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);
        $message = '';

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {

            $rider_id = $request->rider_id;

            $added_at = Carbon::createFromTimestampMs($request->added_at)->toDateTimeString();
            if (!Shipment::where('id', $request->shipment_id)->where('shipper_status_id', 25)->exists()) {
                if (!RiderReturnDelivery::where('return_note_id', $request->return_note_id)->where('shipment_id', $request->shipment_id)->where('delivered_status', 1)->exists()) {
                    if (ReturnNoteShipment::join('return_notes as rn', 'return_note_shipments.return_note_id', 'rn.id')->where('return_note_id', $request->return_note_id)->where('shipment_id', $request->shipment_id)->where('rn.rider_id', $rider_id)->exists()) {
                        {

                            $destination = $request->actual_location_latitude . ',' . $request->actual_location_longitude;

                            $rider_return_delivery = new RiderReturnDelivery();

                            $rider_return_delivery->added_at = $added_at;
                            $rider_return_delivery->return_note_id = $request->return_note_id;
                            $rider_return_delivery->shipment_id = $request->shipment_id;
                            $rider_return_delivery->rider_id = $request->rider_id;
                            $rider_return_delivery->start_location_latitude = $request->start_location_latitude;
                            $rider_return_delivery->start_location_longitude = $request->start_location_longitude;
                            $rider_return_delivery->actual_location_latitude = $request->actual_location_latitude;
                            $rider_return_delivery->actual_location_longitude = $request->actual_location_longitude;
                            $rider_return_delivery->rider_status_id = 25;
                            $rider_return_delivery->delivered_status = 1;
                            $received_by = NULL;
                            if ($request->has('receiver_name')) {
                                $received_by = $request->receiver_name;
                            }
                            if ($request->has('cnic')) {
                                $rider_return_delivery->cnic = $request->cnic;
                                $received_by .= ' | ' . $request->cnic;
                            }

                            $shipment = Shipment::find($request->shipment_id);
                            $shipper_data = $shipment->user;
                            $pickup_address = $shipment->pickup_address;

                            $shipper_phone_number_1 = $pickup_address->phone;
                            $shipper_address = $pickup_address->pickup_address;

                            $shipper_lat = $pickup_address->location_latitude;
                            $shipper_long = $pickup_address->location_longitude;

                            if ($request->actual_location_latitude > 0 && $request->actual_location_longitude > 0) {
                                $origin = $request->start_location_latitude . ',' . $request->start_location_longitude;

                                $rider_return_delivery->distance_from_start_to_actual = $this->distance($origin, $destination);

                                if ($shipper_lat != null && $shipper_long != null) {
                                    $rider_return_delivery->current_location_latitude = $shipper_lat;
                                    $rider_return_delivery->current_location_longitude = $shipper_long;

                                    $origin = $shipper_lat . ',' . $shipper_long;

                                    $distance = $this->distance($origin, $destination);

                                    $rider_return_delivery->distance_from_current_to_actual = $distance;
                                }
                            } else {
                                $rider_return_delivery->distance_from_start_to_actual = 0;

                                if ($shipper_lat != null && $shipper_long != null) {
                                    $rider_return_delivery->current_location_latitude = $shipper_lat;
                                    $rider_return_delivery->current_location_longitude = $shipper_long;
                                    $rider_return_delivery->distance_from_current_to_actual = 0;
                                }
                            }
                            $rider_return_delivery->save();


                            if ($request->has('picture')) {
                                $picture_path = 'rider_return_delivery/picture_' . $rider_return_delivery->id . '.png';
                                Storage::disk('public')->put($picture_path, file_get_contents($request->picture));
                                $rider_return_delivery->picture_path = $picture_path;
                                $rider_return_delivery->save();
                            }

                            if (ReturnNote::where('id', $request->return_note_id)->exists()) {
                                $shipment->shipper_status_id = 25;
                                $shipment->consignee_status_id = 25;
                                $shipment->save();
                                ReturnNoteShipment::where('return_note_id', $request->return_note_id)->where('shipment_id', $shipment->id)->update(['status' => 2, 'update_type' => 1]);
                                ShipmentsJourneyController::add($shipment->id, 25, 25, NULL, NULL, NULL, NULL, $request->return_note_id, NULL, 1, $received_by, $rider_id);
                            }
                            $rider_return_note_status = RiderReturnNoteStatus::where('return_note_id', $request->return_note_id);
                            if (!$rider_return_note_status->exists()) {
                                $new_status = new RiderReturnNoteStatus();
                                $new_status->return_note_id = $request->return_note_id;
                                $new_status->status = 1;
                                $new_status->save();
                            }
                            $return_note_data = ReturnNote::find($request->return_note_id);

                            if ($return_note_data->completion_status == 0) {
                                $return_note_data->completion_status = 1;
                                $return_note_data->save();
                            }
                            $updated_shipments_count = ReturnNoteShipment::where('return_note_id', $request->return_note_id)->where('status', 0)->count();

                            if ($updated_shipments_count == 0) {
                                $return_note_data->status = 3;
                                $return_note_data->updated_at = Carbon::now();
                                $return_note_data->save();
                            }
                            $rider_return_note_status = RiderReturnNoteStatus::where('return_note_id', $request->return_note_id);
                            if ($rider_return_note_status->exists()) {
                                $rider_return_note_status = $rider_return_note_status->first();
                                $rider_return_note_status->status = 2;
                                $rider_return_note_status->save();
                            }
                            $message = 'Shipment is marked as delivered Successfully';
                        }
                    }
                } else {
                    $message = 'Shipment is marked as delivered already';
                }
            } else {
                $message = 'Shipment is marked as delivered already';
            }
        }
        return response()->json(['status' => 0, 'message' => $message, 'return_note_id' => $request->return_note_id, 'shipment_id' => $request->shipment_id]);
    }

    public function return_shipment_undelivered(Request $request)
    {
        $rules = [
            'added_at' => ['required'],
            'return_note_id' => ['required', 'integer', 'digits_between:1,10', 'exists:return_notes,id'],
            'start_location_latitude' => ['required', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'start_location_longitude' => ['required', 'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'],
            'actual_location_latitude' => ['required', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'actual_location_longitude' => ['required', 'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'],
            'shipment_id' => ['required', 'integer', 'digits_between:1,10', 'exists:shipments,id'],
            'shipper_status_id' => ['required', 'integer', 'digits_between:1,10', 'exists:shipment_status,id'],
            'status_reason_id' => ['nullable'],
            'remarks' => ['nullable', 'string', 'max:255'],
            'picture' => ['required', 'image']
        ];
        $message = '';

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {

            $rider_id = $request->rider_id;

            $added_at = Carbon::createFromTimestampMs($request->added_at)->toDateTimeString();
            if (!RiderReturnDelivery::where('return_note_id', $request->return_note_id)->where('shipment_id', $request->shipment_id)->where('delivered_status', 1)->exists()) {
                if (ReturnNoteShipment::join('return_notes as rn', 'return_note_shipments.return_note_id', 'rn.id')->where('return_note_id', $request->return_note_id)->where('shipment_id', $request->shipment_id)->where('rn.rider_id', $rider_id)->exists()) {
                    $shipment = Shipment::find($request->shipment_id);
                    $shipper_data = $shipment->user;
                    $pickup_address = $shipment->pickup_address;

                    $destination = $request->actual_location_latitude . ',' . $request->actual_location_longitude;

                    $rider_return_delivery = new RiderReturnDelivery();

                    $rider_return_delivery->added_at = $added_at;
                    $rider_return_delivery->return_note_id = $request->return_note_id;
                    $rider_return_delivery->shipment_id = $shipment->id;
                    $rider_return_delivery->rider_id = $request->rider_id;
                    $rider_return_delivery->start_location_latitude = $request->start_location_latitude;
                    $rider_return_delivery->start_location_longitude = $request->start_location_longitude;
                    $rider_return_delivery->actual_location_latitude = $request->actual_location_latitude;
                    $rider_return_delivery->actual_location_longitude = $request->actual_location_longitude;
                    $rider_return_delivery->rider_status_id = $request->shipper_status_id;
                    $rider_return_delivery->rider_status_reason_id = ($request->status_reason_id != -1) ? $request->status_reason_id : null;
                    $rider_return_delivery->delivered_status = 0;
                    $shipper_phone_number_1 = $pickup_address->phone;
                    $shipper_address = $pickup_address->pickup_address;
                    $shipper_lat = $pickup_address->location_latitude;
                    $shipper_long = $pickup_address->location_longitude;

                    if ($request->actual_location_latitude > 0 && $request->actual_location_longitude > 0) {
                        $origin = $request->start_location_latitude . ',' . $request->start_location_longitude;

                        $rider_return_delivery->distance_from_start_to_actual = $this->distance($origin, $destination);

                        if ($shipper_lat != null && $shipper_long != null) {
                            $rider_return_delivery->current_location_latitude = $shipper_lat;
                            $rider_return_delivery->current_location_longitude = $shipper_long;

                            $origin = $shipper_lat . ',' . $shipper_long;

                            $distance = $this->distance($origin, $destination);

                            $rider_return_delivery->distance_from_current_to_actual = $distance;
                        }
                    } else {
                        $rider_return_delivery->distance_from_start_to_actual = 0;

                        if ($shipper_lat != null && $shipper_long != null) {
                            $rider_return_delivery->current_location_latitude = $shipper_lat;
                            $rider_return_delivery->current_location_longitude = $shipper_long;
                            $rider_return_delivery->distance_from_current_to_actual = 0;
                        }
                    }
                    $rider_return_delivery->save();

                    $picture_path = 'rider_return_delivery/' . $rider_return_delivery->id . '.png';
                    Storage::disk('public')->put($picture_path, file_get_contents($request->picture));
                    $rider_return_delivery->picture_path = $picture_path;
                    $rider_return_delivery->save();

                    if (ReturnNote::where('id', $request->return_note_id)->exists()) {

                        $shipment->shipper_status_id = $request->shipper_status_id;
                        $shipment->consignee_status_id = $request->shipper_status_id;
                        $shipment->save();

                        $remarks = NULL;

                        if ($request->has('remarks')) {
                            $remarks = $request->remarks;
                        }

                        ShipmentsJourneyController::add($shipment->id, $request->shipper_status_id, $request->shipper_status_id, ($request->status_reason_id != -1) ? $request->status_reason_id : null, $remarks, NULL, NULL, $request->return_note_id, NULL, 1, NULL, $rider_id);
                        ReturnNoteShipment::where('return_note_id', $request->return_note_id)->where('shipment_id', $shipment->id)->update(['status' => 1, 'update_type' => 1]);
                        $rider_return_note_status = RiderReturnNoteStatus::where('return_note_id', $request->return_note_id);
                        if (!$rider_return_note_status->exists()) {
                            $new_status = new RiderReturnNoteStatus();
                            $new_status->return_note_id = $request->return_note_id;
                            $new_status->status = 2;
                            $new_status->save();
                        } else {
                            $rider_return_note_status = $rider_return_note_status->first();
                            $rider_return_note_status->status = 2;
                            $rider_return_note_status->save();
                        }
                    }

                    $return_note_data = ReturnNote::find($request->return_note_id);

                    if ($return_note_data->completion_status == 0) {
                        $return_note_data->completion_status = 1;
                        $return_note_data->save();
                    }

                    $updated_shipments_count = ReturnNoteShipment::where('return_note_id', $request->return_note_id)->where('status', 0)->count();

                    if ($updated_shipments_count == 0) {
                        $return_note_data->status = 3;
                        $return_note_data->updated_at = Carbon::now();
                        $return_note_data->save();
                    }
                    $message = 'Shipment is marked as Undelivered Successfully';
                }
            } else {
                $message = 'Shipment is already marked as Undelivered';
            }
            return response()->json(['status' => 0, 'message' => $message, 'return_note_id' => $request->return_note_id, 'shipment_id' => $request->shipment_id]);

        }
    }

    public function return_action_log(Request $request)
    {
        $rules = [
            'actions' => ['required', 'array', 'min:1'],
            'actions.*.logged_at' => ['required'],
            'actions.*.type_id' => ['required', 'integer', 'digits_between:1,10', 'exists:delivery_actions,id'],
            'actions.*.return_note_id' => ['required', 'integer', 'digits_between:1,10', 'exists:return_notes,id'],
            'actions.*.shipment_id' => ['required', 'integer', 'digits_between:1,10', 'exists:shipments,id'],
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $rider_id = $request->rider_id;

            foreach ($request->actions as $action) {
                $rider_return_delivery_action_log = new RiderReturnDeliveryActionLog();

                $rider_return_delivery_action_log->logged_at = Carbon::createFromTimestampMs($action['logged_at'])->toDateTimeString();
                $rider_return_delivery_action_log->type_id = $action['type_id'];
                $rider_return_delivery_action_log->return_note_id = $action['return_note_id'];
                $rider_return_delivery_action_log->shipment_id = $action['shipment_id'];

                $rider_return_delivery_action_log->save();
            }

            return response()->json(['status' => 0, 'message' => 'Return Delivery Action Log(s) Successfully']);
        }
    }

    public function return_history(Request $request)
    {
        return response()->json(['status' => 1, 'message' => 'Please Update Your Bolt App']);
        $rider_id = $request->rider_id;
        $from_date = $request->get('date');
        $return_note_id = $request->get('return_note_id');
        $tracking_no = $request->get('tracking_no');

        if ($from_date == null && $return_note_id == null && $tracking_no == null) {
            return response()->json(["status" => 1, "message" => "Please Provide parameter(s)"]);
        } else {
            $rider_return_deliveries = ReturnNote::join('return_note_shipments as rns', 'return_notes.id', '=', 'rns.return_note_id')
                ->join('shipments as s', 'rns.shipment_id', '=', 's.id')
                ->where('return_notes.rider_id', $rider_id)
                ->whereIn('return_notes.status', [1, 3]);

            if ($from_date != null) {
                $rider_return_deliveries = $rider_return_deliveries->whereDate('return_notes.created_at', $from_date)
                    ->groupBy('return_notes.id');
            }

            if ($return_note_id != null) {
                $rider_return_deliveries = $rider_return_deliveries->where('return_notes.id', $return_note_id)
                    ->groupBy('return_notes.id');
            }

            if ($tracking_no != null) {
                $rider_return_deliveries = $rider_return_deliveries->where('s.tracking_number', $tracking_no)
                    ->groupBy('return_notes.id');
            }
            $rider_return_history = array();
            if ($rider_return_deliveries->exists()) {
                $rider_return_deliveries = $rider_return_deliveries->get();
                foreach ($rider_return_deliveries as $rider_return_delivery) {
                    $return_history = array();
                    $return_history['return_note_id'] = $rider_return_delivery->return_note_id;
                    $return_history['total_shipments'] = $rider_return_delivery->shipments_count;
                    $return_history['delivered_shipments'] = ReturnNoteShipment::where('return_note_id', $rider_return_delivery->return_note_id)->where('status', 2)->count();
                    $rider_return_history[] = $return_history;
                }
                return response()->json(["status" => 0, "return_history" => $rider_return_history]);
            } else {
                return response()->json(["status" => 1, "message" => "No return deliveries found!"]);
            }
        }


    }

    public function scan_shipment_detail(Request $request)
    {
        $rider_id = $request->rider_id;
        $tracking_no = $request->tracking_no;
        $pickup_requests = V2PickupRequest::join('v2_pickup_request_shipments as prs', 'v2_pickup_requests.id', '=', 'prs.pickup_request_id')
            ->join('shipments as s', 'prs.shipment_id', '=', 's.id')
            ->where('s.tracking_number', $tracking_no)
            ->where('v2_pickup_requests.status_id', 1)
            ->where('prs.status', 0);

        if ($pickup_requests->exists()) {
            $pickup_requests = $pickup_requests->first();
            $shipment_status = $pickup_requests->shipper_status_id;
            if ($pickup_requests->current_rider_id == $rider_id) {
                return response()->json(['status' => 1, 'message' => 'Pickup Already Assigned to You']);
            } else if ($pickup_requests->current_rider_id != null) {
                return response()->json(['status' => 1, 'message' => 'Pickup Already Assigned']);
            } elseif ($shipment_status != 1 && $shipment_status != 17) {
                return response()->json(['status' => 1, 'message' => 'Pickup Already Modified']);
            } else {
                $pickup = array();
                $pickup_address = $pickup_requests->pickup_address;
                $pickup['pickup_request_id'] = $pickup_requests->pickup_request_id;
                $pickup['shipments'] = $pickup_requests->booked;
                $pickup['shipper_name'] = $pickup_address->user->name;
                $pickup['person_of_contact'] = $pickup_address->poc;
                $pickup['phone_number'] = $pickup_address->phone;
                $pickup['address'] = $pickup_address->pickup_address;
                $pickup['location_latitude'] = $pickup_address->location_latitude;
                $pickup['location_longitude'] = $pickup_address->location_longitude;
                return response()->json(['status' => 0, 'shipment_detail' => $pickup]);
            }
        } else {
            return response()->json(['status' => 1, 'message' => 'Pickup Request Not Found']);
        }
    }

    public function scan_shipment_assign(Request $request)
    {

        $rider_id = $request->rider_id;
        $tracking_no = $request->tracking_no;
        $global_admin_id = 346;

        $pickup_requests = V2PickupRequest::join('v2_pickup_request_shipments as prs', 'v2_pickup_requests.id', '=', 'prs.pickup_request_id')
            ->join('shipments as s', 'prs.shipment_id', '=', 's.id')
            ->where('s.tracking_number', $tracking_no)
            ->where('v2_pickup_requests.status_id', 1)
            ->where('prs.status', 0)
            ->whereIn('s.shipper_status_id', [1, 17]);

        if ($pickup_requests->exists()) {
            $pickup_requests = $pickup_requests->first();

            if ($pickup_requests->current_rider_id == $rider_id) {
                return response()->json(['status' => 1, 'message' => 'Pickup Already Assigned to You']);
            } else if ($pickup_requests->current_rider_id != null) {
                return response()->json(['status' => 1, 'message' => 'Pickup Already Assigned']);
            } else {
                $pickup_request_id = $pickup_requests->pickup_request_id;
                $riders = array();
                $riders['new'] = $rider_id;
                $rider_cut_off_time = NULL;
                $rider_settings = GlobalSettings::where('type', 'rider_assignment_cut_off_time');
                if ($rider_settings->exists()) {
                    $rider_settings = $rider_settings->first();
                    if ($rider_settings->setting_value != 0 && $rider_settings->setting_value != null) {
                        $rider_cut_off_time = Carbon::createFromTime($rider_settings->setting_value, '0', '0', 'Asia/Karachi');
                    }

                }
                if ($rider_cut_off_time != null) {
                    if (Carbon::now() > $rider_cut_off_time) {
                        return response()->json(['status' => 1, 'message' => 'Rider can not be assigned after cut off time!']);
                    }
                }
                $pickups = 0;
                $settings = GlobalSettings::where('type', 'pickup_arrival_cut_off_time');
                $arrival_cut_off_time = '8';
                if ($settings->exists()) {
                    $settings = $settings->first();
                    $arrival_cut_off_time = $settings->setting_value;
                }
                $today = Carbon::today();
                $today->hour($arrival_cut_off_time)->minute(0)->second(0);

                $allowed_pickup_requests = array();
                $existing_pickup_request_attempt = V2PickupRequestAttempt::where('pickup_request_id', $pickup_request_id)->where('attempt_date', '>', $today);

                if (!$existing_pickup_request_attempt->exists()) {
                    $pickup_request = V2PickupRequest::find($pickup_request_id);

                    $pickup_request->rider_status = 2;
                    $pickup_request->attempts = $pickup_request->attempts + 1;
                    $pickup_request->current_rider_id = $rider_id;
                    $pickup_request->last_updated_by = $global_admin_id;
                    $pickup_request->save();

                    $pickup_request_attempt = new V2PickupRequestAttempt();
                    $pickup_request_attempt->pickup_request_id = $pickup_request_id;
                    $pickup_request_attempt->rider_id = $rider_id;
                    $pickup_request_attempt->attempt_date = Carbon::now();
                    $pickup_request_attempt->assigned_by = $global_admin_id;
                    $pickup_request_attempt->save();

                    if (!in_array($pickup_request_id, $allowed_pickup_requests)) {
                        $allowed_pickup_requests[] = $pickup_request_id;
                    }

                    $pickups++;
                    self::retail_pickup_assign($pickup_request_id, $rider_id);
                } else {
                    $pickup_request = V2PickupRequest::find($pickup_request_id);
                    if ($pickup_request->current_rider_id == $rider_id) {
                        pass;
                    } else {
                        $riders['old_rider_id'] = $pickup_request->current_rider_id;
                        $riders['new_rider_id'] = $rider_id;

                        $pickup_request->current_rider_id = $rider_id;
                        $pickup_request->last_updated_by = $global_admin_id;
                        $pickup_request->save();
                        $existing_pickup_request_attempt = $existing_pickup_request_attempt->latest('id')->first();

                        $existing_pickup_rider = $existing_pickup_request_attempt->rider_id;

                        $existing_pickup_request_attempt->rider_id = $rider_id;
                        $existing_pickup_request_attempt->assigned_by = $global_admin_id;
                        $existing_pickup_request_attempt->save();

                        $pickup_note_request = $pickup_request->pickup_note_request;
                        if ($pickup_note_request) {
                            $pickup_note = $pickup_note_request->pickup_note;
                            $pickup_note_rider = $pickup_note->rider_id;
                            if ($existing_pickup_rider == $pickup_note_rider) {
                                $pickup_request->pickup_note_request->delete();
                                $pickup_note->pickups = $pickup_note->pickups - 1;
                                $pickup_note->save();
                            }
                        }
                        $pickups++;
                        if (!in_array($pickup_request_id, $allowed_pickup_requests)) {
                            $allowed_pickup_requests[] = $pickup_request_id;
                        }
                        self::retail_pickup_assign($pickup_request_id, $rider_id);
                    }

                }
                if (count($allowed_pickup_requests) > 0) {
                    $pickup_note = V2PickupNote::where('rider_id', $rider_id)->where('status', 0);

                    if ($pickup_note->exists()) {
                        $pickup_note = $pickup_note->first();
                        if (!V2PickupNoteRequest::where('pickup_note_id', $pickup_note->id)->whereIn('pickup_request_id', $allowed_pickup_requests)->exists()) {
                            $pickup_note->pickups += $pickups;

                            $pickup_note->save();

                        }
                        $pickup_note_id = $pickup_note->id;
                    } else {
                        $pickup_note = new V2PickupNote();

                        $pickup_note->rider_id = $rider_id;
                        $pickup_note->pickups = $pickups;
                        $pickup_note->save();

                        $pickup_note_id = $pickup_note->id;
                    }

                    foreach ($allowed_pickup_requests as $pickup_request_id) {
                        if (!V2PickupNoteRequest::where('pickup_note_id', $pickup_note_id)->where('pickup_request_id', $pickup_request_id)->exists()) {
                            V2PickupNoteRequest::where('pickup_request_id', $pickup_request_id)->where('status', 0)->delete();
                            $pickup_note_request = new V2PickupNoteRequest();

                            $pickup_note_request->pickup_note_id = $pickup_note_id;
                            $pickup_note_request->pickup_request_id = $pickup_request_id;

                            $pickup_note_request->save();
                            $pickup_request = V2PickupRequest::find($pickup_request_id);
                            $assigned_shipments = $pickup_request->pickup_request_shipments;
                        }
                    }
                }
                $information = array();
                $information['pickup_note_id'] = $pickup_note->id;
                $rider = Rider::find($rider_id);
                $city = City::find($rider->city_id);

                if ($city->location_latitude && $city->location_longitude) {
                    $starting_location = $city->location_latitude . ',' . $city->location_longitude;
                    $pickup_note_requests = $pickup_note->pickup_note_requests;
                    $this->set_order_v2($starting_location, $pickup_note->id, $pickup_note_requests);
                }
                $pickup_address = $pickup_requests->pickup_address;
                $information['pickup_request_id'] = $pickup_requests->pickup_request_id;
                $information['shipments'] = $pickup_requests->booked;
                $information['shipper_name'] = $pickup_address->user->name;
                $information['person_of_contact'] = $pickup_address->poc;
                $information['phone_number'] = $pickup_address->phone;
                $information['address'] = $pickup_address->pickup_address;
                $information['location_latitude'] = $pickup_address->location_latitude;
                $information['location_longitude'] = $pickup_address->location_longitude;
                return response()->json(['status' => 0, 'message' => 'Pickup(s) are Assigned', 'information' => $information]);
            }
        } else {
            return response()->json(['status' => 1, 'message' => 'No Pickup(s) Assigned']);
        }
    }

    public function rider_wallet(Request $request)
    {
        $rules = [
//            'delivery_note_ids' => ['required', 'array', 'min:1', 'exists:delivery_notes,id']
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $rider_id = $request->rider_id;

            $delivery_notes = DeliveryNote::
            join('riders', 'delivery_notes.rider_id', '=', 'riders.id')
                ->select(['delivery_notes.id as delivery_note_id', 'delivery_notes.received_cod_amount as amount'])
                ->where('delivery_notes.cash_collection_status', 0)
                ->where('delivery_notes.status', '!=', 4)
                ->where('delivery_notes.rider_id', $rider_id);

            if ($delivery_notes->exists()) {
                $delivery_notes = $delivery_notes->get();
                return response()->json(['status' => 0, 'delivery_notes' => $delivery_notes]);
            } else {
                return response()->json(['status' => 0, 'message' => "No Delivery Note Found"]);
            }
        }
    }

    public function history_details(Request $request)
    {
        $rules = [
            'delivery_note_id' => ['nullable', 'exists:delivery_notes,id'],
            'return_note_id' => ['nullable', 'exists:return_notes,id'],
            'pickup_note_id' => ['nullable', 'exists:v2_pickup_notes,id']
        ];
        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            if ($request->has('pickup_note_id')) {
                $pickup_note_id = $request->pickup_note_id;
                $pickup_details = V2PickupNoteRequest::join('v2_pickup_requests as pr', 'pr.id', '=', 'v2_pickup_note_requests.pickup_request_id')
                    ->join('users as u', 'u.id', '=', 'pr.shipper_id')
                    ->select('u.name as shipper', 'pr.id as pickup_request_id', 'pr.booked as total_shipment', DB::raw('(select shipments from v2_rider_pickups where pickup_request_id = pr.id and pickup_type = 1) as rider_picked'), 'pr.received as arrived', DB::raw('(select created_at from v2_rider_pickups where pickup_request_id = pr.id) as pickup_date'))
                    ->where('v2_pickup_note_requests.pickup_note_id', $pickup_note_id);
                if ($pickup_details->exists()) {
                    $pickup_details = $pickup_details->orderBy('v2_pickup_note_requests.pickup_note_id', 'DESC')->get();
                    $data = array();
                    foreach ($pickup_details as $shipment_detail) {
                        $datum = array();
                        $datum['shipper'] = $shipment_detail->shipper;
                        $datum['pickup_request_id'] = $shipment_detail->pickup_request_id;
                        $datum['total_shipment'] = ($shipment_detail->total_shipment == null) ? 0 : $shipment_detail->total_shipment;
                        $datum['rider_picked'] = ($shipment_detail->rider_picked == null) ? 0 : $shipment_detail->rider_picked;
                        $datum['arrived'] = ($shipment_detail->arrived == null) ? 0 : $shipment_detail->arrived;
                        $datum['pickup_date'] = $shipment_detail->pickup_date;
                        $data[] = $datum;
                    }
                    return response()->json(['status' => 0, 'pickup_history_details' => $data]);
                } else {
                    return response()->json(['status' => 1, 'message' => "No Details Found"]);
                }
            } else if ($request->has('delivery_note_id')) {
                $delivery_note_id = $request->delivery_note_id;
                $shipment_details = DeliveryNoteShipment::join('shipments as s', 's.id', '=', 'delivery_note_shipments.shipment_id')
                    ->join('shipments_journey', function ($join) {
                        $join->on('shipments_journey.shipment_id', '=', 'delivery_note_shipments.shipment_id')
                            ->where('shipments_journey.id', '=',
                                DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = delivery_note_shipments.shipment_id and reference_1_id = delivery_note_shipments.delivery_note_id and shipments_journey.shipper_status_id != 5 and shipments_journey.rider_id is not null)'));
                    })
                    ->join('rider_deliveries', function ($join) {
                        $join->on('delivery_note_shipments.shipment_id', '=', 'rider_deliveries.shipment_id')
                            ->where('rider_deliveries.id', '=',
                                DB::raw('(select max(id) from rider_deliveries as rrd where rrd.shipment_id = delivery_note_shipments.shipment_id AND rrd.delivery_note_id = delivery_note_shipments.delivery_note_id)'));
                    })
                    ->leftjoin('shipment_status as ss', 'ss.id', '=', 'shipments_journey.shipper_status_id')
                    ->leftJoin('shipment_status_reason as ssr', 'ssr.id', '=', 'shipments_journey.status_reason_id')
                    ->select('s.tracking_number', 'shipments_journey.shipper_status_id', 'ss.name as shipment_status', 'ssr.name as shipment_reason', 'shipments_journey.created_at as update_date_time', 'shipments_journey.received_or_refused_by', 'rider_deliveries.picture_path', 'delivery_note_shipments.status as delivery_note_shipments_status', 'delivery_note_shipments.update_type as updated_type', 'delivery_note_shipments.fake_status as fake_status', 'delivery_note_shipments.fake_status_updated_at as fake_status_updated_at')
                    ->where('delivery_note_shipments.delivery_note_id', $delivery_note_id);

                if ($shipment_details->exists()) {
                    $shipment_details = $shipment_details->orderBy('delivery_note_shipments.delivery_note_id', 'DESC')->get();
                    $data = array();
                    foreach ($shipment_details as $shipment_detail) {
                        $datum = array();
                        $datum['tracking_no'] = $shipment_detail->tracking_number;
                        $datum['shipment_status'] = $shipment_detail->shipment_status;
                        $datum['shipment_reason'] = $shipment_detail->shipment_reason;
                        $datum['update_date_time'] = $shipment_detail->update_date_time;
                        $datum['received_or_refused_by'] = $shipment_detail->received_or_refused_by;
                        $datum['picture_path'] = $shipment_detail->picture_path;
                        $datum['fake_status'] = $shipment_detail->fake_status;
                        $datum['fake_status_updated_at'] = $shipment_detail->fake_status_updated_at;
                        if ($shipment_detail->updated_type == 0) {
                            $datum['updated_by'] = "Debriefer";
                        } else {
                            $datum['updated_by'] = "Rider";
                        }
                        if (in_array($shipment_detail->shipper_status_id, [14, 30, 36, 37])) {
                            $datum['status'] = 'Delivered';
                        } else {
                            $datum['status'] = 'Undelivered';
                        }
                        $data[] = $datum;
                    }
                    return response()->json(['status' => 0, 'history_details' => $data]);
                } else {
                    return response()->json(['status' => 1, 'message' => "No Details Found"]);
                }

            } else if ($request->has('return_note_id')) {
                $return_note_id = $request->return_note_id;
                $shipment_details = ReturnNoteShipment::join('shipments as s', 's.id', '=', 'return_note_shipments.shipment_id')
                    ->join('shipments_journey', function ($join) {
                        $join->on('shipments_journey.shipment_id', '=', 'return_note_shipments.shipment_id')
                            ->where('shipments_journey.id', '=',
                                DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = return_note_shipments.shipment_id and reference_1_id = return_note_shipments.return_note_id and shipments_journey.shipper_status_id != 23 and shipments_journey.rider_id is not null)'));
                    })
                    ->join('rider_return_deliveries', function ($join) {
                        $join->on('return_note_shipments.shipment_id', '=', 'rider_return_deliveries.shipment_id')
                            ->where('rider_return_deliveries.id', '=',
                                DB::raw('(select max(id) from rider_return_deliveries as rrd where rrd.shipment_id = return_note_shipments.shipment_id AND rrd.return_note_id = return_note_shipments.return_note_id)'));
                    })
                    ->leftjoin('shipment_status as ss', 'ss.id', '=', 'shipments_journey.shipper_status_id')
                    ->leftJoin('shipment_status_reason as ssr', 'ssr.id', '=', 'shipments_journey.status_reason_id')
                    ->select('s.tracking_number', 'shipments_journey.shipper_status_id', 'ss.name as shipment_status', 'ssr.name as shipment_reason', 'shipments_journey.created_at as update_date_time', 'shipments_journey.received_or_refused_by', 'rider_return_deliveries.picture_path', 'return_note_shipments.status as return_note_shipments_status', 'return_note_shipments.update_type as updated_type')
                    ->where('return_note_shipments.return_note_id', $return_note_id);

                if ($shipment_details->exists()) {
                    $shipment_details = $shipment_details->orderBy('return_note_shipments.return_note_id', 'DESC')->get();
                    $data = array();
                    foreach ($shipment_details as $shipment_detail) {
                        $datum = array();
                        $datum['tracking_no'] = $shipment_detail->tracking_number;
                        $datum['shipment_status'] = $shipment_detail->shipment_status;
                        $datum['shipment_reason'] = $shipment_detail->shipment_reason;
                        $datum['update_date_time'] = $shipment_detail->update_date_time;
                        $datum['received_or_refused_by'] = $shipment_detail->received_or_refused_by;
                        $datum['picture_path'] = $shipment_detail->picture_path;
                        if ($shipment_detail->updated_type == 0) {
                            $datum['updated_by'] = "Return Assistant";
                        } else {
                            $datum['updated_by'] = "Rider";
                        }
                        if (in_array($shipment_detail->shipper_status_id, [25])) {
                            $datum['status'] = 'Delivered';
                        } else {
                            $datum['status'] = 'Undelivered';
                        }
                        $data[] = $datum;
                    }
                    return response()->json(['status' => 0, 'history_details' => $data]);
                } else {
                    return response()->json(['status' => 1, 'message' => "No Details Found"]);
                }

            } else {
                return response()->json(['status' => 1, 'message' => "No Parameter Provided"]);
            }
        }
    }

    public function pickups_history_v2(Request $request)
    {
        $rider_id = $request->rider_id;
        $from_date = explode(" ",$request->get('from_date'))[0]." 06:00:00";
        $to_date = $request->get('to_date');
        if($to_date == null){
            $to_date = strval(Carbon::parse($from_date)->addDay());
        }else{
            $to = explode(" ",$request->get('to_date'))[0]." 06:00:00";
            $to_date = strval(Carbon::parse($to)->addDay());
        }
        $pickup_request_id = $request->get('pickup_request_id');
        $pickup_note_id = $request->get('pickup_note_id');

        if ($from_date == null && $to_date == null && $pickup_request_id == null && $pickup_note_id == null) {
            return response()->json(["status" => 1, "message" => "Please provide parameter(s)"]);
        } else {
            $rider_pickups = V2PickupNote::select('v2_pickup_notes.id as pickup_note_id', DB::raw('(SELECT SUM(vprs.booked) FROM v2_pickup_note_requests AS vpnr LEFT JOIN v2_pickup_requests AS vprs ON vprs.id = vpnr.pickup_request_id WHERE vpnr.pickup_note_id = v2_pickup_notes.id ) as total_shipments'), DB::raw('(SELECT SUM(vrp.shipments) FROM v2_rider_pickups as vrp WHERE vrp.pickup_note_id = v2_pickup_notes.id) as rider_picked'), DB::raw('(SELECT COUNT(vpnr2.shipment_id) FROM v2_pickup_received_shipments AS vpnr2 WHERE vpnr2.pickup_note_id = v2_pickup_notes.id AND vpnr2.pickup_note_id is not null and vpnr2.created_at between "' . $from_date . '" and "' . $to_date . '") as arrived'), 'v2_pickup_notes.created_at as created_at')
                ->groupBy('v2_pickup_notes.id')
                ->where('v2_pickup_notes.rider_id', '=', $rider_id)
                ->where('v2_pickup_notes.status', 1);

            if ($from_date != null && $to_date != null) {
                $rider_pickups = $rider_pickups->whereBetween('v2_pickup_notes.created_at', [$from_date, $to_date]);
            } elseif ($from_date != null) {
                $rider_pickups = $rider_pickups->whereDate('v2_pickup_notes.created_at', $from_date);
            }
            if ($pickup_request_id != null) {
                $pickup_note = V2PickupNoteRequest::where('pickup_request_id', $pickup_request_id);
                if ($pickup_note->exists()) {
                    $pickup_note = $pickup_note->first();
                    $rider_pickups = $rider_pickups->where('v2_pickup_notes.id', $pickup_note->pickup_note_id);
                } else {
                    return response()->json(["status" => 1, "message" => "No pickups found!"]);
                }
            }
            if ($pickup_note_id != null) {
                $rider_pickups = $rider_pickups->where('v2_pickup_notes.id', $pickup_note_id);
            }

            if ($rider_pickups->exists()) {
                $rider_pickups = $rider_pickups->orderBy('v2_pickup_notes.created_at', 'DESC')->get();
                return response()->json(["status" => 0, "pickups" => $rider_pickups]);
            } else {
                return response()->json(["status" => 1, "message" => "No pickups found!"]);
            }
        }
    }

    public function delivery_history_v2(Request $request)
    {
        $rider_id = $request->rider_id;
        $from_date = $request->get('from_date');
        $to_date = $request->get('to_date');
        $delivery_note_id = $request->get('delivery_note_id');
        $tracking_no = $request->get('tracking_no');

        if ($from_date == null && $to_date == null && $delivery_note_id == null && $tracking_no == null) {
            return response()->json(["status" => 1, "message" => "Please provide parameter(s)"]);
        } else {
            $rider_deliveries = DeliveryNote::join('delivery_note_shipments', 'delivery_note_shipments.delivery_note_id', '=', 'delivery_notes.id')
                ->join('shipments', 'shipments.id', '=', 'delivery_note_shipments.shipment_id')
                ->select('delivery_notes.created_at as created_at', 'delivery_notes.id as delivery_note_id', 'shipments.tracking_number as tracking_number')
                ->where('delivery_notes.pending_status', 1)
                ->where('delivery_notes.rider_id', $rider_id);

            if ($from_date != null && $to_date != null) {
                $rider_deliveries = $rider_deliveries->whereBetween('delivery_notes.created_at', [$from_date, $to_date])
                    ->groupBy('delivery_notes.id');
            } elseif ($from_date != null) {
                $rider_deliveries = $rider_deliveries->whereDate('delivery_notes.created_at', $from_date)
                    ->groupBy('delivery_notes.id');
            }

            if ($delivery_note_id != null) {
                $rider_deliveries = $rider_deliveries->where('delivery_notes.id', $delivery_note_id)
                    ->groupBy('delivery_notes.id');
            }

            if ($tracking_no != null) {
                $rider_deliveries = $rider_deliveries->where('shipments.tracking_number', $tracking_no)
                    ->groupBy('delivery_notes.id');
            }

            if ($rider_deliveries->exists()) {
                $rider_deliveries = $rider_deliveries->orderBy('delivery_notes.created_at', 'DESC')->get();
                $rider_delivery_history = array();
                foreach ($rider_deliveries as $rider_delivery) {
                    $fake_status_count = DeliveryNoteShipment::where('delivery_note_id', $rider_delivery->delivery_note_id)
                        ->where('update_type', 1)
                        ->where('fake_status', 1)->count('fake_status');
                    $undelivered_shipments = DeliveryNoteShipment::where('delivery_note_id', $rider_delivery->delivery_note_id)->where('status', 1)->where('update_type', 1)->count();
                    $delivered_shipments = DeliveryNoteShipment::where('delivery_note_id', $rider_delivery->delivery_note_id)->where('status', '>', 1)->where('update_type', 1)->count();
                    $delivery_history = array();
                    $delivery_history['delivery_note'] = $rider_delivery->delivery_note_id;
                    $delivery_history['shipments_count'] = $delivered_shipments + $undelivered_shipments;
                    $delivery_history['delivered_shipments'] = $delivered_shipments;
                    $delivery_history['undelivered_shipments'] = $undelivered_shipments;
                    $delivery_history['fake_status_count'] = $fake_status_count;
                    $delivery_history['created_at'] = date('Y-m-d', strtotime($rider_delivery->created_at));
                    $rider_delivery_history[] = $delivery_history;
                }
                return response()->json(["status" => 0, "deliveries" => $rider_delivery_history]);
            } else {
                return response()->json(["status" => 1, "message" => "No deliveries found!"]);
            }
        }


    }

    public function return_history_v2(Request $request)
    {
        $rider_id = $request->rider_id;
        $from_date = $request->get('from_date');
        $to_date = $request->get('to_date');
        $return_note_id = $request->get('return_note_id');
        $tracking_no = $request->get('tracking_no');

        if ($from_date == null && $to_date == null && $return_note_id == null && $tracking_no == null) {
            return response()->json(["status" => 1, "message" => "Please Provide parameter(s)"]);
        } else {
            $rider_return_deliveries = ReturnNote::join('return_note_shipments as rns', 'return_notes.id', '=', 'rns.return_note_id')
                ->join('shipments as s', 'rns.shipment_id', '=', 's.id')
                ->select('return_notes.id as return_note_id', 'return_notes.created_at as created_at', 's.tracking_number as tracking_number')
                ->where('return_notes.rider_id', $rider_id)
                ->whereIn('return_notes.status', [1, 3]);

            if ($from_date != null && $to_date != null) {
                $rider_return_deliveries = $rider_return_deliveries->whereBetween('return_notes.created_at', [$from_date, $to_date])
                    ->groupBy('return_notes.id');
            } elseif ($from_date != null) {
                $rider_return_deliveries = $rider_return_deliveries->whereDate('return_notes.created_at', $from_date)
                    ->groupBy('return_notes.id');
            }

            if ($return_note_id != null) {
                $rider_return_deliveries = $rider_return_deliveries->where('return_notes.id', $return_note_id)
                    ->groupBy('return_notes.id');
            }

            if ($tracking_no != null) {
                $rider_return_deliveries = $rider_return_deliveries->where('s.tracking_number', $tracking_no)
                    ->groupBy('return_notes.id');
            }
            $rider_return_history = array();
            if ($rider_return_deliveries->exists()) {
                $rider_return_deliveries = $rider_return_deliveries->orderBy('return_notes.created_at', 'DESC')->get();
                foreach ($rider_return_deliveries as $rider_return_delivery) {
                    $undelivered_shipments = ReturnNoteShipment::where('return_note_id', $rider_return_delivery->return_note_id)->where('status', 1)->where('update_type', 1)->count();
                    $delivered_shipments = ReturnNoteShipment::where('return_note_id', $rider_return_delivery->return_note_id)->where('status', 2)->where('update_type', 1)->count();
                    $return_history = array();
                    $return_history['return_note_id'] = $rider_return_delivery->return_note_id;
                    $return_history['total_shipments'] = $delivered_shipments + $undelivered_shipments;
                    $return_history['delivered_shipments'] = $delivered_shipments;
                    $return_history['undelivered_shipments'] = $undelivered_shipments;
                    $return_history['created_at'] = date('Y-m-d', strtotime($rider_return_delivery->created_at));
                    $rider_return_history[] = $return_history;
                }
                return response()->json(["status" => 0, "return_history" => $rider_return_history]);
            } else {
                return response()->json(["status" => 1, "message" => "No return deliveries found!"]);
            }
        }


    }

    public function shipment_undelivered_v2(Request $request)
    {

        $rules = [
            'added_at' => ['required'],
            'delivery_note_id' => ['required', 'integer', 'digits_between:1,10', 'exists:delivery_notes,id'],
            'start_location_latitude' => ['required', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'start_location_longitude' => ['required', 'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'],
            'actual_location_latitude' => ['required', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'actual_location_longitude' => ['required', 'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'],
            'shipment_id' => ['required', 'integer', 'digits_between:1,10', 'exists:shipments,id'],
            'shipper_status_id' => ['required', 'integer', 'digits_between:1,10', 'exists:shipment_status,id'],
            'status_reason_id' => ['required', 'integer', 'digits_between:1,10', 'exists:shipment_status_reason,id'],
            'remarks' => ['nullable', 'string', 'max:255'],
            'picture' => ['required', 'image'],
            'open_box' => ['required', 'integer'],
            'audio' => ['nullable', 'file'],
            'otp_entered' => ['nullable', 'integer'],
        ];
        $message = '';

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {

            $rider_id = $request->rider_id;

            $added_at = Carbon::createFromTimestampMs($request->added_at)->toDateTimeString();
            if (!RiderDelivery::where('delivery_note_id', $request->delivery_note_id)->where('shipment_id', $request->shipment_id)->where('delivered_status', 1)->exists()) {
                if (!Shipment::where('id', $request->shipment_id)->whereIn('shipper_status_id', [14, 30, 36, 37, 20, 52, 13])->exists()) {
                    if (DeliveryNoteShipment::join('delivery_notes as dn', 'delivery_note_shipments.delivery_note_id', 'dn.id')->where('delivery_note_id', $request->delivery_note_id)->where('shipment_id', $request->shipment_id)->where('dn.rider_id', $rider_id)->exists()) {
                        $shipments = ShipmentsJourney::select('shipper_status_id', 'status_reason_id')
                            ->where('reference_1_id', $request->delivery_note_id)
                            ->where('shipment_id', $request->shipment_id)
                            ->where('shipper_status_id', $request->shipper_status_id)
                            ->where('status_reason_id', $request->status_reason_id)
                            ->where('rider_id', $rider_id);
                        if (!$shipments->exists()) {
                            $shipment = Shipment::find($request->shipment_id);

                            $destination = $request->actual_location_latitude . ',' . $request->actual_location_longitude;

                            $rider_delivery = new RiderDelivery();

                            $rider_delivery->added_at = $added_at;
                            $rider_delivery->delivery_note_id = $request->delivery_note_id;
                            $rider_delivery->shipment_id = $request->shipment_id;
                            $rider_delivery->rider_id = $request->rider_id;
                            $rider_delivery->start_location_latitude = $request->start_location_latitude;
                            $rider_delivery->start_location_longitude = $request->start_location_longitude;
                            $rider_delivery->actual_location_latitude = $request->actual_location_latitude;
                            $rider_delivery->actual_location_longitude = $request->actual_location_longitude;
                            $rider_delivery->rider_status_id = $request->shipper_status_id;
                            $rider_delivery->rider_status_reason_id = $request->status_reason_id;
                            $rider_delivery->delivered_status = 0;

                            $consignee_phone_number_1 = $shipment->consignee_phone_number_1;
                            $consignee_phone_number_2 = $shipment->consignee_phone_number_2;
                            $consignee_address = $shipment->consignee_address;

                            $coordinates = ConsigneeLocation::where(function ($sub_query) use ($consignee_phone_number_1, $consignee_phone_number_2) {
                                $sub_query->where('phone_number', $consignee_phone_number_1)
                                    ->orwhere('phone_number', $consignee_phone_number_2);
                            })->where('address', $consignee_address);

                            if ($request->actual_location_latitude > 0 && $request->actual_location_longitude > 0) {
                                $origin = $request->start_location_latitude . ',' . $request->start_location_longitude;

                                $rider_delivery->distance_from_start_to_actual = $this->distance($origin, $destination);

                                if ($coordinates->exists()) {
                                    $coordinates = $coordinates->latest()->first();

                                    $rider_delivery->current_location_latitude = $coordinates->lat;
                                    $rider_delivery->current_location_longitude = $coordinates->long;

                                    $origin = $coordinates->lat . ',' . $coordinates->long;

                                    $distance = $this->distance($origin, $destination);

                                    $rider_delivery->distance_from_current_to_actual = $distance;
                                }
                            } else {
                                $rider_delivery->distance_from_start_to_actual = 0;

                                if ($coordinates->exists()) {
                                    $rider_delivery->current_location_latitude = $coordinates->lat;
                                    $rider_delivery->current_location_longitude = $coordinates->long;
                                    $rider_delivery->distance_from_current_to_actual = 0;
                                }
                            }
                            if($request->has('otp_entered') && $request->status_reason_id == 8){
                                $rider_delivery->otp_entered = $request->otp_entered;
                            }
                            $rider_delivery->save();

                            $time = Carbon::now()->toDateString();
                            $picture_path = 'rider_delivery/' . $rider_delivery->id . '_' . $time . '.png';
                            Storage::disk('public')->put($picture_path, file_get_contents($request->picture));
                            $rider_delivery->picture_path = $picture_path;
                            $rider_delivery->save();
                            $environment = config('app.env');

                            if ($request->has('audio')) {
                                $time = Carbon::now()->toDateString();
                                if ($environment == 'production') {
                                    $extension = $request->file('audio')->getClientOriginalExtension();
                                    $audio_path = 'rider_delivery_audio/' . $rider_delivery->id . '-' . $time . '.' . $extension;
                                    Storage::disk('s3')->put($audio_path, file_get_contents($request->audio));
                                    $rider_delivery->audio_path = $audio_path;
                                    $rider_delivery->save();
                                } else {
                                    $extension = $request->file('audio')->getClientOriginalExtension();
                                    $audio_path = 'rider_delivery_audio/' . $rider_delivery->id . '-' . $time . '.' . $extension;
                                    Storage::disk('public')->put($audio_path, file_get_contents($request->audio));
                                    $rider_delivery->audio_path = $audio_path;
                                    $rider_delivery->save();
                                }
                            }

                            if (DeliveryNote::where('id', $request->delivery_note_id)->where('pending_status', 0)->exists()) {

                                $shipment->shipper_status_id = $request->shipper_status_id;
                                $shipment->consignee_status_id = $request->shipper_status_id;
                                $shipment->open_box = $request->open_box;
                                $shipment->delivery_in_route = 0;
                                $shipment->save();

                                $remarks = NULL;
                                if ($request->has('remarks')) {
                                    $remarks = $request->remarks;
                                }

                                ShipmentsJourneyController::add($shipment->id, $request->shipper_status_id, $request->shipper_status_id, $request->status_reason_id, $remarks, NULL, NULL, $request->delivery_note_id, NULL, 0, NULL, $rider_id);
                                if($request->shipper_status_id != 7){
                                    NotificationsController::send(145, $shipment->id, $request->delivery_note_id);
                                }
                                DeliveryNoteShipment::where('delivery_note_id', $request->delivery_note_id)->where('shipment_id', $shipment->id)->update(['status' => 1, 'update_type' => 1]);
                                $rider_delivery_note_status = RiderDeliveryNoteStatus::where('delivery_note_id', $request->delivery_note_id);
                                if (!$rider_delivery_note_status->exists()) {
                                    $new_status = new RiderDeliveryNoteStatus();
                                    $new_status->delivery_note_id = $request->delivery_note_id;
                                    $new_status->status = 2;
                                    $new_status->save();
                                } else {
                                    $rider_delivery_note_status = $rider_delivery_note_status->first();
                                    $rider_delivery_note_status->status = 2;
                                    $rider_delivery_note_status->save();
                                }
                            }


                            $updated_shipments_count = DeliveryNoteShipment::where('delivery_note_id', $request->delivery_note_id)->where('status', 0)->count();
                            if ($updated_shipments_count == 0) {
                                DeliveryNote::where('id', $request->delivery_note_id)->update(['pending_status' => 1, 'pending_for_verification_at' => Carbon::now()]);
                            }

                            $arr['shipment_id'] = $request->shipment_id;
                            $arr['delivery_note_id'] = $request->delivery_note_id;
                            dispatch(new ProcessAgentCallMonitoring($arr));


                            $message = 'Shipment is marked as Undelivered Successfully';
                        } else {
                            $message = 'Shipment is already marked as Undelivered';
                        }
                    }
                } else {
                    $message = 'Shipment status is already marked';
                }
            } else {
                $message = 'Shipment is already marked as Delivered';
            }
            return response()->json(['status' => 0, 'message' => $message, 'delivery_note_id' => $request->delivery_note_id, 'shipment_id' => $request->shipment_id]);

        }
    }

    public function pickup_not_pick_v3(Request $request)
    {
        $rules = [
            'added_at' => ['required'],
            'pickup_note_id' => ['required', 'integer', 'digits_between:1,10', 'exists:v2_pickup_notes,id'],
            'pickup_request_id' => ['required', 'integer', 'digits_between:1,10', 'exists:v2_pickup_requests,id'],
            'start_location_latitude' => ['required', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'start_location_longitude' => ['required', 'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'],
            'actual_location_latitude' => ['required', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'actual_location_longitude' => ['required', 'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'],
            'reason_id' => ['required', 'integer', 'digits_between:1,10', 'exists:v2_pickup_request_not_pick_reasons,id'],
            'rider_remarks' => ['nullable'],
            'picture' => ['required', 'image'],
            'audio' => ['nullable', 'file']
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $rider_id = $request->rider_id;

            $added_at = Carbon::createFromTimestampMs($request->added_at)->toDateTimeString();

            if (!V2RiderPickup::where('pickup_note_id', $request->pickup_note_id)->where('pickup_request_id', $request->pickup_request_id)->where('pickup_type', 0)->where('added_at', $added_at)->exists()) {
                if (V2PickupRequest::where('id', $request->pickup_request_id)->where('current_rider_id', $rider_id)->exists()) {
                    $pickup_request = V2PickupRequest::find($request->pickup_request_id);

                    $pickup_address = $pickup_request->pickup_address;

                    $pickup_request->status_id = 3;
                    $pickup_request->pickup_in_route = 0;
                    $pickup_request->save();
                    $pickup_request_attempt = $pickup_request->pickup_attempt_latest->where('rider_id', $rider_id)->first();
                    $pickup_request_attempt->reason_id = $request->reason_id;
                    $pickup_request_attempt->save();

                    $destination = $request->actual_location_latitude . ',' . $request->actual_location_longitude;

                    $rider_pickup = new V2RiderPickup();

                    $rider_pickup->added_at = $added_at;
                    $rider_pickup->pickup_note_id = $request->pickup_note_id;
                    $rider_pickup->pickup_request_id = $request->pickup_request_id;
                    $rider_pickup->pickup_type = 0;
                    $rider_pickup->start_location_latitude = $request->start_location_latitude;
                    $rider_pickup->start_location_longitude = $request->start_location_longitude;
                    $rider_pickup->actual_location_latitude = $request->actual_location_latitude;
                    $rider_pickup->actual_location_longitude = $request->actual_location_longitude;

                    if ($request->actual_location_latitude > 0 && $request->actual_location_longitude > 0) {
                        $origin = $request->start_location_latitude . ',' . $request->start_location_longitude;

                        $rider_pickup->distance_from_start_to_actual = $this->distance($origin, $destination);

                        if ($pickup_address->location_latitude && $pickup_address->location_longitude) {
                            $rider_pickup->current_location_latitude = $pickup_address->location_latitude;
                            $rider_pickup->current_location_longitude = $pickup_address->location_longitude;

                            $origin = $pickup_address->location_latitude . ',' . $pickup_address->location_longitude;

                            $rider_pickup->distance_from_current_to_actual = $this->distance($origin, $destination);
                        }
                    } else {
                        $rider_pickup->distance_from_start_to_actual = 0;

                        if ($pickup_address->location_latitude && $pickup_address->location_longitude) {
                            $rider_pickup->current_location_latitude = $pickup_address->location_latitude;
                            $rider_pickup->current_location_longitude = $pickup_address->location_longitude;
                            $rider_pickup->distance_from_current_to_actual = 0;
                        }
                    }

                    $rider_pickup->pickup_not_pick_reason_id = $request->reason_id;
                    $rider_pickup->rider_remarks = str_replace("\"", "", $request->rider_remarks);

                    $rider_pickup->save();

                    $picture_path = 'rider_pickup/' . $rider_pickup->id . '.png';

                    Storage::disk('public')->put($picture_path, file_get_contents($request->picture));

                    $rider_pickup->picture_path = $picture_path;

                    $rider_pickup->save();

                    $environment = config('app.env');

                    if ($request->has('audio')) {
                        if ($environment == 'production') {
                            $extension = $request->file('audio')->getClientOriginalExtension();
                            $audio_path = 'rider_pickup_audio/' . $rider_pickup->id . '.' . $extension;
                            Storage::disk('s3')->put($audio_path, file_get_contents($request->audio));
                            $rider_pickup->audio_path = $audio_path;
                            $rider_pickup->save();
                        } else {
                            $extension = $request->file('audio')->getClientOriginalExtension();
                            $audio_path = 'rider_pickup_audio/' . $rider_pickup->id . '.' . $extension;
                            Storage::disk('public')->put($audio_path, file_get_contents($request->audio));
                            $rider_pickup->audio_path = $audio_path;
                            $rider_pickup->save();
                        }
                    }

                    V2PickupNoteRequest::where('pickup_note_id', $request->pickup_note_id)->where('pickup_request_id', $request->pickup_request_id)->update(['status' => 1]);
                    $pickup_note_requests_count = V2PickupNoteRequest::where('pickup_note_id', $request->pickup_note_id)->where('status', 0)->count();
                    if ($pickup_note_requests_count == 0) {
                        V2PickupNote::where('id', $request->pickup_note_id)->update(['status' => 1]);
                    }

                    NotificationsController::send(105, $request->pickup_request_id, $request->reason_id);
                }
            }

            return response()->json(['status' => 0, 'message' => 'Pickup Not Pick Successfully', 'pickup_note_id' => $request->pickup_note_id, 'pickup_request_id' => $request->pickup_request_id]);
        }
    }

    public function return_shipment_undelivered_v2(Request $request)
    {
        $rules = [
            'added_at' => ['required'],
            'return_note_id' => ['required', 'integer', 'digits_between:1,10', 'exists:return_notes,id'],
            'start_location_latitude' => ['required', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'start_location_longitude' => ['required', 'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'],
            'actual_location_latitude' => ['required', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'actual_location_longitude' => ['required', 'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'],
            'shipment_id' => ['required', 'integer', 'digits_between:1,10', 'exists:shipments,id'],
            'shipper_status_id' => ['required', 'integer', 'digits_between:1,10', 'exists:shipment_status,id'],
            'status_reason_id' => ['nullable'],
            'remarks' => ['nullable', 'string', 'max:255'],
            'picture' => ['required', 'image'],
            'audio' => ['nullable', 'file']
        ];
        $message = '';

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {

            $rider_id = $request->rider_id;

            $added_at = Carbon::createFromTimestampMs($request->added_at)->toDateTimeString();
            if (!RiderReturnDelivery::where('return_note_id', $request->return_note_id)->where('shipment_id', $request->shipment_id)->where('delivered_status', 1)->exists()) {
                if (!Shipment::where('id', $request->shipment_id)->whereIn('shipper_status_id', [25])->exists()) {
                    if (ReturnNoteShipment::join('return_notes as rn', 'return_note_shipments.return_note_id', 'rn.id')->where('return_note_id', $request->return_note_id)->where('shipment_id', $request->shipment_id)->where('rn.rider_id', $rider_id)->exists()) {
                        $shipments = ShipmentsJourney::select('shipper_status_id', 'status_reason_id')
                            ->where('reference_1_id', $request->delivery_note_id)
                            ->where('shipment_id', $request->shipment_id)
                            ->where('shipper_status_id', $request->shipper_status_id)
                            ->where('status_reason_id', $request->status_reason_id)
                            ->where('rider_id', $rider_id);
                        if (!$shipments->exists()) {
                            $shipment = Shipment::find($request->shipment_id);
                            $shipper_data = $shipment->user;
                            $pickup_address = $shipment->pickup_address;

                            $destination = $request->actual_location_latitude . ',' . $request->actual_location_longitude;

                            $rider_return_delivery = new RiderReturnDelivery();

                            $rider_return_delivery->added_at = $added_at;
                            $rider_return_delivery->return_note_id = $request->return_note_id;
                            $rider_return_delivery->shipment_id = $shipment->id;
                            $rider_return_delivery->rider_id = $request->rider_id;
                            $rider_return_delivery->start_location_latitude = $request->start_location_latitude;
                            $rider_return_delivery->start_location_longitude = $request->start_location_longitude;
                            $rider_return_delivery->actual_location_latitude = $request->actual_location_latitude;
                            $rider_return_delivery->actual_location_longitude = $request->actual_location_longitude;
                            $rider_return_delivery->rider_status_id = $request->shipper_status_id;
                            $rider_return_delivery->rider_status_reason_id = ($request->status_reason_id != -1) ? $request->status_reason_id : null;
                            $rider_return_delivery->delivered_status = 0;
                            $shipper_phone_number_1 = $pickup_address->phone;
                            $shipper_address = $pickup_address->pickup_address;
                            $shipper_lat = $pickup_address->location_latitude;
                            $shipper_long = $pickup_address->location_longitude;

                            if ($request->actual_location_latitude > 0 && $request->actual_location_longitude > 0) {
                                $origin = $request->start_location_latitude . ',' . $request->start_location_longitude;

                                $rider_return_delivery->distance_from_start_to_actual = $this->distance($origin, $destination);

                                if ($shipper_lat != null && $shipper_long != null) {
                                    $rider_return_delivery->current_location_latitude = $shipper_lat;
                                    $rider_return_delivery->current_location_longitude = $shipper_long;

                                    $origin = $shipper_lat . ',' . $shipper_long;

                                    $distance = $this->distance($origin, $destination);

                                    $rider_return_delivery->distance_from_current_to_actual = $distance;
                                }
                            } else {
                                $rider_return_delivery->distance_from_start_to_actual = 0;

                                if ($shipper_lat != null && $shipper_long != null) {
                                    $rider_return_delivery->current_location_latitude = $shipper_lat;
                                    $rider_return_delivery->current_location_longitude = $shipper_long;
                                    $rider_return_delivery->distance_from_current_to_actual = 0;
                                }
                            }
                            $rider_return_delivery->save();

                            $picture_path = 'rider_return_delivery/' . $rider_return_delivery->id . '.png';
                            Storage::disk('public')->put($picture_path, file_get_contents($request->picture));
                            $rider_return_delivery->picture_path = $picture_path;
                            $rider_return_delivery->save();

                            $environment = config('app.env');
                            if ($request->has('audio')) {
                                if ($environment == 'production') {
                                    $extension = $request->file('audio')->getClientOriginalExtension();
                                    $audio_path = 'rider_return_delivery_audio/' . $rider_return_delivery->id . '.' . $extension;
                                    Storage::disk('s3')->put($audio_path, file_get_contents($request->audio));
                                    $rider_return_delivery->audio_path = $audio_path;
                                    $rider_return_delivery->save();
                                } else {
                                    $extension = $request->file('audio')->getClientOriginalExtension();
                                    $audio_path = 'rider_return_delivery_audio/' . $rider_return_delivery->id . '.' . $extension;
                                    Storage::disk('public')->put($audio_path, file_get_contents($request->audio));
                                    $rider_return_delivery->audio_path = $audio_path;
                                    $rider_return_delivery->save();
                                }
                            }

                            if (ReturnNote::where('id', $request->return_note_id)->exists()) {

                                $shipment->shipper_status_id = $request->shipper_status_id;
                                $shipment->consignee_status_id = $request->shipper_status_id;
                                $shipment->save();

                                $remarks = NULL;

                                if ($request->has('remarks')) {
                                    $remarks = $request->remarks;
                                }

                                ShipmentsJourneyController::add($shipment->id, $request->shipper_status_id, $request->shipper_status_id, ($request->status_reason_id != -1) ? $request->status_reason_id : null, $remarks, NULL, NULL, $request->return_note_id, NULL, 1, NULL, $rider_id);
                                ReturnNoteShipment::where('return_note_id', $request->return_note_id)->where('shipment_id', $shipment->id)->update(['status' => 1, 'update_type' => 1]);
                                $rider_return_note_status = RiderReturnNoteStatus::where('return_note_id', $request->return_note_id);
                                if (!$rider_return_note_status->exists()) {
                                    $new_status = new RiderReturnNoteStatus();
                                    $new_status->return_note_id = $request->return_note_id;
                                    $new_status->status = 2;
                                    $new_status->save();
                                } else {
                                    $rider_return_note_status = $rider_return_note_status->first();
                                    $rider_return_note_status->status = 2;
                                    $rider_return_note_status->save();
                                }
                            }

                            $return_note_data = ReturnNote::find($request->return_note_id);

                            if ($return_note_data->completion_status == 0) {
                                $return_note_data->completion_status = 1;
                                $return_note_data->save();
                            }

                            $updated_shipments_count = ReturnNoteShipment::where('return_note_id', $request->return_note_id)->where('status', 0)->count();

                            if ($updated_shipments_count == 0) {
                                $return_note_data->status = 3;
                                $return_note_data->updated_at = Carbon::now();
                                $return_note_data->save();
                            }
                            $message = 'Shipment is marked as Undelivered Successfully';
                        }
                    } else {
                        $message = 'Shipment is already marked as Undelivered';
                    }
                } else {
                    $message = 'Shipment is already marked as Delivered';
                }
            }

            return response()->json(['status' => 0, 'message' => $message, 'return_note_id' => $request->return_note_id, 'shipment_id' => $request->shipment_id]);

        }
    }

    public function delivery_summary_multiple_v2(Request $request)
    {
        return response()->json(['status' => 0, 'message' => 'Please Update Your Bolt App']);
        $rider_id = $request->rider_id;

        $delivery_notes = DeliveryNote::where('rider_id', $rider_id)->where('status', 0)->where('pending_status', 0);

        if ($delivery_notes->exists()) {
            $delivery_notes = $delivery_notes->get();

            $nodes = array();

            foreach ($delivery_notes as $delivery_note) {

                if ($delivery_note->shipments_count == $delivery_note->delivered_shipments) {
                    continue;
                }
                $information = array();

                $information['delivery_note_id'] = $delivery_note->id;
                $information['assigned_date'] = $delivery_note->created_at->toDateTimeString();
                $information['no_of_parcels'] = $delivery_note->shipments_count;
                $information['summary'] = array();
                $total_shipments = $delivery_note->shipments_count;
                $information['summary']['deliveries'] = $total_shipments;
                $information['summary']['completed'] = array();
                $information['summary']['completed']['pending'] = 0;
                $information['summary']['completed']['undelivered'] = 0;
                $information['summary']['completed']['delivered'] = 0;
                $rider_deliveries = RiderDelivery::where('delivery_note_id', $delivery_note->id);

                $updated_shipments = 0;

                if ($rider_deliveries->exists()) {
                    $undelivered_shipments = 0;
                    $delivered_shipments = 0;
                    $updated_shipments_count = RiderDelivery::where('delivery_note_id', $delivery_note->id)->count(DB::raw('DISTINCT shipment_id'));
                    if ($updated_shipments_count > 0) {
                        $updated_shipments = $updated_shipments_count;
                    }

                    $undelivered_shipments_count = RiderDelivery::where('delivery_note_id', $delivery_note->id)->where('delivered_status', 0)->count(DB::raw('DISTINCT shipment_id'));
                    if ($undelivered_shipments_count > 0) {
                        $undelivered_shipments = $undelivered_shipments_count;
                    }

                    $delivered_shipments_count = RiderDelivery::where('delivery_note_id', $delivery_note->id)->where('delivered_status', 1)->count(DB::raw('DISTINCT shipment_id'));
                    if ($delivered_shipments_count > 0) {
                        $delivered_shipments = $delivered_shipments_count;
                    }
                    $information['summary']['completed']['pending'] = $total_shipments - $updated_shipments;
                    $information['summary']['completed']['undelivered'] = $undelivered_shipments;
                    $information['summary']['completed']['delivered'] = $delivered_shipments;
                } else {
                    $information['summary']['completed']['pending'] = $total_shipments;
                }

                $information['summary']['requests'] = array();
                $information['summary']['requests']['complains'] = 0;
                $information['summary']['requests']['service_requests'] = 0;
                $information['summary']['requests']['claims'] = 0;

                $information['deliveries'] = array();
                $delivery_note_shipments = $delivery_note->delivery_note_shipments->sortBy('ordering');
                foreach ($delivery_note_shipments as $delivery_note_shipment) {

                    $shipment_data = $delivery_note_shipment->shipment;
                    $shipment_id = $shipment_data->id;
                    $tracking_number = $shipment_data->tracking_number;
                    $consignee_name = $shipment_data->consignee_name;
                    $consignee_address = $shipment_data->consignee_address;
                    $booking_type = $shipment_data->booking_type_id;
                    $consignee_phone = $shipment_data->consignee_phone_number_1;
                    if ($shipment_data->consignee_phone_number_2 != null) {
                        $consignee_phone .= ' / ' . $shipment_data->consignee_phone_number_2;
                    }
                    $cod_amount = number_format($shipment_data->amount);
                    $special_instructions = $shipment_data->special_instructions;
                    $remarks = '';
                    $journey = ShipmentsJourney::where('shipment_id', $shipment_data->id)->where('remarks', '!=', null)->select('remarks');
                    if ($journey->exists()) {
                        $journey = $journey->orderBy('id', 'DESC')->first();
                        $remarks = $journey->remarks;
                    }
                    $rider_delivery = RiderDelivery::where('delivery_note_id', $delivery_note->id)->where('shipment_id', $shipment_id);

                    if ($delivery_note_shipment->status == 1) {
                        $status = 3;
                    } else if ($delivery_note_shipment->status > 1) {
                        $status = 2;
                    } else {
                        $status = 1;
                    }

                    $deliveries = array();
                    $deliveries['shipment_id'] = $shipment_id;
                    $deliveries['tracking_number'] = $tracking_number;
                    $deliveries['consignee_name'] = $consignee_name;
                    $deliveries['consignee_address'] = $consignee_address;
                    $deliveries['consignee_phone'] = $consignee_phone;
                    $deliveries['cod_amount'] = $cod_amount;
                    $deliveries['special_instructions'] = $special_instructions;
                    $deliveries['booking_type'] = $booking_type;
                    $deliveries['remarks'] = $remarks;
                    $deliveries['latitude'] = NULL;
                    $deliveries['longitude'] = NULL;
                    $deliveries['status'] = $status;
                    $shipment_location = ConsigneeShipmentLocation::where('shipment_id', $shipment_id);
                    if ($shipment_location->exists()) {
                        $shipment_location = $shipment_location->first();
                        $previous_location_id = $shipment_location->previous_location_id;
                        $location = ConsigneeLocation::find($previous_location_id);
                        $deliveries['latitude'] = $location->lat;
                        $deliveries['longitude'] = $location->long;
                    }


                    if (CrmRequest::where('shipment_id', $shipment_data->id)->whereIn('case_nature_id', [1, 2, 4])->whereNotIn('status_id', [3, 4])->exists()) {
                        $deliveries['request'] = array();
                        $crm_request = CrmRequest::where('shipment_id', $shipment_data->id)->where('case_nature_id', '!=', 3)->latest()->first();
                        $deliveries['request']['id'] = $crm_request->id;

                        if ($crm_request->case_nature_id == 1) {
                            $information['summary']['requests']['complains']++;
                            $deliveries['ordering'] = 1;
                            $deliveries['request']['type'] = 1;
                        } else if ($crm_request->case_nature_id == 2) {
                            $information['summary']['requests']['service_requests']++;
                            $deliveries['ordering'] = 2;
                            $deliveries['request']['type'] = 2;
                        } else if ($crm_request->case_nature_id == 4) {
                            $information['summary']['requests']['claims']++;
                            $deliveries['ordering'] = 3;
                            $deliveries['request']['type'] = 4;
                        } else {
                            $deliveries['ordering'] = 4;
                        }

                        $deliveries['request']['added_date'] = Carbon::parse($crm_request->created_at)->format('Y-m-d H:i:s');
                        $deliveries['request']['description'] = $crm_request->description;
                        $deliveries['request']['comments'] = array();

                        $crm_request_comments = $crm_request->comments->where('comment_type', 2);
                        if (count($crm_request_comments) > 0) {
                            foreach ($crm_request_comments as $crm_request_comment) {
                                if ($crm_request_comment->comment_by == 0) {
                                    $comments = array();
                                    $comments['name'] = Admin::find($crm_request_comment->comment_by_id)->name;
                                    $comments['comment'] = $crm_request_comment->comment;
                                    $comments['type'] = 'Admin';
                                    $comments['commented_at'] = Carbon::parse($crm_request_comment->created_at)->format('Y-m-d H:i:s');
                                    $deliveries['request']['comments'][] = $comments;
                                } else if ($crm_request_comment->comment_by == 2) {
                                    $comments = array();
                                    $comments['name'] = Rider::find($crm_request_comment->comment_by_id)->name;
                                    $comments['comment'] = $crm_request_comment->comment;
                                    $comments['type'] = 'Rider';
                                    $comments['commented_at'] = Carbon::parse($crm_request_comment->created_at)->format('Y-m-d H:i:s');
                                    $deliveries['request']['comments'][] = $comments;
                                }
                            }
                        }

                    } else {
                        $deliveries['ordering'] = 4;
                    }

                    $information['deliveries'][] = $deliveries;
                }

                usort($information['deliveries'], function ($a, $b) {
                    return $a['ordering'] <=> $b['ordering'];
                });

                $nodes[] = $information;
            }
            return response()->json(['status' => 0, 'message' => 'Delivery Note Is Assigned', 'information' => $nodes]);
        }
        return response()->json(['status' => 0, 'message' => 'No Delivery Note Assigned']);
    }

    public function return_summary_multiple_v2(Request $request)
    {
        $rider_id = $request->rider_id;

        $return_notes = ReturnNote::where('rider_id', $rider_id)->where('status', 0)->where('shipments_count', '!=', 0);

        if ($return_notes->exists()) {
            $return_notes = $return_notes->get();

            $nodes = array();

            foreach ($return_notes as $return_note) {
                $information = array();

                $information['return_note_id'] = $return_note->id;
                $information['assigned_date'] = $return_note->created_at->toDateTimeString();
                $information['no_of_parcels'] = $return_note->shipments_count;
                $information['summary'] = array();
                $total_shipments = $return_note->shipments_count;
                $information['summary']['deliveries'] = $total_shipments;
                $information['summary']['completed'] = array();

                $information['summary']['completed']['pending'] = $return_note->return_note_shipments->where('status', 0)->count('shipment_id');
                $information['summary']['completed']['undelivered'] = $return_note->return_note_shipments->where('status', 2)->count('shipment_id');
                $information['summary']['completed']['delivered'] = $return_note->return_note_shipments->where('status', 1)->count('shipment_id');

                $information['summary']['requests'] = array();
                $information['summary']['requests']['complains'] = 0;
                $information['summary']['requests']['service_requests'] = 0;
                $information['summary']['requests']['claims'] = 0;

                $information['return_deliveries'] = array();
                $return_note_shipments = $return_note->return_note_shipments->where('status', 0);
                foreach ($return_note_shipments as $return_note_shipment) {
                    $reason_mandatory_shippers = ReturnReasonMandatoryShipper::pluck('shipper_id')->toArray();
                    $shipment_data = $return_note_shipment->shipment;
                    if($shipment_data->return_address_id){
                        $pickup_address = $shipment_data->return_address;
                        $address_id = $shipment_data->return_address_id;
                    }else{
                        $pickup_address = $shipment_data->pickup_address;
                        $address_id = $shipment_data->pickup_address_id;
                    }

                    $shipment_id = $shipment_data->id;
                    $tracking_number = $shipment_data->tracking_number;
                    $shipper_name = $pickup_address->user->name;
                    $shipper_id = $shipment_data->user_id;
                    $shipper_address_id = $address_id;
                    $shipper_poc = $pickup_address->poc;
                    $shipper_address = $pickup_address->pickup_address;
                    $shipper_phone = $pickup_address->phone;
                    $special_instructions = $shipment_data->special_instructions;
                    $remarks = '';
                    $journey = ShipmentsJourney::where('shipment_id', $shipment_data->id)->where('remarks', '!=', null)->select('remarks');
                    if ($journey->exists()) {
                        $journey = $journey->orderBy('id', 'DESC')->first();
                        $remarks = $journey->remarks;
                    }
                    $rider_return_deliveries = RiderReturnDelivery::where('return_note_id', $return_note->id)->where('shipment_id', $shipment_id);
                    if ($rider_return_deliveries->exists()) {
                        $rider_return_deliveries = $rider_return_deliveries->first();
                        if ($rider_return_deliveries->delivered_status == 0) {
                            $status = 3;
                        } else if ($rider_return_deliveries->delivered_status == 1) {
                            $status = 2;
                        }
                    } else {
                        $status = 1;
                    }
                    $reason_mandatory = (in_array($shipper_id, $reason_mandatory_shippers)) ? 1 : 0;

                    $deliveries = array();
                    $deliveries['shipment_id'] = $shipment_id;
                    $deliveries['tracking_number'] = $tracking_number;
                    $deliveries['consignee_name'] = $shipper_name;
                    $deliveries['consignee_id'] = $shipper_id;
                    $deliveries['consignee_address_id'] = $shipper_address_id;
                    $deliveries['consignee_poc'] = $shipper_poc;
                    $deliveries['consignee_address'] = $shipper_address;
                    $deliveries['consignee_phone'] = $shipper_phone;
                    $deliveries['special_instructions'] = $special_instructions;
                    $deliveries['remarks'] = $remarks;
                    $deliveries['reason_mandatory'] = $reason_mandatory;
                    $deliveries['latitude'] = NULL;
                    $deliveries['longitude'] = NULL;
                    $deliveries['status'] = $status;
                    $shipper_lat = $pickup_address->location_latitude;
                    $shipper_long = $pickup_address->location_longitude;
                    if ($shipper_lat != null && $shipper_long != null) {
                        $deliveries['latitude'] = $shipper_lat;
                        $deliveries['longitude'] = $shipper_long;
                    }
                    if (CrmRequest::where('shipment_id', $shipment_data->id)->whereIn('case_nature_id', [1, 2, 4])->whereNotIn('status_id', [3, 4])->exists()) {
                        $deliveries['request'] = array();
                        $crm_request = CrmRequest::where('shipment_id', $shipment_data->id)->where('case_nature_id', '!=', 3)->latest()->first();
                        $deliveries['request']['id'] = $crm_request->id;

                        if ($crm_request->case_nature_id == 1) {
                            $information['summary']['requests']['complains']++;
                            $deliveries['ordering'] = 1;
                            $deliveries['request']['type'] = 1;
                        } else if ($crm_request->case_nature_id == 2) {
                            $information['summary']['requests']['service_requests']++;
                            $deliveries['ordering'] = 2;
                            $deliveries['request']['type'] = 2;
                        } else if ($crm_request->case_nature_id == 4) {
                            $information['summary']['requests']['claims']++;
                            $deliveries['ordering'] = 3;
                            $deliveries['request']['type'] = 4;
                        } else {
                            $deliveries['ordering'] = 4;
                        }

                        $deliveries['request']['added_date'] = Carbon::parse($crm_request->created_at)->format('Y-m-d H:i:s');
                        $deliveries['request']['description'] = $crm_request->description;
                        $deliveries['request']['comments'] = array();

                        $crm_request_comments = $crm_request->comments->where('comment_type', 2);
                        if (count($crm_request_comments) > 0) {
                            foreach ($crm_request_comments as $crm_request_comment) {
                                if ($crm_request_comment->comment_by == 0) {
                                    $comments = array();
                                    $comments['name'] = Admin::find($crm_request_comment->comment_by_id)->name;
                                    $comments['comment'] = $crm_request_comment->comment;
                                    $comments['type'] = 'Admin';
                                    $comments['commented_at'] = Carbon::parse($crm_request_comment->created_at)->format('Y-m-d H:i:s');
                                    $deliveries['request']['comments'][] = $comments;
                                } else if ($crm_request_comment->comment_by == 2) {
                                    $comments = array();
                                    $comments['name'] = Rider::find($crm_request_comment->comment_by_id)->name;
                                    $comments['comment'] = $crm_request_comment->comment;
                                    $comments['type'] = 'Rider';
                                    $comments['commented_at'] = Carbon::parse($crm_request_comment->created_at)->format('Y-m-d H:i:s');
                                    $deliveries['request']['comments'][] = $comments;
                                }
                            }
                        }

                    } else {
                        $deliveries['ordering'] = 4;
                    }

                    $information['return_deliveries'][] = $deliveries;
                }

                usort($information['return_deliveries'], function ($a, $b) {
                    return $a['ordering'] <=> $b['ordering'];
                });

                $nodes[] = $information;
            }
            return response()->json(['status' => 0, 'message' => 'Return Delivery Note Is Assigned', 'information' => $nodes]);
        }
        return response()->json(['status' => 0, 'message' => 'No Return Delivery Note Assigned']);
    }

    public function rider_profile(Request $request)
    {
        $rider_id = $request->rider_id;
        $rider_profile = Rider::join('rider_categories as rc', 'rc.id', '=', 'riders.rider_category_id')
            ->join('cities as c', 'c.id', '=', 'riders.city_id')
            ->join('cities as h', 'h.id', '=', 'c.hub_id')
            ->leftjoin('employees as e', 'e.trax_id', '=', 'riders.trax_id')
            ->leftjoin('employee_blood_groups as bg', 'bg.id', '=', 'e.blood_group')
            ->select('riders.trax_id as trax_id', 'c.name as city_name', 'h.name as hub', 'riders.name as rider_name', 'riders.phone as phone', 'riders.cnic as cnic', 'riders.address as address', 'rc.name as category', 'bg.name as blood_group', 'e.emergency_contact as emergency_contact_no', 'e.emergency_contact_person as emergency_contact_person')
            ->where('riders.id', $rider_id);
        if ($rider_profile->exists()) {
            $rider_profile = $rider_profile->get();
            return response()->json(['status' => 0, 'rider' => $rider_profile]);
        } else {
            return response()->json(['status' => 1, 'message' => "Rider Profile Not Found"]);
        }
    }

    public function mark_attendance(Request $request)
    {
        return response()->json(['status' => 1, 'message' => 'Please Update Your Bolt App']);
        $rules = [
            'attendance_date' => ['required'],
            'latitude' => ['required', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'longitude' => ['required', 'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'],
            'action' => ['required', 'integer', 'digits_between:1,10', 'exists:attendance_actions,id'],
        ];

        $rider_id = $request->rider_id;
        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $location_status = 0;
            $reporting_location = ReportingLocation::join('employees as e', 'reporting_locations.id', 'e.reporting_location_id')
                ->join('riders as r', 'e.id', 'r.employee_id')
                ->where('r.id', $rider_id);
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

            $rider_attendance = EmployeeAttendance::where('employee_id', $rider_id)
                ->whereDate('attendance_date', $attendance_date)
                ->where('employee_type', 2);
            $rider_attendance_action = new EmployeeAttendanceActionLog();
            if ($rider_attendance->exists()) {
                $rider_attendance = $rider_attendance->first();
            } else {
                $rider_attendance = new EmployeeAttendance();
                $rider_attendance->employee_id = $rider_id;
                $rider_attendance->employee_type = 2;
                $rider_attendance->attendance_date = $attendance_date;
            }
            if ($request->action == 1) {
                $rider_attendance->clock_in = $attendance_time;
                $rider_attendance->clock_in_datetime = $attendance_datetime;
                $rider_attendance->clock_in_latitude = $request->latitude;
                $rider_attendance->clock_in_longitude = $request->longitude;
                $rider_attendance->clock_in_location = $location_status;
                $rider_attendance->save();

                $rider_attendance_action->employee_id = $rider_id;
                $rider_attendance_action->employee_type = 2;
                $rider_attendance_action->action_id = $request->action;
                $rider_attendance_action->attendance_date = $attendance_date;
                $rider_attendance_action->action_date = $attendance_datetime;
                $rider_attendance_action->latitude = $request->latitude;
                $rider_attendance_action->longitude = $request->longitude;
                $rider_attendance_action->location_status = $location_status;
                $rider_attendance_action->save();

                return response()->json(['status' => 0, 'message' => 'Clocked-In Successfully', 'response' => $rider_attendance_action]);
            } elseif ($request->action == 2) {
                $rider_attendance->clock_out = $attendance_time;
                $rider_attendance->clock_out_datetime = $attendance_datetime;
                $rider_attendance->clock_out_latitude = $request->latitude;
                $rider_attendance->clock_out_longitude = $request->longitude;
                $rider_attendance->clock_out_location = $location_status;
                $rider_attendance->save();

                $rider_attendance_action->employee_id = $rider_id;
                $rider_attendance_action->employee_type = 2;
                $rider_attendance_action->action_id = $request->action;
                $rider_attendance_action->attendance_date = $attendance_date;
                $rider_attendance_action->action_date = $attendance_datetime;
                $rider_attendance_action->latitude = $request->latitude;
                $rider_attendance_action->longitude = $request->longitude;
                $rider_attendance_action->location_status = $location_status;
                $rider_attendance_action->save();
                return response()->json(['status' => 0, 'message' => 'Clocked-Out Successfully', 'response' => $rider_attendance_action]);
            }

            return response()->json(['status' => 1, 'message' => 'Failed']);
        }

    }

    public function attendance_details(Request $request)
    {
        return response()->json(['status' => 1, 'message' => 'Please Update Your Bolt App']);
        $rules = [
            'attendance_date' => ['required']
        ];
        $rider_id = $request->rider_id;
        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $rider_attendance_action = EmployeeAttendanceActionLog::where('employee_id', $rider_id)
                ->whereDate('action_date', $request->attendance_date)
                ->where('employee_type', 2)
                ->select('action_id', 'action_date', 'latitude', 'longitude', 'location_status')
                ->orderBy('action_date', 'ASC');
            if ($rider_attendance_action->exists()) {
                $rider_attendance_action = $rider_attendance_action->get();
                return response()->json(['status' => 0, 'attendance_details' => $rider_attendance_action]);
            }
            return response()->json(['status' => 0, 'attendance_details' => []]);
        }
    }

    public function attendance_history(Request $request)
    {
        return response()->json(['status' => 1, 'message' => 'Please Update Your Bolt App']);
        $rules = [
            'from_date' => ['required'],
            'to_date' => ['nullable']
        ];
        $rider_id = $request->rider_id;
        $from_date = $request->get('from_date');
        $to_date = $request->get('to_date');
        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {

            $rider_attendance = EmployeeAttendance::where('rider_id', $rider_id)
                ->orderBy('attendance_date', 'ASC');

            if ($to_date != null) {
                $rider_attendance = $rider_attendance->whereBetween('attendance_date', [$from_date, $to_date]);
            } else {
                $rider_attendance = $rider_attendance->whereDate('attendance_date', $from_date);
            }

            if ($rider_attendance->exists()) {
                $rider_attendance = $rider_attendance->get();
                return response()->json(['status' => 0, 'history_details' => $rider_attendance]);
            }
            return response()->json(['status' => 1, 'message' => "No Details Found"]);
        }
    }

    public function rider_ticker_images(Request $request)
    {
        $rider_ticker_images = RiderTickerImage::orderBy('id', 'ASC');
        if ($rider_ticker_images->exists()) {
            $rider_ticker_images = $rider_ticker_images->get();
            return response()->json(['status' => 0, 'images' => $rider_ticker_images]);
        } else {
            return response()->json(['status' => 1, 'message' => 'No Images Found']);
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
        $shifts = EmployeeShift::where('id' ,'!=', 1)->select('id', 'name', 'start_time', 'end_time')->get();
        $category = RiderCategory::all();
        $main_category = RiderMainCategory::all();
        $employee_nature = EmployeeNature::select('id', 'name')->get();
        $replacement_employees = Employee::select('id', 'trax_id', 'name')->where('employee_type_id', 2)->whereNotNull('trax_id')->get();
        $shift_data = array();
        foreach($shifts as $shift){
            $datum = array();
            $datum['id'] = $shift->id;
            $datum['name'] = $shift->name. ' ('.$shift->start_time.' - '. $shift->end_time.') ';
            $shift_data[] = $datum;
        }

        $replacement_employee_data = array();
        foreach($replacement_employees as $replacement_employee){
            $datum = array();
            $datum['id'] = $replacement_employee->id;
            $datum['name'] = $replacement_employee->trax_id.' | '.$replacement_employee->name;
            $replacement_employee_data[] = $datum;
        }
        return response()->json(['status' => 0, "cities" => $cities, "designation" => $designation, "domicile" => $domicile, "marital_status" => $marital_status, "nationality" => $nationality, "religion" => $religion, "gender" => $gender, "zone" => $zone, "department" => $department, "hub" => $hub, "blood_group" => $blood_group, "relationships" => $relationships, 'banks' => $banks, 'rider_type' => $rider_type, 'staff_categories' => $staff_categories, 'shifts' => $shift_data, 'rider_sub_category' => $category, 'rider_main_category' => $main_category, 'employee_nature' => $employee_nature, 'replacement_employees' => $replacement_employee_data]);
    }

    public function rider_signup_v2(Request $request)
    {
        return response()->json(['status' => 1, 'message' => 'Please Update Your Bolt App']);
        if ($request->isMethod('post')) {
            $rules = [
                'rider_type_id' => ['nullable', 'integer', 'digits_between:1,10', 'exists:rider_types,id'],
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
                $rider_request = RiderRequest::where('phone_no', $request->input('phone_number'))
                    ->orWhere('cnic', $request->input('cnic_no'));

                $employee = Employee::where('employee_type_id', 2)
                    ->where('phone_number', $request->input('phone_number'))
                    ->orWhere('cnic', $request->input('cnic_no'));

                //Check RiderRequest Already Exist
                if ($rider_request->exists()) {
                    $rider_request = $rider_request->first();
                    if ($rider_request->phone_no == $request->input('phone_number') && $rider_request->cnic == $request->input('cnic_no')) {
                        $message = "Phone Number & CNIC Already Exists";

                    } else if ($rider_request->phone_no == $request->input('phone_number')) {
                        $message = "Phone Number Already Exist";

                    } else if ($rider_request->cnic == $request->input('cnic_no')) {
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
                } //Check Rider Already Exist
                else {
                    $rider = Rider::where('phone', $request->input('phone_number'))->orWhere('cnic', $request->input('cnic_no'));

                    if ($rider->exists()) {
                        $rider = $rider->first();
                        if ($rider->phone == $request->phone_number && $rider->cnic == $request->input('cnic_no')) {
                            $message = "Phone Number & CNIC Already Exists";

                        } else if ($rider->phone == $request->phone_number) {
                            $message = "Phone Number Already Exist";

                        } else if ($rider->cnic == $request->input('cnic_no')) {
                            $message = "CNIC Already Exist";
                        }

                    } else {
                        try {
                            $rider_request = new RiderRequest();
                            $rider_request->name = $request->name;
                            $rider_request->cnic = $request->cnic_no;
                            $rider_request->phone_no = $request->phone_number;
                            $rider_request->pin = $request->pin;
                            $rider_request->city_id = $request->city_id;
                            $rider_request->rider_type_id = $request->rider_type_id;
                            $rider_request->save();

                            $employee_request = new Employee();
                            $employee_request->name = $request->name;
                            $employee_request->employee_gender_id = $request->employee_gender_id;
                            $employee_request->city_id = $request->city_id;
                            $employee_request->cnic = $request->cnic_no;
                            $employee_request->phone_number = $request->phone_number;
                            $employee_request->employee_type_id = 2;
                            $employee_request->rider_request_id = $rider_request->id;
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
            }
        } else {
            $message = 'Post Method is Required';
        }
        $response['message'] = $message;
        return response()->json($response);
    }

    public function rider_attachments_store(Request $request)
    {
        return response()->json(['status' => 1, 'message' => 'Please Update Your Bolt App']);
        $rules = [
            //Attachments
            'employee_id' => ['required', 'integer', 'digits_between:1,10', 'exists:employees,id'],
            'cv' => ['mimes:png,jpeg,jpg,pdf,doc,docx'],
            'academic_credentials' => ['mimes:png,jpeg,jpg,pdf,doc,docx'],
            'cnic' => ['mimes:png,jpeg,jpg,pdf,doc,docx'],
            'photo' => ['mimes:png,jpeg,jpg,pdf,doc,docx'],
            'experience_certificates' => ['mimes:png,jpeg,jpg,pdf,doc,docx'],
            'pay_slip' => ['mimes:png,jpeg,jpg,pdf,doc,docx'],
            'nikkah_nama' => ['mimes:png,jpeg,jpg,pdf,doc,docx'],
            'cnic_spouse' => ['mimes:png,jpeg,jpg,pdf,doc,docx'],
            'bform' => ['mimes:png,jpeg,jpg,pdf,doc,docx'],
            'cnic_nominee' => ['mimes:png,jpeg,jpg,pdf,doc,docx'],
            'utility_bill' => ['mimes:png,jpeg,jpg,pdf,doc,docx'],
            'affidavit' => ['mimes:png,jpeg,jpg,pdf,doc,docx'],
            'cheque' => ['mimes:png,jpeg,jpg,pdf,doc,docx'],
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
                $directory = 'employee_directory/employee_' . 