<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\ShipmentScanningJourneyController;
use App\Http\Models\ConsigneeLocation;
use App\Http\Models\ConsigneeShipmentLocation;
use App\Http\Models\Shipment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CoordinatesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }
    public function add_index(){
        return view('admin.coordinates.add');
    }
    public function add_submit(Request $request){
        $shipment = Shipment::find($request->shipment_id);
        $consignee_phone_number_1 = $shipment->consignee_phone_number_1;
        $consignee_phone_number_2 = $shipment->consignee_phone_number_2;
        $coordinates = ConsigneeLocation::where(function ($sub_query) use ($consignee_phone_number_1, $consignee_phone_number_2) {
            $sub_query->where('phone_number', $consignee_phone_number_1)
                ->orwhere('phone_number', $consignee_phone_number_2);
        })->where('address', $shipment->consignee_address)->where('lat', $request->lat)->where('long', $request->long);

        if($coordinates->exists()){
            $new_coordinates = $coordinates->first();
        }
        else{
            $new_coordinates = new ConsigneeLocation();
            $new_coordinates->phone_number = $consignee_phone_number_1;
            $new_coordinates->address = $shipment->consignee_address;
            $new_coordinates->lat = $request->lat;
            $new_coordinates->long = $request->long;
            $new_coordinates->save();
        }

        $shipment->consignee_latitude = $request->lat;
        $shipment->consignee_longitude = $request->long;
        $shipment->save();

        $shipment_coordinates = ConsigneeShipmentLocation::where('shipment_id', $shipment->id);
        if($shipment_coordinates->exists()){
            $new_shipment_coordinates = $shipment_coordinates->first();
            $new_shipment_coordinates->previous_location_id = $new_coordinates->id;
            $new_shipment_coordinates->current_location_id = NULL;
            $new_shipment_coordinates->save();
        }
        else{
            $new_shipment_coordinates = new ConsigneeShipmentLocation();
            $new_shipment_coordinates->shipment_id = $shipment->id;
            $new_shipment_coordinates->previous_location_id = $new_coordinates->id;
            $new_shipment_coordinates->current_location_id = NULL;
            $new_shipment_coordinates->save();
        }
        return redirect()->back()->with('success', 'Shipment\'s Latitude Longitude updated successfully!');
    }
    public function shipment_details(Request $request) {
        $shipment = Shipment::where('tracking_number', $request->tracking_number);

        if ($shipment->exists()) {
            $shipment = $shipment->first();

            $details = array();

            $shipper = $shipment->user;

            $details['id'] = $shipment->id;

            $details['tracking_number'] = $shipment->tracking_number;
            $details['status'] = $shipment->status_shipper->name;

            $details['service_type'] = $shipment->booking_type->booking_type;
            $details['shipping_mode'] = $shipment->shipping_mode->mode;
            $details['weight'] = ($shipment->actual_weight) ? floatval($shipment->actual_weight) : floatval($shipment->estimated_weight);

            $details['payment_mode'] = $shipment->payment_mode->mode;
            $details['amount'] = number_format($shipment->amount);

            $details['shipper']['name'] = $shipper->name;
            $details['shipper']['account_number'] = str_pad($shipper->id, 6, '0', STR_PAD_LEFT);
            $details['shipper']['phone_number_1'] = $shipper->phone;
            $details['shipper']['phone_number_2'] = $shipper->phone2;
            $details['shipper']['origin'] = $shipper->city->name;
            $details['shipper']['address'] = $shipper->address;

            $details['consignee']['name'] = $shipment->consignee_name;
            $details['consignee']['phone_number_1'] = $shipment->consignee_phone_number_1;
            $details['consignee']['phone_number_2'] = $shipment->consignee_phone_number_2;
            $details['consignee']['destination'] = $shipment->consignee_city->name;
            $details['consignee']['address'] = $shipment->consignee_address;

            ShipmentScanningJourneyController::add($shipment->id, 15, 1, Auth::id(), null,null);
            return ['status' => 0, 'success' => 'Shipment Found', 'details' => $details];
        }
        else {
            return ['status' => 1, 'error' => 'No Shipment exists with given Tracking Number'];
        }
    }

    public function address_search(Request $request){
        $coordinates = ConsigneeLocation::where('address', 'like', '%' . $request->address . '%');
        if($coordinates->exists()){
            $coordinates = $coordinates->get();
            return response()->json(['status' => 0, 'success' => 'Address Found', 'coordinates' => $coordinates]);
        }
        else {
            return response()->json(['status' => 1, 'error' => 'Address not found']);
        }
    }
}
