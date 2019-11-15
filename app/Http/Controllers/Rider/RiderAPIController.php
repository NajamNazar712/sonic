<?php

namespace App\Http\Controllers\Rider;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Storage;

use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\ShipmentsJourneyController;

use App\Http\Models\City;
use App\Http\Models\Rider;
use App\Http\Models\PickupNote;
use App\Http\Models\PickupRequestAssignedShipment;
use App\Http\Models\PickupRequest;
use App\Http\Models\Shipper\UserShippingInfo;
use App\Http\Models\Shipment;
use App\Http\Models\RiderPickup;
use App\Http\Models\RiderPickupShipment;
use App\Http\Models\PickupNoteRequest;
use App\Http\Models\RiderPickupActionLog;

class RiderAPIController extends Controller {
    private $names = [
        'phone_number' => 'Phone Number',
        'pin' => 'PIN',

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

        'actions' => 'Actions',
        'actions.*' => 'Action',
        'actions.*.logged_at' => 'Logged At',
        'actions.*.type_id' => 'Type ID',
        'actions.*.pickup_note_id' => 'Pickup Note ID',
        'actions.*.pickup_request_id' => 'Pickup Request ID'
    ];

    private $messages = [
        'phone_number.regex' => ':attribute format is Invalid, required Format is: 03000000000.',
        'integer' => ':attribute must be an Integer.',
        'digits' => ':attribute must be of :digits Digits.',
        'exists' => 'Given :attribute is of Invalid ID.',
        'actual_location_latitude.regex' => ':attribute is Invalid Latitude Coordinates.',
        'actual_location_longitude.regex' => ':attribute is Invalid Longitude Coordinates.',
        'start_location_latitude.regex' => ':attribute is Invalid Latitude Coordinates.',
        'start_location_longitude.regex' => ':attribute is Invalid Longitude Coordinates.',
        'image' => ':attribute must be an Image.'
    ];

    private function distance($origin, $destination) {
        return $this->vincenty_distance($origin, $destination);
    }

    private function google_distance($origin, $destination) {
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
                }
                else {
                    $distance = $this->vincenty_distance($origin, $destination);
                }
            }
            else {
                $distance = $this->vincenty_distance($origin, $destination);
            }
        } catch (RequestException $e) {
            $distance = $this->vincenty_distance($origin, $destination);
        }

        return $distance;
    }

    private function haversine_distance($origin, $destination) {
        $earth_radius = 6371;

        list($origin_latitude, $origin_longitude) = explode(',', $origin);
        list($destination_latitude, $destination_longitude) = explode(',', $destination);

        $difference_latitude = deg2rad($destination_latitude - $origin_latitude);
        $difference_longitude = deg2rad($destination_longitude - $origin_longitude);

        $distance = round($earth_radius * (2 * asin(sqrt(sin($difference_latitude / 2) * sin($difference_latitude / 2) + cos(deg2rad($origin_latitude)) * cos(deg2rad($destination_latitude)) * sin($difference_longitude / 2) * sin($difference_longitude / 2)))), 2);

        return $distance;
    }

    private function vincenty_distance($origin, $destination) {
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

    private function set_order($starting_location, $pickup_note_id, $pickup_note_requests) {
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
                }
                else {
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

    private function verify_pickup_address_location($pickup_address_id) {
        $number_of_entries = 5;

        $pickup_requests = PickupRequest::where('pickup_address_id', $pickup_address_id)->where('status', 2);

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

    public function login(Request $request) {
        $rules = [
            'phone_number' => ['required', 'regex:/^[0][0-9]{10}$/'],
            'pin' => ['required', 'integer', 'digits:4']
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        }
        else {
            $rider = Rider::where('phone', substr_replace($request->input('phone_number'), '-', 4, 0));

            if ($rider->exists()) {
                $rider = $rider->first();

                if ($rider->status) {
                    if (Hash::check($request->input('pin'), $rider->pin)) {
                        $information = array();

                        $information['name'] = $rider->name;

                        if ($rider->api_token) {
                            $information['api_token'] = $rider->api_token;
                        }
                        else {
                            $api_token = uniqid(base64_encode(str_random(60)));

                            $rider->api_token = $api_token;

                            $rider->save();

                            $information['api_token'] = $api_token;
                        }

                        return response()->json(['status' => 0, 'message' => 'Login Successful', 'information' => $information]);
                    }
                    else {
                        return response()->json(['status' => 1, 'message' => 'Invalid PIN']);
                    }
                }
                else {
                    return response()->json(['status' => 1, 'message' => 'Your Account is Disabled']);
                }
            }
            else {
                return response()->json(['status' => 1, 'message' => 'Invalid Credentials']);
            }
        }
    }

    public function pickup_summary(Request $request) {
        $rider_id = $request->rider_id;

        $pickup_note = PickupNote::where('rider_id', $rider_id)->where('status_id', 2);

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
                $starting_location = $city->location_latitude. ',' . $city->location_longitude;

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
        }
        else {
            return response()->json(['status' => 0, 'message' => 'No Pickup(s) Assigned']);
        }
    }

    public function pickup_pick(Request $request) {
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
                    }
                    else {
                        $pickup_address->location_latitude = $request->actual_location_latitude;
                        $pickup_address->location_longitude = $request->actual_location_longitude;

                        $pickup_address->save();
                    }
                }
                else {
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

    public function pickup_not_pick(Request $request) {
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
        }
        else {
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
                }
                else {
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

    public function pickup_action_log(Request $request) {
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
        }
        else {
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
}