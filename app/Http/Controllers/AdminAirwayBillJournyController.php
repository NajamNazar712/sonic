<?php

namespace App\Http\Controllers;

use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\AdminDepartment;
use App\Http\Models\Admin\AdminRole;
use App\Http\Models\Admin\Retail\RetailUser;
use App\Http\Models\City;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentsAirWaybillJourney;
use App\Http\Models\ShipmentScanningJourney;
use App\Http\Models\ShipmentScanningScreenLocation;
use App\Http\Models\ShipmentsJourney;
use App\Http\Models\Shipper\SubstituteUser;
use App\Http\Models\Shipper\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admins\ActivityTrailController;

class AdminAirwayBillJournyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 418);
        return view('admin.airwaybill_journey.index');
    }

    public function details(Request $request)
    {

        $tracking_number = $request->tracking_number;

        $shipment = Shipment::where('tracking_number', $tracking_number);
        $details = array();
        if ($shipment->exists()) {
            $shipment = $shipment->first();
            ShipmentScanningJourneyController::add($shipment->id, 28, 1, Auth::id(), null,null);
            $scanning_histories = ShipmentsAirWaybillJourney::where('shipment_id', $shipment->id)->orderBy('updated_at','DESC')->get();
            if (count($scanning_histories) > 0) {
                foreach ($scanning_histories as $index => $scanning_history) {
                    if ($scanning_history->user_type == 3) {
                        $account_type = 'Admin';
                        $admin = Admin::find($scanning_history->user_id);
                        $scanned_by = $admin->name;
                        $roles = AdminRole::find($admin->role_id);
                        $roles_department = AdminDepartment::find($roles->department_id);
                        $role = $roles_department->name;

                    } elseif ($scanning_history->user_type == 1) {
                        $account_type = 'Shipper';
                        $user = User::find($scanning_history->user_id);
                        $scanned_by = $user->name;
                        $role = '-';
                    } elseif ($scanning_history->user_type == 2) {
                        $account_type = 'Substitute Shipper';
                        $sub_user = SubstituteUser::find($scanning_history->substitute_user_id);
                        $scanned_by = $sub_user->name;
                        $role = '-';
                    } elseif ($scanning_history->user_type == 4) {
                        $account_type = 'Retail User';
                        $retail_admin = RetailUser::find($scanning_history->admin_id);
                        if($retail_admin){
                            $scanned_by = $retail_admin->name;
                        }
                        else{
                            $scanned_by = '';
                        }
                        $role = '-';
                    } else {
                        $account_type = '-';
                        $scanned_by = '-';
                        $city = '-';
                    }
                    $details[$index]['user_name'] =$scanned_by;
                    $details[$index]['account_type'] = $account_type;
                    $details[$index]['updated_at'] = Carbon::parse($scanning_history->updated_at)->format('Y-m-d H:i:s');
                    $details[$index]['ip_address'] = $scanning_history->ip_address;
                    $details[$index]['role_name'] = $role;

                    }
                $data['tracking_number'] = $shipment->tracking_number;
                $data['history'] = $details;
            } else {
                $data['empty'][] = $request->tracking_number;
            }
        } else {
            $data['invalid'][] = $request->tracking_number;
        }

        return $data;
    }
}
