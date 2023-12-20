<?php

namespace App\Http\Controllers\Admins;

use DB;
use Carbon\Carbon;
use App\Http\Models\City;
use App\Http\Models\Rider;
use Illuminate\Http\Request;
use App\Http\Models\Shipment;
use App\Http\Models\Admin\Admin;
use App\Http\Models\Shipper\User;
use App\Http\Models\ShipmentPiece;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Models\BagScanningJourney;
use App\ShipmentScanningJourneyAreaLog;
use App\Http\Models\Shipper\SubstituteUser;
use App\Http\Models\Admin\Retail\RetailUser;
use App\Http\Models\ShipmentScanningJourney;
use App\Http\Models\BagScanningScreenLocation;
use App\Http\Models\ShipmentScanningScreenLocation;
use App\Http\Controllers\Admins\ActivityTrailController;
use App\Http\Models\Admin\CargoManifest\CargoManifestBag;

class AdminShipmentScanningHistoryController extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function index(){
        ActivityTrailController::createActivityTrailLog(Auth::id(),250);
        return view('admin.scanning_history.index');
    }
    public function details(Request $request){
        $details = array();
        if($request->has('search_type') && $request->search_type == 1){
            $flag = false;
            $tracking_number = $request->tracking_number;
            $shipment = Shipment::where('tracking_number', $tracking_number);
            if ($shipment->exists()) {
                $shipment = $shipment->first();
                $flag = true;
                $scanning_histories = ShipmentScanningJourney::where('shipment_id', $shipment->id)->get();
//                $scanning_histories = ShipmentScanningJourney::where('shipment_id', $shipment->id)->whereNull('piece_id')->get();
            }
            else {
//                $shipment_piece = ShipmentPiece::where('tracking_number', $request->tracking_number);
//                if ($shipment_piece->exists()) {
//                    $shipment_piece = $shipment_piece->first();
//                    $flag = true;
//                    $scanning_histories = ShipmentScanningJourney::where('piece_id', $shipment_piece->id)->get();
//                }
//                else{
                    $data['invalid'][] = $request->tracking_number;
//                }
            }
            if($flag == true){
                if(count($scanning_histories) > 0){
                    foreach($scanning_histories as $index => $scanning_history){
                        $screen_location = ShipmentScanningScreenLocation::where('id', $scanning_history->screen_location_id)->first();
                        if($scanning_history->admin_id == null && $scanning_history->user_id == null){
                            $account_type = '-';
                            $scanned_by = '-';
                            $city = '-';
                            $area = '-';
                        }
                        else {
                            if ($scanning_history->user_type == 1) {
                                $account_type = 'Admin';
                                $admin = Admin::find($scanning_history->admin_id);
                                $c = City::find($admin->default_hub_id);
                                ($c) ? $city = $c['name'] : $city = '-';
                                $scanned_by = $admin->name;
                                ($admin->area_id != null) ? $area = $admin->area->name : $area = '-';
                            } elseif ($scanning_history->user_type == 2) {
                                $account_type = 'Shipper';
                                $user = User::find($scanning_history->user_id);
                                $scanned_by = $user->name;
                                $city = '-';
                                $area = '-';


                            } elseif ($scanning_history->user_type == 3) {
                                $account_type = 'Substitute Shipper';
                                $sub_user = SubstituteUser::find($scanning_history->substitute_user_id);
                                $scanned_by = $sub_user->name;
                                $city = '-';
                                $area = '-';
                            } else if ($scanning_history->user_type == 4) {
                                $account_type = 'Retail User';
                                $retail_admin = RetailUser::find($scanning_history->admin_id);
                                $c = City::find($retail_admin->city_id);
                                ($c) ? $city = $c['name'] : $city = '-';
                                $scanned_by = $retail_admin->name;
                                $area = '-';
                            } else if ($scanning_history->user_type == 5) {
                                $account_type = 'Rider';
                                $rider = Rider::find($scanning_history->admin_id);
                                $c = City::find($rider->city_id);
                                ($c) ? $city = $c['name'] : $city = '-';
                                $scanned_by = $rider->name;
                                ($rider->area_id != null) ? $area = $rider->area->name : $area = '-';
                            } else {
                                $account_type = '-';
                                $scanned_by = '-';
                                $city = '-';
                                $area = '-';
                            }
                        }
                        $details[$index]['screen_location'] = $screen_location->name;
                        $details[$index]['account_type'] = $account_type;
                        $details[$index]['scanned_by'] = $scanned_by;
                        $details[$index]['city'] = $city;
                        $details[$index]['updated_via'] = DB::table('shipment_scanning_journey_vias')->whereId($scanning_history->updated_via)->pluck('name')->first() ?? '-';
                        $details[$index]['area'] = $area;
                        $details[$index]['scanned_at'] = Carbon::parse($scanning_history->created_at)->format('Y-m-d H:i:s');
                        $details[$index]['ip_address'] = $scanning_history->ip_address;
                        $details[$index]['latitude'] = $scanning_history->latitude ?? '-';
                        $details[$index]['longitude'] = $scanning_history->longitude ?? '-';
                        $details[$index]['area_log'] = ShipmentScanningJourneyAreaLog::where('shipment_scanning_journey_id', $scanning_history->id)->first();

                    }
                    $data['tracking_number'] = $request->tracking_number;
                    $data['history'] = $details;
                }
                else {
                    $data['empty'][] = $request->tracking_number;
                }
            }
        }
        if($request->has('search_type') && $request->search_type == 2){
            $flag = false;
            $seal_number = $request->tracking_number;
            $bag = CargoManifestBag::where('seal_number', $seal_number);
            if ($bag->exists()) {
                $bag = $bag->first();
                $flag = true;
                $scanning_histories = BagScanningJourney::where('bag_id', $bag->id)->get();
            }
            else {
                $data['invalid'][] = $request->tracking_number;
            }

            if($flag == true){
                if(count($scanning_histories) > 0){
                    foreach($scanning_histories as $index => $scanning_history){
                        $screen_location = BagScanningScreenLocation::where('id', $scanning_history->screen_location_id)->first();
                        if($scanning_history->admin_id == null && $scanning_history->user_id == null){
                            $account_type = '-';
                            $scanned_by = '-';
                            $city = '-';
                            $area = '-';
                        }
                        else {
                            $account_type = 'Admin';
                            $admin = Admin::find($scanning_history->admin_id);
                            $c = City::find($admin->default_hub_id);
                            ($c) ? $city = $c['name'] : $city = '-';
                            $scanned_by = $admin->name;
                            $area = ($admin->area_id != null) ? $admin->area->name : '-';
                        }
                        $details[$index]['screen_location'] = $screen_location->name;
                        $details[$index]['account_type'] = $account_type;
                        $details[$index]['scanned_by'] = $scanned_by;
                        $details[$index]['city'] = $city;
                        $details[$index]['area'] = $area;
                        $details[$index]['scanned_at'] = Carbon::parse($scanning_history->created_at)->format('Y-m-d H:i:s');

                        $details[$index]['ip_address'] = $scanning_history->ip_address;

                        $details[$index]['latitude'] = $scanning_history->latitude;
                        $details[$index]['longitude'] = $scanning_history->longitude;
                    }
                    $data['tracking_number'] = $request->tracking_number;
                    $data['history'] = $details;
                }
                else {
                    $data['empty'][] = $request->tracking_number;
                }
            }
        }

        return $data;
    }
}
