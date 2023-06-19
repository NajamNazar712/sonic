<?php

namespace App\Http\Controllers\Shippers;

use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Models\City;
use App\Http\Models\InterceptReBookRequest;
use App\Http\Models\InterceptReBookRequestHistory;
use App\Http\Models\RestrictedCityIntercept;
use App\Http\Models\ReturnAssignedShipmentLogs;
use App\Http\Models\ReturnAssignedShipments;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentReplacementParcelImage;
use App\Http\Models\ShipmentStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\SelfCollectionShipment;
use App\Http\Models\ShipmentDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ShipperInterceptReBookController extends Controller
{
    public function __construct() {
        $this->middleware('auth:web,substitute_users');

        $this->middleware('Permission');
    }

    public function intercept_re_book_index($shipment_id){
        if($shipment_id){
            $shipment = Shipment::where('id',$shipment_id)->first();
            if($shipment){
                if($shipment->shipping_mode_id == 2){
                    $restricted_cities = RestrictedCityIntercept::pluck('city_id')->toArray();
                    $consignee_cities = Shipment::leftjoin('city_deliveries as cd', 'cd.booking_type_id', '=', 'shipments.booking_type_id')
                        ->leftjoin('cities as c', 'c.id', '=', 'cd.city_id')
                        ->select('c.id as id', 'c.name as name')
                        ->where('shipments.id', $shipment_id)
                        ->where('c.status', 1)
                        ->where('cd.shipping_mode_id',2)
                        ->whereNotNull('c.zone_id')
                        ->whereNotIn('c.id', $restricted_cities);
                }
                else{
                    $consignee_cities = Shipment::leftjoin('city_deliveries as cd', 'cd.booking_type_id', '=', 'shipments.booking_type_id')
                        ->leftjoin('cities as c', 'c.id', '=', 'cd.city_id')
                        ->select('c.id as id', 'c.name as name')
                        ->where('shipments.id', $shipment_id)
                        ->where('c.status', 1)
                        ->whereNotNull('c.zone_id');
                }
                $consignee_cities = $consignee_cities->orderBy('c.name')
                    ->groupBy('c.name')
                    ->get();
//        $consignee_cities = City::where('status', 1)->where('pickup',1)->whereNotNull('zone_id')->orderBy('name')->get();
                return view('client.intercept.index')->with(['shipment' => $shipment, 'consignee_cities' => $consignee_cities]);
            }
            return redirect()->back()->with('error', 'Shipment not found!');
        }
        return redirect()->back()->with('error', 'Shipment not found!');

    }

    public function intercept_re_book_update(Request $request)
    {
        $rules = [
            'replacement_parcel_image' => ['nullable', 'mimes:png,jpeg,jpg'],
        ];
        $validate = Validator::make($request->all(), $rules);
        if ($validate->fails()) {
            return back()->with(['error' => "Invalid File Format Of Replacement Parcel Image"]);
        } else {
        $s_amount = str_replace(",", "", $request->amount);
        $amount = intval($s_amount);
        $shipment = Shipment::find($request->shipment_id);
        $user_id = session('user_id');
        $intercept_type = $request->consignee;
        
        $shipment_status = $shipment->status_shipper->name;

        if ($shipment['shipper_status_id'] == 12) {
            if ($shipment['consignee_city_id'] != $request->consignee_city || $shipment['consignee_name'] != $request->consignee_name || $shipment['consignee_address'] != $request->consignee_address || $shipment['consignee_phone_number_1'] != $request->consignee_phone_number_1 || $shipment['consignee_phone_number_2'] != $request->consignee_phone_number_2 || $shipment['consignee_email'] != $request->consignee_email || $shipment['amount'] != $amount) {
                if ($shipment['intercepted'] == 1) {
                    return redirect()->back()->with('error', 'Intercept/Re-Book is already requested against Tracking Number: ' . $shipment['tracking_number']);
                } else {
                    $s_amount = str_replace(",", "", "$request->amount");
                    $amount = (int)$s_amount;

                    //Different Consignee
                    if ($intercept_type == 1){
                        InterceptReBookRequest::create([
                            'shipment_id' => $request->shipment_id,
                            'consignee_city_id' => $request->consignee_city,
                            'consignee_name' => $request->consignee_name,
                            'consignee_address' => $request->consignee_address,
                            'consignee_phone_number_1' => $request->consignee_phone_number_1,
                            'consignee_phone_number_2' => $request->consignee_phone_number_2,
                            'consignee_email' => $request->consignee_email,
                            'amount' => $amount,
                            'shipper_id' => $user_id,
                            'status' => 0,
                            'intercept_type' => $intercept_type,
                            'admin_id' => Auth::id()
                        ]);
                        $shipment->consignee_status_id = 54;
                        $shipment->shipper_status_id = 54;
                        $shipment->intercepted = 1;
                        $shipment->save();

                        ShipmentsJourneyController::add($request->shipment_id, 54, 54, NULL, NULL, $user_id, NULL);
                    }
                    else{
                        
                        InterceptReBookRequestHistory::create([
                            'shipment_id' =>$request->shipment_id,
                            'old_consignee_city_id' => $shipment->consignee_city_id,
                            'new_consignee_city_id' => $request->consignee_city,
                            'old_consignee_name' => $shipment->consignee_name,
                            'new_consignee_name' => $request->consignee_name,
                            'old_consignee_address' => $shipment->consignee_address,
                            'new_consignee_address' => $request->consignee_address,
                            'old_consignee_phone_number_1' => $shipment->consignee_phone_number_1,
                            'new_consignee_phone_number_1' => $request->consignee_phone_number_1,
                            'old_consignee_phone_number_2' => $shipment->consignee_phone_number_2,
                            'new_consignee_phone_number_2' => $request->consignee_phone_number_2,
                            'old_consignee_email' => $shipment->consignee_email,
                            'new_consignee_email' => $request->consignee_email,
                            'old_amount' => $shipment->amount,
                            'new_amount' => $amount,
                            'shipper_id' => $user_id,
                        ]);
                        $shipment->consignee_status_id = 55;
                        $shipment->shipper_status_id = 55;
                        $shipment->intercepted = 1;
                        $shipment->save();

                        ShipmentsJourneyController::add($request->shipment_id, 55, 55, NULL, NULL, $user_id, NULL);

                        $return_assign_shipment = ReturnAssignedShipments::where('shipment_id', $request->shipment_id);
                        if($return_assign_shipment->exists()){

                            $return_assign_shipment = $return_assign_shipment ->latest()->first();
                            $return_assign_shipment->status = 0;
                            $return_assign_shipment->save();

                            // Adding row as request intercept with status = 9
                            $return_assign_log = new ReturnAssignedShipmentLogs();
                            $return_assign_log->return_assign_shipment_id = $return_assign_shipment->id;
                            $return_assign_log->status = 9;
                            $return_assign_log->assigned_by = Auth::id();
                            $return_assign_log->save();

                            // Adding another row as approved intercept with status = 10
                            $return_assign_log = new ReturnAssignedShipmentLogs();
                            $return_assign_log->return_assign_shipment_id = $return_assign_shipment->id;
                            $return_assign_log->status = 10;
                            $return_assign_log->assigned_by = Auth::id();
                            $return_assign_log->save();
                        }

                        if($request->hasFile('replacement_parcel_image')){
                            $shipment_parcel_image = ShipmentReplacementParcelImage::where('shipment_id', $request->shipment_id);
                            if($shipment_parcel_image->exists()){
                                $shipment_parcel_image = $shipment_parcel_image->first();
                                Storage::disk('public')->delete($shipment_parcel_image->picture_path);
                            }else{
                                $shipment_parcel_image = new ShipmentReplacementParcelImage();
                                $shipment_parcel_image->shipment_id = $request->shipment_id;
                            }
                            $time = Carbon::now()->toDateString();
                            $picture_path = 'replacement_parcel/' . $request->shipment_id . '_' . $time . '.png';
                            Storage::disk('public')->put($picture_path, file_get_contents($request->replacement_parcel_image));
                            $shipment_parcel_image->picture_path = $picture_path;
                            $shipment_parcel_image->save();
                        }
                        
                    }


                    return redirect()->route('cod.return.pending.index')->with('success', 'Intercept/Re-Book request submitted against Tracking Number: ' . $shipment['tracking_number']);
                }
            } else {
                return redirect()->back()->with('error', 'Shipment is already book with same details against Tracking Number: ' . $shipment['tracking_number']);
            }
        } else {
            return redirect()->route('cod.return.pending.index')->with('error', 'Shipment is already updated with Status : ' . $shipment_status . ' against Tracking Number: ' . $shipment['tracking_number']);
        }
    }
    }

}
