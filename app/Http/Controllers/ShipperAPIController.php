<?php

namespace App\Http\Controllers;

use App\Http\Models\ConsigneeUser;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentsJourney;
use App\Http\Models\Shipper\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ShipperAPIController extends Controller
{
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
                if (Hash::check($request->input('passsword'), $shipper->password)) {
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
}
