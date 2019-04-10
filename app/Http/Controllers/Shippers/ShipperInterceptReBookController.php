<?php

namespace App\Http\Controllers\Shippers;

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
//        return $request;
        $shipment = Shipment::where('id',$request->shipment_id)->first();
//        dd($shipment);
        $user_id = session('user_id');

        if($shipment['consignee_city_id'] != $request->consignee_city || $shipment['consignee_name'] != $request->consignee_name || $shipment['consignee_address'] != $request->consignee_address  || $shipment['consignee_phone_number_1'] != $request->consignee_phone_number_1 || $shipment['consignee_phone_number_2'] != $request->consignee_phone_number_2 || $shipment['consignee_email'] != $request->consignee_email || $shipment['amount'] != $request->amount) {
            $intercept_request = InterceptReBookRequest::where(['shipment_id' => $request->shipment_id, 'status' => 0]);
            if($intercept_request->exists()) {

                $intercept_request = $intercept_request->first();
                if($intercept_request['consignee_city_id'] != $request->consignee_city || $intercept_request['consignee_name'] != $request->consignee_name || $intercept_request['consignee_address'] != $request->consignee_address  || $intercept_request['consignee_phone_number_1'] != $request->consignee_phone_number_1 || $shipment['consignee_phone_number_2'] != $request->consignee_phone_number_2 || $shipment['consignee_email'] != $request->consignee_email || $intercept_request['amount'] != $request->amount) {
                    $intercept_request->update([
                        'consignee_city_id' => $request->consignee_city,
                        'consignee_name' => $request->consignee_name,
                        'consignee_address' => $request->consignee_address,
                        'consignee_phone_number_1' => $request->consignee_phone_number_1,
                        'consignee_phone_number_2' => $request->consignee_phone_number_2,
                        'consignee_email' => $request->consignee_email,
                        'amount' => $request->amount,
                        'shipper_id' => $user_id
                    ]);

                    return redirect()->route('cod.return.pending.index')->with('success', 'Intercept/Re-Book request updated for Tracking Number: ' . $shipment['tracking_number']);
                }
                else{
                    return redirect()->back()->with('error', 'Intercept/Re-Book request is already requested with same details');
                }
            }
            else{
                $new_intercept_request = InterceptReBookRequest::create([
                    'shipment_id' => $request->shipment_id,
                    'consignee_city_id' => $request->consignee_city,
                    'consignee_name' => $request->consignee_name,
                    'consignee_address' => $request->consignee_address,
                    'consignee_phone_number_1' => $request->consignee_phone_number_1,
                    'consignee_phone_number_2' => $request->consignee_phone_number_2,
                    'consignee_email' => $request->consignee_email,
                    'amount' => $request->amount,
                    'shipper_id' => $user_id
                ]);

                return redirect()->route('cod.return.pending.index')->with('success', 'Intercept/Re-Book request submitted for Tracking Number: ' .$shipment['tracking_number']);
            }
        }
        else{
            return redirect()->back()->with('error', 'Shipment is already book with same details');
        }
    }
}
