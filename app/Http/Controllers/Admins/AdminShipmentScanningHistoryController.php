<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\Retail\RetailUser;
use App\Http\Models\City;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentScanningJourney;
use App\Http\Models\ShipmentScanningScreenLocation;
use App\Http\Models\Shipper\SubstituteUser;
use App\Http\Models\Shipper\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admins\ActivityTrailController;

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

        $tracking_number = $request->tracking_number;
        $shipment = Shipment::where('tracking_number', $tracking_number);
        $details = array();
            if ($shipment->exists()) {
                $shipment = $shipment->first();
                    $scanning_histories = ShipmentScanningJourney::where('shipment_id', $shipment->id)->get();
                    if(count($scanning_histories) > 0){
                        foreach($scanning_histories as $index => $scanning_history){
                            $screen_location = ShipmentScanningScreenLocation::where('id', $scanning_history->screen_location_id)->first();
                            if($scanning_history->user_type == 1){
                                $account_type = 'Admin';
                                $admin = Admin::find($scanning_history->admin_id);
                                $c = City::find($admin->default_hub_id);
                                ($c)?$city=$c['name']:$city='-';
                                $scanned_by = $admin->name;
                            }
                            elseif($scanning_history->user_type == 2){
                                $account_type = 'Shipper';
                                $user = User::find($scanning_history->user_id);
                                $scanned_by = $user->name;
                                $city='-';

                            }
                            elseif($scanning_history->user_type == 3){
                                $account_type = 'Substitute Shipper';
                                $sub_user = SubstituteUser::find($scanning_history->substitute_user_id);
                                $scanned_by = $sub_user->name;
                                $city='-';
                            }
                            else if($scanning_history->user_type == 4){
                                $account_type = 'Retail User';
                                $retail_admin = RetailUser::find($scanning_history->admin_id);
                                $c = City::find($retail_admin->city_id);
                                ($c)?$city=$c['name']:$city='-';
                                $scanned_by = $retail_admin->name;
                            }
                            else if($scanning_history->user_type == 5){
                                $account_type = 'Rider';
                                $rider = Rider::find($scanning_history->admin_id);
                                $c = City::find($rider->city_id);
                                ($c)?$city=$c['name']:$city='-';
                                $scanned_by = $rider->name;
                            }
                            else{
                                $account_type = '-';
                                $scanned_by = '-';
                                $city='-';
                            }
                            $details[$index]['screen_location'] = $screen_location->name;
                            $details[$index]['account_type'] = $account_type;
                            $details[$index]['scanned_by'] = $scanned_by;
                            $details[$index]['city'] = $city;
                            $details[$index]['scanned_at'] = Carbon::parse($scanning_history->created_at)->format('Y-m-d H:i:s');

                            $details[$index]['ip_address'] = $scanning_history->ip_address;

                            $details[$index]['latitude'] = $scanning_history->latitude;
                            $details[$index]['longitude'] = $scanning_history->longitude;
                        }
                        $data['tracking_number'] = $shipment->tracking_number;
                        $data['history'] = $details;
                    }
                    else {
                        $data['empty'][] = $request->tracking_number;
                    }
            }
            else {
                $data['invalid'][] = $request->tracking_number;
            }

        return $data;
    }
}
