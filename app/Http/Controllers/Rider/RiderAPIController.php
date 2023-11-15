<?php

namespace App\Http\Controllers\Rider;

use DB;
use Validator;
use Carbon\Carbon;
use GuzzleHttp\Client;
use App\Http\Models\City;
use App\Http\Models\Zone;
use App\RiderLocationLog;
use App\Http\Models\Rider;
use App\Http\Models\Route;
use App\RiderMainCategory;
use App\Http\Models\Product;
use Illuminate\Http\Request;
use App\Http\Models\Shipment;
use App\Http\Models\BanksList;
use App\RiderWiseDeliveryNote;
use App\Http\Models\PayslipPdf;
use App\Http\Models\PickupNote;
use App\Jobs\LastMileAppReport;
use App\Http\Models\Admin\Admin;
use App\Http\Models\HR\Employee;
use App\Http\Models\RiderPickup;
use App\Http\Models\ShipmentOtp;
use App\RiderDeliveryNoteStatus;
use App\BoltUndeliveredReasonMap;
use App\Http\Models\CityDelivery;
use App\Http\Models\HR\LeaveType;
use App\Http\Models\ShipmentItem;
use App\Http\Models\EmployeeShift;
//use App\Http\Models\Admin\OneLink\OneLinkPaymentTransaction;
use App\Http\Models\PickupRequest;
use App\Http\Models\RiderCategory;
use App\Http\Models\RiderDelivery;
use App\Http\Models\ShipmentPiece;
use App\Http\Models\CRM\CrmRequest;
use App\Http\Models\ShipmentDetail;
use App\Http\Models\ShipmentStatus;
use App\Http\Controllers\Controller;
use App\Http\Models\Admin\RiderType;
use App\Http\Models\AppNotification;
use App\Http\Models\CRM\CrmComments;
use App\Http\Models\ShipmentOpenBox;
use App\ReturnDeliveredToShipperSms;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Models\Admin\ReturnNote;
use App\Http\Models\BusinessCategory;
use App\Http\Models\HR\EmployeeLeave;
use App\Http\Models\HR\StaffCategory;
use App\Http\Models\MisroutedHistory;
use App\Http\Models\ShipmentsJourney;
use App\RiderWiseDeliveryNoteSummary;
use App\Http\Models\ConsigneeLocation;
use App\Http\Models\Handover\Handover;
use App\Http\Models\HR\EmployeeGender;
use App\http\Models\HR\EmployeeNature;
use App\Http\Models\PickupNoteRequest;
use App\Http\Models\ReportingLocation;
use App\Http\Models\Rider\RiderRemark;
use App\RiderWiseDeliveryNoteShipment;
use Barryvdh\Snappy\Facades\SnappyPdf;
use App\Http\Models\Admin\DeliveryNote;
use App\Http\Models\HR\EmployeePayslip;
use App\Http\Models\Rider\RiderRequest;
use Illuminate\Support\Facades\Storage;
use App\Http\Models\EmployeeDeviceToken;
use App\Http\Models\HR\EmployeeDomicile;
use App\Http\Models\HR\EmployeeReligion;
use App\Jobs\ProcessAgentCallMonitoring;
use App\RiderAssignedHubForDeliveryNote;
use phpDocumentor\Reflection\Types\This;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Admin\ShipmentOnHold;
use App\Http\Models\DeliveryNoteRequests;
use App\Http\Models\RiderPickupActionLog;
use App\Http\Models\Admin\AdminDepartment;
use App\Http\Models\HR\EmployeeAttachment;
use App\Http\Models\HR\EmployeeBloodGroup;
use App\Http\Models\Rider\RidersIncentive;
use App\Http\Models\RiderPickupInvalidLog;
use App\Http\Models\V2Pickup\V2PickupNote;
use App\Http\Models\WarehouseStockRequest;
use GuzzleHttp\Exception\RequestException;
use App\Http\Models\Admin\DeliveryRelation;
use App\Http\Models\Admin\RetailPickupNote;
use App\Http\Models\HR\EmployeeDesignation;
use App\Http\Models\HR\EmployeeNationality;
use App\Http\Models\Rider\RiderTickerImage;
use App\Http\Models\V2Pickup\V2RiderPickup;
use App\Http\Traits\LastMileAppReportTrait;
use App\Http\Controllers\AdminAPIController;
use App\Http\Models\Admin\Retail\RetailUser;
use App\Http\Models\HR\EmployeeRelationship;
use App\Http\Models\ReturnAssignedShipments;
use App\Http\Models\ShipmentOtpVerification;
use App\Http\Models\Admin\ReturnNoteShipment;
use App\Http\Models\Admin\TraxPayTransaction;
use App\Http\Models\HR\EmployeeMaritalStatus;
use App\Http\Models\PackagingMaterialRequest;
use App\Http\Models\Shipper\UserShippingInfo;
use App\Http\Models\V2Pickup\V2PickupRequest;
use App\Http\Models\Admin\RiderCategoryByPass;
use App\Http\Models\ConsigneeShipmentLocation;
use App\Http\Models\Rider\RiderReturnDelivery;
use App\Jobs\ProcessOneLinkExpireDeliveryNote;
use App\Jobs\ProcessTraxPayExpireDeliveryNote;
use App\Http\Models\Admin\DeliveryNoteShipment;
use App\Http\Models\Admin\Retail\RetailTraxBox;
use App\Http\Models\Handover\HandoverShipments;
use App\Http\Models\HR\EmployeeBankInformation;
use App\Http\Models\ReturnAssignedShipmentLogs;
use App\Http\Models\Rider\RiderIncentivePickup;
use App\Http\Models\Admin\FintechPaymentDetails;
use App\Http\Models\Admin\Retail\RetailShipment;
use App\Http\Models\EmployeeNotificationHistory;
use App\Http\Models\Rider\RiderReturnNoteStatus;
use App\Http\Models\ShipmentDistributionProduct;
use App\Jobs\ProcessOneLinkDeliveryNoteShipment;
use App\Http\Controllers\NotificationsController;
use App\Http\Models\Rider\RiderDeliveryActionLog;
use App\Http\Models\Rider\RiderIncentiveDelivery;
use App\Http\Models\Rider\RiderReturnNoteRequest;
use App\Http\Models\V2Pickup\V2PickupNoteRequest;
use App\Http\Models\Admin\ChangeShipmentAmountLog;
use App\Http\Models\Admin\Retail\RetailTraxCenter;
use App\Http\Models\HR\EmployeeEmployementHistory;
use App\Http\Models\HR\EmployeeMedicalInformation;
use App\Http\Models\InterceptReBookRequestHistory;
use App\Http\Controllers\Admins\DeliveryController;
use App\Http\Models\Admin\Retail\RetailCashDeposit;
use App\Http\Models\Admin\Retail\RetailPaymentMode;
use App\Http\Models\Admin\Retail\RetailShipperInfo;
use App\Http\Models\Rider\RiderDeliveryNoteRequest;
use App\Http\Models\ShipmentReplacementParcelImage;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Models\Admin\Retail\RetailShippingMode;
use App\Http\Models\HR\EmployeeAttendanceAdjustment;
use App\Http\Models\V2Pickup\V2PickupRequestAttempt;
use App\Http\Models\V2Pickup\V2RiderPickupActionLog;
use App\Http\Controllers\UndeliveredReasonController;
use App\Http\Models\HR\EmployeeEducationalBackground;
use App\Http\Models\V2Pickup\V2PickupRequestShipment;
use App\Http\Controllers\EmployeeAttendanceController;
use App\Http\Controllers\Admins\AdminFinanceController;
use App\Http\Controllers\Admins\AdminPickupsController;
use App\http\Models\Admin\ReturnReasonMandatoryShipper;
use App\Http\Models\Rider\RiderReturnDeliveryActionLog;
use App\Http\Models\Admin\Attendance\EmployeeAttendance;
use App\Http\Models\Rider\RiderReturnNoteRequestShipment;
use App\Http\Controllers\Admins\ShipmentChargesController;
use App\Http\Controllers\ShipmentOpenBoxJourneyController;
use App\Http\Controllers\ShipmentScanningJourneyController;
use App\Http\Models\Admin\Retail\RetailCashDepositShipment;
use App\Http\Models\Rider\RiderDeliveryNoteRequestShipment;
use App\Http\Controllers\Retail\RetailShipmentBookController;
use App\Http\Controllers\Admins\CheckDisputeShipmentsController;
use App\Http\Controllers\Retail\RetailRatesCalculationController;
use App\Http\Models\Admin\Attendance\EmployeeAttendanceActionLog;
use App\Http\Models\Admin\OneLink\OneLinkOutForDeliveryShipmentPayment;
use App\Http\Controllers\Admins\Handover\HandoverShipmentJourneyController;

class RiderAPIController extends Controller
{
    use LastMileAppReportTrait;

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

    static public function rider_pickup_invalid_logs($rider_id, $pickup_request_id, $pickup_note_id, $shipment_id, $status_id)
    {
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
        for ($date = $start_date->copy(); $date->lte($end_date); $date->addDay()) {
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
        } else {
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
                if (DeliveryNoteShipment::join('delivery_notes as dn', 'delivery_note_shipments.delivery_note_id', 'dn.id')->where('delivery_note_id', $request->delivery_note_id)->where('shipment_id', $request->shipment_id)->where('dn.rider_id', $rider_id)->exists()) { {

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
            $rider_deliveries = DeliveryNote::join('cities AS oc', 'delivery_notes.hub_id', '=', 'oc.id')
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
                    if (ReturnNoteShipment::join('return_notes as rn', 'return_note_shipments.return_note_id', 'rn.id')->where('return_note_id', $request->return_note_id)->where('shipment_id', $request->shipment_id)->where('rn.rider_id', $rider_id)->exists()) { {

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
                $shipments = 0;
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
                    $shipments = $shipments + $pickup_request->booked;
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
                                $pickup_note->shipments = $pickup_note->shipments - $pickup_request->booked;
                                $pickup_note->save();
                            }
                        }
                        $pickups++;
                        $shipments = $shipments + $pickup_request->booked;
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
                            $pickup_note->shipments += $shipments;

                            $pickup_note->save();
                        }
                        $pickup_note_id = $pickup_note->id;
                    } else {
                        $pickup_note = new V2PickupNote();

                        $pickup_note->rider_id = $rider_id;
                        $pickup_note->pickups = $pickups;
                        $pickup_note->shipments = $shipments;
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

            $delivery_notes = DeliveryNote::join('riders', 'delivery_notes.rider_id', '=', 'riders.id')
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
                            ->where(
                                'shipments_journey.id',
                                '=',
                                DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = delivery_note_shipments.shipment_id and reference_1_id = delivery_note_shipments.delivery_note_id and shipments_journey.shipper_status_id != 5 and shipments_journey.rider_id is not null)')
                            );
                    })
                    ->join('rider_deliveries', function ($join) {
                        $join->on('delivery_note_shipments.shipment_id', '=', 'rider_deliveries.shipment_id')
                            ->where(
                                'rider_deliveries.id',
                                '=',
                                DB::raw('(select max(id) from rider_deliveries as rrd where rrd.shipment_id = delivery_note_shipments.shipment_id AND rrd.delivery_note_id = delivery_note_shipments.delivery_note_id)')
                            );
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
                            ->where(
                                'shipments_journey.id',
                                '=',
                                DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = return_note_shipments.shipment_id and reference_1_id = return_note_shipments.return_note_id and shipments_journey.shipper_status_id != 23 and shipments_journey.rider_id is not null)')
                            );
                    })
                    ->join('rider_return_deliveries', function ($join) {
                        $join->on('return_note_shipments.shipment_id', '=', 'rider_return_deliveries.shipment_id')
                            ->where(
                                'rider_return_deliveries.id',
                                '=',
                                DB::raw('(select max(id) from rider_return_deliveries as rrd where rrd.shipment_id = return_note_shipments.shipment_id AND rrd.return_note_id = return_note_shipments.return_note_id)')
                            );
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
        $from_date = explode(" ", $request->get('from_date'))[0] . " 06:00:00";
        $to_date = $request->get('to_date');
        if ($to_date == null) {
            $to_date = strval(Carbon::parse($from_date)->addDay());
        } else {
            $to = explode(" ", $request->get('to_date'))[0] . " 06:00:00";
            $to_date = strval(Carbon::parse($to)->addDay());
        }
        $pickup_request_id = $request->get('pickup_request_id');
        $pickup_note_id = $request->get('pickup_note_id');

        if ($from_date == null && $to_date == null && $pickup_request_id == null && $pickup_note_id == null) {
            return response()->json(["status" => 1, "message" => "Please provide parameter(s)"]);
        } else {
            $rider_pickups = V2PickupNote::join('v2_pickup_note_requests as pnr', 'pnr.pickup_note_id', '=', 'v2_pickup_notes.id')
                ->join('v2_pickup_requests as pr', 'pr.id', '=', 'pnr.pickup_request_id')
                ->leftjoin('v2_pickup_request_shipments as prs', 'prs.pickup_request_id', '=', 'pr.id')
                ->leftjoin('shipments_journey as total_s', function ($join) {
                    $join->on('total_s.shipment_id', '=', 'prs.shipment_id')
                        ->where(
                            'total_s.id',
                            '=',
                            DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = prs.shipment_id and shipments_journey.reference_1_id = pr.id and shipments_journey.shipper_status_id = 53 and verification = 1)')
                        );
                })
                ->select('v2_pickup_notes.id as pickup_note_id', DB::raw('(SELECT SUM(vprs.booked) FROM v2_pickup_note_requests AS vpnr LEFT JOIN v2_pickup_requests AS vprs ON vprs.id = vpnr.pickup_request_id WHERE vpnr.pickup_note_id = v2_pickup_notes.id ) as total_shipments'), DB::raw('count(total_s.id) as rider_picked'), DB::raw('(SELECT COUNT(vpnr2.shipment_id) FROM v2_pickup_received_shipments AS vpnr2 WHERE vpnr2.pickup_note_id = v2_pickup_notes.id AND vpnr2.pickup_note_id is not null and vpnr2.created_at between "' . $from_date . '" and "' . $to_date . '") as arrived'), 'v2_pickup_notes.created_at as created_at')
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
                            if ($request->has('otp_entered') && $request->status_reason_id == 8) {
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
                                if ($request->shipper_status_id != 7) {
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
                    if ($shipment_data->return_address_id) {
                        $pickup_address = $shipment_data->return_address;
                        $address_id = $shipment_data->return_address_id;
                    } else {
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
        $shifts = EmployeeShift::where('id', '!=', 1)->select('id', 'name', 'start_time', 'end_time')->get();
        $category = RiderCategory::all();
        $main_category = RiderMainCategory::all();
        $employee_nature = EmployeeNature::select('id', 'name')->get();
        $replacement_employees = Employee::select('id', 'trax_id', 'name')->where('employee_type_id', 2)->whereNotNull('trax_id')->get();
        $shift_data = array();
        foreach ($shifts as $shift) {
            $datum = array();
            $datum['id'] = $shift->id;
            $datum['name'] = $shift->name . ' (' . $shift->start_time . ' - ' . $shift->end_time . ') ';
            $shift_data[] = $datum;
        }

        $replacement_employee_data = array();
        foreach ($replacement_employees as $replacement_employee) {
            $datum = array();
            $datum['id'] = $replacement_employee->id;
            $datum['name'] = $replacement_employee->trax_id . ' | ' . $replacement_employee->name;
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

    public function login_v2(Request $request)
    {
        return response()->json(['status' => 1, 'message' => 'Please Update Your Bolt App']);
    }

    public function notification_history(Request $request)
    {
        $rider_id = $request->rider_id;
        $from_date = Carbon::now()->subDays(30)->format('Y-m-d 00:00:00');
        $to_date = Carbon::now()->format('Y-m-d 23:59:59');

        $notifiction_history = EmployeeNotificationHistory::where('employee_id', $rider_id)
            ->where('employee_type_id', 2)
            ->whereBetween('created_at', [$from_date, $to_date])
            ->orderBy('created_at', 'desc');
        if ($notifiction_history->exists()) {
            $notifiction_history = $notifiction_history->get();
            return response()->json(['status' => 0, 'data' => $notifiction_history]);
        }
        return response()->json(['status' => 1, 'message' => "Notification History Not Found"]);
    }

    public function return_shipment_undelivered_v3(Request $request)
    {
        $rules = [
            'added_at' => ['required'],
            'return_note_id' => ['required', 'integer', 'digits_between:1,10', 'exists:return_notes,id'],
            'start_location_latitude' => ['required', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'start_location_longitude' => ['required', 'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'],
            'actual_location_latitude' => ['required', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'actual_location_longitude' => ['required', 'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'],
            'shipment_id' => ['required', 'integer', 'digits_between:1,10', 'exists:shipments,id'],
            'remarks' => ['nullable', 'string', 'max:255'],
            'picture' => ['nullable', 'image'],
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
                if (!Shipment::where('id', $request->shipment_id)->where('shipper_status_id', 25)->exists()) {
                    if (ReturnNoteShipment::join('return_notes as rn', 'return_note_shipments.return_note_id', 'rn.id')->where('return_note_id', $request->return_note_id)->where('shipment_id', $request->shipment_id)->where('rn.rider_id', $rider_id)->exists()) {
                        $shipments = ShipmentsJourney::select('shipper_status_id', 'status_reason_id')
                            ->where('reference_1_id', $request->return_note_id)
                            ->where('shipment_id', $request->shipment_id)
                            ->where('shipper_status_id', 24)
                            ->where('status_reason_id', 62)
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
                            $rider_return_delivery->rider_status_id = 24;
                            $rider_return_delivery->rider_status_reason_id = 62;
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
                            if ($request->has('picture')) {
                                $picture_path = 'rider_return_delivery/' . $rider_return_delivery->id . '.png';
                                Storage::disk('public')->put($picture_path, file_get_contents($request->picture));
                                $rider_return_delivery->picture_path = $picture_path;
                                $rider_return_delivery->save();
                            }

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

                                $shipment->shipper_status_id = 24;
                                $shipment->consignee_status_id = 24;
                                $shipment->save();

                                $remarks = NULL;

                                if ($request->has('remarks')) {
                                    $remarks = $request->remarks;
                                }

                                ShipmentsJourneyController::add($shipment->id, 24, 24, 62, $remarks, NULL, NULL, $request->return_note_id, NULL, 1, NULL, $rider_id);
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
                        } else {
                            $message = 'Shipment is already marked as Undelivered';
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

    public function validate_cnic_phone_number(Request $request)
    {
        $rules = [
            'cnic_no' => ['required', 'regex:/^[0-9]{5}-[0-9]{7}-[0-9]{1}$/'],
            'phone_number' => ['required', 'regex:/^[0][0-9]{3}-[0-9]{7}$/'],
        ];
        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            $message = 'Error(s) in Input';
            return response()->json(['status' => 1, 'message' => $message, 'errors' => $validate->errors()]);
        } else {
            $rider_request = RiderRequest::where('phone_no', $request->input('phone_number'))
                ->orWhere('cnic', $request->input('cnic_no'));

            $employee = Employee::where('phone_number', $request->input('phone_number'))
                ->orWhere('cnic', $request->input('cnic_no'));

            $rider = Rider::where('phone', $request->input('phone_number'))
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
            } //Check Rider Already Exist
            else if ($rider->exists()) {
                $rider = $rider->first();
                if ($rider->phone == $request->phone_number && $rider->cnic == $request->input('cnic_no')) {
                    $message = "Phone Number & CNIC Already Exists";
                } else if ($rider->phone == $request->phone_number) {
                    $message = "Phone Number Already Exist";
                } else if ($rider->cnic == $request->input('cnic_no')) {
                    $message = "CNIC Already Exist";
                }
                return response()->json(['status' => 1, 'message' => $message]);
            }
            return response()->json(['status' => 0, 'message' => 'Success']);
        }
    }

    public function rider_attachments_store_v2(Request $request)
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

    public function rider_attachments_view(Request $request)
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

    public function rider_attachments_delete(Request $request)
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

    public function delivery_summary_multiple_v3(Request $request)
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
                    $shipment_status_count = ShipmentsJourney::where('shipment_id', $shipment_id)
                        ->where('shipper_status_id', 5)->count();
                    if ($shipment_status_count > 1) {
                        $deliveries['rcp'] = 1;
                    } else {
                        $deliveries['rcp'] = 0;
                    }
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

    public function delivery_summary_multiple_v4(Request $request)
    {
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
                $information['delivery_note_otp'] = $delivery_note->otp;
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
                    $payment_mode = $shipment_data->payment_mode_id;
                    $tracking_number = $shipment_data->tracking_number;
                    $consignee_name = $shipment_data->consignee_name;
                    $consignee_address = $shipment_data->consignee_address;
                    $booking_type = $shipment_data->booking_type_id;
                    $consignee_phone = $shipment_data->consignee_phone_number_1;
                    $shipper_name = $shipment_data->user->name;
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
                    $shipment_status_count = ShipmentsJourney::where('shipment_id', $shipment_id)
                        ->where('shipper_status_id', 5)->count();

                    if ($booking_type == 3) {
                        $product = array();
                        $parcel = ShipmentItem::where('shipment_id', $shipment_id);
                        if ($parcel->exists()) {
                            $parcel = $parcel->get();
                            foreach ($parcel as $item) {
                                $product[] = ['pid' => $item->id, 'type' => $item->product->product_name, 'description' => ($item->description == '') ? ' - ' : $item->description, 'price' => $item->price];
                            }
                        }
                        $deliveries['try_n_buy_items'] = $product;
                        $deliveries['try_and_buy_fees'] = (float)$shipment_data->try_and_buy_fees;
                    }
                    if ($shipment_status_count > 1) {
                        $deliveries['rcp'] = 1;
                    } else {
                        $deliveries['rcp'] = 0;
                    }
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
                    $deliveries['shipper'] = $shipper_name;
                    $deliveries['ccd'] = ($payment_mode == 2) ? 1 : 0;
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

    public function shipment_delivered_v2(Request $request)
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
                if (DeliveryNoteShipment::join('delivery_notes as dn', 'delivery_note_shipments.delivery_note_id', 'dn.id')->where('delivery_note_id', $request->delivery_note_id)->where('shipment_id', $request->shipment_id)->where('dn.rider_id', $rider_id)->exists()) { {

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
                                $res = str_replace(array('[', ']', '"'), '', $request->trybuy_id_list);
                                $item_ids = explode(',', $res);
                                $total_cod = 0;
                                foreach ($item_ids as $item_id) {
                                    $shipment_item = ShipmentItem::find($item_id);
                                    $total_cod += $shipment_item->price;
                                    $shipment_item->bought = 1;
                                    $shipment_item->save();
                                }
                                $shipment = Shipment::find($shipment->id);
                                $total_cod += $shipment->try_and_buy_fees;
                                $total_parcels = ShipmentItem::where('shipment_id', $shipment->id)->count();
                                $delivered_parcels = count($item_ids);
                                if ($total_parcels == $delivered_parcels) {
                                    Shipment::where('id', $shipment->id)->update(['amount' => $total_cod, 'received_amount' => $total_cod, 'shipper_status_id' => 36, 'consignee_status_id' => 36]);
                                    ShipmentsJourneyController::add($shipment->id, 36, 36, NULL, NULL, NULL, NULL, $request->delivery_note_id, NULL, 1, $received_by, $rider_id);
                                } else {
                                    Shipment::where('id', $shipment->id)->update(['amount' => $total_cod, 'received_amount' => $total_cod, 'shipper_status_id' => 37, 'consignee_status_id' => 37]);
                                    ShipmentsJourneyController::add($shipment->id, 37, 37, NULL, NULL, NULL, NULL, $request->delivery_note_id, NULL, 1, $received_by, $rider_id);
                                }
                                DeliveryNoteShipment::where('delivery_note_id', $request->delivery_note_id)->where('shipment_id', $shipment->id)->update(['status' => 5, 'update_type' => 1]);
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
                            $shipment->delivery_in_route = 0;
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

    public function rider_incentive(Request $request)
    {
        return response()->json(['status' => 1, 'message' => 'Please Update Your Bolt App']);
        $rider_id = $request->rider_id;
        $date = $request->get('date');
        $rider_incentives = DB::table('riders_incentives')
            ->select(DB::raw('sum(pickup_shipments) as pickup_shipments,sum(pickup_incentive) as pickup_incentive,sum(delivery_shipments) as delivery_shipments,sum(delivery_incentive) as delivery_incentive, sum(pickup_shipments) + sum(delivery_shipments) as total_shipments, sum(pickup_incentive) + sum(delivery_incentive) as total_incentives'))
            ->where('rider_id', $rider_id);
        if ($date != null) {
            $rider_incentives = $rider_incentives->whereDate('date', $date);
        }
        if ($rider_incentives->exists()) {
            $rider_incentives = $rider_incentives->get();
            return response()->json(["status" => 0, "incentives" => $rider_incentives]);
        } else {
            return response()->json(["status" => 1, "message" => "Incentives Not Found found!"]);
        }
    }

    public function retail_index(Request $request)
    {
        $rider_default_hub = Rider::where('riders.id', $request->rider_id)
            ->join('cities as c', 'c.id', '=', 'riders.city_id')
            ->select('c.hub_id as hub_id')->first();
        $retail_trax_centers = RetailTraxCenter::where('default_hub', $rider_default_hub->hub_id)->where('status', 1)->select('name', 'code', 'pickup_address_id')->get();
        $products = Product::all();
        $business_categories = BusinessCategory::where('id', '!=', 2)->get();
        $shipping_modes = RetailShippingMode::all();
        $domestic_cities = City::where('business_category_id', 1)->where('status', 1)->get();
        $domestic_overland_cities = CityDelivery::join('cities as c', 'c.id', '=', 'city_deliveries.city_id')->where('city_deliveries.booking_type_id', 1)->where('city_deliveries.shipping_mode_id', 2)->where('c.business_category_id', 1)->where('c.status', 1)->select('c.id', 'c.name')->get();
        $payment_modes = RetailPaymentMode::where('id', '=', 1)->get();
        $trax_boxes = RetailTraxBox::all();
        $banks = BanksList::all();
        return response()->json(["status" => 0, 'products' => $products, 'business_categories' => $business_categories, 'shipping_modes' => $shipping_modes, 'domestic_cities' => $domestic_cities, 'domestic_overland_cities' => $domestic_overland_cities, 'payment_modes' => $payment_modes, 'trax_boxes' => $trax_boxes, 'banks' => $banks, 'trax_centers' => $retail_trax_centers]);
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
        $rider_id = $request->rider_id;
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
        } else {
            $amount = 0;
            $r_amount = 0;
        }
        $payment_mode_id = 1;
        $try_and_buy_charges = NULL;

        $pieces_quantity = $request->input('pieces');
        $business_category_id = $request->input('business_category_id');

        $charges_mode_id = 1;

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
        $retail_shipment->rider_id = $rider_id;
        $retail_shipment->save();


        $date = Carbon::today()->toDateString();
        $cash_deposit = RetailCashDeposit::whereDate('created_at', $date)->where('category', 4)->where('admin_id', $rider_id);
        if ($cash_deposit->exists()) {
            $cash_deposit = $cash_deposit->first();
            $total_shipments = $cash_deposit->total_cn + 1;
            $total_cash = $cash_deposit->total_cash + $total_charges;
            $cash_deposit->total_cn = $total_shipments;
            $cash_deposit->total_cash = $total_cash;
            $cash_deposit->save();
        } else {
            $cash_deposit = new RetailCashDeposit();
            $cash_deposit->category = 4;
            $cash_deposit->rider_id = $rider_id;
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

    public function retail_shipment_store_v2(Request $request)
    {
        $rider_id = $request->rider_id;
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

        $city = City::find($pickup_city_id);

        /*$request->weight_charges = str_replace(',', '', $request->input('weight_charges'));
        $request->fuel_surcharge = str_replace(',', '', $request->input('fuel_surcharge'));
        $city = City::find($pickup_city_id);
        $gst = $city->zone->gst;
        $total_charges_without_gst = $request->weight_charges + $request->fuel_surcharge;
        $gst = $gst * $total_charges_without_gst;
        $total_charges = $total_charges_without_gst + $gst;*/

        if ($shipping_mode_check == 3) {
            $amount = str_replace(',', '', $request->input('cod_amount'));
            $r_amount = 0;
        } else {
            $amount = 0;
            $r_amount = 0;
        }
        $payment_mode_id = 1;
        $try_and_buy_charges = NULL;

        $pieces_quantity = $request->input('pieces');
        $business_category_id = $request->input('business_category_id');

        $charges_mode_id = 1;

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
        $retail_shipment->total_charges = $amount;
        $retail_shipment->shipper_account_no = $shipper_info->id;
        $retail_shipment->weight = $estimated_weight;
        $retail_shipment->length = $length;
        $retail_shipment->breadth = $breadth;
        $retail_shipment->height = $height;
        $retail_shipment->rider_id = $rider_id;
        $retail_shipment->save();


        $date = Carbon::today()->toDateString();
        $cash_deposit = RetailCashDeposit::whereDate('created_at', $date)->where('category', 4)->where('admin_id', $rider_id);
        if ($cash_deposit->exists()) {
            $cash_deposit = $cash_deposit->first();
            $total_shipments = $cash_deposit->total_cn + 1;
            $total_cash = $cash_deposit->total_cash + $amount;
            $cash_deposit->total_cn = $total_shipments;
            $cash_deposit->total_cash = $total_cash;
            $cash_deposit->save();
        } else {
            $cash_deposit = new RetailCashDeposit();
            $cash_deposit->category = 4;
            $cash_deposit->rider_id = $rider_id;
            $cash_deposit->total_cn = 1;
            $cash_deposit->total_cash = $amount;
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

    public function retail_shipment_calculate_rates(Request $request)
    {
        $pickup_address_id = $request->pickup_address_id;
        $pickup_address = RetailTraxCenter::where('pickup_address_id', $pickup_address_id);
        if ($pickup_address->exists()) {
            $pickup_address = $pickup_address->first();
            $pickup_city_id = $pickup_address->pickup_address->city_id;
            $discount =  $pickup_address->discount;
            $insurance =  intval($pickup_address->insurance);
            $trax_box_id = ($request->trax_box_id != -1) ? $request->trax_box_id : null;
            if ($request->volumetric_weight == 1) {
                $weight = (($request->input('length') * $request->input('breadth') * $request->input('height')) / 5000);
            } else {
                $weight = $request->input('weight');
            }

            $packaging = ($request->packaging_amount != null) ?  intval($request->packaging_amount) : 0;

            $insurance_amount = ($request->insurance_amount != null) ?  str_replace(',', '', $request->insurance_amount) : 0;
            if ($insurance_amount > 0) {
                $insurance_amount = round(intval($insurance_amount) * $insurance / 100, 2);
            }

            $rates = RetailRatesCalculationController::rates($request->shipping_mode_id, $request->business_category_id, $pickup_city_id, $request->city_id, $trax_box_id, $discount, $weight, $insurance_amount, $packaging);
            return response()->json(['status' => 0, 'rates' => $rates]);
        }
        return response()->json(['status' => 1, 'message' => "Invalid Pickup Address"]);
    }

    public function delivery_in_route(Request $request)
    {
        $rules = [
            'shipment_id' => ['required', 'integer', 'digits_between:1,10', 'exists:shipments,id']
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $shipment = Shipment::where('id', $request->shipment_id);
            if ($shipment->exists()) {
                $shipment = $shipment->first();
                $shipment->delivery_in_route = 1;
                $shipment->save();
                return response()->json(['status' => 0, 'message' => 'Delivery In-Route Successfully']);
            }
            return response()->json(['status' => 1, 'message' => 'Delivery In-Route Failed']);
        }
    }

    public function pickup_in_route(Request $request)
    {
        $rules = [
            'pickup_request_id' => ['required', 'integer', 'digits_between:1,10', 'exists:v2_pickup_requests,id']
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $pickup_request = V2PickupRequest::where('id', $request->pickup_request_id);
            if ($pickup_request->exists()) {
                $pickup_request = $pickup_request->first();
                $pickup_request->pickup_in_route = 1;
                $pickup_request->save();
                return response()->json(['status' => 0, 'message' => 'Pickup In-Route Successfully']);
            }
            return response()->json(['status' => 1, 'message' => 'Pickup In-Route Failed']);
        }
    }

    public function shipment_delivered_v3(Request $request)
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
            'picture' => ['nullable', 'image'],
            'cnic_image' => ['nullable', 'image'],
            'house_image' => ['nullable', 'image'],
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {

            $rider_id = $request->rider_id;

            $added_at = Carbon::createFromTimestampMs($request->added_at)->toDateTimeString();

            if (!RiderDelivery::where('delivery_note_id', $request->delivery_note_id)->where('shipment_id', $request->shipment_id)->where('delivered_status', 1)->exists()) {
                if (DeliveryNoteShipment::join('delivery_notes as dn', 'delivery_note_shipments.delivery_note_id', 'dn.id')->where('delivery_note_id', $request->delivery_note_id)->where('shipment_id', $request->shipment_id)->where('dn.rider_id', $rider_id)->exists()) { {

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
                            $picture_path = 'rider_delivery/picture_' . $rider_delivery->id . '.png';
                            Storage::disk('public')->put($picture_path, file_get_contents($request->picture));
                            $rider_delivery->picture_path = $picture_path;
                            $rider_delivery->save();
                        }
                        if ($request->has('cnic_image')) {
                            $picture_path = 'rider_delivery/cnic_image_' . $rider_delivery->id . '.png';
                            Storage::disk('public')->put($picture_path, file_get_contents($request->cnic_image));
                            $rider_delivery->cnic_image = $picture_path;
                            $rider_delivery->save();
                        }
                        if ($request->has('house_image')) {
                            $picture_path = 'rider_delivery/house_image_' . $rider_delivery->id . '.png';
                            Storage::disk('public')->put($picture_path, file_get_contents($request->house_image));
                            $rider_delivery->house_image = $picture_path;
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
                                $res = str_replace(array('[', ']', '"'), '', $request->trybuy_id_list);
                                $item_ids = explode(',', $res);
                                $total_cod = 0;
                                foreach ($item_ids as $item_id) {
                                    $shipment_item = ShipmentItem::find($item_id);
                                    $total_cod += $shipment_item->price;
                                    $shipment_item->bought = 1;
                                    $shipment_item->save();
                                }
                                $shipment = Shipment::find($shipment->id);
                                $total_cod += $shipment->try_and_buy_fees;
                                $total_parcels = ShipmentItem::where('shipment_id', $shipment->id)->count();
                                $delivered_parcels = count($item_ids);
                                if ($total_parcels == $delivered_parcels) {
                                    Shipment::where('id', $shipment->id)->update(['amount' => $total_cod, 'received_amount' => $total_cod, 'shipper_status_id' => 36, 'consignee_status_id' => 36]);
                                    ShipmentsJourneyController::add($shipment->id, 36, 36, NULL, NULL, NULL, NULL, $request->delivery_note_id, NULL, 1, $received_by, $rider_id);
                                } else {
                                    Shipment::where('id', $shipment->id)->update(['amount' => $total_cod, 'received_amount' => $total_cod, 'shipper_status_id' => 37, 'consignee_status_id' => 37]);
                                    ShipmentsJourneyController::add($shipment->id, 37, 37, NULL, NULL, NULL, NULL, $request->delivery_note_id, NULL, 1, $received_by, $rider_id);
                                }
                                DeliveryNoteShipment::where('delivery_note_id', $request->delivery_note_id)->where('shipment_id', $shipment->id)->update(['status' => 5, 'update_type' => 1]);
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
                            $shipment->delivery_in_route = 0;
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

    public function return_shipment_undelivered_v4(Request $request)
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
            'status_reason_id' => ['nullable', 'integer', 'digits_between:1,10', 'exists:shipment_status_reason,id'],
            'remarks' => ['nullable', 'string', 'max:255'],
            'picture' => ['nullable', 'image'],
            'audio' => ['nullable', 'file']
        ];
        $message = '';

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {

            $rider_id = $request->rider_id;
            $status_reason_id = ($request->has('status_reason_id')) ? $request->status_reason_id : null;
            $added_at = Carbon::createFromTimestampMs($request->added_at)->toDateTimeString();
            if (!RiderReturnDelivery::where('return_note_id', $request->return_note_id)->where('shipment_id', $request->shipment_id)->where('delivered_status', 1)->exists()) {
                if (!Shipment::where('id', $request->shipment_id)->where('shipper_status_id', 25)->exists()) {
                    if (ReturnNoteShipment::join('return_notes as rn', 'return_note_shipments.return_note_id', 'rn.id')->where('return_note_id', $request->return_note_id)->where('shipment_id', $request->shipment_id)->where('rn.rider_id', $rider_id)->exists()) {
                        $shipments = ShipmentsJourney::select('shipper_status_id', 'status_reason_id')
                            ->where('reference_1_id', $request->return_note_id)
                            ->where('shipment_id', $request->shipment_id)
                            ->where('shipper_status_id', $request->shipper_status_id)
                            ->where('status_reason_id', $status_reason_id)
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
                            $rider_return_delivery->rider_status_reason_id = $status_reason_id;
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
                            if ($request->has('picture')) {
                                $time = Carbon::now()->toDateString();
                                $picture_path = 'rider_return_delivery/' . $rider_return_delivery->id . '_' . $time . '.png';
                                Storage::disk('public')->put($picture_path, file_get_contents($request->picture));
                                $rider_return_delivery->picture_path = $picture_path;
                                $rider_return_delivery->save();
                            }

                            $environment = config('app.env');
                            if ($request->has('audio')) {
                                $time = Carbon::now()->toDateString();
                                if ($environment == 'production') {
                                    $extension = $request->file('audio')->getClientOriginalExtension();
                                    $audio_path = 'rider_return_delivery_audio/' . $rider_return_delivery->id . '_' . $time . '.' . $extension;
                                    Storage::disk('s3')->put($audio_path, file_get_contents($request->audio));
                                    $rider_return_delivery->audio_path = $audio_path;
                                    $rider_return_delivery->save();
                                } else {
                                    $extension = $request->file('audio')->getClientOriginalExtension();
                                    $audio_path = 'rider_return_delivery_audio/' . $rider_return_delivery->id . '_' . $time . '.' . $extension;
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

                                ShipmentsJourneyController::add($shipment->id, $request->shipper_status_id, $request->shipper_status_id, $status_reason_id, $remarks, NULL, NULL, $request->return_note_id, NULL, 1, NULL, $rider_id);
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

                            $updated_shipments = ReturnNoteShipment::where('return_note_id', $request->return_note_id);
                            $updated_shipments_count = $updated_shipments->where('status', 0)->count();
                            $total_shipments_count = $updated_shipments->count();
                            $undelivered_shipments_count = $updated_shipments->where('status', 1)->count();

                            if ($updated_shipments_count == 0) {
                                if ($total_shipments_count == $undelivered_shipments_count) {
                                    $return_note_data->status = 1;
                                } else {
                                    $return_note_data->status = 3;
                                }
                                $return_note_data->updated_at = Carbon::now();
                                $return_note_data->save();
                            }
                            $message = 'Shipment is marked as Undelivered Successfully';
                        } else {
                            $message = 'Shipment is already marked as Undelivered';
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

    public function shipment_delivered_v4(Request $request)
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
            'picture' => ['nullable', 'image'],
            'cnic_image' => ['nullable', 'image'],
            'house_image' => ['nullable', 'image'],
            'ccd_image' => ['nullable', 'image'],
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {

            $rider_id = $request->rider_id;

            $added_at = Carbon::createFromTimestampMs($request->added_at)->toDateTimeString();

            if (!RiderDelivery::where('delivery_note_id', $request->delivery_note_id)->where('shipment_id', $request->shipment_id)->where('delivered_status', 1)->exists()) {
                if (DeliveryNoteShipment::join('delivery_notes as dn', 'delivery_note_shipments.delivery_note_id', 'dn.id')->where('delivery_note_id', $request->delivery_note_id)->where('shipment_id', $request->shipment_id)->where('dn.rider_id', $rider_id)->exists()) { {

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
                            $picture_path = 'rider_delivery/picture_' . $rider_delivery->id . '.png';
                            Storage::disk('public')->put($picture_path, file_get_contents($request->picture));
                            $rider_delivery->picture_path = $picture_path;
                            $rider_delivery->save();
                        }
                        if ($request->has('cnic_image')) {
                            $picture_path = 'rider_delivery/cnic_image_' . $rider_delivery->id . '.png';
                            Storage::disk('public')->put($picture_path, file_get_contents($request->cnic_image));
                            $rider_delivery->cnic_image = $picture_path;
                            $rider_delivery->save();
                        }
                        if ($request->has('house_image')) {
                            $picture_path = 'rider_delivery/house_image_' . $rider_delivery->id . '.png';
                            Storage::disk('public')->put($picture_path, file_get_contents($request->house_image));
                            $rider_delivery->house_image = $picture_path;
                            $rider_delivery->save();
                        }
                        if ($request->has('ccd_image')) {
                            $picture_path = 'rider_delivery/ccd_image_' . $rider_delivery->id . '.png';
                            Storage::disk('public')->put($picture_path, file_get_contents($request->ccd_image));
                            $rider_delivery->ccd_image = $picture_path;
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
                                $res = str_replace(array('[', ']', '"'), '', $request->trybuy_id_list);
                                $item_ids = explode(',', $res);
                                $total_cod = 0;
                                foreach ($item_ids as $item_id) {
                                    $shipment_item = ShipmentItem::find($item_id);
                                    $total_cod += $shipment_item->price;
                                    $shipment_item->bought = 1;
                                    $shipment_item->save();
                                }
                                $shipment = Shipment::find($shipment->id);
                                $total_cod += $shipment->try_and_buy_fees;
                                $total_parcels = ShipmentItem::where('shipment_id', $shipment->id)->count();
                                $delivered_parcels = count($item_ids);
                                if ($total_parcels == $delivered_parcels) {
                                    Shipment::where('id', $shipment->id)->update(['amount' => $total_cod, 'received_amount' => $total_cod, 'shipper_status_id' => 36, 'consignee_status_id' => 36]);
                                    ShipmentsJourneyController::add($shipment->id, 36, 36, NULL, NULL, NULL, NULL, $request->delivery_note_id, NULL, 1, $received_by, $rider_id);
                                } else {
                                    Shipment::where('id', $shipment->id)->update(['amount' => $total_cod, 'received_amount' => $total_cod, 'shipper_status_id' => 37, 'consignee_status_id' => 37]);
                                    ShipmentsJourneyController::add($shipment->id, 37, 37, NULL, NULL, NULL, NULL, $request->delivery_note_id, NULL, 1, $received_by, $rider_id);
                                }
                                DeliveryNoteShipment::where('delivery_note_id', $request->delivery_note_id)->where('shipment_id', $shipment->id)->update(['status' => 5, 'update_type' => 1]);
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
                            $shipment->delivery_in_route = 0;
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

    public function rider_incentive_v2(Request $request)
    {
        return response()->json(['status' => 1, 'message' => 'Please Update Your Bolt App']);
        $rider_id = $request->rider_id;
        $from_date = $request->get('from_date');
        $to_date = $request->get('to_date');
        $rider_incentives = RidersIncentive::where('rider_id', $rider_id);
        if ($to_date) {
            $rider_incentives = $rider_incentives->whereBetween('date', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);
        } else {
            $rider_incentives = $rider_incentives->whereDate('date', $from_date);
        }
        if ($rider_incentives->exists()) {
            $rider_incentives = $rider_incentives->get();
            $pickup_shipments = 0;
            $pickup_incentive = 0;
            $delivery_shipments = 0;
            $delivery_incentive = 0;
            foreach ($rider_incentives as $rider_incentive) {
                $pickup_shipments += $rider_incentive->pickup_shipments;
                $pickup_incentive += $rider_incentive->pickup_incentive;
                $delivery_shipments += $rider_incentive->delivery_shipments;
                $delivery_incentive += $rider_incentive->delivery_incentive;
            }
            $total_shipments = $pickup_shipments + $delivery_shipments;
            $total_incentives = $pickup_incentive + $delivery_incentive;
            $pickup_shipments = strval($pickup_shipments);
            $pickup_incentive = strval($pickup_incentive);
            $delivery_shipments = strval($delivery_shipments);
            $delivery_incentive = strval($delivery_incentive);
            $total_shipments = strval($total_shipments);
            $total_incentives = strval($total_incentives);
            $data = ['pickup_shipments' => $pickup_shipments, 'pickup_incentive' => $pickup_incentive, 'delivery_shipments' => $delivery_shipments, 'delivery_incentive' => $delivery_incentive, 'total_shipments' => $total_shipments, 'total_incentives' => $total_incentives];
            return response()->json(["status" => 0, "incentives" => $data]);
        }
        return response()->json(["status" => 1, "message" => "Incentives Not Found"]);
    }

    public function rider_incentive_v3(Request $request)
    {
        $rider_id = $request->rider_id;
        $from_date = $request->get('from_date');
        $to_date = $request->get('to_date');
        $rider_incentives = RidersIncentive::where('rider_id', $rider_id);
        if ($to_date) {
            $rider_incentives = $rider_incentives->whereBetween('date', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);

            $total_payable = DeliveryNote::where('rider_id', $rider_id)
                ->whereBetween('created_at', [$from_date . ' 00:00:00', $to_date . ' 23:59:59'])
                ->sum('received_cod_amount');

            $total_earned_qs = RidersIncentive::select(DB::raw('sum(pickup_incentive) as  pickup_incentive'), DB::raw('sum(delivery_incentive) as  delivery_incentive'))
                ->whereBetween('date', [$from_date . ' 00:00:00', $to_date . ' 23:59:59'])
                ->where('rider_id', $rider_id)->first();
        } else {
            $rider_incentives = $rider_incentives->whereDate('date', $from_date);

            $total_payable = DeliveryNote::where('rider_id', $rider_id)
                ->whereDate('created_at', $from_date)
                ->sum('received_cod_amount');

            $total_earned_qs = RidersIncentive::select(DB::raw('sum(pickup_incentive) as  pickup_incentive'), DB::raw('sum(delivery_incentive) as  delivery_incentive'))
                ->whereDate('date', $from_date)
                ->where('rider_id', $rider_id)->first();
        }

        if ($rider_incentives->exists()) {
            $rider_incentives = $rider_incentives->get();
            $data = array();
            foreach ($rider_incentives as $rider_incentive) {
                $datum = array();
                $payable = DeliveryNote::where('rider_id', $rider_id)
                    ->whereDate('created_at', date('Y-m-d', strtotime($rider_incentive->date)))
                    ->sum('received_cod_amount');
                $datum['created_at'] = date('Y/m/d', strtotime($rider_incentive->date));
                $datum['pickup_shipments'] = $rider_incentive->pickup_shipments;
                $datum['pickup_incentive'] = $rider_incentive->pickup_incentive;
                $datum['delivery_shipments'] = $rider_incentive->delivery_shipments;
                $datum['delivery_incentive'] = $rider_incentive->delivery_incentive;
                $datum['total_incentive'] = $rider_incentive->delivery_incentive;
                $datum['cod_amount'] = $payable;
                $data[] = $datum;
            }
            $total_earned = $total_earned_qs->pickup_incentive + $total_earned_qs->delivery_incentive;
            return response()->json(["status" => 0, "incentives" => $data, "total_earned" => $total_earned, "total_payable" => $total_payable]);
        }
        return response()->json(["status" => 1, "message" => "Incentives Not Found"]);
    }

    public function delivery_summary_multiple_v5(Request $request)
    {
        $rider_id = $request->rider_id;
        $delivery_notes = DeliveryNote::where('rider_id', $rider_id)->where('status', 0)->where('pending_status', 0);
        $relation = DeliveryRelation::select('id', 'name')->get();

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
                $information['delivery_note_otp'] = $delivery_note->otp;

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
                    $refusal_otp = null;
                    $replacement_parcel_image = null;
                    $shipment_otp = ShipmentOtp::where('shipment_id', $shipment_id);
                    if ($shipment_otp->exists()) {
                        $shipment_otp = $shipment_otp->first();
                        $refusal_otp = $shipment_otp->otp;
                    }
                    $payment_mode = $shipment_data->payment_mode_id;
                    $tracking_number = $shipment_data->tracking_number;
                    $consignee_name = $shipment_data->consignee_name;
                    $consignee_address = $shipment_data->consignee_address;
                    $booking_type = $shipment_data->booking_type_id;
                    $consignee_phone = $shipment_data->consignee_phone_number_1;
                    $shipper_name = $shipment_data->user->name;
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
                    $deliveries['distribution'] = 0;
                    $shipment_status_count = ShipmentsJourney::where('shipment_id', $shipment_id)
                        ->where('shipper_status_id', 5)->count();

                    if ($shipment_data->user_id == 10354 && $booking_type == 1) {
                        $deliveries['distribution'] = 1;
                        $items = array();
                        $products = ShipmentDistributionProduct::where('shipment_id', $shipment_id);
                        if ($products->exists()) {
                            $products = $products->get();
                            foreach ($products as $product) {
                                $qty = $product->items * $product->units_per_item;
                                $per_unit_price = $product->price / $qty;
                                $items[] = ['pid' => $product->id, 'product_name' => $product->item->name, 'qty' => $qty, 'per_unit_price' => $per_unit_price];
                            }
                        }
                        $deliveries['distribution_items'] = $items;
                    } elseif ($booking_type == 3) {
                        $product = array();
                        $parcel = ShipmentItem::where('shipment_id', $shipment_id);
                        if ($parcel->exists()) {
                            $parcel = $parcel->get();
                            foreach ($parcel as $item) {
                                $product[] = ['pid' => $item->id, 'type' => $item->product->product_name, 'description' => ($item->description == '') ? ' - ' : $item->description, 'price' => $item->price];
                            }
                        }
                        $deliveries['try_n_buy_items'] = $product;
                        $deliveries['try_and_buy_fees'] = (float)$shipment_data->try_and_buy_fees;
                    } elseif ($booking_type == 2) {
                        $replacement_parcel = ShipmentReplacementParcelImage::where('shipment_id', $shipment_id);
                        if ($replacement_parcel->exists()) {
                            $replacement_parcel = $replacement_parcel->first();
                            $replacement_parcel_image = $replacement_parcel->picture_path;
                        }
                    }
                    if ($shipment_status_count > 1) {
                        $deliveries['rcp'] = 1;
                    } else {
                        $deliveries['rcp'] = 0;
                    }
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
                    $deliveries['shipper'] = $shipper_name;
                    $deliveries['refusal_otp'] = (string)$refusal_otp;
                    $deliveries['ccd'] = ($payment_mode == 2) ? 1 : 0;
                    $deliveries['replacement_parcel_image'] = $replacement_parcel_image;
                    $one_link_payment = OneLinkOutForDeliveryShipmentPayment::where('shipment_id', $shipment_id)->where('delivery_note_id', $delivery_note->id);
                    $deliveries['amount_paid'] = ($one_link_payment->exists()) ? 1 : 0;
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
            return response()->json(['status' => 0, 'message' => 'Delivery Note Is Assigned', 'information' => $nodes, 'relation_list' => $relation]);
        }
        return response()->json(['status' => 0, 'message' => 'No Delivery Note Assigned']);
    }

    public function shipment_delivered_v5(Request $request)
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
            'relation' => ['nullable', 'max:255'],
            'picture' => ['nullable', 'mimes:png,jpeg,jpg'],
            'cnic_image' => ['nullable', 'mimes:png,jpeg,jpg'],
            'house_image' => ['nullable', 'mimes:png,jpeg,jpg'],
            'ccd_image' => ['nullable', 'mimes:png,jpeg,jpg'],
            'replacement_image' => ['nullable', 'mimes:png,jpeg,jpg'],
            'dbf_otp_entered' => ['nullable', 'integer'],
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {

            try {
                //code...
                DB::beginTransaction();
                $rider_id = $request->rider_id;

                $added_at = Carbon::createFromTimestampMs($request->added_at)->toDateTimeString();
                //$added_at = $request->added_at;
                if (!RiderDelivery::where('delivery_note_id', $request->delivery_note_id)->where('shipment_id', $request->shipment_id)->where('delivered_status', 1)->exists()) {
                    if (DeliveryNoteShipment::join('delivery_notes as dn', 'delivery_note_shipments.delivery_note_id', 'dn.id')->where('dn.id', $request->delivery_note_id)->where('shipment_id', $request->shipment_id)->where('dn.rider_id', $rider_id)->exists()) { {

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
                                $receiver_name = str_replace('"', '', $request->receiver_name);
                                $received_by = $receiver_name;
                            }

                            $cnic = str_replace('"', '', $request->cnic);
                            if ($cnic != "Empty") {
                                $rider_delivery->cnic = $cnic;
                            } else {
                                $cnic = NULL;
                            }

                            $relation = str_replace('"', '', $request->relation);
                            if ($relation != "Empty") {
                                $rider_delivery->relation = $relation;
                            } else {
                                $relation = NULL;
                            }

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
                                $time = Carbon::now()->toDateString();
                                $picture_path = 'rider_delivery/picture_' . $rider_delivery->id . '_' . $time . '.png';
                                Storage::disk('public')->put($picture_path, file_get_contents($request->picture));
                                $rider_delivery->picture_path = $picture_path;
                                $rider_delivery->save();
                            }
                            if ($request->has('cnic_image')) {
                                $time = Carbon::now()->toDateString();
                                $picture_path = 'rider_delivery/cnic_image_' . $rider_delivery->id . '_' . $time . '.png';
                                Storage::disk('public')->put($picture_path, file_get_contents($request->cnic_image));
                                $rider_delivery->cnic_image = $picture_path;
                                $rider_delivery->save();
                            }
                            if ($request->has('house_image')) {
                                $time = Carbon::now()->toDateString();
                                $picture_path = 'rider_delivery/house_image_' . $rider_delivery->id . '_' . $time . '.png';
                                Storage::disk('public')->put($picture_path, file_get_contents($request->house_image));
                                $rider_delivery->house_image = $picture_path;
                                $rider_delivery->save();
                            }
                            if ($request->has('replacement_image')) {
                                $time = Carbon::now()->toDateString();
                                $picture_path = 'rider_delivery/replacement_image_' . $rider_delivery->id . '_' . $time . '.png';
                                Storage::disk('public')->put($picture_path, file_get_contents($request->replacement_image));
                                $rider_delivery->replacement_image = $picture_path;
                                $rider_delivery->save();
                            }
                            if ($request->has('ccd_image')) {
                                $time = Carbon::now()->toDateString();
                                $picture_path = 'rider_delivery/ccd_image_' . $rider_delivery->id . '_' . $time . '.png';
                                Storage::disk('public')->put($picture_path, file_get_contents($request->ccd_image));
                                $rider_delivery->ccd_image = $picture_path;
                                $rider_delivery->save();
                            }

                            if (DeliveryNote::where('id', $request->delivery_note_id)->where('pending_status', 0)->exists()) {
                                
                                if ($request->distribution == 1) {
                                    if ($request->has('distribution_items_list')) {
                                        $distribution_items = json_decode($request->distribution_items_list, true);
                                        foreach ($distribution_items as $distribution_item) {
                                            $product = ShipmentDistributionProduct::find($distribution_item["pid"]);
                                            $product->total_delivered_units = $distribution_item["delivered_qty"];
                                            $product->received_amount = round($distribution_item["total_amount_in_double"]);
                                            $total_delivered_skus = $distribution_item["delivered_qty"] / $product->units_per_item;
                                            $product->total_delivered_skus = (int)$total_delivered_skus;
                                            $product->save();
                                        }
                                        $shipment->shipper_status_id = 14;
                                        $shipment->consignee_status_id = 14;
                                        $shipment->received_amount = round($request->total_cod_amount);
                                        $shipment->amount = round($request->total_cod_amount);
                                        DeliveryNoteShipment::where('delivery_note_id', $request->delivery_note_id)->where('shipment_id', $shipment->id)->update(['status' => 6, 'update_type' => 1]);
                                        ShipmentsJourneyController::add($shipment->id, 14, 14, NULL, NULL, NULL, NULL, $request->delivery_note_id, NULL, 1, $received_by, $rider_id, $cnic, $relation);
                                    
                                        //$this->rider_wise_delivery_note($shipment->id,$request->delivery_note_id,$rider_id,14,$added_at,$rider_delivery,2);
                                        LastMileAppReport::dispatch($shipment->id,$request->delivery_note_id,$rider_id,14,$added_at,$rider_delivery,2);

                                    }
                                }
                                elseif ($shipment->booking_type_id == 2) {
                                    $shipment->shipper_status_id = 30;
                                    $shipment->consignee_status_id = 30;
                                    $shipment->received_amount = $shipment->amount;
                                    DeliveryNoteShipment::where('delivery_note_id', $request->delivery_note_id)->where('shipment_id', $shipment->id)->update(['status' => 2, 'update_type' => 1]);
                                    ShipmentsJourneyController::add($shipment->id, 30, 30, NULL, NULL, NULL, NULL, $request->delivery_note_id, NULL, 1, $received_by, $rider_id, $cnic, $relation);

                                    $details = ['tracking_number' => $shipment->tracking_number, 'shipper_number_1' => $shipment->user->phone, 'shipper_number_2' => $shipment->user->phone2, 'name' => $shipment->consignee_name, 'consignee_number_1' => $shipment->consignee_phone_number_1, 'consignee_number_2' => $shipment->consignee_phone_number_2, 'shipper_name' => $shipment->user->name];
                                    NotificationsController::send(183, $details);
                                    NotificationsController::send(184, $details);
                                    $rider_delivery->rider_status_id = 30;

                                    //$this->rider_wise_delivery_note($shipment->id,$request->delivery_note_id,$rider_id,30,$added_at,$rider_delivery,2);
                                    LastMileAppReport::dispatch($shipment->id,$request->delivery_note_id,$rider_id,30,$added_at,$rider_delivery,2);
                                }
                                else if ($shipment->booking_type_id == 3) {
                                    $res = str_replace(array('[', ']', '"'), '', $request->trybuy_id_list);
                                    $item_ids = explode(',', $res);
                                    $total_cod = 0;
                                    foreach ($item_ids as $item_id) {
                                        $shipment_item = ShipmentItem::find($item_id);
                                        $total_cod += $shipment_item->price;
                                        $shipment_item->bought = 1;
                                        $shipment_item->save();
                                    }
                                    $shipment = Shipment::find($shipment->id);
                                    $total_cod += $shipment->try_and_buy_fees;
                                    $total_parcels = ShipmentItem::where('shipment_id', $shipment->id)->count();
                                    $delivered_parcels = count($item_ids);
                                    if ($total_parcels == $delivered_parcels) {
                                        Shipment::where('id', $shipment->id)->update(['amount' => $total_cod, 'received_amount' => $total_cod, 'shipper_status_id' => 36, 'consignee_status_id' => 36]);
                                        ShipmentsJourneyController::add($shipment->id, 36, 36, NULL, NULL, NULL, NULL, $request->delivery_note_id, NULL, 1, $received_by, $rider_id, $cnic, $relation);
                                        $rider_delivery->rider_status_id = 36;

                                        //$this->rider_wise_delivery_note($shipment->id,$request->delivery_note_id,$rider_id,36,$added_at,$rider_delivery,2);
                                        LastMileAppReport::dispatch($shipment->id,$request->delivery_note_id,$rider_id,36,$added_at,$rider_delivery,2);
                                    } else {
                                        Shipment::where('id', $shipment->id)->update(['amount' => $total_cod, 'received_amount' => $total_cod, 'shipper_status_id' => 37, 'consignee_status_id' => 37]);
                                        ShipmentsJourneyController::add($shipment->id, 37, 37, NULL, NULL, NULL, NULL, $request->delivery_note_id, NULL, 1, $received_by, $rider_id, $cnic, $relation);
                                        $rider_delivery->rider_status_id = 37;

                                        //$this->rider_wise_delivery_note($shipment->id,$request->delivery_note_id,$rider_id,37,$added_at,$rider_delivery,2);
                                        LastMileAppReport::dispatch($shipment->id,$request->delivery_note_id,$rider_id,37,$added_at,$rider_delivery,2);
                                    }
                                    DeliveryNoteShipment::where('delivery_note_id', $request->delivery_note_id)->where('shipment_id', $shipment->id)->update(['status' => 5, 'update_type' => 1]);
                                }
                                else if ($shipment->booking_type_id == 4) {
                                    ShipmentsJourneyController::add($shipment->id, 14, 14, NULL, NULL, NULL, NULL, $request->delivery_note_id, NULL, 1, $received_by, $rider_id, $cnic, $relation);
                                
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

                                    //$this->rider_wise_delivery_note($shipment->id,$request->delivery_note_id,$rider_id,14,$added_at,$rider_delivery,2);
                                    LastMileAppReport::dispatch($shipment->id,$request->delivery_note_id,$rider_id,14,$added_at,$rider_delivery,2);
                                }
                                else {
                                    $shipment->shipper_status_id = 14;
                                    $shipment->consignee_status_id = 14;
                                    $shipment->received_amount = $shipment->amount;
                                    DeliveryNoteShipment::where('delivery_note_id', $request->delivery_note_id)->where('shipment_id', $shipment->id)->update(['status' => 6, 'update_type' => 1]);
                                    ShipmentsJourneyController::add($shipment->id, 14, 14, NULL, NULL, NULL, NULL, $request->delivery_note_id, NULL, 1, $received_by, $rider_id, $cnic, $relation);

                                    /*if($shipment->packaging_material_request == 1){
                                        self::delivery_packaging_material_update($shipment->tracking_number);
                                    }*/

                                    //$this->rider_wise_delivery_note($shipment->id,$request->delivery_note_id,$rider_id,14,$added_at,$rider_delivery,2);
                                    LastMileAppReport::dispatch($shipment->id,$request->delivery_note_id,$rider_id,14,$added_at,$rider_delivery,2);
                                }
                                $shipment->delivery_in_route = 0;
                                $shipment->save();
                            }
                            $rider_delivery->save();

                            if ($request->has('dbf_otp_entered')) {
                                $shipment_verification = ShipmentOtpVerification::where('shipment_id', $shipment->id);
                                if ($shipment_verification->exists()) {
                                    $shipment_verification = $shipment_verification->first();
                                } else {
                                    $shipment_verification = new ShipmentOtpVerification();
                                    $shipment_verification->shipment_id = $shipment->id;
                                }
                                $shipment_verification->via_dbf_otp = $request->dbf_otp_entered;
                                $shipment_verification->save();
                            }
                            $message = 'Shipment is marked as delivered Successfully';
                        }
                    } else {
                        $message = 'Shipment is marked as delivered already';
                    }
                }
                $delivered_shipment_ids = DeliveryNoteShipment::where('delivery_note_id', $request->delivery_note_id)->where('status', '>', 1)->where('status', '!=', 8)->select('shipment_id')->get();
                if(count($delivered_shipment_ids) > 0){
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
                }
                DB::commit();
                return response()->json(['status' => 0, 'message' => $message, 'delivery_note_id' => $request->delivery_note_id, 'shipment_id' => $request->shipment_id]);
            } catch (\Throwable $th) {
                DB::rollback();

                $this->createDeliveryNoteErrorLog($request->delivery_note_id, $request->shipment_id, $th->getMessage());
                return response()->json(['status' => 1, 'message' => 'Something Went Wrong!']);

                //throw $th;
            }
        }
    }

    public function return_shipment_delivered_v2(Request $request)
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
            'picture' => ['nullable', 'image'],
            'pod_image' => ['nullable', 'image']
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
                    if (ReturnNoteShipment::join('return_notes as rn', 'return_note_shipments.return_note_id', 'rn.id')->where('return_note_id', $request->return_note_id)->where('shipment_id', $request->shipment_id)->where('rn.rider_id', $rider_id)->exists()) { {

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
                                $time = Carbon::now()->toDateString();
                                $picture_path = 'rider_return_delivery/picture_' . $rider_return_delivery->id . '_' . $time . '.png';
                                Storage::disk('public')->put($picture_path, file_get_contents($request->picture));
                                $rider_return_delivery->picture_path = $picture_path;
                                $rider_return_delivery->save();
                            }

                            if ($request->has('pod_image')) {
                                $time = Carbon::now()->toDateString();
                                $picture_path = 'rider_return_delivery/pod_image_' . $rider_return_delivery->id . '_' . $time . '.png';
                                Storage::disk('public')->put($picture_path, file_get_contents($request->pod_image));
                                $rider_return_delivery->pod_image = $picture_path;
                                $rider_return_delivery->save();
                            }

                            if (ReturnNote::where('id', $request->return_note_id)->exists()) {
                                $shipment->shipper_status_id = 25;
                                $shipment->consignee_status_id = 25;
                                $shipment->save();
                                ReturnNoteShipment::where('return_note_id', $request->return_note_id)->where('shipment_id', $shipment->id)->update(['status' => 2, 'update_type' => 1]);
                                ShipmentsJourneyController::add($shipment->id, 25, 25, NULL, NULL, NULL, NULL, $request->return_note_id, NULL, 1, $received_by, $rider_id);

                                $users = GlobalSettings::where('type', 'returned_shipment_notification');
                                if($users->exists()) {
                                    $users = $users->first();
                                    $current_users = $users->text;
                                    $c_user = explode(',', $current_users);

                                    if(in_array($shipment->user_id,$c_user)){
                                        $return_delivered_to_shipper = ReturnDeliveredToShipperSms::where(['return_note_id'=>$request->return_note_id,'user_id' => $shipment->user_id,'shipment_id' => $shipment->id,'status' => 0]);
                                        if(!$return_delivered_to_shipper->exists()){
                                            $return_delivered = new ReturnDeliveredToShipperSms();
                                            $return_delivered->return_note_id = $request->return_note_id;
                                            $return_delivered->user_id = $shipment->user_id;
                                            $return_delivered->shipment_id = $shipment->id;
                                            $return_delivered->status = 0;
                                            $return_delivered->save();
                                        }
                                    }
                                }
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

    public function shipment_attempt_settings(Request $request)
    {
        $settings = GlobalSettings::whereIn('type', ['rider_shipment_attempt_count', 'rider_shipment_attempt_waiting_duration'])
            ->select('type as key', 'setting_value as value');
        if ($settings->exists()) {
            $settings = $settings->get();
            return response()->json(['status' => 0, 'data' => $settings]);
        }
        return response()->json(['status' => 1, 'message' => 'Settings not found']);
    }

    public function rider_signup_v3(Request $request)
    {
        return response()->json(['status' => 1, 'message' => 'Please Update Your Bolt App']);
        if ($request->isMethod('post')) {
            $rules = [
                'rider_type_id' => ['nullable', 'integer', 'digits_between:1,10', 'exists:rider_types,id'],
                //Employees
                'name' => ['nullable'],
                'mother_name' => ['nullable'],
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
            }
        } else {
            $message = 'Post Method is Required';
        }
        $response['message'] = $message;
        return response()->json($response);
    }

    public function rider_shift(Request $request)
    {
        $rider_id = $request->rider_id;
        $riders = Rider::find($rider_id);
        if ($riders) {
            $response = array();
            $date = '-';
            $employee_shift = EmployeeShift::where('id', $riders->shift_id);
            if ($employee_shift->exists()) {
                $employee_shift = $employee_shift->first();
                $shift_time = Carbon::createFromFormat('H:i:s', $employee_shift->start_time);
                if ($shift_time->lt(Carbon::now())) {
                    $date = Carbon::now()->format("Y-m-d");
                } else {
                    if (Carbon::now()->format("l") == "Monday") {
                        $date = Carbon::now()->subDays(2)->format("Y-m-d");
                    } else {
                        $date = Carbon::now()->subDays(1)->format("Y-m-d");
                    }
                    $last_action_log = EmployeeAttendanceActionLog::where('employee_id', $rider_id)
                        ->where('employee_type', 2)->whereDate('attendance_date', $date)->orderBy('id', 'DESC');
                    if ($last_action_log->exists()) {
                        $last_action_log = $last_action_log->first();
                        if ($last_action_log->action_id == 2) {
                            $date = Carbon::now()->format("Y-m-d");
                        }
                    }
                }
            }
            $response["status"] = 0;
            if ($employee_shift->exists()) {
                $employee_shift = $employee_shift->first();
                $response["shift_name"] = $employee_shift->name;
                $response["start_time"] = $employee_shift->start_time;
                $response["end_time"] = $employee_shift->end_time;
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
        return response()->json(['status' => 1, 'message' => 'Please Update Your Bolt App']);
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
            $rider_id = $request->rider_id;
            $dates = $this->generateDateRange($request->first_day, $request->last_day);
            $data = array();
            $shift = EmployeeShift::join('riders as r', 'employee_shifts.id', '=', 'r.shift_id')
                ->where('r.id', $rider_id)
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
                $attendance = EmployeeAttendance::where('employee_id', $rider_id)
                    ->where('employee_type', 2)
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
                                    $datum["status"] = 2; //Late
                                } else {
                                    $datum["status"] = 1; //Present
                                }
                            } else {
                                $datum["status"] = 2; //Late
                            }
                        } else {
                            $clock_in = Carbon::parse($attendance->clock_in)->format("H:i:s");
                            $time_diff = Carbon::parse($clock_in)->diffInMinutes(Carbon::parse($shift->start_time));
                            if ($time_diff > $shift->grace_time) {
                                $datum["status"] = 2; //Late
                            } else {
                                $datum["status"] = 1; //Present
                            }
                        }
                    } else {
                        $datum["status"] = 1;
                    }
                } else {
                    $datum["status"] = 3; //Absent
                }
                $data[] = $datum;
            }
            return response()->json(['status' => 0, 'data' => $data]);
        }
    }

    public function mark_attendance_v2(Request $request)
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
            $location_status = $this->calculate_location_status($request->latitude, $request->longitude);
            if ($request->action == 1) {
                $rider_attendance->clock_in_datetime = Carbon::now()->format("Y-m-d H:i:s");
                $rider_attendance->clock_in_latitude = $request->latitude;
                $rider_attendance->clock_in_longitude = $request->longitude;
                $rider_attendance->clock_in_location = $location_status;
                $rider_attendance->save();

                $rider_attendance_action->employee_id = $rider_id;
                $rider_attendance_action->employee_type = 2;
                $rider_attendance_action->action_id = $request->action;
                $rider_attendance_action->action_date = Carbon::now()->format("Y-m-d H:i:s");
                $rider_attendance_action->attendance_date = $attendance_date;
                $rider_attendance_action->latitude = $request->latitude;
                $rider_attendance_action->longitude = $request->longitude;
                $rider_attendance_action->location_status = $location_status;
                $rider_attendance_action->save();

                return response()->json(['status' => 0, 'message' => 'Clocked-In Successfully', 'response' => $rider_attendance_action]);
            } elseif ($request->action == 2) {
                $rider_attendance->clock_out_datetime = Carbon::now()->format("Y-m-d H:i:s");
                $rider_attendance->clock_out_latitude = $request->latitude;
                $rider_attendance->clock_out_longitude = $request->longitude;
                $rider_attendance->clock_out_location = $location_status;
                $rider_attendance->save();

                $rider_attendance_action->employee_id = $rider_id;
                $rider_attendance_action->employee_type = 2;
                $rider_attendance_action->action_id = $request->action;
                $rider_attendance_action->action_date = Carbon::now()->format("Y-m-d H:i:s");
                $rider_attendance_action->attendance_date = $attendance_date;
                $rider_attendance_action->latitude = $request->latitude;
                $rider_attendance_action->longitude = $request->longitude;
                $rider_attendance_action->location_status = $location_status;
                $rider_attendance_action->save();
                return response()->json(['status' => 0, 'message' => 'Clocked-Out Successfully', 'response' => $rider_attendance_action]);
            }

            return response()->json(['status' => 1, 'message' => 'Failed']);
        }
    }

    public function attendance_details_v2(Request $request)
    {
        $rules = [
            'attendance_date' => ['required']
        ];
        $rider_id = $request->rider_id;
        $employee_id = $request->rider_employee;
        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $rider_attendance_action = EmployeeAttendanceActionLog::where('employee_id', $employee_id)
                ->whereDate('attendance_date', $request->attendance_date)
                ->where('employee_type', 2)
                ->select('action_id', 'action_date', 'latitude', 'longitude', 'location_status', 'attendance_date')
                ->orderBy('action_date', 'ASC');
            if ($rider_attendance_action->exists()) {
                $rider_attendance_action = $rider_attendance_action->get();
                return response()->json(['status' => 0, 'attendance_details' => $rider_attendance_action]);
            }
            return response()->json(['status' => 0, 'attendance_details' => []]);
        }
    }

    public function rider_payslip(Request $request)
    {
        $rules = [
            'date' => ['required']
        ];
        $rider_id = $request->rider_id;
        $riders = Rider::find($rider_id);
        $validate = Validator::make($request->all(), $rules, $this->messages);
        $validate->setAttributeNames($this->names);
        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $payslip = EmployeePayslip::where('trax_id', $riders->trax_id)
                ->whereMonth('payroll_month', Carbon::parse($request->date)->format("m"))
                ->whereYear('payroll_month', Carbon::parse($request->date)->format("Y"));
            if ($payslip->exists()) {
                $payslip_obj = $payslip->get();
                $payslip = $payslip->first();
                $payroll_month = Carbon::parse($payslip->payroll_month)->format('F Y');
                $payroll_cut_off_date = Carbon::parse($payslip->payroll_cut_off_date)->toDateString();
                $personal_contact = $riders->phone;
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
                    $filename = 'payslip_' . $payslip->id .  Carbon::now()->format('Uu')  . '-' . $payroll_month . '.pdf';
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
            $rider = Rider::where('phone', substr_replace($request->input('phone_number'), '-', 4, 0));
            if ($rider->exists()) {
                $rider = $rider->first();
                if ($rider->status) {
                    $pin = rand(100000, 999999);
                    $rider->reset_pin_otp = $pin;
                    $rider->save();
                    NotificationsController::send(158, $rider->id);
                    return response()->json(['status' => 0, 'message' => 'Otp has been sent to your registered number', 'otp' => $pin]);
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
            $rider = Rider::where('phone', substr_replace($request->input('phone_number'), '-', 4, 0));
            if ($rider->exists()) {
                $rider = $rider->first();
                if ($request->input('otp') == $rider->reset_pin_otp) {
                    $rider->pin = bcrypt($request->pin);
                    $rider->dummy_pin = $request->pin;
                    $rider->reset_pin_otp = NULL;
                    $rider->save();

                    $employee = Employee::where('trax_id', $rider->trax_id)->where('trax_id', '!=', null);
                    if ($employee->exists()) {
                        $employee = $employee->first();
                        $employee->pin = $request->pin;
                        $employee->update();
                    }
                    return response()->json(['status' => 0, 'reset_message' => 'Pin has been reset successfully']);
                } else {
                    return response()->json(['status' => 1, 'message' => 'Invalid OTP']);
                }
            } else {
                return response()->json(['status' => 1, 'message' => 'Phone number not registered']);
            }
        }
    }

    public function fake_status_count(Request $request)
    {
        $rider_id = $request->rider_id;
        $fake_status_count = NULL;
        $month = NULL;
        if ($request->has('date')) {
            $r_current_date = Carbon::createFromFormat("Y-m-d H:i:s", $request->date . '-26 23:59:59')->toDateTimeString();
            $r_previous_month = Carbon::parse($r_current_date)->subMonth()->addDay()->format("Y-m-d 00:00:00");

            $fake_status_count = DeliveryNoteShipment::join('delivery_notes as dn', 'delivery_note_shipments.delivery_note_id', '=', 'dn.id')
                ->whereBetween('dn.status_verified_at', [$r_previous_month, $r_current_date])
                ->where('rider_id', $rider_id)
                ->where('update_type', 1)
                ->where('fake_status', 1)->count('fake_status');
            $month = Carbon::parse($request->date)->format("F-Y");
        }
        $current_month = Carbon::now()->format("Y-m");
        $current_date = Carbon::createFromFormat("Y-m-d H:i:s", $current_month . '-26 23:59:59')->toDateTimeString();
        $previous_month = Carbon::parse($current_date)->subMonth()->addDay()->format("Y-m-d 00:00:00");

        $current_fake_status_count = DeliveryNoteShipment::join('delivery_notes as dn', 'delivery_note_shipments.delivery_note_id', '=', 'dn.id')
            ->whereBetween('dn.status_verified_at', [$previous_month, $current_date])
            ->where('rider_id', $rider_id)
            ->where('update_type', 1)
            ->where('fake_status', 1)->count('fake_status');
        $c_month = Carbon::now()->format("F-Y");
        return response()->json(['status' => 0, 'current_count' => $current_fake_status_count, 'current_month' => $c_month, 'count' => $fake_status_count, 'month' => $month]);
    }

    public function get_rider_location_v2(Request $request)
    {
        return response()->json(['status' => 1, 'message' => 'Please Update Your Bolt App']);
        $rules = [
            'latitude' => ['required', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'longitude' => ['required', 'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'],
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);
        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $rider_id = $request->rider_id;
            $rider = Rider::find($rider_id);
            if ($rider) {
                $latitude = $request->latitude;
                $longitude = $request->longitude;

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
                $notification = AppNotification::find(10);
                if ($notification) {
                    if ($notification->status) {
                        $title = $notification->title;
                        $body = $notification->body;
                        if ($rider->reporting_location_id) {
                            $reporting_location = ReportingLocation::join('riders as r', 'reporting_locations.id', 'r.reporting_location_id')
                                ->where('r.id', $rider_id);
                        } else {
                            $reporting_location = ReportingLocation::join('employees as e', 'reporting_locations.id', 'e.reporting_location_id')
                                ->join('riders as r', 'e.id', 'r.employee_id')
                                ->where('r.id', $rider_id);
                        }
                        if ($reporting_location->exists()) {
                            $reporting_location = $reporting_location->first();
                            $reporting_location->radius;
                            $destination = $reporting_location->lat . ',' . $reporting_location->long;
                            $origin = $request->latitude . ',' . $request->longitude;
                            $distance = $this->distance($origin, $destination);
                            if ($distance <= $reporting_location->radius / 1000) {
                                if ($rider->shift_id) {
                                    $shift = EmployeeShift::where('id', $rider->shift_id);
                                } else {
                                    $shift = EmployeeShift::join('employees as e', 'employee_shifts.id', '=', 'e.shift_id')
                                        ->where('e.id', $rider->employee_id);
                                }
                                if ($shift->exists()) {
                                    $shift = $shift->first();
                                    $now_time = Carbon::createFromFormat("H:i:s", Carbon::now()->format("H:i") . ':00');
                                    $grace_time = $shift->extension_minutes;
                                    if (strpos($body, '[time]') !== FALSE) {
                                        $body = str_replace('[time]', Carbon::parse($shift->start_time)->addMinutes($grace_time + 1)->toTimeString(), $body);
                                    }
                                    if (strpos($body, '[name]') !== FALSE) {
                                        $body = str_replace('[name]', $rider->name, $body);
                                    }
                                    $attendance = EmployeeAttendance::where('employee_id', $rider_id)->where('employee_type', 2)->whereDate('attendance_date', Carbon::now()->format("Y-m-d"))
                                        ->whereNotNull('clock_in_datetime');
                                    if (!$attendance->exists()) {
                                        if ($now_time->diffInMinutes(Carbon::parse($shift->start_time)) == 0) {
                                            $notification_history = new EmployeeNotificationHistory();
                                            $notification_history->employee_id = $rider_id;
                                            $notification_history->employee_type_id = 2;
                                            $notification_history->title = $title;
                                            $notification_history->message = $body;
                                            $notification_history->save();
                                            return response()->json(['status' => 0, 'data' => ['body' => $body, 'title' => $title], 'notification' => 0, 'message' => "success"]);
                                        } elseif ($now_time->diffInMinutes(Carbon::parse($shift->start_time)->addMinutes(ceil($grace_time / 2))) == 0) {
                                            $notification_history = new EmployeeNotificationHistory();
                                            $notification_history->employee_id = $rider_id;
                                            $notification_history->employee_type_id = 2;
                                            $notification_history->title = $title;
                                            $notification_history->message = $body;
                                            $notification_history->save();
                                            return response()->json(['status' => 0, 'data' => ['body' => $body, 'title' => $title], 'notification' => 0, 'message' => "success"]);
                                        } elseif ($now_time->diffInMinutes(Carbon::parse($shift->start_time)->addMinutes(($grace_time - 1))) == 0) {
                                            $notification_history = new EmployeeNotificationHistory();
                                            $notification_history->employee_id = $rider_id;
                                            $notification_history->employee_type_id = 2;
                                            $notification_history->title = $title;
                                            $notification_history->message = $body;
                                            $notification_history->save();
                                            return response()->json(['status' => 0, 'data' => ['body' => $body, 'title' => $title], 'notification' => 0, 'message' => "success"]);
                                        } else {
                                            return response()->json(['status' => 1, 'message' => 'Time error', 'notification' => 1]);
                                        }
                                    } else {
                                        return response()->json(['status' => 1, 'message' => 'Attendance Already Marked', 'notification' => 1]);
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
                        return response()->json(['status' => 1, 'message' => 'Notification Disabled', 'notification' => 1]);
                    }
                } else {
                    return response()->json(['status' => 1, 'message' => 'Notification not found', 'notification' => 1]);
                }
            } else {
                return response()->json(['status' => 1, 'message' => 'User Not Found!', 'notification' => 1]);
            }
        }
    }

    public function signup_required_details(Request $request)
    {
        if ($request->isMethod('post')) {
            $rules = [
                'rider_type_id' => ['required', 'integer', 'digits_between:1,10', 'exists:rider_types,id'],
                'rider_sub_category' => ['nullable', 'integer', 'digits_between:1,10', 'exists:rider_categories,id'],
                'rider_main_category' => ['nullable', 'integer', 'digits_between:1,10', 'exists:rider_main_categories,id'],
                //Employees
                'name' => ['required'],
                'mother_name' => ['required'],
                'employee_gender_id' => ['required', 'integer', 'digits_between:1,10', 'exists:employee_genders,id'],
                'city_id' => ['required', 'integer', 'digits_between:1,10', 'exists:cities,id'],
                'shift_id' => ['required', 'integer', 'digits_between:1,10', 'exists:employee_shifts,id'],
                'cnic_no' => ['required', 'regex:/^[0-9]{5}-[0-9]{7}-[0-9]{1}$/'],
                'phone_number' => ['required', 'regex:/^[0][0-9]{3}-[0-9]{7}$/'],
                'guardian_name' => ['required'],
                'religion_id' => ['required', 'integer', 'digits_between:1,10', 'exists:employee_religions,id'],
                'nationality_id' => ['required', 'integer', 'digits_between:1,10', 'exists:employee_nationalities,id'],
                'domicile_id' => ['nullable', 'integer', 'digits_between:1,10', 'exists:employee_domiciles,id'],
                'marital_status_id' => ['required', 'integer', 'digits_between:1,10', 'exists:employee_marital_statuses,id'],
                'blood_group_id' => ['nullable', 'integer', 'digits_between:1,10', 'exists:employee_blood_groups,id'],
                'address' => ['required'],
                'emergency_contact' => ['nullable', 'regex:/^[0][0-9]{3}-[0-9]{7}$/'],
                'emergency_contact_person' => ['nullable'],
                'zone_id' => ['nullable', 'integer', 'digits_between:1,10', 'exists:zones,id'],
                'date_of_birth' => ['required'],
                'pin' => ['required', 'integer', 'digits:4'],
                'cnic_1' => ['required', 'mimes:png,jpeg,jpg,pdf,doc,docx'],
                'cnic_2' => ['required', 'mimes:png,jpeg,jpg,pdf,doc,docx'],
                'fuel' => ['nullable'],
                'employee_nature_id' => ['nullable',  'integer', 'digits_between:1,10'],
                'line_manager_id' => ['nullable',  'integer', 'digits_between:1,10'],
                'sub_department' => ['nullable'],

                //BankInformation
                'bank_id' => ['nullable', 'integer', 'digits_between:1,10', 'exists:banks_lists,id'],
                'account_title' => ['nullable'],
                'iban' => ['nullable'],

                //replacementInfo
                'replacement_employee_id' => ['nullable', 'integer', 'digits_between:1,10', 'exists:employees,id'],
                'replacement_last_working_day' => ['nullable'],


            ];
            $response = ['status' => 1];
            $message = 'Unknown';

            $validate = Validator::make($request->all(), $rules, $this->messages);

            $validate->setAttributeNames($this->names);

            if ($validate->fails()) {
                $message = 'Error(s) in Input';
                $response['phone_number'] = $request->phone_number;
                $response['cnic'] = $request->cnic_no;
                $response['errors'] = $validate->errors();
            } else {
                $rider_request = RiderRequest::where('phone_no', $request->input('phone_number'))
                    ->orWhere('cnic', $request->input('cnic_no'));

                $employee = Employee::where('phone_number', $request->input('phone_number'))
                    ->orWhere('cnic', $request->input('cnic_no'));

                if ($employee->exists()) {
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
                            $employee_request = new Employee();
                            $employee_request->name = $request->name;
                            $employee_request->employee_gender_id = $request->employee_gender_id;
                            $employee_request->city_id = $request->city_id;
                            $employee_request->cnic = $request->cnic_no;
                            $employee_request->phone_number = $request->phone_number;
                            $employee_request->employee_type_id = 2;
                            //                            $employee_request->rider_request_id = $rider_request->id;
                            $employee_request->status_id = 2;
                            $employee_request->guardian_name = $request->guardian_name;
                            $employee_request->religion_id = $request->religion_id;
                            $employee_request->nationality_id = $request->nationality_id;
                            $employee_request->domicile_id = $request->domicile_id;
                            $employee_request->marital_status_id = $request->marital_status_id;
                            $employee_request->blood_group = $request->blood_group_id;
                            $employee_request->address = $request->address;
                            $employee_request->emergency_contact = $request->emergency_contact;
                            $employee_request->emergency_contact_person = $request->emergency_contact_person;
                            $employee_request->zone_id = $request->zone_id;
                            $employee_request->shift_id = $request->shift_id;
                            $employee_request->date_of_birth = $request->date_of_birth;
                            $employee_request->mother_name = $request->mother_name;
                            $employee_request->pin = $request->pin;
                            $employee_request->rider_main_category = $request->rider_main_category;
                            $employee_request->rider_sub_category = $request->rider_sub_category;
                            $employee_request->rider_type_id = $request->rider_type_id;
                            $employee_request->department_id = 6;
                            $employee_request->fuel = $request->fuel;
                            $employee_request->replacement_employee_id = $request->replacement_employee_id;
                            $employee_request->replacement_last_working_day = $request->replacement_last_working_day;
                            $employee_request->employee_nature_id = $request->employee_nature_id;
                            $employee_request->sub_department = $request->sub_department;
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
            }
        } else {
            $message = 'Post Method is Required';
        }
        $response['message'] = $message;
        $response['phone_number'] = $request->phone_number;
        $response['cnic'] = $request->cnic_no;
        return response()->json($response);
    }

    public function signup_optional_details(Request $request)
    {
        if ($request->isMethod('post')) {
            $rules = [
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
                } else {
                    $response['status'] = 1;
                    $message = 'Invalid Employee ID';
                }
            }
        } else {
            $message = 'Post Method is Required';
        }
        $response['message'] = $message;
        return response()->json($response);
    }

    public function leave_index(Request $request)
    {
        return response()->json(['status' => 1, 'message' => 'Please Update Your Bolt App']);
        $employee_id = $request->rider_employee;
        $employee = Employee::find($employee_id);
        if ($employee) {
            if ($employee->line_manager_id == null) {
                return response()->json(['status' => 1, 'message' => "Department Head is not present!"]);
            }
            $data = array();
            $data['trax_id'] = $employee->trax_id;
            $data['name'] = $employee->name;
            $data['designation'] = "Rider";
            $data['department'] = "Operations";
            $data['approver_email'] = $employee->line_manager->email;
            $data['approver_name'] = $employee->line_manager->name;
            $data['user_type'] = 0;
            return response()->json(['status' => 0, 'data' => $data]);
        }
        return response()->json(['status' => 1, 'message' => "Rider not found"]);
    }

    public function leave_apply(Request $request)
    {
        return response()->json(['status' => 1, 'message' => 'Please Update Your Bolt App']);
        $rules = [
            'from' => ['required'],
            'to' => ['nullable'],
            'reason' => ['required', 'max:500'],
            'leave_id' => ['nullable', 'integer', 'digits_between:1,10', 'exists:employee_leaves,id'],
        ];

        $rider_id = $request->rider_id;
        $employee_id = $request->rider_employee;
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
                        $leave_request->save();
                        $message = "Leave Request edited successfully";
                    } else {
                        return response()->json(['status' => 1, 'message' => 'Invalid Leave Request ID']);
                    }
                } else {
                    $leave = EmployeeLeave::where('employee_id', $employee_id)->where('employee_type_id', 2)->whereIn('status', [1, 2]);
                    if ($leave->exists()) {
                        return response()->json(['status' => 1, 'message' => 'Leave Request Already Submitted & Pending for Approval']);
                    }
                    $leave_request = new EmployeeLeave();
                    $leave_request->employee_id = $employee_id;
                    $leave_request->employee_type_id = 2;
                    $leave_request->reporter_id = $employee->line_manager_id;
                    $leave_request->from = $request->from;
                    $leave_request->to = $request->to;
                    $leave_request->applied_reason = $request->reason;
                    $leave_request->save();

                    $user = Admin::where('employee_id', $leave_request->reporter_id)->first();
                    NotificationsController::app_notification(11, $rider_id, 2, $leave_request->id);
                    NotificationsController::app_notification(12, $user->id, 1, $leave_request->id);
                    $message = "Leave Request submitted successfully";
                }
                return response()->json(['status' => 0, 'apply_message' => $message]);
            } else {
                return response()->json(['status' => 1, 'message' => 'User Not Found']);
            }
        }
    }

    public function employee_leave_list(Request $request)
    {
        return response()->json(['status' => 1, 'message' => 'Please Update Your Bolt App']);
        $rider_id = $request->rider_id;
        $employee_id = $request->rider_employee;
        $employee_leaves = EmployeeLeave::join('leave_statuses as ls', 'employee_leaves.status', '=', 'ls.id')
            ->select('employee_leaves.id as id', 'employee_leaves.from as from', 'employee_leaves.to as to', 'employee_leaves.applied_reason as applied_reason', 'employee_leaves.rejected_reason as rejected_reason', 'employee_leaves.status as status_id', 'ls.name as status')
            ->where('employee_id', $employee_id)
            ->where('employee_type_id', 2);
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
                    $working_days = $employee_leave->employee->department->working_days;
                    $start_date = Carbon::createFromFormat('Y-m-d', $employee_leave->from);
                    $end_date = Carbon::createFromFormat('Y-m-d', $employee_leave->to);
                    if ($working_days == 1) {
                        $diffDays = $start_date->diffInWeekdays($end_date, Carbon::setWeekendDays([Carbon::SUNDAY]));
                    } else {
                        $diffDays = $start_date->diffInWeekdays($end_date, Carbon::setWeekendDays([Carbon::SATURDAY, Carbon::SUNDAY]));
                    }
                    $datum['days_count'] = $diffDays;
                } else {
                    $datum['days_count'] = 1;
                }
                $data[] = $datum;
            }
            return response()->json(['status' => 0, 'response' => $data]);
        }
        return response()->json(['status' => 1, 'message' => "No Leave Found!"]);
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
                    $dates = AdminAPIController::generateDateRange($employee_leaves->from, $employee_leaves->to);
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
            $employee_id = $request->rider_employee;
            $dates = $this->generateDateRange($request->first_day, $request->last_day);
            $data = array();
            $shift = EmployeeShift::join('employees as r', 'employee_shifts.id', '=', 'r.shift_id')
                ->where('r.id', $employee_id)
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
                    ->where('employee_type', 2)
                    ->whereDate('attendance_date', Carbon::parse($date)->format("Y-m-d"));
                if ($attendance->exists()) {
                    $attendance = $attendance->first();
                    if ($attendance->leave_status == 2) {
                        $datum["status"] = 4; //adjustment apply
                    } else if ($attendance->leave_status == 1) {
                        $datum["status"] = 5; //leave apply
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
                                        $datum["status"] = 2; //Late
                                    } else {
                                        $datum["status"] = 1; //Present
                                    }
                                } else {
                                    $datum["status"] = 2; //Late
                                }
                            } else {
                                $datum["status"] = 3; //Absent
                            }
                        } else {
                            if ($attendance->clock_in_datetime) {
                                $datum["status"] = 1; //Present
                            } else {
                                $datum["status"] = 3; //Absent
                            }
                        }
                    }
                } else {
                    $datum["status"] = 3; //Absent
                }
                $data[] = $datum;
            }
            return response()->json(['status' => 0, 'data' => $data]);
        }
    }

    public function get_employee_id(Request $request)
    {
        $rider_id = $request->rider_id;
        $riders = Rider::find($rider_id);
        if ($riders) {
            $employee = Employee::where('trax_id', $riders->trax_id);
            if ($employee->exists()) {
                $employee = $employee->first();
                return response()->json(['status' => 0, 'employee_id' => $employee->id]);
            } else {
                return response()->json(['status' => 1, 'message' => "Rider Not Found"]);
            }
        } else {
            return response()->json(['status' => 1, 'message' => "Rider Not Found"]);
        }
    }

    public function check_profile(Request $request)
    {
        return response()->json(['status' => 0, 'message' => 'Please Update Your Bolt App']);
        $rider_id = $request->rider_id;
        $rider_profile = Rider::join('employees as e', 'riders.trax_id', '=', 'e.trax_id')
            ->select('e.id as employee_id', 'e.blood_group as blood_group_id', 'e.emergency_contact as emergency_contact_no', 'e.emergency_contact_person as emergency_contact_person')
            ->where('riders.id', $rider_id);
        if ($rider_profile->exists()) {
            $rider_profile = $rider_profile->first();
            if (!$rider_profile->blood_group_id || !$rider_profile->emergency_contact_no || !$rider_profile->emergency_contact_person) {
                return response()->json(['status' => 0, 'message' => "Please Update Your Profile"]);
            } else {
                return response()->json(['status' => 1, 'message' => "Profile already updated"]);
            }
        } else {
            return response()->json(['status' => 1, 'message' => "Profile Not Found"]);
        }
    }

    public function get_profile(Request $request)
    {
        return response()->json(['status' => 0, 'message' => 'Please Update Your Bolt App']);
        $rider_id = $request->rider_id;
        $blood_group_list = EmployeeBloodGroup::all();
        $rider_profile = Rider::join('employees as e', 'riders.trax_id', '=', 'e.trax_id')
            ->leftjoin('employee_blood_groups as bg', 'bg.id', '=', 'e.blood_group')
            ->select('e.id as employee_id', 'bg.name as blood_group_name', 'bg.id as blood_group_id', 'e.emergency_contact as emergency_contact_no', 'e.emergency_contact_person as emergency_contact_person')
            ->where('riders.id', $rider_id);
        if ($rider_profile->exists()) {
            $rider_profile = $rider_profile->first();
            return response()->json(['status' => 0, 'blood_group_list' => $blood_group_list, 'blood_group_id' => $rider_profile->blood_group_id, 'blood_group_name' => $rider_profile->blood_group_name, 'employee_id' => $rider_profile->employee_id, 'emergency_contact_no' => $rider_profile->emergency_contact_no, 'emergency_contact_person' => $rider_profile->emergency_contact_person]);
        } else {
            return response()->json(['status' => 1, 'message' => "Profile Not Found"]);
        }
    }

    public function update_profile(Request $request)
    {
        return response()->json(['status' => 0, 'message' => 'Please Update Your Bolt App']);
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
        return response()->json(['status' => 1, 'message' => 'Please Update Your Bolt App']);
        $rider_id = $request->rider_id;
        $rider = Rider::find($rider_id);
        if ($rider) {
            if ($rider->reset_pin_status == 1) {
                return response()->json(['status' => 0, 'pin_status' => 1]);
            }
            return response()->json(['status' => 0, 'pin_status' => 0]);
        }
        return response()->json(['status' => 0, 'pin_status' => 0]);
    }

    public function logout(Request $request)
    {
        $rider_id = $request->rider_id;
        $rider = Rider::find($rider_id);
        if ($rider) {
            if ($rider->reset_pin_status == 1) {
                $rider->reset_pin_status = 0;
                $rider->save();
                return response()->json(['status' => 0, 'message' => "Logout Successfully"]);
            }
            return response()->json(['status' => 0, 'message' => "Logout Successfully"]);
        }
        return response()->json(['status' => 0, 'message' => "Logout Successfully"]);
    }

    public function check_profile_v2(Request $request)
    {
        return response()->json(['status' => 0, 'message' => 'Please Update Your Bolt App']);
        $rider_id = $request->rider_id;
        $rider = Rider::find($rider_id);
        if ($rider) {
            $profile = Employee::where('trax_id', $rider->trax_id);
            if ($profile->exists()) {
                $profile = $profile->first();
                if (!$profile->blood_group || !$profile->emergency_contact || !$profile->emergency_contact_person || !$profile->guardian_name || !$profile->mother_name  || !$profile->address  || !$profile->employee_gender_id || !$profile->religion_id || !$profile->marital_status_id || !$profile->date_of_birth || !$profile->shift_id || !$profile->domicile_id || !$profile->rider_main_category || !$profile->rider_sub_category || !$profile->nationality_id) {
                    return response()->json(['status' => 0, 'message' => "Please Update Your Profile"]);
                } else {
                    return response()->json(['status' => 1, 'message' => "Profile already updated"]);
                }
            } else {
                return response()->json(['status' => 1, 'message' => "Profile Not Found"]);
            }
        } else {
            return response()->json(['status' => 1, 'message' => "Profile Not Found"]);
        }
    }

    public function get_profile_v2(Request $request)
    {
        $rider_id = $request->rider_id;
        $rider = Rider::find($rider_id);
        if ($rider) {
            $blood_group_list = EmployeeBloodGroup::all();
            $gender_list = EmployeeGender::all();
            $religion_list = EmployeeReligion::all();
            $marital_status_list = EmployeeMaritalStatus::all();
            $shift_list = EmployeeShift::all();
            $domecile_list = EmployeeDomicile::all();
            $nationalities_list = EmployeeNationality::all();
            $rider_type_list = RiderType::all();
            $profile = Employee::where('trax_id', $rider->trax_id);
            if ($profile->exists()) {
                $profile = $profile->get();
                return response()->json(['status' => 0, 'blood_group_list' => $blood_group_list, 'gender_list' => $gender_list, 'religion_list' => $religion_list, 'marital_status_list' => $marital_status_list, 'rider_type_list' => $rider_type_list, 'shift_list' => $shift_list, 'domecile_list' => $domecile_list, 'nationalities_list' => $nationalities_list, 'employee_data' => $profile]);
            } else {
                return response()->json(['status' => 1, 'message' => "Profile Not Found"]);
            }
        } else {
            return response()->json(['status' => 1, 'message' => "Profile Not Found"]);
        }
    }

    public function update_profile_v2(Request $request)
    {
        $rules = [
            'employee_id' => ['required', 'integer', 'digits_between:1,10', 'exists:employees,id'],
            'mother_name' => ['nullable'],
            'rider_type_id' => ['nullable', 'integer', 'digits_between:1,10', 'exists:rider_types,id'],
            'employee_gender_id' => ['nullable', 'integer', 'digits_between:1,10', 'exists:employee_genders,id'],
            'shift_id' => ['nullable', 'integer', 'digits_between:1,10', 'exists:employee_shifts,id'],
            'guardian_name' => ['nullable'],
            'religion_id' => ['nullable', 'integer', 'digits_between:1,10', 'exists:employee_religions,id'],
            'domicile_id' => ['nullable', 'integer', 'digits_between:1,10', 'exists:employee_domiciles,id'],
            'marital_status_id' => ['nullable', 'integer', 'digits_between:1,10', 'exists:employee_marital_statuses,id'],
            'blood_group_id' => ['required', 'integer', 'digits_between:1,10', 'exists:employee_blood_groups,id'],
            'nationality_id' => ['nullable', 'integer', 'digits_between:1,10', 'exists:employee_nationalities,id'],
            'address' => ['nullable'],
            'emergency_contact' => ['required', 'regex:/^[0][0-9]{3}-[0-9]{7}$/'],
            'emergency_contact_person' => ['required'],
            'date_of_birth' => ['nullable'],
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $employee_request = Employee::find($request->employee_id);
            if ($employee_request) {
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

                if ($request->has('date_of_birth')) {
                    $employee_request->date_of_birth = $request->date_of_birth;
                }

                if ($request->has('mother_name')) {
                    $employee_request->mother_name = $request->mother_name;
                }

                if ($request->has('shift_id')) {
                    $employee_request->shift_id = $request->shift_id;
                }

                if ($request->has('rider_type_id')) {
                    $employee_request->rider_type_id = $request->rider_type_id;
                }
                if ($request->has('nationality_id')) {
                    $employee_request->nationality_id = $request->nationality_id;
                }

                $employee_request->save();
                return response()->json(['status' => 0, 'message' => "Profile update successfully"]);
            } else {
                return response()->json(['status' => 1, 'message' => 'User not found!']);
            }
        }
    }

    public function check_bolt_version(Request $request)
    {
        $global_settings = GlobalSettings::where('type', 'bolt_updated_version')->select('setting_value as setting_value');
        if ($global_settings->exists()) {
            $global_settings = $global_settings->first();
            return response()->json(['status' => 0, 'app_version' => $global_settings->setting_value]);
        } else {
            return response()->json(['status' => 0, 'app_version' => 24]);
        }
    }

    public function pickup_pick_v3(Request $request)
    {
        $rules = [
            'added_at' => ['required'],
            'pickup_note_id' => ['required', 'integer', 'digits_between:1,10', 'exists:v2_pickup_notes,id'],
            'pickup_request_id' => ['required', 'integer', 'digits_between:1,10', 'exists:v2_pickup_requests,id'],
            'start_location_latitude' => ['required'],
            'start_location_longitude' => ['required'],
            'actual_location_latitude' => ['required'],
            'actual_location_longitude' => ['required'],
            'shipment_ids' => ['nullable'],
            'shipments' => ['nullable', 'integer', 'digits_between:1,10'],
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

                    $rider_pickup->shipments = ($request->has('shipments')) ? $request->shipments : null;

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

                    $pickup_request_shipments = V2PickupRequestShipment::where('pickup_request_id', $request->pickup_request_id)->pluck('shipment_id')->toArray();
                    if ($request->has('shipment_ids')) {
                        $shipment_count = 0;
                        $shipment_ids = explode(',', $request->shipment_ids);
                        $notification_shipments = array();
                        foreach ($shipment_ids as $shipment_id) {
                            $shipment = Shipment::where('tracking_number', $shipment_id);
                            if ($shipment->exists()) {
                                $shipment = $shipment->first();
                                if ($shipment->shipper_status_id == 1 && in_array($shipment->id, $pickup_request_shipments)) {
                                    $shipment->shipper_status_id = 53;
                                    $shipment->consignee_status_id = 53;
                                    $shipment->save();
                                    ShipmentsJourneyController::add($shipment->id, 53, 53, NULL, NULL, NULL, NULL, $request->pickup_request_id, $request->pickup_note_id, 1, NULL, $rider_id);
                                    $shipment_count += 1;
                                    $notification_shipments[] = $shipment->id;
                                } else {
                                    self::rider_pickup_invalid_logs($rider_id, $request->pickup_request_id, $request->pickup_note_id, $shipment->id, 53);
                                }
                            }
                        }
                        $pickup_note = V2PickupNote::find($request->pickup_note_id);
                        if($pickup_note){
                            $pickup_note->shipments_scanned_by_rider = $pickup_note->shipments_scanned_by_rider + $shipment_count;
                            $pickup_note->save();
                        }
                        if (count($notification_shipments) > 0) {
                            NotificationsController::send(210, $notification_shipments, $request->pickup_request_id);
                        }
                    }
                }
            }

            return response()->json(['status' => 0, 'message' => 'Pickup Pick Successfully', 'pickup_note_id' => $request->pickup_note_id, 'pickup_request_id' => $request->pickup_request_id]);
        }
    }

    public function shipment_undelivered_v3(Request $request)
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
            'remarks_id' => ['nullable', 'integer', 'digits_between:1,10', 'exists:consignee_refused_reasons,id'],
            'picture' => ['required', 'mimes:png,jpeg,jpg'],
            'open_box' => ['required', 'integer'],
            'audio' => ['nullable', 'file'],
            'otp_entered' => ['nullable', 'integer'],
        ];
        $message = '';

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        }
        else {
            try {
                //code...
                DB::beginTransaction();

                $rc_flag = false;
                $rider_id = $request->rider_id;

                $added_at = Carbon::createFromTimestampMs($request->added_at)->toDateTimeString();
                //$added_at = $request->added_at;
                $shipment_journey = ShipmentsJourney::where('shipment_id', $request->shipment_id)->orderBy('id', 'DESC');
                if ($shipment_journey->exists()) {
                    $shipment_journey = $shipment_journey->first();
                    if ($shipment_journey->shipper_status_id == 5) {
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
                                        if ($request->has('otp_entered') && $request->status_reason_id == 8) {
                                            $rider_delivery->otp_entered = $request->otp_entered;
                                            if ($request->otp_entered == 1) {
                                                $rc_flag = true;
                                            }
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
                                            $shipment->delivery_in_route = 0;

                                            if (in_array($request->open_box, [1, 2])) {
                                                $shipment->open_box = 1;
                                                $shipment_open_box = ShipmentOpenBox::where('shipment_id', $shipment->id);
                                                if ($shipment_open_box->exists()) {
                                                    $shipment_open_box = $shipment_open_box->first();
                                                } else {
                                                    $shipment_open_box = new ShipmentOpenBox();
                                                    $shipment_open_box->shipment_id = $shipment->id;
                                                }
                                                $shipment_open_box->open_box_type = $request->open_box;
                                                $shipment_open_box->save();
                                            } else {
                                                $shipment->open_box = 0;
                                            }
                                            $shipment->save();

                                            $remarks = NULL;
                                            if ($request->has('remarks')) {
                                                $remarks = $request->remarks;
                                            }

                                            $remarks_id = NULL;
                                            if ($request->has('remarks_id')) {
                                                $remarks_id = $request->remarks_id;
                                            }

                                            ShipmentsJourneyController::add($shipment->id, $request->shipper_status_id, $request->shipper_status_id, $request->status_reason_id, $remarks, NULL, NULL, $request->delivery_note_id, NULL, 0, NULL, $rider_id, NULL, NULL, $remarks_id);
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

                                            //$this->rider_wise_delivery_note($shipment->id, $request->delivery_note_id, $rider_id, $request->shipper_status_id, $added_at, $rider_delivery, 2);
                                            LastMileAppReport::dispatch($shipment->id,$request->delivery_note_id,$rider_id,$request->shipper_status_id,$added_at,$rider_delivery,2);
                                            if ($request->shipper_status_id != 7) {
                                                NotificationsController::send(145, $shipment->id, $request->delivery_note_id);
                                            }
                                        }

                                        if ($rc_flag == true) {
                                            $otp_bypass = $this->otp_bypass($shipment->user_id);
                                            if ($otp_bypass) {
                                                $this->auto_return_confirm($shipment->id);
                                                ShipmentsJourneyController::add($shipment->id, 20, 20, 8, $remarks, NULL, 346, $request->delivery_note_id, NULL, 1, NULL, $rider_id, NULL, NULL, $remarks_id);
                                            } else {
                                                $arr['shipment_id'] = $request->shipment_id;
                                                $arr['delivery_note_id'] = $request->delivery_note_id;
                                                dispatch(new ProcessAgentCallMonitoring($arr));
                                            }
                                        }
                                        if ($rc_flag == false) {
                                            $arr['shipment_id'] = $request->shipment_id;
                                            $arr['delivery_note_id'] = $request->delivery_note_id;
                                            dispatch(new ProcessAgentCallMonitoring($arr));
                                        }

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
                    } else {
                        $message = 'Shipment is not for Out for Delivery';
                    }
                } else {
                    $message = 'Shipment is not for Out for Delivery';
                }
                DB::commit();
                return response()->json(['status' => 0, 'message' => $message, 'delivery_note_id' => $request->delivery_note_id, 'shipment_id' => $request->shipment_id]);
            }
            catch (\Throwable $th)
            {
                DB::rollback();

                $this->createDeliveryNoteErrorLog($request->delivery_note_id, $request->shipment_id, $th->getMessage());
                return response()->json(['status' => 1,  'message' => 'Something Went Wrong!']);

                //throw $th;
            }
        }
    }

    public function shipment_undelivered_v4(Request $request)
    {

        $rules = [
            'added_at' => ['required'],
            'delivery_note_id' => ['required', 'integer', 'digits_between:1,10', 'exists:delivery_notes,id'],
            'start_location_latitude' => ['required', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'start_location_longitude' => ['required', 'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'],
            'actual_location_latitude' => ['required', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'actual_location_longitude' => ['required', 'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'],
            'shipment_id' => ['required', 'integer', 'digits_between:1,10', 'exists:shipments,id'],
            'status_reason_id' => ['required', 'integer', 'digits_between:1,10', 'exists:shipment_status_reason,id'],
            'remarks' => ['nullable', 'string', 'max:255'],
            'remarks_id' => ['nullable', 'integer', 'digits_between:1,10', 'exists:consignee_refused_reasons,id'],
            'picture' => ['required', 'mimes:png,jpeg,jpg'],
            'open_box' => ['required', 'integer'],
            'audio' => ['nullable', 'file'],
            'otp_entered' => ['nullable', 'integer'],
        ];
        $message = '';

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        }
        else {
            try {
                //code...
                DB::beginTransaction();

                $rc_flag = false;
                $rider_id = $request->rider_id;

                $added_at = Carbon::createFromTimestampMs($request->added_at)->toDateTimeString();
                //$added_at = $request->added_at;
                $shipment_journey = ShipmentsJourney::where('shipment_id', $request->shipment_id)->orderBy('id', 'DESC');
                if ($shipment_journey->exists()) {
                    $shipment_journey = $shipment_journey->first();
                    if ($shipment_journey->shipper_status_id == 5) {
                        if (!RiderDelivery::where('delivery_note_id', $request->delivery_note_id)->where('shipment_id', $request->shipment_id)->where('delivered_status', 1)->exists()) {
                            if (!Shipment::where('id', $request->shipment_id)->whereIn('shipper_status_id', [14, 30, 36, 37, 20, 52, 13])->exists()) {
                                if (DeliveryNoteShipment::join('delivery_notes as dn', 'delivery_note_shipments.delivery_note_id', 'dn.id')->where('delivery_note_id', $request->delivery_note_id)->where('shipment_id', $request->shipment_id)->where('dn.rider_id', $rider_id)->exists()) {
                                 
                                        $shipment = Shipment::find($request->shipment_id);
                                        $shipper_status_id = UndeliveredReasonController::add($request->shipment_id, $request->delivery_note_id, $request->status_reason_id);
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
                                        $rider_delivery->rider_status_id = $shipper_status_id;
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
                                        if ($request->has('otp_entered') && $request->status_reason_id == 8) {
                                            $rider_delivery->otp_entered = $request->otp_entered;
                                            if ($request->otp_entered == 1) {
                                                $rc_flag = true;
                                            }
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

                                            $shipment->shipper_status_id = $shipper_status_id;
                                            $shipment->consignee_status_id = $shipper_status_id;
                                            $shipment->delivery_in_route = 0;

                                            if (in_array($request->open_box, [1, 2])) {
                                                $shipment->open_box = 1;
                                                $shipment_open_box = ShipmentOpenBox::where('shipment_id', $shipment->id);
                                                if ($shipment_open_box->exists()) {
                                                    $shipment_open_box = $shipment_open_box->first();
                                                } else {
                                                    $shipment_open_box = new ShipmentOpenBox();
                                                    $shipment_open_box->shipment_id = $shipment->id;
                                                }
                                                $shipment_open_box->open_box_type = $request->open_box;
                                                $shipment_open_box->save();
                                            } else {
                                                $shipment->open_box = 0;
                                            }
                                            $shipment->save();

                                            $remarks = NULL;
                                            if ($request->has('remarks')) {
                                                $remarks = $request->remarks;
                                            }

                                            $remarks_id = NULL;
                                            if ($request->has('remarks_id')) {
                                                $remarks_id = $request->remarks_id;
                                            }

                                            ShipmentsJourneyController::add($shipment->id, $shipper_status_id, $shipper_status_id, $request->status_reason_id, $remarks, NULL, NULL, $request->delivery_note_id, NULL, 0, NULL, $rider_id, NULL, NULL, $remarks_id);
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

                                            //$this->rider_wise_delivery_note($shipment->id, $request->delivery_note_id, $rider_id, $request->shipper_status_id, $added_at, $rider_delivery, 2);
                                            LastMileAppReport::dispatch($shipment->id,$request->delivery_note_id,$rider_id,$shipper_status_id,$added_at,$rider_delivery,2);
                                            if ($shipper_status_id != 7) {
                                                NotificationsController::send(145, $shipment->id, $request->delivery_note_id);
                                            }
                                        }

                                        if ($rc_flag == true) {
                                            $otp_bypass = $this->otp_bypass($shipment->user_id);
                                            if ($otp_bypass) {
                                                $this->auto_return_confirm($shipment->id);
                                                ShipmentsJourneyController::add($shipment->id, 20, 20, 8, $remarks, NULL, 346, $request->delivery_note_id, NULL, 1, NULL, $rider_id, NULL, NULL, $remarks_id);
                                            } else {
                                                $arr['shipment_id'] = $request->shipment_id;
                                                $arr['delivery_note_id'] = $request->delivery_note_id;
                                                dispatch(new ProcessAgentCallMonitoring($arr));
                                            }
                                        }
                                        if ($rc_flag == false) {
                                            $arr['shipment_id'] = $request->shipment_id;
                                            $arr['delivery_note_id'] = $request->delivery_note_id;
                                            dispatch(new ProcessAgentCallMonitoring($arr));
                                        }

                                        $message = 'Shipment is marked as Undelivered Successfully';
                                   
                                }
                            } else {
                                $message = 'Shipment status is already marked';
                            }
                        } else {
                            $message = 'Shipment is already marked as Delivered';
                        }
                    } else {
                        $message = 'Shipment is not for Out for Delivery';
                    }
                } else {
                    $message = 'Shipment is not for Out for Delivery';
                }
                DB::commit();
                return response()->json(['status' => 0, 'message' => $message, 'delivery_note_id' => $request->delivery_note_id, 'shipment_id' => $request->shipment_id]);
            }
            catch (\Throwable $th)
            {
                DB::rollback();

                $this->createDeliveryNoteErrorLog($request->delivery_note_id, $request->shipment_id, $th->getMessage());
                return response()->json(['status' => 1,  'message' => 'Something Went Wrong!']);

                //throw $th;
            }
        }
    }
    
    public function undelivered_reason_map()
    {
        $undelivered_reason_map = BoltUndeliveredReasonMap::join('shipment_status_reason as ssr','ssr.id','bolt_undelivered_reason_maps.reason_id')->select('ssr.id','ssr.name')->get();
        return response()->json(['status' => 0, 'message' => $undelivered_reason_map]);

    }

    public function check_profile_v3(Request $request)
    {
        $rider_id = $request->rider_id;
        $rider = Rider::find($rider_id);
        if ($rider) {
            if ($request->has('device_token')) {
                EmployeeDeviceToken::where('device_token', $request->get('device_token'))->delete();
                EmployeeDeviceToken::where('employee_type_id', 2)->where('employee_id', $rider->id)->delete();
                $employee_device_token = new EmployeeDeviceToken();
                $employee_device_token->employee_id = $rider->id;
                $employee_device_token->employee_type_id = 2;
                $employee_device_token->device_token = $request->get('device_token');
                $employee_device_token->save();
            }
            $rider->current_app_version = $request->app_version;
            $rider->save();
            $profile = Employee::where('trax_id', $rider->trax_id);
            if ($profile->exists()) {
                $profile = $profile->first();
                if (!$profile->blood_group || !$profile->emergency_contact || !$profile->emergency_contact_person || !$profile->guardian_name || !$profile->mother_name  || !$profile->address  || !$profile->employee_gender_id || !$profile->religion_id || !$profile->marital_status_id || !$profile->date_of_birth || !$profile->shift_id || !$profile->domicile_id || !$profile->rider_main_category || !$profile->rider_sub_category || !$profile->nationality_id) {
                    return response()->json(['status' => 1, 'message' => "Please Update Your Profile"]);
                } else {
                    return response()->json(['status' => 1, 'message' => "Profile already updated"]);
                }
            } else {
                return response()->json(['status' => 1, 'message' => "Profile Not Found"]);
            }
        } else {
            return response()->json(['status' => 1, 'message' => "Profile Not Found"]);
        }
    }

    public function mark_attendance_v3(Request $request)
    {
        $rules = [
            'attendance_date' => ['required'],
            'latitude' => ['required', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'longitude' => ['required', 'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'],
            'action' => ['required', 'integer', 'digits_between:1,10', 'exists:attendance_actions,id'],
        ];

        $employee_id = $request->rider_employee;
        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $attendance_date = Carbon::createFromFormat('Y-m-d', $request->attendance_date);
            $last_action_log = EmployeeAttendanceActionLog::where('employee_id', $employee_id)
                ->where('employee_type', 2)->whereDate('attendance_date', $attendance_date->format("Y-m-d"))->orderBy('id', 'DESC');
            if ($last_action_log->exists()) {
                $last_action_log = $last_action_log->first();
                if (($attendance_date->lt(Carbon::now()->format("Y-m-d")) && $last_action_log->action_id == 2) || $attendance_date->format('l') == "Sunday") {
                    $attendance_date = $attendance_date->addDays(1);
                }
            }

            $rider_attendance = EmployeeAttendance::where('employee_id', $employee_id)
                ->whereDate('attendance_date', Carbon::parse($attendance_date)->format("Y-m-d"))
                ->where('employee_type', 2);
            $rider_attendance_action = new EmployeeAttendanceActionLog();
            if ($rider_attendance->exists()) {
                $rider_attendance = $rider_attendance->first();
            } else {
                $rider_attendance = new EmployeeAttendance();
                $rider_attendance->employee_id = $employee_id;
                $rider_attendance->employee_type = 2;
                $rider_attendance->attendance_date = $attendance_date;
            }
            $location_status = $this->calculate_location_status($request->latitude, $request->longitude);
            if ($request->action == 1) {
                if ($rider_attendance->clock_in_datetime == null) {
                    $rider_attendance->clock_in_datetime = Carbon::now()->format("Y-m-d H:i:s");
                    $rider_attendance->clock_in_latitude = $request->latitude;
                    $rider_attendance->clock_in_longitude = $request->longitude;
                    $rider_attendance->clock_in_location = $location_status;
                    $rider_attendance->save();
                }

                $rider_attendance_action->employee_id = $employee_id;
                $rider_attendance_action->employee_type = 2;
                $rider_attendance_action->action_id = $request->action;
                $rider_attendance_action->action_date = Carbon::now()->format("Y-m-d H:i:s");
                $rider_attendance_action->attendance_date = $attendance_date;
                $rider_attendance_action->latitude = $request->latitude;
                $rider_attendance_action->longitude = $request->longitude;
                $rider_attendance_action->location_status = $location_status;
                $rider_attendance_action->save();

                return response()->json(['status' => 0, 'message' => 'Clocked-In Successfully', 'response' => $rider_attendance_action, 'attendance_date' => Carbon::parse($attendance_date)->format("Y-m-d")]);
            } elseif ($request->action == 2) {
                $last_clockin_action = EmployeeAttendanceActionLog::where('employee_id', $employee_id)
                    ->where('employee_type', 2)->orderBy('id', 'DESC')->where('action_id', 1)->first();
                $attendance_date = $last_clockin_action->attendance_date;

                $rider_attendance->clock_out_datetime = Carbon::now()->format("Y-m-d H:i:s");
                $rider_attendance->clock_out_latitude = $request->latitude;
                $rider_attendance->clock_out_longitude = $request->longitude;
                $rider_attendance->clock_out_location = $location_status;
                $rider_attendance->save();

                $rider_attendance_action->employee_id = $employee_id;
                $rider_attendance_action->employee_type = 2;
                $rider_attendance_action->action_id = $request->action;
                $rider_attendance_action->action_date = Carbon::now()->format("Y-m-d H:i:s");
                $rider_attendance_action->attendance_date = $attendance_date;
                $rider_attendance_action->latitude = $request->latitude;
                $rider_attendance_action->longitude = $request->longitude;
                $rider_attendance_action->location_status = $location_status;
                $rider_attendance_action->save();
                return response()->json(['status' => 0, 'message' => 'Clocked-Out Successfully', 'response' => $rider_attendance_action, 'attendance_date' => Carbon::parse($attendance_date)->format("Y-m-d")]);
            }

            return response()->json(['status' => 1, 'message' => 'Failed']);
        }
    }

    public function login_v4(Request $request)
    {
        $rules = [
            'phone_number' => ['required', 'regex:/^[0][0-9]{10}$/'],
            'pin' => ['required', 'integer', 'digits:4'],
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $generate_otp = false;
            $rider = Rider::where('phone', substr_replace($request->input('phone_number'), '-', 4, 0));
            if ($rider->exists()) {
                $rider = $rider->first();
                if ($rider->status) {
                    if (Hash::check($request->input('pin'), $rider->pin)) {
                        $environment = config('app.env');
                        $settings = GlobalSettings::where('type', 'rider_otp');
                        if ($settings->exists()) {
                            $settings = $settings->first();
                            if ($settings->setting_value) {
                                if ($environment == 'production' || $environment == 'staging') {
                                    $otp = mt_rand(100000, 999999);
                                    $rider->otp = $otp;
                                    $rider->last_login_attempt = Carbon::now();
                                    $rider->save();
                                    $data = array("otp" => $otp, "phone_number" => $request->phone_number);
                                    NotificationsController::send(138, $rider, $data);
                                    $generate_otp = true;
                                }
                            }
                        }

                        if ($rider->api_token) {
                            $api_token = $rider->api_token;
                        } else {
                            $api_token = uniqid(base64_encode(str_random(60)));
                            $rider->api_token = $api_token;
                        }
                        $rider->save();
                        if ($generate_otp) {
                            return response()->json(['status' => 0, 'message' => 'Otp Generated', 'api_token' => $api_token, 'otp_generated' => 1]);
                        } else {
                            $information = array();
                            $information['name'] = $rider->name;
                            $information['phone'] = $rider->phone;
                            $information['cnic'] = $rider->cnic;
                            $information['address'] = $rider->address;
                            $information['role'] = 'rider';
                            $information['api_token'] = $rider->api_token;
                            $information['cargo_user'] = 0;
                            $information['welcome_bit'] = 0;
                            if (!$rider->first_login) {
                                $information['welcome_bit'] = 1;
                                $information['welcome_message'] = "Welcome to TRAX " . $rider->name;
                            }
                            $reporting_location = ReportingLocation::join('employees as e', 'reporting_locations.id', 'e.reporting_location_id')
                                ->join('riders as r', 'e.id', 'r.employee_id')
                                ->where('r.id', $rider->id);
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
                            $rider->first_login = 1;
                            $rider->save();
                            return response()->json(['status' => 0, 'message' => 'Otp Generated', 'api_token' => $api_token, 'information' => $information]);
                        }
                    } else {
                        return response()->json(['status' => 1, 'message' => 'Invalid PIN']);
                    }
                } else {
                    if ($rider->first_login == 0) {
                        return response()->json(['status' => 1, 'message' => "Dear " . $rider->name . "- Your request is in process and is pending for approval from HR."]);
                    } else {
                        return response()->json(['status' => 1, 'message' => 'Your Account is Disabled']);
                    }
                }
            } else {
                $employee = Employee::whereIn('request_status_id', [1, 2])->where('employee_type_id', 2)->where('phone_number', substr_replace($request->input('phone_number'), '-', 4, 0))->orWhere('official_phone_number', substr_replace($request->input('phone_number'), '-', 4, 0));
                if ($employee->exists()) {
                    $employee = $employee->first();
                    return response()->json(['status' => 1, 'message' => "Dear " . $employee->name . "- Your request is in process and is pending for approval from HR."]);
                } else {
                    return response()->json(['status' => 1, 'message' => 'Invalid Credentials']);
                }
            }
        }
    }

    public function validate_otp(Request $request)
    {
        $rules = [
            'otp' => ['required', 'integer', 'digits:6'],
            'device_token' => ['nullable']
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $rider_id = $request->rider_id;
            $rider = Rider::find($rider_id);
            if ($rider->otp == $request->otp) {
                $information = array();

                $information['name'] = $rider->name;
                $information['phone'] = $rider->phone;
                $information['cnic'] = $rider->cnic;
                $information['address'] = $rider->address;
                $information['role'] = 'rider';
                $information['api_token'] = $rider->api_token;
                $information['cargo_user'] = 0;

                if ($request->has('device_token')) {
                    EmployeeDeviceToken::where('device_token', $request->get('device_token'))->delete();
                    EmployeeDeviceToken::where('employee_type_id', 2)->where('employee_id', $rider->id)->delete();
                    $employee_device_token = new EmployeeDeviceToken();
                    $employee_device_token->employee_id = $rider->id;
                    $employee_device_token->employee_type_id = 2;
                    $employee_device_token->device_token = $request->get('device_token');
                    $employee_device_token->save();
                }

                $reporting_location = ReportingLocation::join('employees as e', 'reporting_locations.id', 'e.reporting_location_id')
                    ->join('riders as r', 'e.id', 'r.employee_id')
                    ->where('r.id', $rider->id);
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
                if (!$rider->first_login) {
                    $information['welcome_bit'] = 1;
                    $information['welcome_message'] = "Welcome to TRAX " . $rider->name;
                }
                $rider->first_login = 1;
                $rider->save();
                return response()->json(['status' => 0, 'message' => 'Login Successful', 'information' => $information]);
            } else {
                return response()->json(['status' => 1, 'message' => 'Invalid OTP']);
            }
        }
    }

    public function adjustment_index(Request $request)
    {
        $employee_id = $request->rider_employee;
        $rider = Employee::find($employee_id);
        if ($rider) {
            if ($rider->line_manager_id == null) {
                return response()->json(['status' => 1, 'message' => "Department Head is not present!"]);
            }
            $data = array();
            $data['trax_id'] = $rider->trax_id;
            $data['name'] = $rider->name;
            $data['designation'] = "Rider";
            $data['department'] = "Operations";
            $data['approver_email'] = $rider->line_manager->email;
            $data['approver_name'] = $rider->line_manager->name;
            $data['user_type'] = 0;
            return response()->json(['status' => 0, 'data' => $data]);
        }
        return response()->json(['status' => 1, 'message' => "Rider not found"]);
    }

    public function adjustment_apply(Request $request)
    {
        $rules = [
            'date' => ['required'],
            'reason' => ['required', 'max:500'],
        ];

        $rider_id = $request->rider_id;
        $employee_id = $request->rider_employee;
        $validate = Validator::make($request->all(), $rules, $this->messages);
        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $rider = Employee::find($employee_id);
            if ($rider) {
                if ($rider->line_manager_id != null) {

                    $leave = EmployeeAttendanceAdjustment::where('employee_id', $employee_id)->where('employee_type_id', 2)->whereIn('status', [1, 2])->where('date', $request->date);
                    if ($leave->exists()) {
                        return response()->json(['status' => 1, 'message' => 'Adjustment Request Already Submitted & Pending for Approval']);
                    }
                    $leave_request = new EmployeeAttendanceAdjustment();
                    $leave_request->employee_id = $employee_id;
                    $leave_request->employee_type_id = 2;
                    $leave_request->reporter_id = $rider->line_manager_id;
                    $leave_request->date = $request->date;
                    $leave_request->applied_reason = $request->reason;
                    $leave_request->save();
                    $notify = false;
                    $user = Admin::find($rider->line_manager->admin->id);
                    if ($user) {
                        $user_id = $user->id;
                        $notify = true;
                    }
                    NotificationsController::app_notification(17, $rider_id, 2, $leave_request->id);
                    if ($notify) {
                        NotificationsController::app_notification(18, $leave_request->reporter_id, 1, $leave_request->id);
                    }
                    $message = "Adjustment Request submitted successfully";
                    return response()->json(['status' => 0, 'apply_message' => $message]);
                } else {
                    return response()->json(['status' => 1, 'message' => 'Department Head Not Found']);
                }
            } else {
                return response()->json(['status' => 1, 'message' => 'User Not Found']);
            }
        }
    }

    public function employee_adjustment_list(Request $request)
    {
        $rider_id = $request->rider_id;
        $employee_id = $request->rider_employee;
        $employee_leaves = EmployeeAttendanceAdjustment::join('leave_statuses as ls', 'employee_attendance_adjustments.status', '=', 'ls.id')
            ->select('employee_attendance_adjustments.id as id', 'employee_attendance_adjustments.date as date', 'employee_attendance_adjustments.applied_reason as applied_reason', 'employee_attendance_adjustments.rejected_reason as rejected_reason', 'employee_attendance_adjustments.status as status_id', 'ls.name as status')
            ->where('employee_id', $employee_id)
            ->where('employee_type_id', 2);
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

    public function rider_attachments_check(Request $request)
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
            'city_id' => ['required', 'integer', 'digits_between:1,10'],
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $hub_id = City::where('id', $request->city_id)->select('hub_id')->first();
            $line_managers = $line_managers = Employee::leftjoin('cities as c', 'c.id', 'employees.city_id')
                ->leftjoin('cities as h', 'h.id', 'c.hub_id')
                ->where('is_line_manager', 1)
                ->where('department_id', 6)
                ->where('employees.city_id', $hub_id->hub_id)
                ->select(['employees.name', 'employees.trax_id', 'employees.id', 'h.name as hub']);
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

    public function leave_index_v2(Request $request)
    {
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
                $data['designation'] = "Rider";
                $data['department'] = "Operations";
                $data['approver_email'] = $employee->line_manager->email;
                $data['approver_name'] = $employee->line_manager->name;
                $data['user_type'] = 0;
                $data['total_leaves'] = $employee->leave_count;
                $availed_leaves = EmployeeLeave::where('employee_id', $employee->id)
                    ->whereIn('status', [2, 4, 6])->whereIn('leave_type', [1, 2, 3, 4])->count();
                $data['availed_leaves'] = $availed_leaves;
                return response()->json(['status' => 0, 'data' => $data, 'leave_types' => $leave_types]);
            }
            return response()->json(['status' => 1, 'message' => "Line Manager is not selected!"]);
        }
        return response()->json(['status' => 1, 'message' => "Employee not found!"]);
    }

    public function leave_apply_v2(Request $request)
    {
        $rules = [
            'from' => ['required'],
            'to' => ['required'],
            'reason' => ['required_if:leave_type,[1,5,6]', 'max:500'],
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
                    $diffDays = $from_date->diffInWeekdays($to_date, Carbon::setWeekendDays([Carbon::SATURDAY, Carbon::SUNDAY]));
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
                        if ($diffDays > $leave_type->count) {
                            return response()->json(['status' => 1, 'message' => 'Exceed Quota: Dear user, Your limit can\'t be exceed from ' . $leave_type->count . ' days']);
                        }
                    }
                    if ($request->leave_type == 3) {
                        if ($employee->employee_gender_id == 2) {
                            return response()->json(['status' => 1, 'message' => 'Your gender doesn\'t allow to apply this leave category.']);
                        }
                        if ($diffDays > $leave_type->count) {
                            return response()->json(['status' => 1, 'message' => 'Exceed Quota: Dear user, Your limit can\'t be exceed from ' . $leave_type->count . ' days']);
                        }
                    }
                    if ($request->leave_type == 4) {
                        if ($employee->religion_id != 1) {
                            return response()->json(['status' => 1, 'message' => 'Your are not allow to apply this leave category.']);
                        }
                        if ($diffDays > $leave_type->count) {
                            return response()->json(['status' => 1, 'message' => 'Exceed Quota: Dear user, Your limit can\'t be exceed from ' . $leave_type->count . ' days']);
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
                        $leave = EmployeeLeave::where('employee_id', $employee->id)->where('employee_type_id', 2)->whereIn('status', [1, 2]);
                        if ($leave->exists()) {
                            return response()->json(['status' => 1, 'message' => 'Leave Request Already Submitted & Pending for Approval']);
                        }
                        $leave_request = new EmployeeLeave();
                        $leave_request->employee_id = $employee->id;;
                        $leave_request->employee_type_id = 2;
                        $leave_request->reporter_id = $employee->line_manager->admin->id;
                        $leave_request->from = $request->from;
                        $leave_request->to = $request->to;
                        $leave_request->applied_reason = $request->reason;
                        $leave_request->leave_type = $request->leave_type;
                        $leave_request->save();
                        $message = "Leave Request submitted successfully";
                    }
                    $employee->save();
                    NotificationsController::app_notification(11, $request->rider_id, 2, $leave_request->id);
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
        if (!$request->has('rider_employee')) {
            return response()->json(['status' => 1, 'message' => "No Leave Found!"]);
        }
        $rider_employee = $request->rider_employee;
        $employee_leaves = EmployeeLeave::join('leave_statuses as ls', 'employee_leaves.status', '=', 'ls.id')
            ->join('leave_types as lt', 'employee_leaves.leave_type', '=', 'lt.id')
            ->select('employee_leaves.employee_id as employee_id', 'employee_leaves.id as id', 'employee_leaves.from as from', 'employee_leaves.to as to', 'employee_leaves.applied_reason as applied_reason', 'employee_leaves.rejected_reason as rejected_reason', 'employee_leaves.status as status_id', 'ls.name as status', 'lt.name as leave_type', 'lt.id as leave_type_id')
            ->where('employee_id', $rider_employee)
            ->where('employee_type_id', 2);
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
                    $working_days = $employee_leave->employee->department->working_days;
                    $start_date = Carbon::createFromFormat('Y-m-d', $employee_leave->from);
                    $end_date = Carbon::createFromFormat('Y-m-d', $employee_leave->to);
                    if ($working_days == 1) {
                        $diffDays = $start_date->diffInWeekdays($end_date, Carbon::setWeekendDays([Carbon::SUNDAY]));
                    } else {
                        $diffDays = $start_date->diffInWeekdays($end_date, Carbon::setWeekendDays([Carbon::SATURDAY, Carbon::SUNDAY]));
                    }
                    $datum['days_count'] = $diffDays + 1;
                } else {
                    $datum['days_count'] = 1;
                }
                $data[] = $datum;
            }
            return response()->json(['status' => 0, 'response' => $data]);
        }
        return response()->json(['status' => 1, 'message' => "No Leave Found!"]);
    }

    public function rider_incentive_v4(Request $request)
    {
        $rider_id = $request->rider_id;
        $from_date = $request->get('from_date');
        $to_date = $request->get('to_date');
        if ($to_date) {
            $rider_delivery_incentives = RiderIncentiveDelivery::join('rider_incentive_delivery_courier_types as ct', 'ct.id', '=', 'rider_incentive_deliveries.courier_type_id')
                ->join('rider_incentive_delivery_shipment_types as st', 'st.id', '=', 'rider_incentive_deliveries.shipment_type_id')
                ->join('rider_incentive_delivery_shipment_weight_types as wt', 'wt.id', '=', 'rider_incentive_deliveries.shipment_weight_type_id')
                ->where('rider_incentive_deliveries.rider_id', $rider_id)->whereBetween('rider_incentive_deliveries.date', [$from_date, $to_date])
                ->select('rider_incentive_deliveries.*', 'st.name as shipment_type', 'ct.name as courier_type', 'wt.name as weight_type');

            $rider_pickup_incentives = RiderIncentivePickup::where('rider_id', $rider_id)->whereBetween('date', [$from_date, $to_date]);
        } else {
            $rider_delivery_incentives = RiderIncentiveDelivery::join('rider_incentive_delivery_courier_types as ct', 'ct.id', '=', 'rider_incentive_deliveries.courier_type_id')
                ->join('rider_incentive_delivery_shipment_types as st', 'st.id', '=', 'rider_incentive_deliveries.shipment_type_id')
                ->join('rider_incentive_delivery_shipment_weight_types as wt', 'wt.id', '=', 'rider_incentive_deliveries.shipment_weight_type_id')
                ->where('rider_incentive_deliveries.rider_id', $rider_id)->whereDate('date', $from_date)
                ->select('rider_incentive_deliveries.*', 'st.name as shipment_type', 'ct.name as courier_type', 'wt.name as weight_type');

            $rider_pickup_incentives = RiderIncentivePickup::where('rider_id', $rider_id)->whereDate('date', $from_date);
        }
        if ($rider_delivery_incentives->exists() || $rider_pickup_incentives->exists()) {
            $data = array();
            $pickup_shipment = NULL;
            $pickup_incentive = NULL;
            $delivered_shipment = NULL;
            $delivered_incentive = NULL;
            if ($rider_delivery_incentives->exists()) {
                $rider_delivery_incentives = $rider_delivery_incentives->get();
                foreach ($rider_delivery_incentives as $rider_delivery_incentive) {
                    $datum = array();
                    $datum['date'] = date('Y/m/d', strtotime($rider_delivery_incentive->date));
                    $datum['shipments'] = (string)$rider_delivery_incentive->shipments;
                    $datum['rate'] = (string)$rider_delivery_incentive->rate;
                    $datum['incentive'] = (string)$rider_delivery_incentive->incentive;
                    $datum['shipment_type'] = $rider_delivery_incentive->shipment_type;
                    $datum['weight_type'] = $rider_delivery_incentive->weight_type;
                    $datum['type'] = "1";
                    $data[$rider_delivery_incentive->date]["delivered"][] = $datum;
                    $delivered_shipment += $rider_delivery_incentive->shipments;
                    $delivered_incentive += $rider_delivery_incentive->incentive;
                }
            }
            if ($rider_pickup_incentives->exists()) {
                $rider_pickup_incentives = $rider_pickup_incentives->get();
                foreach ($rider_pickup_incentives as $rider_pickup_incentive) {
                    $datum = array();
                    $datum['date'] = date('Y/m/d', strtotime($rider_pickup_incentive->date));
                    $datum['shipments'] = (string)$rider_pickup_incentive->shipments;
                    $datum['rate'] = (string)$rider_pickup_incentive->rate;
                    $datum['incentive'] = (string)$rider_pickup_incentive->incentive;
                    $datum['shipment_type'] = NULL;
                    $datum['weight_type'] = NULL;
                    $datum['type'] = "2";
                    $data[$rider_pickup_incentive->date]["picked"][] = $datum;
                    $pickup_shipment += $rider_pickup_incentive->shipments;
                    $pickup_incentive += $rider_pickup_incentive->incentive;
                }
            }
            return response()->json(["status" => 0, "incentives" => $data, "delivered_count" => $delivered_shipment, "delivered_incentive" => $delivered_incentive, "pickup_incentive" => $pickup_incentive, "pickup_count" => $pickup_shipment]);
        } else {
            return response()->json(["status" => 1, "message" => "Incentives Not Found"]);
        }
    }

    public function delivery_summary_multiple_v6(Request $request)
    {
        $rider_id = $request->rider_id;

        $delivery_notes = DeliveryNote::where('rider_id', $rider_id)->where('status', 0)->where('pending_status', 0);

        if ($delivery_notes->exists()) {
            $delivery_notes = $delivery_notes->get();
            $delivery_otp = 0;
            $settings = GlobalSettings::where('type', 'delivery_otp');
            $all_shippers = GlobalSettings::where('type', 'non_cod_otp_all_shippers');
            if ($settings->exists()) {
                $settings = $settings->first();
                $delivery_otp = $settings->setting_value;
            }

            if ($all_shippers->exists()) {
                $all_shippers = $all_shippers->first();
                if ($all_shippers->setting_value == 1) {
                    $excluded_shippers = GlobalSettings::where('type', 'non_cod_otp_excluded_shippers');
                    if ($excluded_shippers->exists()) {
                        $excluded_shippers = $excluded_shippers->first();
                        $excluded_shippers = array_map('intval', explode(',', $excluded_shippers->text));
                    } else {
                        $excluded_shippers = [];
                    }
                } else {
                    $only_shippers = GlobalSettings::where('type', 'non_cod_otp_only_shippers');
                    if ($only_shippers->exists()) {
                        $only_shippers = $only_shippers->first();
                        $only_shippers = array_map('intval', explode(',', $only_shippers->text));
                    } else {
                        $only_shippers = [];
                    }
                }
            }

            $nodes = array();

            foreach ($delivery_notes as $delivery_note) {

//                if ($delivery_note->shipments_count == $delivery_note->delivered_shipments) {
//                    continue;
//                }
                $information = array();

                $information['delivery_note_id'] = $delivery_note->id;
                $information['assigned_date'] = $delivery_note->created_at->toDateTimeString();
                $information['no_of_parcels'] = $delivery_note->shipments_count;
                $information['delivery_note_otp'] = $delivery_note->otp;

                $information['pending_shipment_count'] = 0;
                $pending_shipments_count = DeliveryNoteShipment::where('delivery_note_id', $delivery_note->id)->where('status', 0)->count();
                if($pending_shipments_count == 0){
                    $information['pending_shipment_count'] = 1;
                }

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
                    $replacement_parcel_image = null;
                    $shipment_data = $delivery_note_shipment->shipment;
                    $shipment_id = $shipment_data->id;
                    $refusal_otp = null;
                    $dbf_otp = null;
                    $shipment_otp = ShipmentOtp::where('shipment_id', $shipment_id);
                    if ($shipment_otp->exists()) {
                        $shipment_otp = $shipment_otp->first();
                        $refusal_otp = $shipment_otp->otp;
                        $dbf_otp = $shipment_otp->dbf_otp;
                    }
                    $payment_mode = $shipment_data->payment_mode_id;
                    $tracking_number = $shipment_data->tracking_number;
                    $consignee_name = $shipment_data->consignee_name;
                    $consignee_address = $shipment_data->consignee_address;
                    $booking_type = $shipment_data->booking_type_id;
                    $consignee_phone = $shipment_data->consignee_phone_number_1;
                    $shipper_name = $shipment_data->user->name;
                    if ($shipment_data->consignee_phone_number_2 != null) {
                        $consignee_phone .= ' / ' . $shipment_data->consignee_phone_number_2;
                    }

                    if (in_array($shipment_data->user->id, [10358, 10104, 15587, 17363, 15636, 16292])) {
                        $relation_lists = DeliveryRelation::select('id', 'name')->get();
                    } else {
                        $relation_lists = DeliveryRelation::where('id', '<>', 7)->select('id', 'name')->get();
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
                    $deliveries['distribution'] = 0;
                    $shipment_status_count = ShipmentsJourney::where('shipment_id', $shipment_id)
                        ->where('shipper_status_id', 5)->count();

                    if ($shipment_data->user_id == 10354 && $booking_type == 1) {
                        $deliveries['distribution'] = 1;
                        $items = array();
                        $products = ShipmentDistributionProduct::where('shipment_id', $shipment_id);
                        if ($products->exists()) {
                            $products = $products->get();
                            foreach ($products as $product) {
                                $qty = $product->items * $product->units_per_item;
                                $per_unit_price = $product->price / $qty;
                                $items[] = ['pid' => $product->id, 'product_name' => $product->item->name, 'qty' => $qty, 'per_unit_price' => $per_unit_price];
                            }
                        }
                        $deliveries['distribution_items'] = $items;
                    } elseif ($booking_type == 3) {
                        $product = array();
                        $parcel = ShipmentItem::where('shipment_id', $shipment_id);
                        if ($parcel->exists()) {
                            $parcel = $parcel->get();
                            foreach ($parcel as $item) {
                                $product[] = ['pid' => $item->id, 'type' => $item->product->product_name, 'description' => ($item->description == '') ? ' - ' : $item->description, 'price' => $item->price];
                            }
                        }
                        $deliveries['try_n_buy_items'] = $product;
                        $deliveries['try_and_buy_fees'] = (float)$shipment_data->try_and_buy_fees;
                    } elseif ($booking_type == 2) {
                        $replacement_parcel = ShipmentReplacementParcelImage::where('shipment_id', $shipment_id);
                        if ($replacement_parcel->exists()) {
                            $replacement_parcel = $replacement_parcel->first();
                            $replacement_parcel_image = $replacement_parcel->picture_path;
                        }
                    }
                    if ($shipment_status_count > 1) {
                        $deliveries['rcp'] = 1;
                    } else {
                        $deliveries['rcp'] = 0;
                    }
                    $deliveries['shipment_id'] = $shipment_id;

                    $deliveries['tracking_number'] = $tracking_number;

                    if ($shipment_data->shipper_status_id == 5) {
                        $shipment_reattempt = ShipmentsJourney::where('shipment_id', '=', $deliveries['shipment_id'])
                            ->orderBy('id', 'desc')
                            ->skip(1)
                            ->first();
                    }
                    else {
                        $shipment_reattempt = NULL;
                    }

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
                    $deliveries['shipment_reattempt'] = ($shipment_reattempt && $shipment_reattempt->shipper_status_id == 13) ? 1 : 0;
                    $deliveries['shipper'] = $shipper_name;
                    $deliveries['refusal_otp'] = (string)$refusal_otp;
                    $deliveries['dbf_otp'] = ($dbf_otp != null) ? (string)$dbf_otp : null;
                    $deliveries['ccd'] = ($payment_mode == 2) ? 1 : 0;
                    $deliveries['replacement_parcel_image'] = $replacement_parcel_image;
                    $deliveries['relation_list'] = $relation_lists;
                    if ($delivery_otp == 1) {
                        if ($all_shippers->setting_value == 1) {
                            if (count($excluded_shippers) > 0) {
                                $deliveries['delivery_otp'] = (in_array($shipment_data->user_id, $excluded_shippers)) ? 1 : 0;
                            } else {
                                $deliveries['delivery_otp'] = 0;
                            }
                        } else {
                            if (count($only_shippers) > 0) {
                                $deliveries['delivery_otp'] = (in_array($shipment_data->user_id, $only_shippers)) ? 0 : 1;
                            } else {
                                $deliveries['delivery_otp'] = 1;
                            }
                        }
                    } else {
                        $deliveries['delivery_otp'] = 1;
                    }

                    $one_link_payment = OneLinkOutForDeliveryShipmentPayment::where('shipment_id', $shipment_id)->where('delivery_note_id', $delivery_note->id);
                    $fintech_payment  = FintechPaymentDetails::join('trax_pay_transactions','fintech_payment_details.trax_pay_id','trax_pay_transactions.id')
                    ->where('trax_pay_transactions.shipment_id',$shipment_id)
                    ->where('trax_pay_transactions.delivery_note_id',$delivery_note->id);
                    $deliveries['payment_link'] = "";
                    //fintech code 
                    if($one_link_payment->exists()){
                        $deliveries['amount_paid'] = 1;
                    }
                    else if($fintech_payment->exists()){
                        $deliveries['amount_paid'] = 1;
                    }
                    else{
                        $deliveries['amount_paid'] = 0;
                        $payment_link = TraxPayTransaction::where('shipment_id',$shipment_id)->first();
                        if(!empty($payment_link)){
                            $deliveries['payment_link'] = $payment_link->link;   
                        }   
                    }
                    // $deliveries['amount_paid'] = ($one_link_payment->exists()) ? 1 : 0;
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

    public function delivery_note_index(Request $request)
    {
        $rider_id = $request->rider_id;
        $rider = Rider::find($rider_id);
        if ($rider->operation_rider_id == 1) {
            $hub_id = $request->rider_hub;

            $routes = Route::leftjoin('cities as c', 'routes.city_id', '=', 'c.id')
                ->leftjoin('cities as h', 'c.hub_id', '=', 'h.id')
                ->where('routes.status', 1)
                ->where('h.id', $hub_id);

            $routes = $routes->select('routes.*')->get();
            $datetime = Carbon::createFromFormat('Y-m-d H:i:s', '2021-05-18 23:59:00');
            if ($hub_id == 202) {
                $delivery_note = DeliveryNote::join('riders as r', 'r.id', '=', 'delivery_notes.rider_id')
                    ->where('r.id', $rider_id)
                    ->where('delivery_notes.cash_collection_status', 0)
                    ->where('delivery_notes.total_cod_amount', '>', 0)
                    ->where('delivery_notes.status', '!=', 4)
                    ->whereDate('delivery_notes.created_at', '>', $datetime)
                    ->whereDate('delivery_notes.created_at', '<', Carbon::today())
                    ->where('r.operation_rider_id', 1);
            } else {
                $delivery_note = DeliveryNote::join('riders as r', 'r.id', '=', 'delivery_notes.rider_id')
                    ->where('r.id', $rider_id)
                    ->where('delivery_notes.dncc_status', 0)
                    ->where('delivery_notes.total_cod_amount', '>', 0)
                    ->where('delivery_notes.status', '!=', 4)
                    ->whereDate('delivery_notes.created_at', '>', $datetime)
                    ->whereDate('delivery_notes.created_at', '<', Carbon::today())
                    ->where('r.operation_rider_id', 1);
            }


            if ($delivery_note->exists()) {
                $delivery_note_request = DeliveryNoteRequests::where('rider_id', $rider_id)->where('status', 2)->where('completed', 0)->latest()->first();
                if ($delivery_note_request) {
                    $delivery_note_request->completed = 1;
                    $delivery_note_request->save();
                    $ccd_rider = $rider->ccd;
                    return response()->json(['status' => 0, 'routes' => $routes, 'ccd_rider' => $ccd_rider]);
                } else {
                    return response()->json(['status' => 1, 'message' => "You are not allowed to create delivery note, because previous delivery note is not been completed"]);
                }
            } else {
                $ccd_rider = $rider->ccd;
                return response()->json(['status' => 0, 'ccd_rider' => $ccd_rider, 'routes' => $routes]);
            }
        } else {
            return response()->json(['status' => 1, 'message' => "You are not allowed to create delivery note from bolt"]);
        }
    }

    public function get_delivery_shipment_details(Request $request)
    {
        $rules = [
            'tracking' => ['required'],
        ];

        $validate = Validator::make($request->all(), $rules);
        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            if (Shipment::where('tracking_number', $request->tracking)->exists()) {
                $rider_hub = $request->rider_hub;
                $rider_id = $request->rider_id;
                /*if ($request->tracking != '' && $request->rider_id != '') {
                    $tracking_number = $request->tracking;
                    $

                    $rider_default_type = Rider::where('id', $rider_id)->select('rider_category_id')->first();

                    $shipment = Shipment::where('tracking_number', $tracking_number)->select('actual_weight', 'consignee_city_id')->first();


                    $weight = GlobalSettings::where('type', 'light_heavy_weight_for_shipment')->select('text')->first();

                    if ($shipment->actual_weight > $weight->text) {
                        $rider_bypass_type = RiderCategoryByPass::where('rider_id', $rider_id)->where('status', 1)->where('rider_category_id', 2)->select('rider_category_id', 'id')->latest()->first();

                        if ($rider_bypass_type) {
                            $rider_bypass_id = $rider_bypass_type->id;
                            if ($rider_bypass_type->rider_category_id != 2) {
                                if ($rider_default_type->rider_category_id == 1) {
                                    return response()->json(['status' => 1, 'message' => 'Shipment is heavy weighted and the selected rider type is light weighted !']);
                                }
                            }
                        } elseif ($rider_default_type->rider_category_id == 1) {
                            return response()->json(['status' => 1, 'message' => 'Shipment is heavy weighted and the selected rider type is light weighted !']);
                        }
                    } elseif ($shipment->actual_weight <= $weight->text) {
                        $rider_bypass_type = RiderCategoryByPass::where('rider_id', $rider_id)->where('status', 1)->where('rider_category_id', 1)->select('rider_category_id', 'id')->latest()->first();

                        if ($rider_bypass_type) {
                            $rider_bypass_id = $rider_bypass_type->id;
                            if ($rider_bypass_type->rider_category_id != 1) {
                                if ($rider_default_type->rider_category_id == 2) {
                                    return response()->json(['status' => 1, 'message' => 'Shipment is light weighted and the selected rider type is heavy weighted !']);
                                }
                            }
                        } elseif ($rider_default_type->rider_category_id == 2) {
                            return response()->json(['status' => 1, 'message' => 'Shipment is light weighted and the selected rider type is heavy weighted !']);
                        }
                    }
                }*/
                $flag = true;
                $pending_status = array(2, 4, 6, 7, 8, 9, 10, 13, 15, 49, 55, 59);
                if ($request->tracking != '') {
                    $shipment = Shipment::where('tracking_number', $request->tracking)->whereIn('shipper_status_id', $pending_status);
                    $remarks = ' - ';
                    $status = ' - ';
                    $rider_name = ' - ';
                    if ($shipment->exists()) {
                        $shipment = $shipment->first();
                        $dispute_check = CheckDisputeShipmentsController::check($shipment->id);
                        if (!$dispute_check) {
                            return response()->json(['status' => 1, 'message' => 'Shipment is in Dispute! For further assistance, please contact QA (CX)']);
                        }

                        if ($shipment->shipment_detail()->exists()) {
                            if ($shipment->shipment_detail->is_open == 1) {
                                $is_open_box = 1;
                            } else {
                                $is_open_box = 0;
                            }
                        } else {
                            $is_open_box = 0;
                        }
                        $on_hold_shipment = ShipmentOnHold::where('shipment_id', $shipment->id)->where('status', 1);
                        if ($on_hold_shipment->exists()) {
                            if (!in_array(Auth::id(), [10, 288, 423])) {
                                $on_hold_shipment = $on_hold_shipment->first();
                                $delivery_date = Carbon::parse($on_hold_shipment->delivery_date);
                                $today = Carbon::today();
                                if ($delivery_date > $today) {
                                    $delivery_date = $delivery_date->toFormattedDateString();
                                    return response()->json(['status' => 1, 'message' => 'Shipment is marked as On-Hold until ' . $delivery_date]);
                                }
                            }
                        }
                        if ($shipment->packaging_material_request == 1) {
                            $packaging_material_request = PackagingMaterialRequest::where('tracking_number', $shipment->tracking_number)->first();
                            if ($packaging_material_request != null) {
                                if ($packaging_material_request->status_id != 3) {
                                    return response()->json(['status' => 1, 'message' => 'Packaging Material Request is not dispatched yet!']);
                                }
                            }
                            $packaging_material_request_stock = WarehouseStockRequest::where('tracking_number', $shipment->tracking_number)->first();
                            if ($packaging_material_request_stock != null) {
                                if ($packaging_material_request_stock->status_id != 3) {
                                    return response()->json(['status' => 1, 'message' => 'Warehouse Stock Request is not dispatched yet!']);
                                }
                            }
                        }

                        $admin_hub = City::find($shipment->consignee_city->hub_id)->id;

                        // rider assigned hub setting
                        $rider_assigned_hub = RiderAssignedHubForDeliveryNote::where('rider_id',$request->rider_id);
                        if ($rider_assigned_hub->exists())
                        {
                            $rider_assigned_hub = $rider_assigned_hub->first();
                            $rider_assigned_hubs = $rider_assigned_hub->hubs;
                            $rider_assigned_hubs = explode(',',$rider_assigned_hubs);

                            if (in_array($admin_hub,$rider_assigned_hubs))
                            {
                                $flag = true;
                            }
                            elseif ($rider_hub == $admin_hub)
                            {
                                $flag = true;
                            }
                            else
                            {
                                $flag = false;
                            }
                        }
                        elseif ($rider_hub == $admin_hub)
                        {
                            $flag = true;
                        }
                        else
                        {
                            $flag = false;
                        }
                        // rider assigned hub setting end

                        if ($flag) {
                            $old_delivery_note_id = DeliveryNoteShipment::join('delivery_notes', 'delivery_notes.id', '=', 'delivery_note_shipments.delivery_note_id')->where('delivery_note_shipments.shipment_id', $shipment->id)->where('delivery_notes.status', '!=', 4)->orderBy('delivery_note_id', 'desc');
                            if ($old_delivery_note_id->exists()) {
                                $old_delivery_note_id = $old_delivery_note_id->first();
                                $delivery_note_rider = DeliveryNote::where('id', $old_delivery_note_id->delivery_note_id)->first();
                                $rider_name = ($delivery_note_rider->rider_id != null) ? $delivery_note_rider->rider->name : " - ";
                                $is_updateable = DeliveryNoteShipment::where('delivery_note_id', $old_delivery_note_id->delivery_note_id)->where('status', 0)->count();
                            } else {
                                $is_updateable = 0;
                            }


                            if ($is_updateable == 0) {
                                if (($shipment->consignee_city->hub_id != $shipment->pickup_address->city->hub_id) && $shipment->shipper_status_id == 2) {
                                    return response()->json(['status' => 1, 'message' => 'Cargo not arrived at destination center!']);
                                } else if ($shipment->shipper_status_id == 49) {
                                    $misroute_history = MisroutedHistory::where('shipment_id', $shipment->id);
                                    if ($misroute_history->exists()) {
                                        $misroute_history = $misroute_history->latest()->first();
                                        if ($misroute_history->old_consignee_city->hub_id != $misroute_history->new_consignee_city->hub_id) {
                                            return response()->json(['status' => 1, 'message' => 'Shipment needs to be moved through cargo!']);
                                        }
                                    } else {
                                        return response()->json(['status' => 1, 'message' => 'Shipment Not found!']);
                                    }
                                } else if ($shipment->shipper_status_id == 55) {
                                    $request_history = InterceptReBookRequestHistory::where('shipment_id', $shipment->id);
                                    if ($request_history->exists()) {
                                        $request_history = $request_history->first();
                                        if ($request_history->old_consignee_city->hub_id != $request_history->new_consignee_city->hub_id) {
                                            return response()->json(['status' => 1, 'message' => 'Shipment needs to be moved through cargo!']);
                                        }
                                    } else {
                                        return response()->json(['status' => 1, 'message' => 'Shipment Not found!']);
                                    }
                                }

                                if ($request->has('hub_id')) {
                                    $hub_id = $shipment->consignee_city->hub_id;

                                    // rider assigned hub setting
                                    $rider_assigned_hub = RiderAssignedHubForDeliveryNote::where('rider_id',$request->rider_id);
                                    if ($rider_assigned_hub->exists())
                                    {
                                        $rider_assigned_hub = $rider_assigned_hub->first();
                                        $rider_assigned_hubs = $rider_assigned_hub->hubs;
                                        $rider_assigned_hubs = explode(',',$rider_assigned_hubs);

                                        if (in_array($hub_id,$rider_assigned_hubs))
                                        {
                                            $flag = true;
                                        }
                                        elseif ($request->hub_id == $hub_id)
                                        {
                                            $flag = true;
                                        }
                                        else
                                        {
                                            $flag = false;
                                        }
                                    }
                                    elseif ($request->hub_id == $hub_id)
                                    {
                                        $flag = true;
                                    }
                                    else
                                    {
                                        $flag = false;
                                    }
                                    // rider assigned hub setting end
                                    
                                    if ($flag) {
                                        if (!$request->has('pieces_confirm')) {
                                            if ($shipment->booking_type_id == 1 && $shipment->pieces > 1) {
                                                $details = array();
                                                $shipment_pieces = ShipmentPiece::where('shipment_id', $shipment->id)->pluck('tracking_number')->toArray();

                                                $details['id'] = $shipment->id;
                                                $details['tracking_number'] = $shipment->tracking_number;
                                                $details['pieces_count'] = $shipment->pieces;
                                                $details['pieces_tracking_numbers'] = $shipment_pieces;
                                                return response()->json(['status' => 0, 'message' => 'Shipment Piece(s) found!', 'details' => $details]);
                                            }
                                        }
                                        $destination = $shipment->consignee_city->name;
                                        $hub = City::find($shipment->consignee_city->hub_id)->id;
                                        $service = $shipment->booking_type->booking_type;
                                        $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id);
                                        if ($shipment_journey->exists()) {
                                            $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->select('shipper_status_id', 'remarks')->orderBy('id', 'DESC')->first();

                                            $remarks = ($shipment_journey->remarks != '') ? $shipment_journey->remarks : ' - ';
                                            $status_id = ($shipment_journey->shipper_status_id) ? $shipment_journey->shipper_status_id : '';
                                            if ($status_id != '') {
                                                $status_name = ShipmentStatus::where('id', $status_id)->select('name')->first();
                                                $status = $status_name->name;
                                            } else {
                                                $status = ' - ';
                                            }
                                        }
                                        $complaint_row = 0;
                                        if (CrmRequest::where('shipment_id', $shipment->id)->where('case_nature_id', 1)->whereIn('status_id', [2, 3, 5])->exists()) {
                                            $complaint_row = 1;
                                        }
                                        ShipmentScanningJourneyController::add($shipment->id, 4, 5, $request->rider_id, null, null);
                                        $consolidation_details = DeliveryController::check_consolidation($shipment->id);
                                        $consolidation_flag = FALSE;

                                        if ($consolidation_details) {
                                            $consolidation_flag = TRUE;
                                        }
                                        $intercept = false;
                                        if (InterceptReBookRequestHistory::where('shipment_id', $shipment->id)->exists()) {
                                            $intercept = true;
                                        }
                                        $amount_check = false;
                                        $amount_log = ChangeShipmentAmountLog::where('shipment_id', $shipment->id);
                                        if ($amount_log->exists()) {
                                            $amount_log = $amount_log->first();
                                            $amount_check = true;
                                        }
                                        $success_message = null;
                                        if ($intercept == true || $amount_check == true) {
                                            $success_message .= 'This Shipment with Tracking Number: ' . $shipment->tracking_number . ' has following changes:' . PHP_EOL;
                                        }
                                        if (($intercept == true && ($shipment->intercept_history->old_amount != $shipment->intercept_history->new_amount)) || ($amount_check == true && ($amount_log->old_amount != $amount_log->new_amount))) {
                                            if ($amount_check) {
                                                $cod_change = $amount_log->new_amount;
                                            } else {
                                                $cod_change = $shipment->intercept_history->new_amount;
                                            }
                                            $success_message .= '   COD : ' . $cod_change . PHP_EOL;
                                        }
                                        if (($intercept == true && ($shipment->intercept_history->old_consignee_address != $shipment->intercept_history->new_consignee_address))) {
                                            $address_change = $shipment->intercept_history->new_consignee_address;
                                            $success_message .= '    Address : ' . $address_change . PHP_EOL;
                                        }
                                        if (($intercept == true && ($shipment->intercept_history->old_consignee_phone_number_1 != $shipment->intercept_history->new_consignee_phone_number_1))) {
                                            $phone_one_change = $shipment->intercept_history->new_consignee_phone_number_1;
                                            $success_message .= '    Phone : ' . $phone_one_change . PHP_EOL;
                                        }
                                        $ccd_shipment = 0;
                                        if ($shipment->payment_mode_id == 2) {
                                            $success_message .= 'This shipment ' . $shipment->tracking_number . ' requires POS machine for Card swiping on delivery, please ensure that the rider has the training for using POS machine and the necessary arrangements (paper rolls and ink ready) for printing receipts.';
                                            $ccd_shipment = 1;
                                        }
                                        return response()->json(['status' => 0, 'shipment_details' => ['shId' => $shipment->id, 'tracking_number' => $shipment->tracking_number, 'destination' => $destination, 'hub' => $hub, 'consignee_name' => $shipment->consignee_name, 'phone' => $shipment->consignee_phone_number_1, 'address' => $shipment->consignee_address, 'amount' => number_format($shipment->amount), 'service_type' => $service, 'shipment_status' => $status, 'rider_name' => $rider_name, 'remarks' => $remarks, 'crm_row' => $complaint_row, 'consolidation_flag' => $consolidation_flag, 'consolidation_details' => $consolidation_details, 'is_open_box' => $is_open_box, 'ccd_shipment' => $ccd_shipment], 'message' => $success_message]);
                                    } else {
                                        return response()->json(['status' => 1, 'message' => 'Different hub, Select shipments from same hub!', 'hub_old' => $request->hub_id, 'newHub' => $hub_id]);
                                    }
                                } else {
                                    if (!$request->has('pieces_confirm')) {
                                        if ($shipment->booking_type_id == 1 && $shipment->pieces > 1) {
                                            $details = array();
                                            $shipment_pieces = ShipmentPiece::where('shipment_id', $shipment->id)->pluck('tracking_number')->toArray();

                                            $details['id'] = $shipment->id;
                                            $details['tracking_number'] = $shipment->tracking_number;
                                            $details['pieces_count'] = $shipment->pieces;
                                            $details['pieces_tracking_numbers'] = $shipment_pieces;
                                            return response()->json(['status' => 0, 'message' => 'Shipment Piece(s) found!', 'details' => $details]);
                                        }
                                    }
                                    $destination = $shipment->consignee_city->name;
                                    $hub = City::find($shipment->consignee_city->hub_id)->id;
                                    $service = $shipment->booking_type->booking_type;
                                    $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id);
                                    if ($shipment_journey->exists()) {
                                        $shipment_journey = $shipment_journey->select('shipper_status_id', 'remarks')->orderBy('id', 'DESC')->first();

                                        $remarks = ($shipment_journey->remarks != '') ? $shipment_journey->remarks : ' - ';
                                        $status_id = ($shipment_journey->shipper_status_id) ? $shipment_journey->shipper_status_id : '';
                                        if ($status_id != '') {
                                            $status_name = ShipmentStatus::where('id', $status_id)->select('name')->first();
                                            $status = $status_name->name;
                                        } else {
                                            $status = ' - ';
                                        }
                                    }
                                    $complaint_row = 0;
                                    if (CrmRequest::where('shipment_id', $shipment->id)->where('case_nature_id', 1)->whereIn('status_id', [2, 3, 5])->exists()) {
                                        $complaint_row = 1;
                                    }
                                    ShipmentScanningJourneyController::add($shipment->id, 4, 5, $rider_id, null, null);
                                    $consolidation_details = DeliveryController::check_consolidation($shipment->id);

                                    $consolidation_flag = FALSE;

                                    if ($consolidation_details) {
                                        $consolidation_flag = TRUE;
                                    }

                                    $intercept = false;
                                    if (InterceptReBookRequestHistory::where('shipment_id', $shipment->id)->exists()) {
                                        $intercept = true;
                                    }
                                    $amount_check = false;
                                    $amount_log = ChangeShipmentAmountLog::where('shipment_id', $shipment->id);
                                    if ($amount_log->exists()) {
                                        $amount_log = $amount_log->first();
                                        $amount_check = true;
                                    }
                                    $success_message = null;
                                    if ($intercept == true || $amount_check == true) {
                                        $success_message .= 'This Shipment with Tracking Number: ' . $shipment->tracking_number . ' has following changes:' . PHP_EOL;
                                    }
                                    if (($intercept == true && ($shipment->intercept_history->old_amount != $shipment->intercept_history->new_amount)) || ($amount_check == true && ($amount_log->old_amount != $amount_log->new_amount))) {
                                        if ($amount_check) {
                                            $cod_change = $amount_log->new_amount;
                                        } else {
                                            $cod_change = $shipment->intercept_history->new_amount;
                                        }
                                        $success_message .= '   COD : ' . $cod_change . PHP_EOL;
                                    }
                                    if (($intercept == true && ($shipment->intercept_history->old_consignee_address != $shipment->intercept_history->new_consignee_address))) {
                                        $address_change = $shipment->intercept_history->new_consignee_address;
                                        $success_message .= '    Address : ' . $address_change . PHP_EOL;
                                    }
                                    if (($intercept == true && ($shipment->intercept_history->old_consignee_phone_number_1 != $shipment->intercept_history->new_consignee_phone_number_1))) {
                                        $phone_one_change = $shipment->intercept_history->new_consignee_phone_number_1;
                                        $success_message .= '    Phone : ' . $phone_one_change . PHP_EOL;
                                    }
                                    $ccd_shipment = 0;
                                    if ($shipment->payment_mode_id == 2) {
                                        $ccd_shipment = 1;
                                        $success_message .= 'This shipment ' . $shipment->tracking_number . ' requires POS machine for Card swiping on delivery, please ensure that the rider has the training for using POS machine and the necessary arrangements (paper rolls and ink ready) for printing receipts.';
                                    }
                                    return response()->json(['status' => 0, 'shipment_details' => ['shId' => $shipment->id, 'tracking_number' => $shipment->tracking_number, 'destination' => $destination, 'hub' => $hub, 'consignee_name' => $shipment->consignee_name, 'phone' => $shipment->consignee_phone_number_1, 'address' => $shipment->consignee_address, 'amount' => number_format($shipment->amount), 'service_type' => $service, 'shipment_status' => $status, 'rider_name' => $rider_name, 'remarks' => $remarks, 'crm_row' => $complaint_row, 'consolidation_flag' => $consolidation_flag, 'consolidation_details' => $consolidation_details, 'is_open_box' => $is_open_box, 'ccd_shipment' => $ccd_shipment], 'message' => $success_message]);
                                }
                            } else {
                                return response()->json(['status' => 1, 'message' => 'This Shipment is already in an unverified delivery note!']);
                            }
                        } else {
                            return response()->json(['status' => 1, 'message' => 'This Shipment doesn\'t belongs to your assigned hubs!']);
                        }
                    } else {
                        return response()->json(['status' => 1, 'message' => 'This Shipment is not ready for delivery yet or already in delivery note, please check tracking!']);
                    }
                }
            } else {
                return response()->json(['status' => 1, 'message' => 'Invalid Tracking Number']);
            }
        }
    }

    public function get_delivery_piece_details(Request $request)
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

    public function delivery_note_otp_generation(Request $request)
    {
        $settings = GlobalSettings::where('type', 'rider_otp');
        $environment = config('app.env');

        if ($settings->exists()) {
            $settings = $settings->first();
            if ($settings->setting_value == 1) {
                if ($environment == 'production' || $environment == 'staging') {
                    $rider_id = $request->get('rider_id');
                    $rider = Rider::find($rider_id);
                    if ($rider) {
                        $otp = mt_rand(100000, 999999);
                        $rider->delivery_note_otp = $otp;
                        $rider->otp_date = Carbon::now();
                        $rider->save();
                        NotificationsController::app_notification(9, $rider->id, 2, $otp);
                        NotificationsController::send(144, $rider, $otp);
                        return response()->json(['status' => 0, 'generate_message' => "Otp Generated"]);
                    } else {
                        return response()->json(['status' => 1, 'message' => 'Rider not found!']);
                    }
                } else {
                    return response()->json(['status' => 0, 'generate_message' => "Otp Generated"]);
                }
            } else {
                return response()->json(['status' => 0, 'verified_message' => 'Otp Verified']);
            }
        } else {
            return response()->json(['status' => 0, 'verified_message' => 'Otp Verified']);
        }
    }

    public function delivery_note_otp_verification(Request $request)
    {
        $environment = config('app.env');
        if ($environment == 'production' || $environment == 'staging') {
            $rider = Rider::find($request->rider_id);
            if ($rider) {
                if ($rider->delivery_note_otp == $request->otp) {
                    return response()->json(['status' => 0, 'verified_message' => 'Otp Verified']);
                } else {
                    return response()->json(['status' => 1, 'message' => 'Invalid OTP']);
                }
            } else {
                return response()->json(['status' => 1, 'message' => 'Rider not selected!']);
            }
        } else {
            return response()->json(['status' => 0, 'verified_message' => 'Otp Verified']);
        }
    }

    public function create_delivery_note(Request $request)
    {
        $rules = [
            'hub_id' => ['required'],
            'selected_route_id' => ['required'],
            'shipment_ids' => ['required'],
            'open_box_ids' => ['nullable'],
            'notification_ids' => ['nullable'],
            'rider_info_ids' => ['nullable'],
        ];
        $validate = Validator::make($request->all(), $rules, $this->messages);
        $validate->setAttributeNames($this->names);
        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $tracking_numbers = explode(',', $request->shipment_ids);
            $open_box_ids = explode(',', $request->open_box_ids);
            $notifications = explode(',', $request->notification_ids);
            $rider_informations = explode(',', $request->rider_info_ids);
            $shipments = Shipment::whereIn('tracking_number', $tracking_numbers)->pluck('id')->toArray();
            if (count($shipments) == 0) {
                return response()->json(['status' => 0, 'message' => 'Shipments not entered!']);
            }
            $pending_status = array(2, 4, 6, 7, 8, 9, 10, 13, 15, 49, 55, 59);
            $valid_shipments = Shipment::whereIn('id', $shipments)->whereIn('shipper_status_id', $pending_status)->pluck('id');
            $shipments_count = count($valid_shipments);
            if ($shipments_count != 0) {
                $valid_shipments = $valid_shipments->toArray();
                $total_cod_amount = Shipment::whereIn('id', $valid_shipments)->where(function ($query) {
                    $query->where('booking_type_id', '!=', 4)
                        ->orWhere(function ($sub_query) {
                            $sub_query->where('booking_type_id', '=', 4)
                                ->where('charges_mode_id', '=', 2);
                        });
                })->sum('amount');
                $order = false;
                if ($request->has('order_checkbox')) {
                    $order = true;
                }
                $note = RiderDeliveryNoteRequest::create([
                    'hub_id' => $request->hub_id,
                    'rider_id' => $request->rider_id,
                    'route_id' => $request->selected_route_id,
                    'shipment_count' => $shipments_count,
                    'total_cod_amount' => $total_cod_amount,
                    'ordering' => $order
                ]);
                if ($note) {
                    if (!$order) {  //Default
                        sort($valid_shipments); //sort_valid_shipments;
                    }
                    $serial = 1;
                    foreach ($valid_shipments as $shipment) {
                        RiderDeliveryNoteRequestShipment::create([
                            'request_note_id' => $note->id,
                            'shipment_id' => $shipment,
                            'notification' => (in_array($shipment, $notifications)) ? 1 : 0,
                            'rider_information' => (in_array($shipment, $rider_informations)) ? 1 : 0,
                            'open_box' => (in_array($shipment, $open_box_ids)) ? 1 : 0,
                            'ordering' => $serial
                        ]);
                        $serial++;
                    }
                }
                return response()->json(['status' => 0, 'create_message' => 'Delivery note Request has been created successfully & Pending for approval']);
            } else {
                return response()->json(['status' => 1, 'message' => 'All the Shipment(s) are not ready for delivery yet or already in another delivery note, please check tracking!']);
            }
        }
    }

    public function generate_otp_for_consignee(Request $request)
    {
        $rules = [
            'latitude' => ['required', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'longitude' => ['required', 'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'],
            'shipment_id' => ['required', 'integer', 'digits_between:1,10', 'exists:shipments,id'],
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $rider_id = $request->rider_id;
            $shipment_id = $request->shipment_id;
            $otp = mt_rand(100000, 999999);
            $shipment_otp = ShipmentOtp::where('shipment_id', $shipment_id)->where('rider_id', $rider_id)->whereDate('updated_at', Carbon::today());
            if ($shipment_otp->exists()) {
                $shipment_otp = $shipment_otp->first();
            } else {
                $shipment_otp = new ShipmentOtp();
                $shipment_otp->shipment_id = $shipment_id;
            }
            $shipment_otp->otp = $otp;
            $shipment_otp->rider_id = $rider_id;
            $shipment_otp->latitude = $request->latitude;
            $shipment_otp->longitude = $request->longitude;
            $shipment_otp->save();
            NotificationsController::send(192, $rider_id, $shipment_id);
            return response()->json(['status' => 0, 'message' => 'OTP sent to consignee successfully!', 'otp' => $otp]);
        }
    }

    /*public function delivery_packaging_material_update($tracking_number){
        $packaging_material_shipment = PackagingMaterialRequest::where('tracking_number', $tracking_number)->where('status_id', 3)->first();
        if($packaging_material_shipment != null){
            $packaging_material_shipment->status_id = 4;
            $packaging_material_shipment->save();

            $packaging_request_history = new PackagingMaterialRequestHistory();
            $packaging_request_history->packaging_material_request_id = $packaging_material_shipment->id;
            $packaging_request_history->status = 4;
            $packaging_request_history->updated_by = 6;
            $packaging_request_history->save();
        }
        $warehouse_stock_request = WarehouseStockRequest::where('tracking_number', $tracking_number);
        if($warehouse_stock_request->exists()){
            $warehouse_stock_request = $warehouse_stock_request->first();
            $warehouse_stock_request->status_id = 4;
            $warehouse_stock_request->save();

            $warehouse_stock_request_history = new WarehouseStockRequestHistory();
            $warehouse_stock_request_history->warehouse_stock_request_id = $warehouse_stock_request->id;
            $warehouse_stock_request_history->status = 4;
            $warehouse_stock_request_history->updated_by = 6;
            $warehouse_stock_request_history->save();
            foreach ($warehouse_stock_request->stock_request_details as $detail){
                if(WarehouseStock::where('warehouse_id', $warehouse_stock_request->requested_by)->where('type_id', $detail->type_id)->where('type_size_id', $detail->size_id)->exists()){
                    $receiver_stock = WarehouseStock::where('warehouse_id', $warehouse_stock_request->requested_by)->where('type_id', $detail->type_id)->where('type_size_id', $detail->size_id)->first();
                    $receiver_stock->stock += $detail->quantity;
                    $receiver_stock->save();
                }else{

                    $receiver_stock = new WarehouseStock();
                    $receiver_stock->warehouse_id = $warehouse_stock_request->requested_by;
                    $receiver_stock->type_id = $detail->type_id;
                    $receiver_stock->type_size_id = $detail->size_id;
                    $receiver_stock->stock = $detail->quantity;
                    $receiver_stock->save();
                }
            }
        }
    }*/

    public function otp_bypass($user_id)
    {
        $otp_bypass = false;
        $only_shippers = array();
        $excluded_shippers = array();
        $otp_bypass_setting = GlobalSettings::where('type', 'otp_refusal_bypass');
        if ($otp_bypass_setting->exists()) {
            $otp_bypass_setting = $otp_bypass_setting->first();
            $otp_bypass = $otp_bypass_setting->setting_value;
        }
        if ($otp_bypass) {
            $all_shipper_settings = GlobalSettings::where('type', 'otp_refusal_bypass_all_shippers');
            if ($all_shipper_settings->exists()) {
                $all_shipper_settings = $all_shipper_settings->first();
                $all_shippers = $all_shipper_settings->setting_value;

                if ($all_shippers) {
                    $excluded_shipper_settings = GlobalSettings::where('type', 'otp_refusal_bypass_exclude_shippers');
                    if ($excluded_shipper_settings->exists()) {
                        $excluded_shipper_settings = $excluded_shipper_settings->first();
                        if ($excluded_shipper_settings->setting_value) {
                            $excluded_shippers = array_map('intval', explode(',', $excluded_shipper_settings->text));
                            return (in_array($user_id, $excluded_shippers)) ? false : true;
                        } else {
                            return true;
                        }
                    } else {
                        return true;
                    }
                } else {
                    $only_shippers_setting = GlobalSettings::where('type', 'otp_refusal_bypass_only_shippers');
                    if ($only_shippers_setting->exists()) {
                        $only_shippers_setting = $only_shippers_setting->first();
                        if ($only_shippers_setting->setting_value) {
                            $only_shippers = array_map('intval', explode(',', $only_shippers_setting->text));
                            return (in_array($user_id, $only_shippers)) ? true : false;
                        } else {
                            return true;
                        }
                    } else {
                        return true;
                    }
                }
            }
        } else {
            return false;
        }
    }

    public function auto_return_confirm($shipment_id)
    {
        $shipment = Shipment::find($shipment_id);

        $shipment->shipper_status_id = 20;
        $shipment->consignee_status_id = 20;
        $shipment->save();

        if ($shipment->shipment_type == 1) {
            if ($shipment->booking_type_id != 4) {
                ShipmentChargesController::return($shipment_id);

                if ($shipment->packaging_material_request != 1) {

                    AdminFinanceController::add_payment($shipment_id, 1);
                }
            } else {
                ShipmentChargesController::walk_in_return($shipment_id);

                $shipment->walk_in_status = 2;

                $shipment->save();

                AdminFinanceController::done_payment($shipment_id, 1);
            }
        }
        // $return_assign_shipment = ReturnAssignedShipments::where('shipment_id', $shipment_id);
        // if ($return_assign_shipment->exists()) {

        //     $return_assign_shipment = $return_assign_shipment->latest()->first();
        //     $return_assign_shipment->status = 0;
        //     $return_assign_shipment->save();

        //     $return_assign_log = new ReturnAssignedShipmentLogs();
        //     $return_assign_log->return_assign_shipment_id = $return_assign_shipment->id;
        //     $return_assign_log->status = 2;
        //     $return_assign_log->assigned_by = 346;
        //     $return_assign_log->save();
        // }
    }

    public function return_create_index(Request $request)
    {
        $routes = Route::where('status', 1)->where('city_id', $request->rider_hub)->get();
        return response()->json(['status' => 0, 'routes' => $routes]);
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
            $rider_hub = $request->rider_hub;
            $rider_id = $request->rider_id;
            // $role_id = $request->admin_role_id;
            // $admin_id = $request->admin_id;
            // $admin_hubs = AdminHub::where('admin_id', $admin_id)->pluck('hub_id')->toArray();
            $different_city_statuses_2 = array(22, 24, 27, 29, 33, 35, 42, 44, 45, 46, 47, 48, 60);
            $different_city_statuses = array(22, 24, 27, 29, 33, 35, 37, 42, 44, 45, 46, 47, 48, 60);
            $allowed_statuses = array(20, 22, 24, 27, 29, 30, 33, 35, 37, 42, 44, 45, 46, 47, 48, 680);
            $return_note_statuses = array(20, 22, 24, 27, 29, 30, 33, 35, 37, 42, 44, 45, 46, 47, 4, 60);
            $shipment = Shipment::where('tracking_number', $request->tracking)->whereIn('shipper_status_id', $allowed_statuses);
            $status = '';
            if ($shipment->exists()) {
                $shipment = $shipment->first();
                $dispute_check = CheckDisputeShipmentsController::check($shipment->id);
                if (!$dispute_check) {
                    return response()->json(['status' => 1, 'message' => 'Shipment is in Dispute! For further assistance, please contact QA (CX)']);
                }
                ShipmentScanningJourneyController::add($shipment->id, 7, 5, $rider_id, null, null);
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
                $destination_id = $destination_id->hub_id; //first it was origin now for return its destination
                // if ($role_id == 1 || in_array($destination_id, $admin_hubs)) {
                if ($destination_id == $rider_hub) {
                    $origin = $shipment->consignee_city->hub_id; //let's suppose consignee city is origin now
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
                            // if ($shipment->booking_type_id != 4) {
                            //     $settings = GlobalSettings::where('type', 'return_note_restriction_bypass');
                            //     if ($settings->exists()) {
                            //         $settings = $settings->first();
                            //         $role_ids = array_map('intval', explode(',', $settings->text));
                            //     } else {
                            //         $role_ids = array();
                            //     }
                            //     array_push($role_ids, 1);

                            //     if (!in_array($role_id, $role_ids)) {
                            //         if (!$shipment->packaging_material_request) {
                            //             $shipper_payable = 0;
                            //             $pending_payment = PendingPayment::where('user_id', $shipment->user_id);
                            //             if ($pending_payment->exists()) {
                            //                 $pending_payment = $pending_payment->first();

                            //                 $pending_payment_shipments = PendingPaymentShipment::where('pending_payment_id', $pending_payment->id);
                            //                 if ($pending_payment_shipments->exists()) {
                            //                     $pending_payment_shipments = $pending_payment_shipments->get();
                            //                     foreach ($pending_payment_shipments as $pending_payment_shipment) {
                            //                         $shipper_payable += $pending_payment_shipment->payable;
                            //                     }
                            //                 }
                            //             }
                            //             if ($shipper_payable < 0) {
                            //                 return response()->json(['status' => 1, 'message' => 'Shipper with Negative Balance, Contact Sales Team!']);
                            //             }
                            //         }
                            //     }
                            // }
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
                            // if ($shipment->booking_type_id != 4) {
                            //     $settings = GlobalSettings::where('type', 'return_note_restriction_bypass');

                            //     if ($settings->exists()) {
                            //         $settings = $settings->first();
                            //         $role_ids = array_map('intval', explode(',', $settings->text));
                            //         array_push($role_ids, 1);
                            //         if (!in_array($role_id, $role_ids)) {
                            //             if (!$shipment->packaging_material_request) {
                            //                 $shipper_payable = 0;
                            //                 $pending_payment = PendingPayment::where('user_id', $shipment->user_id);
                            //                 if ($pending_payment->exists()) {
                            //                     $pending_payment = $pending_payment->first();

                            //                     $pending_payment_shipments = PendingPaymentShipment::where('pending_payment_id', $pending_payment->id);
                            //                     if ($pending_payment_shipments->exists()) {
                            //                         $pending_payment_shipments = $pending_payment_shipments->get();
                            //                         foreach ($pending_payment_shipments as $pending_payment_shipment) {
                            //                             $shipper_payable += $pending_payment_shipment->payable;
                            //                         }
                            //                     }
                            //                 }
                            //                 if ($shipper_payable < 0) {
                            //                     return response()->json(['status' => 1, 'message' => 'Shipper with Negative Balance, Contact Sales Team!']);
                            //                 }
                            //             }
                            //         }
                            //     }
                            // }
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
                    } else if ($request->has('hub_id') && ($destination_id == $request->hub_id)) {
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
                            // if ($shipment->booking_type_id != 4) {
                            //     $settings = GlobalSettings::where('type', 'return_note_restriction_bypass');
                            //     if ($settings->exists()) {
                            //         $settings = $settings->first();
                            //         $role_ids = array_map('intval', explode(',', $settings->text));
                            //         array_push($role_ids, 1);
                            //         if (!in_array($role_id, $role_ids)) {
                            //             if (!$shipment->packaging_material_request) {
                            //                 $shipper_payable = 0;
                            //                 $pending_payment = PendingPayment::where('user_id', $shipment->user_id);
                            //                 if ($pending_payment->exists()) {
                            //                     $pending_payment = $pending_payment->first();

                            //                     $pending_payment_shipments = PendingPaymentShipment::where('pending_payment_id', $pending_payment->id);
                            //                     if ($pending_payment_shipments->exists()) {
                            //                         $pending_payment_shipments = $pending_payment_shipments->get();
                            //                         foreach ($pending_payment_shipments as $pending_payment_shipment) {
                            //                             $shipper_payable += $pending_payment_shipment->payable;
                            //                         }
                            //                     }
                            //                 }
                            //                 if ($shipper_payable < 0) {
                            //                     return response()->json(['status' => 1, 'message' => 'Shipper with Negative Balance, Contact Sales Team!']);
                            //                 }
                            //             }
                            //         }
                            //     }
                            // }
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
                            // if ($shipment->booking_type_id != 4) {
                            //     $settings = GlobalSettings::where('type', 'return_note_restriction_bypass');

                            //     if ($settings->exists()) {
                            //         $settings = $settings->first();
                            //         $role_ids = array_map('intval', explode(',', $settings->text));
                            //         array_push($role_ids, 1);
                            //         if (!in_array($role_id, $role_ids)) {
                            //             if (!$shipment->packaging_material_request) {
                            //                 $shipper_payable = 0;
                            //                 $pending_payment = PendingPayment::where('user_id', $shipment->user_id);
                            //                 if ($pending_payment->exists()) {
                            //                     $pending_payment = $pending_payment->first();

                            //                     $pending_payment_shipments = PendingPaymentShipment::where('pending_payment_id', $pending_payment->id);
                            //                     if ($pending_payment_shipments->exists()) {
                            //                         $pending_payment_shipments = $pending_payment_shipments->get();
                            //                         foreach ($pending_payment_shipments as $pending_payment_shipment) {
                            //                             $shipper_payable += $pending_payment_shipment->payable;
                            //                         }
                            //                     }
                            //                 }
                            //                 if ($shipper_payable < 0) {
                            //                     return response()->json(['status' => 1, 'message' => 'Shipper with Negative Balance, Contact Sales Team!']);
                            //                 }
                            //             }
                            //         }
                            //     }
                            // }
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
            'route_id' => ['required'],
            'open_box' => ['nullable'],
            'trackings' => ['required'],
        ];
        $validate = Validator::make($request->all(), $rules, $this->messages);
        $validate->setAttributeNames($this->names);
        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $tracking_numbers = explode(',', $request->trackings);
            $open_box_ids = explode(',', $request->open_box);
            $shipments = Shipment::whereIn('tracking_number', $tracking_numbers)->pluck('id')->toArray();
            if (count($shipments) == 0) {
                return response()->json(['status' => 0, 'message' => 'Shipments not entered!']);
            }
            $pending_status = array(20, 22, 24, 27, 29, 30, 33, 35, 37, 42, 44, 45, 46, 47, 48, 60);
            $valid_shipments = Shipment::whereIn('id', $shipments)->whereIn('shipper_status_id', $pending_status)->pluck('id');
            $shipments_count = count($valid_shipments);
            if ($shipments_count != 0) {
                $valid_shipments = $valid_shipments->toArray();
                // $total_cod_amount = Shipment::whereIn('id', $valid_shipments)->where(function ($query) {
                //     $query->where('booking_type_id', '!=', 4)
                //         ->orWhere(function ($sub_query) {
                //             $sub_query->where('booking_type_id', '=', 4)
                //                 ->where('charges_mode_id', '=', 2);
                //         });
                // })->sum('amount');
                $order = false;
                if ($request->has('order_checkbox')) {
                    $order = true;
                }
                $note = RiderReturnNoteRequest::create([
                    'hub_id' => $request->rider_hub,
                    'rider_id' => $request->rider_id,
                    'route_id' => $request->route_id,
                    'shipment_count' => $shipments_count,
                    // 'total_cod_amount' => $total_cod_amount,
                    'ordering' => $order
                ]);
                if ($note) {
                    if (!$order) {  //Default
                        sort($valid_shipments); //sort_valid_shipments;
                    }
                    $serial = 1;
                    foreach ($valid_shipments as $shipment) {
                        RiderReturnNoteRequestShipment::create([
                            'request_note_id' => $note->id,
                            'shipment_id' => $shipment,
                            'open_box' => (in_array($shipment, $open_box_ids)) ? 1 : 0,
                            'ordering' => $serial
                        ]);
                        $serial++;
                    }
                }
                return response()->json(['status' => 0, 'create_message' => 'Return note Request has been created successfully & Pending for approval']);
            } else {
                return response()->json(['status' => 1, 'message' => 'All the Shipment(s) are not ready for return yet or already in another return note, please check tracking!']);
            }
        }
    }

    public function retail_shipment_store_v3(Request $request)
    {
        $rider_id = $request->rider_id;
        $setting = GlobalSettings::where('type', 'retail_store')->first();
        $user_id = $setting->setting_value;
        $pickup_address_id = $request->pickup_address_id;
        $user_shipping_info = UserShippingInfo::find($pickup_address_id);
        $pickup_city_id = $user_shipping_info->city_id;
        $information_display = TRUE;
        $trax_box_id = ($request->trax_box_id != -1) ? $request->trax_box_id : null;

        $consignee_name = $request->input('consignee_name');
        $consignee_address = $request->input('consignee_address');
        $consignee_phone_number_1 = $request->input('consignee_cell_no');
        $consignee_phone_number_2 = NULL;
        $consignee_email_address = NULL;
        $order_id = $request->input('order_id');
        $package_type = FALSE;
        $special_instructions = NULL;


        $retail_type = RetailTraxCenter::where('pickup_address_id', $pickup_address_id)->first();
        $discount = $retail_type->discount;
        $insurance = $retail_type->insurance;


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

        $city = City::find($pickup_city_id);

        /*$request->weight_charges = str_replace(',', '', $request->input('weight_charges'));
        $request->fuel_surcharge = str_replace(',', '', $request->input('fuel_surcharge'));
        $city = City::find($pickup_city_id);
        $gst = $city->zone->gst;
        $total_charges_without_gst = $request->weight_charges + $request->fuel_surcharge;
        $gst = $gst * $total_charges_without_gst;
        $total_charges = $total_charges_without_gst + $gst;*/

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

        $packaging = ($request->packaging_amount != null) ? intval($request->packaging_amount) : 0;

        $insurance_amount = ($request->insurance_amount != null) ? str_replace(',', '', $request->insurance_amount) : 0;
        if ($insurance_amount > 0) {
            $insurance_amount = round(intval($insurance_amount) * $insurance / 100, 2);
        }

        $rates = RetailRatesCalculationController::rates($request->shipping_mode_id, $request->business_category_id, $pickup_city_id, $request->city_id, $trax_box_id, $discount, $estimated_weight, $insurance_amount, $packaging);

        $charges = $rates["total_charges"];

        if ($shipping_mode_check == 3) {
            $amount = str_replace(',', '', $request->input('cod_amount'));
            $r_amount = 0;
        } else {
            $amount = 0;
            $r_amount = 0;
        }
        $payment_mode_id = 1;
        $try_and_buy_charges = NULL;


        $pieces_quantity = $request->input('pieces');
        $business_category_id = $request->input('business_category_id');

        $charges_mode_id = 1;

        $shipment_id = RetailShipmentBookController::book($user_id, 1, $pickup_address_id, $information_display, $consignee_city_id, $consignee_name, $consignee_address, $consignee_phone_number_1, $consignee_phone_number_2, $consignee_email_address, $order_id, $package_type, $special_instructions, $estimated_weight, $shipping_mode_id, $same_day_timing_id, $amount, $r_amount, $payment_mode_id, $charges_mode_id, $try_and_buy_charges, $pieces_quantity, $business_category_id, $length, $breadth, $height);

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
        $retail_shipment->total_charges = $charges;
        $retail_shipment->shipper_account_no = $shipper_info->id;
        $retail_shipment->weight = $estimated_weight;
        $retail_shipment->length = $length;
        $retail_shipment->breadth = $breadth;
        $retail_shipment->height = $height;
        $retail_shipment->charges_with_discount = $rates['charges_with_discount'];
        $retail_shipment->discount = $rates['discount_amount'];
        $retail_shipment->packaging_charges = $rates['packaging_charges'];
        $retail_shipment->insurance_charges = $rates['insurance_amount'];
        $retail_shipment->total_charges_without_gst = $rates['charges_without_gst'];
        $retail_shipment->weight_charges = $rates['charges_without_gst'];
        $retail_shipment->gst = $rates['gst_charges'];
        $retail_shipment->rider_id = $rider_id;
        $retail_shipment->save();


        $date = Carbon::today()->toDateString();
        $cash_deposit = RetailCashDeposit::whereDate('created_at', $date)->where('category', 4)->where('admin_id', $rider_id);
        if ($cash_deposit->exists()) {
            $cash_deposit = $cash_deposit->first();
            $total_shipments = $cash_deposit->total_cn + 1;
            $total_cash = $cash_deposit->total_cash + $amount;
            $cash_deposit->total_cn = $total_shipments;
            $cash_deposit->total_cash = $total_cash;
            $cash_deposit->save();
        } else {
            $cash_deposit = new RetailCashDeposit();
            $cash_deposit->category = 4;
            $cash_deposit->rider_id = $rider_id;
            $cash_deposit->total_cn = 1;
            $cash_deposit->total_cash = $amount;
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

    public function rider_remarks(Request $request)
    {
          $rules = [
            'rider_remarks' => [
                'required', 'string']
        ];


        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $rider_time_period = RiderRemark::where('rider_id', $request->rider_id)->latest()->first();

            if(!isset($rider_time_period) || Carbon::parse($rider_time_period->created_at)->copy()->endOfDay()->isPast()){
                $rider_remark = new RiderRemark;
                $rider_remark->rider_id = $request->rider_id;
                $rider_remark->rider_remarks = $request->rider_remarks;
                $rider_remark->save();
                return response()->json(['status' => 0, 'message' => 'Remarks Has Been Added']);
            }else{
                return response()->json(['status' => 1, 'message' => 'Only One Remarks Is Allowed For A Day']);
            }
        }

    }
    public function rider_remark_list(Request $request)
    {
        $rider = $request->rider_id;

        $rider_remarks = RiderRemark::leftJoin('riders as r', 'r.id', '=', 'rider_remarks.rider_id')
            ->leftJoin('admins as ad', 'ad.id', '=', 'rider_remarks.updated_by')
            ->leftJoin('cities as city', 'r.city_id', '=', 'city.id')
            ->leftJoin('cities as c', 'city.hub_id', '=', 'c.id')
            ->leftJoin('zones as z', 'city.zone_id', '=', 'z.id')
            ->leftJoin('rider_remarks_response as rrr', function ($join) {
                $join->on('rrr.rider_remarks_id', '=', 'rider_remarks.id')
                    ->where('rrr.type', '=', 1)
                    ->whereRaw('rrr.id = (SELECT MAX(id) FROM rider_remarks_response WHERE rider_remarks_id = rider_remarks.id AND type = 1)');
            })
            ->leftJoin('rider_remarks_response as rrr_2', function ($join) {
                $join->on('rrr_2.rider_remarks_id', '=', 'rider_remarks.id')
                    ->where('rrr_2.type', '=', 2)
                    ->whereRaw('rrr_2.id = (SELECT MAX(id) FROM rider_remarks_response WHERE rider_remarks_id = rider_remarks.id AND type = 2)');
            })
            ->leftJoin('rider_remark_statuses as rrs', 'rrs.id', '=', 'rider_remarks.rider_remarks_status_id')

            ->where('rider_remarks.rider_id', '=', $rider)

            ->select([
                'rider_remarks.id as id',
                'r.name as rider_name',
                'r.id as rider_id',
                'r.trax_id as traxID',
                'city.id as city_id',
                'city.name as city_name',
                'c.name as hub',
                'z.name as zone_name',
                'rider_remarks.created_at as created_at',
                'rrs.name as status',
                'rider_remarks.updated_by as updated_by',
                'rider_remarks.updated_at as updated_at',
                'rider_remarks.rider_remarks as rider_remarks',
                'rrr.response as initial_response',
                'rrr_2.response as final_response',
                'rider_remarks.id as rider_remarks_id',
                'ad.name as admin_name'
            ])
            ->orderBy('rider_remarks.updated_at', 'desc')
            ->get();


        if (!$rider_remarks->isEmpty()) {
            return response()->json(['status' => 0, 'data' => $rider_remarks]);
        } else {
            return response()->json(['status' => 1, 'message' => 'No Remarks Have Been Found']);
        }
    }

    public function rider_checkin(Request $request)
    {

        $rules = [
            'from' => ['required', 'date_format:Y/m/d'],
            'to' => ['required', 'date_format:Y/m/d'],
            'trax_id' => ['nullable','string']
        ];


        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $from = Carbon::parse($request->from);
            $to = Carbon::parse($request->to);
            $today = Carbon::now();

            $total_difference_from = $from;
            $total_difference_from = $total_difference_from->diff($today)->days;
            $between_difference_from = $from;
            $between_difference_from = $between_difference_from->diff($to)->days;
            if($total_difference_from <= 90){
                if($between_difference_from <= 45){
                    $details = array();

                    $trax_id_check = false;
                    if($request->has('trax_id')){
                        if($request->trax_id != null && $request->trax_id != ''){
                            $employee = Employee::where('trax_id', $request->trax_id);
                            if($employee->exists()){
                                $employee = $employee->first();

                                $trax_id_check = true;
                            }
                            else{
                                return response()->json(['status' => 1, 'message' => 'Employee not found!']);
                            }
                        }
                    }

                    if($trax_id_check == false){
                        $rider_attendances = EmployeeAttendance::where('employee_type', 2)->whereBetween('attendance_date',[$from,$to]);
                    }
                    else{
                        $rider_attendances = EmployeeAttendance::where('employee_id', $employee->id)->where('employee_type', 2)->whereBetween('attendance_date',[$from,$to]);
                    }

                    if($rider_attendances->exists()){
                        $rider_attendances = $rider_attendances->get();
                        foreach ($rider_attendances as $rider_attendance){
                            if($trax_id_check == false) {
                                $employee = Employee::find($rider_attendance->employee_id);
                            }
                            if($employee){
                                if($rider_attendance->clock_in_datetime != null){
                                    $detail = array('Trax ID' => $employee->trax_id, 'Name' => $employee->name, 'Clock In' => $rider_attendance->clock_in_datetime);
                                    $details[$rider_attendance->attendance_date][] = $detail;
                                }
                            }
                        }

                        return response()->json(['status' => 0, 'data' => $details]);
                    }
                    else{
                        return response()->json(['status' => 1, 'message' => 'Data not found!']);
                    }
                }
                else{
                    return response()->json(['status' => 1, 'message' => 'Date difference range must not exceed 45 days']);
                }
            }
            else{
                return response()->json(['status' => 1, 'message' => 'Date range should not exceed 90 days']);
            }

        }
    }

    public function scan_shipment(Request $request)
    {
        $rules = [
            'tracking_number' => 'required',
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        }
        else {
            $tracking_number = explode(',', $request->tracking_number);
            $shipments = Shipment::whereIn('tracking_number', $tracking_number)->pluck('tracking_number')->toArray();

            if(count($tracking_number) == count($shipments))
            {   
                $shipment_scanned = AdminApiController::quick_tracking_shipment_scan($tracking_number, 5, $request->rider_id);

                return ['status' => 0, 'message' => 'Scanned Sucessfully!', 'data' => $shipment_scanned];
            }
            else{
                $tracking_not_found = array_diff($tracking_number, $shipments);
                return ['status' => 1, 'message' => 'Tracking Number Not found', 'tracking_number' => $tracking_not_found];
            }
        }
    }

    public function scan_rider_picked_shipment(Request $request)
    {
        $rules = [
            'tracking_number' => 'required',
            'call_from' => 'required',
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        }
        else {
            $tracking_number = $request->tracking_number;
            $shipper_status_id = Shipment::where('tracking_number', $tracking_number)->first()->shipper_status_id ?? NULL;
            if(!$shipper_status_id){
                return response()->json(['status' => 1, 'message' => 'Invalid tracking number']);
            } else {
                $data = [
                    'tracking_number' => $tracking_number,
                    'call_from' => $request->call_from
                ];

                switch ($shipper_status_id) {
                    case 1 : //Booked...
                        return response()->json(['status' => 0, 'success_message' => 'Shipment scanned successfuly', 'data' => $data]);
                        break;

                    case 17 : //Cancelled..
                        return response()->json(['status' => 1, 'message' => 'Shipment is cancelled']);
                        break;

                    case 53 : //Rider Picked...
                        return response()->json(['status' => 1, 'message' => 'Shipment is already rider picked']);
                        break;

                    default:
                        return response()->json(['status' => 1, 'message' => 'Shipment is not on booked status']);
                        break;
                }
            }
        }
    }

    public function pending_for_verification(Request $request){

        $message = '';
        $rules = [
            'delivery_note_id' => ['required', 'integer', 'digits_between:1,10', 'exists:delivery_notes,id'],
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {

            $rider_id = $request->rider_id;

            $rider_delivery_note_status = RiderDeliveryNoteStatus::where('delivery_note_id', $request->delivery_note_id);
            if (!$rider_delivery_note_status->exists()) {
                $new_status = new RiderDeliveryNoteStatus();
                $new_status->delivery_note_id = $request->delivery_note_id;
                $new_status->status = 1;
                $new_status->save();
            }
            $updated_shipments_count = DeliveryNoteShipment::where('delivery_note_id', $request->delivery_note_id)->where('status', 0)->count();

            if ($updated_shipments_count == 0) {
                $delivery_note = DeliveryNote::where('id', $request->delivery_note_id);
                if($delivery_note->exists()){
                    $delivery_note = $delivery_note->first();
                    $delivery_note->pending_status = 1;
                    $delivery_note->pending_for_verification_at = Carbon::now();
                    $delivery_note->save();
                }
//                DeliveryNote::where('id', $request->delivery_note_id)->update(['pending_status' => 1, 'pending_for_verification_at' => Carbon::now()]);

                dispatch(new ProcessOneLinkExpireDeliveryNote($request->delivery_note_id));

                //fintech

                $unique_codes = TraxPayTransaction::where('delivery_note_id')->pluck('unique_code')->toArray();
                if(count($unique_codes) > 0){
                    dispatch(new ProcessTraxPayExpireDeliveryNote($unique_codes));
                }

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

            return response()->json(['status' => 0, 'message' => 'Delivery Note is ready for verification!', 'delivery_note_id' => $request->delivery_note_id]);
        }
    }
}
