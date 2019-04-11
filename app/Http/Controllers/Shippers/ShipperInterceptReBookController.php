<?php

namespace App\Http\Controllers\Shippers;

use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Models\City;
use App\Http\Models\InterceptReBookRequest;
use App\Http\Models\Shipment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ShipperInterceptReBookController extends Controller
{
    public function __construct() {
        $this->middleware('auth:web,substitute_users');

        $this->middleware('Permission');
    }

    public function intercept_re_book_index($shipment_id){
//        dd(session('user_id'));
        $shipment = Shipment::where('id',$shipment_id)->first();
        $consignee_cities = City::where('status', 1)->whereNotNull('zone_id')->orderBy('name')->get();
        return view('client.intercept.index')->with(['shipment' => $shipment, 'consignee_cities' => $consignee_cities]);
    }

    public function intercept_re_book_update(Request $request){
        $s_amount = str_replace(",","","$request->amount");
        $amount = intval($s_amount);
        $shipment = Shipment::where('id',$request->shipment_id)->first();
        $user_id = session('user_id');

        if($shipment['consignee_city_id'] != $request->consignee_city || $shipment['consignee_name'] != $request->consignee_name || $shipment['consignee_address'] != $request->consignee_address  || $shipment['consignee_phone_number_1'] != $request->consignee_phone_number_1 || $shipment['consignee_phone_number_2'] != $request->consignee_phone_number_2 || $shipment['consignee_email'] != $request->consignee_email || $shipment['amount'] != $amount) {
            if($shipment['intercepted'] == 1) {
                return redirect()->back()->with('error', 'Intercept/Re-Book is already requested against Tracking Number: ' . $shipment['tracking_number']);
            }
            else{
                $s_amount = str_replace(",","","$request->amount");
                $amount = (int)$s_amount;
                $new_intercept_request = InterceptReBookRequest::create([
                    'shipment_id' => $request->shipment_id,
                    'consignee_city_id' => $request->consignee_city,
                    'consignee_name' => $request->consignee_name,
                    'consignee_address' => $request->consignee_address,
                    'consignee_phone_number_1' => $request->consignee_phone_number_1,
                    'consignee_phone_number_2' => $request->consignee_phone_number_2,
                    'consignee_email' => $request->consignee_email,
                    'amount' => $amount,
                    'shipper_id' => $user_id,
                    'status' => 0
                ]);

                $shipment = Shipment::find($request->shipment_id);

                $shipment->consignee_status_id = 54;
                $shipment->shipper_status_id = 54;
                $shipment->intercepted = 1;
                $shipment->save();

                ShipmentsJourneyController::add($request->shipment_id, 54, 54, NULL, NULL, $user_id, NULL);

                return redirect()->route('cod.return.pending.index')->with('success', 'Intercept/Re-Book request submitted against Tracking Number: ' . $shipment['tracking_number']);
            }
        }
        else{
            return redirect()->back()->with('error', 'Shipment is already book with same details against Tracking Number: ' . $shipment['tracking_number']);
        }
    }
}
